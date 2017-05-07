<!DOCTYPE html>
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
    <style>
        .fl{float:left; margin-right:20px;}
    </style>
</head>
<body>
<div class="fl">
    <a href="?code=getAuthnum">获取验证码</a><br />
    <a href="?code=login">易管理用户登陆</a><br />
    <a href="?code=yjjRegist">易家家用户注册</a><br />
    <a href="?code=yjj_login">易家家用户登陆</a><br />
    <a href="?code=checkMobile">修改密码验证手机</a><br />
    <a href="?code=changePass">修改密码接口</a><br />
    <a href="?code=changeMobile">易家家修改手机号</a><br />
</div>
<div class="fl">
    <a href="?code=getDept">获取部门列表</a><br />
    <a href="?code=getMenu">获取部门菜单</a><br />
    <a href="?code=getInfo">获取部门文章列表</a><br />
    <a href="?code=getPro">获取项目列表</a><br />
</div>
<div class="fl">
    <a href="?code=getArea">获取地区</a><br />
    <a href="?code=getDomain">获取领域</a><br />
    <a href="?code=getExpert">获取专家列表</a><br />
    <a href="?code=searchFriend">搜素好友</a><br />
    
</div>

<div class="fl">
    <a href="?code=wzc_getList">获取问政策列表</a><br />
    <a href="?code=xqw_getList">获取想去玩列表</a><br />
    <a href="?code=businessList">获取买卖信息列表</a><br />
    <a href="?code=businessAdd">买卖信息添加</a><br />
</div>
<div class="fl">
    <a href="?code=traininfo">易家家培训信息</a><br />
    <a href="?code=getvideos">易家家视频课件</a><br />
    <a href="?code=getlives">获取直播列表</a><br />
</div>

<div class="fl">
    <a href="?code=zxList">易家家资讯列表</a><br />
    <a href="?code=ddnjList">易家家滴滴农机列表</a><br />
    <a href="?code=addNjUser">易家家增加滴滴农机用户</a><br />
</div>

<div class="fl">
    <a href="?code=yqgl_tjbb">易管理舆论报表</a><br />
    <a href="?code=expert_comment">易家家专家评论提交</a><br />
    <a href="?code=getChat">易家家获取部门聊天人账号</a><br />
</div>

<div class="fl">
    <a href="?code=iot">物联网数据</a><br />
    <a href="?code=getdatabydeviceid">获取监测详情</a><br />
    <a href="?code=wantLive">专家要直播</a><br />
    <a href="?code=expertLives">专家直播列表</a><br />
    <a href="?code=giveDomain">获取领域</a><br />
</div>
<div class="fl">
    <a href="?code=notice">公告列表</a><br />
    <a href="?code=noticeNew">公告最新三条</a><br />
    <a href="?code=getExpertReportForms">专家服务统计报表</a><br />
    <a href="?code=workreport">获取工作汇报部门列表-级</a><br />
    <a href="?code=deptreportList">获取工作汇报列表二级</a><br />
</div>

<Div style="clear:both;"></Div>
<?php
require_once('./Application/Common/Common/CurlRequest.class.php');

$cr = new curlRequest();
$uri = 'http://sn.local/';
$code = '';
if(!empty($_GET['code'])){
    $code = $_GET['code'];
}
switch ($code) {
    case 'getAuthnum':
        getAuthnum();
        break;
    case 'login':
        yglLogin();
        break;
    case 'yjj_login':
        yjjLogin();
        break;
    case 'yjjRegist':
        yjjRegist();
        break;
    case 'getDept':
        getDept();
        break;
    case 'getMenu':
        getMenu();
        break;
    case 'getInfo':
        getInfo();
        break;
    case 'getArea':
        getArea();
        break;
    case 'getDomain':
        getDomain();
        break;
    case 'getExpert':
        getExpert();
        break;
    case 'getPro':
        getPro();
        break;
    case 'checkMobile':
        checkMobile();
        break;
    case 'changePass':
        changePass();
        break;

    case 'wzc_getList':
        wzc_getList();
        break;
    case 'xqw_getList':
        xqw_getList();
        break;
    case 'businessList':
        businessList();
        break;
    case 'businessAdd':
        businessAdd();
        break;
    case 'traininfo':
        traininfo();
        break;
    case 'getvideos':
        getvideo();
        break;
    case 'getlives':
        getlive();
        break;

    case 'zxList':
        zxList();
        break;

    case 'iot':
        iot();
        break;
    case 'getdatabydeviceid':
        getdatabydeviceid();
        break;
    case 'ddnjList':
        ddnjList();
        break;
    case 'addNjUser':
        addNjUser();
        break;
    case 'notice':
        notice();
        break;
    case 'noticeNew':
        noticeNew();
        break;

    case 'yqgl_tjbb':
        yqgl_tjbb();
        break;
    case 'expert_comment':
        expert_comment();
        break;
    case 'getExpertReportForms':
        getExpertReportForms();
        break;
    case 'workreport':
        workreport();
        break;
    case 'deptreportList':
        deptreportList();
        break;
    case 'wantLive':
        wantLive();
        break;
    case 'expertLives':
        expertLives();
        break;
    case 'changeMobile':
        changeMobile();
        break;
    case 'giveDomain':
        giveDomain();
        break;
    case 'getChat':
        getChat();
        break;
}

