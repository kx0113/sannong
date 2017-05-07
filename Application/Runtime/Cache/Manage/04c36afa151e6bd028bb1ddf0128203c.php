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
                        <h3>添加预警</h3>
                        <ul style='padding-top:40px; padding-bottom:50px;'>
                            <form method="post" enctype="multipart/form-data">
                                <li>
                                    <span>标题</span>
                                    <input type='' name="title" id="title" value="<?php echo ($edit_big_data["title"]); ?>">
                                </li>
                                <li>
                                    <span>来自</span>
                                    <input type='' name="afrom" id="afrom" value="<?php echo ($edit_big_data["afrom"]); ?>">
                                </li>
                                <li>
                                    <span>展示图片：</span>
                                    <input style="margin-left: 87px;" type='file' name="picture">
                                </li>
                                <img src="/Uploads/<?php echo ($edit_big_data["images"]); ?>" style="margin-left: 87px;width: 150px;" />
                                <!--<li>-->
                                    <!--<span>处理状态</span>-->
                                    <!--<select name="status" id="status">-->
                                        <!--<option value="" >请选择...</option>-->
                                        <!--<option value="3" <?php if($edit_big_data[status] == 3): ?>selected="selected"<?php endif; ?>>待处理</option>-->
                                        <!--<option value="2" <?php if($edit_big_data[status] == 2): ?>selected="selected"<?php endif; ?>>处理中</option>-->
                                        <!--<option value="1" <?php if($edit_big_data[status] == 1): ?>selected="selected"<?php endif; ?>>已处理</option>-->
                                    <!--</select>-->
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
                                <li>
                                    <span>内容：</span>
                                   <!--<textarea  name="content" type="text/plain" style="width:700px;height:300px;"><?php echo ($edit_big_data_content); ?></textarea>-->
                                    <script id="container" name="content" type="text/plain" style="width:700px;height:300px;"><?php echo ($edit_big_data_content); ?></script>
                                </li>
                                <li class='xh-an'>
                                    <button type="submit" style="margin-right: 100px;" onclick=" return yanzheng()">保存</button>
                                    <button type="button" onclick="location.href='/Manage/Ygl/emergency_index';" class="czhi">取消</button>
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
        if($('#title').val()==''){
            alert('请填写标题');
            return false;
        }
        /* if($('#container').val()==0){
            alert('请填写内容！');
            return false;
        } */
    }
</script>
<!-- CORE JS FRAMEWORK - START -->
<script src="/Style/js/jquery-1.11.2.min.js" type="text/javascript"></script>
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
</html>