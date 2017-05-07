<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
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
        <style>
        html,body{
            height:100%;
            width:100%;
        }
            .dl-bg{
                position: relative; 
            }
            .sign{
                position:fixed;
                width:720px;
                margin-left:-360px;
                left:50%;
                top:50%;
                margin-top:-312px;
            }
            .sign >h3{
                width:720px;
                height:61px;
                background:url(/Style/img/al.png) no-repeat center;
            }
            .sign> p{
                font-size:30px;
                color:#fff;
                text-align:center;
                font-family:'微软雅黑';
                margin-bottom:30px;
                margin-top:10px;
            }
            .nb-sign{
                background:#fff;
                width:720px;
                height:490px;
                border-radius:10px;
            }
            .left-sign{
                float:left;
                height:368px;
                width:427px;
                background:url(/Style/img/es_07.png) no-repeat center;
                position:relative;
                left:-70px;
                top:75px;
            }
            .right-sign{
                width:290px;
                height;
                padding:0 25px;
                float:right;
            }
            .right-sign h3{
                text-align:center;
                font-size:24px;
                font-weight:600;
                color:#333;
                font-family:'微软雅黑';
            }
            .right-sign a{
                border:1px solid #ddd;
                font-size:14px;
                color:#666;
                border-radius:16px;
                display:inline-block;
                padding:3px 12px;
                margin-left:10px;
            }
            .right-sign a.on{
                background:#728ca7;
                color:#fff;
                border:1px solid #728ca7;
            }
            .right-sign  input{
                width:240px;
                border:none;
                padding-left:40px;
                border-bottom:1px solid #ddd;
                height:40px;

            }
            .right-sign h3{
                margin: 50px 0 40px;
            }
            .right-sign p{
                position:relative;
                height:40px;
                margin-bottom:0;
            }

            .right-sign p i{
                position:absolute;
                left:0;
                bottom:0;
                width:30px;
                height:30px;
                display:block;
            }
            .right-sign p i:before{
                position:absolute;
                top:50%;
                left:50%;
                margin-top:-7px;
                margin-left:-7px;  
           }
            .right-sign  input.fel{
                width:130px;

            }
            .right-sign  input.fep{
                width:25px;
                height:25px;
            }
            .right-sign  label{
                position:relative;
                padding-left:30px;
            }
            .right-sign  label input{
                position:absolute;
                left:0;
                top:0;
                margin:0
            }
            .right-sign button{
                background:#33d197;
                border-radius:20px;
                height:40px;
                line-height:40px;
                width:240px;
                border:none;
                color:#fff;
                margin-top:30px;
            }
            a.yzm{
                border:none;
                margin:0;
                padding:0;
            }
            a.yzm img{
                width:100px;
                height:40px;
            }
        </style>
    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class=" "><!-- START TOPBAR -->
        <div class='dl-bg'>
            <div class='sign'>
                <h3></h3>
                <p>综合管理中心</p>
                <div class='nb-sign'>
                    <div class='left-sign'></div>
                    <div class='right-sign'>
                        <h3>欢迎登录</h3>
                        <form method="post">
                            <p>
                                <i class='fa fa-user'></i>
                                <input type='text' name="logname" placeholder='登录名'>
                            </p>
                            <p>
                                <i class='fa fa-lock'></i>
                                <input type='password' name="password" placeholder='密码'>
                            </p>
                             <p>
                                <i class='fa fa-lock'></i>
                                <input type='text' name="verify" class='fel' placeholder='验证码'>
                                <a class='yzm'><img src="/Manage/Public/verify" onclick="this.src='/Manage/Public/verify?d='+Math.random();"></a>
                            </p><br/>
                            <!--<p style='padding-top:20px;'>
                                <i></i>
                                <label style='line-height: 25px;'>
                                  <input type="checkbox" class='fep'>
                                记住用户名
                                </label>
                            </p>-->
                            <br/>
                            <!-- <p style='text-align:center;'>
                                <select style="width: 230px;" name="dept">
                                    <option value="0">总台</option>
                                    <?php if(is_array($depts)): $i = 0; $__LIST__ = $depts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["did"]); ?>"><?php echo ($vo["dname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </p> -->
                            <p>
                                <button type="submit">立即登录</button>
                            </p>
                        </form>
                        <!--<p style='text-align:center;margin-bottom:20px;'>
                            <a href="javascript:;" class='on'>总台</a>
                            <a href="javascript:;">部门</a>
                        </p>-->
                    </div>
                </div>
            </div>
        </div>
<script src="/Style/js/jquery-1.11.2.min.js" type="text/javascript"></script> 
<script src="/Style/js/jquery.easing.min.js" type="text/javascript"></script> 
<script src="/Style/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
<script src="/Style/plugins/pace/pace.min.js" type="text/javascript"></script>  
<script src="/Style/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script> 
<script src="/Style/plugins/viewport/viewportchecker.js" type="text/javascript"></script>  
<script>window.jQuery||document.write('<script src="/Style/js/jquery-1.11.2.min.js"><\/script>');</script>



<script src="/Style/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="/Style/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script>
<!-- CORE TEMPLATE JS - START --> 
<script src="/Style/js/scripts.js" type="text/javascript"></script>
<script type="text/javascript" src='/Style/js/layer.js'></script>  
<script type="text/javascript">
$('.da2').click(function(){
    event.stopPropagation();  
    $('.xzen,.zhao').fadeIn(200);
    return false;
});
     $(document).click(function(event){
          var _con = $('.xzen');   // 设置目标区域
          if(!_con.is(event.target) && _con.has(event.target).length === 0){ // Mark 1

            $('.xzen').fadeOut(200);
            $('.zhao').fadeOut(200)          //淡出消失
          }
    });

</script>
<script type="text/javascript">
    $('.table1 span.dx-zi').click(function(event) {
        $(this).toggleClass('on');
    });

</script>
<script type="text/javascript">
    $('.fa-trash').on('click',function(){
        var t = $(this).attr('data-nub');
        layer.open({
        type : 1,
        shadeClose : false,
        // area : [ '500px', '300px' ],
        title : "确认",
        btn : [ '确定', '取消' ],
        btn1 : function(index, layero) {
              $('tbody tr').each(function(){
                if($(this).attr('data-in') == t){
                    $(this).remove();
                    return;
                }
              });  
            
            layer.close(index);
        
        },
        btn2 : function(index) {
            layer.close(index);
        }
    });
    })
    $('.da3').on('click',function(){
        layer.open({
        type : 1,
        shadeClose : false,
        // area : [ '500px', '300px' ],
        title : "确认",
        btn : [ '确定', '取消' ],
        btn1 : function(index, layero) {
             shanc();
            
            layer.close(index);
        
        },
        btn2 : function(index) {
            layer.close(index);
        }
    });
    })
    function shanc(){
        if ($('.dx-zi.on').length>0) {
            $('.dx-zi').each(function(index, el) {
               if ($(this).hasClass('on')) {
                $(this).parents('tr').remove();
               };
            });
        }else{
             layer.msg('请选择', {icon: 1});
        }
    }
    $('.dx-zi1').on('click',function(){
        if($(this).attr('data-on') == 1){
            $(this).attr('data-on','0' );
            $('.dx-zi').each(function(){
                $(this).addClass('on');
            })
        }else{
            $(this).attr('data-on','1' );
            $('.dx-zi').each(function(){
                $(this).removeClass('on');
            })
        }
    })
</script>
</body>
</html>