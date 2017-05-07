<?php
namespace Manage\Controller;
use Think\Think;
use Think\Upload;
use Common\Common;
date_default_timezone_set('PRC');//设置时区
ignore_user_abort(); // 忽视用户行为，后台运行
set_time_limit(0); // 取消脚本运行时间的超时上限
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 10:17
 */






class TrainingController extends BaseController{

    public function traininfo(){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('Traininginfo');
        $where = array();
        $where1 = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        if($_GET['type']){
            $type = txt($_GET['type']);
            $where1['software'] = array('like', "%{$type}%");
        }
        $count = $m->where($where)->where($where1)->count();
        $page = getPage($count, 10);
        $where['software'] = 2;
        $infos = $m->where($where)->where($where1)->order('addtime DESC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign('cz',$type);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'traininfo');
        $this->display();
    }

    public function trainadd(){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        if(IS_POST){
            $data = I('post.');
            $data['title'] = trim($data['title']);
            $data['content'] = stripslashes(I('post.content'));
            $data['software'] = intval($data['software']);
            $data['software'] = 2;
            $data['im'] = str_replace(' ', '', $data['im']);
            if (empty($data['software'])){
                $this->error('请选择软件');
            }
            if($data['stime'] == ''){
                $this->error('请填写直播时间！');
            }
            $file = $_FILES['image'];
            if($file['size'] > 0){
                $upinfo = upload('trainimg/');
                if($upinfo['code'] == 0){
                    $data['image'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('Traininginfo');
            if($m->add($data)){
                $this->success('添加成功', U('traininfo'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'trainadd');
            $this->assign('type',2);
            $this->display();
        }
    }

    public function traindel($tid){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        $tid = intval($tid);
        if($tid){
            if(M('Traininginfo')->delete($tid) !== false){
                $this->success('删除成功', U('traininfo'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }

    public function addvideo(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            $data['title'] = trim($data['title']);
            $data['url'] = trim($data['url']);
            $data['software'] = intval($data['software']);
            $data['software'] = 2;
            if (empty($data['software'])){
                $this->error('请选择软件');
            }
            $file = $_FILES['image'];
            if($file['size'] > 0){
                $upinfo = upload('trainimg/');
                if($upinfo['code'] == 0){
                    $data['image'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }

            //视频上传
            $videos = $_FILES['video'];
            if($videos['size']>0){
                $subname = M('video')->field('url3')->select();
                $ftp = new Ftp();   
                $config = array(
                    'hostname' => '58.59.18.69', //服务器地址
                    'username' => 'gurundong',  //FTP登录账号
                    'password' => '!LeiFeng&F',   //FTP登录密码
                    'port' => 21         //端口号
                );
                $ftp->connect($config);
                $time = time();
                $a = $ftp->upload($_FILES['video']['tmp_name'],'/sannong_video/'.$time.'_'.$_FILES['video']['name']);
                $data['url3'] = 'http://58.59.18.69:9009/'.$time.'_'.$_FILES['video']['name'];
            }

            //文件上传
            $text = $_FILES['text'];
            if($text['size']>0){
                unset($file);
                $path = '../upload/traintext/';
                $subpath = date('Ym');
                $upload = new \Think\Upload();
                $upload->maxSize = 10145728;
                $upload->exts = array('rar', 'zip', 'txt', 'doc','xls','ppt','pdf');
                $upload->mimes = array('application/octet-stream','application/zip','text/plain','application/msword','application/vnd.ms-excel','application/vnd.ms-powerpoint','application/pdf');
                $upload->savePath = $path;
                $upload->subName   =     $subpath;
                $textpath = time().'_'.mt_rand(10,20);
                $upload->saveName  =    $textpath;
                $info = $upload->upload();
               // dump($info);exit;
                if(empty($info['text'])){
                    $this->error($upload->getError());
                }else{
                    $data['text'] = $path.$subpath.'/'.$info['text']['savename'];
                    $data['text'] = $data['text'];
                    $data['text'] = substr($data['text'],3);
                    $data['text_name'] = $info['text']['name'];
                }
            }
            $data['dept_id'] = intval(@$data['dept_id']);
            if($data['dept_id']){
                    $dept = M('Department')->find($data['dept_id']);
                    if($dept){
                        $data['dept'] = $dept['dname'];
                    }
                }else{
                    $data['dept_id'] = $_SESSION['admin']['dept_id'];
                    $dept = M('Department')->find($data['dept_id']);
                    if($dept){
                        $data['dept'] = $dept['dname'];
                    }
                }
            $m = M('Video');
            if($m->add($data)){
                $this->success('添加成功', U('videos'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $depts = M('Department')->field('did, dname')->where(array('pid' => 0))->select();
            $this->assign('depts', $depts);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'videos');
            $this->assign('type',2);
            $this->display();
        }
    }

    public function videos(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('Video');
        $where = array();
        $where1 = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        if($_GET['type']){
            $type = txt($_GET['type']);
            $where1['software'] = array('like', "%{$type}%");
        }
        $where['software'] = 2;
        $dept_id = $_SESSION['admin']['dept_id'];
        if($dept_id != 0){
            $where['dept_id'] = $dept_id;
        }
        $count = $m->where($where)->where($where1)->count();
        $page = getPage($count, 10);
        $infos = $m->where($where)->where($where1)->order('addtime DESC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign('cz',$type);
        $this->assign('infos', $infos);
        //dump($infos);exit;
        $this->assign('page', $page->show());
        $this->assign('dept_id',$dept_id);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'videos');
        $this->display();
    }

    public function videodel($vid){
        $vid = intval($vid);
        if($vid){
            if(M('Video')->delete($vid) !== false){
                $this->success('删除成功', U('videos'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
    //直播添加
    public function addlive(){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        if(IS_POST){
            $data = I('post.');
            $data['status'] = intval($data['status']);
            $data['_order'] = intval($data['_order']);
            $data['software'] = intval($data['software']);
            $data['software'] = 2;
            if (empty($data['software'])){
                $this->error('请选择软件');
            }
            $file = $_FILES['image'];
            if($file['size'] > 0){
                $upinfo = upload('trainimg/');
                if($upinfo['code'] == 0){
                    $data['image'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('Live');
            if($m->add($data)){
                $this->success('添加成功', U('lives'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'addlive');
            $this->assign('type',2);
            $this->display();
        }
    }

    public function lives(){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('Live');
        $where = array();
        $where1 = array();
        //$where1['software'] = 1;
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        if($_GET['type']){
            $type = txt($_GET['type']);
            $where1['software'] = array('like', "%{$type}%");
        }
        $where['software'] = 2;
        $count = $m->where($where)->where($where1)->count();
        $page = getPage($count, 10);
        $infos = $m->where($where)->where($where1)->order('_order ASC')->limit($page->firstRow, $page->listRows)->select();
        
        //$this->assign('cz',$type);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'lives');
        $this->display();
    }

    public function livedel($lid){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        $lid = intval($lid);
        if($lid){
            if(M('Live')->delete($lid) !== false){
                $this->success('删除成功', U('lives'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }

    public function editlive($lid){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        if(IS_POST){
            $data = I('post.');
            $data['id'] = intval($data['id']);
            $data['status'] = intval($data['status']);
            $data['_order'] = intval($data['_order']);
            $data['software'] = intval($data['software']);
            $data['software'] = 2;
            $file = $_FILES['image'];
            if($file['size'] > 0){
                $upinfo = upload('trainimg/');
                if($upinfo['code'] == 0){
                    $data['image'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('Live');
            if($m->save($data)){
                $this->success('保存成功', U('lives'));
            }else{
                $this->error('保存失败,请稍后再试');
            }
        }else{
            $lid = intval($lid);
            if($lid){
                $info = M('Live')->find($lid);
                if($info){
                    $this->assign('info', $info);
                    $this->assign('menus', session('menus'));
                    $this->assign('liclass', 'yjj');
                    $this->assign('type',2);
                    $this->display();
                }
            }
        }
    }
    
    public function trainEdit(){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        if ($dp_id!=0){
            $this->error('此功能暂时只对总台开放');
        }
        if(IS_POST){
            $data = I('post.');
            $data['title'] = trim($data['title']);
            $data['software'] = intval($data['software']);
            $data['software'] = 2;
            if (empty($data['id'])){
                $this->error('缺少相关id参数');
            }
            if($data['stime'] == ''){
                $this->error('请填写直播时间！');
            }
            foreach ($data as $k=>$item){
                if (empty($item)){
                    unset($data[$k]);
                }
            }
            
            $file = $_FILES['image'];
            if($file['size'] > 0){
                $upinfo = upload('trainimg/');
                if($upinfo['code'] == 0){
                    $data['image'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('Traininginfo');
            if($m->save($data)){
                $this->success('修改成功', U('traininfo'));
            }else{
                $this->error('修改失败,请稍后再试');
            }
        }else{
            $where =array();
            if (isset($_GET['id'])){
                $where['id'] = $_GET['id'];
            }else{
                $this->error('缺少id参数');
            }
            $info = M('traininginfo')->where($where)->select();
            if (empty($info)){
                $this->error('未找到该信息或已删除');
            }else{
                $info = $info[0];
            }
            $info['content'] = html_entity_decode($info['content']);
            $this->assign('info',$info);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'trainadd');
            $this->assign('type',2);
            $this->display();
        }
    }
    
    public function videosEdit(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $data['title'] = trim($data['title']);
            $data['software'] = intval($data['software']);
            $data['software'] = 2;
            if (empty($data['id'])){
                $this->error('缺少相关id参数');
            }
            foreach ($data as $k=>$item){
                if (empty($item)){
                    unset($data[$k]);
                }
            }
            //视频上传
            $videos = $_FILES['video'];
            if($videos['size']>0){
                $subname = M('video')->field('url3')->select();
                $ftp = new Ftp();   
                $config = array(
                    'hostname' => '58.59.18.69', //服务器地址
                    'username' => 'gurundong',  //FTP登录账号
                    'password' => '!LeiFeng&F',   //FTP登录密码
                    'port' => 21         //端口号
                );
                $ftp->connect($config);
                $time = time();
                $a = $ftp->upload($_FILES['video']['tmp_name'],'/sannong_video/'.$time.'_'.$_FILES['video']['name']);
                $data['url3'] = 'http://58.59.18.69:9009/'.$time.'_'.$_FILES['video']['name'];
            }

            $file = $_FILES['image'];
            if($file['size'] > 0){
                $upinfo = upload('trainimg/');
                if($upinfo['code'] == 0){
                    $data['image'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $text = $_FILES['text'];
            if($text['size']>0){
                unset($file);
                $path = '../upload/traintext/';
                $subpath = date('Ym');
                $upload = new \Think\Upload();
                $upload->maxSize = 10145728;
                $upload->exts = array('rar', 'zip', 'txt', 'doc','xls','ppt','pdf');
                $upload->mimes = array('application/octet-stream','application/zip','text/plain','application/msword','application/vnd.ms-excel','application/vnd.ms-powerpoint','application/pdf');
                $upload->savePath = $path;
                $upload->subName   =     $subpath;
                $textpath = time().'_'.mt_rand(10,20);
                $upload->saveName  =    $textpath;
                $info = $upload->upload();
                if(empty($info['text'])){
                    $this->error($upload->getError());
                }else{
                    $data['text'] = $path.$subpath.'/'.$info['text']['savename'];
                    $data['text'] = $data['text'];
                    $data['text'] = substr($data['text'],3);
                    $data['text_name'] = $info['text']['name'];
                }
            }
            $m = M('video');
            if($m->save($data)){
                $this->success('修改成功', U('videos'));
            }else{
                $this->error('修改失败,请稍后再试');
            }
        }else{
            $where =array();
            if (isset($_GET['id'])){
                $where['id'] = $_GET['id'];
            }else{
                $this->error('缺少id参数');
            }
            $info = M('video')->where($where)->select();
            if (empty($info)){
                $this->error('未找到该信息或已删除');
            }else{
                $info = $info[0];
            }
            $dept_id = $_SESSION['admin']['dept_id'];
            $depts = M('Department')->field('did, dname')->where(array('pid' => 0))->select();
            $this->assign('depts', $depts);
            //$info['content'] = html_entity_decode($info['content']);
            $this->assign('info',$info);
            $this->assign('menus', session('menus'));
            $this->assign('dept_id',$dept_id);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'videos');
            $this->assign('type',2);
            $this->display();
        }
    }
    
}

class Ftp {
        private $hostname = '';
        private $username = '';
        private $password = '';
        private $port = 21;
        private $passive = TRUE;
        private $debug = TRUE;
        private $conn_id = FALSE;
        /**
         * 构造函数
         *
         * @param array 配置数组 : $config = array('hostname'=>'','username'=>'','password'=>'','port'=>''...);
        */
        public function __construct($config = array()) {
            if(count($config) > 0) {
                $this->_init($config);
            }
        }
        /**
        * FTP连接
        *
        * @access public
        * @param array 配置数组
        * @return boolean
        */
        public function connect($config = array()) {
            if(count($config) > 0) {
            $this->_init($config);
            }
            //判断是否打开了ftp连接 
            if(FALSE === ($this->conn_id = @ftp_connect($this->hostname,$this->port))) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_connect");
                }
                return FALSE;
            }
            //判断是否登录成功
            if( ! $this->_login()) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_login");
                }
                return FALSE;
            }
            //判断是否开启FTP被动模式
            if($this->passive === TRUE) {
                ftp_pasv($this->conn_id, TRUE);
            }
 
            return TRUE;
        }
            /**
              * 目录改变
              *
              * @access public
              * @param string 目录标识(ftp)
              * @param boolean 
              * @return boolean
            */
        public function chgdir($path = '', $supress_debug = FALSE) {
            if($path == '' OR ! $this->_isconn()) {
                return FALSE;
            }
            $result = @ftp_chdir($this->conn_id, $path);
            if($result === FALSE) {
                if($this->debug === TRUE AND $supress_debug == FALSE) {
                $this->_error("ftp_unable_to_chgdir:dir[".$path."]");
            }
            return FALSE;
            }
            return TRUE;
        }
        /**
          * 目录生成
          *
          * @access public
          * @param string 目录标识(ftp)
          * @param int  文件权限列表 
          * @return boolean
        */
        public function mkdir($path = '', $permissions = NULL) {
            if($path == '' OR ! $this->_isconn()) {
                return FALSE;
            }

            $result = @ftp_mkdir($this->conn_id, $path);

            if($result === FALSE) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_mkdir:dir[".$path."]");
                }
            return FALSE;
            }

            if( ! is_null($permissions)) {
                $this->chmod($path,(int)$permissions);
            }

            return TRUE;
        }
        /**
          * 上传
          *
          * @access public
          * @param string 本地目录标识
          * @param string 远程目录标识(ftp)
          * @param string 上传模式 auto || ascii
          * @param int 上传后的文件权限列表

          * @return boolean
        */
        public function upload($localpath, $remotepath, $mode = 'auto', $permissions = NULL) {
            if( ! $this->_isconn()) {
            return FALSE;
            }
            //判断本地文件是否存在
            if( ! file_exists($localpath)) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_no_source_file:".$localpath);
                }
            return FALSE;
            }
            //判断上传模式
            if($mode == 'auto') {
                  //获取文件后缀类型
                $ext = $this->_getext($localpath);
                  //根据后缀类型决定上传模式是 FTP_ASCII(文本模式) 还是 FTP_BINARY(二进制模式);
                $mode = $this->_settype($ext);
            }

            $mode = ($mode == 'ascii') ? FTP_ASCII : FTP_BINARY;
            //上传
            $result = @ftp_put($this->conn_id, $remotepath, $localpath, $mode);
            //判断上传是否成功
            if($result === FALSE) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_upload:localpath[".$localpath."]/remotepath[".$remotepath."]");
                }
                return FALSE;
            }
            //判断是否需要改写文件权限
                if( ! is_null($permissions)) {
                    $this->chmod($remotepath,(int)$permissions);
                }

            return TRUE;
        }
        /**
          * 下载
          *
          * @access public
          * @param string 远程目录标识(ftp)
          * @param string 本地目录标识
          * @param string 下载模式 auto || ascii

          * @return boolean
        */
        public function download($remotepath, $localpath, $mode = 'auto') {
            if( ! $this->_isconn()) {
                return FALSE;
            }

            if($mode == 'auto') {
                $ext = $this->_getext($remotepath);
                $mode = $this->_settype($ext);
            }

            $mode = ($mode == 'ascii') ? FTP_ASCII : FTP_BINARY;

            $result = @ftp_get($this->conn_id, $localpath, $remotepath, $mode);

            if($result === FALSE) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_download:localpath[".$localpath."]-remotepath[".$remotepath."]");
                }
            return FALSE;
            }

            return TRUE;
        }
        /**
          * 重命名/移动
          *
          * @access public
          * @param string 远程目录标识(ftp)
          * @param string 新目录标识
          * @param boolean 判断是重命名(FALSE)还是移动(TRUE)

          * @return boolean
        */
        public function rename($oldname, $newname, $move = FALSE) {
            if( ! $this->_isconn()) {
                return FALSE;
            }
 
            $result = @ftp_rename($this->conn_id, $oldname, $newname);
 
            if($result === FALSE) {
                if($this->debug === TRUE) {
                    $msg = ($move == FALSE) ? "ftp_unable_to_rename" : "ftp_unable_to_move";
                $this->_error($msg);
                }
                return FALSE;
            }
 
            return TRUE;
        }
        /**
          * 删除文件
          *
          * @access public
          * @param string 文件标识(ftp)
          * @return boolean
        */
        public function delete_file($file) {
            if( ! $this->_isconn()) {
                return FALSE;
            }
 
            $result = @ftp_delete($this->conn_id, $file);
 
            if($result === FALSE) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_delete_file:file[".$file."]");
                }
                return FALSE;
            }
 
            return TRUE;
        }
        /**
          * 删除文件夹
          *
          * @access public
          * @param string 目录标识(ftp)
          * @return boolean
        */
        public function delete_dir($path) {
            if( ! $this->_isconn()) {
                return FALSE;
            }
 
            //对目录宏的'/'字符添加反斜杠'\'
            $path = preg_replace("/(.+?)\/*$/", "\\1/", $path);
 
            //获取目录文件列表
            $filelist = $this->filelist($path);
 
            if($filelist !== FALSE AND count($filelist) > 0) {
                foreach($filelist as $item) {
                    //如果我们无法删除,那么就可能是一个文件夹
                    //所以我们递归调用delete_dir()
                    if( ! @delete_file($item)) {
                        $this->delete_dir($item);
                    }
                }
            }
 
            //删除文件夹(空文件夹)
            $result = @ftp_rmdir($this->conn_id, $path);
 
            if($result === FALSE) {
                if($this->debug === TRUE) {
                     $this->_error("ftp_unable_to_delete_dir:dir[".$path."]");
                }
                return FALSE;
            }
             
            return TRUE;
        }
        /**
          * 修改文件权限
          *
          * @access public
          * @param string 目录标识(ftp)
          * @return boolean
        */
        public function chmod($path, $perm) {
            if( ! $this->_isconn()) {
                return FALSE;
            }
 
            //只有在PHP5中才定义了修改权限的函数(ftp)
            if( ! function_exists('ftp_chmod')) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_chmod(function)");
                }
                return FALSE;
            }
 
            $result = @ftp_chmod($this->conn_id, $perm, $path);
             
            if($result === FALSE) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_unable_to_chmod:path[".$path."]-chmod[".$perm."]");
                }
                return FALSE;
            }
            return TRUE;
        }   
        /**
          * 获取目录文件列表
          *
          * @access public
          * @param string 目录标识(ftp)
          * @return array
        */
        public function filelist($path = '.') {
            if( ! $this->_isconn()) {
                return FALSE;
            }
 
            return ftp_nlist($this->conn_id, $path);
        }    
        /**
          * 关闭FTP
          *
          * @access public
          * @return boolean
        */
        public function close() {
            if( ! $this->_isconn()) {
                return FALSE;
            }
         
            return @ftp_close($this->conn_id);
        }
         
        /**
          * FTP成员变量初始化
          *
          * @access private
          * @param array 配置数组  
          * @return void
        */
        private function _init($config = array()) {
            foreach($config as $key => $val) {
                if(isset($this->$key)) {
                    $this->$key = $val;
                }
            }
            //特殊字符过滤
            $this->hostname = preg_replace('|.+?://|','',$this->hostname);
        }
        /**
          * FTP登陆
          *
          * @access private
          * @return boolean
        */
        private function _login() {
            return @ftp_login($this->conn_id, $this->username, $this->password);
        }
        /**
          * 判断con_id
          *
          * @access private
          * @return boolean
        */
        private function _isconn() {
            if( ! is_resource($this->conn_id)) {
                if($this->debug === TRUE) {
                    $this->_error("ftp_no_connection");
                }
                return FALSE;
            }
            return TRUE;
        }
        /**
          * 从文件名中获取后缀扩展
          *
          * @access private
          * @param string 目录标识
          * @return string
        */
        private function _getext($filename) {
            if(FALSE === strpos($filename, '.')) {
                return 'txt';
            }
         
            $extarr = explode('.', $filename);
            return end($extarr);
        }
        /**
        * 从后缀扩展定义FTP传输模式 ascii 或 binary
        *
        * @access private
        * @param string 后缀扩展
        * @return string
        */
        private function _settype($ext) {
            $text_type = array (
                'txt',
                'text',
                'php',
                'phps',
                'php4',
                'js',
                'css',
                'htm',
                'html',
                'phtml',
                'shtml',
                'log',
                'xml'
            );
         
            return (in_array($ext, $text_type)) ? 'ascii' : 'binary';
        }
        /**
        * 错误日志记录
        *
        * @access prvate
        * @return boolean
        */
        private function _error($msg) {
            return @file_put_contents('ftp_err.log', "date[".date("Y-m-d H:i:s")."]-hostname[".$this->hostname."]-username[".$this->username."]-password[".$this->password."]-msg[".$msg."]\n", FILE_APPEND);
        }
 }