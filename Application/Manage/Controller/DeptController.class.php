<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/29
 * Time: 10:51
 */

namespace Manage\Controller;

class DeptController extends BaseController {

    public function addMenu(){
        session_start();
        $admin = session('admin');
        $mmenu = M('menu');
        if(IS_POST){
            $data = I('post.');
            $data['pid'] = intval($data['pid']);
            $data['morder'] = intval($data['morder']);
            $data['dept_id'] = intval($data['dept_id']);
            $name = trim(I('mname'));
            $name = substr($name,0,15);
            $data['mname'] = $name;
            $menu_type = $mmenu->where(array('pid'=>0,'type'=>1,'dept_id'=>$data['dept_id']))->find();
            if (empty($data['dept_id'])){
                $this->error('请选择部门');
            }
            if (empty($data['mname'])){
                $this->error('菜单名称不能为空');
            }
            if ($data['pid']==0){
                $wherex = array('dept_id'=>$data['dept_id'],'pid'=>0);
                $ckInfo = M('menu')->where($wherex)->count();
                if ($ckInfo >= 3){
                    $this->error('一级菜单最多为3个');
                }
            }
            if(!$menu_type){
                $data1['mname'] = '办公';
                $data1['dept_id'] = intval($data['dept_id']);
                $data1['pid'] = intval($data['pid']);
                $data1['type'] = 1;
                $data['morder'] = 2;
                $mmenu->add($data1);
            }
            if($mmenu->add($data)){

                $this->success('新增成功', U('menus'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            $where1 = array('did'=>$admin['dept_id']);
            if ($admin['dept_id'] != 0){
                $dept = M('department')->field('did,dname')->where($where1)->select();
                $where = array('dept_id' => $dept[0]['did'], 'pid' => 0,);
                $parents = $mmenu->where($where)->select();
            }else{
                $dept = M('department')->field('did,dname')->select();
                $where = array('dept_id' => $dept[0]['did'], 'pid' => 0,);
                $parents = $mmenu->where($where)->select();
            }
            $this->assign('dept', $dept);
            $this->assign('parents', $parents);
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'addmenu');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function step_menu(){
        if (IS_POST){
            $where = array();
            $where['dept_id'] = intval($_POST['dept_id']);
            $where['pid'] = 0;
            $info = M('menu')->field('mid,mname')->where($where)->select();
            $this->output_data($info);
        }else{
            $this->error('非法请求');
        }
    }
    public function yjj_article_menu(){
        if (IS_POST){
            $where = array();
            $where['dept_id'] = intval($_POST['dept_id']);
            $mid = M('yjjmenu')->field('mid')->where($where)->find();
            $where['pid'] = $mid['mid'];
            $info = M('yjjmenu')->field('mid,mname')->where($where)->select();
            $this->output_data($info);
        }else{
            $this->error('非法请求');
        }
    }
    public function yjj_step_menu(){
        if (IS_POST){
            $where = array();
            $where['dept_id'] = intval($_POST['dept_id']);
            $where['pid'] = 0;
            $info = M('yjjmenu')->field('mid,mname')->where($where)->select();
            $this->output_data($info);
        }else{
            $this->error('非法请求');
        }
    }

    public function output_data($datas, $extend_data = array()) {
        $data = array();
        $data['error'] = '0';
    
        if(!empty($extend_data)) {
            $data = array_merge($data, $extend_data);
        }
    
        $data['result'] = $datas;
    
        if(!empty($_GET['callback'])) {
            echo $_GET['callback'].'('.json_encode($data).')';die;
        } else {
            echo json_encode($data);die;
        }
    }
    
    public function menus(){
        session_start();
        $admin = session('admin');
        $mmenu = M('Menu');
        //dump($admin);die();
        if ($admin['dept_id'] !=0){
            $menu = $mmenu->where(array('dept_id' => $admin['dept_id']))->order('morder asc')->select();
            $bangong = $mmenu->where(array('dept_id'=>$admin['dept_id'],'mname'=>'办公'))->find();
        }else{
            if(!empty($_GET['dept_id'])){
                $dept_id = $_GET['dept_id'];
                $where = array('dept_id'=>$_GET['dept_id']);
            }
            $dept = M('department')->field('did,dname')->select();
            $menu = $mmenu->where($where)->order('morder asc')->select();
            $this->assign('dept',$dept);
            $this->assign('cz',$dept_id);
        }
        //dump($menu);die();
        $menu = listToTree($menu, 'mid', 'pid');
        $this->assign('menu', $menu);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'menus');
        $this->assign('bangong',$bangong);
        $this->display();
    }

    public function setMenu(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            //$data['pid'] = intval($data['pid']);
            $gb['morder'] = intval($data['morder']);
            //$data['dept_id'] = $admin['dept_id'];
            $gb['mname'] = trim($data['mname']);
            unset($data['pid']);
            unset($data['dept']);
            if(M('Menu')->where(array('mid'=>$data['mid']))->save($data) !== false){
                $this->success('保存成功!', U('menus'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            $mid = intval(I('get.mid'));
            if($mid){
                $mmenu = M('Menu');
                $info = $mmenu->find($mid);
                if($info){
                    
                    $where['did'] = $info['dept_id'];
                    $where1['mid'] = $info['pid'];
                    $dept = M('department')->field('did,dname')->where($where)->select();
                    if (empty($dept)){
                        $this->error('无该部门');
                    }else{
                        $dept = $dept[0];
                        $this->assign('dept',$dept);
                    }
                    if ($info['pid']){
                        $pname = M('menu')->field('mid,mname')->where($where1)->select();
                        if (empty($pname)){
                            $this->error('无该分类');
                        }else{
                            $pname = $pname[0];
                            $this->assign('pname',$pname);
                        }
                    }else{
                        $pname = array('mname'=>'顶级');
                        $this->assign('pname',$pname);
                    }
                    
                    
                    $this->assign('dept', $dept);
                    $this->assign('info', $info);
                    $this->assign('parents', $parents);
                    $this->assign('liclass', 'ygl');
                    $this->assign('menus', session('menus'));
                    $this->display();
                }
            }
        }
    }

    public function delMenu($mid){
        $mid = intval($mid);
        if($mid){
            $model = M('Menu');
            if($model->delete($mid) !== false){
                $model->where(array('pid' => $mid))->delete();
                $this->success('删除成功', U('menus'));
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }

    public function addInfo(){
        session_start();
        $admin = session('admin');
        $dept_id = $admin['dept_id'];
        if(IS_POST){
            $data = I('post.');
            $data['menu_id'] = intval($data['menu_id']);
            $data['content'] = stripslashes(I('post.content'));
//            if($admin['dept_id'] == 0){
//                $data['dept_id'] = intval($data['dept_id']);
//                $wherex = array('mid' => $data['menu_id']);
//            }
//            $cKInfo = M('menu')->where($wherex)->select();
//            if (!empty($cKInfo)){
//                if ($cKInfo[0]['pid'] == 0){
//                    $this->error('只有二级分类下才能有信息');
//                }
//            }else{
//                $this->error('未找到您所指定的分类 或已删除');
//            }
            if ($admin['dept_id'] != 0){
                if ($data['dept'] != $dept_id){
                    $this->error('您暂无权限修改其他部门的信息');
                }
            }
            $data['department'] = $admin['department'];
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $data['menu_id'] = $data['menu'];
            unset($data['menu']);
            $data['dept_id'] = $data['dept'];
            unset($data['dept']);
            $m = M('Dept_article');
            if($m->add($data)){
                $this->success('添加成功', U('infos'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $dept_id = $admin['dept_id'];
            $where = array();
            $where['pid'] = array('NEQ',0);
            $mmenu = M('Menu');
            if ($admin['dept_id'] == 0){
                $dept = M('department')->field('did,dname')->select();
                $menu = $mmenu->where($where)->select();
            }else{
                $dept = M('department')->field('did,dname')->where(array('did'=>$dept_id))->select();
                $where['dept_id'] = $dept_id;
                $menu = $mmenu->where($where)->select();
            }
            //$menu = listToTree($menu, 'mid', 'pid');
            $this->assign('menu', $menu);
            $this->assign('dept_id',$dept_id);
            $this->assign('dept',$dept);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass','addinfo');
            $this->display();
        }
    }
    
    public function step_info(){
        if (IS_POST){
            $where = array();
            $where['dept_id'] = intval($_POST['dept_id']);
            $where['pid'] = array('NEQ',0);
            $info = M('menu')->field('mid,mname')->where($where)->select();
            $this->output_data($info);
        }else{
            $this->error('非法请求');
        }
    }
    
    public function infos(){
        session_start();
        $admin = session('admin');
        $dept_id = $_SESSION['admin']['dept_id'];
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = D('DeptArticle');
        $mm = M('dept_article');
        if ($admin['dept_id'] !=0){
            $where = array('dept_id' => $admin['dept_id']);
        }elseif ($_GET['dept_id']){
            $key1 = intval($_GET['dept_id']);
            $where['dept_id'] = $key1;
            $dept = M('department')->field('did,dname')->select();
            $this->assign('dept',$dept);
            $this->assign('dp',$key1);
        }else{
            $dept = M('department')->field('did,dname')->select();
            $this->assign('dept',$dept);
            $this->assign('dp',$key1);
        }
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        $count = $mm->where($where)->count();
        $page = getPage($count, 10);
        //$infos = $m->getInfos($where, $page->firstRow, $page->listRows);
        $infos = M('dept_article')->where($where)->order('aid desc')->page($p.',10')->select();
//        foreach($infos as $k=>$v){
//            $department = M('department')->field('dname')->where(array('did'=>$v['dept_id']))->find();
//            $infos[$k]['department'] = $department['dname'];
//        }
        //dump($infos);exit;
        foreach($infos as $k=>$v){
            $where1['mid'] = $v['menu_id'];
            $mname = M('Menu')->where($where1)->find();
            $infos[$k]['mname'] = $mname['mname'];
            $department = M('department')->field('dname')->where(array('did'=>$v['dept_id']))->find();
            $infos[$k]['department'] = $department['dname'];
        }
        $this->assign('infos', $infos);
        //dump($infos);exit;
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'infos');
        $this->assign('dept_id',$dept_id);
        $this->display();
    }

    public function delInfo($aid){
        $aid = intval($aid);
        if($aid){
            if(M('Dept_article')->delete($aid) !== false){
                $this->success('删除成功', U('infos'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }

    public function editInfo($aid){
        session_start();
        $admin = session('admin');
        
        if(IS_POST){
            $data = I('post.');
//            $data['menu_id'] = intval($data['menu_id']);
            $data['department'] = $admin['department'];
            $data['content'] = stripslashes(I('post.content'));
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            if($admin['dept_id'] == 0){
                $this->error('参数错误');
            }else{
                $data['dept_id'] = $admin['dept_id'];
            }
            $m = M('Dept_article');
            if($m->save($data)){
                $this->success('保存成功', U('infos'));
            }else{
                $this->error('保存失败,请稍后再试');
            }
        }else{
            $aid = intval($aid);
            if($aid){
                $info = M('Dept_article')->find($aid);
                if($info){
                    $info['content'] = html_entity_decode($info['content']);
                    $where = array();
                    $where['pid'] = array('NEQ',0);
                    $dept_id = M('dept_article')->field('dept_id')->where(array('aid'=>$aid))->find();
                    $where = array('dept_id' => $dept_id['dept_id']);
                    $menu = M('Menu')->where($where)->select();
//                    $where = array('dept_id' => $admin['dept_id'], 'url' => '');
//                    $menu = M('Menu')->where($where)->select();
//                    $menu = listToTree($menu, 'mid', 'pid');
                    $mee = M('Menu')->find($info['menu_id']);
                    $this->assign('mee',$mee);
                    $this->assign('info', $info);
                    $this->assign('menu', $menu);
                    $this->assign('menus', session('menus'));
                    $this->assign('dept_id',$admin['dept_id']);
                    $this->assign('liclass', 'ygl');
                    $this->assign('aclass', 'infos');
                    $this->display();
                }
            }
        }
    }
    //易家家部门文章菜单管理-增加
    public function yjj_addMenu(){
        session_start();
        $admin = session('admin');
        $mmenu = M('yjjmenu');
        if(IS_POST){
            $data = I('post.');
            $data['pid'] = intval($data['pid']);
            $data['morder'] = intval($data['morder']);
            $data['dept_id'] = intval($data['dept_id']);
            $name = trim(I('post.mname'));
            $name = substr($name,0,15);
            $data['mname'] = $name;
            $where =array('dept_id'=>$data['dept_id']);
            $pnum = $mmenu->where('pid = 0')->where($where)->count();
            //var_dump($pnum);die();
            if ($data['pid'] == 0){
                if ($pnum >=2){
                    $this->error('顶级菜单最多只有两个');
                }
            }
            if($mmenu->add($data)){
                $this->success('新增成功', U('yjj_Menus'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            $where1 = array('did'=>$admin['dept_id']);
            if ($admin['dept_id'] != 0){
                $dept = M('department')->field('did,dname')->where($where1)->select();
                $where = array('dept_id' => $dept[0]['did'], 'pid' => 0,);
                $parents = $mmenu->where($where)->select();
            }else{
                $dept = M('department')->field('did,dname')->select();
                $where = array('dept_id' => $dept[0]['did'], 'pid' => 0,);
                $parents = $mmenu->where($where)->select();
            }
            $this->assign('dept', $dept);
            $this->assign('parents', $parents);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjj_Menus');
            $this->assign('menus', session('menus'));
            $menus = session('menus');
            $this->display();
        }
    }
    //易家家部门文章-菜单列表
    public function yjj_Menus(){
        session_start();
        $admin = session('admin');
        //die('adc');
        $mmenu = M('yjjmenu');
        //dump($admin);die();
        if ($admin['dept_id'] !=0){
            $menu = $mmenu->where(array('dept_id' => $admin['dept_id']))->order('morder asc')->select();
        }else{
            $dept_id = isset($_GET['dept_id']) ? $_GET['dept_id'] : 1;
            $dept = M('department')->field('did,dname')->select();
            $menu = $mmenu->where(array('dept_id' => $dept_id))->order('morder asc')->select();
            $this->assign('dept',$dept);
            $this->assign('cz',$dept_id);
        }
        //dump($menu);die();
        $menu = listToTree($menu, 'mid', 'pid');
        $this->assign('menu', $menu);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'yjj_Menus');
        $this->display();
    }
    //易家家部门文章菜单编辑
    public function yjjSetMenu(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            $data['pid'] = intval($data['pid']);
            $data['morder'] = intval($data['morder']);
            unset($data['pid']);
            unset($data['dept']);
            if(M('yjjmenu')->where(array('mid'=>$data['mid']))->save($data) !== false){
                $this->success('保存成功!', U('yjj_Menus'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            $mid = intval(I('get.mid'));
            if($mid){
                $mmenu = M('yjjmenu');
                $info = $mmenu->find($mid);
                if($info){
                    $where = array('dept_id' => $admin['dept_id'], 'pid' => 0, 'url' => '');
                    $parents = $mmenu->where($where)->select();
                    $this->assign('info', $info);
                    $this->assign('parents', $parents);
                    $this->assign('liclass', 'yjj');
                    $this->assign('menus', session('menus'));
                    $this->display();
                }
            }
        }
    }
    //易家家部门文章菜单删除
    public function yjjDelMenu($mid){
        $mid = intval($mid);
        if($mid){
            $model = M('yjjmenu');
            if($model->delete($mid) !== false){
                $model->where(array('pid' => $mid))->delete();
                $this->success('删除成功', U('yjj_Menus'));
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }
    //易家家部门文章添加
    public function yjjAddInfo(){
        session_start();
        $admin = session('admin');
        $dept_id = $admin['dept_id'];
        if(IS_POST){
            $data = I('post.');
            $data['menu_id'] = intval($data['menu_id']);
            $data['department'] = $admin['department'];
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('yjj_dept_article');
            $data['addtime'] = date('Y-m-d H:i:s');
            if($dept_id == 0) {
                $data['dept_id'] = $data['dept'];
                unset($data['dept']);
            }
            if($dept_id != 0){
                $data['menu_id'] = $data['menu'];
                unset($data['menu']);
                $data['dept_id'] = $admin['dept_id'];
                unset($data['dept']);
            }
            if($m->add($data)){
                $this->success('添加成功', U('yjjInfos'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $where = array();
            $where['pid'] = array('NEQ',0);
            $mmenu = M('yjjmenu');
            if($admin['dept_id'] == 0){
                $dept = M('department')->field('did,dname')->select();
                $menu = $mmenu->where($where)->select();
            }elseif($admin['dept_id'] != 0){
                $dept = M('department')->field('did,dname')->where(array('did'=>$dept_id))->select();
                $where['dept_id'] = $dept_id;
                $menu = $mmenu->where($where)->select();
            }
            $this->assign('dept_id',$dept_id);
            $this->assign('menu', $menu);
            $this->assign('dept',$dept);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjjInfos');
            $this->display();
        }
    }

    //易家家部门文章列表
    public function yjjInfos(){
        session_start();
        $admin = session('admin');
        $dept_id = $admin['dept_id'];
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        if ($admin['dept_id'] !=0){
            $where = array('dept_id' => $admin['dept_id']);
        }elseif ($_GET['dept_id']){
            $key1 = intval($_GET['dept_id']);
            $where['dept_id'] = $key1;
            $dept = M('department')->field('did,dname')->select();
            $this->assign('dept',$dept);
            $this->assign('dp',$key1);
        }else{
            $dept = M('department')->field('did,dname')->select();
            $this->assign('dept',$dept);
            $this->assign('dp',$key1);
        }
        $count = M('yjj_dept_article')->where($where)->count();
        $page = getPage($count, 10);
        $infos = M('yjj_dept_article')->where($where)->order('aid desc')->page($p.',10')->select();
        foreach($infos as $k=>$v){
            $where1['mid'] = $v['menu_id'];
            $mname = M('yjjmenu')->where($where1)->find();
            $infos[$k]['mname'] = $mname['mname'];
            $department = M('department')->field('dname')->where(array('did'=>$v['dept_id']))->find();
            $infos[$k]['department'] = $department['dname'];
        }
       // $infos = $m->getYjjInfos($where, $page->firstRow, $page->listRows);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('dept_id',$dept_id);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'yjjInfos');
        $this->display();
    }
    //易家家部门文章编辑
    public function yjjEditInfo($aid){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            $data['menu_id'] = intval($data['menu_id']);
            $data['department'] = $admin['department'];
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            if($admin['dept_id'] == 0){
                $this->error('参数错误');
            }else{
                $data['dept_id'] = $admin['dept_id'];
            }
            $m = M('yjj_dept_article');
            if($m->save($data)){
                $this->success('保存成功', U('yjjInfos'));
            }else{
                $this->error('保存失败,请稍后再试');
            }
        }else{
            $aid = intval($aid);
            if($aid){
                $info = M('yjj_dept_article')->find($aid);
                if($info){
                    $info['content'] = html_entity_decode($info['content']);
                    $where = array();
                    $where['pid'] = array('NEQ',0);
                    $dept_id = M('yjj_dept_article')->field('dept_id')->where(array('aid'=>$aid))->find();
                    $where = array('dept_id' => $dept_id['dept_id']);
                    $menu = M('yjjmenu')->where($where)->select();
                    //$where = array('dept_id' => $admin['dept_id'], 'url' => '');
                    //$menu = M('yjjmenu')->where($where)->select();
                    //$menu = listToTree($menu, 'mid', 'pid');
                    $this->assign('info', $info);
                    $this->assign('menu', $menu);
                    $this->assign('menus', session('menus'));
                    $this->assign('dept_id',$admin['dept_id']);
                    $this->assign('liclass', 'yjj');
                    $this->display();
                }
            }
        }
    }
    //易家家部门文章删除
    public function yjjDelInfo($aid){
        $aid = intval($aid);
        if($aid){
            if(M('yjj_dept_article')->delete($aid) !== false){
                $this->success('删除成功', U('yjjInfos'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
}