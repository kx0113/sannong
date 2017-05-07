<?php
namespace Yjj\Controller;
use Think\Controller;
use Common\Common\General;

class LiveController extends Controller{
    
    private $appid = 1252950332;
    private $region = 'bj';
    private $secretId = 'AKIDwkQvKfevlfRDqGnaVEl06065tfJsuhCY';
    private $secretKey = 'JQLrHYjomaCcqshYr5B6pSuGVcXcbP1W';
    private $domain = 'live.api.qcloud.com';
    private $road = '/v2/index.php';
    //获取直播推流地址
    public function getUpScream(){
        $general = new General();
        if (IS_POST){
            if (!empty($_POST['account'])){
                $account = trim($_POST['account']);
                $where = array('account'=>$account);
                $data['account'] = $account;
            }else{
                $general->error(14);
            }
            //$account = 15192776736;
            $ckInfo = M('expert')->field('eid,ename,account')->where($where)->select();
            if (empty($ckInfo)){
                $general->error(97);
            }
            if (!empty($_POST['title'])){
                $data['title'] = trim($_POST['title']);
            }else{
                $general->error(76);
            }
            $data['status'] = 1;
            $data['addtime'] = date('Y-m-d H:i:s');
            $data['expert_name'] = $ckInfo[0]['ename'];
            $where1 = array();
            $where1['expert_id'] = $ckInfo[0]['eid'];
            $ckPic = M('expertlive_picture')->field('id,expert_id,picture')->where($where1)->select();
            if (!empty($ckPic)){
                $data['picture'] = $ckPic[0]['picture'];
            }else{
                $general->error(82);
            }
            $num = $this->checkNum();
            if ($num >4){
                $general->error(100);
            }
            $sig = array();
            $sig['Action'] = 'CreateLVBChannel';           //方法名
            $sig['Region'] = $this->region;                //签名实例所在区域
            $sig['SecretId'] = $this->secretId;            //签名秘钥Id
            $secretKey = $this->secretKey;          //签名秘钥Key
            $sig['Timestamp'] = time();                    //签名当前时间戳
            $sig['Nonce'] = rand(10000,99999);             //签名随机数
            $sig['channelName'] = date('YmdHis');                //频道名称
            //$sig['channelDescribe'] = $data['title'];              //直播频道描述
            $sig['outputSourceType'] = 1;                  //输出源选择
            //$sourceList = array('name'=>$title,'type'=>1);//直播源列表
            ksort($sig);
            $qingqiu = http_build_query($sig).'&sourceList.1.name='.date('YmdHis').'&sourceList.1.type=1';
            
            $yuanwen = 'GET'.$this->domain.$this->road.'?'.$qingqiu;
            $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
            $signature = urlencode($signStr);
            $requestUrl = 'https://'.$this->domain.$this->road.'?'.$qingqiu.'&Signature='.$signature;

            $res = $this->curl($requestUrl, "", false, "GET");
            //dump($res);die();
            $obj = json_decode($res);
            if($obj->code == 0){
                $data['scream'] = $obj->channelInfo->upstream_address;
                $data['channel_id'] = $obj->channel_id;
                $down_add = $obj->channelInfo->downstream_address;
                $data['down_address'] = $down_add[0]->flv_downstream_address;
                
                $hx = new \Org\Huanxin\Expert();
                $res_lts = $hx->hx_lts($data['channel_id'],$account);
                $obj_lts = json_decode($res_lts);
                $chatroom_id = $obj_lts->data->id;
                if (empty($chatroom_id)){
                    $general->error(118);
                }else{
                    $data['chatroom_id'] = $chatroom_id;
                }
                if (M('expertlive')->add($data)){
                    $info = array();
                    $info['scream'] = $obj->channelInfo->upstream_address;
                    $info['channel_id'] = $obj->channel_id;
                    $info['chatroom_id'] = $chatroom_id;
                    $general->returnData($info,'success');
                }else{
                    $general->error(99);
                }
            }else{
                $general->error(98);
            }
         }else{
            $general->error(6);
         }
    }
    //检查正在直播的数量
    public function checkNum(){
        $sig = array();
        $sig['Action'] = 'DescribeLVBChannelList';           //方法名
        $sig['Region'] = $this->region;                //签名实例所在区域
        $sig['SecretId'] = $this->secretId;            //签名秘钥Id
        $secretKey = $this->secretKey;          //签名秘钥Key
        $sig['Timestamp'] = time();                    //签名当前时间戳
        $sig['Nonce'] = rand(10000,99999);
        ksort($sig);
        $qingqiu = http_build_query($sig);
        $yuanwen = 'GET'.$this->domain.$this->road.'?'.$qingqiu;
        $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
        $signature = urlencode($signStr);
        $requestUrl = 'https://'.$this->domain.$this->road.'?'.$qingqiu.'&Signature='.$signature;
        $check_res = $this->curl($requestUrl, "", false, "GET");
        $check_res = json_decode($check_res);
        if ($check_res->code ==0){
            $num = $check_res->all_count;
            //dump($check_res);die();
            return $num;
        }else{
            return false;
        }
    }
    
