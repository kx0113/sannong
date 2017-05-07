<?php
namespace Manage\Controller;
use Think\Controller;
ini_set("memory_limit", "1024M");
set_time_limit(0);
ignore_user_abort();

class SpiderController extends Controller{
//舆情数据分析
    public function ygl_spider_index(){
    	session_start();
        $analyse = M('analyze','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        $url = M('url','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $where = array();
         if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['keyword'] = array('like', "%{$key}%");
        }
        if($_GET['s_status']){
            $s_status = txt($_GET['s_status']);
            $where['status'] = array('like', "%{$s_status}%");
        }
        $count = $analyse->where($where)->count();
        $page = getPage($count, 10);
        $analyse_list = $analyse->where($where)->order('time desc')->page($p.',10')->select();
        foreach ($analyse_list as $k => $v) {
            $analyse_list[$k]['url_name'] = $url->where(array('url_id'=>$v['url_id']))->getField('name');
            unset($analyse_list[$k]['url_id']);
        }
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_spider_index');
        $this->assign('analyse_list', $analyse_list);
        $this->display();
    }
    public function switchs(){
        $tag = $_POST['tag'];
        $analyse = M('analyze','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8');
        switch ($tag){
            //处理设置
            case $tag == "update":
//                p($_POST);
                $data['status'] = $_POST['status'];
                $res = $analyse->where(array('id'=>$_POST['id']))->data($data)->save();
                break;
        }
        if ($res) {
            $json['res'] = 'success';
        } else {
            $json['res'] = 'error';
        }
        $this->ajaxReturn($json);
    }

    //舆情数据分析删除
    public function spider_del(){
        $id = $_GET['id'];
        if($id){
           $url = M('url','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
           $url->where(array("id = $id"))->delete(); 
        }
        $this->success('删除成功',U('ygl_spider_index'),1);
    }

    public function spider_edit(){
        $data = array();
        $id = $_GET['id'];
        $data['status'] = '2';
        //dump($data);exit;
        if($id){
           $url = M('analyze','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
           $a=$url->where(array('id'=>$id))->save($data);
        }
        if($a){
            $this->success('修改状态成功',U('ygl_spider_index'),1);
        }else{
            $this->error('修改失败',U('ygl_spider_index'),1);
        }
        
    }

    //舆情即时查询
    public function ygl_spider_immediately(){
        session_start();
        if(IS_POST){
            $temp_url = M('url','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
            $data = I('post.');
            foreach ($data as $k => $v) {
                for($i = 0;$i<count($v);$i++){
                    $temp['id'] = $i;
                    $temp['url'] = $v[$i];
                    //向临时表中存入temp_url数据
                    $temp_url->add($temp);
                }
            }
            $this->curl();
        }else{
            $url = M('url','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
            $url_list = $url->select();
            $this->assign('url_list',$url_list);
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_spider_index');
            $this->assign('menus', session('menus'));
            $this->display();  
    }
    }

    //舆情安全人
    public function ygl_spider_man(){
        session_start();
        $warning = M('warning','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $adminModel = M('Admin');
        $count = $adminModel->count();
        $infos = $warning->page($p.',10')->order('id desc')->select();
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_spider_man');
        $this->display();
    }

    //舆情安全人新增
    public function ygl_spider_manadd(){
        if(IS_POST){
            session_start();
            $admin = session('admin');
            $data = I('post.');
            $warning = M('warning','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
            $name = $warning->field('name')->select();
            foreach ($name as $k => $v) {
                if($data['name'] == $v){
                    $this->errro('已存在该安全员，请重试');
                }
            }            
            if(!preg_match("/^1[34578]{1}\d{9}$/",$data['mobile'])){  
                $this->error('您输入的手机号码格式不正确，请重试');
            }
            //"/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i"
            if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$data['email'])){
                $this->error('您输入的邮箱格式不正确，请重试');
            }
            if($warning->add($data)){
                $this->success('新增成功', U('ygl_spider_man'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_spider_man');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }

    //舆情安全人修改
    public function ygl_spider_manedit(){
        session_start();
        $warning = M('warning','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        if(IS_POST){
            $data = I('post.');
            if(!preg_match("/^1[34578]{1}\d{9}$/",$data['mobile'])){  
                $this->error('您输入的手机号码格式不正确，请重试');
            }
            //"/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i"
            if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$data['email'])){
                $this->error('您输入的邮箱格式不正确，请重试');
            }
            $where = array();
            $where['id'] = $data['id'];
            if($warning->where($where)->save($data) !== false){
                $this->success('保存成功', U('ygl_spider_man'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            $id = I('get.id');
            if($id){
                $where = array();
                $where['id'] = $id;
                $info = $warning->where($where)->find();
                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'ygl_spider_man');
                $this->assign('info', $info);
                $this->display();
            }
        }
    }

    //舆情安全人删除
    public function ygl_spider_mandel($id){
        $warning = M('warning','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        $id = intval($id);
        if($id){
            if($warning->delete($id)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }

    //关键字新增
    public function ygl_spider_keyword(){
        session_start();
        $M = M('keyword','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        if(IS_POST){
            session_start();
            $admin = session('admin');
            $data = I('post.keyword');
            if($data == ''){
                $this->error('关键字不能为空');
            }
            $data = str_replace(' ', '', $data);
            $keyword = array();
            $keyword['name'] = $data;
            $keyword['status'] = 1;
            $keyword['type'] = 1;
            $keyword['time'] = date('Y-m-d h:i:m',time());
            if($M->add($keyword)){
                $this->success('新增成功', U('ygl_spider_keyword'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            $keyword_info = $M->select();
            $this->assign('keyword_info',$keyword_info);
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_spider_man');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }

    //关键字删除
    public function ygl_spider_keyword_del(){
        session_start();
        $keyword = M('keyword','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        $data = I('post.keyword');
        $k = 0;
        $count = count($data);
        for($i = 0;$i<$count;$i++){
            if($keyword->where("id = $data[$i]")->delete()){
                $k++;
           }
        }
        if($k != 0){
            $this->success('删除成功');
        }else{
            $this->error('删除失败，请重试');
        }
    }

    public function curl(){
    	session_start();
    	$ch = curl_init();
    	//curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1/google/php_spider/Application/common/run.php');
        curl_setopt($ch, CURLOPT_URL, 'http://124.133.16.116:9110/common/run.php');
    	//curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    	//curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    	$a = curl_exec($ch);
    	curl_close ( $ch );
    	$this->success('已经后台进行处理，请稍后查看舆论数据分析结果',U('ygl_spider_index'),5);
    	$this->assign('liclass', 'ygl');
    	$this->assign('menus', session('menus'));
        $this->assign('aclass', 'ygl_spider_index');
        $this->assign('analyse_list', $analyse_list);
    }
}