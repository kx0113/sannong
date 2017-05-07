<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 14:56
 */
namespace Ygl\Controller;
use Think\Controller;
use Common\Common\General;

class PublicController extends Controller
{

    public function login()
    {
        if (IS_POST) {
            $account = trim(I('post.account'));
            $password = trim(I('post.password'));
            $general = new General();
            if (empty($account) || empty($password)) {
                $general->error(1);
            }
            $user = D('YglUser')->getUser(array('account' => $account));
            if (empty($user)) {
                $general->error(3);
            }
            $md5pass = $general->makePassword($password, $user[0]['salt']);
            if ($md5pass == $user[0]['password']) {
                $token = $general->makeToken($account, $md5pass);
                if (empty($user[0]['headimg'])){
                    $is_first = 1;
                }else{
                    $is_first = 2;
                }
                $data = array('account' => $account, 'token' => $token,
                    'auth' => $user[0]['auth'], 'is_group' => $user[0]['is_group'],
                    'is_serv' => $user[0]['is_service'], 'real_name' => $user[0]['real_name'], 'headimg' => $user[0]['headimg'],'is_first'=>$is_first);
                $general->returnData($data, 'success');
            } else {
                $general->error(3);
            }
        }
    }

    /*
     * 获取部门列表
     * @param
     * @return json
     */
    public function getDept()
    {
        $where = array('pid' => 0);
        $depts = M('Department')->field('did, dname, logo')->where($where)->select();
        $general = new General();
        $general->returnData($depts);
    }

    /*
     *获取地区
     * @param
     * @return json
     */
    public function getArea()
    {
        $area = areaJson();
        $arr = json_decode($area);
        $newarr = array(array('name' => '地区'));
        foreach ($arr as $k => $v) {
            foreach ($v as $key => $val) {
                $newarr[]['name'] = $val->p;
            }
        }
        $general = new General();
        $general->returnData($newarr);
    }

    /*
     * 获取领域
     * @param
     * @return json
     */
    public function getDomain()
    {
        $domain = M('Domain')->field('id, pid, name')->order('corder ASC')->select();
        $domain = listToTree($domain, 'id', 'pid');
        array_unshift($domain, array('id' => 0, 'pid' => 0, 'name' => '领域'));
        $general = new General();
        $general->returnData($domain);
    }

