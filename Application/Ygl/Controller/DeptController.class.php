<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10
 * Time: 17:12
 */
namespace Ygl\Controller;
use Common\Common\General;
use Common\Common\StringHandle;

class DeptController extends BaseController {

    /*
     *获取部门菜单
     * @param dept_id 部门id
     * @return json
     */
    public function getMenu(){
        $general = new General();
        if(!isset($_POST['dept'])){
            $general->error(13);
        }
        $dept = intval($_POST['dept']);
//        $dept = 1;
        $menus = M('Menu')->field('mid, mname, pid, url')
            ->where(array('dept_id' => $dept))
            ->order('morder ASC')
            ->select();
        $menus = listToTree($menus, 'mid', 'pid');
        foreach($menus as $key=>&$v){
            $a = $v['_child'];
            if(empty($v['_child'])){
                $v['_child'] = array();
            }
        }
        if(!$menus){
            $menus = array();
        }
        $general->returnData($menus);
    }

    public function getChild($menus){
        foreach($menus as $k=>$v){
            $child = $v['_child'];
        }
    }

    /*
     * 获取部门文章列表
     * @param dept 部门id
     * @param menu 菜单id（可选）
     * @param page 页码（可选，默认1）
     * @return json
     */
    public function getInfo(){
        $general = new General();
        if(!isset($_POST['dept'])){
            $general->error(13);
        }
        $where = array();
        $dept = intval($_POST['dept']);
        $where['sn_dept_article.dept_id'] = $dept;
        $menu = intval($_POST['menu']);
        if($menu){
            $where['menu_id'] = $menu;
        }
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = 5;
        $limit = ($page - 1) * $offset;
        $model = D('DeptArticle');
        $infos = $model->getInfos($where, $limit, $offset, 'aid, title, picture, content, addtime,info');
        $shandle = new StringHandle();
        foreach($infos as $k => $v){
            $infos[$k]['picture'] = '/Uploads/'.$v['picture'];
            $infos[$k]['content'] = $shandle->sub_str(html_entity_decode($v['content']), 30);
            $infos[$k]['addtime'] = substr($v['addtime'], 5, 11);
        }
        $general->returnData($infos);
    }

    /*
     * 获取项目列表
     * @param dept 部门（可选）
     * @param status 状态（可选）
     * @param stime 时间（可选）
     * @param ptitle 项目名称（可选）
     * @param page 页码（可选，默认1）
     * @return json
     */
    public function getPro(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        if(isset($_POST['dept'])){
            $where['dept_id'] = intval($_POST['dept']);
        }
        if(isset($_POST['status'])){
            $where['status'] = intval($_POST['status']);
        }
//        $_POST['stime'] = '2016-9-15';
//        $_POST['etime'] = '2016-12-9';
        if(isset($_POST['stime'])){
            $where['addtime'] = array('EGT', trim($_POST['stime']));
        }
        if(isset($_POST['etime'])){
            $where['addtime'] = array('ELT', trim($_POST['etime']));
        }
        if(isset($_POST['ptitle'])){
            $where['ptitle'] = array("like","%".$_POST['ptitle']."%");
        }
        $offset = 5;
        $limit = ($page - 1) * $offset;
        $data = M('Project')->field('pid, ptitle, pic, department,status,mobile,account,addtime as stime')
            ->where($where)->order('addtime DESC')
            ->limit($limit , $offset)->select();
        foreach($data as $k => $v){
            $data[$k]['pic'] = '/Uploads/'.$v['pic'];
            $data[$k]['stime'] = substr($v['stime'],0,10);
        }
        $general = new General();
        $general->returnData($data);
    }

    /*
     *物联网信息数据
     *@param account 账号 必选
     *@param token Token 必选
     *@param dept 部门id 必选
     *@param type 类别 必选（1监控；2土壤；3空气；4光照）
     *@return json
     */
    public function iot(){
        $general = new General();
        if(empty($_POST['dept'])){
            $general->error(13);
        }else{
            $dept = intval($_POST['dept']);
        }
        if(empty($_POST['type'])){
            $general->error(22);
        }else{
            $type = intval($_POST['type']);
        }
        //字段
        $fields = array(
            1 => 'deviceid',
            2 => 'deviceid,soil_temperature,soil_wet',
            3 => 'deviceid,evnironment_temperature,evnironment_wet',
            4 => 'deviceid,sunlight'
        );
        $model = D('Weather');
        $data = $model->getDataByDept($dept, $fields[$type]);
        if(!$data){
            $general->error(23);
        }
        $general->returnData($data);
    }

