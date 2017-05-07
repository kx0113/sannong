<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22
 * Time: 17:53
 */
namespace Manage\Controller;
use Common\Common\General;
use Think\Controller;
use Common\Model\AdminAction;
class PublicController extends Controller{

    public function login(){
        if(IS_POST){
            $data = I('post.');
            if($data['logname'] == '' || $data['password'] == ''){
                $this->error('请输入用户名和密码');
            }
 //           dump($data);die();
             if($this->checkVerify($data['verify'])){
                $where = array('logname' => $data['logname']);
                $info = M('Admin')->where($where)->find();
                if(!empty($info) && $info['password'] == md5($data['password'].'xyz')){
                    session_start();
//                    session(array('name'=>'session_id','expire'=>20));
                    session('admin', $info);
                    session('menus', $this->getMenus());
                    //var_dump($_SESSION);die();
                    redirect('/Manage/Index');
                }else{
                    $this->error('用户名或密码错误');
                }
             }else{
                $this->error('验证码错误');
            } 
        }else{
            $depts = M('Department')->select();
            $this->assign('depts', $depts);
            $this->display();
        }
    }

    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }

    public function checkVerify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    public function getMenus(){
        session_start();
        $info = session('admin');
        if($info){
            /* $adminModel = D('AdminAction'); */
            $adminModel =  new \Common\Model\AdminActionModel;
            if($info['level'] == 1){
                if($info['dept_id'] == 0){
                    //总台超级管理员权限
                    $menus = $adminModel->getAction(array('type' => 1));
                }else{
                    //部门超级管理员权限
                    $menus = $adminModel->getDeptAction($info['dept_id']);
                }
            }else{
                //普通管理员权限
                $where = array('action_id' => array('in', $info['action_id']));
                $menus = $adminModel->getAction($where);
            }
            $general = new General();
            $menus = $general->listToTree($menus, 'action_id');
            return $menus;
        }else{
            redirect('/Manage/Public/login');
        }
    }

    public function logout(){
        session('admin', null);
        session('menus', null);
        redirect('/Manage/Public/login');
    }

    
    
    
    
    
    
    
    
    
    
    
    
}