function getAuthnum(){
    global $uri, $cr;
    $url = $uri.'Yjj/Public/authNum';
    $data = 'mobile=15192776736';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function yglLogin(){
    global $uri, $cr;
    $url = $uri . 'Ygl/Public/login';
    $data = 'account=15192776736&password=123456';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url . '?' . $data . '<br/>';
    var_dump(json_decode($rel));
    echo '<br/>' . $rel;
}

function yjjLogin(){
    global $uri, $cr;
    $url = $uri.'Yjj/Public/login';
    $data = 'account=15588882222&password=123456&type=expert';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function yjjRegist(){
    global $uri, $cr;
    $url = $uri.'Yjj/Public/register';
    $data = 'mobile=15588882222&password=123456id=session_id&authnum=123456';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getDept(){
    global $uri, $cr;
    $url = $uri.'Ygl/Public/getDept';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getMenu(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/getMenu';
    $data = 'account=15192776736&token=aa038c966754dfa8e96671f231df6b11&dept=0';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getInfo(){
    global $uri, $cr;
    $url = $uri.'Ygl/dept/getInfo';
    $data = 'account=15192776736&token=c2a0c4461a5f44e7ec890fa1e8c03942&dept=0&menu=3';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getArea(){
    global $uri, $cr;
    $url = $uri.'Ygl/Public/getArea';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getDomain(){
    global $uri, $cr;
    $url = $uri.'Ygl/Public/getDomain';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getExpert(){
    global $uri, $cr;
    $url = $uri.'Ygl/Public/getExpert';
    $data = 'domain=38&area=山东&level=1&sevnum=0&ename=名';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getPro(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/getPro';
    $data = 'account=15192776736&token=aa038c966754dfa8e96671f231df6b11&dept=0&status=1&stime=2016-10&ptitle=项目';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function checkMobile(){
    global $uri, $cr;
    $url = $uri.'Yjj/Public/checkMobile';
    $data = 'session_id=nl7vmlk5t38ppoeofokhd7h8e5&mobile=15192776736&authnum=123456&type=1';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function changePass(){
    global $uri, $cr;
    $url = $uri.'Yjj/Public/changePass';
    $data = 'session_id=nl7vmlk5t38ppoeofokhd7h8e5&pass=123456&type=1';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function wzc_getList(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/wzc_getList';
    $data = 'dept_id=6';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function xqw_getList(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/xqw_getList';
    $data = 'cate_id=1';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function businessList(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/businessList';
    $data = 'type=2&cate_id=1';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function businessAdd(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/businessAdd';
    $data = 'cate_id=1&type=2&username=jack&title=我要买东西&introduce=我是来买东西的&tel=15566662222&expire_date=2016-11-15&content=5566444';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function traininfo(){
    global $uri, $cr;
    $url = $uri.'Yjj/Article/traininfo';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function getvideo(){
    global $uri, $cr;
    $url = $uri.'Yjj/Article/getVideos';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}


function getlive(){
    global $uri, $cr;
    $url = $uri.'Yjj/Article/getLives';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}


function zxList(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/zxList';
    $data = 'cate_id=1';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function iot(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/iot';
    $data = 'account=15192776736&token=c2a0c4461a5f44e7ec890fa1e8c03942&dept=5&type=2';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}


function getdatabydeviceid(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/getDataByDevice';
    $data = 'account=15192776736&token=c2a0c4461a5f44e7ec890fa1e8c03942&deviceid=75,00,11,85,10,09,16,20&_date=2016-10-22';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function ddnjList(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/ddnjList';
    $data = 'cate_id=2';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function addNjUser(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/addNjUser';
    $data = 'tel=15192776736&username=bale&cate_id=4&number_plate=鲁A66666&sex=1&longitude=115.33268&latitude=36.22568';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function yqgl_tjbb(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/yqgl_tjbb';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function expert_comment(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/expert_comment';
    $data = 'uid=1&uname=bale&comment=很好，很专业&score=5&expert_id=1';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}


function notice(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/notice';
    $data = 'type=1';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function noticeNew(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/noticeNew';
    $data = 'type=1';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function getExpertReportForms(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/getExpertReportForms';
    $data = 'account=15192776736&token=c2a0c4461a5f44e7ec890fa1e8c03942&page=1&sort=1';
    //$rel = $cr->get($url, $data);
   $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function workreport(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/workreport';
    $data = 'account=15192776736&token=c2a0c4461a5f44e7ec890fa1e8c03942&uid=1';
    //$rel = $cr->get($url, $data);
   $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function deptreportList(){
    global $uri, $cr;
    $url = $uri.'Ygl/Dept/deptreportList';
    $data = 'account=15192776736&token=c2a0c4461a5f44e7ec890fa1e8c03942&uid=1&dept_id=1';
    //$rel = $cr->get($url, $data);
   $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function wantLive(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/wantLive';
    $data = 'account=15192776736&title=防虫治理培训&url=http://www.baidu.com/s?code=122';
    //$rel = $cr->get($url, $data);
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function expertLives(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/expertLives';
    $data = '';
    //$rel = $cr->get($url, $data);
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}

function changeMobile(){
    global $uri, $cr;
    $url = $uri.'Yjj/User/changeMobile';
    $data = 'account=13606309514&token=db2bf223bc311a1c6132c73467e06139&type=user&newmob=13606309511';
    $rel = $cr->sendRequest($url, $data, 'POST');
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function giveDomain(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/giveDomain';
    $data = '';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
function getChat(){
    global $uri, $cr;
    $url = $uri.'Yjj/Yingyong/getChat';
    $data = 'did=5';
    $rel = $cr->get($url, $data);
    echo '<pre>';
    echo $url.'?'.$data.'<br/>';
    var_dump(json_decode($rel));
    echo '<br/>'.$rel;
}
?>
</body>
<script>
    /*$('#yanzhengma').click(function(){
        $.ajax({
            type:"POST",
            url:"http://localhost/cjyijia/public/api/?service=User.GetAuthnum",
            data:{mobile:"15192776736"},
            dataType:"json",
            success:function(data){
                console.info(data);
            },
            //调用出错执行的函数
            error: function(){
                //请求出错处理
            }
        });
    });*/
</script>
</html>