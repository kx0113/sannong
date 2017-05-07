<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 14:22
 */

namespace Ygl\Controller;
use Common\Common\General;
use Common\Common\ImageHandle;

class UserController extends BaseController{

    /*
     * 更改手机号
     * @param account 用户账号
     * @param token 用户Token
     * @param newmob 用户新手机号
     * @return json
     */
    public function changeMobile(){
        $general = new General();
        $newmob = trim($_POST['newmob']);
        $account = trim($_POST['account']);
        if(!$general->isMobile($newmob)){
            $general->error(2);
        }
        //检验手机号是否存在
        $model = D('YglUser');
        $user  = $model->getUser(array('mobile' => $newmob));
        if(!empty($user)){
            $general->error(26);
        }
        //修改操作
        $rel = $model->upUser(array('account' => $account), array('mobile' => $newmob));
        if($rel !== false){
            $general->returnData();
        }else{
            $general->error(27);
        }
    }
    //易管理用户中心更改用户名、头像、性别
    public function changeInfo(){
        $general = new General();
        $imghd = new ImageHandle();
            if (!isset($_POST['account'])){
                $general->error(6);
            }else{
                $where1 = array();
                $where1['account'] = $_POST['account'];
            }
            $data = array();
            if (!empty($_POST['real_name'])){
                $data['real_name'] = trim($_POST['real_name']);
            }
            
            $data['sex'] = intval($_POST['sex']);
            /* $file = $_FILES['headimg'];
            if ($file['size']>0){
                $img = $imghd->image($file);
                $data['headimg'] = 'upload/'.date('Ym').'/'.$img;
            } */
            $config = array(
                'mimes'         =>  array(), //允许上传的文件MiMe类型
                'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
                'exts'          =>  array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'autoSub'       =>  true, //自动子目录保存文件
                'subName'       =>  array('date', 'Ym'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName'   =>    array('uniqid',''),
                'rootPath'      =>  './upload/', //保存根路径
                'savePath'      =>  '',//保存路径
            
            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $info   =   $upload->upload();
            if (!empty($info)){
                $data['headimg'] = 'upload/'.$info['headimg']['savepath'].$info['headimg']['savename'];
            }
            if (M('ygl_user')->where($where1)->save($data)){
                $general->returnData($data,'success');
            }else{
                $general->error(27);
            }
        
           
    }
    //易管理用户中心查看用户资料接口
    public function viewInfo(){
        $general = new General();
        $data = array();
        if (empty($_POST['account'])){
            $general->error(6);
        }else{
            $where = array();
            $where['account'] = trim($_POST['account']);
        }
        $m1 = M('ygl_user');
        $mes = $m1->field('uid,account,real_name,mobile,department,headimg,sex')->where($where)->select();
        if (empty($mes)){
            $general->error(9);
        }else{
            $info =$mes[0];
            $data['real_name'] = $info['real_name'];
            $data['headimg'] = $info['headimg'];
            $data['mobile'] = $info['mobile'];
            if($info['sex'] ==1){
                $data['sex'] = '男';
            }else{
                $data['sex'] = '女';
            }
            $data['department'] = $info['department'];
        }
        foreach ($data as $k=>$v){
            if (!isset($v)){
                $data[$k] = '';
            }
        }
        
        $general->returnData($data,'success');
    }
    
    //易管理搜索账号功能
    public function searchFriend(){
        $general = new General();
        if (IS_POST){
            if (isset($_POST['s_account'])){
                $where['account'] = trim($_POST['s_account']);
            }else{
                $general->error(6);
            }
            $info = M('ygl_user')->field('account,headimg,real_name,sex,department,office,position')->where($where)->select();
            if (!empty($info)){
                $info = $info[0];
                if ($info['sex'] == 1){
                    $info['sex'] = '男';
                }else{
                    $info['sex'] = '女';
                }
            }else{
                $general->error(9);
            }
            /* $info['sex'] = urlencode($info['sex']);
            $info['real_name'] = urlencode($info['real_name']); */
            $general->returnData($info,'succees');
        }else{
            $general->error(6);
        }
    }
}