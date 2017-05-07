<?php
namespace Manage\Controller;
use Common\Common\General;
use Think\Crypt\Driver\Think;

class IndexController extends BaseController {

    public function index(){

		
        session_start();
        $admin = session('admin');
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $adminModel = M('Admin');
        $where = array('dept_id' => $admin['dept_id']);
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['real_name'] = array('like', "%{$key}%");
        }
        $count = $adminModel->where($where)->count();


        $infos = $adminModel->where($where)->page($p.',10')->select();
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'admin');
        $this->assign('aclass', 'adminlist');
        $this->display();
    }

    public function adminDel($aid){
        $aid = intval($aid);
        if($aid){
            if(M('admin')->delete($aid)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }

    public function adminAdd(){
        if(IS_POST){
            session_start();
            $admin = session('admin');
            $data = I('post.');
            if(isset($data['action'])){
                $data['action_id'] = implode(',', $data['action']);
                unset($data['action']);
            }
            $where_ck = array('logname'=>$data['logname']);
            $ckInfo = M('Admin')->where($where_ck)->find();
            if (!empty($ckInfo)){
                $this->error('该用户名已存在，请换个名字试一试');
            }
            $data['password'] = md5($data['password'].'xyz');
            $data['dept_id'] = $admin['dept_id'];
            $dept = M('Department')->find($admin['dept_id']);
            if($dept){
                $data['department'] = $dept['dname'];
            }
            if(M('Admin')->add($data)){
                $this->success('新增成功', U('index'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
            $admin = session('admin');
            $adminModel = D('AdminAction');
            if($admin['dept_id'] == 0){
                //综合后台管理员权限
                $actions = $adminModel->getAction(array('type' => 1));
            }else{
                //部门后台管理员权限
                $actions = $adminModel->getDeptAction($admin['dept_id']);
            }
            $general = new General();
            $actions = $general->listToTree($actions, 'action_id');
            $this->assign('actions', $actions);
            $this->assign('liclass', 'admin');
            $this->assign('aclass', 'adminadd');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }

    public function adminEdit(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            $where = array('logname'=>$data['logname']);
            unset($data['logname']);
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = md5($data['password'].'xyz');
            }
            if(isset($data['action'])){
                $data['action_id'] = implode(',', $data['action']);
                unset($data['action']);
            }
            $data['dept_id'] = $admin['dept_id'];
            $dept = M('Department')->find($admin['dept_id']);
            if($dept){
                $data['department'] = $dept['dname'];
            }
            if(M('Admin')->where($where)->save($data) !== false){
                $this->success('保存成功', U('index'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            $aid = intval(I('get.aid'));
            if($aid){
                $madmin = M('Admin');
                $adminModel = D('AdminAction');
                $info = $madmin->find($aid);
                if($info){
                    if($info['dept_id'] == 0){
                        //综合后台管理员权限
                        $actions = $adminModel->getAction(array('type' => 1));
                    }else{
                        //部门后台管理员权限
                        $actions = $adminModel->getDeptAction($admin['dept_id']);
                    }
                    $general = new General();
                    $actions = $general->listToTree($actions, 'action_id');
                    $this->assign('menus', session('menus'));
                    $this->assign('liclass', 'admin');
                    $this->assign('actions', $actions);
                    $this->assign('info', $info);
                    $this->display();
                }
            }
        }
    }

}