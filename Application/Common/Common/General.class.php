<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 17:05
 */

namespace Common\Common;
use Common\Model;

class General {

    //错误信息
    public function error($code){
        $errMsg = array(
            1 => '请输入账号和密码',
            2 => '手机号码错误',
            3 => '账号或密码错误',
            4 => '手机号码不一致',
            5 => '短信验证码错误',
            6 => '非法请求',
            7 => '手机号、密码及短信验证码为必填字段',
            8 => '注册失败，请联系管理员',
            9 => '该用户不存在',
            10 => '密码不正确',
            11 => '该用户已存在',
            12 => '请输入手机号及密码',
            13 => '缺少部门参数',
            14 => '缺少账号参数',
            15 => '缺少必要参数Token',
            16 => 'token校验失败,请重新登录',
            17 => '缺少必要参数session_id',
            18 => '请填写新密码',
            19 => '请先验证手机',
            20 => '请先获取短信验证码',
            21 => '修改密码失败',
            22 => '请选择监控类别',
            23 => '暂无监控数据',
            24 => '缺少设备ID',
            25 => '请选择日期',
            26 => '该手机号已存在',
            27 => '修改失败',
            28 => '用户类型参数错误',
            50 => '部门代码错误或暂无文章',
            51 => '分类代码错误或暂无文章',
            52 => '类型或分类数据丢失',
            53 => '标题、简介、联系人姓名、电话、过期时间均不能为空',
            54 => '请上传至少一张图片',
            55 => '添加信息失败，请稍后重试',
            56 => '暂无分类',
            57 => '该分类下暂无数据',
            58 => '手机号不能为空',
            59 => '用户姓名不能为空',
            60 => '车牌号不能为空',
            61 => '车辆类型不能为空',
            62 => '定位信息不能为空',
            63 => 'salt不能为空',
            64 => '未找到该专家',
            65 => '未找到相关信息',
            66 => '部门代码错误',
            67 => '缺少必要参数专家id',
            68 => '缺少用户手机号',
            69 => '评价失败，请重试',
            70 => '缺少评分',
            71 => '缺少项目pid',
            72 => '找不到该项目或已删除',
            73 => '未找到该部门',
            74 => '缺少视频播放参数',
            75 => '未找到该直播视频',
            76 => '标题不能为空',
            77 => 'URL不能为空',
            78 => '直播失败,请重试',
            79 => '暂无直播',
            80 => '缺少直播id编号',
            81 => '未找到该直播或已暂停',
            82 => '您还未上传直播展示图片，请前往个人中心上传',
            83 => '缺少账号相关信息',
            84 => '您已经上传过一张了',
            85 => '请上传图片',
            86 => '您已经是专家了',
            87 => '未找到该部门或未找到该部门下的维护者',
            88 => '缺少群id',
            89 => '暂无权限删除',
            90 => '删除失败',
            91 => '您不是群主',
            92 => '缺少群主id',
            93 => '没有新用户可以添加该权限',
            94 => '您没有发布群公告的权限',
            95 => '缺少操作对象',
            96 => '未找到该权限人',
            97 => '您不是专家',
            98 => '直播请求失败，请稍后重试',
            99 => '直播请求失败，请稍后重试',
            100 => '直播功能暂时只支持同时开放5个频道，请联系管理员协调直播时间',
            101 => '缺少直播频道号',
            102 => '该频道已停止',
            103 => '停止失败…',
            104 => '未找到相关地理信息详情',
            105 => '该手机2分钟以内发送过短信，请等待',
            106 => '没有上传机主IM互动账号',
            107 => '缺少农机种类',
            108 => '无培训信息',
            109 => '缺少培训信息id',
            110 => '您已订阅',
            111 => '缺少预警及账号相关参数',
            112 => '缺少可下载文件',
            113 => '缺少课件id',
            114 => '没有该文件文件',
            115 => 'im账号格式不正确',
            116 => 'im账号已注册过，请正确填写',
            117 => '该发动机号已经注册过',
            118 => '您尚未注册环信或发生其他错误',
            119 => '缺少聊天室信息'
        );
        $return = array('error' => $code, 'data' => array(), 'msg' => $errMsg[$code]);
        exit(json_encode($return));
    }