    //停止直播(本地直播列表)
    public function stopUpScream(){
        $general = new General();
        if (!empty($_GET['channel_id'])){
            $where = array();
            $where['channel_id'] = trim($_GET['channel_id']);
        }else{
            $general->error(101);
        }
        if (empty($_GET['chatroom_id'])){
            $general->error(119);
        }else{
            $chatroom_id = $_GET['chatroom_id'];
        }
        $where['status'] = 1;
        $info = M('expertlive')->where($where)->select();
        empty($info) ? $general->error(75) : $info = $info[0];
        $data['status'] = 2;
        $where1 = array();
        $where1['channel_id'] = $where['channel_id'];
        $res = M('expertlive')->where($where1)->save($data);
        if (!empty($res)){
            $mes = $this->stopOnline($where['channel_id']);
            if ($mes ==0){
                $hx = new \Org\Huanxin\Expert();
                $mes_chatroom = $hx->hx_lts_del($chatroom_id);
                
                $general->returnData(array(),'success');
            }else{
                $general->error(103);
            }
        }else{
            $general->error(102);
        }
    }
    
    //停止直播(腾讯云管理平台,先关闭后删除)
    public function stopOnline($channel_id){
        $sig = array();
        $sig['Action'] = 'StopLVBChannel';           //方法名
        $sig['Region'] = $this->region;                //签名实例所在区域
        $sig['SecretId'] = $this->secretId;            //签名秘钥Id
        $secretKey = $this->secretKey;          //签名秘钥Key
        $sig['Timestamp'] = time();                    //签名当前时间戳
        $sig['Nonce'] = rand(10000,99999);
        //$sig['channelIds'] = array()$channel_id;
        ksort($sig);
        $qingqiu = http_build_query($sig).'&channelIds.1='.$channel_id;
        $yuanwen = 'GET'.$this->domain.$this->road.'?'.$qingqiu;
        $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
        $signature = urlencode($signStr);
        $requestUrl = 'https://'.$this->domain.$this->road.'?'.$qingqiu.'&Signature='.$signature;
        $stop_res = $this->curl($requestUrl, "", false, "GET");
        //以下为删除该频道
        $cn = array();
        $cn['Action'] = 'DeleteLVBChannel';           //方法名
        $cn['Region'] = $this->region;                //签名实例所在区域
        $cn['SecretId'] = $this->secretId;            //签名秘钥Id
        $cn['Timestamp'] = time();                    //签名当前时间戳
        $cn['Nonce'] = rand(10000,99999);
        //$cn['channelIds'] = $channel_id;
        ksort($cn);
        $qingqiu = http_build_query($cn).'&channelIds.1='.$channel_id;
        $yuanwen = 'GET'.$this->domain.$this->road.'?'.$qingqiu;
        $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
        $signature = urlencode($signStr);
        $requestUrl = 'https://'.$this->domain.$this->road.'?'.$qingqiu.'&Signature='.$signature;
        $del_res = $this->curl($requestUrl, "", false, "GET");
        $del_res = json_decode($del_res);
        $mes = $del->code;
        return $mes;
    }
    
