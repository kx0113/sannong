<?php
namespace Manage\Controller;
use Common\Common\General;
use Think\Crypt\Driver\Think;
use Common\Common\ImageHandle;

class YjjController extends BaseController {
    
    public function index(){
        session_start();
        //var_dump(session());die();

        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $yjjModel = M('Yjj_user');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['uname'] = array('like', "%{$key}%");

        }
        $count = $yjjModel->where($where)->count();
        $infos = $yjjModel->where($where)->page($p.',10')->select();
        if(!empty($infos)){
            foreach ($infos as &$item){
                if ($item['is_expert']==1){
                    $item['is_expert']='是';
                }else{
                    $item['is_expert']='否';
                }
                if ($item['sex']==0){
                    $item['sex']='女';
                }else{
                    $item['sex']='男';
                }
            }
        }
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'yjjList');
        $this->display();
    }
    
    public function yjjAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $mobile = I('post.mobile','','trim,htmlspecialchars');
            $password = I('post.password','','trim,htmlspecialchars');
            $real_name = I('post.real_name','','trim,htmlspecialchars');
            $sex = I('post.sex','','trim,htmlspecialchars');
            $files = $_FILES['picture'];
            
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['picture'] = 'upload/'.date('Ym').'/'.$img;
            }
            if(!($mobile && $password)){
                $this->error('手机号及密码为必填字段');
            }
            if(!$general->isMobile($mobile)){
                $this->error('不是手机号');
            }
            if(D('YjjUser')->checkUser($mobile)){
                $this->error('该账号已存在');
            }

            $salt = $general->randNum();
            $password = $general->makePassword($password, $salt);
            //$info = array();
            $info['mobile'] = $mobile;
            $info['uname'] = $mobile;
            $info['password'] = $password;
            if ($real_name){
                $info['real_name'] = $real_name;
            }
            $info['sex'] = $sex;
            $info['salt'] = $salt;
            if(D('YjjUser')->addUser($info)){
                redirect(U('index'));;
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjjAdd');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function del($id){
        $id = intval($id);
        if($id){
            $d = D('YjjUser');
            $where = array('uid'=>$id);
            $inf = $d->getUser($where);
            $filename = $inf[0]['picture'];
            //var_dump($inf);die();
            if($filename){
                unlink($filename);
            }
            if(M('Yjj_user')->delete($id)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }
    
    public function yjjEdit($id){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $general = new General();
            $salt = $general->randNum();
            $data['password'] = $general->makePassword($data['password'], $salt);
            $data['salt'] = $salt;
            $info['uname'] = $data['mobile'];
            unset($data['password']);
            if(M('Yjj_user')->save($data) !== false){
                redirect(U('index'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            session_start();
            $id = intval(I('get.id'));
            if($id){
                $yjj = M('Yjj_user');
                $info = $yjj->find($id);
                    $this->assign('menus', session('menus'));
                    $this->assign('liclass', 'yjj');
                    $this->assign('info', $info);
                    $this->display();
                }
            }
        }

    //问政策
    public function wzc(){
        session_start();
        //var_dump(session());die();
        $dept = $_SESSION['admin']['dept_id'];
        $id = $_SESSION['admin']['dept_id'];
        $deptModel = M('department');
        $this->assign('dp_id',$id);
        //var_dump($id);die();
        if ($id!=0){
            $dept1=array();
            $d = $deptModel->where(array('did'=>$id))->select();
            $dept1[0]['dname'] = $d[0]['dname'];
            $dept1[0]['did'] = $d[0]['did'];
        }else{
            $d = $deptModel->field('did,dname')->select();
            $dept1=array();
            for ($i=0;$i<count($d);$i++){
                $dept1[$i]['dname'] = $d[$i]['dname'];
                $dept1[$i]['did'] = $d[$i]['did'];
            }
        }

        $this->assign('dept', $dept1);
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $yjjModel = M('policy');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }  if($_GET['dept_id']){
            $where['dept_id'] = $_GET['dept_id'];
            $this->assign('s_dept_id', $_GET['dept_id']);
        }  if(isset($_GET['s_is_top'])){
            $where['is_top'] = $_GET['s_is_top'];
            if ($where['is_top'] ==0){
                $this->assign('s_is_top',2);
            }else{
                $this->assign('s_is_top', $_GET['s_is_top']);
            }
        }
        if (!$dept){
            $count = $yjjModel->where($where)->order('is_top desc,id desc')->count();
            $infos = $yjjModel->where($where)->order('is_top desc,id desc')->page($p.',10')->select();
        }else{
            $count = $yjjModel->where($where)->where(array('dept_id'=>$dept))->order('is_top desc,id desc')->count();
            $infos = $yjjModel->where($where)->where(array('dept_id'=>$dept))->order('is_top desc,id desc')->page($p.',10')->select();
        }
        if($_GET['dept_id'] || isset($_GET['s_is_top'])){
            $where['dept_id'] = $_GET['dept_id'];
            $this->assign('s_dept_id', $_GET['dept_id']);
            $dept2 = $deptModel->where(array('did'=>"{$where['dept_id']}"))->find();
            $this->assign('dept2',$dept2);
        }
        $deptModel = M('department');
        foreach ($infos as &$v){
            $d = $deptModel->where(array('did'=>$v['dept_id']))->select();
            $v['dept'] = $d[0]['dname']; 
            if($v['is_top']==1){
                $v['top'] = '是';
            }else{
                $v['top'] = '否';
            }
        }
        //var_dump($infos);die();
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'wzc');
        $this->display();
    }
    
    public function wzcAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $title = I('post.title','','trim,htmlspecialchars');
            $introduce = I('post.introduce','','trim,htmlspecialchars');
            $dept_id = I('post.dept_id','','trim,htmlspecialchars');
            $is_top = I('post.is_top','','trim,htmlspecialchars');
            $im = I('post.im');
            $im = str_replace(' ', '', $im);
            //dump($im);exit;
            if (!$dept_id){
                $this->error('部门名称不能为空');
            } if(empty($im)){
                $this->error('IM账号不能为空');
            }
            //$files = I('post.picture','','',$_FILES);
            $files = $_FILES['picture'];

        
            if($files['size'] > 0){
                
                $img = $imghd->image($files);
                $info['picture'] = 'upload/'.date('Ym').'/'.$img;
            }
            if($is_top == 1 ) {
                $info = M('policy')->where(array('dept_id' => $dept_id, 'is_top' => 1))->find();
                if (!empty($info)) {
                    $this->error('新增失败，当前部门已存在题图！');
                }
            }
            $info['title'] = $title;
            $info['introduce'] = $introduce;
            $info['dept_id'] = $dept_id;
            $info['addtime'] = date("Y-m-d H:i:s");
            $info['content'] = stripslashes(I('post.content'));
            $info['im'] = $im;
            /* dump($info['content']);die(); */
            $info['is_top'] = $is_top;
            if(M('policy')->add($info)){
                redirect(U('wzc'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
            $id = $_SESSION['admin']['dept_id'];
            $deptModel = M('department');
            //var_dump($id);die();
            if ($id!=0){
                $dept=array();
                $d = $deptModel->where(array('did'=>$id))->select();
                $dept[0]['dname'] = $d[0]['dname'];
                $dept[0]['did'] = $d[0]['did'];
            }else{
                $d = $deptModel->field('did,dname')->select();
                $dept=array();
                for ($i=0;$i<count($d);$i++){
                    $dept[$i]['dname'] = $d[$i]['dname'];
                    $dept[$i]['did'] = $d[$i]['did'];
                }
            }
            //var_dump($d);die();
            $this->assign('id', $id);
            $this->assign('dept', $dept);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'wzc');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function wzcDel($id){
        $id = intval($id);
        if($id){
            $d = D('DeptArticle');
            $where = array('id'=>$id);
            $table = 'sn_policy';
            $inf = $d->findOne($where,$table);
            if ($inf[0]['picture']){
                $filename = $inf[0]['picture'];
                unlink($filename);
            }
            //var_dump($inf);die();
            if(M('policy')->delete($id)){
                
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }
    
    public function wzcEdit(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $data = I('post.');
            $data['im'] = str_replace(' ', '', $data['im']);
            $data['content'] = stripslashes(I('post.content'));
            $where =array();
            $where['id'] = $data['id'];
//             if(empty($data['im'])){
//                 $this->error('IM账号不能为空');
//             }
            if (empty($where['id'])){
                $this->error('参数错误');
            }else{
                unset($data['id']);
            }
            foreach ($data as $k=>$v){
                if ($v==''){
                    unset($data[$k]);
                }
            }
            $files = $_FILES['picture'];
            if($files['size'] > 0){
                    
                $img = $imghd->image($files);
                $data['picture'] = 'upload/'.date('Ym').'/'.$img;
            }
            $is_top = $data['is_top'];
            $map['dept_id']  =$data['dept_id'];
            $map['is_top']  = 1;
            $id_= I('get.id');
            $map['id']  = array('not in',"$id_");
            $info=M('policy')->where($map)->find();
            if(!empty($info) && $is_top == 1){
                $this->error('新增失败，当前部门已存在题图,类型不能再修改为题图！');
            }

            if(M('policy')->where($where)->save($data)){
                redirect(U('wzc'));
            }else{
                $this->error('修改失败，或数据未改动，请稍后再试');
            }
        }else{
            session_start();
            $did = $_SESSION['admin']['dept_id'];
            $id = intval($_GET['id']);
            $where = array();
            if(!empty($id)){
                $where['id'] = $id;
            }else{
                $this->error('参数错误，请重试');
            }
            $info = M('policy')->where($where)->select();
            if(empty($info)){
                $this->error('文章不存在，请重试');
            }
            $deptModel = M('department');
            if ($did!=0){
                $dept=array();
                $d = $deptModel->where(array('did'=>$did))->select();
                $dept[0]['dname'] = $d[0]['dname'];
                $dept[0]['did'] = $d[0]['did'];
            }else{
                $d = $deptModel->field('did,dname')->select();
                $dept=array();
                for ($i=0;$i<count($d);$i++){
                    $dept[$i]['dname'] = $d[$i]['dname'];
                    $dept[$i]['did'] = $d[$i]['did'];
                }
            }
            $info[0]['content'] = html_entity_decode($info[0]['content']);
            $this->assign('did',$did);
            $this->assign('dept', $dept);
            $this->assign('info',$info[0]);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'wzc');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    //想去玩
    public function xqw(){
        session_start();
        $dept = $_SESSION['admin']['dept_id'];
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $yjjModel = M('play');
        $where = array();
        $where1 = array();
        if(!empty($_GET['keyword'])){
            $key = txt($_GET['keyword']);
            $where['cate_id'] = array('like', "%{$key}%");
        }
        if(!empty($_GET['s_title'])){
            $tt = txt($_GET['s_title']);
            $where1['name'] = array('like', "%{$tt}%");
        }

        $count = $yjjModel->where($where)->where($where1)->order('id desc')->count();
        $infos = $yjjModel->where($where)->where($where1)->order('id desc')->page($p.',10')->select();

        foreach ($infos as &$v){
            if($v['is_top']==1){
                $v['top'] = '是';
            }else{
                $v['top'] = '否';
            }
            switch ($v['cate_id']){
                case 1 :
                    $v['type'] = '采摘';
                    continue;
                case 2 :
                    $v['type'] = '垂钓';
                    continue;
                case 3 :
                    $v['type'] = '游泳';
                    continue;
                case 4 :
                    $v['type'] = '登山';
                    continue;
                default:
                    $v['type'] = '其他';
                    continue;
            }
            
        }
        
        $tt = $tt ? $tt : 0;
        $key = $key ? $key : 0;
        $this->assign('kw', $key);
        $this->assign('tt', $tt);
        //var_dump($key);die();
        $page = getPage($count, 10);
        //var_dump($page->show());die();
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'xqw');
        $this->display();
    }
    
    public function xqwAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $name = I('post.name','','trim,htmlspecialchars');
            $address = I('post.address','','trim,htmlspecialchars');
            $tel = I('post.tel','','trim,htmlspecialchars');
            $cate_id = intval(I('post.cate_id','','trim,htmlspecialchars'));
            $is_top = intval(I('post.is_top','','trim,htmlspecialchars'));
			$longitude = '';
			$latitude = '';
			$longitude = I('post.longitude','','trim,htmlspecialchars');
			$latitude = I('post.latitude','','trim,htmlspecialchars');
			$im = str_replace(' ','',I('post.im'));
            if (!($name && $address && $tel)){
                $this->error('标题、地址、电话不能为空');
            }
            $files = $_FILES['picture'];
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['picture'] = 'upload/'.date('Ym').'/'.$img;
            }else{
                $this->error('图片不能为空');
            }
            if($is_top == 1) {
                if (M('play')->where(array('cate_id' => $cate_id, 'is_top=1'))->select()) {
                    $this->error('题图已经存在');
                }
            }
			if(empty($longitude)){
				$this->error('经度不能为空');
			}
			if(empty($latitude)){
				$this->error('纬度不能为空');
			}
            $info['is_top'] = $is_top;
            $info['name'] = $name;
            $info['address'] = $address;
            $info['tel'] = $tel;
            $info['cate_id'] = $cate_id;
            $info['addtime'] = date("Y-m-d H:i:s");
            $info['content'] = stripslashes(I('post.content'));
			$info['longitude'] = $longitude;
			$info['latitude'] = $latitude;
			$info['im'] = $im;
            if(M('play')->add($info)){
                unset($name,$address,$tel,$cate_id,$is_top);
                redirect(U('xqw'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();

            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'xqw');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function xqwEdit(){
        if (IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $info = array();
            $where = array();
            $info = I('post.');
            $info['content'] = stripslashes(I('post.content'));
			$longitude = '';
			$latitude = '';
			$longitude = I('post.longitude','','trim,htmlspecialchars');
			$latitude = I('post.latitude','','trim,htmlspecialchars');
			$info['longitude'] = $longitude;
			$info['latitude'] = $latitude;
			$info['im'] = str_replace(' ','',I('post.im'));
            $where['id'] = $info['id'];
            if (!empty($where['id'])){
                unset($info['id']);
            }else{
                $this->error('参数错误，请重试');
            }
            foreach ($info as $k=>$v){
                if ($v==''){
                    unset($info[$k]);
                }else{
                    trim($info[$k]);
                }
            }
            if (empty($info)){
                $this->error('没有提交数据');
            }
			if(empty($longitude)){
				$this->error('经度不能为空');
			}
			if(empty($latitude)){
				$this->error('纬度不能为空');
			}
            $is_top = $info['is_top'];
            $map['cate_id'] = $info['cate_id'];
            $map['is_top']  = 1;
            $id_=$_GET['id'];
            $map['id'] = array('not in',"$id_");
            if(M('play')->where($map)->find() && $is_top == 1){
                $this->error('修改失败，题图已经存在，请修改题图类型');
            }
            $files = $_FILES['picture'];
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['picture'] = 'upload/'.date('Ym').'/'.$img;
            }
            if(M('play')->where($where)->save($info)){
                redirect(U('xqw'));
            }else{
                $this->error('编辑失败，或数据未改变请稍后再试');
            }
        }else{
            session_start();
            $id = intval($_GET['id']);
            $where = array();
            if (empty($id)){
                $this->error('参数错误，请重试');
            }
            $where['id'] = $id;
            $info = M('play')->where($where)->select();
            if (empty($info)){
                $this->error('参数错误，未找到该信息');
            }
            $info[0]['content'] = html_entity_decode($info[0]['content']);
            $this->assign('info', $info[0]);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'xqw');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function xqwDel($id){
        $id = intval($id);
        if($id){
            $d = D('DeptArticle');
            $where = array('id'=>$id);
            $table = 'sn_play';
            $inf = $d->findOne($where,$table);
            if ($inf[0]['picture']){
                $filename = $inf[0]['picture'];
                unlink($filename);
            }
            //var_dump($inf);die();
            if(M('play')->delete($id)){
    
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }
    
    public function mmxx(){
        session_start();
        //var_dump(session());die();
        $dept = $_SESSION['admin']['dept_id'];
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $yjjModel = D('Business');
        $where = array();
        if($_GET['s_type'] || $_GET['s_cate_id'] || $_GET['s_title']){
            $s_type = txt($_GET['s_type']);
            $s_cate_id = txt($_GET['s_cate_id']);
            $s_title = txt($_GET['s_title']);
            $where['type'] = array('like', "%{$s_type}%");
            $where['cate_id'] = array('like', "%{$s_cate_id}%");
            $where['title'] = array('like', "%{$s_title}%");
        }
        
        $count = $yjjModel->num($where);
        $infos = $yjjModel->getInfo($where,$p);
        //var_dump($infos);die();
        foreach ($infos as &$v){
            if ($v['type']==1){
                $v['type_desc'] = '出售';
            }elseif ($v['type']==2){
                $v['type_desc'] = '求购';
            }else{
                $v['type_desc'] = '未知';
            }
            switch ($v['cate_id']){
                case 1:
                    $v['cate_desc'] = '农业';
                    continue;
                case 2:
                    $v['cate_desc'] = '林业';
                    continue;
                case 3:
                    $v['cate_desc'] = '畜牧业';
                    continue;
                case 4:
                    $v['cate_desc'] = '农机';
                    continue;
                default:
                    $v['cate_desc'] = '未知';
                    continue;
            }
        }
        $page = getPage($count, 10);
        
        $tp = $s_type ? $s_type : 0;
        $cd = $s_cate_id ? $s_cate_id : 0;
        $tt = $s_title ? $s_title : 0;
        $this->assign('tp', $tp);
        $this->assign('cd', $cd);
        $this->assign('tt', $tt);
        
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'mmxx');
        $this->display();
    }
    
    public function mmxxDel($id){
        $id = intval($id);
        if($id){
            $d = D('Business');
            $where = array('id'=>$id);
            $inf = $d->Detail($where);
            if ($inf[0]['pictures']){
                $filename = $inf[0]['pictures'];
                $arr = explode(',', $filename);
                foreach ($arr as $v){
                unlink($v);
                }
            }
            //var_dump($inf);die();
            if(M('business')->delete($id)){
        
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }
    
    public function mmxxSP(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $yjjModel = D('Business');
        $count = $yjjModel->unSPnum();
        $infos = $yjjModel->getUnSP($p);
        foreach ($infos as &$v){
            if ($v['type']==1){
                $v['type_desc'] = '出售';
            }elseif ($v['type']==2){
                $v['type_desc'] = '求购';
            }else{
                $v['type_desc'] = '未知';
            }
            switch ($v['cate_id']){
                case 1:
                    $v['cate_desc'] = '农业';
                    continue;
                case 2:
                    $v['cate_desc'] = '林业';
                    continue;
                case 3:
                    $v['cate_desc'] = '畜牧业';
                    continue;
                case 4:
                    $v['cate_desc'] = '农机';
                    continue;
                default:
                    $v['cate_desc'] = '未知';
                    continue;
            }
        }
        //var_dump($infos);die();
        $page = getPage($count, 10);
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'mmxxSP');
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->display();
        
    }
    
    public function mmxxSP_OK($id){
        $id = intval($id);
        $status = 2;
        $yjjModel = D('Business');
        //var_dump($id);die();
        if($yjjModel->changeStatus($id,$status)){
            $this->success('已通过');
        }else{
            $this->error('操作失败');
        }
    }
    
    public function mmxxSP_NO($id){
        $id = intval($id);
        $status = 3;
        $yjjModel = D('Business');
        //var_dump($id);die();
        if($yjjModel->changeStatus($id,$status)){
            $this->success('已拒绝');
        }else{
            $this->error('操作失败');
        }
    }
    
    public function getDetail($id){
        $id = intval($id);
        $yjjModel = D('Business');
        $infos = $yjjModel->Detail($id);
        if (empty($infos)){
            $this->error('读取失败');
        }
        foreach ($infos as &$v){
            if ($v['type']==1){
                $v['type_desc'] = '出售';
            }elseif ($v['type']==2){
                $v['type_desc'] = '求购';
            }else{
                $v['type_desc'] = '未知';
            }
            if (!empty($v['pictures'])){
                $v['photo'] = explode(',', $v['pictures']);
            }else{
                $v['photo'] = 0;
            }
            switch ($v['cate_id']){
                case 1:
                    $v['cate_desc'] = '农业';
                    continue;
                case 2:
                    $v['cate_desc'] = '林业';
                    continue;
                case 3:
                    $v['cate_desc'] = '畜牧业';
                    continue;
                case 4:
                    $v['cate_desc'] = '农机';
                    continue;
                default:
                    $v['cate_desc'] = '未知';
                    continue;
            }
        }
        
        //var_dump($infos);die();
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'mmxxSP');
        $this->assign('infos', $infos[0]);
        $this->assign('menus', session('menus'));
        $this->display();
    }
    
    public function zxList(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $where = array();
        if($_GET['s_is_top'] || $_GET['s_cate_id'] || $_GET['s_title']){
            $s_is_top = txt($_GET['s_is_top']);
            $s_cate_id = txt($_GET['s_cate_id']);
            $s_title = txt($_GET['s_title']);
            $where['is_top'] = array('like', "%{$s_is_top}%");
            $where['cate_id'] = array('like', "%{$s_cate_id}%");
            $where['title'] = array('like', "%{$s_title}%");
        }
        $zxModel = M('zixun');
        $infos = $zxModel->field('id,cate_id,title,from,link,addtime,is_top')->where($where)->order('id desc')->select();
        if (!empty($infos)){
            foreach ($infos as $k=>$v){
                if($v['is_top']==1){
                    $infos[$k]['is_top'] = '是';
                }else{
                    $infos[$k]['is_top'] = '否';
                }
                switch ($v['cate_id']){
                    case 1:
                        $infos[$k]['cate'] = '推荐';
                        continue;
                    case 2:
                        $infos[$k]['cate'] = '视频';
                        continue;
                    case 3:
                        $infos[$k]['cate'] = '热点';
                        continue;
                    case 4:
                        $infos[$k]['cate'] = '农事';
                        continue;
                    default:
                        $infos[$k]['cate'] = '未知';
                        continue;
                }
            }    
        }
        $count = $zxModel->where($where)->count();
        $page = getPage($count, 10);
        
        $tp = $s_is_top ? $s_is_top : 0;
        $cd = $s_cate_id ? $s_cate_id : 0;
        $tt = $s_title ? $s_title : 0;
        $this->assign('tp', $tp);
        $this->assign('cd', $cd);
        $this->assign('tt', $tt);
        
        $this->assign('page', $page->show());
        $this->assign('infos',$infos);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'zxList');
        $this->display();
    }
    
    public function zxAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $info = array();
            $info = I('post.');
            $info['content'] = stripslashes(I('post.content'));
            //dump($info);die();
            $cate_id = intval(I('post.cate_id','','trim,htmlspecialchars'));
            if($cate_id==2){
                $info['is_top'] = 2;
            }
            if (!($info['title'] && $info['cate_id'])){
                $this->error('标题、类别不能为空');
            }
            $files = $_FILES['picture'];
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['picture'] = 'upload/'.date('Ym').'/'.$img;
            }else{
                $this->error('图片不能为空');
            }
            $info['addtime'] = date('Y-m-d H:i:s');
            if($info['is_top'] == 1) {
                if (M('zixun')->where(array('cate_id' => $cate_id, 'is_top=1'))->find()) {
                    $this->error('题图已经存在');
                }
            }
            if(M('zixun')->add($info)){
                redirect(U('zxList'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
        
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'zxList');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    
    public function zxEdit(){
        if (IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $info = array();
            $where = array(); 
            $info = I('post.');
            $info['content'] = stripslashes(I('post.content'));
            $where['id'] = intval($info['id']);
            $cate_id = intval(I('post.cate_id','','trim,htmlspecialchars'));
            if($cate_id==2){
                $info['is_top'] = 2;
            }
            foreach ($info as $k=>$v){
                if (empty($v)){
                    unset($info[$k]);
                }else{
                    trim($info[$k]);
                }
            }
//            $cate = $info['cate_id'];
//            $id_=$_GET['id'];
//            $this->error($cate);
            $is_top = $info['is_top'];
            //dump($is_top);exit;
            $map['cate_id'] = $cate_id;
            $map['is_top']  = 1;
            $id_=$_GET['id'];
            $map['id'] = array('not in',"$id_");
            if(M('zixun')->where($map)->find() && $is_top == 1){
                $this->error('题图已经存在');
            }
            $files = $_FILES['picture'];
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $info['picture'] = 'upload/'.date('Ym').'/'.$img;
            }
            if(empty($info) && $files['size']<=0){
                $this->error('未改变数据');
            }
            if(M('zixun')->where($where)->save($info)){
                redirect(U('zxList'));
            }else{
                $this->error('新增失败，或数据未更改，请稍后再试');
            }
        }else{
            session_start();
            $where = array();
            $where['id'] = intval($_GET['id']);
            if(empty($where['id'])){
                $this->error('参数错误，请重试');
            }
            $info = M('zixun')->where($where)->select();
            if (empty($info)){
                $this->error('未找到该数据');
            }
            $info[0]['content'] = html_entity_decode($info[0]['content']);
            //dump($info);die();
            $this->assign('info',$info[0]);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'zxList');
            $this->assign('menus', session('menus'));
            $this->display();
            
        }
    }
    
    public function zxDel($id){
        $id = intval($id);
        if($id){
            $d = D('DeptArticle');
            $where = array('id'=>$id);
            $table = 'sn_zixun';
            $inf = $d->findOne($where,$table);
            if ($inf[0]['picture']){
                $filename = $inf[0]['picture'];
                unlink($filename);
            }
            //var_dump($inf);die();
            if(M('zixun')->delete($id)){
        
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }
    //滴滴农机列表
    public function ddnjList(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $where = array();
        if($_GET['s_username'] || $_GET['s_cate_id'] ){
            $s_username = txt($_GET['s_username']);
            $s_cate_id = txt($_GET['s_cate_id']);
            $where['username'] = array('like', "%{$s_username}%");
            $where['cate_id'] = array('like', "%{$s_cate_id}%");
        }
        $where['status'] = 1;
        $njModel = D('DeptArticle');
        $infos = $njModel->ddnjList($where,$p);
        $yjjModel = M('ddnj');
        $count = $yjjModel->where($where)->count();
        $page = getPage($count, 10);
        foreach ($infos as $k=>$v){
            if ($v['sex']==1){
                $infos[$k]['sex'] = "男";
            }else{
                $infos[$k]['sex'] = "女";
            }
        }
        $cateModel = M('ddnj_cate');
        $cates = $cateModel->field('id,name')->order('corder asc')->select();
        $nm = $s_username ? $s_username : 0;
        $cd = $s_cate_id ? $s_cate_id : 0;
        $this->assign('nm', $nm);
        $this->assign('cd', $cd);
        $this->assign('cates',$cates);
        $this->assign('page', $page->show());
        $this->assign('infos',$infos);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'ddnjList');
        $this->display();
    }
    //滴滴农机后台添加
    public function ddnjAdd(){
        if (IS_POST){
            session_start();
            $infos = I('post.','','trim,htmlspecialchars');
            $infos['account'] = str_replace(' ', '', $infos['account']);
            if (empty($infos['cate_id'])){
                $this->error('农机类型不能为空');
            }
            if (empty($infos['username'])){
                $this->error('机主姓名不能为空');
            }
            if (empty($infos['tel'])){
                $this->error('机主联系方式不能为空');
            }elseif (!preg_match("/^1[34578]\d{9}$/", $infos['tel'])){
                $this->error('机主联系方式不正确');
            }
            if (empty($infos['number_plate'])){
                $this->error('车牌号不能为空');
            }
            if (empty($infos['longitude'])||empty($infos['latitude'])){
                $this->error('经纬度不能为空');
            }
            if (!preg_match("/^1[34578]\d{9}$/", $infos['account'])){
                $this->error('IM互动账号格式不正确');
            }
            $infos['addtime'] = date("Y-m-d");
            $yjjModel = M('ddnj');
            if ($yjjModel->add($infos)){
                redirect(U('ddnjList'));
            }else{
                $this->error('添加失败，请稍后重试');
            }
        }else{
            session_start();
            $njModel = M('ddnj_cate');
            $cates = $njModel->order('corder asc')->select();
            $this->assign('cate',$cates);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'ddnjList');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    //滴滴农机删除
    public function ddnjDel($id){
        $id = intval($_GET['id']);
        $yjjModel = M('ddnj');
        if (!empty($id)){
            if($yjjModel->delete($id)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }else{
            $this->error('参数错误,请稍后再试');
        }
    }
    //滴滴农机编辑
    public function ddnjEdit(){
        if (IS_POST){
            session_start();
            $data = array();
            $where = array();
            $data = I('post.','','trim,htmlspecialchars');
            $data['account'] = str_replace(' ', '', $data['account']);
            if (!preg_match("/^1[34578]\d{9}$/", $data['account'])){
                $this->error('IM互动账号格式不正确');
            }
            $where['id'] = intval($data['id']);
            if (empty($where)){
                $this->error('数据错误，请重试');
            }else{
                unset($data['id']);
            }
            foreach ($data as $key=>$val){
                if (empty($val)){
                    unset($data[$key]);
                }
            }
            $yjjModel = M('ddnj');
            if ($yjjModel->where($where)->save($data)){
                redirect(U('ddnjList'));
            }else{
                $this->error('修改失败，或数据未改动，请稍后再试');
            }
        }else{
            session_start();
            $id = intval($_GET['id']);
            $where = array();
            if (empty($id)){
                $this->error('参数错误');
            }
            $where['id'] = $id;
            $yjjModel = M('ddnj');
            $info = $yjjModel->where($where)->select();
            $cates = M('ddnj_cate')->field('id,name')->select();
            //var_dump($info);die();
            if (empty($info)){
                $this->error('查不到该信息，请重试');
            }else{
                $this->assign('info',$info[0]);
                $this->assign('cate',$cates);
                $this->assign('liclass', 'yjj');
                $this->assign('aclass', 'ddnjList');
                $this->assign('menus', session('menus'));
                $this->display();
            }
        }
        
    }
    //滴滴农机审批列表
    public function ddnjApprovalList(){
        $where = array();
        $where['status'] = 0;
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $count = M('ddnj')->where($where)->order('id asc')->count();
        $res = M('ddnj')->where($where)->order('id asc')->page($p.',10')->select();
        foreach ($res as $key=>$val){
            
            $where1 = array();
            $where1['id'] = $val['cate_id'];
            $mes = M('ddnj_cate')->field('name')->where($where1)->find();
            if (!empty($mes)){
                $res[$key]['cate'] = $mes['name'];
            }else{
                $res[$key]['cate'] = '未知';
            }
        }
        $page = getPage($count, 10);
        $this->assign('info',$res);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'ddnjapproval');
        $this->display();
    }
    
    //滴滴农机审批-通过
    public function ddnjApproval_OK(){
        $where['id'] = intval(I('get.id'));
        if (empty($where['id'])){
            $this->error('缺少相关参数');
        }
        $data = array('status'=>1);
        $res = M('ddnj')->where($where)->save($data);
        if ($res){
            $this->success('操作成功',U('ddnjApprovalList'));
        }else{
            $this->error('操作失败',U('ddnjApprovalList'));
        }
    }
    
    //滴滴农机审批-拒绝
    public function ddnjApproval_NO(){
        $where['id'] = intval(I('get.id'));
        if (empty($where['id'])){
            $this->error('缺少相关参数');
        }
        $data = array('status'=>2);
        $res = M('ddnj')->where($where)->save($data);
        if ($res){
            $this->success('操作成功',U('ddnjApprovalList'));
        }else{
            $this->error('操作失败',U('ddnjApprovalList'));
        }
    }
    
    //滴滴农机-审批详情
    public function njDetail(){
        $where = array();
        $where['id'] = intval(I('get.id'));
        $info = M('ddnj')->where($where)->find();
        if (!empty($info)){
            $where1['id'] = $info['cate_id'];
            $cate_name = M('ddnj_cate')->field('name')->where($where1)->find();
            if (!empty($cate_name['name'])){
                $info['cate'] = $cate_name['name'];
            }else{
                $info['cate'] = '未知';
            }
        }else{
            $this->error('您所请求的农机信息不存在');
        }
        $this->assign('infos',$info);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'ddnjapproval');
        $this->display();
    }
    
    //滴滴农机类型列表
    public function ddnjCate(){
        session_start();
        $njModel = M('ddnj_cate');
        $cates = $njModel->order('corder asc')->select();
        
        $this->assign('cates', $cates);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'ddnjCate');
        $this->display();
    }
    //滴滴农机类型添加
    public function njCateAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $info = array();
            $info['name'] = I('post.name','','trim,htmlspecialchars');
            $info['corder'] = intval(I('post.corder','','trim,htmlspecialchars'));
            if (!($info['name'] && $info['corder'])){
                $this->error('名称、展示顺序不能为空');
            }
            $info['addtime'] = date("Y-m-d");
            if(M('ddnj_cate')->add($info)){
                redirect(U('ddnjCate'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
        
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'ddnjCate');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    //滴滴农机类型编辑
    public function njCateEdit(){
        if(IS_POST){
            $general = new General();
            $info = array();
            $where = array();
            $where['id'] = intval(I('post.id','','trim,htmlspecialchars'));
            $info['name'] = I('post.name','','trim,htmlspecialchars');
            $info['corder'] = intval(I('post.corder','','trim,htmlspecialchars'));
            if (!($where['id'])){
                $this->error('id错误，请重试');
            }
            if ($info['name'] == ''){
                unset($info['name']);
            }
            if ($info['corder'] == ''){
                unset($info['corder']);
            }
            if (!(isset($info['name']) || isset($info['corder']))){
                $this->error('没有更改数据');
            }
            
            $info['addtime'] = date("Y-m-d");
            //var_dump($info);die();
            if(M('ddnj_cate')->where($where)->save($info)){
                redirect(U('ddnjCate'));
            }else{
                $this->error('编辑失败或数据未改动，请稍后再试');
            }
        }else{
            $id = intval($_GET['id']);
            $where = array();
            $where['id'] = $id;
            $yjjModel = M('ddnj_cate');
            $info = $yjjModel->where($where)->select();
            if(empty($info)){
                $this->error('查询不到该分类，请尝试刷新');
            }
            $this->assign('infos', $info[0]);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'ddnjCate');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    //滴滴农机种类删除
    public function njCateDel($id){
        $id = intval($id);
        if(!empty($id)){
            //var_dump($inf);die();
            if(M('ddnj_cate')->delete($id)){
        
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }else{
            $this->error('参数错误，请稍后再试');
        }
    }
    /*
    * 广告图首页
    * by King
    * 2016-10-28
    * */
    public function banner_index(){
        $banner = M('banner');
        $banner_list = $banner->where(array('status'=>1))->order('id asc')->select();
//        dump($banner_list);exit;
        $this->assign('banner_list', $banner_list);
        $this->assign('liclass', 'notice');
        $this->assign('aclass', 'banner');
        $this->assign('menus', session('menus'));
        $this->display();
    }
    /*
  * 广告图首页-增加
  * by King
  * 2016-10-28
  * */
    public function banner_add(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            if(empty($_FILES['picture'])){
                $this->error('选择图片！');
            }
            $data = I('post.');
//            $data['menu_id'] = intval($data['menu_id']);
//            $data['dept_id'] = $admin['dept_id'];
            $data['status'] =1;
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('banner');
            $type = $m->where(array('type'=>$_POST['type']))->count();
            if($type >= 3){
                $this->error('最多只能添加3张广告图！', U('banner_index'));
                exit;
            }else{
                if($m->add($data)){
                    $this->success('保存成功', U('banner_index'));
                }else{
                    $this->error('保存失败,请稍后再试', U('banner_index'));
                }
            }
        }else{
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'banner');
            $this->assign('menus', session('menus'));
            $this->display();
        }

    }
    /*
    * 广告图首页-编辑
    * by King
    * 2016-10-28
    * */
    public function banner_edit(){
        session_start();
        $admin = session('admin');
        $id = $_GET['id'];
        $m = M('banner');
        if(IS_POST){
            $data = I('post.');
//            $data['menu_id'] = intval($data['menu_id']);
//            $data['dept_id'] = $admin['dept_id'];
            $data['status'] =1;
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }

            if($m->where(array('id'=>$id))->save($data)){
                $this->success('保存成功', U('banner_index'));
            }else{
                $this->error('保存失败,请稍后再试', U('banner_index'));
            }
        }else{
            $info = $m->find($id);
            $this->assign('info', $info);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'banner');
            $this->assign('menus', session('menus'));
            $this->display("banner_add");
        }

    }
    /*
    * 广告图首页-删除
    * by King
    * 2016-10-28
    * */
    public function banner_del($id){
        $id = $_GET['id'];

        if(!empty($id)){
            //var_dump($inf);die();
            if(M('banner')->delete($id)){

                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }else{
            $this->error('参数错误，请稍后再试');
        }
    }
    //示范点列表
    public function sfdList(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $where = array();
        if($_GET['s_username'] ){
            $s_username = txt($_GET['name']);
            $where['name'] = array('like', "%{$s_username}%");

        }
        $sfdModel = M('sfd');
        $count = $sfdModel->where($where)->count();
        $infos = $sfdModel->where($where)->order('addtime desc')->page($p.',10')->select();

        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'sfdList');
        $this->display();
    }
    //示范点后台添加
    public function sfdAdd(){
        if (IS_POST){
            session_start();
            $infos = I('post.','','trim,htmlspecialchars');

            if (empty($infos['name'])){
                $this->error('标题不能为空');
            }

            if (empty($infos['lng'])||empty($infos['lat'])){
                $this->error('经纬度不能为空');
            }
            $infos['addtime'] = date("Y-m-d");
            $yjjModel = M('sfd');
            if ($yjjModel->add($infos)){
                redirect(U('sfdList'));
            }else{
                $this->error('添加失败，请稍后重试');
            }
        }else{
            session_start();

            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'sfdList');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    //易家家后台部门文章聊天人员列表
    public function yjj_bmltList(){
        session_start();
        $m1 = M('department');
        $list = $m1->field('did,dname,yjj_server')->where()->select();
        foreach ($list as $k=>$v){
            $where = array();
            $where['uname'] = $v['yjj_server'];
            $info = M('yjj_user')->field('uid,uname,real_name,mobile')->where($where)->select();
            if (!empty($info)){
                $list[$k]['real_name'] = $info[0]['real_name'];
                $list[$k]['mobile'] = $info[0]['mobile'];
            }else{
                $list[$k]['real_name'] = '';
                $list[$k]['mobile'] = '';
            }
        }
        $this->assign('list',$list);
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'yjj_bmltList');
        $this->assign('menus', session('menus'));
        $this->display();
    }
    //易家家部门文章聊天人员修改
    public function yjj_bmltEdit(){
        if (IS_POST){
            if (isset($_POST['did'])){
                $where = array();
                $where['did'] = intval($_POST['did']);
                if (empty($where)){
                    $this->error('请求数据有误');
                }
            }else{
                $this->error('非法请求');
            }
            if (isset($_POST['yjj_server'])){
                $data = array();
                $search = array();
                $data['yjj_server'] = trim($_POST['yjj_server']);
                $search['uname'] = $data['yjj_server'];
            }else{
                $this->error('请输入账号');
            }
            $m1 = M('yjj_user');
            $checkinfo1 = $m1->where($search)->select();
            if (empty($checkinfo1)){
                $this->error('不存在该用户');
            }
            $m2 = M('department');
            $checkinfo2 = $m2->where($data)->select();
            if (!empty($checkinfo2)){
                $this->error('该用户已负责某个部门的维护，请重新选取');
            }
            if($m2->where($where)->save($data)){
                $this->success('修改成功',U('yjj_bmltList'));
            }else{
                $this->error('修改失败，请稍后再试');
            }
        }else{
            session_start();
            if (isset($_GET['did'])){
                $where = array();
                $where['did'] = intval($_GET['did']);
            }else{
                $this->error('非法请求');
            }
            $m1 = M('department');
            $info = $m1->where($where)->select();
            if (empty($info)){
                $this->error('未找到该部门');
            }
            $this->assign('info',$info[0]);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjj_bmltList');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    public function yjj_notice(){
        session_start();
        //var_dump(session());die();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $eptModel = M('Notice');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        $where['type'] = 2;
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
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'yjj_notice');
        $this->display();
    }
    public function yjj_notice_add(){
        if(IS_POST){
            $type = I('type','','trim,htmlspecialchars');
            $did = I('did','','trim,htmlspecialchars');
            $title = I('title','','trim,htmlspecialchars');
            $content = I('content','','trim,htmlspecialchars');
            $data = array('type' => $type,'did' => $did, 'title' => $title, 'content' => $content,'addtime'=> date('Y-m-d h:i:s'));
            //var_dump($data);die();
            $length = mb_strlen($content,'utf8');
            if($length >200){
                $this->error('您所输入的内容字数不得大于200');
            }
            $data['type'] = 2;
            if(M('Notice')->add($data)){
                redirect(U('Manage/Yjj/yjj_notice'));
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
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjj_notice');
            $this->assign('type',2);
            $this->display();
        }
    }
    public function yjj_notice_edit(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            //var_dump($data);die();
            $length = mb_strlen($data['content'],'utf8');
            if($length >200){
                $this->error('您所输入的内容字数不得大于200');
            }
            $general = new General();

            if ($data['did']=='请选择部门'){
                unset($data['did']);
            }
            $data['type'] = 2;
            if(M('notice')->save($data) !== false){
                redirect(U('Manage/Yjj/yjj_notice'));
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
                $this->assign('liclass', 'yjj');
                $this->assign('aclass', 'yjj_notice');
                $this->assign('info', $info);
                $this->assign('type',2);
                $this->display();
            }
        }
    }
    public function yjj_notice_del($id){
        $id = intval($id);
        if(M('notice')->where('id='.$id)->delete()){
            redirect(U('Manage/Yjj/yjj_notice'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjj_notice');
        }else{
            $this->error('删除失败，请稍后再试');
        }
    }
    public function yjj_adver(){
        $banner = M('banner');
        $banner_list = $banner->where(array('status'=>1,'type'=>2))->order('id asc')->select();
        $this->assign('banner_list', $banner_list);
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'yjj_adver');
        $this->assign('menus', session('menus'));
        $this->display();
    }
    public function yjj_adver_add(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            if(empty($_FILES['picture'])){
                $this->error('选择图片！');
            }
            $data = I('post.');
//            $data['menu_id'] = intval($data['menu_id']);
//            $data['dept_id'] = $admin['dept_id'];
            $data['status'] =1;
            $data['type'] = 2;
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $m = M('banner');
            $type = $m->where(array('type'=>$_POST['type']))->count();
            if($type >= 3){
                $this->error('最多只能添加3张广告图！', U('Manage/Yjj/yjj_adver'));
                exit;
            }else{
                if($m->add($data)){
                    $this->success('保存成功', U('Manage/Yjj/yjj_adver'));
                }else{
                    $this->error('保存失败,请稍后再试', U('Manage/Yjj/yjj_adver'));
                }
            }
        }else{
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjj_adver');
            $this->assign('type',2);
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    public function yjj_adver_edit(){
        session_start();
        $admin = session('admin');
        $id = $_GET['id'];
        $m = M('banner');
        if(IS_POST){
            $data = I('post.');
//            $data['menu_id'] = intval($data['menu_id']);
//            $data['dept_id'] = $admin['dept_id'];
            $data['status'] =1;
            $data['type'] = 2;
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['picture'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            if($m->where(array('id'=>$id))->save($data)){
                $this->success('保存成功', U('Manage/Yjj/yjj_adver'));
            }else{
                $this->error('保存失败,请稍后再试', U('Manage/Yjj/yjj_adver'));
            }
        }else{
            $info = $m->find($id);
            $this->assign('info', $info);
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'yjj_adver');
            $this->assign('menus', session('menus'));
            $this->assign('type',1);
            $this->display("yjj_adver_add");
        }
    }
    public function yjj_adver_del()
    {
        $id = $_GET['id'];

        if (!empty($id)) {
            //var_dump($inf);die();
            if (M('banner')->delete($id)) {
                $this->assign('liclass', 'yjj');
                $this->assign('aclass', 'yjj_adver');
                redirect(U('Manage/Yjj/yjj_adver'));
            } else {
                $this->error('删除失败，请稍后再试');
            }
        } else {
            $this->error('参数错误，请稍后再试');
        }

    }
    /*
     * 地理位置-坐标-首页
     * by King
     * 2016-10-31
     * */
    public function coordinate_index(){

        $m = M("example");
        $gid = intval($_GET['gid']);
        $example_find = $m->where(array('id'=>$gid))->find();
        $this->assign('example_find', $example_find);
        //搜索
        if($_POST['s_status']){
            $where['status'] = array("like","%".$_POST['s_status']."%");
            $where['type'] = 1;
        }
        if($_POST['keyword']){
            $where['name'] = array("like","%".$_POST['keyword']."%");
            $where['type'] = 1;
        }
        $where['type'] = 1;
        $example_list = $m->where($where)->order('id desc')->select();
        $this->assign('example_list', $example_list);

        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'yjj');
        $this->assign('aclass', 'coordinate_index');
        $this->display();
    }
    /*
    * 地理位置-坐标-添加
    * by King
    * 2016-10-31
    * */
    public function coordinate_add(){
        $gid = intval($_GET['gid']);
        $example_find = M('example')->where(array('id'=>$gid))->find();
        if($_POST){
            $data = I('post.');
            $data['type'] = 1;
            $data['name'] = trim($data['name']);
            $data['longitude'] = $data['longitude'];
            $data['latitude'] = $data['latitude'];
            $data['time'] = date('Y-m-d H-i-s');
            $res = M('example')->add($data);
            if($res){
                $this->success('添加成功', U('coordinate_index'));
            }else{
                $this->error('添加失败,请稍后再试', U('coordinate_index'));
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'coordinate_index');
            $this->assign('example_find', $example_find);
            $this->display();
        }
    }
    /*
   * 地理位置-坐标-编辑
   * by King
   * 2016-10-31
   * */
    public function coordinate_edit(){
        $example_find = M('example')->where(array('id'=>$gid))->find();
        $id = intval($_GET['id']);
        $example_finds = M('example')->where(array('id'=>$id))->find();
        $example_finds['content'] = html_entity_decode($example_finds['content']);
        if($_POST){
            $data = I('post.');
            $data['type'] = 1;
            $data['name'] = trim($data['name']);
            $data['longitude'] = $data['longitude'];
            $data['latitude'] = $data['latitude'];
            $data['time'] = date('Y-m-d H-i-s');
            $res = M('example')->where(array('id'=>$_GET['id']))->save($data);
            if($res){
                $this->success('修改成功', U('coordinate_index'));
            }else{
                $this->error('修改失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'yjj');
            $this->assign('aclass', 'coordinate_index');
            $this->assign('example_find', $example_find);
            $this->assign('example_finds', $example_finds);
            $this->display('coordinate_add');
        }
    }
    /*
     * 地理位置-删除
     * by King
     * 2016-10-28
     * */
    public function coordinate_del(){
        $id = intval($_GET['id']);
        $m =M('example');
        if($id){
            if($m->delete($id) !== false){
                $this->success('删除成功', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
            }else{
                $this->error('删除失败,请稍后再试', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
            }
        }
    }

}