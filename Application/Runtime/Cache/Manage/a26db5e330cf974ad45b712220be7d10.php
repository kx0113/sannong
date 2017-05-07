<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html class="">
<head>
    <!--
     * @Package: Complete Admin - Responsive Theme
     * @Subpackage: Bootstrap
     * @Version: 2.2
     * This file is part of Complete Admin Theme.
    -->
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <title>综合管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script src="/Style/js/jquery-1.11.2.min.js" type="text/javascript"></script>
    <link rel="shortcut icon" href="../assets/images/favicon.png" type="image/x-icon" />    <!-- Favicon -->
    <link rel="apple-touch-icon-precomposed" href="../assets/images/apple-touch-icon-57-precomposed.png">	<!-- For iPhone -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/images/apple-touch-icon-114-precomposed.png">    <!-- For iPhone 4 Retina display -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/images/apple-touch-icon-72-precomposed.png">    <!-- For iPad -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/images/apple-touch-icon-144-precomposed.png">    <!-- For iPad Retina display -->

    <!-- CORE CSS FRAMEWORK - START -->
    <link href="/Style/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/Style/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/fonts/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/css/animate.min.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/css/css.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/css/css1.css" rel="stylesheet" type="text/css"/>
    <link href='/Style/css/layer.css'>

    <!-- CORE CSS FRAMEWORK - END -->

    <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - START -->


    <link href="/Style/plugins/jvectormap/jquery-jvectormap-2.0.1.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/Style/plugins/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/Style/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/Style/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/Style/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" media="screen"/>

    <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - END -->


    <!-- CORE CSS TEMPLATE - START -->
    <link href="/Style/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/Style/css/responsive.css" rel="stylesheet" type="text/css"/>
    <!-- CORE CSS TEMPLATE - END -->

    <!-- ueditor -->
    <script type="text/javascript" charset="utf-8" src="/Style/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Style/ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="/Style/ueditor/lang/zh-cn/zh-cn.js"></script>
    <!-- ueditor -->
	<style>

.xzen ul li{
	    position: relative;
}
.btn{
	    top: 17px;
    right: -62px;
}</style>
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="" style="height: auto;">
<!-- START TOPBAR -->
<div class='page-topbar '>
    <div class='logo-area'>

    </div>
    <div class='quick-area'>
        <div class='pull-left'>
            <ul class="info-menu left-links list-inline list-unstyled">
                <!--<li class="sidebar-toggle-wrap sidebar-toggle-wrap1">
                    <a href="#" data-toggle="sidebar" class="sidebar_toggle">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
                <li class="sidebar-toggle-wrap">
                    <a href="index.html" class="sidebar_toggle">
                        <i class='fa fa-home'></i>
                        首页
                    </a>
                </li>
                <li class="message-toggle-wrapper">
                    <a href="yhgl.html" class="toggle">
                        <i class='fa fa-cogs'></i>
                       易管理
                    </a>
                </li>
                <li class="notify-toggle-wrapper">
                    <a href="yjj.html"  class="toggle">
                        <i class='fa fa-group'></i>
                        易家家
                    </a>
                </li>
                <li class="hidden-sm hidden-xs searchform">
                    <form action="ui-search.html" method="post">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control animated fadeIn" placeholder="Search & Enter">
                        </div>
                        <input type='submit' value="">
                    </form>
                </li>-->
            </ul>
        </div>
        <div class='pull-right'>
            <ul class="info-menu right-links list-inline list-unstyled">

                <?php if(!empty($headList)): if(is_array($headList)): foreach($headList as $key=>$vo): ?><li class="profile <?php if(($vo["action_code"]) == $header_value): ?>on<?php endif; ?>"  style='float:left;margin-right:10px;'>
                            <a style="color: #fff" href="<?php echo ($vo["action_url"]); ?>/action_id/<?php echo ($vo["action_id"]); ?>">
                                <span style="font-size: 16px;"><?php echo ($vo["action_name"]); ?></span>
                            </a>
                        </li><?php endforeach; endif; endif; ?>

                <li class="profile" style='float:left;margin-right:10px;color:#FFF;'>
                    <a href="">
                    <span style="font-size: 20px;color:#fff;o"><?php echo ($_SESSION['admin']['logname']); ?></span>
                    </a>
                </li>
               <li class="chat-toggle-wrapper" style='float:left;'>
                    <a onclick="return confirm('确定退出吗?');" href="/Manage/Public/logout" data-toggle="chatbar" class="toggle_chat  dl-tc">
                        <i class="fa fa-times"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>
