<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2017/1/5
 * Time: 11:18
 */

namespace Manage\Controller;

use Think\Controller;

use Think\Exception;

class PublicApiController extends  Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    /*
     *首页
     * */

    public function  index()
    {
        $this->display('index');
    }

    /* */
   public function  checkUser($uid,$passkey)
    {

        if($uid && $passkey)
        {

            if(md5('林业局'.$uid) == $passkey )
            {
                return true;

            }else{

                return false;

            }

        }else{

            throw new Exception('uid 和 加密串不能为空');
        }

    }


    public function  getUserList()
    {

        if(!IS_POST) {

            $this->ajaxReturn(array('ret'=>406,'data'=>'','message'=>"提交方式不对"));

        }else{

            $result = array();
            $keywords = I('keywords');
            $passkey = I('passkey');
            $uid = I('uid');

            //$dd = md5('林业局'.'2');

            $keywords = trim($keywords);

            try {

                $checkReturn = $this->checkUser($uid,$passkey);

                if($checkReturn == false)
                {
                    $this->ajaxReturn(array('ret' => 407, 'data' => $result, 'message' => '用户不对'));
                    exit();

                }

                if ($keywords) {
                    $where['real_name'] = array('like', '%' . $keywords . '%');
                    $where['mobile'] = array('like', '%' . $keywords . '%');
                    $where['_logic'] = 'OR';

                } else {

                    $where['_string'] = '1=1';
                }

                $res = M('ygl_user')->where($where)->select();

                if ($res) {
                    foreach ($res as $key => $val) {

                        $result[$key]['real_name'] = $val['real_name'];
                        $result[$key]['mobile'] = $val['mobile'];
                    }
                }

                $this->ajaxReturn(array('ret' => 200, 'data' => $result, 'message' => '成功'));

            } catch (Exception $e) {

                $this->ajaxReturn(array('ret' => 404, 'data' => $result, 'message' => '失败'.$e->getMessage()));

            }
        }

    }



    public function  emergencyInfo()
    {
        if(!IS_POST)
        {

            $this->ajaxReturn(array('ret'=>406,'data'=>'','message'=>"提交方式不对"));

        }else{

            try{

                $result = '';
                $passkey = I('passkey');
                $uid = I('uid');
                $title = I('title');
                $content = I('content');
                $lng = I('lng');
                $lat = I('lat');
                $location = I('location');
                $video = I('video');
                $commander = I('commander');
                $insertData = array();
                $message = '';

                $checkReturn = $this->checkUser($uid,$passkey);
                if($checkReturn == false)
                {
                    $this->ajaxReturn(array('ret' => 407, 'data' => $result, 'message' => '用户不对'));
                    exit();
                }

                $insertData['title'] =  trim($title);
                $insertData['content'] =  trim($content);
                $insertData['longitude'] =  trim($lng);
                $insertData['latitude'] =  trim($lat);
                $insertData['location'] =  trim($location);

                if($video)
                {
                    foreach($video as $key=>$val)
                    {
                        $insertData['video_url_'.($key+1)] = $val['videoUrl'];
                        $insertData['video_location_'.($key+1)] = $val['videoLocation'];
                        if($key == 1)
                        {
                            break;
                        }

                    }

                }

                if($commander){

                    foreach($commander as $key=>$val)
                    {
                        if($insertData['commander'] || !$insertData['commander_mobile'])
                        {
                            if($val['commanderPhone'])
                            {
                                $flag = $this->checkPhone($val['commanderPhone']);

                                if($flag == false)
                                {
                                    $message.= $key.'电话错误;';
                                }

                            }

                            $insertData['commander'] = $val['commanderName'].',';
                            $insertData['commander_mobile'] = $val['commanderPhone'].',';
                        }else{
                            $message.= '指挥员的名字和电话不能为空。';
                        }
                    }

                }else{

                    $message.= '指挥员的名字和电话不能为空。';

                }


                if(!$insertData['title'])
                {

                    $message .= '标题不能为空；';
                }else{

                    echo "dfsdf";

                    echo $insertData['title'];

                    echo mb_strlen($insertData['title']);

                    if(mb_strlen($insertData['title'])>50)
                    {
                        $message .= '标题不能超过50个字；';
                    }
                }

                if(!$insertData['content'])
                {
                    $message .= '内容不能为空；';
                }else{

                    if(mb_strlen($insertData['content'])>300)
                    {
                        $message .= '内容不能超过300个字；';
                    }

                }

                if(!$insertData['longitude'] || !$insertData['latitude'] )
                {
                    $message .= '经纬度不能为空；';
                }else{

                    if(mb_strlen($insertData['longitude'])>20)
                    {
                        $message .= '经度不能超过20个字；';
                    }

                    if(mb_strlen($insertData['latitude'])>20)
                    {
                        $message .= '纬度不能超过20个字；';
                    }

                }

                if(!$insertData['location'])
                {
                    $message .= '险情地点不能为空；';
                }else{
                    if(mb_strlen($insertData['title']>300))
                    {
                        $message .= '内容不能超过300个字；';
                    }

                }

                if($message =='')
                {
                    $insertData['name'] =  '森林防火';
                    $insertData['status'] =  0;
                    $insertData['did'] = 2 ;
                    $insertData['account'] = '14';
                    $insertData['type'] = 1 ;
                    $insertData['time'] =  date("Y-m-d H:i:s",time());
                    $insertData['pid'] = 1 ;
                    $insertData['is_read'] = '' ;
                    $insertData['ever'] =  1;

                    $result = $this->addEmergency($insertData);

                    if(is_numeric($result))
                    {
                        $this->ajaxReturn(array('ret'=>200,'data'=>'','message'=>'信息添加成功'));

                    }else{

                        $this->ajaxReturn(array('ret'=>404,'data'=>'','message'=>'信息添加失败'));
                    }

                }else{

                    $this->ajaxReturn(array('ret'=>404,'data'=>'','message'=>$message));
                }


            }catch (Exception $e){

                $this->ajaxReturn(array('ret' => 404, 'data' => array(), 'message' => '数添加失败'.$e->getMessage()));

            }



        }

    }

    /*
     * 添加森林返防火的警报
     * */
    public function addEmergency($data)
    {
        $result = false;
        if($data)
        {
            $result =  M('emergency')->data($data)->add();
        }

        return $result;
    }


    /*
     * 发送信息
     * */
    
    public function sendMessag()
    {
        
        $message = new \Common\Common\SendMessageController();

        //$result = $messag->setPhone(array('15563995435'))->singleMessage('网信科技','你好')->send();
        $result1 = $message->getRemainFee()->send();
        echo $result1->result;

    
    }

    /*验证手机号*/
    public function checkPhone($phone)
    {

        $count = strlen($phone);

        if($count == 11)
        {
            $isMatched = preg_match_all('/^1[34578]\d{9}$/', $phone, $matches);

            if($isMatched == 0)
            {
                return false;

            }else{
                return true;
            }

        }else{


            return false;
        }


    }









}