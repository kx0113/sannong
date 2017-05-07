<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <script>
        //10px 1rem;
        !function(){function a(){if(document.documentElement.clientWidth<600){document.documentElement.style.fontSize=document.documentElement.clientWidth/32+"px"}else{document.documentElement.style.fontSize="16.875px"}}var b=null;window.addEventListener("resize",function(){clearTimeout(b),b=setTimeout(a,300)},!1),a()}(window);
        </script>
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="stylesheet" href="/Style/css/normalize.css">
        <link rel="stylesheet" href="/Style/css/main.css">
        <link rel="stylesheet" type="text/css" href="/Style/css/css2.css">
        <script src="/Style/js/modernizr-2.6.2.min.js"></script>
        <style>
        	.bg td{
	border-bottom:1px solid #ddd;
        		border-right:1px solid #ddd;
        		text-align: center;
        		font-size:1.2rem;
        		padding:.3rem 0;
        	}
        	.bg table{
	border-top:1px solid #ddd;
        		border-left:1px solid #ddd;
        	}
        </style>
    </head>
    <body class='bc1'>
        <!-- <div class='loading'><img src="img/loading_more.gif" style='width:auto'></div> -->
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class='warp '>
            <div id="leftTabBox" class="tabBox vertical-view">
                <div class='hd maple-tab clearFix'>
                    <ul class='page' l id="pagenavi">
                        <li <?php if($cz == 1): ?>class='active'<?php endif; ?>  data-li='1'><a href='/Ygl/Public/spider?dept_id=1'>本周</a></li>
                        <li <?php if($cz == 2): ?>class='active'<?php endif; ?> data-li='2'><a href="/Ygl/Public/spider?dept_id=2">本月</a></li>
                        <li <?php if($cz == 3): ?>class='active'<?php endif; ?> data-li='3'><a href="/Ygl/Public/spider?dept_id=3">今年</a></li>
                        <!--<li <?php if($cz == 4): ?>class='active'<?php endif; ?> data-li='3'><a href="/Ygl/Public/spider?dept_id=4">蔬菜</a></li>-->
                    </ul>
                </div>
                <div class='bd flex-z swiper-container'>
                    <div class='swiper-wrapper'>
                        <div class='swiper-slide tab-content-1'>
                        <form method="post"  id="dept_data" action="/Ygl/Public/jgqs2">
                            <div class='horizontal-view po-sj' style='    padding: 2rem 1rem;'>
                                <input type="hidden" name="dept_id" value="<?php echo ($cz); ?>"/>
                                <div class='flex-z '>
                                    <input type='date' name="date1" class='box-s' value="<?php echo ($date1); ?>" max='1985-04-12T23:20:50.52'>
                                    <input type='hidden' id ='dept_id' name="dept_id">
                                </div>
                                <div class='zj-sj'>-</div>
                                <div class='flex-z '>
                                    <input type='date' name="date2"   value="<?php echo ($date2); ?>"  class='box-s'>
                                </div>
								<a type="submit" onclick="submit()" style='height:;inline-block;line-height:2.8rem;padding:0 1rem;background:#7067e2;color:#fff;font-size:1.2rem;margin-left:.5rem;border-radius: .2rem;'>提交</a>

                            </div>
                            </form>
                            <div class='tit'>
                                <h3>来源统计</h3>
                            </div>
                            <div id="container" style="height: 25rem"></div>
                            <div class='tit'>
                                <h3>处理情况统计</h3>
                            </div>
                            <div id="container2" style="height: 25rem"></div>

                            <!--<div class='bg' style='padding:.5rem;'>
                            	<table width='100%'>
                                    <tr>
                                        <td>-</td>
                                        <?php if(is_array($weekList[0]['adddate'])): foreach($weekList[0]['adddate'] as $key=>$vo): ?><td><?php echo ($vo); ?></td><?php endforeach; endif; ?>
                                    </tr>
                                    <?php if(is_array($weekList)): foreach($weekList as $key=>$vo): ?><tr>
                                            <td><?php echo ($vo["cate_name"]); ?></td>
                                            <?php if(is_array($vo["child"])): foreach($vo["child"] as $key=>$vv): ?><td><?php echo ($vv["price"]); ?></td><?php endforeach; endif; ?>
                                        </tr><?php endforeach; endif; ?>
                            	</table>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src='/Style/js/jquery-1.11.2.min.js'></script>

        <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/echarts-all-3.js"></script>
        <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/extension/dataTool.min.js"></script>
        <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/china.js"></script>
        <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/world.js"></script>
        <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ZUONbpqGBsYGXNIYHicvbAbM"></script>
        <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/extension/bmap.min.js"></script>
        <script src="/Style/js/plugins.js"></script>
        <script src="/Style/js/main.js"></script>
        <script type="text/javascript">
        $('.loading').hide();
        </script>
        <script type="text/javascript">
            var dom = document.getElementById("container");
            var myChart = echarts.init(dom);
            var app = {};
            option = null;
            option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:[<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>'<?php echo ($vo["name"]); ?>',<?php endforeach; endif; else: echo "" ;endif; ?>]
                },
                grid: {
                    left: '3%',
                    right: '6%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data:[<?php if(is_array($time_7)): $i = 0; $__LIST__ = $time_7;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>'<?php echo ($vo); ?>',<?php endforeach; endif; else: echo "" ;endif; ?>]
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>{
                        name:'<?php echo ($vo["name"]); ?>',
                        type:'line',
                        stack: '总量',
//                        data:[<?php echo ($vo["data"]["count_1"]); ?>, <?php echo ($vo["data"]["count_2"]); ?>, <?php echo ($vo["data"]["count_3"]); ?>, <?php echo ($vo["data"]["count_4"]); ?>, <?php echo ($vo["data"]["count_5"]); ?>, <?php echo ($vo["data"]["count_6"]); ?>, <?php echo ($vo["data"]["count_7"]); ?>]
                        data:[<?php if(is_array($vo["date"])): $i = 0; $__LIST__ = $vo["date"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?>'<?php echo ($v1); ?>',<?php endforeach; endif; else: echo "" ;endif; ?>]
            },<?php endforeach; endif; else: echo "" ;endif; ?>
                ]
            };
            ;
            if (option && typeof option === "object") {
                myChart.setOption(option, true);
            }
        </script>
        <script type="text/javascript">
            var dom = document.getElementById("container2");
            var myChart = echarts.init(dom);
            var app = {};
            option = null;
            option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data:[<?php if(is_array($status)): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>'<?php echo ($vo["name"]); ?>',<?php endforeach; endif; else: echo "" ;endif; ?>]
                },
                grid: {
                    left: '3%',
                    right: '6%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
//                    data: ['9-02','9-03','9-04','9-05','9-06','9-07','9-08']
                    data:[<?php if(is_array($time_7)): $i = 0; $__LIST__ = $time_7;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>'<?php echo ($vo); ?>',<?php endforeach; endif; else: echo "" ;endif; ?>]
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                <?php if(is_array($status)): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>{
                name:'<?php echo ($vo["name"]); ?>',
                type:'line',
                stack: '总量',
//                        data:[<?php echo ($vo["data"]["count_1"]); ?>, <?php echo ($vo["data"]["count_2"]); ?>, <?php echo ($vo["data"]["count_3"]); ?>, <?php echo ($vo["data"]["count_4"]); ?>, <?php echo ($vo["data"]["count_5"]); ?>, <?php echo ($vo["data"]["count_6"]); ?>, <?php echo ($vo["data"]["count_7"]); ?>]
                data:[<?php if(is_array($vo["data"])): $i = 0; $__LIST__ = $vo["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v1): $mod = ($i % 2 );++$i;?>'<?php echo ($v1); ?>',<?php endforeach; endif; else: echo "" ;endif; ?>]
            },<?php endforeach; endif; else: echo "" ;endif; ?>
                ]
            };
            ;
            if (option && typeof option === "object") {
                myChart.setOption(option, true);
            }
        </script>


    </body>
</html>