<!-- END TOPBAR -->
<!-- START CONTAINER -->
<div class="page-container row-fluid container-fluid">

    <!-- SIDEBAR - START -->

    <div class="page-sidebar fixedscroll">

        <!-- MAIN MENU - START -->
        <div class="page-sidebar-wrapper" id="main-menu-wrapper">
    <div>
        <a href="" style="background-color:#179d24" class='fbxx'>发布信息</a>
        <ul class='wraplist' style='border-top:1xp solid #3e4359'>
            <?php if(is_array($menus)): $i = 0; $__LIST__ = $menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($_GET['action_id'] == 1): if($vo['action_id'] == 1): ?><li <?php if($liclass == $vo['action_code']): ?>class="open"<?php endif; ?>>
                        <a href="javascript:;">
                            <i class="fa  fa-pie-chart"></i>
                            <span class="title"><?php echo ($vo["action_name"]); ?></span>
                            <span class="arrow "></span>
                        </a>
                        <ul class="sub-menu" >
                            <?php if(is_array($vo["_child"])): $i = 0; $__LIST__ = $vo["_child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;?><li>
                                    <a <?php if($aclass == $child['action_code']): ?>class="active"<?php endif; ?> href="<?php echo ($child["action_url"]); ?>" ><?php echo ($child["action_name"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                        </li><?php endif; ?>

                <?php else: ?>
                    <li <?php if($liclass == $vo['action_code']): ?>class="open"<?php endif; ?>>
                    <a href="javascript:;">
                        <i class="fa  fa-pie-chart"></i>
                        <span class="title"><?php echo ($vo["action_name"]); ?></span>
                        <span class="arrow "></span>
                    </a>
                    <ul class="sub-menu" >
                        <?php if(is_array($vo["_child"])): $i = 0; $__LIST__ = $vo["_child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;?><li>
                                <a <?php if($aclass == $child['action_code']): ?>class="active"<?php endif; ?> href="<?php echo ($child["action_url"]); ?>" ><?php echo ($child["action_name"]); ?></a>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                    </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>
        <!-- MAIN MENU - END -->



    </div>
    <!--  SIDEBAR - END -->
    <!-- START CONTENT -->
    <section id="main-content" class=" ">

        <section class="wrapper main-wrapper row">
            <!--<div class='mbx'>
                <i class='fa fa-home'></i>
                <a href="">首页</a>/
                <a href="">易管理</a>/
                <span>用户管理</span>
            </div>-->
            <div class="col-lg-12">
                <section class="box">
                    <div class='xzen xzen1'>
                        <h3>应急管理-<a  style="color: #f00;"><?php echo ($emergency_find["name"]); ?></a>-编辑预警</h3>
                        <ul style='padding-top:40px; padding-bottom:50px;'>
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="pid" value="<?php echo ($info["pid"]); ?>">
                                <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
                                <li>
                                    <span>二级菜单</span>
                                    <input type='text'  name="name" value="<?php echo ($info["name"]); ?>" disabled>
                                </li>
                                <li>
                                    <span>预警状态</span>
                                    <select name="status" id="status">
                                        <option value="" >请选择...</option>
                                        <option value="1" <?php if($info["status"] == 1): ?>selected<?php endif; ?> >开启</option>
                                        <option value="2" <?php if($info["status"] == 2): ?>selected<?php endif; ?> >关闭</option>
                                    </select>
                                </li>
                                <li>
                                    <span>标题</span>
                                    <input type='text' name="title" id="title" value="<?php echo ($info["title"]); ?>">
                                </li>
                                <li>
                                    <span>内容</span>
                                    <input type='text' name="content" id="content" value="<?php echo ($info["content"]); ?>">
                                </li>
                                <li>
                                    <span>地址名称</span>
                                    <input type='text' name="location" id="location" value="<?php echo ($info["location"]); ?>">
                                </li>

                                <script src="http://api.map.baidu.com/api?key=&v=1.1&services=true"></script>
                                <li id="mapxy">
                                    <span style='float:left'>经纬度：</span>
                                    <div style='width:250px;display: inline-block;padding-left: 4px;'>
                                        <input style='width:120px;    display: inline-block;' type="text" class="form-control" id="lng" name="longitude" readonly  value="<?php echo ($info["longitude"]); ?>"/>

                                        <input style='width:120px;    display: inline-block;' type="text" class="form-control" id="lat" name="latitude" readonly value="<?php echo ($info["latitude"]); ?>"/>
                                    </div>
                                    <label class="control-label cRed" id="mapxy_label"  style='width:100px;padding-left:0'>*点击地图拾取</label>
                                </li>
                                <li>
                                    <span>无人机监控</span>
                                    <input type='text' name="uav_url" id="uav_url" value="<?php echo ($info["uav_url"]); ?>">
                                    <span>无人机监控--监控位置描述</span>
                                    <input type='text' name="uav_location" id="uav_location" value="<?php echo ($info["uav_location"]); ?>">
                                </li>
                                <li  style="position: relative;">
                                    <span style="position: absolute;color:#ff0000; top: 0px;right: -165px;" class="btn" id="btn24">&nbsp;&nbsp;增加</span>
                                </li>
                                <?php if(is_array($info['video_urls'])): $i = 0; $__LIST__ = $info['video_urls'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li style="position: relative;">
                                    <span>视频监控</span>
                                    <input type='text' name="video_url[]" id="video_url[]" value="<?php echo ($v["video_url"]); ?>">
                                    <span>视频监控--监控位置描述</span>
                                    <input type='text' name="video_location[]" id="video_location[]" value="<?php echo ($v["video_location"]); ?>">
                                    <span style="position: absolute;color:#ff0000;top:0;" class="btn" onclick="sc($(this))">&nbsp;&nbsp;删除</span>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                <div style="clear: both;"></div>
                                <li class="sss" style="position: relative;">
                                    <span style="position: absolute;color:#ff0000; right: -166px;" class="btn" id="btn2">&nbsp;&nbsp;增加</span>
                                </li>
                                    <?php if(is_array($info['commander_account'])): $i = 0; $__LIST__ = $info['commander_account'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="ss">
                                            <span>指挥员易管理账号</span>
                                            <input type='text' name="commander_account[]" id="commander" value="<?php echo ($vo); ?>">
                                            <span style="position: absolute;color:#ff0000;" class="btn" id="btn3" onclick="sc($(this))">&nbsp;&nbsp;删除</span>
                                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                
                                <br>
                                <!--引入群账号页面-->
                                <li class='d'>
    <span>群分类</span>
    <select name="cate_id" id='cid'>
        <option value="123456789" >请选择...</option>
        <?php if(is_array($group_cate)): $i = 0; $__LIST__ = $group_cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($info['cate_id'] == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </select>
</li>
<li>
    <span>群账号</span>
    <select id="group_account" name="account">
        <?php if($info['account']): if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($vo["id"]) == $info['account']): ?><option value="<?php echo ($vo["id"]); ?>" <?php if($info['account'] == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["group_account"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
    </select>
</li>
<script type="text/javascript">
    $('#cid').change(function(){
        var cid= $("#cid").find("option:selected").val();
        areaChange(cid);
    });
    function areaChange(cid){
        if(cid){
            $.post("<?php echo U('step_group_cate',array('m'=>'group_cate'));?>",
                    {cid:cid},
                    function(data) {
                        $("#group_account option").remove();
                        var obj = jQuery.parseJSON(data);
                        var t = obj.result.length;
                        for (var o = 0;o<t;o++){
                            var p ='<option value='+obj.result[o].id+'>'+obj.result[o].group_account+'</option>';
                            $('#group_account').append(p);
                        }
                    }
            );
        }
    }
</script>
                                <div style="width:450px;height:350px;border:#ccc solid 1px;margin-left: 100px;" id="dituContent"></div><br/>
                                <!--<li>-->
                                    <!--<span>地址信息：</span>-->
                                    <!--<script id="container" name="content" type="text/plain" style="width:700px;height:300px;"><?php echo ($emergency_find["content"]); ?></script>-->
                                <!--</li>-->
                                <!--<li>-->
                                    <!--<span>展示图片：</span>-->
                                    <!--<input style="margin-left: 87px;" type='file' name="pic">-->
                                <!--</li>-->
                                <!--<li>-->
                                    <!--<span>项目状态：</span>-->
                                    <!--<select name="status">-->
                                        <!--<option value="1">未启动</option>-->
                                        <!--<option value="2">进行中</option>-->
                                        <!--<option value="3">已结束</option>-->
                                    <!--</select>-->
                                <!--</li>-->
                                <!--<li>-->
                                    <!--<span>开始时间：</span>-->
                                    <!--<input style="margin-left: 87px;" type='date' name="stime">-->
                                <!--</li>-->
                                <!--<li>-->
                                    <!--<span>结束时间：</span>-->
                                    <!--<input style="margin-left: 87px;" type='date' name="etime">-->
                                <!--</li>-->
                                <!--<li>-->
                                    <!--<span>内容：</span>-->
                                    <!--<script id="container" name="content" type="text/plain" style="width:700px;height:300px;"><?php echo ($edit_big_data_content); ?></script>-->
                                <!--</li>-->
                                <li class='xh-an'>
                                    <?php if(($dp_id == $did)): ?><button type="submit" style="margin-right: 100px;" onclick=" return yanzheng()">保存</button><?php endif; ?>
                                    <button type="button" onclick="location.href=history.back()" class="czhi">取消</button>
                                </li>
                            </form>
                        </ul>
                    </div>
                </section>
            </div>
            <!-- MAIN CONTENT AREA STARTS -->

            <!-- MAIN CONTENT AREA ENDS -->
        </section>
    </section>
    <div class="chatapi-windows ">
    </div>    </div>
<div class='zhao'></div>
<!-- END CONTAINER -->
<!-- LOAD FILES AT PAGE END FOR FASTER LOADING -->

<!-- ueditor -->
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('container');
</script>
<!-- ueditor -->
<script>
    function yanzheng(){
        if($('#status').val()==''){
            alert('请选择预警状态！');
            return false;
        }if($('#title').val()==''){
            alert('请填写预警标题！');
            return false;
        }if($('#content').val()==''){
            alert('请填写内容！');
            return false;
        }
        if($('#location').val()==''){
            alert('请填写地址！');
            return false;
        }
         if($('#lng').val()==0){
            alert('请选择坐标！');
            return false;
        }  if($('#lat').val()==0){
            alert('请选择坐标！');
            return false;
        }
    }
//    $(document).ready(function(){
//        $("#btn2").click(function(){
//            $("#ol").append($("#oll").html());
//            $("#oll").append(" " +
//             "<li><br><span style='color: #ff0000'>指挥员易管理</span> <input type='text' name='commander_account[]'></li>"
//             );
//        });
//        $("#btn3").click(function(){
//            $("#oll li:last").remove();
//            $("#oll li:last").remove();
//        });
//    });

	$('#btn2').click(function(){
    	var kl = '' +
                '<li>' +
                    '<span>指挥员易管理账号</span>' +
                    '<input type=text name="commander_account[]" id="commander" value="<?php echo ($vo["commander"]); ?>">' +
                    '<span style="position: absolute;color:#ff0000;" class="btn" id="btn3" onclick="sc($(this))">&nbsp;&nbsp;删除</span>' +
                '</li>'
    	$('.d').before(kl)
    });
    $('#btn24').click(function(){
    	var kl = '' +
                '<li>' +
                    '<span style="color: #ff0000">视频监控</span>' +
                    '<input type=text name="video_url[]" id="video_url" value="<?php echo ($vo["video_url"]); ?>"><br><br>' +
                    '<span style="color: #ff0000">视频监控--监控位置描述</span>' +
                    '<input type=text name="video_location[]" id="video_location" value="<?php echo ($vo["video_location"]); ?>">' +
                    '<span style="position: absolute;color:#ff0000;top:0px;" class="btn" id="btn3" onclick="sc($(this))">&nbsp;&nbsp;删除</span>' +
                '</li>'
    	$('.sss').before(kl)
    })

    function sc(div){
    	div.parents('li').remove();
    }
</script>
<!-- CORE JS FRAMEWORK - START -->

<script src="/Style/js/jquery.easing.min.js" type="text/javascript"></script>
<script src="/Style/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/Style/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="/Style/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script>
<script src="/Style/plugins/viewport/viewportchecker.js" type="text/javascript"></script>
<script>window.jQuery||document.write('<script src="/Style/js/jquery-1.11.2.min.js"><\/script>');</script>
<!-- CORE JS FRAMEWORK - END -->

<script src="/Style/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script>
<!-- CORE TEMPLATE JS - START -->
<script src="/Style/js/scripts.js" type="text/javascript"></script>
<script type="text/javascript" src='/Style/js/layer.js'></script>
<!-- END CORE TEMPLATE JS - END -->
</body>
<script type="text/javascript">
    // 当前经纬度
    var map_x = '<?php echo ($info["longitude"]); ?>';
    var map_y = '<?php echo ($info["latitude"]); ?>';

    //创建和初始化地图函数：
    function initMap(){
        createMap();//创建地图
        setMapEvent();//设置地图事件
        addMapControl();//向地图添加控件
        addMarker();//向地图中添加marker
    }

    //创建地图函数：
    function createMap(){
        var map = new BMap.Map("dituContent");//在百度地图容器中创建一个地图
        var point = new BMap.Point(map_x,map_y);//定义一个中心点坐标
        map.centerAndZoom(point,14);//设定地图的中心点和坐标并将地图显示在地图容器中
        window.map = map;//将map变量存储在全局
        map.addEventListener("click", getXy);
    }

    //地图事件设置函数：
    function setMapEvent(){
        map.enableDragging();//启用地图拖拽事件，默认启用(可不写)
        map.enableScrollWheelZoom();//启用地图滚轮放大缩小
        map.enableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
        map.enableKeyboard();//启用键盘上下左右键移动地图
    }

    //地图控件添加函数：
    function addMapControl(){
        //向地图中添加缩放控件
        var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_SMALL});
        map.addControl(ctrl_nav);
        //向地图中添加缩略图控件
        var ctrl_ove = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:0});
        map.addControl(ctrl_ove);
        //向地图中添加比例尺控件
        var ctrl_sca = new BMap.ScaleControl({anchor:BMAP_ANCHOR_TOP_RIGHT});
        map.addControl(ctrl_sca);
    }

    //标注点数组
    var myMarker = {title:"",content:"",point:''+map_x+'|'+map_y,isOpen:0,icon:{w:23,h:25,l:46,t:21,x:9,lb:12}};
    var curMarker = null;

    //创建marker
    function addMarker(){
        var json = myMarker;
        var p0 = json.point.split("|")[0];
        var p1 = json.point.split("|")[1];
        var point = new BMap.Point(p0,p1);
        var iconImg = createIcon(json.icon);
        var marker = new BMap.Marker(point,{icon:iconImg});
        map.addOverlay(marker);
        curMarker = marker;
    }
    //创建InfoWindow
    function createInfoWindow(i){
        var json = markerArr[i];
        var iw = new BMap.InfoWindow("<b class='iw_poi_title' title='" + json.title + "'>" + json.title + "</b><div class='iw_poi_content'>"+json.content+"</div>");
        return iw;
    }
    //创建一个Icon
    function createIcon(json){
        var icon = new BMap.Icon("http://api.map.baidu.com/img/markers.png", new BMap.Size(json.w,json.h),{imageOffset: new BMap.Size(0, 0 - 10 * 27.5),infoWindowOffset:new BMap.Size(json.lb+5,1),offset:new BMap.Size(json.x,json.h)})
        return icon;
    }

    function getXy(e){
        //alert(e.point.lng + ", " + e.point.lat);
        $('#lng').val(e.point.lng);
        $('#lat').val(e.point.lat);
        curMarker.setPoint(e.point);
    }

    initMap();//创建和初始化地图
</script>
</html>