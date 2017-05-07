<?php
namespace Org\Huanxin;
class Huanxin
{

    private $app_key = 'yiguanli#yiguanli';
    private $client_id = 'YXA6ldJGUH4JEeayf4NV__adXA';
    private $client_secret = 'YXA6EljTTfLW0B8boHyEYPqwU3XD4d8';
    private $url = "https://a1.easemob.com/yiguanli/yiguanli";
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
    
    /*
     * 删除IM用户[单个]
     */
    public function hx_user_delete($username)
    {
        $url = $this->url . "/users/${username}";
        $header = array(
            'Authorization: Bearer ' . $this->token
        );
        return $this->curl($url, "", $header, "DELETE");
    }
    //检查群主
    public function hx_check($qid){
        $url = $this->url . "/chatgroups/".$qid;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        return $this->curl($url, "", $header, "GET");
    }
    
    //添加群
    public function addGroup($groupName,$groupDesc,$public,$maxuser,$approval,$owner,$members){
        $url = $this->url . "/chatgroups/";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        if (empty($members)){
            $data = array(
                'groupname' => $groupName,
                'desc' => $groupDesc,
                'public' => $public,
                'maxusers' => $maxuser,
                'approval' => $approval,
                'owner' => $owner,
            );
        }else{
            $data = array(
                'groupname' => $groupName,
                'desc' => $groupDesc,
                'public' => $public,
                'maxusers' => $maxuser,
                'approval' => $approval,
                'owner' => $owner,
                'members' => $members
            );
        }
        
        return $this->curl($url, $data, $header, "POST");
    }
    
    //获取APP下所有群组
    public function listGroup(){
        $url = $this->url . "/chatgroups";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        /* $data = array(
          'params' =>array('limit'=>10)  
        ); */
        return $this->curl($url, $data, $header, "GET");
    }
    
    //删除某个群组
    public function delGroup($groupid){
        $url = $this->url . "/chatgroups/".$groupid;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        $data = array();
        return $this->curl($url, $data, $header, "DELETE");
    }
    
    //获取某个群组下的所有成员
    public function memberGroup($groupid){
        $url = $this->url . "/chatgroups/".$groupid.'/users';
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        $data = array();
        return $this->curl($url, $data, $header, "GET");
    }
    
    //发送群消息
    public function messageGroup($groupid,$message,$user){
        $url = $this->url . "/messages";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        $data = array(
            'target_type' => 'chatgroups',
            "target" => array($groupid),
            'msg' => array('type'=>'txt','msg'=>$message),
            'from' => $user
        );
        return $this->curl($url, $data, $header, "POST");
    }
    
    //获取某个群的详情
    public function detailGroup($groupid){
        $url = $this->url . "/chatgroups/".$groupid;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        $data = array();
        return $this->curl($url, $data, $header, "GET");
    }
    
    //修改某个群的属性
    public function editGroup($groupid,$groupname,$description,$maxusers){
        $url = $this->url . "/chatgroups/".$groupid;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        $data = array(
            'groupname' => $groupname,
            "description" => $description,
            'maxusers' => $maxusers
        );
        return $this->curl($url, $data, $header, "PUT");
    }
    
    //批量删除群内成员
    public function delGroupMem($groupid,$who){
        $url = $this->url . "/chatgroups/".$groupid.'/users/'.$who;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        $data = array();
        return $this->curl($url, $data, $header, "DELETE");
    }
    
    //批量添加群成员
    public function addGroupMem($groupid,$users){
        $url = $this->url . "/chatgroups/".$groupid.'/users';
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
        
        $data = array(
            'usernames'=>$users
        );
        return $this->curl($url, $data, $header, "POST");
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