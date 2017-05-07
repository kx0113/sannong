<?php
namespace Manage\Controller;
use Common\Common\General;
use Think\Crypt\Driver\Think;

class HuanxinController extends BaseController {
    
    //群分类列表
    public function groupCateList(){
        session_start();
        $where = array('status'=>1);
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $count = M('group_cate')->where($where)->count();
        $page = getPage($count, 10);
        $info = M('group_cate')->field('id,name,addtime')->where($where)->order('id desc')->page($p.',10')->select();
        
        $this->assign('page', $page->show());
        $this->assign('info',$info);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'groupCateList');
        $this->display();
    }
    
    //群分类添加
    public function groupCateAdd(){
        session_start();
        if (IS_POST){
            $data = array();
            $data['name'] = trim(I('post.name','','htmlspecialchars'));
            $data['desc'] = trim(I('post.desc','','htmlspecialchars'));
            if (empty($data['name'])||empty($data['desc'])){
                $this->error('名称及描述不能为空');
            }
            $data['addtime'] = date('Y-m-d');
            $data['status'] = 1;
            $m = M('group_cate');
            if($m->add($data)){
                $this->success('添加成功',U('groupCateList'));
            }else{
                $this->success('添加失败，请重试',U('groupCateList'));
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'groupCateList');
            $this->display();
        }
    }
    
    //群分类编辑
    public function groupCateEdit(){
        session_start();
        if (IS_POST){
            $data = array();
            $where = array();
            $data['name'] = trim(I('post.name','','htmlspecialchars'));
            $data['desc'] = trim(I('post.desc','','htmlspecialchars'));
            $where['id'] = intval(I('post.id','','htmlspecialchars'));
            if (empty($where['id'])){
                $this->error('缺少相关参数，请重试');
            }
            if (empty($data['name'])||empty($data['desc'])){
                $this->error('名称及描述不能为空');
            }
            $data['addtime'] = date('Y-m-d');
            $data['status'] = 1;
            $m = M('group_cate');
            if($m->where($where)->save($data)){
                $this->success('修改成功',U('groupCateList'));
            }else{
                $this->success('修改失败，请重试',U('groupCateList'));
            }
        }else{
            if (empty($_GET['id'])){
                $this->error('缺少相关参数，请重试');
            }else{
                $where = array();
                $where['id'] = intval(I('get.id','','htmlspecialchars'));
                $info = M('group_cate')->field('id,name,desc')->where($where)->find();
                if (empty($info)){
                    $this->error('您所查找的信息不存在');
                }
            }
            $this->assign('info',$info);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'groupCateList');
            $this->display();
        }
    }
    
    //群分类删除
    public function groupCateDel(){
        if (empty($_GET['id'])){
            $this->error('缺少相关参数，请重试');
        }
        $where = array();
        $data = array('status'=>2);
        $where['id'] = I('get.id','','htmlspecialchars');
        if(M('group_cate')->where($where)->save($data)){
            $this->success('操作成功',U('groupCateList'));
        }else{
            $this->error('操作失败',U('groupCateList'));
        }
    }
    
    //易管理APP下 群组列表
    public function groupList(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
        
        $hx = new \Org\Huanxin\Huanxin;
        $res = $hx->listGroup();
        $res = json_decode($res);
        
        $groups = $res->data;
        $count = $res->count;
        $page = getPage($count, 10);
        foreach ($groups as $k=>$v){
            $groups[$k] = $this->object2array($groups[$k]);
        }
        $groups = array_reverse($groups);
//        dump($groups);die();
        $info = array();
        $start = 10*($p-1);
        $end = 10*($p);
        foreach ($groups as $k1=>$v1){
            if ($k1>=$start && $k1<$end){
                $info[] = $v1;
            }
        }
        foreach ($info as $k2=>$v2){
            $where = array();
            $where['group_account'] = $v2['groupid'];
            $info[$k2]['owner'] = substr($v2['owner'],-11);
            $info[$k2]['created'] = substr(date('Y-m-d H:i:s',substr($v2['created'],0,10)),0,10);
            $mes = M('group')->where($where)->find();
            if (!empty($mes)){
                $where1 = array();
                $where1['id'] = $mes['cid'];
                $mes1 = M('group_cate')->where($where1)->find();
                if (!empty($mes1)){
                    $info[$k2]['cate'] = $mes1['name'];
                }else{
                    $info[$k2]['cate'] = '手机创建';
                }
            }else{
                $info[$k2]['cate'] = '手机创建';
            }
            
        }
        //dump($info);die();
        $this->assign('info',$info);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'groupList');
        $this->display();
    }
    
    //对象转换为数组
    public function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
    //添加群组
    public function groupAdd(){
        session_start();
        if (IS_POST){
            $data = I('post.','');
            $data['did'] = intval($data['did']);
            $data['master_account'] = htmlspecialchars($data['master_account']);
            $data['groupname'] = trim($data['groupname']);
            $data['public'] =intval($data['public']);
            $data['approval'] = intval($data['approval']);
            $data['maxuser'] = intval($data['maxuser']);
            $data['groupdesc'] = trim(htmlspecialchars($data['groupdesc']));
            $data['cate'] = intval(htmlspecialchars($data['cate']));
            foreach ($data['members'] as $k=>$v){
                $where_user = array();
                $where_user['account'] = $v;
                $ckUser = M('ygl_user')->where($where_user)->find();
                if (empty($ckUser)){
                    $this->error('请您检查是否有未找到的用户在群成员中');
                }
            }
            
            if ($data['approval']){
                $data['approval'] = true;
            }else{
                $data['approval'] = false;
            }
            if ($data['public']){
                $data['public'] = true;
            }else{
                $data['public'] = false;
                $data['approval'] = true;
            }
            
            /* if (empty($data['did'])){
                $this->error('缺少参数，请重试');
            } */
            if (empty($data['master_account'])){
                $this->error('缺少群主账号，请重试');
            }
            if (empty($data['groupname'])){
                $this->error('缺少群名称，请重试');
            }
            if (empty($data['groupdesc'])){
                $this->error('缺少群描述，请重试');
            }
            if (empty($data['maxuser'])){
                $this->error('缺少群规模，请重试');
            }
            $where_ck = array('account'=>$data['master_account']);
            $ckInfo = M('ygl_user')->where($where_ck)->find();
            if (empty($ckInfo)){
                $this->error('不存在该群主的易管理用户，请重试');
            }
            //dump($data);die();
            //以下为注册环信群 环节
            $hx = new \Org\Huanxin\Huanxin;
            $res = $hx->addGroup($data['groupname'], $data['groupdesc'], $data['public'], $data['maxuser'], $data['approval'], $data['master_account'], $data['members']);
            $res = json_decode($res);
            //dump($res);die();
            $group_account = $res->data->groupid;
            //获取数据后，存至本地服务器
            if (!empty($group_account)){
                $mes = array();
                $mes['group_account'] = $group_account;
                $mes['cid'] = $data['cate'];
                $mes['master_account'] = $data['master_account'];
                $mes['addtime'] = date('Y-m-d H:i:s');
                $mes['status'] = 1;
            }else{
                $this->error('添加至云端失败');
            }
            if (M('group')->add($mes)){
                $this->success('添加成功',U('groupList'));
            }else{
                $this->success('添加失败',U('groupList'));
            }
        }else{
            $cates = M('group_cate')->field('id,name')->select();
            $this->assign('cates',$cates);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'groupList');
            $this->display();
        }
    }
    
    //ajax验证易管理用户
    public function ajaxCheckUser(){
        $where = array();
        $where['account'] = $_GET['useraccount'];
        $res = M('ygl_user')->field('uid,account,real_name')->where($where)->find();
        if (!empty($res)){
            $arr = array('status'=>1,'data'=>$res['real_name']);
            $this->ajaxReturn(json_encode($arr));
        }else{
            $arr = array('status'=>0,'data'=>'找不到该用户');
            $this->ajaxReturn(json_encode($arr));
        }
    }
    
    //删除对应群组
    public function groupDel(){
        if (empty($_GET['groupid'])){
            $this->error('缺少相关参数，请重试');
        }else{
            $groupid = htmlspecialchars($_GET['groupid']);
            $where['group_account'] = $groupid;
        }
        $hx = new \Org\Huanxin\Huanxin;
        $res = $hx->delGroup($groupid);
        $res = json_decode($res);
        if (!$res->data->success){
            $this->error('云端操作失败',U('groupList'));
        }
        $mes = array('status'=>2);
        $info = M('group')->where($where)->find();
        if (!empty($info)){
            M('group')->where($where)->save($mes);
            $this->success('操作成功',U('groupList'));
        }else{
            $this->success('操作成功',U('groupList'));
        }
    }
    
    //查看某群的详细信息
    public function groupDetail(){
        session_start();
        if (empty($_GET['groupid'])){
            $this->error('缺少群号信息');
        }else{
            $groupid = htmlspecialchars($_GET['groupid']);
        }
        
        $hx = new \Org\Huanxin\Huanxin;
        $res = $hx->detailGroup($groupid);
        $res = json_decode($res);
        $info = $res->data[0];
//        dump($info);die();
        $group_name = $info->name;
        $pb = $info->public;
        $desc = $info->description;
        $gid = $info->id;
        $maxuser = $info->maxusers;
        $num = $info->affiliations_count;
        $mem = $info->affiliations;
        foreach($mem as $key=>$val){
            $mem[$key] = $this->object2array($mem[$key]);
        }
//        dump($mem);die();
        $owner = '';
        //循环出成员姓名
        foreach ($mem as $k=>$v){
            foreach ($v as $k1=>$v1){
                
                $where = array();
                $where['account'] = $v1;
                $name = M('ygl_user')->field('account,real_name')->where($where)->find();
                if (!empty($name)){
                    $mem[$k]['name'] = $name['real_name'];
                }else{
                    $mem[$k]['name'] = '未知';
                }
                //如果是群主，单独拿出来
                if ($k1=='owner'){
                    $owner = $mem[$k]['name'];
                    unset($mem[$k]);
                }
            }
        }
        if ($pb){
            $pb = '公开群';
        }else{
            $pb = '私密群';
        }
//        dump($mem);die();
        $this->assign('groupid',$gid);
        $this->assign('groupname',$group_name);
        $this->assign('mem',$mem);
        $this->assign('owner',$owner);
        $this->assign('num',$num);
        $this->assign('desc',$desc);
        $this->assign('pb',$pb);
        $this->assign('maxuser',$maxuser);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'groupList');
        $this->display();
    }
    
    //更改群属性
    public function groupProperty(){
        session_start();
        if (IS_POST){
            $data = I('post.','');
            $data['groupid'] = htmlspecialchars($data['groupid']);
            $data['groupname'] = trim($data['groupname']);
            $data['maxuser'] = intval($data['maxuser']);
            $data['groupdesc'] = trim(htmlspecialchars($data['groupdesc']));
            $data['cate'] = intval(htmlspecialchars($data['cate']));

            if (empty($data['groupname'])){
                $this->error('缺少群名称，请重试');
            }
            if (empty($data['groupdesc'])){
                $this->error('缺少群描述，请重试');
            }
            if (empty($data['maxuser'])){
                $this->error('缺少群规模，请重试');
            }
            if (empty($data['cate'])){
                $this->error('缺少群分类，请重试');
            }
            
            $hx = new \Org\Huanxin\Huanxin;
            $res = $hx->editGroup($data['groupid'], $data['groupname'], $data['groupdesc'], $data['maxuser']);
            $where = array();
            $where['group_account'] = $data['groupid'];
            $ckInfo = M('group')->where($where)->find();
            if (!empty($ckInfo)){
                $mes = array('cid'=>$data['cate']);
                $result = M('group')->where($where)->save($mes);
                if ($result){
                    $this->success('修改成功',U('groupList'));
                }else{
                    $this->error('修改失败',U('groupList'));
                }
            }else{
                $data_new = array();
                $data_new['group_account'] = $data['groupid'];
                $data_new['cid'] = $data['cate'];
                $data_new['status'] = 1;
                $data_new['addtime'] = date('Y-m-d H:i:s');
                //获取群主账号
                $hx = new \Org\Huanxin\Huanxin;
                $res = $hx->detailGroup($data['groupid']);
                $res = json_decode($res);
                $info = $res->data[0];
                $mem = $info->affiliations;
                foreach($mem as $key=>$val){
                    $mem[$key] = $this->object2array($mem[$key]);
                }
                foreach ($mem as $k=>$v){
                    foreach ($v as $k1=>$v1){
                        //如果是群主，单独拿出来
                        if ($k1=='owner'){
                            $data_new['master_account'] = $v1;
                        }
                    }
                }
                
                $fin = M('group')->add($data_new);
                if ($fin){
                    $this->success('修改成功',U('groupList'));
                }else{
                    $this->error('修改失败',U('groupList'));
                }
            }
        }else{
            if (!empty($_GET['groupid'])){
                $groupid = htmlspecialchars($_GET['groupid']);
                $where = array('group_account'=>$groupid);
            }else{
                $this->error('缺少群号参数');
            }
            $hx = new \Org\Huanxin\Huanxin;
            $res = $hx->detailGroup($groupid);
            $res = json_decode($res);
            $info = $res->data[0];
//            dump($info);die();
            
            $group_name = $info->name;
            $desc = $info->description;
            $gid = $info->id;
            $maxuser = $info->maxusers;
            
            $cates = M('group_cate')->field('id,name')->select();
            $ckCate = M('group')->where($where)->find();
            if (!empty($ckCate)){
                $cid = $ckCate['cid'];
            }else{
                $cid = 0;
            }
            $this->assign('cates',$cates);
            $this->assign('cid',$cid);
            $this->assign('groupid',$gid);
            $this->assign('groupname',$group_name);
            $this->assign('desc',$desc);
            $this->assign('maxuser',$maxuser);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'groupList');
            $this->display();
        }
    }
    
    //删除群内成员
    public function groupMembersDel(){
        //群id获取
        $groupid = htmlspecialchars($_GET['gid']);
        //群成员获取
        $members = htmlspecialchars($_POST['ids']);
        if (empty($groupid)){
            $arr = array('status'=>0,'message'=>'群id不能为空');
            $this->ajaxReturn(json_encode($arr));
        }
        if (empty($members)){
            $arr = array('status'=>0,'message'=>'操作对象不能为空');
            $this->ajaxReturn(json_encode($arr));
        }
        $hx = new \Org\Huanxin\Huanxin;
        $res = $hx->delGroupMem($groupid, $members);
        $res = json_decode($res);
        $info = $res->data;
        if (!empty($info)){
            $arr = array('status'=>1,'message'=>'操作成功');
            $this->ajaxReturn(json_encode($arr));
        }else {
            $arr = array('status'=>0,'message'=>'操作失败');
            $this->ajaxReturn(json_encode($arr));
        }
        
    }
    
    //群成员列表页
    public function groupMembers(){
        $groupid = htmlspecialchars($_GET['groupid']);
        if(empty($groupid)){
            $this->error('缺少群参数');
        }
        
        $hx = new \Org\Huanxin\Huanxin;
        $res = $hx->memberGroup($groupid);
        $res = json_decode($res);
//        dump($res);die();
        $mem = $res->data;
        
        foreach($mem as $key=>$val){
            $mem[$key] = $this->object2array($mem[$key]);
        }
        $owner = '';
        $owner_account = '';
        if (!empty($mem)){
            //循环出成员姓名
            foreach ($mem as $k=>$v){
                foreach ($v as $k1=>$v1){
            
                    $where = array();
                    $where['account'] = $v1;
                    $name = M('ygl_user')->field('account,real_name')->where($where)->find();
                    if (!empty($name)){
                        $mem[$k]['name'] = $name['real_name'];
                    }else{
                        $mem[$k]['name'] = '未知';
                    }
                    //如果是群主，单独拿出来
                    if ($k1=='owner'){
                        $owner = $mem[$k]['name'];
                        $owner_account = $v1;
                        unset($mem[$k]);
                    }
                }
            }
        }
//        dump($owner_account);die();
        
        $this->assign('owner_name',$owner);
        $this->assign('owner_account',$owner_account);
        $this->assign('mem',$mem);
        $this->assign('groupid',$groupid);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'groupList');
        $this->display();
        
    }
    
    //批量新增群成员
    public function groupMembersAdd(){
        session_start();
        $groupid = htmlspecialchars($_GET['groupid']);
        if (empty($groupid)){
            $this->error('缺少相关参数，请重试');
        }
        if (IS_POST){
            $data = I('post.');
            $data['groupid'] = htmlspecialchars($data['groupid']);
            foreach ($data['members'] as $k=>$v){
                $where_user = array();
                $where_user['account'] = $v;
                $ckUser = M('ygl_user')->where($where_user)->find();
                if (empty($ckUser)){
                    $this->error('请您检查是否有未找到的用户在群成员中');
                }
            }
            
            
            $hx = new \Org\Huanxin\Huanxin;
            $res = $hx->addGroupMem($data['groupid'], $data['members']);
            $res = json_decode($res);
            if (empty($res->data)){
                $this->error('新增失败或用户已在群中',U('groupList'));
            }else{
                $this->success('操作成功',U('groupList'));
            }
            
        }else{
            $this->assign('gid',$groupid);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'groupList');
            $this->display();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}