    /*
     * 获取专家列表
     * @param domain 专家领域（可选）
     * @param area 地区 （可选）
     * @param level 等级（可选）
     * @param sevnum 服务人数（可选）
     * @param ename 姓名（可选）
     * @param page 页码（可选，默认1）
     */
    public function getExpert()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $where = array();
        if (!empty($_GET['domain'])) {
            $where['did'] = intval($_GET['domain']);
        }
        if (!empty($_GET['area'])) {
            $where['province'] = $_GET['area'];
        }
        if (!empty($_GET['level'])) {
            $where['level'] = intval($_GET['level']);
        }
        if (isset($_GET['sevnum'])) {
            $sevnum = intval($_GET['sevnum']);
            if ($sevnum > 2) {
                $where['service_num'] = array('gt', 200);
            } else {
                $min = ($sevnum - 1) * 100;
                $max = $min + 100;
                $where['service_num'] = array('between', "{$min}, {$max}");
            }
        }
        if (!empty($_GET['ename'])) {
            $ename = trim($_GET['ename']);
            $where['ename'] = array('like', "%{$ename}%");
        }
        $offset = 10;
        $limit = ($page - 1) * $offset;
        if ($limit < 0) {
            $limit = 0;
        }
        $model = D('Expert');
        $infos = $model->getExpertWithDomain($where, $limit, $offset, 'eid, headimg, ename, account, sn_domain.name, level, service_num');
        foreach ($infos as $k => $v) {
            if ($infos[$k]['headimg']) {
                $infos[$k]['headimg'] = '/' . $v['headimg'];
            }
        }
        $general = new General();
        $general->returnData($infos);
    }

    //易管理重要舆论详情
    public function yqgl_xq()
    {
        $where = array();
        $fix = array('type' => 1);
        if (empty($_GET['id'])) {
            $this->error('参数错误');
        } else {
            $where['id'] = $_GET['id'];
        }
        $m = M('big_data');
        $art = $m->where($fix)->where($where)->field('id,title,content,addtime,status')->select();
        if (empty($art)) {
            $this->error('参数错误');
        } else {
            $art = $art[0];
        }
        switch ($art['status']) {
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
        $art['content'] = html_entity_decode($art['content']);
        $this->assign('art', $art);
        $this->display();

    }

    //易管理大数据价格趋势调用H5页面接口
    public function jgqs()
    {
        $where1 = array();
        $where2 = array();
        $where3 = array();
        if ($_GET['date1'] != '') {
            $where1['adddate'] = array('egt', $_GET['date1']);
        } else {
            $where1['adddate'] = array('egt', date('Y-m-d', strtotime('-6 day')));
        }
        if ($_GET['date2'] != '') {
            $where3['adddate'] = array('elt', $_GET['date2']);
        } else {
            $tday = date('Y-m-d');
            $where3['adddate'] = array('elt', $tday);
        }
        if (isset($_GET['dept_id'])) {
            $where2['dept_id'] = $_GET['dept_id'];
        } else {
            $where2['dept_id'] = 1;
        }
        $num = (strtotime($where3['adddate'][1]) - strtotime($where1['adddate'][1])) / 86400 + 1;
        $sc_time = array();
        for ($s = 0; $s < $num; $s++) {
            $sc_time[] = date('Y-m-d', (strtotime($where1['adddate'][1]) + $s * 86400));
        }


        $fix = array('is_display' => 1);
        $c = M('jgqs_cates');
        $dis = $c->field('id,name')->where($where2)->where($fix)->order('id desc')->limit(5)->select();
        if (!empty($dis)) {
            $cate = array();
            $ids = array();
            foreach ($dis as $val) {
                $cate[] = $val['name'];
                $ids[] = $val['id'];
            }

        } else {
            $cate = array();
            $ids = array();
        }
        $where4 = array();
        $m = M('jgqs');
        foreach ($ids as $k => $item) {
            $where4['cate_id'] = $item;
            $infos[$k] = $m->field('cate_id,cate_name,price,adddate')->where($where1)->where($where3)->where($where4)->order('adddate asc')->select();
        }


        $temp = $infos;
        foreach ($infos as $k => $item) {
            foreach ($item as $key => $val) {
                $infos[$k][$key] = '';
            }
        }
        foreach ($temp as $key => $val) {
            foreach ($val as $kk => $vv) {
                $real_key = (strtotime($vv['adddate']) - strtotime($where1['adddate'][1])) / 86400;
                $infos[$key][$real_key] = $vv;
            }
        }
        foreach ($infos as $key => $val) {
            for ($i = 0; $i < $num; $i++) {
                if (empty($infos[$key][$i])) {
                    $infos[$key][$i] = array('cate_id' => '', 'cate_name' => '', 'price' => '', 'adddate' => '');
                }
            }
            ksort($infos[$key]);
        }

        /* echo "<pre>";


        print_r($infos);

        exit(); */
        //var_dump($cate);die();
        $this->assign('cz', $where2['dept_id']);
        $this->assign('cate', $cate);
        $this->assign('sc_time', $sc_time);
        $this->assign('infos', $infos);
        $this->display();

    }

    /**
     * 舆情统计 H5
     * by King
     * 2017-1-17
     */
    public function Spider()
    {
        $analyse_model = M('analyze','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8');
        $url_model = M('url','sp_','mysql://admin:En@xun90@124.133.16.116:3306/php_spider#utf8');

        /*
         * 来源统计
         * */
        // 查询所有数据源
        $analyse_count = $analyse_model->field('url_id')->select();
        foreach($analyse_count as $k=>$v){
            $analyse_counts[] = $v['url_id'];
        }
        //去除数组重复数据
        $analyse_count = array_unique($analyse_counts);
        $where_url['url_id'] = array('in',$analyse_count);
        //查询url name
        $data = $url_model->where($where_url)->field('url_id,name')->select();
        //拼装7天时间为一维数组
        $time_7 = array();
        $start = date('Y-m-d', strtotime(date('Y-m-d', time())) -1 * 24 * 3600);
        for($i=1;$i>=-5;$i--){
            $date = date('Y-m-d', strtotime($start) + $i * 24 * 3600);
            $time_7[] = $date;
        }
        foreach($data as $k=>$v){
            //循环前7天时间进行查询
            foreach($time_7 as $v1){
                //来源统计
                $source = $analyse_model->where(array('url_id'=>$v['url_id'],'time'=>$v1))->count();
                $data[$k]['date'][$v1] =  $source;
            }
        }
         /*
          * 处理情况统计
          * */
        $status = array(
            '1'=>array('name'=>'待处理','status'=>1),
            '2'=>array('name'=>'处理中','status'=>2),
            '3'=>array('name'=>'已处理','status'=>3),
        );
        foreach($status as $k=>$v){
            //循环前7天时间进行查询
            foreach($time_7 as $v1){
                //处理情况统计
                $dispose = $analyse_model->where(array('time'=>$v1,'status'=>$v['status']))->count();
                $status[$k]['data'][$v1] =  $dispose;
            }
        }
        $this->assign('status',$status);
        $this->assign('data',$data);
        $this->assign('time_7',$time_7);
        $this->display('spider');


    }

    /**
     *
     */
    public function jgqs2()
    {

        $start = I('date1');
        $end = I('date2');
        $list = array();

        //获取这项下的数据最大时间
        $dept_id = I('dept_id');

        $dept_id = $dept_id ? $dept_id : 1;
        $where['dept_id'] = $dept_id;

        $rescent_time = M('jgqs')->where($where)->max('adddate');
        $cataList = M('jgqs')->field('dept_id,cate_id,cate_name,id')->group('cate_name')->where($where)->order('adddate asc')->select();

        if (!$rescent_time) {
            $rescent_time = date('Y-m-d', time());
        }

        if ($start && $end) {

            if (strtotime($start) > strtotime($end)) {
                $start = date('Y-m-d', strtotime($rescent_time) - 7 * 24 * 3600);
                $end = $rescent_time;
            }

            if (strtotime($start) > (strtotime($end) - 7 * 24 * 3600)) {

                $start = date('Y-m-d', strtotime($end) - 7 * 24 * 3600);
            }

        } else {

            $start = date('Y-m-d', strtotime($rescent_time) - 7 * 24 * 3600);
            $end = $rescent_time;
        }


        $num = (strtotime($end) - strtotime($start)) / (24 * 3600) + 1;
        $time_array = array();

        for ($s = 0; $s < $num; $s++) {
            $time_array[] = date('Y-m-d', strtotime($start) + $s * 24 * 3600);
        }

        $where['adddate'] = array(array('egt', $start), array('elt', $end));
        $where['is_display'] = 1;
        $result = M('jgqs')->where($where)->select();

        $list = $this->suppleData($result,$cataList,$time_array);

        $listCount = count($list);
        for ($k = 0; $k < $listCount; $k++) {
            sort($list[$k]['adddate']);
            $list[$k]['child'] = $this->sortDate($list[$k]['child']);
        }

        /*获取最近一星期的价格*/
        $where = array();
        $start = date('Y-m-d', time() - 8 * 24 * 3600);
        $end = date('Y-m-d', time());
        $where['adddate'] = array(array('egt', $start), array('elt', $end));
        $where['is_display'] = 1;
        $weekData = M('jgqs')->where($where)->select();

        $num = (strtotime($end) - strtotime($start)) / (24 * 3600) + 1;
        $time_array =array();

        for ($s = 0; $s < $num-1; $s++) {
            $time_array[] = date('Y-m-d', strtotime($start) + $s * 24 * 3600);
        }


        $weekList = $this->suppleData($weekData,$cataList,$time_array);

        $listCount = count($weekList);
        for ($k = 0; $k < $listCount; $k++) {
            sort($weekList[$k]['adddate']);

            $weekList[$k]['child'] = $this->sortDate($weekList[$k]['child']);
        }


        if($weekList)
        {

            foreach ($weekList as $key=> $val)
            {
                $weekAdddate = array();
                foreach($val['adddate'] as $kk=> $vv)
                {
                    $weekAdddate[$kk] =date('m/d',strtotime($vv));
                }


                $weekList[$key]['adddate'] = $weekAdddate;

            }


        }


        $this->assign('cz', $dept_id);
        $this->assign('date1', $start);
        $this->assign('date2', $end);
        $this->assign('infos', $list);
        $this->assign('weekList',$weekList);
        $this->display('jgqs');


    }


    public function suppleData($result,$list,$time_array)
    {

        if($result){

            foreach ($list as $key=>$val)
            {
                foreach ($result as $kk=> $vv)
                {
                    if($vv['cate_id'] == $val['cate_id'] )
                    {
                        $num = count($list[$key]['child']);
                        $list[$key]['child'][$num]['price'] = $vv['price'];
                        $list[$key]['child'][$num]['adddate'] = $vv['adddate'];
                        $list[$key]['adddate'][] = $vv['adddate'];
                        $vv['price'] = 0;

                    }
                }
            }

            foreach($list as $k=>$val)
            {

                foreach($time_array as $kk=>$vv)
                {
                    if(!in_array($vv,$val['adddate']))
                    {
                        $num = count($list[$k]['child']);
                        $list[$k]['child'][$num]['price'] = 0;
                        $list[$k]['child'][$num]['adddate'] = $vv;
                        $list[$k]['adddate'][] = $vv;
                    }

                }

            }

        }else {

            foreach ($list as $k => $val) {

                foreach ($time_array as $kk => $vv) {
                    if (!in_array($vv, $val['adddate'])) {
                        $num = count($list[$k]['child']);
                        $list[$k]['child'][$num]['price'] = 0;
                        $list[$k]['child'][$num]['adddate'] = $vv;
                        $list[$k]['adddate'][] = $vv;

                    }
                }
            }
        }


        return $list;


    }


    public function sortDate($priceData)
    {

        $num = count($priceData);

        for ($i = 0; $i < $num; $i++)
        {
            $tmp = $priceData[$i];
            //内层循环控制，比较并插入

            for ($j = $i - 1; $j >= 0; $j--)
            {

                $jvalue = strtotime($priceData[$j]['adddate']);
                $tempvalue = strtotime($tmp['adddate']);

                if ($tempvalue < $jvalue)
                {
                    //发现插入的元素要小，交换位置，将后边的元素与前面的元素互换
                    $priceData[$j + 1] = $priceData[$j];
                    $priceData[$j] = $tmp;
                } else {
                    //如果碰到不需要移动的元素，由于是已经排序好是数组，则前面的就不需要再次比较了。
                    break;
                }
            }


        }

        return $priceData;
    }
    /*
    * 应急管理-应急警报-API
    * by King
    * 2016-11-03
    * Ygl/public/emergency_index
    * */
    public function emergency_index(){
        $general = new General();
        $emergency = M("emergency");
        $where = array(
            'pid'=>0,
            'status'=>1,
        );
        $emergency_list =$emergency->where($where)->field('id,name')->select();
        if (!empty($emergency_list)){
            $general->returnData($emergency_list,'success');
        }else{
            $general->error();
        }
    }
    /*
   * 应急管理-应急警报-详情-API
   * by King
   * 2016-11-10
   * Ygl/public/emergencys_index
   * */
    public function emergencys_index(){
        $general = new General();
//        $id = intval($_GET['id']);
        $id = 47;
//        $account = $_GET['account'];
//        if (empty($id) || empty($account)){
//            $general->error(111);
//        }

        $emergency = M("emergency");
        $where = array(
            'id'=>$id,
            'status'=>1,
        );
        $emergency_list =$emergency->where($where)->select();
        $emergency_list[0]['commander_account'] = explode(',',$emergency_list[0]['commander_account']);
        $emergency_list[0]['commander'] = explode(',',$emergency_list[0]['commander']);
        $emergency_list[0]['commander_mobile'] = explode(',',$emergency_list[0]['commander_mobile']);
        //循环输出指挥员信息
        foreach($emergency_list[0]['commander'] as $k=>$v){
            foreach($emergency_list[0]['commander_mobile'] as $k1=>$v1){
                foreach($emergency_list[0]['commander_account'] as $k2=>$v2){
                if($k == $k1 && $k1 == $k2){
                    $emergency_list[0]['commanders'][] = array(
                        'commander'=>$v,
                        'commander_mobile'=>$v1,
                        'commander_account'=>$v2
                    );
                }
                }
            }
        }
        unset($emergency_list[0]['commander'],$emergency_list[0]['commander_mobile'],$emergency_list[0]['commander_account']);
        //循环输出监控信息
        $emergency_list[0]['video_url'] = explode(',',$emergency_list[0]['video_url']);
        $emergency_list[0]['video_location'] = explode(',',$emergency_list[0]['video_location']);
        $emergency_list[0]['video_urls'] = array();
        foreach($emergency_list[0]['video_url'] as $k=>$v){
            foreach($emergency_list[0]['video_location'] as $k1=>$v1){
                if($k == $k1){
                    $emergency_list[0]['video_urls'][] = array(
                        'video_url'=>$v,
                        'video_location'=>$v1
                    );
                }
            }
        }
        unset($emergency_list[0]['video_url'],$emergency_list[0]['video_location']);
        
        if (!empty($emergency_list)){
            $ck = explode(',',$emergency_list[0]['is_read']);
            if (!in_array($account,$ck)){
                if (!empty($emergency_list[0]['is_read'])){
                    $data['is_read'] = $emergency_list[0]['is_read'].','.$account;
                }else{
                    $data['is_read'] = $account;
                }
                $emergency->where($where)->save($data);
            }

//            dump($emergency_list);die();

            $general->returnData($emergency_list,'success');
        }else{
            $general->error();
        }
    }
    //应急管理-应急警报列表
    public function emergency_list(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $general = new General();
        $where = array();
        if (empty($_GET['account'])){
            $general->error(14);
        }else{
            $account = trim($_GET['account']);
        }
        if (empty($_GET['pid'])){
            $general->error(52);
        }else{
            $where['pid'] = intval($_GET['pid']);
        }
        $where['status'] = 1;
        $res = M('emergency')->field('id,title,is_read')->where($where)->page($page,10)->select();
        if (!empty($res)){
            foreach ($res as $key=>$val){
                $ck = array();
                $ck = explode(',',$val['is_read']);
                if (in_array($account,$ck)){
                    //已读
                    $res[$key]['is_read'] = 1;
                }else{
                    //未读
                    $res[$key]['is_read'] = 0;
                }
            }
        }else{
            $res = array();
        }
        $general->returnData($res,'success');
    }

    /*
      * 应急管理-预警通知-API
      * by King
      * 2016-11-03
      * http://sn.cc/Ygl/public/emergency_message_index/page/1
      * */
    public function emergency_message_index(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $general = new General();
        $emergency_message = M("big_data");
        $where = array(
            'status'=>4,
            'type'=>1,
        );
        $offset = 5;
        $limit = ($page - 1) * $offset;
        if($limit < 0){
            $limit = 0;
        }
        $emergency_message =$emergency_message->where($where)->limit($limit, $offset)->field('id,title,content,addtime,afrom,images')->select();
        foreach($emergency_message as $k=>$v){
            $emergency_message[$k]['addtime'] = substr($v['addtime'],0,10);
            $emergency_message[$k]['images'] = '/Uploads/'.$v['images'];
        }
        if (!empty($emergency_message)){
            $general->returnData($emergency_message,'success');
        }else{
            $general->error();
        }
    }
    //易管理应急管理-预警通知-详情
    public function emergency_message_detail(){
        $general = new General();
        $where = array();
        $id = intval($_GET['id']);
        if (empty($id)){
            $general->error(6);
        }
        
        $emergency_message = M("big_data");
        $where = array(
            'status'=>4,
            'id'=>$id
        );
        $emergency_message =$emergency_message->where($where)->field('id,title,content,addtime,afrom,images')->find();
        if (!empty($emergency_message)){
            $emergency_message['content'] = html_entity_decode($emergency_message['content']);
            $this->assign('info',$emergency_message);
            $this->display();
        }else{
            $general->error(65);
        }
    }

    //易管理-应急管理-监控视频调用地址
    public function watchMonitor(){
        $general = new General();
        if (empty($_GET['url'])){
            $general->error(74);
        }else{
            $url = $_GET['url'];
        }
        $this->assign('url',$url);
        $this->display();
    }

    //易管理添加群公告
    public function addQungg(){
        $general = new General();
        if (IS_POST){
            $data = array();
            if (empty($_POST['qid'])){
                $general->error(88);
            }else{
                $data['qid'] = htmlspecialchars($_POST['qid']);
            }
            if (empty($_POST['account'])){
                $general->error(14);
            }else{
                $where = array();
                $where['account'] = trim($_POST['account']);
                $data['account'] = trim($_POST['account']);
            } 
            //$data['qid'] = 287703600350626324;
            if (empty($_POST['title'])){
                $data['title'] = '公告';
            }else{
                $data['title'] = trim($_POST['title']);
            }
            if (!empty($_POST['content'])){
                $data['content'] = trim($_POST['content']);
            }
            $info = M('ygl_user')->where($where)->select();
            if (!empty($info)){
                $data['hostname'] = $info[0]['real_name'];
            }else{
                $general->error(9);
            }
            //权限判断
            $where1 = array();
            $where1['account'] = $where['account'];
            $where1['qid'] = $data['qid'];
            $where1['status'] = 1;
            $qx = M('qungg_qx')->where($where1)->select();
            if (empty($qx)){
                $ckInfo = $this->getQunz($data['qid']);
                if ($data['account'] != $ckInfo){
                    $general->error(94);
                }
            }

            $data['addtime'] = date('Y-m-d H-i-s',time());
            if (M('qungg')->add($data)){
                
                $hx = new \Org\Huanxin\Huanxin;
                $res = $hx->messageGroup($data['qid'], $data['content'], $data['account']);

                $general->returnData(array(),'success');
            }else{
                $general->error(55);
            }
        }else{
            $general->error(6);
        }
    }
    //易管理获取群公告
    public function getQungg(){
        $general = new General();
        $where = array();
        if (empty($_GET['qid'])){
            $general->error(88);
        }else{
            $where['qid'] = trim($_GET['qid']);
        }
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $res = M('qungg')->field('id,qid,hostname,title,content,addtime,account')->where($where)->where(array('status'=>1))->order('addtime desc')->page($page,5)->select();
        foreach ($res as $key=>$val){
            $where_gg = array();
            $where_gg['account'] = $val['account'];
            $headimg = M('ygl_user')->field('account,headimg')->where($where_gg)->find(); 
            if (!empty($headimg)){
                $res[$key]['headimg'] = $headimg['headimg'];
            }else{
                $res[$key]['headimg'] = '';
            }
        }
        $general->returnData($res,'success');
    }
    //易管理删除群公告
    public function delQungg(){
        $general = new General();
        $where = array();
        if (IS_POST){
            if (empty($_POST['id'])){
                $general->error(6);
            }else{
                $where['id'] = intval($_POST['id']);
            }
            if (empty($_POST['account'])){
                $general->error(6);
            }else{
                $account = trim($_POST['account']);
            }
            $info = M('qungg')->where($where)->where(array('status'=>1))->select();
            if (!empty($info)){
                $info = $info[0];
                $where1 = array();
                $where1['qid'] = $info['qid'];
                $where1['status'] = 1;
                $where1['account'] = $account;
                $ckInfo = M('qungg_qx')->where($where1)->select();
                if (empty($ckInfo)){
                    $qzInfo = $this->getQunz($info['qid']);
                    if ($qzInfo !=$account){
                        $general->error(89);
                    }
                }
            }else{
                $general->error(65);
            }
            $data = array('status'=>0);
            if (M('qungg')->where($where)->save($data)){
                $general->returnData(array(),'success');
            }else{
                $general->error(90);
            }
        }else{
            $general->error(6);
        }
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
    //引用类库获取群主账号信息
    private function getQunz($qid){
        $hx = new \Org\Huanxin\Huanxin;
        $res = $hx->hx_check($qid);
        $res = json_decode($res);
        $mem = $res->data[0]->affiliations;
        
        foreach($mem as $key=>$val){
            $mem[$key] = $this->object2array($mem[$key]);
        }
        foreach ($mem as $k=>$v){
            foreach ($v as $k1=>$v1){
                //如果是群主，单独拿出来
                if ($k1=='owner'){
                    $owner = $v1;
                }
            }
        }
        return $owner;
    }
    //添加群内可以发布公告权限的用户
    /* @qz_account 群主账号，即操作人账号
     * @qid 群id
     * @account 要添加权限的账号
     *  */
    public function addQggQx(){
        $general = new General();
        $js = $_POST['addQx'];
        $js = str_replace('\\', '', $js);
        $qx = json_decode($js,true);

        $qid = $qx['qid'];
        $qz_account = $qx['qz_account'];
        $account = $qx['account'];
        /* $qid = '1483518378763';
        $qz_account = '13315512168'; */
        /* $account = '17606395038'; */
        if (empty($qid)){
            $general->error(88);
        }
        if (empty($qz_account)){
            $general->error(92);
        }
        if (empty($account)){
            $general->error(95);
        }
        $qz_info = $this->getQunz($qid);
        if ($qz_info != $qz_account){
            $general->error(91);
        }
//        echo('OK');die();
        $data = array();
        $data['account'] = array();
        foreach ($account as $key=>$val){
            $where = array();
            $where['qid'] = $qid;
            $where['account'] = $val;
            $where['status'] = 1;
            $ckInfo = M('qungg_qx')->where($where)->select();
            //var_dump($ckInfo);die();
            if (!empty($ckInfo)){
                unset($account[$key]);
            }else{
                if (empty($data['account'])){
                    $data['account'] = array($account[$key]);
                }else{
                    $data['account'][] = $account[$key];
                    //var_dump($account[$key]);die();
                }
            }
        }
        $data['qid'] = $qid;
        $data['status'] = 1;
        $data['addtime'] = date('Y-m-d H:i:s');
        //var_dump($data);die();
        if (!empty($data['account'])){
            $values = '';
            foreach ($data['account'] as $kk=> $vv){
                if (empty($values)){
                    $values = "({$data['qid']},{$vv},{$data['status']},"."'"."{$data['addtime']}"."'".")";
                }else{
                    $values = $values.",({$data['qid']},{$vv},{$data['status']},"."'"."{$data['addtime']}"."'".")";
                }
            }
            $sql = "INSERT INTO sn_qungg_qx(qid,account,status,addtime) values".$values;
            //var_dump($sql);die();
            $res = M('qungg_qx')->execute($sql);
            if ($res){
                $general->returnData(array(),'success');
            }else{
                $general->error(55);
            }
        }else{
            $general->error(93);
        }

    }
    //群主获取本群内可以操作群公告的用户列表
    /* @qid     群id
     * @account 群主账号
     *  */
    public function listQggQx(){
        $general = new General();
        $where = array();
        if (!empty($_POST['qid'])){
            $where['qid'] = trim($_POST['qid']);
        }else{
            $general->error(88);
        }
        if (!empty($_POST['account'])){
            $account = trim($_POST['account']);
        }else{
            $general->error(92);
        }
        $qz = $this->getQunz($where['qid']);
        if ($qz != $account){
            $general->error(91);
        }
        $where['status'] = 1;
        $qx = M()->table('sn_qungg_qx Q')->where($where)->join('sn_ygl_user U on Q.account=U.account','LEFT')
                ->field('qid,Q.account,U.headimg,U.real_name')->select();
        $general->returnData($qx,'success');
    }

    //群主删除群内操作公告者权限
    public function delQggQx(){
        $general = new General();
        $where = array();
        if (!empty($_POST['qid'])){
            $where['qid'] = trim($_POST['qid']);
        }else{
            $general->error(88);
        }
        if (!empty($_POST['qz_account'])){
            $qz_account = trim($_POST['qz_account']);
        }else{
            $general->error(92);
        }
        if (!empty($_POST['account'])){
            $account = trim($_POST['account']);
            $where['account'] = $account;
        }else{
            $general->error(95);
        }
        $qz = $this->getQunz($where['qid']);
        if ($qz != $qz_account){
            $general->error(91);
        }
        $where['status'] = 1;
        $info = M('qungg_qx')->where($where)->select();
        if (!empty($info)){
            $data = array('status'=>0);
            if(M('Qungg_qx')->where($where)->save($data)){
                $general->returnData(array(),'success');
            }else{
                $general->error(27);
            }
        }else{
            $general->error(96);
        }
    }
    /*
 * 易管理部门文章详情页
 *
 *
 */
    public function dept_article_detail(){
        $general = new General();
        if (empty($_GET['id'])) {
            $general->error(6);
        } else {
            $where = array();
            $where['aid'] = $_GET['id'];
        }

        $info = M('dept_article')->where($where)->find();
        if (empty($info)) {
            $general->error(104);
        }else{
            $info['content'] = html_entity_decode($info['content']);
        }
        $this->assign('infos', $info);
      //  dump($info);die();
        $this->display();
    }
    //应急管理-统计报表
    public function yjgl_tj(){
        $general = new General();
        if (empty($_GET['action'])){
            $action = 1;
        }else{
            $action = $_GET['action'];
        }
        $where = array();
        switch ($action){
            case 1:
                $first_date = date("Y-m-d", strtotime("-1 week"));
                $last_date = date("Y-m-d");
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
                $last_date = date("Y-m-d");
                break;
        }

        $num = (strtotime($last_date)-strtotime($first_date))/86400;
        $sc_time = array();
        for ($s=0;$s<$num;$s++){
            $sc_time[] = date('Y-m-d',(strtotime($first_date)+$s*86400));
        }

        $where['time'] = array(array('egt',"{$first_date}"),array('elt',"{$last_date}"));
        $where['status'] = 2;
        $where['ever'] = 1;
        $where['pid'] = 1;
        $line1 = M('emergency')->field('pid,count(ever) as num,SUBSTR(time,1,10) as sj')->where($where)->group('SUBSTR(time,1,10)')->order('time asc')->select();
        $where['pid'] = 2;
        $line2 = M('emergency')->field('pid,count(ever) as num,SUBSTR(time,1,10) as sj')->where($where)->group('SUBSTR(time,1,10)')->order('time asc')->select();
        $where['pid'] = 3;
        $line3 = M('emergency')->field('pid,count(ever) as num,SUBSTR(time,1,10) as sj')->where($where)->group('SUBSTR(time,1,10)')->order('time asc')->select();
        $where['pid'] = 4;
        $line4 = M('emergency')->field('pid,count(ever) as num,SUBSTR(time,1,10) as sj')->where($where)->group('SUBSTR(time,1,10)')->order('time asc')->select();

        $j1=0;
        $j2=0;
        $j3=0;
        $j4=0;
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

        //组装下方表格
        $sheet1 = array_reverse($res1);
        $sheet2 = array_reverse($res2);
        $sheet3 = array_reverse($res3);
        $sheet4 = array_reverse($res4);
        $tm = array_reverse($sc_time);
        $fin = array();
        for ($x=0;$x<7;$x++){
            $fin[$x]['tm'] = $tm[$x];
            $fin[$x][1] = $sheet1[$x];
            $fin[$x][2] = $sheet2[$x];
            $fin[$x][3] = $sheet3[$x];
            $fin[$x][4] = $sheet4[$x];
        }
//        dump($fin);die();
        $this->assign('fin',$fin);
        $this->assign('line1',$res1);
        $this->assign('line2',$res2);
        $this->assign('line3',$res3);
        $this->assign('line4',$res4);
        $this->assign('st',$action);
        $this->assign('x',$sc_time);
        $this->display();
    }

    public function getInfo()
    {

        $url = "http://nc.mofcom.gov.cn/channel/gxdj/jghq/jg_detail.shtml?id=20756&page=1";
        $contents =  file_get_contents("compress.zlib://".$url);
        $output = iconv('GB2312', 'UTF-8', $contents);
        preg_match_all('/<td>([^<]*)<\/td>/i', $output, $matches);

        echo "<pre>";
        print_r($matches);



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
            $data['software'] = '1';
            $info = M('training_push')->field('account,trainid')->where(array('software'=>1))->select();
            foreach ($info as $k=>$v){
                if($data['account'] == $v['account'] && $data['id'] == $v['trainid'] ){
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

    public function peixun_xq(){
        $general = new General();
        if (!isset($_GET['id'])){
            $general->error(6);
        }else{
            $where = array();
            $where['id'] = $_GET['id'];
        }
        $m = M('traininginfo');
        $info = $m->where($where)->where(array('software'=>1))->select();
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

    //易管理部门评价接口
    public function deptEvaluation(){
        $general = new General();
        $data = array();
        //软件
        $data['type'] = 1;
        $data['number'] = 1;
        $data['addtime'] = date('Y-m-d H:i:s');
        //部门
        if (empty($_POST['dept_id'])){
            $general->error(13);
        }else{
            $data['dept_id'] = intval($_POST['dept_id']);
        }
        //分数
        if (empty($_POST['score'])){
            $general->error(70);
        }else{
            $data['score'] = intval($_POST['score']);
        }
        //评价者账号
        if (empty($_POST['account'])){
            $general->error(83);
        }else{
            $data['account'] = $_POST['account'];
        }
        //评价内容
        if (empty($_POST['content'])){
            $data['content'] = '十分感谢，您的回答完美解决了我的问题';
        }else{
            $data['content'] = trim($_POST['content']);
        }
        if(M('dept_comment')->add($data)){
            $general->returnData(array(),'success');
        }else{
            $general->error(69);
        }
    
    }

    //易管理-大数据-部门服务统计
    public function showEvaluation(){
        $general = new General();
        $model = M('dept_comment');
        $info = $model->field('type,sum(number) as number,avg(score) as avg_score,dept_id')->group('type,dept_id')->order('type asc,avg_score desc,number desc')->select();
//        dump($info);
        if (!empty($info)){
            foreach ($info as $key=>$val){
                $info[$key]['avg_score'] = round($val['avg_score'],1);
                $info[$key]['xx_number'] = round($val['avg_score']);
                switch ($val['dept_id']){
                    case 1:
                        $info[$key]['dept_name'] = '农办';
                        continue;
                    case 2:
                        $info[$key]['dept_name'] = '林业局';
                        continue;
                    case 3:
                        $info[$key]['dept_name'] = '畜牧局';
                        continue;
                    case 4:
                        $info[$key]['dept_name'] = '水务局';
                        continue;
                    case 5:
                        $info[$key]['dept_name'] = '气象局';
                        continue;
                    case 6:
                        $info[$key]['dept_name'] = '粮食局';
                        continue;
                    case 7:
                        $info[$key]['dept_name'] = '农广校';
                        continue;
                    case 8:
                        $info[$key]['dept_name'] = '农机局';
                        continue;
                    case 9:
                        $info[$key]['dept_name'] = '农业局';
                        continue;
                    case 10:
                        $info[$key]['dept_name'] = '合作社';
                        continue;
                }
            }
        }else{
            $info = array();
        }
        $general->returnData($info,'success');
    }
    
    //易管理-监测是否为可以发送群公告的人
    public function checkQggqx(){
        $general = new General();
        $where = array();
        if (empty($_GET['qid'])){
            $general->error(88);
        }else{
            $where['qid'] = htmlspecialchars($_GET['qid']);
        }
        if (empty($_GET['account'])){
            $general->error(14);
        }else{
            $where['account'] = htmlspecialchars($_GET['account']);
        }
        $where['status'] = 1;
        $info = M('qungg_qx')->where($where)->find();
        if (!empty($info)){
            $data = array('status'=>1);
            $general->returnData($data,'success');
        }else{
            $ckInfo = $this->getQunz($where['qid']);
            if ($where['account'] != $ckInfo){
                $data = array('status'=>2);
                $general->returnData($data,'error');
            }else{
                $data = array('status'=>1);
                $general->returnData($data,'success');
            }
        }
    }
    
    //易管理-部门服务统计报表
    public function bmfw_tjbb(){
        if (empty($_GET['action'])){
            $action = 1;
        }else{
            $action = intval($_GET['action']);
        }
        switch ($action){
            case 1:
                $first_date = date("Y-m-d", strtotime("-1 week"));
                $last_date = date("Y-m-d");
                break;
            case 2:
                $first_date = date("Y-m-d", strtotime("-1 month"));
                $last_date = date("Y-m-d");
                break;
            case 3:
                $first_date = date("Y-m-d", strtotime("-1 year"));
                $last_date = date("Y-m-d");
                break;
        }
        $model = M('dept_comment');
        $where = array();
        $where['addtime'] = array(array('egt',"{$first_date}"),array('elt',"{$last_date}"));
        //易管理数据
        $where['type'] = 1;
        $info1 = $model->field('sum(number) as number,avg(score) as avg_score,dept_id')->where($where)->group('dept_id')->order('dept_id asc')->select();
        //易家家数据
        $where['type'] = 2;
        $info2 = $model->field('sum(number) as number,avg(score) as avg_score,dept_id')->where($where)->group('dept_id')->order('dept_id asc')->select();
        
        //循环遍历部门
        $j1 = 0;
        $j2 = 0;
        for ($i=1;$i<=10;$i++){
            if ($info1[$j1]['dept_id'] == $i){
                $ygl[$i] = $info1[$j1]['number'];
                $j1++;
            }else{
                $ygl[$i] = 0;
            }
            if ($info2[$j2]['dept_id'] == $i){
                $yjj[$i] = $info2[$j2]['number'];
                $j2++;
            }else{
                $yjj[$i] = 0;
            }
        }
        $dept = array('农办','林业局','畜牧局','水务局','气象局','粮食局','农广校','农机局','农业局','合作社');
        /* dump($info1);
        dump($info2);
        dump($ygl);
        dump($yjj);
        die(); */
        $this->assign('st',$action);
        $this->assign('ygl',$ygl);
        $this->assign('yjj',$yjj);
        $this->assign('dept',$dept);
        $this->display();
    }
    
    
    
    
    
    
    
    
    
    

}
