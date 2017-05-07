<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html class=" ">
    <head>
        <!-- 
         * @Package: Complete Admin - Responsive Theme
         * @Subpackage: Bootstrap
         * @Version: 2.2
         * This file is part of Complete Admin Theme.
        -->
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
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
    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class=" ">
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
<script>
    function switchs(tag,id,status){
        $.ajax({
            url:"<?php echo U('Spider/switchs');?>",
            type:"POST",
            dataType:"json",
            data:"tag="+tag+"&id="+id+"&status="+status,
            success:function(res){
                if(res.res == 'success'){
                    alert("操作成功！");setTimeout("location.reload();");
                }else{
                    alert("操作失败！");
                }
            }
        });
    }
</script>
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

    <section class="wrapper main-wrapper row" style=''>
        <!--<div class='mbx'>
            <i class='fa fa-home'></i>
            <a href="">首页</a>/
            <a href="">易管理</a>/
            <span>用户管理</span>
        </div>-->
        <div class="col-lg-12">
            <section class="box ">
                    <header class="panel_header">
                        <h2 class="title pull-left">问政策列表</h2>
                        <div class="actions panel_actions pull-right">
                             <div class='pl cle' style='margin-bottom:10px;'>
                            <!--<a href="" class="da da1"><i class="fa fa-upload"></i>导入</a>-->
                            <?php if($dp_id != 0): ?><a href="<?php echo U('wzcAdd');?>" class="da da2"><i class="fa fa-plus"></i>新增</a><?php endif; ?>
                            <!--<a href="javascript:;" class="da da3"><i class="fa fa-trash"></i>删除</a>-->
                             <label>
                                 <form>
                                     <select name="dept_id" type="search">
                                         <?php if($dept2 != 0): ?><option value="" selected><?php echo ($dept2['dname']); ?></option><?php endif; ?>
                                         <?php if($dept2 == 0): ?><option value="" selected>请选择</option><?php endif; ?>
                                         <?php if(is_array($dept)): foreach($dept as $key=>$val): ?><option value="<?php echo ($val["did"]); ?>" ><?php echo ($val["dname"]); ?></option><?php endforeach; endif; ?>
                                     </select>
                                     <select type="search" name="s_is_top">
                                         <option value="" >请选择</option>
                                         <option value="1" <?php if($s_is_top==1): ?>selected<?php endif; ?> >题图</option>
                                         <option value="0" <?php if($s_is_top==2): ?>selected<?php endif; ?> >非题图</option>
                                     </select>
                                     <input type="search" name="keyword" placeholder="请输入关键字">
                                     <button type="submit">搜索</button>
                                 </form>
                            </label>
                        </div>
                        </div>
                    </header>

                    <div class="content-body">
                       
                        <div class="row">
                            <div class="col-xs-12">
                                <table id="example-1" class="table table-striped dt-responsive display table1">
                                    <thead>
                                    <tr>
                                        <!--<th><span class='dx-zi'></span></th>-->
                                        <th>部门</th>
                                        <th>政策标题</th>
                                        <th>发布时间</th>
                                        <th>是否题图</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>

                                    <!--<tfoot>-->
                                    <!--<tr>-->
                                        <!--&lt;!&ndash;<th style='position: relative;'><span class='dx-zi dx-zi1' data-on='1'></span></th>&ndash;&gt;-->
                                        <!--<th>部门</th>-->
                                        <!--<th>政策标题</th>-->
                                        <!--<th>发布时间</th>-->
                                        <!--<th>是否题图</th>-->
                                        <!--<th>操作</th>-->
                                    <!--</tr>-->
                                    <!--</tfoot>-->
                                    <tbody>
                                    <?php if(is_array($infos)): $i = 0; $__LIST__ = $infos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr data-in='0'>
                                            <!--<td><span class='dx-zi'></span></td>-->
                                            <td><?php echo ($vo["dept"]); ?></td>
                                            <td><?php echo ($vo["title"]); ?></td>
                                            <td><?php echo ($vo["addtime"]); ?></td>
                                            <td><?php echo ($vo["top"]); ?></td>
                                            <td>
                                                <a onclick="return confirm('确定删除此信息？')" href="/Manage/Yjj/wzcDel/id/<?php echo ($vo["id"]); ?>" class='cz'><i class='fa fa-trash' data-nub='0'></i></a>
                                                <a href="/Manage/Yjj/wzcEdit/?id=<?php echo ($vo["id"]); ?>" class='cz'><i class='fa fa-pencil'></i></a>
                                            </td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="dataTables_paginate paging_bootstrap" id="example-1_paginate">
                                <ul class="pagination">
                                    <?php echo ($page); ?>
                                </ul>
                            </div>
                        </div>
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



<!-- CORE JS FRAMEWORK - START --> 
<script src="/Style/js/jquery-1.11.2.min.js" type="text/javascript"></script> 
<script src="/Style/js/jquery.easing.min.js" type="text/javascript"></script> 
<script src="/Style/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="/Style/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="/Style/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script> 
<script src="/Style/plugins/viewport/viewportchecker.js" type="text/javascript"></script>  
<script>window.jQuery||document.write('<script src="/Style/js/jquery-1.11.2.min.js"><\/script>');</script>
<!-- CORE JS FRAMEWORK - END --> 


<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 

<!-- <script src="/Style/plugins/jvectormap/jquery-jvectormap-2.0.1.min.js" type="text/javascript"></script>
<script src="/Style/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script> -->
<!-- <script src="/Style/js/dashboard.js" type="text/javascript"></script> -->
<!-- <script src="/Style/plugins/echarts/echarts-custom-for-dashboard.js" type="text/javascript"></script> -->
<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 


<script src="/Style/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script>
<!-- CORE TEMPLATE JS - START --> 
<script src="/Style/js/scripts.js" type="text/javascript"></script>
<script type="text/javascript" src='/Style/js/layer.js'></script>  
<!-- END CORE TEMPLATE JS - END --> 


<!-- General section box modal start -->
<div class="modal" id="section-settings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog animated bounceInDown">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Section Settings</h4>
            </div>
            <div class="modal-body">

                Body goes here...

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-success" type="button">Save changes</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>