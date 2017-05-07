<?php 

date_default_timezone_set('PRC');//设置时区
ignore_user_abort(); // 忽视用户行为，后台运行
set_time_limit(0); // 取消脚本运行时间的超时上限

/*
 * curl发送
 */
function curl($url, $data, $header = false, $method = "POST")
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($header) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    $ret = curl_exec($ch);
    return $ret;
}

/*
 * 定时激光推送易管理
 */
function push(){
    //连接数据库，规定数据格式，查询所有专家直播
    $dblocalhost = '124.133.16.116:3306';
    $dbusername = 'gurundong';
    $dbpassword = '123456';
    $dblink=@mysql_connect($dblocalhost,$dbusername,$dbpassword);
    $query1 = 'set character_set_connection=utf8,character_set_results=utf8,character_set_client=binary';
    $query2 = 'use sannong';
    $query3 = 'select * from sn_traininginfo';
    @mysql_query($query1,$dblink);
    @mysql_query($query2,$dblink);
    $sql = mysql_query($query3,$dblink);
    while($res=mysql_fetch_array($sql,MYSQL_ASSOC)) {
        $temp[]=$res;
    }
    //train_id为专家直播号，根据时间判断即将开始的直播号
    $train_id = array();
    foreach ($temp as $k=>$v){
        $a = 0;
        $time1 = 0;
        $time = 0;
        $time = time();
        $time1 = strtotime($temp[$k]['stime']);
        $a = $time1-$time;
        if($a <= 3600 && $a>0){
            $train_id[] = $v['id'];
        }
    }
    $count = count($train_id);
    //有将要开始的直播，进行用户激光推送
    if(!empty($train_id)){
                    $AppKey = 'e908866884320e67a2b4605f';
                    $MSecret = '2ac79289175f3fc164570bcd';
                    $str = $AppKey.':'.$MSecret;
                    $url = 'https://api.jpush.cn/v3/push';
                    $auth = base64_encode($str);
                    $header =  array(
                        'Content-Type: application/json',
                        'Authorization: Basic '.$auth
                    );
                //每一次循环，推送一个专家直播号提醒
                for ($i=0;$i<$count;$i++){
                    $query = '';
                    $query2 = '';
                    $title = '';
                    $res = array();
                    $push_info = array();
                    $account = array();
                    $query = "select account,title,status from sn_training_push where trainid = $train_id[$i] and software = 1";
                    $sql = @mysql_query($query,$dblink);
                    while($res=mysql_fetch_array($sql,MYSQL_ASSOC)) {
                        $push_info[]=$res;
                    }
                    //如果推送者不为空，则进行推送，为空进行下一个专家直播推送
                    if(empty($push_info)){
                        continue;
                    }else{
                        //推送内容title
                        $title = $push_info[0]['title'];
                        foreach($push_info as $k1=>$v1){
                            $account[$k1] = $push_info[$k1]['account'];
                        }
                        if($push_info[$k1]['status'] == 1){
                            continue;
                        }else{
                            //修改专家直播推送状态，每个直播只推送一次
                            $query2 = "update sn_training_push set status = 1 where trainid = $train_id[$i] and software = 1";
                            mysql_query($query2,$dblink);
                            $count_account = '';
                            $count_account = count($account);
                            //获取推送账号，每次推送最多为3个，循环推送完成
                            for($j = 0;$j<$count_account;$j = $j+3){
                                $target = array();
                                if(isset($account[$j+2])){
                                    $target[0] = $account[$j];
                                    $target[1] = $account[$j+1];
                                    $target[2] = $account[$j+2];
                                }elseif(isset($account[$j+1])){
                                    $target[0] = $account[$j];
                                    $target[1] = $account[$j+1];
                                }else{
                                    $target[0] = $account[$j];
                                }
                                $mes = array(
                                    'platform' => 'android',
                                    'audience' => array('alias'=>$target),
                                    'notification' => array('alert' =>$title)
                                );
                                $mes = json_encode($mes);
                                //推送发送
                                curl($url, $mes, $header, "POST");
                            }
                        }
                    }
                }
        mysql_close($dblink);
    }else{
        return 0;
    }
}

