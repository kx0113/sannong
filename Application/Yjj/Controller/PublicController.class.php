<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22
 * Time: 11:36
 */
namespace Yjj\Controller;
use Think\Controller;
use Common\Common\General;
use Common\Common\ImageHandle;
class PublicController extends Controller {

    public function register(){
        $general = new General();
        if(IS_POST){
            $imghd = new ImageHandle();
            $mobile = I('post.account','','trim,htmlspecialchars');
            $id = I('post.session_id','','trim,htmlspecialchars');
            $authnum = I('post.authnum','','trim,htmlspecialchars');
            $password = I('post.password','','trim,htmlspecialchars');
            $files = $_FILES['picture'];
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['picture'] = '/upload/'.date('Ym').'/'.$img;
            }
            if(!($mobile && $id && $authnum && $password)){
                $general->error(7);
            }
            if(!$general->isMobile($mobile)){
                $general->error(2);
            }
            if(D('YjjUser')->checkUser($mobile)){
                $general->error(11);
            }
            session_id($id);
            session_start();
            $sess = $_SESSION['auth'];

            if($sess['mobile'] != $mobile){
                $general->error(4);
            }
            if($sess['authnum'] != $authnum){
                $general->error(5);
            }
            $salt = $general->randNum();
            $password = $general->makePassword($password, $salt);
            $info = array();
            $info['mobile'] = $mobile;
            $info['password'] = $password;
            $info['uname'] = $mobile;
            $info['salt'] = $salt;
            if(D('YjjUser')->addUser($info)){
                $general->returnData(array(),'success');
            }else{
                $general->error(8);
            }
        }else{
            $general->error(6);
        }
    }

    public function login(){
        $general = new General();
        if(IS_POST){
            $mobile = I('post.account','','trim,htmlspecialchars');
            $password = I('post.password','','trim,htmlspecialchars');
            $type = I('post.type','','trim,htmlspecialchars');
            if($mobile == '' || $password == ''){
                $general->error(12);
            }
            if ($type=='user'){
                $yjj = D('YjjUser');
            }elseif ($type=='expert'){
                $yjj = D('Expert');
            }else{
                $general->error(6);
            }
            $user = $yjj->checkUser($mobile);
            if(empty($user)){
                $general->error(9);
            }
            $md5pass = $general->makePassword($password, $user[0]['salt']);
            if($md5pass == $user[0]['password']){
                $token = $general->makeToken($mobile, $md5pass);
                if ($type=='user'){
                    $real_name = $user[0]['real_name'];
                    $headimg = $user[0]['headimg'];
                }else{
                    $real_name = $user[0]['ename'];
                    $headimg = $user[0]['headimg'];
                }
                $data = array('token'=>$token,'account'=>$mobile, 'type' => $type,'real_name'=>$real_name,'headimg'=>$headimg);
                $general->returnData($data);
            }else{
                $general->error(3);
            }
        }
    }

    /*
     * @param mobile 手机号
     * 生成验证码
     */
    public function authNum(){
        $general = new General();
        $mobile = I('get.mobile');
        if(!$general->isMobile($mobile)){
            $general->error(2);
        }
        $authnum = $general->authNum($mobile);
        $authnum['mobile'] = $mobile;
        $general->returnData($authnum);
    }

    /*
     * 找回密码时验证手机
     * @param session_id session_id
     * @param mobile 手机号
     * @param authnum 短信验证码
     * @param type 账号类别：1易管理；2易家家普通用户；3易家家专家用户
     * @return json
     */
    public function checkMobile(){
        $general = new General();
        if(!isset($_POST['session_id'])){
            $general->error(17);
        }
        session_id($_POST['session_id']);
        session_start();
        //未获取验证码
        if(empty($_SESSION['auth'])){
            $general->error(20);
        }
        //手机号不一致
        if($_SESSION['auth']['mobile'] != $_POST['mobile']){
            $general->error(4);
        }
        //短信验证码错误
        if($_SESSION['auth']['authnum'] != $_POST['authnum']){
            $general->error(5);
        }
        //检查用户是否存在
        $type = intval($_POST['type']);
        $mobile = $_POST['mobile'];
        if($type == 1){
            //易管理用户
            $model = D('YglUser');
            $user = $model->getUser(array('mobile' => $mobile));
            if(empty($user)){
                $general->error(9);
            }
            $_SESSION['auth']['check'] = 'success';
            $_SESSION['auth']['account'] = $user[0]['account'];
            $general->returnData(array('session_id' => $_POST['session_id']));
        }else if($type == 2){
            //易家家普通用户
            $model = D('YjjUser');
            $user = $model->getUser(array('mobile' => $mobile));
            if(empty($user)){
                $general->error(9);
            }
            $_SESSION['auth']['check'] = 'success';
            $_SESSION['auth']['account'] = $user[0]['uname'];
            $general->returnData(array('session_id' => $_POST['session_id']));
        }else if($type == 3){
            //易家家专家用户
            $model = D('Expert');
            $user = $model->getUser(array('mobile' => $mobile));
            if(empty($user)){
                $general->error(9);
            }
            $_SESSION['auth']['check'] = 'success';
            $_SESSION['auth']['account'] = $user[0]['account'];
            $general->returnData(array('session_id' => $_POST['session_id']));
        }
    }

    /*
     * 用户修改密码
     * @param session_id session_id
     * @param pass 密码
     * @param type 用户类型：1易管理；2易家家普通用户；3易家家专家用户
     * @return json
     */
    public function changePass(){
        $general = new General();
        if(!isset($_POST['session_id'])){
            $general->error(17);
        }
        session_id($_POST['session_id']);
        session_start();
        //未获取短信验证码
        if(empty($_SESSION['auth'])){
            $general->error(20);
        }
        //短信验证码未通过校验
        if(@$_SESSION['auth']['check'] != 'success'){
            $general->error(19);
        }
        if(trim($_POST['pass'] === '')){
            $general->error(18);
        }
        $type = intval($_POST['type']);
        //重新生成密码
        $salt = $general->randNum();
        $pass = $general->makePassword(trim($_POST['pass']), $salt);
        $data = array('password' => $pass, 'salt' => $salt);
        if($type == 1){
            //易管理用户
            $model = D('YglUser');
            $where = array('account' => $_SESSION['auth']['account']);
            if($model->upUser($where, $data) !== false){
                unset($_SESSION['auth']);
                $general->returnData();
            }else{
                $general->error(21);
            }
        }else if($type == 2){
            //易家家普通用户
            $model = D('YjjUser');
            $where = array('uname' => $_SESSION['auth']['account']);
            if($model->upUser($where, $data) !== false){
                unset($_SESSION['auth']);
                $general->returnData();
            }else{
                $general->error(21);
            }
        }else if($type == 3){
            //易家家专家用户
            $model = D('Expert');
            $where = array('account' => $_SESSION['auth']['account']);
            if($model->upExpert($where, $data) !== false){
                unset($_SESSION['auth']);
                $general->returnData();
            }else{
                $general->error(21);
            }
        }
    }
    //易家家部门评价接口
    public function deptEvaluation(){
        $general = new General();
        $data = array();
        //软件
        $data['type'] = 2;
        $data['number'] = 1;
        $data['addtime'] = date('Y-m-d H:i:s');
        //部门
        if (empty($_POST['dept_id'])){
            $general->error(13);
        }else{
            $data['dept_id'] = intval($_POST['dept_id']);
        }
        //分数
        if (empty($_POST['score'])){
            $general->error(70);
        }else{
            $data['score'] = intval($_POST['score']);
        }
        //评价者账号
        if (empty($_POST['account'])){
            $general->error(83);
        }else{
            $data['account'] = $_POST['account'];
        }
        //评价内容
        if (empty($_POST['content'])){
            $data['content'] = '十分感谢，您的回答完美解决了我的问题';
        }else{
            $data['content'] = trim($_POST['content']);
        }
        if(M('dept_comment')->add($data)){
            $general->returnData(array(),'success');
        }else{
            $general->error(69);
        }
        
    }
    
    
    
    
    
    

}