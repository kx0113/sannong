<?php

date_default_timezone_set('PRC');//设置时区
ignore_user_abort(); // 忽视用户行为，后台运行
set_time_limit(0); // 取消脚本运行时间的超时上限

    $config = array('host'=>'127.0.0.1','user'=>'sannong','pwd'=>'sannong','db'=>'sannong');
    $con = @mysql_connect($config['host'],$config['user'],$config['pwd']);
    @mysql_select_db($config['db'], $con);
    $sql2 = 'SELECT * FROM sn_jgqs_cates WHERE is_display=1';
    $query1 = 'set character_set_connection=utf8,character_set_results=utf8,character_set_client=binary';
    @mysql_query($query1);
    $tmp = @mysql_query($sql2);
    while($every=mysql_fetch_array($tmp,MYSQL_ASSOC)) {
        $res[]=$every;
    }
//    print_r($res);
    
    $day = date("Y-m-d",strtotime("-1 day"));
    foreach ($res as $key=>$val){
        $statics = array();
        $mes = array();
        $data = array();
        $statics = getstatics($day,$val['from_cate'],$val['from_product']);
        if (!empty($statics)){
            $mes = $statics[1];
            $data['price'] = $mes[1];
        }else{
            $data['price'] = 0;
        }
        $data['cate_name'] = $val['name'];
        $data['adddate'] = $day;
        $data['cate_id'] = $val['id'];
        $data['dept_id'] = $val['dept_id'];
        $sql = "INSERT INTO sn_jgqs (dept_id,cate_id,cate_name,price,adddate) VALUES ({$data['dept_id']},{$data['cate_id']},'{$data['cate_name']}',{$data['price']},'{$data['adddate']}')";
        $result = mysql_query($sql);
    }
    
    
    //获取对应产品数据
    function getstatics($day,$cate_id,$product_id){
        $url = "http://nc.mofcom.gov.cn/channel/gxdj/jghq/jg_list.shtml?par_craft_index=".$cate_id."&craft_index=".$product_id."&startTime=".$day."&endTime=".$day."&par_p_index=11&p_index=20531";
        $contents =  file_get_contents("compress.zlib://".$url);

//        $output = iconv('GB2312', 'UTF-8', $contents);
        preg_match_all('/<td>([^<]*)<\/td>/i', $contents, $matches);
        
        return $matches;
    }
    