<?php
namespace Manage\Controller;
use Common\Common\General;
use Think\Crypt\Driver\Think;
use Common\Common\ImageHandle;

class ExpertController extends BaseController {
    
    public function index(){
        session_start();
        //var_dump(session());die();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $eptModel = M('Expert');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['ename'] = array('like', "%{$key}%");
        }
        $count = $eptModel->where($where)->count();
        $infos = $eptModel->where($where)->order('eid desc')->page($p.',10')->select();
        if(!empty($infos)){
            foreach ($infos as &$val){
                if (!empty($val['did'])){
                     $arr = D('Expert')->getCate(array('id'=>$val['did']));
                     $val['ly'] = $arr[0]['name'];
                }
            }
            
            foreach ($infos as &$item){
                switch ($item['level']){
                    case 1:
                        $item['level'] = '国家级';
                        continue;
                    case 2:
                        $item['level'] = '省级';
                        continue;
                    case 3:
                        $item['level'] = '市级';
                        continue;
                    default:
                        $item['level'] = '未知';
                        continue;
                }
            }
        }
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'expert');
        $this->assign('aclass', 'expertList');
        $this->display();
    }
    
    public function expertAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $mobile = I('post.mobile','','trim,htmlspecialchars');
            $password = I('post.password','','trim,htmlspecialchars');
            $ename = I('post.ename','','trim,htmlspecialchars');
            $level = I('post.level','','trim,htmlspecialchars');
            $school = I('post.school','','trim,htmlspecialchars');
            $unit = I('post.unit','','trim,htmlspecialchars');
            $zhicheng = I('post.zhicheng','','trim,htmlspecialchars');
            $province = I('post.province','','trim,htmlspecialchars');
            $city = I('post.city','','trim,htmlspecialchars');
            $district = I('post.district','','trim,htmlspecialchars');
            $birth = I('post.birth','','trim,htmlspecialchars');
            $rpdate = I('post.rpdate','','trim,htmlspecialchars');
            $did = I('post.did','','trim,htmlspecialchars');
            $service = I('post.service','','trim,htmlspecialchars');
            $files = $_FILES['headimg'];
            
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['headimg'] = 'upload/'.date('Ym').'/'.$img;
            }
            if(!($mobile && $password)){
                $this->error('手机号及密码为必填字段');
            }
            if(!$general->isMobile($mobile)){
                $this->error('不是手机号');
            }
            if(D('Expert')->checkUser($mobile)){
                $this->error('该账号已存在');
            }

            $salt = $general->randNum();
            $password = $general->makePassword($password, $salt);
            //$info = array();
            $info['mobile'] = $mobile;
            $info['account'] = $mobile;
            $info['password'] = $password;
            $info['ename'] = $ename;
            if ($birth){
                $info['birth'] = $birth;
            }
            $info['level'] = $level;
            $info['salt'] = $salt;
            $info['school'] = $school;
            $info['unit'] = $unit;
            $info['zhicheng'] = $zhicheng;
            $info['province'] = $province;
            $info['city'] = $city;
            $info['district'] = $district;
            $info['rpdate'] = $rpdate;
            $info['did'] = $did;
            $info['service'] = $service;
            $info['addtime'] = date('Y-m-d h:i:s');
            unset($birth,$city,$district,$ename,$level,$mobile,$password,$province,$salt,$school,$unit,$zhicheng,$service,$did,$rpdate);
            if(D('Expert')->expertAdd($info)){
                //注册易家家环信
                $ex = new \Org\Huanxin\Expert;
                $res = $ex->hx_register($info['mobile'],123456,$info['ename']);
 //               dump($res);die();
                //注册易家家个人用户
                if (!D('YjjUser')->checkUser($mobile)){
                    $yjj_data = array();
                    $yjj_data['uname'] = $info['account'];
                    $yjj_data['password'] = $info['password'];
                    $yjj_data['salt'] = $info['salt'];
                    $yjj_data['real_name'] = $info['ename'];
                    $yjj_data['mobile'] = $info['mobile'];
                    $yjj_data['is_expert'] = 1;
                    $yjj_data['addtime'] = date('Y-m-d H:i:s');
                    D('YjjUser')->addUser($yjj_data);
                }
                //注册易管理环信
                $ygl_hx = new \Org\Huanxin\Huanxin;
                $res1 = $ygl_hx->hx_register($info['mobile'],123456,$info['ename']);
                //注册易管理账号
                $where_ygl = array('account'=>$info['account']);
                $ckMes = M('ygl_user')->where($where_ygl)->select();
                if(empty($ckMes)){
                    $ygl_data = array();
                    $ygl_data['account'] = $info['account'];
                    $ygl_data['password'] = $info['password'];
                    $ygl_data['salt'] = $info['salt'];
                    $ygl_data['real_name'] = $info['ename'];
                    $ygl_data['mobile'] = $info['mobile'];
                    $ygl_data['auth'] = 3;
                    D('YglUser')->addUser($ygl_data);
                }
                redirect(U('index'));;
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
            $cates = D('Expert')->cateList();
            //var_dump($cates);die();
            $this->assign('cates', $cates);
            $this->assign('liclass', 'expert');
            $this->assign('aclass', 'expertAdd');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function del($id){
        $id = intval($id);
        if($id){
            $d = D('Expert');
            $where = array('eid'=>$id);
            $inf = $d->getUser($where);
            $filename = $inf[0]['headimg'];
            //var_dump($inf);die();
            if($filename){
            if(unlink($filename) && D('Expert')->expertDelete($id)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
            }else{
                if(D('Expert')->expertDelete($id)){
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败，请稍后再试');
                }
            }
        }
    }
    
    public function expertEdit($id){
        session_start();
        if(IS_POST){
            $data = I('post.');
            //var_dump($data);die();
            $general = new General();
            $imghd = new ImageHandle();
            $salt = $general->randNum();
            if(!empty($data['password'])){
                $data['password'] = $general->makePassword($data['password'], $salt);
            }else{
                unset( $data['password']);
            }
            $data['salt'] = $salt;
            $data['account'] = $data['mobile'];
            $files = $_FILES['headimg'];
            if ($data['province']=='选择省份'){
                unset($data['province']);
            }
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $data['headimg'] = 'upload/'.date('Ym').'/'.$img;
            }
            if($data['headimg']){
                $d = D('Expert');
                $where = array('eid'=>$data['eid']);
                $inf = $d->getUser($where);
                $filename = $inf[0]['headimg'];
                if($filename){
                    unlink($filename);
                }
            }

            if(M('Expert')->save($data) !== false){
                redirect(U('index'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            session_start();
            $id = intval(I('get.id'));
            if($id){
                $ept = M('Expert');
                $info = $ept->find($id);
                $cates = D('Expert')->cateList();
                $this->assign('cates', $cates);
                //var_dump($cates);die();
                    $this->assign('menus', session('menus'));
                    $this->assign('liclass', 'expert');
                    $this->assign('info', $info);
                    $this->display();
                }
            }
        }
    
    public function lingyuList(){
            session_start();
            $list = D('Expert')->cateList();//列表
            //var_dump($list);die();
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'expert');
            $this->assign("list",$list);
            $this->display();
    }
    
    public function lingyuAdd(){
        if(IS_POST){
            $name = I('name','','trim,htmlspecialchars');
            //$pid = I('pid','','trim,htmlspecialchars');
            $corder = I('corder','','trim,htmlspecialchars');
            $data = array('pid' => 0, 'name' => $name, 'corder' => $corder);
            //var_dump($data);die();
            if(D('Expert')->addCate($data)){
                redirect(U('lingyuList'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            $cates = D('Expert')->cateList();
            //var_dump($cates);die;
            $this->assign('cates', $cates);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'expert');
            $this->display();
        }
    }
    
    public function lingyuEdit(){
        if(IS_POST){
            $info=array();
            $info['name'] = I('name','','trim,htmlspecialchars');
            $info['pid'] = I('pid','','trim,htmlspecialchars');
            $info['corder'] = I('corder','','trim,htmlspecialchars');
            $id = I('id','','trim,htmlspecialchars');
            if($id){
                if(D('Expert')->editCate($info, array('id' => $id))){
                    redirect(U('lingyuList'));
                }else{
                    $this->error('保存失败，请稍后再试');
                }
            }else{
                echo 'illegal operation error';
            }
        }else{
            $id = intval(I('id'));
            if($id){
                $d = D('Expert');
                $cate = $d->getCate(array('id' => $id));
                $cates = $d->cateList();
                //var_dump($cates);die();
                $this->assign('cate', $cate[0]);
                $this->assign('cates', $cates);
                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'expert');
                $this->display();
            }else{
                echo 'illegal operation error';
            }
        }
    }
    
    public function lingyuDel($id){
        $id = intval($id);
        if(M('Domain')->where('id='.$id)->delete()){
            redirect(U('lingyuList'));
        }else{
            $this->error('删除失败，请稍后再试');
        }
    }
    
    public function liveList(){
        $m = M('expertlive');
        $where = array();
        $where['status'] = 1;
        $list = $m->where($where)->select();
        $this->assign('list',$list);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'expert');
        $this->assign('aclass', 'livelist');
        $this->display();
    }
    
    public function liveStop(){
        if (empty($_GET['id'])){
            $this->error('参数错误，请重试');
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
            $data = array('status'=>2);
        }
        $m = M('expertlive');
        $info = $m->where($where)->select();
        if (!empty($info)){
            $channel_id = $info[0]['channel_id'];
        }
        $mes = $this->stopOnline($channel_id);
        
        if ($m->where($where)->save($data)){
            if ($mes == 0){
                $this->success('操作成功',U('liveList'));
            }
        }else{
            $this->error('操作失败，请稍后重试');
        }
        
        
    }
    
    private $appid = 1252950332;
    private $region = 'bj';
    private $secretId = 'AKIDwkQvKfevlfRDqGnaVEl06065tfJsuhCY';
    private $secretKey = 'JQLrHYjomaCcqshYr5B6pSuGVcXcbP1W';
    private $domain = 'live.api.qcloud.com';
    private $road = '/v2/index.php';
    
    //停止腾讯管理端直播
    public function stopOnline($channel_id){
        $sig = array();
        $sig['Action'] = 'StopLVBChannel';           //方法名
        $sig['Region'] = $this->region;                //签名实例所在区域
        $sig['SecretId'] = $this->secretId;            //签名秘钥Id
        $secretKey = $this->secretKey;          //签名秘钥Key
        $sig['Timestamp'] = time();                    //签名当前时间戳
        $sig['Nonce'] = rand(10000,99999);
        //$sig['channelIds'] = array()$channel_id;
        ksort($sig);
        $qingqiu = http_build_query($sig).'&channelIds.1='.$channel_id;
        $yuanwen = 'GET'.$this->domain.$this->road.'?'.$qingqiu;
        $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
        $signature = urlencode($signStr);
        $requestUrl = 'https://'.$this->domain.$this->road.'?'.$qingqiu.'&Signature='.$signature;
        $stop_res = $this->curl($requestUrl, "", false, "GET");
        //以下为删除该频道
        $cn = array();
        $cn['Action'] = 'DeleteLVBChannel';           //方法名
        $cn['Region'] = $this->region;                //签名实例所在区域
        $cn['SecretId'] = $this->secretId;            //签名秘钥Id
        $cn['Timestamp'] = time();                    //签名当前时间戳
        $cn['Nonce'] = rand(10000,99999);
        //$cn['channelIds'] = $channel_id;
        ksort($cn);
        $qingqiu = http_build_query($cn).'&channelIds.1='.$channel_id;
        $yuanwen = 'GET'.$this->domain.$this->road.'?'.$qingqiu;
        $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
        $signature = urlencode($signStr);
        $requestUrl = 'https://'.$this->domain.$this->road.'?'.$qingqiu.'&Signature='.$signature;
        $del_res = $this->curl($requestUrl, "", false, "GET");
        $del_res = json_decode($del_res);
        $mes = $del->code;
        return $mes;
    }
    
    private function curl($url, $data, $header = false, $method = "POST")
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //dump($ch);die();
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        $ret = curl_exec($ch);
        //dump(curl_error($ch));die();
        //dump($ret);die();
        return $ret;
    }
    
    //专家审批列表
    public function expert_sp(){
        /* $num = md5(123456);
        dump($num);die(); */
        $where = array('status'=>1);
        $m = M('expert_shenqing');
        $list = $m->where($where)->select();
        if (!empty($list)){
            foreach($list as $k=>$v){
                if ($v['level'] == 1){
                    $list[$k]['level'] = '国家级';
                }elseif($v['level'] == 2){
                    $list[$k]['level'] = '省级';
                }elseif($v['level'] == 3){
                    $list[$k]['level'] = '市级';
                }
                $temp = M('domain')->where(array('id'=>$v['did']))->select();
                if (!empty($temp)){
                    $list[$k]['domain'] = $temp[0]['name'];
                }
            }
        }
        $this->assign('list',$list);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'expert');
        $this->assign('aclass', 'expert_sp');
        $this->display();
    }
    //专家审批详情
    public function expert_sp_detail(){
        if (!isset($_GET['id'])){
            $this->error('非法请求');
        }else{
            $where = array('id'=>$_GET['id']);
        }
        $info = M('expert_shenqing')->where($where)->select();
        //dump($info);die();
        if (empty($info)){
            $this->error('找不到该申请');
        }
        foreach($info as $k=>$v){
            if ($v['level'] == 1){
                $info[$k]['level'] = '国家级';
            }elseif($v['level'] == 2){
                $info[$k]['level'] = '省级';
            }elseif($v['level'] == 3){
                $info[$k]['level'] = '市级';
            }
            if ($v['sex'] == 1){
                $info[$k]['sex'] = '男';
            }else{
                $info[$k]['sex'] = '女';
            }
            $temp = M('domain')->where(array('id'=>$v['did']))->select();
            if (!empty($temp)){
                $info[$k]['domain'] = $temp[0]['name'];
            }
        }
        $info = $info[0];
        //dump($info);die();
        $this->assign('info',$info);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'expert');
        $this->assign('aclass', 'expert_sp');
        $this->display();
    }
    //专家审批通过
    public function expert_sp_ok(){
        $general = new General();
        if (!isset($_GET['id'])){
            $this->error('非法请求');
        }else{
            $id = intval($_GET['id']);
        }
        
        $m = M('expert_shenqing');
        $where = array('id'=>$id);
        $status = array('status'=>2);
        $data = $m->field('name,mobile,zhicheng,sex,birth,level,school,unit,did,service,account,rpdate')->where($where)->select();
        if (empty($data)){
            $this->error('未找到该申请人');
        }else{
            $data = $data[0];
            $pwd = 111111;
            $salt = $general->randNum();
            $data['password'] = $general->makePassword($pwd, $salt);
//            $data['password'] = 'e10adc3949ba59abbe56e057f20f883e';
            $data['salt'] = $salt;
            $data['addtime'] = date('Y-m-d H:i:s');
            $data['ename'] = $data['name'];
            
        }
        $em = M('expert');
        if($m->where($where)->save($status)){
            if ($em->add($data)){
                $where1 = array('uname'=>$data['account']);
                $type = array('is_expert'=>1);
                if(M('yjj_user')->where($where1)->save($type)){
                    
                    
                    //注册易家家环信
                    $ex = new \Org\Huanxin\Expert;
                    $res = $ex->hx_register($data['account'],123456,$data['name']);
                    //               dump($res);die();
                    //注册易管理环信
                    $ygl_hx = new \Org\Huanxin\Huanxin;
                    $res1 = $ygl_hx->hx_register($data['account'],123456,$data['name']);
                    //注册易管理账号
                    if(!D('YglUser')->checkUser($data['mobile'])){
                        $ygl_data = array();
                        $ygl_data['account'] = $data['account'];
                        $ygl_data['password'] = $data['password'];
                        $ygl_data['salt'] = $data['salt'];
                        $ygl_data['real_name'] = $data['name'];
                        $ygl_data['mobile'] = $data['mobile'];
                        $ygl_data['auth'] = 3;
                        D('YglUser')->addUser($ygl_data);
                    }
                    
                    
                    $this->success('操作成功',U('index'));
                }else{
                    $this->error('操作失败，请稍后重试',U('expert_sp'));
                }
            }
        }else{
            $this->error('操作失败，请稍后重试',U('expert_sp'));
        }
    }
    //专家审批拒绝
    public function expert_sp_no(){
        if (!isset($_GET['id'])){
            $this->error('非法请求');
        }else{
            $id = intval($_GET['id']);
        }
        $m = M('expert_shenqing');
        $where = array('id'=>$id);
        $status = array('status'=>3);
        if($m->where($where)->save($status)){
            $this->success('操作成功',U('expert_sp'));
        }else{
            $this->error('操作失败，请稍后重试',U('expert_sp'));
        }
    }
    
    
    
    
    
    
    
    
}