    /*
     *根据设备号获取数据详情
     *@param account 账号 必选
     *@param token Token 必选
     *@param deviceid 设备id 必选
     *@param _date 日期 必选（date类型）
     *@return json
     */
    public function getDataByDevice(){
        $general = new General();
        if(empty($_POST['deviceid'])){
            $general->error(24);
        }else{
            $deviceid = $_POST['deviceid'];
        }
        if(empty($_POST['_date'])){
            $general->error(25);
        }else{
            $_date = trim($_POST['_date']);
            $stime = $_date . ' 00:00:00';
            $etime = $_date . ' 23:59:59';
        }
        $field = 'evnironment_temperature,evnironment_wet,soil_temperature,soil_wet,sunlight';
        $model = D('Weather');
        $data = array('_date' => $_date);
        $data['infos'] = $model->getDataByDeviceid($deviceid, $stime, $etime, $field);
        $general->returnData($data);
    }

    public function yqgl_tjbb2(){
        $general = new General();
        if (empty($_GET['action'])){
            $action = 1;
        }else{
            $action = $_GET['action'];
        }
        //$action = 2;
        $where = array();
        switch ($action){
            case 1:
                $first_date = date("Y-m-d", strtotime("-1 week"));
                $last_date = date('Y-m-d',strtotime('+1 day'));
                //$last_date = date("Y-m-d");
                break;
            case 2:
                $first_date = date("Y-m-d", strtotime("-1 month"));
                $last_date = date("Y-m-d");
                break;
            case 3:
                $first_date = date("Y-m-d", strtotime("-6 months"));
                $last_date = date("Y-m-d");
                break;
            case 4:
                $first_date = date("Y-m-d", strtotime("-1 year"));
                
                //$last_date = date("Y-m-d");
                break;
        }
        $num = (strtotime($last_date)-strtotime($first_date))/86400;
        $sc_time = array();
        for ($s=0;$s<$num;$s++){
            $sc_time[] = date('Y-m-d',(strtotime($first_date)+$s*86400));
        }
        $analyze = M('analyze','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8'); 
        $where['time'] = array(array('egt',"{$first_date}"),array('elt',"{$last_date}"));
        $where['status'] = 1;
        //dump($where);exit;
        $line1 = $analyze->field('SUBSTR(time,1,10) as sj')->where($where)->order('time asc')->select();

        $j1=0;
        for ($i=0;$i<count($sc_time);$i++){
            if ($line1[$j1]['sj']==$sc_time[$i]){
                $res1[$i] = $line1[$j1]['num'];
                $j1++;
            }else{
                $res1[$i] = 0;
            }

            if ($line2[$j2]['sj']==$sc_time[$i]){
                $res2[$i] = $line2[$j2]['num'];
                $j2++;
            }else{
                $res2[$i] = 0;
            }

            if ($line3[$j3]['sj']==$sc_time[$i]){
                $res3[$i] = $line3[$j3]['num'];
                $j3++;
            }else{
                $res3[$i] = 0;
            }

            if ($line4[$j4]['sj']==$sc_time[$i]){
                $res4[$i] = $line4[$j4]['num'];
                $j4++;
            }else{
                $res4[$i] = 0;
            }
        }
        dump($line1);exit;
    }
    //舆情管理统计报表请求链接
    /* public function yqgl_tjbb(){
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
    } */
    //易管理重要舆论页
   /*  public function yqgl_zyyq(){
        $where = array();
        $fix = array('type'=>1);
        /* if(isset($_GET['status'])){
         $where['status'] = $_GET['status'];
         }else{
         $where['status'] = 1;
        }// 
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
    } */