/*
 * 定时激光推送易家家
 */
function push2(){
    //连接数据库，规定数据格式，查询所有专家直播
    $dblocalhost = '124.133.16.116:3306';
    $dbusername = 'gurundong';
    $dbpassword = '123456';
    $dblink=@mysql_connect($dblocalhost,$dbusername,$dbpassword);
    $query1 = 'set character_set_connection=utf8,character_set_results=utf8,character_set_client=binary';
    $query2 = 'use sannong';
    $query3 = 'select * from sn_traininginfo';
    @mysql_query($query1,$dblink);
    @mysql_query($query2,$dblink);
    $sql = mysql_query($query3,$dblink);
    while($res=mysql_fetch_array($sql,MYSQL_ASSOC)) {
        $temp[]=$res;
    }
    //train_id为专家直播号，根据时间判断即将开始的直播号
    $train_id = array();
    foreach ($temp as $k=>$v){
        $a = 0;
        $time1 = 0;
        $time = 0;
        $time = time();
        $time1 = strtotime($temp[$k]['stime']);
        $a = $time1-$time;
        if($a <= 3600 && $a>0){
            $train_id[] = $v['id'];
        }
    }
    $count = count($train_id);
    //有将要开始的直播，进行用户激光推送
    if(!empty($train_id)){
        $AppKey = 'e908866884320e67a2b4605f';
        $MSecret = '2ac79289175f3fc164570bcd';
        $str = $AppKey.':'.$MSecret;
        $url = 'https://api.jpush.cn/v3/push';
        $auth = base64_encode($str);
        $header =  array(
            'Content-Type: application/json',
            'Authorization: Basic '.$auth
        );
        //每一次循环，推送一个专家直播号提醒
        for ($i=0;$i<$count;$i++){
            $query = '';
            $query2 = '';
            $title = '';
            $res = array();
            $push_info = array();
            $account = array();
            $query = "select account,title,status from sn_training_push where trainid = $train_id[$i] and software = 2";
            $sql = @mysql_query($query,$dblink);
            while($res=mysql_fetch_array($sql,MYSQL_ASSOC)) {
                $push_info[]=$res;
            }
            //如果推送者不为空，则进行推送，为空进行下一个专家直播推送
            if(empty($push_info)){
                continue;
            }else{
                //推送内容title
                $title = $push_info[0]['title'];
                foreach($push_info as $k1=>$v1){
                    $account[$k1] = $push_info[$k1]['account'];
                }
                if($push_info[$k1]['status'] == 1){
                    continue;
                }else{
                    //修改专家直播推送状态，每个直播只推送一次
                    $query2 = "update sn_training_push set status = 1 where trainid = $train_id[$i] and software = 2";
                   //echo $query2;exit;
                    mysql_query($query2,$dblink);
                    $count_account = '';
                    $count_account = count($account);
                    //获取推送账号，每次推送最多为3个，循环推送完成
                    for($j = 0;$j<$count_account;$j = $j+3){
                        $target = array();
                        if(isset($account[$j+2])){
                            $target[0] = $account[$j];
                            $target[1] = $account[$j+1];
                            $target[2] = $account[$j+2];
                        }elseif(isset($account[$j+1])){
                            $target[0] = $account[$j];
                            $target[1] = $account[$j+1];
                        }else{
                            $target[0] = $account[$j];
                        }
                        $mes = array(
                            'platform' => 'android',
                            'audience' => array('alias'=>$target),
                            'notification' => array('alert' =>$title)
                        );
                        $mes = json_encode($mes);
                        //推送发送
                        curl($url, $mes, $header, "POST");
                    }
                }
            }
        }
        mysql_close($dblink);
    }else{
        return 0;
    }
}

//调用激光推送
push();
push2();

exit;


?>