    //数据返回
    public function returnData($data = array(), $msg = 'success'){
        $return = array('error' => 0, 'data' => $data, 'msg' => $msg);
        exit(json_encode($return));
    }

    //避免乱码的数据返回
    /* public function zwData($data = array(), $msg = 'success'){
        $return = array('error' => 0, 'data' => $data, 'msg' => $msg);
        $res = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($data));
        exit($res);
    } */
    
    //生成随机码
    public function randNum(){
        return rand(100000, 999999);
    }


    //短信验证码设置
    public function authNum($mobile){
        $authnum = $this->randNum();
        session_start();
   //     $authnum = '123456';
  /*      $authnum = '123456';
         session_start();
        $_SESSION['auth'] = array('authnum' => $authnum, 'mobile' => $mobile); */
        //检查该手机上次使用短信的时间
        $where = array();
        $where['mobile'] = $mobile;
        $before_time = M('mes_log')->where($where)->select();
        if (!empty($before_time)){
            $onCheck = $before_time[0]['usetime'];
            $nowCheck = time();
            if ($nowCheck - $onCheck <120){
                $this->error(105);
            }
        }
        
        $url = 'http://manager.wxtxsms.cn/smsport/sendPost.aspx';
        $post_data = array();
        $timeout = 5;
        $post_data['uid'] = 'jnht';
        $post_data['upsd'] = md5('jnht@1');
        $post_data['sendtele'] = $mobile;
        $post_data['msg'] = '欢迎注册智慧三农手机客户端，您的验证码为 '.$authnum.' 有效期为15分钟';
        $post_data['sign'] = '网信科技';
        self::makePost($url,$post_data,$timeout);
        //session(array('expire' => 900));

        $ckInfo = M('mes_log')->where($where)->select();
        $data = array();
        if (empty($ckInfo)){
            $data['mobile'] = $mobile;
            $data['usetime'] = time();
            $data['times'] = 1;
            $res = M('mes_log')->add($data);
        }else{
            $data['mobile'] = $mobile;
            $data['usetime'] = time();
            $data['times'] = $ckInfo[0]['times']+1;
            $res = M('mes_log')->where($where)->save($data);
        }
        
        $_SESSION['auth'] = array('authnum' => $authnum, 'mobile' => $mobile);
        return array('session_id' => session_id());
    }

    //post请求
    public static function makePost($url, $post_data = '', $timeout = 5){//curl
    
        $ch = curl_init();
    
        curl_setopt ($ch, CURLOPT_URL, $url);
    
        curl_setopt ($ch, CURLOPT_POST, 1);
    
        if($post_data != ''){
    
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    
        }
    
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    
        curl_setopt($ch, CURLOPT_HEADER, false);
    
        $file_contents = curl_exec($ch);
    
        curl_close($ch);
    
        return $file_contents;
    
    }
    
    //验证手机号
    public function isMobile($mobile){
        return preg_match('/^1[3578]\d{9}$/', $mobile);
    }

    //生成密码
    public function makePassword($password, $num){
        return md5($password.$num);
    }

    //生成token
    public function makeToken($account, $md5pass){
        return md5($account.$md5pass.'xyz');
    }

    //校验易管理用户token
    public function checkToken($account, $token){
        $user = D('YglUser')->getUser(array('account' => $account));
        if(empty($user)){
            return false;
        }else{
            $md5pass = $user[0]['password'];
            if($this->makeToken($account, $md5pass) == $token){
                return true;
            }else{
                return false;
            }
        }
    }

    //校验易家家用户token
    public function yjjCheckToken($account, $token){
        $type = trim(@$_POST['type']);
        if($type == 'user'){
            $user = D('YjjUser')->getUser(array('uname' => $account));
        }else if($type == 'expert'){
            $user = D('Expert')->getUser(array('account' => $account));
        }else{
            $this->error(28);
        }
        if(empty($user)){
            return false;
        }else{
            $md5pass = $user[0]['password'];
            if($this->makeToken($account, $md5pass) == $token){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    function listToTree($list, $pk = 'cid', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        
        return $tree;
    }

    //验证手机短信码
 /*    public function checkAuthnum($mobile,$id){
        $session = session_id($id);
        if($session['mobile'] = $mobile){
            
        }
    } */
}