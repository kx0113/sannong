<?php  

date_default_timezone_set('PRC');//设置时区
ignore_user_abort(); // 忽视用户行为，后台运行
set_time_limit(0); // 取消脚本运行时间的超时上限

// $time=120;
$url="http://124.133.16.116:8110/Public/check.php";  
// if(!file_exists('kaiguan.txt')){
//     die('程序终止');
// }
$con = mysql_connect("localhost","root","root");
mysql_select_db("sannong", $con);
$dd = date('Y-m-d H:i:s');
$sql = "INSERT INTO sn_test (stime) VALUES ('".$dd."')";
mysql_query($sql); 

checkStatus(0);
checkStatus(2);
checkStatus(3);
checkStatus(4);

$con = @mysql_connect("localhost","sannong","sannong");
@mysql_select_db("sannong", $con);
$dd = date('Y-m-d H:i:s');
$sql = "INSERT INTO sn_live_log (stime) VALUES ('".$dd."')";
@mysql_query($sql);

// sleep($time);
// file_get_contents($url);
function checkStatus($number){
    $sig = array();
    $sig['Action'] = 'DescribeLVBChannelList';           //方法名
    $sig['Region'] = 'bj';                //签名实例所在区域
    $sig['SecretId'] = 'AKIDwkQvKfevlfRDqGnaVEl06065tfJsuhCY';            //签名秘钥Id
    $secretKey = 'JQLrHYjomaCcqshYr5B6pSuGVcXcbP1W';          //签名秘钥Key
    $sig['Timestamp'] = time();                    //签名当前时间戳
    $sig['Nonce'] = rand(10000,99999);
    $sig['channelStatus'] = $number;
    ksort($sig);
    $qingqiu = http_build_query($sig);
    $yuanwen = 'GETlive.api.qcloud.com/v2/index.php'.'?'.$qingqiu;
    $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
    $signature = urlencode($signStr);
    $requestUrl = 'https://live.api.qcloud.com/v2/index.php'.'?'.$qingqiu.'&Signature='.$signature;
    $check_res = curl($requestUrl, "", false, "GET");
    $check_res = json_decode($check_res);
    if ($check_res->code ==0){
        $num = $check_res->all_count;
        if ($num){
            $arr = $check_res->channelSet;
            $host = 'localhost';
            $dbname = 'sannong';
            $unm = 'sannong';
            $pwd = 'sannong';
            foreach ($arr as $item){
                $where = array();
                $where['channel_id'] = $item->channel_id;
                $where['status'] = 1;
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $unm, $pwd);
                $sql = "select * from sn_expertlive where channel_id = ? AND status = ?";
                $stmt = $pdo->prepare($sql);
                $rs = $stmt->execute(array($where['channel_id'],$where['status']));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                //$info = M('expertlive')->where($where)->select();
                if (!empty($row)){
                    $data['status'] = 2;
                    $where1 = array();
                    $where1['channel_id'] = $where['channel_id'];
                    $sql1 = "UPDATE sn_expertlive SET status=2 WHERE channel_id = ?";
                    $stmt = $pdo->prepare($sql1);
                    $rs = $stmt->execute(array($where['channel_id']));

                    //$res = M('expertlive')->where($where1)->save($data);
                }
                stopOnline($where['channel_id']);
            }
        }
    }else{
        return false;
    }
}

function stopOnline($channel_id){
    $ss = array();
    $ss['Action'] = 'DescribeLVBChannelList';           //方法名
    $ss['Region'] = 'bj';                //签名实例所在区域
    $ss['SecretId'] = 'AKIDwkQvKfevlfRDqGnaVEl06065tfJsuhCY';            //签名秘钥Id
    $secretKey = 'JQLrHYjomaCcqshYr5B6pSuGVcXcbP1W';          //签名秘钥Key
    $ss['Timestamp'] = time();                    //签名当前时间戳
    $ss['Nonce'] = rand(10000,99999);
    //$sig['channelIds'] = array()$channel_id;
    ksort($ss);
    $qingqiu = http_build_query($ss).'&channelIds.1='.$channel_id;
    $yuanwen = 'GETlive.api.qcloud.com/v2/index.php'.'?'.$qingqiu;
    $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
    $signature = urlencode($signStr);
    $requestUrl = 'https://live.api.qcloud.com/v2/index.php'.'?'.$qingqiu.'&Signature='.$signature;
    $stop_res = curl($requestUrl, "", false, "GET");
    //以下为删除该频道
    $cn = array();
    $cn['Action'] = 'DeleteLVBChannel';           //方法名
    $cn['Region'] = 'bj';                //签名实例所在区域
    $cn['SecretId'] = 'AKIDwkQvKfevlfRDqGnaVEl06065tfJsuhCY';            //签名秘钥Id
    $cn['Timestamp'] = time();                    //签名当前时间戳
    $cn['Nonce'] = rand(10000,99999);
    //$cn['channelIds'] = $channel_id;
    ksort($cn);
    $qingqiu = http_build_query($cn).'&channelIds.1='.$channel_id;
    $yuanwen = 'GETlive.api.qcloud.com/v2/index.php'.'?'.$qingqiu;
    $signStr = base64_encode(hash_hmac('sha1',$yuanwen,$secretKey,true));
    $signature = urlencode($signStr);
    $requestUrl = 'https://live.api.qcloud.com/v2/index.php?'.$qingqiu.'&Signature='.$signature;
    $del_res = curl($requestUrl, "", false, "GET");
    $del_res = json_decode($del_res);
    $mes = $del->code;
    //return $mes;
}

function curl($url, $data, $header = false, $method = "POST")
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
  
?> 