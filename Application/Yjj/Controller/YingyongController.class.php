<?php

namespace Yjj\Controller;
use Think\Controller;
use Common\Common\General;
use Common\Common\ImageHandle;

class YingyongController extends Controller {
    //问政策文章列表
    public function wzc_getList(){
        $where = array();
        if(isset($_GET['dept_id'])){
            $where['dept_id'] = intval($_GET['dept_id']);
        }
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $general = new General();
        //$yjjM = D('YjjYingyong');
        $infos = M('policy')->field('id,dept_id,title,introduce,picture,addtime')->where($where)->where(array('is_top'=>0))->order('id desc')->page($page,10)->select();
        $account = array();
        $account = M('department')->where(array('did'=>$where['dept_id']))->find();
        $account = $account['yjj_server'];
        foreach ($infos as $k=>$v){
            $infos[$k]['account'] = $account;
        }
        if(!$infos[$k]['account']){
            $infos[$k]['account'] = NULL;
        }
        /* if (!empty($infos)){
            $top = '';

            foreach ($infos as $key => $val){
                if($val['is_top'] == 1){
                    $top = '/'.$val['picture'];
                    unset($infos[$key]);
                    break;
                }
            }
            foreach ($infos as $key => $val){
                if($val['is_top'] == 1){

                    unset($infos[$key]);

                }
            }
            $i = 0;
            $num = count($infos);
            $newInfo = array();
            foreach ($infos as $v){
                $newInfo[$i] = $v;
                $i++;
            } */
            //dump($infos);die();
        if (!empty($infos)){
            exit(json_encode(array('error' => 0, 'data' => $infos, 'msg' => 'success')));
        }else{
            $general->error(50);
        }
    }
    //问政策TOP图片
    public function wzc_top(){
        $general = new General();
        $where = array();
        if(isset($_GET['dept_id'])){
            $where['dept_id'] = intval($_GET['dept_id']);
        }
        $infos = M('policy')->field('picture')->where($where)->where(array('is_top'=>1))->order('id desc')->limit(1)->select();
        if(empty($infos)){
            $infos[0]['picture'] = '';
        }
        $general->returnData($infos,'success');
    }
    //问政策文章详情
    public function wzc_detail(){
        $where = array();
        if(isset($_GET['id'])){
            $where['id'] = intval($_GET['id']);
        }
        $yjjM = D('YjjYingyong');

    }
    
