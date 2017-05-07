<?php
namespace Manage\Controller;
use Common\Common\General;
use Think\Crypt\Driver\Think;


class NoticeController extends BaseController {

    public function index(){
        session_start();
        //var_dump(session());die();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $eptModel = M('Notice');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        $count = $eptModel->where($where)->count();
        $infos = $eptModel->where($where)->order('addtime desc')->page($p.',10')->select();
        if(!empty($infos)){
            foreach ($infos as &$val){
                if (!empty($val['did'])){
                     $arr = M('Department')->where(array('did'=>$val['did']))->select();
                     $val['bm'] = $arr[0]['dname'];
                }
            }
        }
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'notice');
        $this->assign('aclass', 'noticeList');
        $this->display();
    }

    public function del($id){
        $id = intval($id);
        if($id){

            if(M('notice')->where('id='.$id)->delete()){
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }

        }
    }

    public function noticeEdit($id){
        session_start();
        if(IS_POST){
            $data = I('post.');
            //var_dump($data);die();
            $general = new General();

            if ($data['did']=='请选择部门'){
                unset($data['did']);
            }
            if(M('notice')->save($data) !== false){
                redirect(U('index'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            session_start();
            $id = intval(I('get.id'));
            if($id){
                $not = M('notice');
                $info = $not->find($id);

                $dep=M('Department')->select();
                $this->assign('dep', $dep);

                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'notice');
                $this->assign('info', $info);
                $this->display();
                }
            }
        }

    public function noticeList(){
            session_start();

           $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
           $notModel = M('Notice');
           $where = array();
           if($_GET['keyword']){
              $key = txt($_GET['keyword']);
              $where['title'] = array('like', "%{$key}%");
           }
           $count = $notModel->where($where)->count();
           $infos = $notModel->where($where)->order('addtime desc')->page($p.',10')->select();
           if(!empty($infos)){
              foreach ($infos as &$val){
                 if (!empty($val['did'])){
                     $arr = M('Department')->where(array('did'=>$val['did']))->select();
                     $val['bm'] = $arr[0]['dname'];
                 }
             }
           }
           $page = getPage($count, 10);
           $this->assign('page', $page->show());
           $this->assign('menus', session('menus'));
           $this->assign('liclass', 'notice');
           $this->assign('aclass', 'noticeList');
           $this->assign("list",$infos);
           $this->display();
    }

    public function noticeAdd(){
        if(IS_POST){
            $type = I('type','','trim,htmlspecialchars');
            $did = I('did','','trim,htmlspecialchars');
            $title = I('title','','trim,htmlspecialchars');
            $content = I('content','','trim,htmlspecialchars');

            $data = array('type' => $type,'did' => $did, 'title' => $title, 'content' => $content,'addtime'=> date('Y-m-d h:i:s'));
            //var_dump($data);die();
            if(M('Notice')->add($data)){
                redirect(U('noticeList'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{

            $dept_id=session('admin.dept_id');

            if(!$dept_id){
                $dep=M('Department')->select();
            }
            else{
                $dep=M('Department')->where(array('did'=>$dept_id))->select();
            }

            $this->assign('dep', $dep);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'notice');
            $this->assign('aclass', 'noticeAdd');

            $this->display();
        }
    }

    public function noticeDel($id){
        $id = intval($id);
        if(M('notice')->where('id='.$id)->delete()){
            redirect(U('noticeList'));
        }else{
            $this->error('删除失败，请稍后再试');
        }
    }







}