    /*
     * 获取专家报表
     * @param page 分页标识 10条一页
     * @param did 领域
     * @param sort 排序   1 按服务人数降序 2按服务人数升序 3 按星级降序 4 按星级升序
     *@return json
     */
public function getExpertReportForms(){
        $sort = intval($_POST['sort']);
       // $sort = intval($_GET['sort']);
        $general = new General();
       // $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $expert=M('expert');
        $expert_commet=M('expert_comment');
        $domain=M('domain');
        $where = array();

        $order='service_num desc';
        if(isset($_POST['did'])){
            $where['did']=$_POST['did'];
        }
        if(isset($_POST['sort'])){
       // if(isset($_GET['sort'])){

            if($sort==1){
                $order='service_num desc';
            }else if($sort==2){
                $order='service_num asc';
            }
        }


        $infos=$expert->field('eid,ename,did,service_num')->where($where)->order($order)->limit(10)->page($page)->select();
        if($sort==3||$sort==4){
            $infos=$expert->field('eid,ename,did,service_num')->where($where)->select();
        }
        if (empty($infos)){
            $general->error(65);
        }else {
            foreach ($infos as $k => $v) {
                if (!empty($infos[$k]['did'])) {
                    $arr = $domain->where(array('id' => $infos[$k]['did']))->select();
                    $infos[$k]['ly'] = $arr[0]['name'];
                }
                if (!empty($infos[$k]['eid'])) {
                    $expert_commetInfo = $expert_commet->field('score')->where(array('expert_id' => $infos[$k]['eid']))->select();
                    $soresum = 0;
                    foreach ($expert_commetInfo as $c_k => $cv) {
                        $soresum = $soresum + $expert_commetInfo[$c_k]['score'];
                    }
                    $infos[$k]['scoresum'] = round($soresum / count($expert_commetInfo));
                }

            }

            if (!empty($infos)) {
                if (isset($_POST['sort'])) {
               // if (isset($_GET['sort'])) {
                    $flag = array();
                    foreach ($infos as $arr) {
                        $flag[] = $arr["scoresum"];
                    }
                    if ($sort== 3) {
                        array_multisort($flag, SORT_DESC, $infos);//重排数组降序排列
                    } else if ($sort == 4) {
                        array_multisort($flag, SORT_ASC, $infos);//重排数组升序排列
                    }
                    $size=10;//每页显示的记录数
                    $infos = array_slice($infos, ($page-1)*$size, $size);
                }

            }
        }
        $domaininfo=$domain->select();
        $this->ajaxReturn(array('error' => 0, 'data' => $infos,'domain' =>$domaininfo, 'msg' => 'success'));

    }


  