    //想去玩文章列表
    public function xqw_getList(){
        $where = array();
        if (isset($_GET['cate_id'])){
            $where['cate_id'] = intval($_GET['cate_id']);
        }
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $general = new General();
        //$yjjM = D('YjjYingyong');
        $infos = M('play')->field('id,cate_id,name,address,picture,tel,longitude,latitude,im')->where($where)->where(array('is_top'=>0))->order('id desc')->page($page,10)->select();
        if (!empty($infos)){
            /* $top = '';

            foreach ($infos as $key => $val){
                if($val['is_top'] == 1){
                    $top = '/'.$val['picture'];
                    unset($infos[$key]);
                    break;
                }
            }
            foreach ($infos as $key => $val){
                if($val['is_top'] == 1){

                    unset($infos[$key]);

                }
            }
            $i = 0;
            $num = count($infos);
            $newInfo = array();
            foreach ($infos as $v){
                $newInfo[$i] = $v;
                $i++;
            } */
            //dump($newInfo);die();

            exit(json_encode(array('error' => 0, 'data' => $infos, 'msg' => 'success')));
        }else{
            $general->error(51);
        }

    }
    //想去玩TOP图片
    public function xqw_top(){
        $general = new General();
        $where = array();
        if(isset($_GET['cate_id'])){
            $where['cate_id'] = intval($_GET['cate_id']);
        }
        $infos = M('play')->field('picture')->where($where)->where(array('is_top'=>1))->order('id desc')->limit(1)->select();
        if(empty($infos)){
            $infos[0]['picture'] = '';
        }
        $general->returnData($infos,'success');
    }
    //想去玩文章详情
    public function xqw_detail(){
        $where = array();
        if(isset($_GET['id'])){
            $where['id'] = intval($_GET['id']);
        }
        $yjjM = D('YjjYingyong');

    }
    //买卖信息列表
    public function businessList(){
        $where1 = array();
        $where2 = array();
        if (isset($_GET['cate_id'])){
            $where1['cate_id'] = intval($_GET['cate_id']);
        }
        if (isset($_GET['type'])){
            $where2['type'] = intval($_GET['type']);
        }else{
            $where2['type'] = 1;
        }
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $general = new General();
        $yjjM = D('YjjYingyong');
        $infos = $yjjM->businessList($where1,$where2,$page);
        if (!empty($infos)){
            foreach ($infos as $key=>$val){
                $arr = explode(',', $val['pictures']);
                if (count($arr)>1){
                    $infos[$key]['pictures'] = '/'.$arr[0];
                }
                if(empty($infos[$key]['account'])){
                    $infos[$key]['account'] = NULL;
                }
            }
            $general->returnData($infos,'success');
        }else{
            $general->error(51);
        }
    }
    //添加买卖信息
   /*  public function businessAdd(){
        $general = new General();
        $imghd = new ImageHandle();
        if (IS_POST){
            $data = $_POST;
            $files = $_FILES;
            if (!($data['cate_id'] && $data['type'])){
                $general->error(52);
            }
            if(!($data['username'] && $data['title'] && $data['introduce'] && $data['tel'] && $data['expire_date'])){
                $general->error(53);
            }
            $data['type'] = intval($data['type']);
            $data['cate_id'] = intval($data['cate_id']);

            if ($files['size']>0){
                $img = $imghd->multi_upload($files);
                $data['pictures'] = 'upload/'.date('Ym').'/'.$img;
            } //else{
               // $general->error(54);
            //}
            foreach ($data as &$item){
                $item = trim($item);
            }
            $data['is_check'] = 1;
            $date['addtime'] = date('Y-m-d H:i:s');
            if(M('business')->add($data)){
                $general->returnData(array(),'success');
            }else{
                $general->error(55);
            }
        }else{
            $general->error(6);
        }
    } */
    //买卖信息添加步骤1
    public function  businessAdd(){
        $general = new General();
        $imghd = new ImageHandle();
//        $_POST['type'] = 8;
//        $_POST['cate_id'] = 8;
//        $_POST['username'] = 'username';
//        $_POST['title'] = 'title';
//        $_POST['introduce'] = 'introduce';
//        $_POST['tel'] = 'tel';
//        $_POST['cate_id'] ='cate_id';
        if (IS_POST){
            $data = array();
            $config = array(
                'mimes'         =>  array(), //允许上传的文件MiMe类型
                'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
                'exts'          =>  array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'autoSub'       =>  true, //自动子目录保存文件
                'subName'       =>  array('date', 'Ym'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName'   =>    array('uniqid',''),
                'rootPath'      =>  './upload/', //保存根路径
                'savePath'      =>  '',//保存路径

            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $info   =   $upload->upload();
            $num = count($info);
                $data = array();
                $data['type'] = intval($_POST['type']);
                $data['cate_id'] = intval($_POST['cate_id']);
                $data['username'] = trim($_POST['username']);
                $data['title'] = trim($_POST['title']);
                $data['content'] = trim($_POST['content']);
                $data['tel'] = trim($_POST['tel']);
                $data['expire_date'] = trim($_POST['expire_date']);
                $data['pictures'] = trim($_POST['pictures']);;
                $data['account'] = str_replace(' ', '', $_POST['account']);
                if (!preg_match("/^1[34578]\d{9}$/", $data['account'])){
                    $general->error(2);
                }
                if (!preg_match("/^1[34578]\d{9}$/", $data['tel'])){
                    $general->error(2);
                }
                for ($i=1;$i<=$num;$i++){
                    $key = 'picture'.$i;
                    if (empty($data['pictures'])){
                        $data['pictures'] = 'upload/'.$info[$key]['savepath'].$info[$key]['savename'];
                    }else{
                        $data['pictures'] = $data['pictures'].','.'upload/'.$info[$key]['savepath'].$info[$key]['savename'];
                    }
                }
                if (!($data['cate_id'] && $data['type'])){
                    $general->error(52);
                }
                if(!($data['username'] && $data['title'] && $data['content'] && $data['tel'] && $data['expire_date'])){
                    $general->error(53);
                }

                $data['is_check'] = 1;
                $data['addtime'] = date('Y-m-d H:i:s');
                $res = M('business')->add($data);
                if($res){
                    $general->returnData();
                }else{
                    $general->error(55);
                }
        }else{
            $general->error(6);
        }
    }
    //买卖信息添加步骤2
    public function businessAdd2(){
        $general = new General();
        if ($general){
            $where = array();
            $data = $_POST;
            if (!($data['cate_id'] && $data['type'])){
                $general->error(52);
            }
            if(!($data['username'] && $data['title'] && $data['content'] && $data['tel'] && $data['expire_date'])){
                $general->error(53);
            }
            if (!($data['salt'])){
                $general->error(63);
            }
            $where['salt'] = $data['salt'];
            $data['type'] = intval($data['type']);
            $data['cate_id'] = intval($data['cate_id']);
            foreach ($data as &$item){
                $item = trim($item);
            }
            $data['is_check'] = 1;
            $data['addtime'] = date('Y-m-d H:i:s');
            if(M('business')->where($where)->save($data)){
                $general->returnData(array(),'success');
            }else{
                $general->error(55);
            }
        }else{
            $general->error(6);
        }
    }

    //资讯列表
   /*  public function zxList(){
        $general = new General();
        $where = array();
        $cate_id = intval($_GET['cate_id']);
        $where['cate_id'] = $cate_id;
        $yjjModel = M('zixun');
        $infos = $yjjModel->where($where)->order('id desc')->select();
        $yjjM = D('YjjYingyong');
        $ppp = $yjjM->articleList($where);
        if (!empty($infos)){
            $top = '';
            foreach ($infos as $key => $val){
                if($val['is_top'] == 1){
                    $top = '/'.$val['picture'];
                    unset($infos[$key]);
                    break;
                }
            }


            //dump($infos);die();
            //exit(json_encode(array('error' => 0, 'data' => $infos, 'top' => $top, 'msg' => 'success')));
            exit(json_encode(array('error' => 0, 'data' => $infos, 'top' => $top, 'msg' => 'success')));
        }else{
            $general->error(51);
        }
    } */
    public function zxList(){
        $where = array();
        if(isset($_GET['cate_id'])){
            $where['cate_id'] = intval($_GET['cate_id']);
        }
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $general = new General();
/*      $yjjM = D('YjjYingyong');
        $infos = $yjjM->articleList($where);  */
        $yjjModel = M('zixun');
        $infos = $yjjModel->field('id,cate_id,title,introduce,picture,addtime,from,link')->where($where)->where(array('is_top'=>2))->order('id desc')->page($page,10)->select();
        if (!empty($infos)){
           /*  $top = '';

            foreach ($fff as $key => $val){
                if($val['is_top'] == 1){
                    $top = '/'.$val['picture'];
                    unset($fff[$key]);
                    break;
                }
            }
            foreach ($fff as $key => $val){
                if($val['is_top'] == 1){

                    unset($fff[$key]);

                }
            }
            $i = 0;
            $num = count($fff);
            $newInfo = array();
            foreach ($fff as $v){
                $newInfo[$i] = $v;
                $i++;
            }
            //dump($newInfo);die();
            foreach ($fff as $key => $val){
                $fff[$i] = $val;
                $i++;
            } */

            exit(json_encode(array('error' => 0, 'data' => $infos, 'msg' => 'success')));
        }else{
            $general->error(50);
        }
    }
    //资讯TOP图片
    public function zx_top(){
        $general = new General();
        $where = array();
        if(isset($_GET['cate_id'])){
            $where['cate_id'] = intval($_GET['cate_id']);
        }
        $infos = M('zixun')->field('picture')->where($where)->where(array('is_top'=>1))->order('id desc')->limit(1)->select();
        if(empty($infos)){
            $infos[0]['picture'] = '';
        }
        $general->returnData($infos,'success');
    }
    //滴滴农机列表
    public function ddnjList(){
        $general = new General();
        $cate_id = $_GET['cate_id'];
        $cates = M('ddnj_cate')->order('corder asc')->select();
        if (empty($cate_id)){
            if (!empty($cates)){
                $cate_id = $cates[0]['id'];
            }else{
                $general->error(56);
            }
        }
        $where = array();
        $where['cate_id'] = $cate_id;
        $where['status'] = 1;
        $infos = M('ddnj')->where($where)->select();
        if (!empty($infos)){
            //$general->returnData($infos,'success');
            exit(json_encode(array('error' => 0, 'data' => $infos, 'cates' => $cates, 'msg' => 'success')));
        }else{
            $general->error(57);
        }
    }
    
    //滴滴农机种类
    public function ddnj_cates(){
        $general = new General();
        if(IS_GET){
            $cates = M('ddnj_cate')->field('id,name')->order('corder asc')->select();
            if(empty($cates)){
                $general->error(107);
            }else{
                foreach($cates as $k=>$v){
                    $cate[$k] = $v;
                }
                $general->returnData($cate);
            }
        }
    }
    
    //成为滴滴农机机主
    public function addNjUser(){
        $general = new General();
        if (IS_POST){
            $info = I('post.');
            $authnum = I('post.authnum','','trim,htmlspecialchars');
            $id = I('post.session_id','','trim,htmlspecialchars');
            $info['account'] = str_replace(' ','',$info['account']);
            session_id($id);
            session_start();
            $sess = $_SESSION['auth'];
            foreach ($info as $k=>$v){
                trim($info[$k]);
            }
            if (empty($info['tel']) ){
                $general->error(58);
            }
            if (empty($info['account']) ){
                $general->error(106);
            }
            if (empty($info['username'])){
                $general->error(59);
            }
            if (empty($info['number_plate'])){
                $general->error(60);
            }
            if (empty($info['cate_id'])){
                $general->error(61);
            }
            if (empty($info['longitude'])){
                $general->error(62);
            }
            if (empty($info['latitude'])){
                $general->error(62);
            }

            intval($info['cate_id']);
            if (!preg_match("/^1[34578]\d{9}$/", $info['tel'])){
                $general->error(2);
            }
            if (!preg_match("/^1[34578]\d{9}$/", $info['account'])){
                $general->error(115);
            }
            $accoun = M('ddnj')->where(array('ID_number'=>$info['ID_number']))->getField('account');
            if(!empty($accoun)){
                if($info['account'] != $accoun){
                    $general->error(116);
                }
            }
            $engine_id = M('ddnj')->where(array('ID_number'=>$info['ID_number']))->getField('engine_number');
            if(!empty($engine_id)){
                if($info['engine_number'] == $engine_id){
                    $general->error(117);
                }
            }
//             if($sess['authnum'] != $authnum){
//                 $general->error(5);
//             }else{
//                 unset($info['authnum']);
//             }
            $info['addtime'] = date("Y-m-d");
            $info['status'] = 0;
            if(M('ddnj')->add($info)){
                $general->returnData(array(),'success');
            }else{
                $general->error(55);
            }

        }else{
            $general->error(6);
        }
    }
    //易管理统计报表页
    public function yqgl_tjbb(){
        $general = new General();
        $where = array();
        $where1=array();
        $where2=array();
        $where3 = array();
        $where['type'] = 1;
        $where1['status'] = 1;
        $where2['status'] = 2;
        $where3['status'] = 3;
        $tian = date('Y-m-d');
        //$xingqi = date('Y-m-').(date('d')-6);
        $yue = date('Y-').(date('m')-1).date('-d');
        $yglModel = M('big_data');
        $info_tian = $yglModel->where($where)->where(array('addtime'=>array('gt',$tian)))->select();
        $yes_tian = $yglModel->where($where)->where(array('addtime'=>array('gt',$tian)))->where($where1)->select();
        $ing_tian = $yglModel->where($where)->where(array('addtime'=>array('gt',$tian)))->where($where2)->select();
        $no_tian = $yglModel->where($where)->where(array('addtime'=>array('gt',$tian)))->where($where3)->select();

        $info_yue = $yglModel->where($where)->where(array('addtime'=>array('gt',$yue)))->select();
        $yes_yue = $yglModel->where($where)->where(array('addtime'=>array('gt',$yue)))->where($where1)->select();
        $ing_yue = $yglModel->where($where)->where(array('addtime'=>array('gt',$yue)))->where($where2)->select();
        $no_yue = $yglModel->where($where)->where(array('addtime'=>array('gt',$yue)))->where($where3)->select();
        $day = array();
        $day[1] = date('Y-m-d',strtotime('-6 day'));
        $day[2] = date('Y-m-d',strtotime('-5 day'));
        $day[3] = date('Y-m-d',strtotime('-4 day'));
        $day[4] = date('Y-m-d',strtotime('-3 day'));
        $day[5] = date('Y-m-d',strtotime('-2 day'));
        $day[6] = date('Y-m-d',strtotime('-1 day'));
        $day[7] = date('Y-m-d');
        $day[8] = date('Y-m-d',strtotime('+1 day'));

        $week_info = array();
        for($i=1;$i<=7;$i++){
            $week_info[$i]['yes'] = $yglModel->where($where)->where(array('addtime'=>array(array('gt',$day[$i]),array('lt',$day[$i+1]))))->where($where1)->count();
            $week_info[$i]['ing'] = $yglModel->where($where)->where(array('addtime'=>array(array('gt',$day[$i]),array('lt',$day[$i+1]))))->where($where2)->count();
            $week_info[$i]['no'] = $yglModel->where($where)->where(array('addtime'=>array(array('gt',$day[$i]),array('lt',$day[$i+1]))))->where($where3)->count();
        }

        for($j=0;$j<=7;$j++){
            $day[$j] = substr($day[$j],5);
        }
        //dump($no_yue);die();
        $this->assign('info_tian',$info_tian);
        $this->assign('info_yue',$info_yue);
        $this->assign('yes_tian',count($yes_tian));
        $this->assign('ing_tian',count($ing_tian));
        $this->assign('no_tian',count($no_tian));
        $this->assign('day',$day);
        $this->assign('week',$week_info);
        $this->assign('yes_yue',count($yes_yue));
        $this->assign('ing_yue',count($ing_yue));
        $this->assign('no_yue',count($no_yue));
        //var_dump($yes_yue);die();
        $this->display();
    }
    //易管理重要舆论页
    public function yqgl_zyyq(){
        $where = array();
        $fix = array('type'=>1);
        /* if(isset($_GET['status'])){
            $where['status'] = $_GET['status'];
        }else{
            $where['status'] = 1;
        } */
        $where1 = array('status'=>1);
        $where2 = array('status'=>2);
        $where3 = array('status'=>3);
        $m = M('big_data');
        $info_yes = $m->field('id,title,addtime')->where($fix)->where($where1)->select();
        $info_ing = $m->field('id,title,addtime')->where($fix)->where($where2)->select();
        $info_no = $m->field('id,title,addtime')->where($fix)->where($where3)->select();
        $this->assign('info_yes',$info_yes);
        $this->assign('info_ing',$info_ing);
        $this->assign('info_no',$info_no);
        $this->display();
    }
    //易管理重要舆论详情
    public function yqgl_xq(){
        $where = array();
        $fix = array('type'=>1);
        if (empty($_GET['id'])){
            $this->error('参数错误');
        }else{
            $where['id'] =$_GET['id'];
        }
        $m = M('big_data');
        $art = $m->where($fix)->where($where)->field('id,title,content,addtime,status')->select();
        if (empty($art)){
            $this->error('参数错误');
        }else{
            $art = $art[0];
        }
        switch ($art['status']){
            case 1:
                $art['status'] = '【已处理】';
                break;
            case 2:
                $art['status'] = '【处理中】';
                break;
            case 3:
                $art['status'] = '【待处理】';
                break;
        }
        $this->assign('art',$art);
        $this->display();

    }
    //易家家专家详情页接口
    public function expert_detail(){
        $general = new General();
        if (empty($_GET['eid'])){
            $general->error(6);
        }else{
            $where = array();
            $where['eid'] = $_GET['eid'];
        }
        $m = M('expert');
        $info = $m->where($where)->select();
		
        if (empty($info)){
            $general->error(64);
        }else {
            $info = $info[0];
            if ($info['sex']==1){
                $info['sex'] = '男';
            }elseif ($info['sex']==2){
                $info['sex'] = '女';
            }
            if ($info['level']==1){
                $info['level']='国家级';
            }elseif($info['level']==2){
                $info['level']='省级';
            }else{
                $info['level']='市级';
            }
            $where2['id'] = $info['did'];
            $mes = M('domain')->field('id,name')->where($where2)->select();
            if (!empty($mes)){
                $info['domain'] = $mes[0]['name'];
            }else{
                $info['domain'] = '';
            }
        }
        $where1 = array();
        $where1['expert_id'] = $where['eid'];
        $m1 = M('expert_comment');
        $score = $m1->where($where1)->avg('score');
        /* $num =
        $sum = 0;
        foreach ($data as $k=>$v){
            $sum += $v['score'];
        } */
        $detail = round($score,1);
        $score = round($score);
        $this->assign('detail',$detail);
        $this->assign('score',$score);
        $this->assign('info',$info);
        $this->display();
    }
    //专家评论详情跳转
    public function getExpertComments(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['expert_id'] = $_GET['id'];
        }
        $m = M('expert_comment');
        $comments = $m->where($where)->order('addtime desc')->select();
        /* if (!empty($comments)){
            $comments =
        } */
        $this->assign('comments',$comments);
        $this->display();
    }
    //问政策详情页接口
    public function wzc_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('policy');
        $info = $m->where($where)->select();
        if (empty($info)){
            $general->error(65);
        }else{
            $info = $info[0];
        }
        $info['addtime'] = substr($info['addtime'], 0,10);
        $info['content'] = html_entity_decode($info['content']);
        $where1 = array();
        $where1['did'] = $info['dept_id'];
        $m1 = M('department');
        $dept = $m1->field('did,dname')->where($where1)->select();
        if (empty($dept)){
            $general->error(66);
        }else{
            $dept = $dept[0]['dname'];
        }
        $this->assign('dept',$dept);
        $this->assign('info',$info);
        $this->display();
    }

