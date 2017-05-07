<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22
 * Time: 15:01
 */


class curlRequest {

    public function get($url,$data){
        //初始化
        $curl = curl_init();
        /*$header = array(
            'Content-Type:application/x-www-form-urlencoded',
            'apikey: e3r4542343ewsd188',
        );*/
        // 添加apikey到header
//        curl_setopt($curl, CURLOPT_HTTPHEADER  , $header);
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url.'?'.$data);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }

    public function sendRequest($url, $data, $method){
//        $data_string = json_encode($data);
        $header = array(
            'Content-Type: application/json',
            //'Content-Length: ' . strlen($data)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                  //设置头信息的地方
//        curl_setopt($ch, CURLOPT_HEADER, 1);                            //是否返回头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                    //返回字串，而不是直接在浏览器显示

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
}