    //易管理直播预告信息列表
    public function getInfoList(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = 5;
        $limit = ($page - 1) * $offset;
        $where = array();
        $where['software'] = 1;
        $m = M('Traininginfo');
        $infos = $m->field('id, image, title, stime')
        ->where($where)
        ->order('addtime DESC')
        ->limit($limit, $offset)
        ->select();
        foreach($infos as $k => $v){
            $infos[$k]['image'] = '/Uploads/'.$v['image'];
            $infos[$k]['stime'] = substr($v['stime'], 5, 11);
        }
        $general = new General();
        $general->returnData($infos);
    }
    //易管理直播列表
    public function getLives(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = 5;
        $limit = ($page - 1) * $offset;
        $where = array('status' => 2);
        $where1 = array('software' => 1);
        $data = M('Live')->field('id, image, title, jianjie, url')
        ->where($where)
        ->where($where1)
        ->order('_order ASC')
        ->limit($limit, $offset)
        ->select();
        foreach($data as $key => $val){
            if($val['image']){
                $data[$key]['image'] = '/Uploads/'.$val['image'];
            }
        }
        $general = new General();
        $general->returnData($data);
    }
    //易管理视频列表
    public function getVideos(){
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = 5;
        $limit = ($page - 1) * $offset;
        $where = array();
        $where1 = array();
        $where1['software'] = 1;
        $m = M('Video');
        $infos = $m->field('id, dept, title, image, addtime, url,text,text_name,url3')
        ->where($where1)
        ->order('addtime DESC')
        ->limit($limit, $offset)
        ->select();
        foreach($infos as $k => $v){
            if($v['text'] == ''){
                $infos[$k]['url2'] = '';
            }else{
                $file_path=$file_sub_path.$v['text'];
                $file_name = $v['text_name'];
                $file_path = '124.133.16.116:8110'.substr($file_path,-42);
                 
                mb_convert_encoding($v['text_name'], "gb2312", "UTF-8");
                $type = pathinfo($file_name);
                $extension = $type['extension'];
                if($extension == 'xls'){
                    $infos[$k]['url2'] = 'http://'.$file_path;
                }elseif($extension == 'rar'){
                    $infos[$k]['url2'] = 'http://'.$file_path;
                }elseif($extension == 'zip'){
                    $infos[$k]['url2'] = 'http://'.$file_path;
                }elseif($extension == 'doc'){
                    $infos[$k]['url2'] = 'http://'.$file_path;
                }elseif($extension == 'ppt'){
                    $infos[$k]['url2'] = 'http://'.$file_path;
                }elseif($extension == 'pdf'){
                    $infos[$k]['url2'] = 'http://'.$file_path;
                }
                else{
                    $file_path = base64_encode($file_path);
                    $infos[$k]['url2'] = 'http://'."124.133.16.116:8110/Yjj/Yingyong/down/file_path/$file_path/file_name/$file_name";
                }
            }
            $infos[$k]['image'] = '/Uploads/'.$v['image'];
            $infos[$k]['addtime'] = substr($v['addtime'], 5, 11);
            unset($infos[$k]['text']);
            unset($infos[$k]['text_name']);
        }
        $general = new General();
        $general->returnData($infos);
    }
    /*
  *  工作汇报一级接口
  * @param post 方式
  * @param account 用户account 必填
  */
    public function workreport(){
        $account=I('post.account');//当前登录用户
        //$uid=1;//当前登录用户
        $general = new General();
        if(empty($account)){
            $general->error(6);
        }

        $rs = M('report_state');//工作汇报状态表
        $userinfo= M('ygl_user')->field('uid')->where(array('account'=>$account))->find();
        $data= $rs->field('dept_id,recive_id')->where(array('recive_id'=>$userinfo['uid']))->group('dept_id')->select();
        if(empty($data)){
            $general->error(65);
        }
        foreach ($data as $k => $v) {
            if (!empty($data[$k]['dept_id'])) {
                $where['dept_id']=$data[$k]['dept_id'];
                $where['recive_id']=$data[$k]['recive_id'];
                $where['is_read']=0;
                $count = $rs->where($where)->count();
                $data[$k]['not_read'] = $count;
            }
            if (!empty($data[$k]['dept_id'])) {
                $departmentInfo = M('department')->where(array('did'=>$data[$k]['dept_id']))->find();
                $data[$k]['dname'] = $departmentInfo['dname'];
                $data[$k]['logo'] = $departmentInfo['logo'];
            }
        }
        $this->ajaxReturn(array('error' => 0, 'data' => $data, 'msg' => 'success'));

    }
    /**
     * 部门下的工作汇报列表
     * @param post方式
     * @param p 分页 int   非必填
     * @param dept_id 部门id   必填
     * @param account     登录者account 必填
     */
    public function deptreportList(){
        $account=I('post.account');//当前登录用户
        $dept_id=I('post.dept_id');//当前部门id
        $p = isset($_POST['p']) ? intval($_POST['p']) : 1;
//        $account = 15192776736;
//        $dept_id = 1;
//        $uid = 1;//当前登录用户
        $general = new General();
        if(empty($account)){
            $general->error(6);
        }
        if(empty($dept_id)){
            $general->error(6);
        }
        $rw = M('work_report');//工作汇报表
        $rs = M('report_state');//工作汇报状态表
        $map['dept_id']=$dept_id;
        $userinfo= M('ygl_user')->field('uid')->where(array('account'=>$account))->find();
        $map['recive_id']=$userinfo['uid'];
        $data= $rs->field('wid,is_read')->where($map)->page($p)->limit(10)->order('wid desc')->select();
        if(empty($data)){
            $general->error(65);
        }
        $info=array();
        foreach ($data as $k => $v) {
            if (!empty($data[$k]['wid'])) {
                $where['rid']=$data[$k]['wid'];
                $info[$k] = $rw->where($where)->order('addtime desc')->find();
                $info[$k]['is_read']=$v['is_read'];
            }

        }
        $this->ajaxReturn(array('error' => 0, 'data' => $info, 'msg' => 'success'));


    }

    //易管理部门聊天获取账号接口
    public function getChat(){
        $general = new General();
        if (isset($_POST['did'])){
            $where = array();
            $where['did'] = intval($_POST['did']);
        }else{
            $general->error(13);
        }
        if (empty($where)){
            $general->error(13);
        }
        $m = M('department');
        $info = $m->field('dname,ygl_server')->where($where)->select();
        if (!empty($info)){
            $arr = array();
            $arr['dname'] = $info[0]['dname'].'客服';
            $arr['ygl_server'] = $info[0]['ygl_server'];
//            $info = $info[0]['ygl_server'];
        }else{
            $general->error(87);
        }
        $general->returnData($arr,'success');
    }
    
    
    
    
}