    /*
     * banner广告图
     * by King
     * 2016-10-28
     *
     * */
    public function banner_index(){
        $type = $_GET['type'];
        $general = new General();
        $banner = M("banner");
        $infos =$banner->where(array('status'=>1,'type'=>$type))->field('id,picture')->limit(3)->select();
        foreach($infos as $k=>$v){
            $infos[$k]['picture'] = '/Uploads/'.$v['picture'];
        }
        if (!empty($infos)){
            $general->returnData($infos,'success');
        }else{
            $general->error(57);
        }
    }
    //专家评价接口
    public function expert_comment(){
        $general = new General();
        if (IS_POST){
            $data = $_POST;
            if(empty($data['expert_id'])){
                $general->error(67);
            }elseif($data['score'] == ''){
                $general->error(70);
            }else{
                intval($data['expert_id']);
                intval($data['score']);
            }
            if(empty($data['mobile'])){
                $general->error(68);
            }
            if (empty($data['content'])){
                $data['content'] = '感谢您的帮助！';
            }
            //if (empty($data['uname'])){
                $wh = array();
                $wh['mobile'] =$data['mobile'];
                $info = M('yjj_user')->where($wh)->select();
                if (empty($info)){
                    $general->error(9);
                }else{
                    $data['uname'] = $info[0]['uname'];
                    $data['uid'] = $info[0]['uid'];
                    unset($data['mobile']);
                }

            foreach ($data as $k=>$v){
                trim($data[$k]);
            }
            $data['addtime'] = date('Y-m-d H:i:s');
            $m = M('expert_comment');
            if($m->add($data)){
                $e = M('expert');
                $ww = array();
                $ww['eid'] = $data['expert_id'];
                if($e->where($ww)->setInc('service_num')){
                    $general->returnData(array(),'success');
                }

            }else{
                $general->error(69);
            }
        }else{
            $general->error(6);
        }
    }
    //想去玩详情接口
    public function xqw_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('play');
        $info = $m->where($where)->select();
        if (empty($info)){
            $general->error(65);
        }else{
            $info = $info[0];
        }
        $info['addtime'] = substr($info['addtime'], 0,10);
        $info['content'] = html_entity_decode($info['content']);

