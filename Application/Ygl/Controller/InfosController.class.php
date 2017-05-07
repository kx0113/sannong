<?php

namespace Ygl\Controller;
use Common\Common\General;
use Common\Common\StringHandle;

class InfosController extends LinkController {

    //舆情管理统计报表请求链接
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
        $info_yes = $m->field('id,title,addtime,afrom')->where($fix)->where($where1)->select();
        $info_ing = $m->field('id,title,addtime,afrom')->where($fix)->where($where2)->select();
        $info_no = $m->field('id,title,addtime,afrom')->where($fix)->where($where3)->select();
        $this->assign('info_yes',$info_yes);
        $this->assign('info_ing',$info_ing);
        $this->assign('info_no',$info_no);
        $this->display();
    }
    //易管理项目管理详情页接口
    public function xmgl_xq(){
        $general = new General();
        if (empty($_GET['pid'])){
            $general->error(71);
        }
        $where = array();
        $where['pid'] = $_GET['pid'];
        $m = M('project');
        $infos = $m->where($where)->order('addtime DESC')->select();
        if (empty($infos)){
            $general->error(72);
        }else{
            $infos = $infos[0];
        }
        $infos['introduce'] = html_entity_decode($infos['introduce']);
        if ($infos['status'] ==1){
            $infos['zhuangtai'] = '未启动';
        }elseif ($infos['status'] ==2){
            $infos['zhuangtai'] ='进行中';
        }elseif ($infos['status']==3){
            $infos['zhuangtai'] = '已完成';
        }
        $where1 = array();
        $where1['did'] = $infos['dept_id'];
        $d = M('department');
        $dept = $d->where($where1)->select();
        if (empty($dept)){
            $general->error(73);
        }else{
            $infos['dept'] = $dept[0]['dname'];
        }
        $this->assign('infos',$infos);
        $this->display();
    }
    /*
    * 工作汇报详情页接口
    * @param post方式
    * @param id 必选
    */
    public function gzhb_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['rid'] = $_GET['id'];
        }
//        $where['rid'] = 42;
        $m = M('work_report');
        $is_read = $m->where($where)->save(array('is_read'=>1));
        $is_read = M('report_state')->where(array('wid'=>$_GET['id']))->save(array('is_read'=>1));
        $infos = $m->where($where)->find();
        if (empty($infos)){
            $general->error(65);
        }

        $infos['addtime'] = substr($infos['addtime'], 0,10);
        $infos['content'] = html_entity_decode($infos['content']);
        $where1 = array();
        $where1['did'] = $infos['dept_id'];
        $m1 = M('department');
        $dept = $m1->field('did,dname')->where($where1)->select();
        if (empty($dept)){
            $general->error(66);
        }else{
            $dept = $dept[0]['dname'];
        }
        $this->assign('dept',$dept);
        $this->assign('info',$infos);
        $this->display();
    }
    //易管理直播播放页
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

}

