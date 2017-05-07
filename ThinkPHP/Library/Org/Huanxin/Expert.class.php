<?php
namespace Org\Huanxin;
class Expert
{

    private $app_key = 'myh-1992#yijiajia';
    private $client_id = 'YXA6NG48cI0YEeayRUfAsH904A';
    private $client_secret = 'YXA6Ps0DSMx8p76PzEfTTcFj4qMunas';
    private $url = "https://a1.easemob.com/myh-1992/yijiajia";
    /*
     * 获取APP管理员Token
     */
    function __construct()
    {
        $url = $this->url . "/token";
        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        );
        $rs = json_decode($this->curl($url, $data), true);
        $this->token = $rs['access_token'];
        //dump($this->curl($url, $data));die();
    }
    /*
     * 注册IM用户(授权注册)
     */
    public function hx_register($username, $password,$nickname)
    {
        $url = $this->url . "/users";
        $data = array(
            'username' => $username,
            'password' => 123456,
            'nickname' => $nickname
        );
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        return $this->curl($url, $data, $header, "POST");
    }
    
    public function hx_check($qid){
        $url = $this->url . "/chatgroups/".$qid;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        return $this->curl($url, "", $header, "GET");
    }
    
    //创建环信聊天室
    public function hx_lts($channel_id,$account){
        $url = $this->url . "/chatrooms";
        $data = array(
            'name' => $channel_id,
            'description' => $channel_id,
            'maxusers' => 500,
            'owner' => $account
        );
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        return $this->curl($url, $data, $header, "POST");
    }
    //删除环信聊天室
    public function hx_lts_del($chatroom_id){
        $url = $this->url . "/chatrooms/".$chatroom_id;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        return $this->curl($url, $data, $header, "DELETE");
    }
    //curl
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
    
}