        $this->assign('info',$info);
        $this->display();
    }
    //买卖信息详情页接口
    public function mmxx_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('business');
        $info = $m->where($where)->select();
        if (empty($info)){
            $general->error(65);
        }else{
            $info = $info[0];
        }
        if (!empty($info['pictures'])){
            $pic = array();
            $pic = explode(',', $info['pictures']);
        }
        switch ($info['cate_id']){
            case 1:
                $info['cates'] = '农业';
                break;
            case 2:
                $info['cates'] = '林业';
                break;
            case 3:
                $info['cates'] = '畜牧业';
                break;
            case 4:
                $info['cates'] = '农机';
                break;
        }
        $info['addtime'] = substr($info['addtime'], 0,10);
        $info['content'] = html_entity_decode($info['content']);
        $this->assign('pic',$pic);
        $this->assign('info',$info);
        $this->display();
    }
    //资讯信息详情页接口
    public function zixun_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('zixun');
        $info = $m->where($where)->select();
        if (empty($info)){
            $general->error(65);
        }else{
            $info = $info[0];
        }
        $info['addtime'] = substr($info['addtime'], 0,10);
        $info['content'] = html_entity_decode($info['content']);

        $this->assign('info',$info);
        $this->display();
    }
    //培训信息详情接口
    public function peixun_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('traininginfo');
        $info = $m->where($where)->where(array('software'=>2))->select();
        if (empty($info)){
            $general->error(65);
        }else{
            $info = $info[0];
        }
        $info['addtime'] = substr($info['addtime'], 0,10);
        $info['content'] = html_entity_decode($info['content']);

        $this->assign('info',$info);
        $this->display();
    }
    
    /*
     * 培训信息预定直播接口
     */
    public function peixun_push(){
        $general = new General();
        if(IS_POST){
            $data = array();
            $data = I('post.');
            $data['account'] = str_replace(' ', '', $data['account']);
            $data['id'] = str_replace(' ', '', $data['id']);
            $data['software'] = '2';
            $info = M('training_push')->field('account,trainid,software')->where(array('software'=>2))->select();
            foreach ($info as $k=>$v){
                if($data['account'] == $v['account'] && $data['id'] == $v['trainid']){
                    $general->error(110);
                }
            }
            if(empty($data['title'])){
                $data['title'] = NULL;
            }
            if(empty($data['account'])){
                $general->error(14);
            }
            if(empty($data['id'])){
                $general->error(109);
            }
            $data['trainid'] = $data['id'];
            unset($data['id']);
            M('training_push')->add($data);
            $general->returnData();
        }else{
            $general->error(6);
        }
    }
    
