<?php
namespace Manage\Controller;
use Common\Common\General;
use Think\Crypt\Driver\Think;
use Common\Common\ImageHandle;

class YglController extends BaseController {

    public function index(){
        session_start();
        //var_dump(session());die();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $yglModel = M('Ygl_user');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['department'] = array('like', "%{$key}%");
        }
        $count = $yglModel->where($where)->count();
        $infos = $yglModel->where($where)->page($p.',10')->select();
        if(!empty($infos)){
            foreach ($infos as &$item){
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
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'yglList');
        $this->display();
    }

    public function yglAdd(){
        if(IS_POST){
            session_start();
            $general = new General();
            $imghd = new ImageHandle();
            $control = $_SESSION['admin']['dept_id'];
            $mobile = I('post.mobile','','trim,htmlspecialchars');
            $password = I('post.password','','trim,htmlspecialchars');
            $real_name = I('post.real_name','','trim,htmlspecialchars');
            $sex = I('post.sex','','trim,htmlspecialchars');
            $dept_id = I('post.dept_id',"$control",'trim,htmlspecialchars');
            $office = I('post.office','','trim,htmlspecialchars');
            $position = I('post.position','','trim,htmlspecialchars');

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
            if(D('YglUser')->checkUser($mobile)){
                $this->error('该账号已存在');
            }

            $salt = $general->randNum();
            $password = $general->makePassword($password, $salt);

            $info['dept_id'] = $dept_id;
            $m = M('Department');
            $dep = $m->where("did = $dept_id")->select();
            if ($dep){
                $info['department'] = $dep[0]['dname'];
            }else{
                $this->error('没有该部门');
            }

            $info['mobile'] = $mobile;
            $info['account'] = $mobile;
            $info['password'] = $password;
            if ($real_name){
                $info['real_name'] = $real_name;
            }
            if ($position){
                $info['position'] = $position;
            }
            if ($office){
                $info['office'] = $office;
            }
            $info['sex'] = $sex;
            $info['salt'] = $salt;
            $info['auth'] = intval(I('post.auth'));
            $info['is_group'] = intval(I('post.is_group'));
            $info['is_service'] = intval(I('post.is_service'));
            if(D('YglUser')->addUser($info)){
                //注册环信模块
                $hx = new \Org\Huanxin\Huanxin;
                $res = $hx->hx_register($mobile,123456);
                //dump($res);die();
                redirect(U('index'));
            }else{
                $this->error('新增失败，请稍后再试');
            }
        }else{
            session_start();
            $admin = session('admin');
            $this->assign('admin_dept', $admin['dept_id']);
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'yglAdd');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }

    public function del($id){
        $id = intval($id);
        //var_dump($id);die();
        if($id){
            $d = D('YglUser');
            $where = array('uid'=>$id);
            $inf = D('YglUser')->getUser($where);
            $mobile = $inf[0]['account'];
            $filename = $inf[0]['headimg'];
            //var_dump($filename);die();
            if($filename){
                unlink($filename);
            }
            $hx = new \Org\Huanxin\Huanxin;
            $res = $hx->hx_user_delete($mobile);
            if(M('Ygl_user')->delete($id)){
                
                $this->success('删除成功');
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }
    }

    public function yglEdit($id){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $general = new General();
//            $salt = $general->randNum();
//            dump($data);die();
//            $data['password'] = $general->makePassword($data['password'], $salt);
//            $data['salt'] = $salt;
            $data['account'] = $data['mobile'];
            if (!isset($data['dept_id'])){
                $data['dept_id'] = $_SESSION['admin']['dept_id'];
            }
            $imghd = new ImageHandle;
            $files = $_FILES['headimg'];
            if($files['size'] > 0){
                $img = $imghd->image($files);
                $data['headimg'] = 'upload/'.date('Ym').'/'.$img;
            }
            if($data['headimg']){
                $d = D('YglUser');
                $where = array('uid'=>$data['uid']);
                $inf = D('YglUser')->getUser($where);
                $filename = $inf[0]['headimg'];
                //var_dump($filename);die();
                if($filename){
                    unlink($filename);
                }
            }
            $m = M('Department');
            $did = $data['dept_id'];
            $dep = $m->where("did = $did")->select();
            if ($dep){
                $data['department'] = $dep[0]['dname'];
            }else{
                $this->error('没有该部门');
            }
//            dump($data);die();
            if(M('Ygl_user')->save($data) !== false){
                redirect(U('index'));
            }else{
                $this->error('保存失败，请稍后再试');
            }
        }else{
            session_start();
            $admin = session('admin');

            $id = intval(I('get.id'));
            if($id){
                $ygl = M('Ygl_user');
                $info = $ygl->find($id);
                $this->assign('admin_dept', $admin['dept_id']);
                    $this->assign('menus', session('menus'));
                    $this->assign('liclass', 'ygl');
                    $this->assign('info', $info);
                    $this->display();
                }
            }
        }

/*     public function tt(){
        dump($_SERVER);die();
    } */
    public function project(){
        session_start();
        $admin = session('admin');
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('Project');
        //搜索11
        if($_GET['s_status']){
            $where['status'] = $_GET['s_status'];
        }
        if($admin['dept_id'] != 0) {
            $where = array('dept_id' => $admin['dept_id']);
        }
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['ptitle'] = array('like', "%{$key}%");
        }
        $count = $m->where($where)->count();
        $page = getPage($count, 10);
        $infos = $m->where($where)->order('addtime DESC')->page($p.',10')->select();
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'project');
        $this->display();
    }

    public function projectAdd(){
        session_start();
        $admin = session('admin');
        $dept_id = $admin['dept_id'];
        if(IS_POST){
            $data = I('post.');
            $data['introduce'] = stripslashes(I('post.introduce'));
            $data['status'] = intval($data['status']);
            $data['addtime'] = date('Y-m-d');
            if($dept_id == 0){
                $data['department'] = $_POST['department'];
                $dept = M('department')->field('did')->where(array('dname'=>$data['department']))->find();
                $data['dept_id'] = $dept['did'];
            }elseif($dept_id != 0){
                $data['department'] = $admin['department'];
                $data['dept_id'] = $admin['dept_id'];
            }
            if($data['stime'] == ''){
                $this->error('请输入开始时间');
            }
            if($data['etime'] == ''){
                $this->error('请输入结束时间');
            }
            if($_FILES['pic']['size'] > 0){
                $upinfo = upload('proimg/');
                //var_dump($upinfo);die();
                if($upinfo['code'] == 0){
                    $data['pic'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            if(M('Project')->add($data)){
                $this->success('添加成功', U('project'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $department = M('department')->field('dname')->select();
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'project');
            $this->assign('dept_id',$dept_id);
            $this->assign('department',$department);
            $this->display();
        }
    }
    public function projectedit($pid){
        session_start();
        $admin = session('admin');
        $dept_id = $admin['dept_id'];
        if(IS_POST){
            $data = I('post.');
            $data['status'] = intval($data['status']);
            $data['dept_id'] = $dept_id;
            $data['department'] = $admin['department'];
            $data['introduce'] = stripslashes(I('post.introduce'));
            $data['addtime'] = date('Y-m-d');
            if($data['stime'] == ''){
                $this->error('请输入开始时间');
            }
            if($data['etime'] == ''){
                $this->error('请输入结束时间');
            }
            if($_FILES['pic']['size'] > 0){
                $upinfo = upload('proimg/');
                //var_dump($upinfo);die();
                if($upinfo['code'] == 0){
                    $data['pic'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
//            dump($admin);die();
            if(M('Project')->where(array('pid'=>$pid))->save($data)){
                $this->success('修改成功', U('project'));
            }else{
                $this->error('修改失败,请稍后再试');
            }
        }else{
            
            if($pid){
                $project = M('project');
                $info = $project->where(array('pid'=>$pid))->find();;
                $info['introduce'] = html_entity_decode($info['introduce']);
                $this->assign('admin_dept', $admin['dept_id']);
                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'project');
                $this->assign('info', $info);
                $this->assign('dept_id',$dept_id);
                $this->display();
            }
        }
    }

    public function delPro($pid){
        $pid = intval($pid);
        if($pid){
            if(M('Project')->delete($pid) !== false){
                $this->success('删除成功', U('project'));
            }else{
                    $this->error('删除失败,请稍后再试');
            }
        }
    }

    public function adddevice(){
        session_start();
        if(IS_POST){
            $admin = session('admin');
            $data = I('post.');
            $data['dept'] = @intval($data['dept']);
            if(!$data['dept']){
                $data['dept'] = $admin['dept_id'];
            }
            $data['deviceid'] = trim($data['deviceid']);
            if(M('Device')->add($data)){
                $this->success('添加成功', U('devices'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'device');
            $this->display();
        }
    }

    public function devices(){
        session_start();
        $admin = session('admin');
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('Device');
        if($admin['dept_id']){
            $where = array('dept' => $admin['dept_id']);
        }
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['name'] = array('like', "%{$key}%");
        }
        $count = $m->where($where)->count();
        $page = getPage($count, 10);
        $devices = $m->where($where)->page($p.',10')->select();
        foreach($devices as $key => $val){
            if($val['dept']){
                $dept_name = M('Department')->find($val['dept']);
                $devices[$key]['dept'] = $dept_name['dname'];
            }else{
                $devices[$key]['dept'] = '未选择';
            }
        }
        $this->assign('devices', $devices);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'device');
        $this->display();
    }

    public function devicedel($id){
        $id = intval($id);
        if($id){
            if(M('Device')->delete($id) !== false){
                $this->success('删除成功', U('devices'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }

    public function deviceset($id){
        session_start();
        if(IS_POST){
            $admin = session('admin');
            $data = I('post.');
            $data['dept'] = @intval($data['dept']);
            if(!$data['dept']){
                $data['dept'] = $admin['dept_id'];
            }
            $data['deviceid'] = trim($data['deviceid']);
            if(M('Device')->save($data)){
                $this->success('保存成功', U('devices'));
            }else{
                $this->error('保存失败,请稍后再试');
            }
        }else{
            $id = intval($id);
            if($id){
                $info = M('Device')->find($id);
                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'device');
                $this->assign('info', $info);
                $this->display();
            }
        }
    }
    /*
     * 大数据-首页
     * */
    public function big_data_index(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("big_data");
        $where1 = array();
        $where2 = array();
        $where = array('type'=>1);
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where1['title'] = array('like', "%{$key}%");
        }
        if($_GET['s_status']){
            $s_status = txt($_GET['s_status']);
            $where2['status'] = array('like', "%{$s_status}%");
        }
        //var_dump($where2);die();
        $count = $m->where($where)->where($where1)->where($where2)->count();
        $page = getPage($count, 10);


//        dump($count);exit;

        $big_data = $m->where($where)->where($where1)->where($where2)->order('id desc')->page($p.',10')->select();
        //dump($big_data);exit;

        $st = $s_status ? $s_status : 0;
        $tt = $key ? $key : 0;
        $this->assign('st', $st);
        $this->assign('key', $key);

        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'big_data');
        $this->assign('big_data', $big_data);
        $this->display();
    }
    /*
    * 大数据-新增页面
    * */
    public function big_data_add(){
        session_start();
        if(IS_POST){
            if(empty($_POST['title'])){
                $this->error('请填写标题！');
            }
            if(empty($_POST['status'])){
                $this->error('请选择状态！');
            }
            if(empty($_POST['content'])){
                $this->error('请填写内容！');
            }

            $data = I('post.');
            if (empty($data['afrom'])){
                $data['afrom'] = '其他';
            }
            $data['title'] = trim($data['title']);
            $data['status'] = intval($data['status']);
            $data['content'] = stripslashes(I('post.content'));
            $data['addtime'] = date('Y-m-d H:i:s');
            if(M('big_data')->add($data)){
                $this->success('添加成功', U('big_data_index'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'big_data');
            $this->display();
        }

    }
        /*
        * 大数据-删除
        * */
    public function del_big_data($pid){
        $pid = intval($pid);
        if($pid){
            if(M('big_data')->delete($pid) !== false){
                $this->success('删除成功', U('big_data_index'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
    /*
  * 大数据-编辑
  * */
    public function big_data_edit($id){
        $id = intval($id);
        if(IS_POST){
            if(empty($_POST['title'])){
                $this->error('请填写标题！');
            }
            if(empty($_POST['status'])){
                $this->error('请选择状态！');
            }
            if(empty($_POST['content'])){
                $this->error('请填写内容！');
            }
            $data = I('post.');
            $data['title'] = trim($data['title']);
            if (empty($data['afrom'])){
                unset($data['afrom']);
            }
//            dump($data);exit;
            if(M('big_data')->where(array('id'=>$id))->save($data)){
                $this->success('保存成功', U('big_data_index'));
            }else{
                $this->error('保存失败,请稍后再试');
            }
        }else{
            $edit_big_data = M("big_data")->where(array('id'=>$id))->find();
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'big_data');
            $edit_big_data_content = html_entity_decode($edit_big_data['content']);
            $this->assign('edit_big_data',$edit_big_data);
            $this->assign('edit_big_data_content',$edit_big_data_content);
            $this->display("big_data_add");
        }
    }
    //价格趋势种类信息维护
    public function jgqs_cates(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("jgqs_cates");
        $where1 = array();
        $where2 = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where1['name'] = array('like', "%{$key}%");
        }
        if($_GET['s_dept_id']){
            $s_dept_id = txt($_GET['s_dept_id']);
            $where2['dept_id'] = array('like', "%{$s_dept_id}%");
        }
        //var_dump($where2);die();
        $count = $m->where($where1)->where($where2)->count();
        $page = getPage($count, 10);
        $big_data = $m->where($where1)->where($where2)->order('id desc')->page($p.',10')->select();

        if (!empty($big_data)){
            foreach ($big_data as $k=>$v){
                if ($v['is_display'] == 1){
                    $big_data[$k]['is_display'] = '是';
                }elseif($v['is_display'] == 0){
                    $big_data[$k]['is_display'] = '否';
                }
                switch ($v['dept_id']){
                    case 1:
                        $big_data[$k]['dept'] = '农业';
                        continue;
                    case 2:
                        $big_data[$k]['dept'] = '林业';
                        continue;
                    case 3:
                        $big_data[$k]['dept'] = '畜牧业';
                        continue;
                    case 4:
                        $big_data[$k]['dept'] = '农机';
                        continue;
                    default:
                        $big_data[$k]['dept'] = '未知';
                        continue;
                }
            }
        }
        //dump($big_data);die();
        $dp = $s_dept_id ? $s_dept_id : 0;
        $this->assign('dp', $dp);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'jgqs_cates');
        $this->assign('infos', $big_data);
        $this->display();
    }
    //价格种类增加
    public function jgqs_AddCates(){
        if (IS_POST){
            $data = I('post.');
            if (empty($data)){
                $this->error('数据传递失败,请稍后再试');
            }
            if (empty($data['dept_id'])){
                $this->error('缺少领域参数');
            }
            if (empty($data['name'])){
                $this->error('请填写种类名称');
            }
            if ($data['is_display']==''){
                $this->error('请选择是否显示');
            }
            $where = array();
            $sel = array('is_display'=>1);
            $where['dept_id'] = intval($data['dept_id']);
            $m = M('jgqs_cates');
            $info = $m->where($where)->where($sel)->count();
            if ($info>=5 && $data['is_display']==1){
                $this->error('对不起，每个领域下只能指定不超过5个显示种类');
            }
            foreach ($data as $k=>$v){
                trim($data[$k]);
            }
            $data['addtime'] = date('Y-m-d');
            if ($m->add($data)){
                $this->success('添加成功',U('jgqs_cates'));
            }else{
                $this->success('添加失败');
            }

        }else{
            session_start();
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'jgqs_cates');
            $this->display();
        }
    }
    //价格种类编辑
    public function jgqs_EditCates(){
        if (IS_POST){
            session_start();
            $data = I('post.');
            $where = array();
            if (empty($data['id'])){
                $this->error('缺少必要参数');
            }else{
                intval($data['id']);
                $where['id'] = $data['id'];
            }
            foreach ($data as $k=>$v){
                if ($v==''){
                    unset($data[$k]);
                }else{
                    trim($data[$k]);
                }
            }
            $sel = array('is_display'=>1);
            $m = M('jgqs_cates');
            $info = $m->where($where)->where($sel)->count();
            if ($info>=5 && $data['is_display']==1){
                $this->error('对不起，每个领域下只能指定不超过5个显示种类');
            }
            //dump($data);die();
            if ($m->where($where)->save($data)){
                $this->success('修改成功',U('jgqs_cates'));
            }else{
                $this->error('修改失败，或数据未改动，请稍后重试');
            }
        }else{
            session_start();
            $where = array();
            if (isset($_GET['id'])){
                $where['id'] = $_GET['id'];
            }else{
                $this->error('参数错误');
            }
            intval($where['id']);
            if (empty($where['id'])){
                $this->error('参数错误');
            }
            $info = M('jgqs_cates')->where($where)->select();
            if (empty($info)){
                $this->error('未找到该种类');
            }
            $this->assign('info',$info[0]);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'jgqs_cates');
            $this->display();

        }
    }
    //价格种类删除
    public function jgqs_DelCates($id){
        intval($id);
        if(M('jgqs_cates')->delete($id)){
            $this->success('删除成功',U('jgqs_cates'));
        }else{
            $this->error('删除失败');
        }
    }

    //价格趋势列表
    public function jgqs(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("jgqs");
        $where1 = array();
        $where2 = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where1['cate_name'] = array('like', "%{$key}%");
        }
        if($_GET['s_dept_id']){
            $s_dept_id = txt($_GET['s_dept_id']);
            $where2['dept_id'] = array('like', "%{$s_dept_id}%");
        }
        //var_dump($where2);die();
        $count = $m->where($where1)->where($where2)->count();
        $page = getPage($count, 10);
        $big_data = $m->where($where1)->where($where2)->order('id desc')->page($p.',10')->select();

        if (!empty($big_data)){
            foreach ($big_data as $k=>$v){
                switch ($v['dept_id']){
                    case 1:
                        $big_data[$k]['dept'] = '畜牧品';
                        continue;
                    case 2:
                        $big_data[$k]['dept'] = '粮油';
                        continue;
                    case 3:
                        $big_data[$k]['dept'] = '果品';
                        continue;
                    case 4:
                        $big_data[$k]['dept'] = '蔬菜';
                        continue;
                    default:
                        $big_data[$k]['dept'] = '未知';
                        continue;
                }
            }
        }
        //dump($big_data);exit;
        $dp = $s_dept_id ? $s_dept_id : 0;
        $this->assign('dp', $dp);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'jgqs');
        $this->assign('infos', $big_data);
        $this->display();
    }
    //价格趋势信息添加
    public function jgqs_Add(){
        if (IS_POST){
            $where1 = array();
            $where2 = array();
            $data = I('post.');
            $data['dept_id'] = intval(I('post.dept_id'));
            $data['cate_id'] = intval(I('post.cate_id'));
            if (empty($data['cate_id']) || empty($data['dept_id'])){
                $this->error('缺少必要字段');
            }
            foreach ($data as &$v){
                trim($v);
            }
            if (empty($data['price'])){
                $this->error('价格不能为空');
            }
            if(empty($data['adddate'])){
                $data['adddate'] = date('Y-m-d',time());
            }
            $where1['id'] =$data['cate_id']; 
            $m =M('jgqs_cates');
            $info = $m->where($where1)->select();
            if (empty($info)){
                $this->error('查不到该分类');
            }else{
                $info = $info[0];
            }
            $data['cate_name'] = $info['name'];
            //$data['adddate'] = date('Y-m-d');
            $where_ck = array();
            $where_ck['cate_id'] = $data['cate_id'];
            $where_ck['adddate'] =  $data['adddate'];
            $ck = M('jgqs');
            $ck_info = $ck->where($where_ck)->select();
            if (!empty($ck_info)){
                $this->error('一天内不能添加两次同一产品价格,如需改动请对应修改');
            }
            if ($ck->add($data)){
                $this->success('添加成功',U('jgqs'));
            }else{
                $this->error('添加失败');
            }

        }else{
            session_start();
            $m1 = M('jgqs');
            $m2 = M('jgqs_cates');
            $cates = $m2->field('id,dept_id,name')->order('dept_id asc')->select();
            //dump($cates);die();
            $i = 0;
            $j = 0;
            $k = 0;
            $h = 0;
            $cate = array();
            foreach ($cates as $key=>$v){
                if ($v['dept_id'] == 1){
                    $cate[1][$i] = array('name'=>$v['name'],'id'=>$v['id']);
                    $i+=1;
                }elseif($v['dept_id'] == 2){
                    $cate[2][$j] = array('name'=>$v['name'],'id'=>$v['id']);
                    $j+=1;
                }elseif ($v['dept_id'] == 3){
                    $cate[3][$k] = array('name'=>$v['name'],'id'=>$v['id']);
                    $k+=1;
                }elseif ($v['dept_id'] == 4){
                    $cate[4][$h] = array('name'=>$v['name'],'id'=>$v['id']);
                    $h+=1;
                }
            }
            //var_dump($cate);die();

            $this->assign('cate',$cate);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'jgqs');
            $this->display();
        }
    }
    /*
     * 地理位置
     * by King
     * 2016-10-27
     * */
    public function geography_index(){
        session_start();
        $m = M("geography");
        //搜索
        if($_POST['s_status']){
            $where['status'] = array("like","%".$_POST['s_status']."%");
            $where['type'] = 2;
        }
        if($_POST['keyword']){
            $where['name'] = array("like","%".$_POST['keyword']."%");
            $where['type'] = 2;
        }
        $where['type'] = 2;
        $geography_list = $m->where($where)->order('id desc')->select();
//        p($_POST);
        $this->assign('geography_list', $geography_list);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'geography_index');

        $this->display();
    }
    /*
       * 地理位置增加
       * by King
       * 2016-10-27
       * */
    public function geography_add(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("geography");

        if(IS_POST){
            if(empty($_POST['name'])){
                $this->error('请填写标题！');
            }

            $data = I('post.');
            $data['name'] = trim($data['name']);
            $data['content'] = stripslashes(I('post.content'));
            $data['type'] = 2;
            $data['time'] = date('Y-m-d H-i-s');
            //判断只能开启4个
            $geography_count =$m->where(array('status'=>1,'type'=>2))->count();
           // p($geography_count);
            if($geography_count >= 4 and $data['status'] ==1){
                $data['status'] = 2;
                $res = $m->add($data);
                if($res){
                    $this->error('修改失败，只能同时开启4个，此菜单默认关闭！', U('geography_index'),5);
                }
            }else{
                $res =$m->add($data);
                if($res){
                    $this->success('添加成功！', U('geography_index'));
                }else{
                    $this->error('添加失败,请稍后再试', U('geography_index'));
                }
            }
//            if(M('geography')->add($data)){
//                $this->success('保存成功', U('geography_index'));
//            }else{
//                $this->error('保存失败,请稍后再试');
//            }
        }else {
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'geography_index');
            $this->display();
        }


    }
    /*
    * 地理位置编辑
    * by King
    * 2016-10-27
    * */
    public function geography_edit(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("geography");
        $id = $_GET['id'];
        if($id){
            $geography_find = $m->where(array('id'=>$id))->find();
            $this->assign('geography_find', $geography_find);
        }
        if(IS_POST){
            if(empty($_POST['name'])){
                $this->error('请填写标题！');
            }

            $data = I('post.');
            $data['name'] = trim($data['name']);
            $data['type'] = 2;
            $data['time'] = date('Y-m-d H-i-s');
            //判断只能开启4个
            $geography_count =$m->where(array('status'=>1,'type'=>2))->count();
            // p($geography_count);
            if($geography_count > 5 && $data['status'] ==1){
                $data['status'] = 2;
                $res = $m->where(array('id'=>$id))->save($data);
                if($res){
                    $this->error('修改失败，只能同时开启4个，此菜单默认关闭！', U('geography_index'),5);
                }
            }else{
                $res =$m->where(array('id'=>$id))->save($data);
                if($res){
                    $this->success('添加成功！', U('geography_index'));
                }else{
                    $this->error('添加失败,请稍后再试', U('geography_index'));
                }
            }
//            if(M('geography')->where(array('id'=>$id))->save($data)){
//                $this->success('保存成功', U('geography_index'));
//            }else{
//                $this->error('保存失败,请稍后再试');
//            }
        }else {
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'geography_index');
            $this->display("geography_add");
        }
    }

    /*
     * 地理位置-删除
     * by King
     * 2016-10-28
     * */
    public function geography_del($pid){
        $pid = intval($pid);
        $m =M('geography');
        $geography_find = $m->where(array('gid'=>$pid))->find();
        if($geography_find){
            $this->error('删除失败,该菜单正在被使用！');
        }else{
            if($pid){
                if($m->delete($pid) !== false){
                    $this->success('删除成功', U('geography_index'));
                }else{
                    $this->error('删除失败,请稍后再试');
                }
            }
        }
    }

    /*
     * 地理位置-坐标-首页
     * by King
     * 2016-10-31
     * */
    public function coordinate_index(){

        $m = M("geography");
        $gid = intval($_GET['gid']);
        $geography_find = $m->where(array('id'=>$gid))->find();
        $this->assign('geography_find', $geography_find);
        //搜索
        if($_POST['s_status']){
            $where['status'] = array("like","%".$_POST['s_status']."%");
            $where['type'] = 1;
            $where['gid'] = $gid;
        }
        if($_POST['keyword']){
            $where['name'] = array("like","%".$_POST['keyword']."%");
            $where['type'] = 1;
            $where['gid'] = $gid;
        }
        $where['type'] = 1;
        $where['gid'] = $gid;
        $geography_list = $m->where($where)->order('id desc')->select();
        $this->assign('geography_list', $geography_list);

        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'geography_index');
        $this->display();
    }
    /*
    * 地理位置-坐标-添加
    * by King
    * 2016-10-31
    * */
    public function coordinate_add(){
        $gid = intval($_GET['gid']);
        $geography_find = M('geography')->where(array('id'=>$gid))->find();
        if($_POST){
            $data = I('post.');
            $data['gid'] = $gid;
            $data['type'] = 1;
            $data['content'] = stripslashes(I('post.content'));
            $data['name'] = trim($data['name']);
            $data['longitude'] = $data['longitude'];
            $data['latitude'] = $data['latitude'];
            $data['time'] = date('Y-m-d H-i-s');
            if (!empty($data['account'])){
                $where = array('account'=>$data['account']);
                $ck = M('ygl_user')->where($where)->find();
                if (empty($ck)){
                    $this->error('不存在该IM账号');
                }
            }
            $res = M('geography')->add($data);
            if($res){
                $this->success('添加成功', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
            }else{
                $this->error('添加失败,请稍后再试', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'geography_index');
            $this->assign('geography_find', $geography_find);
            $this->display();
        }
    }
    /*
   * 地理位置-坐标-编辑
   * by King
   * 2016-10-31
   * */
    public function coordinate_edit(){
        $gid = intval($_GET['gid']);
        $geography_find = M('geography')->where(array('id'=>$gid))->find();
        $id = intval($_GET['id']);
        $geography_finds = M('geography')->where(array('id'=>$id))->find();
        $geography_finds['content'] = html_entity_decode($geography_finds['content']);
        if($_POST){
            $data = I('post.');
            $data['gid'] = $gid;
            $data['type'] = 1;
            $data['content'] = stripslashes(I('post.content'));
            $data['name'] = trim($data['name']);
            $data['longitude'] = $data['longitude'];
            $data['latitude'] = $data['latitude'];
            $data['account'] = trim($data['account']);
            $data['mobile'] = trim($data['mobile']);
            $data['time'] = date('Y-m-d H-i-s');
            if (!empty($data['account'])){
                $where = array('account'=>$data['account']);
                $ck = M('ygl_user')->where($where)->find();
                if (empty($ck)){
                    $this->error('不存在该IM账号');
                }
            }
            $res = M('geography')->where(array('id'=>$_GET['id']))->save($data);
            if($res){
                $this->success('修改成功', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
            }else{
                $this->error('修改失败,请稍后再试', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'geography_index');
            $this->assign('geography_find', $geography_find);
            $this->assign('geography_finds', $geography_finds);
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
        $m =M('geography');
            if($id){
                if($m->delete($id) !== false){
                    $this->success('删除成功', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
                }else{
                    $this->error('删除失败,请稍后再试', U('coordinate_index',array('id'=>$id,'gid'=>$_GET['gid'])));
                }
            }
        }

    /*
    * 应急管理-首页
    * by King
    * 2016-11-02
    * */
    public function emergencys_index()
    {
        session_start();
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'emergency');
        $this->display();
    }
        //价格趋势编辑
        public function jgqs_edit(){
            if (IS_POST){
                $data = I('post.');
                if (empty($data['id'])){
                    $this->error('缺少id参数');
                }else{
                    $where = array();
                    $where['id'] =$_GET['id'];
                    unset($data['id']);
                }
                if (empty($data['dept_id']) || empty($data['cate_id'])){
                    $this->error('缺少领域及种类参数');
                }
                $m = M('jgqs');
                $info = $m->where($where)->select();
                if (empty($info)){
                    $this->error('未找到该信息或已删除');
                }
                $where1 = array();
                $where1['id'] =$data['cate_id'];
                $m1 =M('jgqs_cates');
                $info = $m1->where($where1)->select();
                if (empty($info)){
                    $this->error('查不到该分类');
                }else{
                    $info = $info[0];
                }
                $data['cate_name'] = $info['name'];
                foreach ($data as $k=>$v){
                    trim($data[$k]);
                    if (empty($data[$k])){
                        unset($data[$k]);
                    }
                }
                if (empty($data)){
                    $this->error('修改数据为空');
                }
                if($m->where($where)->save($data)){
                    $this->success('修改成功',U('jgqs'));
                }else{
                    $this->error('修改失败或数据未变动');
                }
                
            }else{
                session_start();
                if (empty($_GET['id'])){
                    $this->error('缺少必要参数');
                }
                $where = array();
                $where['id'] = $_GET['id'];
                $infos = M('jgqs')->where($where)->select();
                if (empty($infos)){
                    $this->error('未找到该条信息或已删除');
                }else{
                    $infos = $infos[0];
                }
                $m2 = M('jgqs_cates');
                $cates = $m2->field('id,dept_id,name')->order('dept_id asc')->select();
                $i = 0;
                $j = 0;
                $k = 0;
                $h = 0;
                $cate = array();
                foreach ($cates as $key=>$v){
                    if ($v['dept_id'] == 1){
                        $cate[1][$i] = array('name'=>$v['name'],'id'=>$v['id']);
                        $i+=1;
                    }elseif($v['dept_id'] == 2){
                        $cate[2][$j] = array('name'=>$v['name'],'id'=>$v['id']);
                        $j+=1;
                    }elseif ($v['dept_id'] == 3){
                        $cate[3][$k] = array('name'=>$v['name'],'id'=>$v['id']);
                        $k+=1;
                    }elseif ($v['dept_id'] == 4){
                        $cate[4][$h] = array('name'=>$v['name'],'id'=>$v['id']);
                        $h+=1;
                    }
                } 
                $this->assign('infos',$infos);
                $this->assign('cate',$cate);
                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'jgqs');
                $this->display();
            }
        }
        //价格趋势信息删除
        public function jgqsDel(){
            if (!isset($_GET['id'])){
                $this->error('缺少相关id参数');
            }else{
                $id = $_GET['id'];
            }
            $m = M('jgqs');
            if ($m->delete($id)){
                $this->success('删除成功',U('jgqs'));
            }else{
                $this->error('删除失败');
            }
        }

    /*
   * 应急管理-首页
   * by King
   * 2016-11-02
   * */
    public function emergency_index(){
        session_start();
//        $dept = $_SESSION['admin']['dept_id'];
        $m = M("emergency");
        //搜索
        if($_POST['s_status']){
            $where['status'] = array("like","%".$_POST['s_status']."%");
        }
        if($_POST['keyword']){
            $where['name'] = array("like","%".$_POST['keyword']."%");
        }
        if($_SESSION['admin']['dept_id'] == 0){
            $where;
        }else{
            $where['did'] = $_SESSION['admin']['dept_id'];
        }
        $where['pid'] = 0;
        $emergency_list = $m->where($where)->order('id desc')->select();

//        p($_POST);
//        $this->assign('dp_id',$_SESSION['admin']['dept_id']);
        $this->assign('emergency_list', $emergency_list);
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'emergency');
        $this->display();
    }
    //应急管理模块列表
    public function emergency_list(){
        session_start();
        if (empty($_GET['pid'])){
            $this->error('缺少相关参数');
        }else{
            $where = array();
            $where['pid'] = intval(I('get.pid'));
        }
        if (!empty($_GET['status'])){
            $where['status'] = intval(I('get.status'));
        }
        if (!empty($_GET['title'])){
            $title = trim(I('get.title'));
            $where['title'] = array("like","%".$title."%");
        } 
        $where_did = array('id'=>$where['pid']);
        $ckInfo = M('emergency')->where($where_did)->find();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $count = M('emergency')->where($where)->count();
        $page = getPage($count, 10);
        $info = M('emergency')->where($where)->order('id desc')->page($p.',10')->select();
        $where_p['id'] = $where['pid'];
        $pname = M('emergency')->where($where_p)->find();
       // dump($info);die();
        //dump($ckInfo);
        //die();
//        $ddd = $_SESSION['admin']['dept_id'];
//        dump($ddd);die();
        $this->assign('dp_id',$_SESSION['admin']['dept_id']);
        $this->assign('st',$where['status']);
        $this->assign('did',$ckInfo['did']);
        $this->assign('pid',$where['pid']);
        $this->assign('pname',$pname);
        $this->assign('info', $info);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'emergency');
        
        $this->display();
       
    }
    //新增应急管理消息
    public function emergency_add_new(){
        session_start();
        //输出全部群分类
        $group_cate = M("group_cate")->where(array('status'=>1))->field('id,name')->select();
        $this->assign('group_cate', $group_cate);
        $group = M("group")->where(array('status'=>1))->field('id,group_account')->select();
        $this->assign('group', $group);
        $pid = intval($_GET['pid']);
        $m =  M('emergency');
        $geography_find =$m->where(array('id'=>$pid))->find();
//        dump($pid);die();
        $dp_id = $_SESSION['admin']['dept_id'];
        if (IS_POST){
            $data = array();
            $data = I('post.');
//            dump($data);die();
            
            $length = mb_strlen($data['title'],'utf8');
            if($length >16){
                $this->error('您所输入的标题字数不得大于16');
            }
            //控制权限
            if ($dp_id != 0){
                if ($data['pid'] != $dp_id){
                    $this->error('您不可以添加其他部门的预警');
                }
            }else{
                if ($data['pid']!=4){
                    $this->error('您不可以添加其他部门的预警');
                }
            }
            //循环遍历数据库匹配易管理信息
            $number = 1;
            $temp = array();
            foreach ($data['commander_account'] as $key=>$val){
                $where1 = array();
                $where1['account'] = $val;
                $res = M('ygl_user')->field('account,real_name,mobile')->where($where1)->find();
                if (!empty($res)){
                    if(!empty($data['commander'])){
                        $data['commander'] = $data['commander'].','.$res['real_name'];
                    }else{
                        $data['commander'] = $res['real_name'];
                    }
                    if(!empty($data['commander_mobile'])){
                        $data['commander_mobile'] = $data['commander_mobile'].','.$res['mobile'];
                    }else{
                        $data['commander_mobile'] = $res['mobile'];
                    }
                }else{
                    $this->error('您所填写的第'.$number.'位用户不存在');
                }
                if (!empty($temp)){
                    $temp['commander_account'] = $temp['commander_account'].','.$val;
                }else{
                    $temp['commander_account'] = $val;
                }
                $data['commander_account'] = $temp['commander_account'];
            }
//            dump($data);die();
            $data['video_url'] = implode(',',$data['video_url']);
            $data['video_location'] = implode(',',$data['video_location']);
            if($data['status']==1){
                $data['ever'] = 1;
            }else{
                $data['ever'] = 0;
            }
            $res = M('emergency')->add($data);
            if ($res){
                if ($data['status'] ==1){
                $AppKey = $this->AppKey;
                $MSecret = $this->MSecret;
                $str = $AppKey.':'.$MSecret;
                $auth = base64_encode($str);
                $header =  array(
                    'Content-Type: application/json',
                    'Authorization: Basic '.$auth
                );
                $where['auth'] = array('lt',3);
                $who = M('ygl_user')->field('account')->where($where)->select();
                $map = array();
                foreach ($who as $k=>$v){
                    $map[] = $v['account'];
                }
                $target = array();
                for($i = 0;$i<count($map);$i = $i+3){
                    $target = array();
                    if(isset($map[$i+2])){
                        $target[0] = $map[$i];
                        $target[1] = $map[$i+1];
                        $target[2] = $map[$i+2];
                    }elseif(isset($map[$i+1])){
                        $target[0] = $map[$i];
                        $target[1] = $map[$i+1];
                    }else{
                        $target[0] = $map[$i];
                    }
 //                   dump($target);
                    $mes = array();
                    $mes = array(
                        'platform' => 'android',
                        'audience' => array('alias'=>$target),
                        'notification' => array('alert' => $data['title'])
                    );
                    $url = $this->url;
                    $result = $this->curl($url, $mes, $header, "POST");
//                    dump($result);
                }
                
                }
                $this->success('操作成功',U('emergency_list',array('pid'=>$pid)));
            }else{
                $this->error('操作失败',U('emergency_list',array('pid'=>$pid)));
            }
        }else{
//            dump($geography_find);die();
            $this->assign('dp_id',$dp_id);
            $this->assign('did',$geography_find['did']);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency');
            $this->assign('emergency_find', $geography_find);
            $this->display();
        }
    }
    /*
     * 群管理联动
     * */
    public function step_group_cate(){
        $Model = new \Think\Model();
        if($_GET['m'] == "group_cate"){
            $cid =$_POST['cid'];
            $result = $Model->query("SELECT * FROM sn_group WHERE cid = $cid and status=1");
            output_data($result);
        }
    }

    //编辑应急管理消息
    public function emergency_edit_new(){
        session_start();
        $dp_id = $_SESSION['admin']['dept_id'];
        //输出全部群分类
        $group_cate = M("group_cate")->where(array('status'=>1))->field('id,name')->select();
        $this->assign('group_cate', $group_cate);
        $group = M("group")->where(array('status'=>1))->field('id,group_account')->select();
        $this->assign('group', $group);
        if (IS_POST){
            $data = array();
            $where = array();
            $data = I('post.');
            //循环遍历数据库匹配易管理信息
            $number = 1;
            $temp = array();
            foreach ($data['commander_account'] as $key=>$val){
                $where1 = array();
                $where1['account'] = $val;
                $res = M('ygl_user')->field('account,real_name,mobile')->where($where1)->find();
                if (!empty($res)){
                    if(!empty($data['commander'])){
                        $data['commander'] = $data['commander'].','.$res['real_name'];
                    }else{
                        $data['commander'] = $res['real_name'];
                    }
                    if(!empty($data['commander_mobile'])){
                        $data['commander_mobile'] = $data['commander_mobile'].','.$res['mobile'];
                    }else{
                        $data['commander_mobile'] = $res['mobile'];
                    }
                }else{
                    $this->error('您所填写的第'.$number.'位用户不存在');
                }
                if (!empty($temp)){
                    $temp['commander_account'] = $temp['commander_account'].','.$val;
                }else{
                    $temp['commander_account'] = $val;
                }
                $data['commander_account'] = $temp['commander_account'];
            }
            $data['video_url'] = implode(',',$data['video_url']);
            $data['video_location'] = implode(',',$data['video_location']);
            $where['id'] = $data['id'];
            unset($data['id']);
            $ck = M('emergency')->where($where)->select();
            if (!empty($ck)){
                //控制权限
                if ($dp_id != 0){
                    if ($ck[0]['pid'] != $dp_id){
                        $this->error('您不可以编辑其他部门的预警');
                    }
                }else{
                    if ($ck[0]['pid']!=4){
                        $this->error('您不可以编辑其他部门的预警');
                    }
                }
                if ($ck[0]['ever']==1 && $ck[0]['status']==2 && $data['status']==1){
                    $this->error('开启过的警报不能再开启');
                }
            }else{
                $this->error('未找到该信息');
            }
            $res = M('emergency')->where($where)->save($data);
            if ($res){
                if ($data['status'] ==1){
                    $AppKey = $this->AppKey;
                    $MSecret = $this->MSecret;
                    $str = $AppKey.':'.$MSecret;
                    $auth = base64_encode($str);
                    $header =  array(
                        'Content-Type: application/json',
                        'Authorization: Basic '.$auth
                    );
                    $where['auth'] = array('lt',3);
                    $who = M('ygl_user')->field('account')->where($where)->select();
                    $map = array();
                    foreach ($who as $k=>$v){
                        $map[] = $v['account'];
                    }
                    $target = array();
                    for($i = 0;$i<count($map);$i = $i+3){
                        $target = array();
                        if(isset($map[$i+2])){
                            $target[0] = $map[$i];
                            $target[1] = $map[$i+1];
                            $target[2] = $map[$i+2];
                        }elseif(isset($map[$i+1])){
                            $target[0] = $map[$i];
                            $target[1] = $map[$i+1];
                        }else{
                            $target[0] = $map[$i];
                        }
                        //                   dump($target);
                        $mes = array();
                        $mes = array(
                            'platform' => 'android',
                            'audience' => array('alias'=>$target),
                            'notification' => array('alert' => $data['title'])
                        );
                        $url = $this->url;
                        $result = $this->curl($url, $mes, $header, "POST");
                        //                    dump($result);
                    }
                   
                }
                //dump($result);die();
                $this->success('操作成功',U('emergency_list',array('pid'=>$data['pid'])));
            }else{
                $this->error('操作失败',U('emergency_list',array('pid'=>$data['pid'])));
            }
        }else{
            $id = intval($_GET['id']);
            $m =  M('emergency');
            $info =$m->where(array('id'=>$id))->find();
            //循环指挥员信息 转换成数组
            $info['commander_account'] = explode(',',$info['commander_account']);
            $info['video_url'] = explode(',',$info['video_url']);
            $info['video_location'] = explode(',',$info['video_location']);
            $info['video_urls'] = array();
            foreach($info['video_url'] as $k=>$v){
                foreach($info['video_location'] as $k1=>$v1){
                    if($k == $k1){
                        $info['video_urls'][] = array(
                            'video_location'=>$v1,
                            'video_url'=>$v
                        );
                    }
                }
            }
            unset($info['video_url'],$info['video_location']);
//            p($info);
            $where = array('id'=>$info['pid']);
            $res = M('emergency')->where($where)->find();
            $this->assign('dp_id',$dp_id);
            $this->assign('did',$res['did']);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency');
            $this->assign('info', $info);
            $this->display();
        }
    }
    
    public function emergency_del_new(){
        if (empty($_GET['id'])){
            $this->error('缺少相关参数');
        }else{
           // $where = array();
            $id = intval(I('get.id'));
        }
        $res = M('emergency')->delete($id);
        if ($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    /*
    * 应急管理-添加
    * by King
    * 2016-11-2
    * */
    public function emergency_add(){
        $gid = intval($_GET['gid']);
        $m =  M('emergency');
        $geography_find =$m->where(array('id'=>$gid))->find();

        if($_POST){
            $data = I('post.');
            $data['time'] = date('Y-m-d H-i-s');
            //判断只能开启4个
            $geography_count =$m->where(array('status'=>1,'type'=>2))->count();
            if($geography_count >= 4 and $data['status'] ==1){
                $data['status'] = 2;
                $res = M('emergency')->add($data);
                if($res){
                    $this->error('修改失败，只能同时开启4个，此菜单默认关闭！', U('emergency_index'),5);
                }
            }else{
                $res = M('emergency')->add($data);
                if($res){
                    $this->success('添加成功！', U('emergency_index'));
                }else{
                    $this->error('添加失败,请稍后再试', U('emergency_index'));
                }
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency');
            $this->assign('geography_find', $geography_find);
            $this->display();
        }
    }
    /*
      * 应急管理编辑
      * by King
      * 2016-11-2
      * */
    public function emergency_edit(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("emergency");
        $id = $_GET['id'];
        if($id){
            $emergency_find = $m->where(array('id'=>$id))->find();
            $this->assign('emergency_find', $emergency_find);
        }
        if(IS_POST){
            if(empty($_POST['name'])){
                $this->error('请填写标题！');
            }
            $data = I('post.');
            $data['name'] = trim($data['name']);
            $data['time'] = date('Y-m-d H-i-s');
            //判断只能开启4个
            $geography_count =$m->where(array('status'=>1,'type'=>2))->count();
            if($geography_count >= 4 and $data['status'] ==1){
                $data['status'] = 2;
                $res = M('emergency')->where(array('id'=>$id))->save($data);
                if($res){
                    $this->error('修改失败，只能同时开启4个，此菜单默认关闭！', U('emergency_index'),5);
                }
            }else{
                $res = M('emergency')->where(array('id'=>$id))->save($data);
                if($res){
                    $this->success('修改成功！', U('emergency_index'));
                }else{
                    $this->error('修改失败,请稍后再试', U('emergency_index'));
                }
            }
        }else {
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency');
            $this->display("emergency_add");
        }
    }
    /*
  * 应急管理-添加
  * by King
  * 2016-11-2
  * */
    private $AppKey = 'd9c8bc753ada5364071b800a';
    private $MSecret = '05e5d785d962151515a456a4';
    private $url = 'https://api.jpush.cn/v3/push';
 //    private $url = 'https://api.jpush.cn/v3/push/validate';
    public function emergency__add(){
        $gid = intval($_GET['gid']);
        $emergency_find = M('emergency')->where(array('id'=>$gid))->find();
        if($_POST){
            $data = I('post.');
            $data['time'] = date('Y-m-d H-i-s');
            $title = $data['title'];
            $res = M('emergency')->where(array('id'=>$gid))->save($data);
            if($res){
                if ($data['type'] == 1){
                    $AppKey = $this->AppKey;
                    $MSecret = $this->MSecret;
                    $str = $AppKey.':'.$MSecret;
                    $auth = base64_encode($str);
                    $header =  array(
                        'Content-Type: application/json',
                        'Authorization: Basic '.$auth
                    );
                     $mes = array(
                        'platform' => 'android',
                        'audience' => array('alias'=>array('yjgl')),
                        'notification' => array('alert' => $title)
                    ); 
                  /*  $mes = '{
                        "platform": "android",
                        "audience" : "android",
                        "notification" : {
                        "alert" : "Hi, JPush!",
                        "android" : {},
                        
                    }
                    }'; */
                   // $mes = json_encode($mes);
                    $url = $this->url;
                    $result = $this->curl($url, $mes, $header, "POST");
                   // dump($result);die();
                }
                $this->success('修改成功', U('emergency_index'));
            }else{
                $this->error('修改失败,请稍后再试', U('emergency_index'));
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency');
            $this->assign('emergency_find', $emergency_find);
            $this->display();
        }
    }
    //curl处理
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
    
    /*
    * 应急管理-删除
    * by King
    * 2016-10-28
    * */
    public function emergency_del($pid){
        $pid = intval($pid);
        $m =M('emergency');
            if($pid){
                if($m->delete($pid) !== false){
                    $this->success('删除成功', U('emergency_index'));
                }else{
                    $this->error('删除失败,请稍后再试');
                }
        }
    }
    /*
    * 预警通知-首页
    * */
    public function emergency_message_index(){
        session_start();
        $dept = $_SESSION['admin']['dept_id'];
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("big_data");
        $where = array(
            'type'=>1,
            'status'=>4,
            'dept_id'=>$dept
        );
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        $count = $m->where($where)->count();
        $page = getPage($count, 10);


        $big_data = $m->where($where)->order('id desc')->page($p.',10')->select();

        $tt = $key ? $key : 0;
        $this->assign('key', $key);

        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'emergency_message_index');
        $this->assign('big_data', $big_data);
        $this->display();
    }
    /*
    * 预警通知-新增页面
    * */
    public function emergency_message_add(){
        session_start();
        $dept = $_SESSION['admin']['dept_id'];
        if(IS_POST){
            if(empty($_POST['title'])){
                $this->error('请填写标题！');
            }
            $data = I('post.');
            if (empty($data['afrom'])){
                $data['afrom'] = '其他';
            }
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['images'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $data['title'] = trim($data['title']);
            $data['status'] = 4;
            $data['dept_id'] = $dept;
            $data['addtime'] = date('Y-m-d H-i-s',time());
            $data['content'] = $_POST['content'];
            if(M('big_data')->add($data)){
                $this->success('添加成功', U('emergency_message_index'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency_message_index');
            $this->display();
        }

    }
    /*
    * 预警通知-删除
    * */
    public function emergency_message_del($pid){
        $pid = intval($pid);
        if($pid){
            if(M('big_data')->delete($pid) !== false){
                $this->success('删除成功', U('emergency_message_index'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
    /*
  * 预警通知-编辑
  * */
    public function emergency_message_edit($id){
        $id = intval($id);
        $dept_id = $_SESSION['admin']['dept_id'];
        if(IS_POST){
            if(empty($_POST['title'])){
                $this->error('请填写标题！');
            }
            $data = I('post.');
            $data['title'] = trim($data['title']);
            if (empty($data['afrom'])){
                unset($data['afrom']);
            }
            $where_ck = array('id'=>$id);
            $ckInfo = M('big_data')->where($where_ck)->find();
            if (!empty($ckInfo)){
                if ($ckInfo['dept_id']!=$dept_id){
                    $this->error('您无法修改其他部门的通知');
                }
            }else{
                $this->error('未找到该信息');
            }
            $file = $_FILES['picture'];
            if($file['size'] > 0){
                $upinfo = upload();
                if($upinfo['code'] == 0){
                    $data['images'] = $upinfo['msg'];
                }else{
                    $this->error($upinfo['msg']);
                }
            }
            $data['content'] = $_POST['content'];
            if(M('big_data')->where(array('id'=>$id))->save($data)){
                $this->success('保存成功', U('emergency_message_index'));
            }else{
                $this->error('保存失败,请稍后再试');
            }
        }else{
            $edit_big_data = M("big_data")->where(array('id'=>$id))->find();
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'emergency_message_index');
            $edit_big_data_content = html_entity_decode($edit_big_data['content']);
            $this->assign('edit_big_data',$edit_big_data);
            $this->assign('edit_big_data_content',$edit_big_data_content);
            $this->display("emergency_message_add");
        }
    }

    /*
     * 工作汇报
     * * by dai
     * 2016-11-1
     */
    public function workreport_index(){
        session_start();
        $admin = session('admin');
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('work_report');
        if($admin['dept_id']!=0){
            $where = array('dept_id' => $admin['dept_id']);
        }
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }




        $count = $m->where($where)->count();
        $infos = $m->where($where)->order('addtime desc')->page($p.',10')->select();
        if(!empty($infos)){
            foreach ($infos as &$val){
                if (!empty($val['dept_id'])){
                    $arr = M('Department')->where(array('did'=>$val['dept_id']))->select();
                    $val['bm'] = $arr[0]['dname'];
                }
            }
        }
        $page = getPage($count, 10);
        $this->assign('dp_id',$admin['dept_id']);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'workreport');

        $this->display();

    }
    //添加工作汇报
    public function addworkreportInfo(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            $data['menu_id'] = intval($data['menu_id']);
            // $data['dept_id'] = I('post.did');
            $data['dept_id'] = $admin['dept_id'];
            //   $data['sender'] = $admin['aid'];
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
            $m = M('work_report');
            $wid=$m->add($data);
            if($wid){
                $rid =I('post.recive_id');
                if(!empty($rid)){
                    $arr_rid = explode(',',$rid);//接收人id 数组
                    for($index=0;$index<count($arr_rid);$index++){
                        $data2['wid']=$wid;
                        $data2['dept_id'] = $admin['dept_id'];
                        $data2['recive_id']=$arr_rid[$index];
                        $data2['sender_id']=$admin['aid'];
                        M('report_state')->add($data2);
                    }
                }

                $this->success('添加成功', U('workreport_index'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{


            $userlist=M('ygl_user')->field('uid,real_name,department')->select();

            $this->assign('userlist', json_encode($userlist));
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'workreport');
            $this->display();
        }
    }
    //获取工作汇报
    public function gzhbInfo($rid){
        session_start();
        $admin = session('admin');

        $rid = intval($rid);
        if($rid){
            $info = M('work_report')->find($rid);
            if($info){

                $info['content'] = html_entity_decode($info['content']);
                $arr_rid = explode(',',$info['recive_id']);//接收人id 数组
                $recive_list=array();
                for($index=0;$index<count($arr_rid);$index++){
                    $recive_list[]=M('ygl_user')->field('real_name,department')->where(array('uid'=>$arr_rid[$index]))->find();
                }
                // dump($recive_list);
                $this->assign('recive_list', $recive_list);
                $this->assign('info', $info);
                $this->assign('menus', session('menus'));
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'workreport');
                $this->display();
            }
        }

    }
    //删除工作汇报
    public function delgzhbInfo($rid){
        $rid = intval($rid);
        if($rid){
            if(M('work_report')->delete($rid) !== false){
                M('report_state')->where(array('wid'=>$rid))->delete();
                $this->success('删除成功', U('workreport_index'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
    //易管理后台部门文章聊天人员列表
    public function ygl_bmltList(){
        session_start();
        $m1 = M('department');
        $list = $m1->field('did,dname,ygl_server')->where()->select();
        foreach ($list as $k=>$v){
            $where = array();
            $where['account'] = $v['ygl_server'];
            $info = M('ygl_user')->field('uid,account,real_name,mobile')->where($where)->select();
            if (!empty($info)){
                $list[$k]['real_name'] = $info[0]['real_name'];
                $list[$k]['mobile'] = $info[0]['mobile'];
            }else{
                $list[$k]['real_name'] = '';
                $list[$k]['mobile'] = '';
            }
        }
        $this->assign('list',$list);
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_bmltList');
        $this->assign('menus', session('menus'));
        $this->display();
    }
    //易管理部门文章聊天人员修改
    public function ygl_bmltEdit(){
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
            if (isset($_POST['ygl_server'])){
                $data = array();
                $search = array();
                $data['ygl_server'] = trim($_POST['ygl_server']);
                $search['account'] = $data['ygl_server'];
            }else{
                $this->error('请输入账号');
            }
            $m1 = M('ygl_user');
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
                $this->success('修改成功',U('ygl_bmltList'));
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
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_bmltList');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    public function ygl_notice(){
        session_start();
        //var_dump(session());die();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $eptModel = M('Notice');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        $where['type'] = 1;
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
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_notice');
        $this->display();
    }
    public function ygl_notice_add(){
        if(IS_POST){
            $type = I('type','','trim,htmlspecialchars');
            $did = I('did','','trim,htmlspecialchars');
            $title = I('title','','trim,htmlspecialchars');
            $content = I('content','','trim,htmlspecialchars');
            $length = mb_strlen($content,'utf8');
            /* dump($content);
            dump($length);die(); */
            if($length >200){
                $this->error('您所输入的内容字数不得大于200');
            }
            $data = array('type' => $type,'did' => $did, 'title' => $title, 'content' => $content,'addtime'=> date('Y-m-d h:i:s'));
            //var_dump($data);die();
            $data['type'] = 1;
            if(M('Notice')->add($data)){
                redirect(U('Manage/Ygl/ygl_notice'));
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
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_notice');
            $this->assign('type',1);
            $this->display();
        }
    }
    public function ygl_notice_edit(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            //var_dump($data);die();
            $content = $data['content'];
            $length = mb_strlen($content,'utf8');
            if($length >200){
                $this->error('您所输入的内容字数不得大于200');
            }
            $general = new General();

            if ($data['did']=='请选择部门'){
                unset($data['did']);
            }
            $data['type'] = 1;
            if(M('notice')->save($data) !== false){
                redirect(U('Manage/Ygl/ygl_notice'));
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
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'ygl_notice');
                $this->assign('info', $info);
                $this->assign('type',1);
                $this->display();
            }
        }
    }
    public function ygl_notice_del($id){
        $id = intval($id);
        if(M('notice')->where('id='.$id)->delete()){
            redirect(U('Manage/Ygl/ygl_notice'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_notice');
        }else{
            $this->error('删除失败，请稍后再试');
        }
    }
    public function ygl_adver(){
        $banner = M('banner');
        $banner_list = $banner->where(array('status'=>1,'type'=>1))->order('id asc')->select();
        $this->assign('banner_list', $banner_list);
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_adver');
        $this->assign('menus', session('menus'));
        $this->display();
    }
    public function ygl_adver_add(){
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
            $data['type'] = 1;
            $type = $m->where(array('type'=>$_POST['type']))->count();
            if($type >= 3){
                $this->error('最多只能添加3张广告图！', U('Manage/Ygl/ygl_adver'));
                exit;
            }else{
                if($m->add($data)){
                    $this->success('保存成功', U('Manage/Ygl/ygl_adver'));
                }else{
                    $this->error('保存失败,请稍后再试', U('Manage/Ygl/ygl_adver'));
                }
            }
        }else{
            $this->assign('type',1);
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_adver');
            $this->assign('menus', session('menus'));
            $this->display();
        }
    }
    public function ygl_adver_edit(){
        session_start();
        $admin = session('admin');
        $id = $_GET['id'];
        $m = M('banner');
        if(IS_POST){
            $data = I('post.');
//            $data['menu_id'] = intval($data['menu_id']);
//            $data['dept_id'] = $admin['dept_id'];
            $data['status'] =1;
            $data['type'] = 1;
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
                $this->success('保存成功', U('Manage/Ygl/ygl_adver'));
            }else{
                $this->error('保存失败,请稍后再试', U('Manage/Ygl/ygl_adver'));
            }
        }else{
            $info = $m->find($id);
            $this->assign('info', $info);
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_adver');
            $this->assign('menus', session('menus'));
            $this->assign('type',1);
            $this->display("Ygl_adver_add");
        }
    }
    public function ygl_adver_del(){
        $id = $_GET['id'];

        if(!empty($id)){
            //var_dump($inf);die();
            if(M('banner')->delete($id)){
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'ygl_adver');
                redirect(U('Manage/Ygl/ygl_adver'));
            }else{
                $this->error('删除失败，请稍后再试');
            }
        }else{
            $this->error('参数错误，请稍后再试');
        }
    }
    public function ygl_train(){
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
        $where['software'] = 1;
        $infos = $m->where($where)->where($where1)->order('addtime DESC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign('cz',$type);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_train');
        $this->display();
    }
    public function ygl_train_add(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $data['content'] = stripslashes(I('post.content'));
            $data['title'] = trim($data['title']);
            $data['software'] = intval($data['software']);
            $data['software'] = 1;
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
                $this->success('添加成功', U('Manage/Ygl/ygl_train'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_train');
            $this->assign('type',1);
            $this->display();
        }
    }
    public function ygl_train_edit(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $data['content'] = stripslashes(I('post.content'));
            $data['title'] = trim($data['title']);
            $data['software'] = intval($data['software']);
            $data['software'] = 1;
            if (empty($data['id'])){
                $this->error('缺少相关id参数');
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
                $this->success('修改成功', U('Manage/Ygl/ygl_train'));
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
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_train');
            $this->assign('type',1);
            $this->display();
        }
    }
    public function ygl_train_del($tid){
        $tid = intval($tid);
        if($tid){
            if(M('Traininginfo')->delete($tid) !== false){
                $this->success('删除成功', U('Manage/Ygl/ygl_train'));
                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'ygl_train');
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
    public function ygl_videos(){
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
        $where['software'] = 1;
        $dept_id = $_SESSION['admin']['dept_id'];
        if($dept_id != 0){
            $where['dept_id'] = $dept_id;
        }
        $count = $m->where($where)->where($where1)->count();
        $page = getPage($count, 10);
        $infos = $m->where($where)->where($where1)->order('addtime DESC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign('cz',$type);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_videos');
        $this->display();
    }
    public function ygl_video_add(){
        session_start();
        $admin = session('admin');
        if(IS_POST){
            $data = I('post.');
            $data['title'] = trim($data['title']);
            $data['url'] = trim($data['url']);
            $data['software'] = intval($data['software']);
            $data['software'] = 1;
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
            $text = $_FILES['text'];
            $extension = substr($text['name'],-3);
            if($text['size']>0 || $extension == 'rar' || $extension == 'zip' || $extension == 'ppt' || $extension == 'pdf'){
                unset($file);
                $path = '../upload/traintext/';
                $subpath = date('Ym');
                $upload = new \Think\Upload();
                $upload->maxSize = 1110145728;
                $upload->exts = array('zip', 'txt', 'doc','xls','ppt','pdf');
                $upload->mimes = array('application/zip','text/plain','application/msword','application/vnd.ms-excel','application/vnd.ms-powerpoint','application/pdf');
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
            $data['dept_id'] = intval(@$data['dept_id']);
            if($data['dept_id']){
                $dept = M('Department')->find($data['dept_id']);
                if($dept){
                    $data['dept'] = $dept['dname'];
                }
            }
            $m = M('Video');
            if($m->add($data)){
                $this->success('添加成功', U('Manage/Ygl/ygl_videos'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $depts = M('Department')->field('did, dname')->where(array('pid' => 0))->select();
            $this->assign('depts', $depts);
             $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_videos');
            $this->assign('type',1);
            $this->display();
        }
    }
    public function ygl_video_edit(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $data['title'] = trim($data['title']);
            $data['software'] = intval($data['software']);
            $data['software'] = 1;
            if (empty($data['id'])){
                $this->error('缺少相关id参数');
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
            $text = $_FILES['text'];
            $extension = substr($text['name'],-3);
            if($text['size']>0 || $extension == 'rar' || $extension == 'zip' || $extension == 'ppt' || $extension == 'pdf'){
                unset($file);
                $path = '../upload/traintext/';
                $subpath = date('Ym');
                $upload = new \Think\Upload();
                $upload->maxSize = 1110145728;
                $upload->exts = array('zip', 'txt', 'doc','xls','ppt','pdf');
                $upload->mimes = array('application/zip','text/plain','application/msword','application/vnd.ms-excel','application/vnd.ms-powerpoint','application/pdf');
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
                $this->success('修改成功', U('Manage/Ygl/ygl_videos'));
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
            $depts = M('Department')->field('did, dname')->where(array('pid' => 0))->select();
            $this->assign('depts', $depts);
            //$info['content'] = html_entity_decode($info['content']);
            $this->assign('info',$info);
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_videos');
            $this->assign('type',1);
            $this->display();
        }
    }
    public function ygl_video_del($vid){
        $vid = intval($vid);
        if($vid){
            if(M('Video')->delete($vid) !== false){
                $this->success('删除成功', U('Manage/Ygl/ygl_videos'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }
    public function ygl_lives(){
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
        $where['software'] = 1;
        $count = $m->where($where)->where($where1)->count();
        $page = getPage($count, 10);
        $infos = $m->where($where)->where($where1)->order('_order ASC')->limit($page->firstRow, $page->listRows)->select();

        //$this->assign('cz',$type);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'ygl_lives');
        $this->display();
    }
    public function ygl_live_add(){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $data['status'] = intval($data['status']);
            $data['_order'] = intval($data['_order']);
            $data['software'] = intval($data['software']);
            $data['software'] = 1;
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
                $this->success('添加成功', U('Manage/Ygl/ygl_lives'));
            }else{
                $this->error('添加失败,请稍后再试');
            }
        }else{
            $this->assign('menus', session('menus'));
            $this->assign('liclass', 'ygl');
            $this->assign('aclass', 'ygl_lives');
            $this->assign('type',1);
            $this->display();
        }
    }
    public function ygl_live_edit($lid){
        session_start();
        if(IS_POST){
            $data = I('post.');
            $data['id'] = intval($data['id']);
            $data['status'] = intval($data['status']);
            $data['_order'] = intval($data['_order']);
            $data['software'] = intval($data['software']);
            $data['software'] = 1;
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
                $this->success('保存成功', U('Manage/Ygl/ygl_lives'));
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
                    $this->assign('liclass', 'ygl');
                    $this->assign('aclass', 'ygl_lives');
                    $this->assign('type',1);
                    $this->display();
                }
            }
        }
    }
    public function ygl_live_del($lid){
        $lid = intval($lid);
        if($lid){
            if(M('Live')->delete($lid) !== false){
                $this->success('删除成功', U('Manage/Ygl/ygl_lives'));
            }else{
                $this->error('删除失败,请稍后再试');
            }
        }
    }

}