    //专家直播直播列表
    public function expertLives(){
        $general = new General();
        $where = array();
        $where['status'] = 1;
        $m = M('expertlive');
        $infos = $m->field('id,expert_name,title,down_address,picture,channel_id')->where($where)->order('id desc')->select();
        if (empty($infos)){
            $general->error(79);
        }
        $general->returnData($infos,'success');
    }
    //发送请求
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
    
    //获取专家直播推流地址
    public function  getPushUrl(){
        $general = new General();
        $data = array();
        if (!empty($_POST['account'])){
            $account = trim($_POST['account']);
            $where = array('account'=>$account);
            $data['account'] = $account;
        }else{
            $general->error(14);
        }
        //$account = 15192776736;
        $ckInfo = M('expert')->field('eid,ename,account')->where($where)->select();
        if (empty($ckInfo)){
            $general->error(97);
        }
        if (!empty($_POST['title'])){
            $data['title'] = trim($_POST['title']);
        }else{
            $general->error(76);
        }
        $data['status'] = 1;
        $data['addtime'] = date('Y-m-d H:i:s');
        $data['expert_name'] = $ckInfo[0]['ename'];
        $where1 = array();
        $where1['expert_id'] = $ckInfo[0]['eid'];
        $ckPic = M('expertlive_picture')->field('id,expert_id,picture')->where($where1)->select();
        if (!empty($ckPic)){
            $data['picture'] = $ckPic[0]['picture'];
        }else{
            $general->error(82);
        }
        
        
        $bizid = 5417;
        $key = '557c7dda2241d3d40c345ca623b00ee2';
        $tm = date('Y-m-d H:i:s',strtotime("+3 hour"));
        //dump($tm);die();
        $txTime = strtoupper(base_convert(strtotime($tm),10,16));

        $livecode = $bizid."_".$account; //直播码
        $txSecret = md5($key.$livecode.$txTime);
        $ext_str = "?".http_build_query(array(
        //    "bizid"=> $bizid,
            "txSecret"=> $txSecret,
            "txTime"=> $txTime
        ));
        
        $url = "rtmp://".$bizid.".livepush.myqcloud.com/live/".$livecode.$ext_str;
        $data['url'] = "http://".$bizid.".livepush.myqcloud.com/live/".$livecode.".flv";
        if (M('expertlive')->add($data)){
            $general->returnData($url,'success');
        }else{
            $general->error(78);
        }
        
    }
    
    //推流之后的状态监测
    public function checkStatus(){
        $general = new General();
        $akey = '5bf8ab425fae51bc4a0b4a84ae5b1060';
        
        $c = file_get_contents('php://input');
        $info = json_decode($c, true);
        if ($info['t']<=time()){
            die();
        }
        $ckInfo = md5($akey,$info['t']);
        if ($ckInfo != $info['sign']){
            die();
        }
        
        $account = substr($info['stream_id'], 5);
        $where = array('account'=>$account);
        
        if ($info['event_type'] == 1){
            $where['status'] = 2;
            $zbMes = M('expertlive')->where($where)->select();
            if (!empty($zbMes)){
                M('expertlive')->where($where)->save();
            }
        }elseif ($info['event_type'] == 0){
            $where['status'] = 1;
            $zbMes = M('expertlive')->where($where)->select();
            if (!empty($zbMes)){
                $data['status'] = 2;
                if (M('expertlive')->where($where)->save($data)){
                    
                }
            }
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