//     //培训课件下载接口
//     public function download(){
//         $general = new General();
// //         if(IS_POST){
//             $id = I('get.id');
//             if(empty($id)){
//                 $general->error(113);
//             }
//             $file = M('video')->field('text,text_name')->where(array('id'=>$id))->find();
//             if(empty($file)){
//                 $general->error(112);
//             }
//             $file_name = $file['text'];
//             $file_name2 = $file['text_name'];
//             //用以解决中文不能显示出来的问题
//             //$file_name=iconv("utf-8","gb2312",$file_name);
//             mb_convert_encoding($file_name2, "gb2312", "UTF-8");
//             $file_sub_path=$_SERVER['DOCUMENT_ROOT'].'/';
//             //dump($file_sub_path);exit;
//             $file_path=$file_sub_path.$file_name;
//             //首先要判断给定的文件存在与否
//             if(!file_exists($file_path)){
//                 $general->error(114);
//             }else{
//                 $url = array();
//                 $url['url'] = "124.133.16.116:8110/Yjj/Yingyong/down/file_path/$file_path/file_name/$file_name2";
//                 dump($url['url']);exit;
//                 $general->returnData($url);
//             }
// //         }
// //         else{
// //             $general->error(6);
// //         }
//     }
    
    /*
     * 培训课件下载接口
     */
    public function down(){
         $file_path = I('get.file_path');
         $file_path = base64_decode($file_path);
         $file_name = I('get.file_name');
        //下载文件需要用到的头
        $type = pathinfo($file_name);
        $extension = $type['extension'];
        if($extension == 'txt'){
            $mime = 'text/plain';
        }else{
            $mime = 'text/plain';
        }
        Header("Content-type: $mime");
        Header("Accept-Ranges: bytes");
        Header("Content-Disposition: attachment; filename=".$file_name);
        $buffer=1024;
        $file_count=0;
        $contents = iconv("gb2312", "utf-8",file_get_contents($file_path));
        echo $contents;
        
    }
    
    //通用公告信息接口
    //type 1 易管理 2 易家家
    public function notice(){
        $notice=M('Notice');
        $general = new General();
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if (!isset($_GET['type'])){
            $general->error(6);
        }else{
            $where = array();
            $where['type'] = I('get.type');
            $noticeList=$notice->field('title,did,content,addtime')->where($where)->order('addtime desc')->page($page,10)->select();
        }
        if (empty($noticeList)){
            $general->error(65);
        }else{
            foreach ($noticeList as &$val){
                if (!empty($val['did'])){
                    $arr = M('Department')->where(array('did'=>$val['did']))->select();
                    $val['bm'] = $arr[0]['dname'];
                }
            }
        }
       // array('error' => 0, 'data' => $noticeList, 'msg' => 'success');
        $this->ajaxReturn(array('error' => 0, 'data' => $noticeList, 'msg' => 'success'));
    }
    //通用公告信息接口-3条最新
    public function noticeNew(){
        $notice=M('Notice');
        $general = new General();
        if (!isset($_GET['type'])){
            $general->error(6);
        }else {
            $where['type'] = I('get.type');
            $noticeList = $notice->field('title,did,content,addtime')->where($where)->limit(3)->order('addtime desc')->select();
        }
        if (empty($noticeList)){
            $general->error(65);
        }else{
            foreach ($noticeList as &$val){
                if (!empty($val['did'])){
                    $arr = M('Department')->where(array('did'=>$val['did']))->select();
                    $val['bm'] = $arr[0]['dname'];
                }
            }
        }
        $this->ajaxReturn(array('error' => 0, 'data' => $noticeList, 'msg' => 'success'));

    }

    //易管理价格趋势H5接口
    public function jgqs(){
        $where1 = array();
        $where2 = array();
        $where3 = array();
        if ($_GET['date1'] !=''){
            $where1['adddate'] = array('egt',$_GET['date1']);
        }else{
            $where1['adddate'] = array('egt',date('Y-m-d',strtotime('-6 day')));
        }
        if ($_GET['date2'] !=''){
            $where3['adddate'] = array('elt',$_GET['date2']);
        }else{
            $tday = date('Y-m-d');
            $where3['adddate'] = array('elt',$tday);
        }
        if (isset($_GET['dept_id'])){
            $where2['dept_id'] = $_GET['dept_id'];
        }else{
            $where2['dept_id'] = 1;
        }
        $num = (strtotime($where3['adddate'][1])-strtotime($where1['adddate'][1]))/86400+1;
        $sc_time = array();
        for ($s=0;$s<$num;$s++){
            $sc_time[] = date('Y-m-d',(strtotime($where1['adddate'][1])+$s*86400));
        }

        $fix = array('is_display'=>1);
        $c = M('jgqs_cates');
        $dis = $c->field('id,name')->where($where2)->where($fix)->order('id desc')->limit(5)->select();
        if (!empty($dis)){
            $cate = array();
            $ids = array();
            foreach ($dis as $val){
                $cate[] = $val['name'];
                $ids[] = $val['id'];
            }

        }else{
            $cate = array();
            $ids = array();
        }
        $where4 = array();
        $m = M('jgqs');
        foreach ($ids as $k=>$item){
            $where4['cate_id'] = $item;
            $infos[$k] = $m->field('cate_id,cate_name,price,adddate')->where($where1)->where($where3)->where($where4)->order('adddate asc')->select();
        }


        $temp = $infos;
        foreach ($infos as $k=>$item){
            foreach($item as $key=>$val){
                $infos[$k][$key] = '';
            }
        }
        foreach ($temp as $key=>$val){
            foreach ($val as $kk=>$vv){
                $real_key = (strtotime($vv['adddate'])-strtotime($where1['adddate'][1]))/86400;
                $infos[$key][$real_key] = $vv;
            }
        }
        foreach ($infos as $key=>$val){
            for ($i=0;$i<$num;$i++){
                if (empty($infos[$key][$i])){
                    $infos[$key][$i] = array('cate_id'=>'','cate_name'=>'','price'=>'','adddate'=>'');
                }
            }
            ksort($infos[$key]);
        }
        //var_dump($cate);die();
        $this->assign('cz',$where2['dept_id']);
        $this->assign('cate',$cate);
        $this->assign('sc_time',$sc_time);
        $this->assign('infos',$infos);
        $this->display();

    }
    /*
    * 地理位置-导航菜单-API
    * by King
    * 2016-11-01
    * Yjj/Yingyong/geography_index
    * */
    public function geography_index(){
        $general = new General();
        $geography = M("geography");
        $geography_list =$geography->where(array('status'=>1,'type'=>2))->field('id,name')->select();
        if (!empty($geography_list)){
            $general->returnData($geography_list,'success');
        }else{
            $general->error();
        }
    }
    /*
   * 地理位置-导航菜单-坐标信息-API
   * by King
   * 2016-11-01
   * Yjj/Yingyong/coordinate_index
   * */
    public function coordinate_index(){
        $general = new General();
        $gid = $_GET['gid'];
        if (empty($gid)){
            $general->error('缺少参数!');
        }
        $geography = M("geography");
        $where = array(
            'status'=>1,
            'type'=>1,
            'gid'=>$gid
        );
        $geography_list =$geography->where($where)->field('id,name,longitude,latitude,proportion,population,content,mobile,account')->select();
        //返回聊天者名称信息
        if ($gid == 2){
            foreach ($geography_list as $key=>$val){
                if (!empty($val['account'])){
                    $where_name = array();
                    $where_name['account'] = $val['account'];
                    $res = M('ygl_user')->field('account,real_name')->where()->find();
                    if (!empty($res)){
                        $geography_list[$key]['real_name'] = $res['real_name'];
                    }else{
                        $geography_list[$key]['real_name'] = $geography_list[$key]['name'].'客服';
                    }
                }else{
                    $geography_list[$key]['real_name'] = $geography_list[$key]['name'].'客服';
                }
            }
        }else{
            foreach ($geography_list as $key=>$val){
                $geography_list[$key]['real_name'] = $geography_list[$key]['name'].'客服';
            }
        }
        if (!empty($geography_list)){
            $general->returnData($geography_list,'success');
        }else{
            $general->error();
        }
    }

    /*
  * 地理位置-导航菜单-坐标详情页-API
  * by google
  * 2016-12-15
  * Yjj/Yingyong/coordinate_index
  * */
    public function geography_detail()
    {
        $general = new General();
        if (empty($_GET['id'])) {
            $general->error(6);
        } else {
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('geography');
        $gid = M('geography')->field('gid')->where(array('id'=>$_GET['id']))->find();
        if ($gid['gid'] != 1) {
            $info = $m->field('name,content')->where($where)->find();
            $info['content'] = htmlspecialchars_decode($info['content']);
        } elseif ($gid['gid'] == 1) {
            $info = $m->field('name,content,proportion,population')->where($where)->find();
            $info['content'] = htmlspecialchars_decode($info['content']);
        }
        if (empty($info)) {
            $general->error(104);
        }
        $this->assign('gid',$gid['gid']);
        $this->assign('infos', $info);
        $this->display();
    }

   //易家家直播播放页

   public function dolive(){
       $general = new General();
       if (!isset($_GET['id'])){
           $general->error(74);
       }else{
           $where = array();
           $where['id'] = $_GET['id'];
       }
       $m = M('live');
       $info = $m->where($where)->select();
       if(empty($info)){
           $general->error(75);
       }else{
           $info = $info[0];
       }
       $this->assign('info',$info);
       $this->display();
   }
    //易家家专家手机直播发起
    public function wantLive(){
        $general = new General();
        $imghd = new ImageHandle();
        if (IS_POST){
            $where = array();
            $data = array();
            $data['title'] = trim($_POST['title']);
            $where['account'] = trim($_POST['account']);
            $data['url'] = $_POST['url'];
            $m = M('expert');
            if (empty($where)){
                $general->error(14);
            }
            if (empty($data['title'])){
                $general->error(76);
            }
            if (empty($data['url'])){
                $general->error(77);
            }

            $info = $m->where($where)->select();
            if (empty($info)){
                $general->error(64);
            }else{
                $info = $info[0];
            }
            $data['expert_name'] = $info['ename'];

            $ck = M('expertlive_picture');
            $pic = $ck->field('id,picture')->where($where)->select();
            if (empty($pic)){
                $general->error(82);
            }else{
                $data['picture'] = $pic[0]['picture'];
            }

            $cut = stripos($data['url'], '?');
            $data['url'] = substr($data['url'], 0,$cut).'.flv';
            $data['status'] = 1;
            $data['addtime'] = date('Y-m-d H:i:s');
            $res =M('expertlive')->add($data);
            if ($res){
                $general->returnData($res,'success');
            }else{
                $general->error(78);
            }
        }else{
            $general->error(6);
        }
    }
    //易家家专家手机直播列表
    public function expertLives(){
        $general = new General();
        $where = array();
        $where['status'] = 1;
        $m = M('expertlive');
        $infos = $m->field('id,expert_name,title,url,picture')->where($where)->select();
        if (empty($infos)){
            $general->error(79);
        }
        $general->returnData($infos,'success');
    }
    //易家家专家手机直播停止接口
    public function liveStop(){
        $general = new General();
        if (empty($_GET['id'])){
            $general->error(80);
        }else{
            $where = array('id'=>$_GET['id']);
        }
        $data = array('status'=>2);
        $m = M('expertlive');
        if($m->where($where)->save($data)){
            $general->returnData(array(),'success');
        }else{
            $general->error(81);
        }
    }
    //易家家专家手机直播状态查询
    public function checkStatus(){
        $general = new General();
        if (empty($_GET['id'])){
            $general->error(6);
        }else{
            $where = array('id'=>$_GET['id']);
        }
        $info = M('expertlive')->field('id,status')->where($where)->select();
        if (empty($info)){
            $general->error(81);
        }elseif($info[0]['status'] == 2){
            $general->error(81);
        }
    }
    //易家家专家个人中心上传直播预览图
    public function livePicture(){
        $general = new General();
        $imghd = new ImageHandle();
        if (IS_POST){
            $data = array();
            if (empty($_POST['account'])){
                $general->error(83);
            }else{
                $where = array('account'=>$_POST['account']);
                $data['account'] = trim($_POST['account']);
            }
            $m = M('expertlive_picture');
            $ckInfo = $m->where($where)->select();
            //info用来判断是修改还是新增
            /* $file = $_FILES['picture'];
            if ($file['size']>0){
                $img = $imghd->image($file);
                $data['picture'] = 'upload/'.date('Ym').'/'.$img;
            }else{
                $general->error(85);
            } */
            $config = array(
                'mimes'         =>  array(), //允许上传的文件MiMe类型
                'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
                'exts'          =>  array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'autoSub'       =>  true, //自动子目录保存文件
                'subName'       =>  array('date', 'Ym'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName'   =>    array('uniqid',''),
                'rootPath'      =>  './upload/', //保存根路径
                'savePath'      =>  '',//保存路径

            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $info   =   $upload->upload();
            if (!empty($info)){
                $data['picture'] = 'upload/'.$info['picture']['savepath'].$info['picture']['savename'];
            }
            $qita = M('expert');
            $ext = $qita->field('eid,ename')->where($where)->select();
            if (empty($ext)){
                $general->error(64);
            }else{
                $data['expert_id'] = $ext[0]['eid'];
                $data['expert_name'] = $ext[0]['ename'];
            }
            $data['addtime'] = date('Y-m-d H:i:s');
 //           dump($ckInfo);die();
            if (empty($ckInfo)){
                if ($m->add($data)){
                    $general->returnData(array(),'success');
                }else{
                    $general->error(55);
                }
            }else{
                if ($m->where($where)->save($data)){
                    $general->returnData(array(),'success');
                }else{
                    $general->error(55);
                }
            }

        }else{
            $general->error(6);
        }
    }

    //易家家用户中心修改个人姓名、性别、头像
    public function changeInfo(){
        $general = new General();
        $imghd = new ImageHandle();
        if (IS_POST){
            if (!isset($_POST['is_expert'])){
                $general->error(6);
            }else{
                $where = array();
                $where['is_expert'] = $_POST['is_expert'];
            }
            if (!isset($_POST['account'])){
                $general->error(6);
            }else{
                $where1 = array();
                $where2 = array();
                $where1['account'] = $_POST['account'];
                $where2['uname'] = $_POST['account'];
            }
            if(!empty($_POST['mobile'])){
                $data['mobile'] = $_POST['mobile'];
            }
            if(!empty($_POST['real_name'])){
                $data['real_name'] = trim($_POST['real_name']);
            }
            if(!empty($_POST['sex'])){
                $data['sex'] = intval($_POST['sex']);
            }
            
            /* $file = $_FILES['headimg'];
            if ($file['size']>0){
                $img = $imghd->image($file);
                $data['headimg'] = 'upload/'.date('Ym').'/'.$img;
            } */
            $config = array(
                'mimes'         =>  array(), //允许上传的文件MiMe类型
                'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
                'exts'          =>  array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'autoSub'       =>  true, //自动子目录保存文件
                'subName'       =>  array('date', 'Ym'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName'   =>    array('uniqid',''),
                'rootPath'      =>  './upload/', //保存根路径
                'savePath'      =>  '',//保存路径

            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $info   =   $upload->upload();
            if (!empty($info)){
                $data['headimg'] = 'upload/'.$info['headimg']['savepath'].$info['headimg']['savename'];
            }
            $rtarr = array('real_name'=>$data['real_name'],'headimg'=>$data['headimg']);
            if ($where['is_expert'] == 1){
                $data['ename'] = $data['real_name'];
                unset($data['real_name']);
                if (M('expert')->where($where1)->save($data)){
                    $general->returnData($rtarr,'success');
                }else{
                    $general->error(27);
                }
            }else{
                if (M('yjj_user')->where($where2)->save($data)){
                    
                    $general->returnData($rtarr,'success');
                }else{
                    $general->error(27);
                }
            }
        }else{
            $data = array();
            if (empty($_GET['account'])){
                $general->error(6);
            }else{
                $where = array();
                $where['uname'] = trim($_GET['account']);
            }
            $m1 = M('yjj_user');
            $mes = $m1->field('uid,uname,real_name,is_expert,headimg,sex,mobile')->where($where)->select();
            if (empty($mes)){
                $general->error(9);
            }else{
                if ($mes[0]['is_expert']){
                    $where1 = array();
                    $where1['account'] = $where['uname'];
                    $info = M('expert')->where($where1)->select();
                    if (empty($info)){
                        $general->error(64);
                    }else{
                        $info = $info[0];
                    }
                    $photo = M('expertlive_picture')->field('id,picture')->where($where1)->select();
                    if (!empty($photo)){
                        $data['picture'] = $photo[0]['picture'];
                    }else{
                        $data['picture'] = '';
                    }
                    $data['real_name'] = $info['ename'];
                    $data['headimg'] = $info['headimg'];
                    $data['sex'] = $info['sex'];
                    $data['mobile'] = $info['mobile'];
                    $data['is_expert'] = 1;
                }else{
                    $info =$mes[0];
                    $data['real_name'] = $info['real_name'];
                    $data['headimg'] = $info['headimg'];
                    $data['sex'] = $info['sex'];
                    $data['mobile'] = $info['mobile'];
                    $data['is_expert'] = 0;
                }

            }
            foreach ($data as $k=>$v){
                if ($v === null){
                    $data[$k] = '';
                }
            }

            $general->returnData($data,'success');
        }
    }

    //易家家领域请求
    public function giveDomain(){
        $general = new General();
        $m = M('domain');
        $info = $m->field('id,name')->select();
        if (empty($info)){
            $general->error(56);
        }else{
            $general->returnData($info,'success');
        }
    }
    //易家家普通用户成为专家接口
    public function toBeExpert(){
        $general = new General();
        if (IS_POST){
            $data =array();
            $data = $_POST;
            if (empty($data['account'])){
                $general->error(83);
            }else{
                //检查是否已经成为专家
                $ck = M('expert');
                $whereck = array('account' => $data['account']);
                $ckinfo = $ck->where($whereck)->select();
                if (!empty($ckinfo)){
                    $general->error(86);
                }
            }
            foreach ($data as $k=>$v){
                trim($data[$k]);
            }
            $where = array('uname' => $data['account']);
            $needInfo = M('yjj_user')->field('uid,mobile')->where($where)->select();
            if (!empty($needInfo)){
                $data['mobile'] = $needInfo[0]['mobile'];
            }else{
                $general->error(9);
            }
            if (empty($data['rpdate'])){
                $data['rpdate'] = date('Y-m-d');
            }
            intval($data['sex']);
            intval($data['level']);
            intval($data['did']);
            $data['status'] = 1;
            $data['addtime'] = date('Y-m-d H:i:s');
            $m = M('expert_shenqing');
            if ($m->add($data)){
                $general->returnData(array(),'success');
            }else{
                $general->error(55);
            }
        }else{
            $general->error(6);
        }
    }
    //易家家部门聊天获取账号接口
    public function getChat(){
        $general = new General();
        if (isset($_GET['did'])){
            $where = array();
            $where['did'] = intval($_GET['did']);
        }else{
            $general->error(13);
        }
        if (empty($where)){
            $general->error(13);
        }
        $m = M('department');
        $info = $m->field('dname,yjj_server')->where($where)->select();
        if (!empty($info)){
            $arr = array();
            $arr['dname'] = $info[0]['dname'].'客服';
            $arr['yjj_server'] = $info[0]['yjj_server'];
        }else{
            $general->error(87);
        }
        $general->returnData($arr,'success');
    }

    //易家家搜索好友功能
    public function searchFriend(){
        $general = new General();
        if (IS_POST){
            if (isset($_POST['s_uname'])){
                $where['uname'] = trim($_POST['s_uname']);
            }else{
                $general->error(6);
            }
            $info = M('yjj_user')->field('uname,headimg,real_name,sex')->where($where)->select();
            if (!empty($info)){
                $info = $info[0];
                if ($info['sex'] == 1){
                    $info['sex'] = '男';
                }else{
                    $info['sex'] = '女';
                }
            }else{
                $general->error(9);
            }
            $general->returnData($info,'succees');
        }else{
            $general->error(6);
        }
    }
    //测试
    public function test(){
        if (IS_POST){
            $config = array(
                'mimes'         =>  array(), //允许上传的文件MiMe类型
                'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
                'exts'          =>  array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
                'autoSub'       =>  true, //自动子目录保存文件
                'subName'       =>  array('date', 'Ym'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName'   =>    array('uniqid',''),
                'rootPath'      =>  './upload/', //保存根路径
                'savePath'      =>  '',//保存路径

            );
            $upload = new \Think\Upload($config);// 实例化上传类
            $info   =   $upload->upload();
            /* if (!empty($info)){
                $data['headimg'] = 'upload/'.$info['headimg']['savepath'].$info['headimg']['savename'];
            } */
            $num = count($info);
            $data['pictures'] = '';
            for ($i=1;$i<=$num;$i++){
                $key = 'picture'.$i;
                if (empty($data['pictures'])){
                    $data['pictures'] = 'upload/'.$info[$key]['savepath'].$info[$key]['savename'];
                }else{
                    $data['pictures'] = $data['pictures'].','.'upload/'.$info[$key]['savepath'].$info[$key]['savename'];
                }
            }
            dump($data);die();
        }else{
            $where = array();
            $info = M('expert')->field('ename,headimg,mobile,account,password,salt,sex')->select();
            $i = 0;
            foreach ($info as $key=>$val){
                $where = array('account'=>$val['account']);
                $ckInfo = M('ygl_user')->where($where)->find();
                if (empty($ckInfo)){
                    $mes = array();
                    $mes['account'] = $val['account'];
                    $mes['password'] = $val['password'];
                    $mes['salt'] = $val['salt'];
                    $mes['headimg'] = $val['headimg'];
                    $mes['real_name'] = $val['ename'];
                    $mes['sex'] = $val['sex'];
                    $mes['mobile'] = $val['mobile'];
                    $mes['auth'] = 3;
                    
                    $make = M('ygl_user')->add($mes);
                    if ($make){
                        $i++;
                        $hx = new \Org\Huanxin\Huanxin;
                        $res = $hx->hx_register($mes['account'],123456);
                    }
                }
            }
            echo $i;die();
            
            $this->display();
        }

    }
    //测试短信
   /*  public function test_mes(){
        $authnum = 123456;
        $mobile = 18354261092;
        session_start();
        $authnum = '123456';
         session_start();
        $_SESSION['auth'] = array('authnum' => $authnum, 'mobile' => $mobile);
        $url = 'http://manager.wxtxsms.cn/smsport/sendPost.aspx';
        $post_data = array();
        $timeout = 5;
        $post_data['uid'] = 'jnht';
        $post_data['upsd'] = md5('jnht@1');
        $post_data['sendtele'] = $mobile;
        $post_data['Msg'] = '欢迎注册智慧三农手机客户端，您的验证码为 '.$authnum.' 有效期为15分钟';
        $post_data['sign'] = '网信科技';
        $res = self::makePost($url,$post_data,$timeout);
        //session(array('expire' => 900));
        $_SESSION['auth'] = array('authnum' => $authnum, 'mobile' => $mobile);
        //return array('session_id' => session_id());
        dump($res);die();
    } */
    public function test_hx(){
        $post_data = array();
        $post_data['qid'] = 1479177155451;
        $post_data['qz_account'] = 15153271641;
        $post_data['account'] = array(0=>'13311119999',1=>'15566667777');
        $data['addQx'] = json_encode($post_data);
        //$post_data['title'] = '777777';
        //$post_data['content'] = '6666666666666666666';
        $url = 'http://sn.local/Ygl/Public/addQggQx';
        $timeout = 5;
        //dump($post_data);die();
        $res = self::makePost($url,$data,$timeout);
        var_dump($res);die();
    }
    public static function makePost($url, $post_data = '', $timeout = 5){//curl

        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $url);

        curl_setopt ($ch, CURLOPT_POST, 1);

        if($post_data != ''){

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        }

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_HEADER, false);

        $file_contents = curl_exec($ch);

        curl_close($ch);

        return $file_contents;

    }

    /*
   * 示范点-导航菜单-坐标信息-API
   * by King
   * 2016-11-01
   * Yjj/Yingyong/coordinate_index
   * */
    public function example_coordinate_index(){
        $general = new General();
        $example = M("example");
        $where = array(
            'status'=>1,
            'type'=>1,
        );
        $example_list =$example->where($where)->field('id,name,longitude,latitude,population,content')->select();
        if (!empty($example_list)){
            $general->returnData($example_list,'success');
        }else{
            $general->error();
        }
    }


    public function video(){





        $id = I('id');
        $example = M("example");

        $where['id'] = $id;

        $video =$example->where($where)->find();

        if($video)
        {

            $this->assign('video',$video);

            $this->display('video');

        }else{

            $this->error('没有这台设备');

        }




    }






}
