<?php
namespace Admin\Controller;
use Think\Controller;
class ReportController extends BaseController {
    public function index(){
        //搜索
        if($_POST['area']){
            $where['area'] = array("like","%".$_POST['area']."%");
            $where['status'] = 1;
            $where['type'] = 1;
        }
        if($_POST['deviceid_type']){
            $where['deviceid_type'] = array("like","%".$_POST['deviceid_type']."%");
            $where['status'] = 1;
            $where['type'] = 1;
        }
        if($_POST['staff']){
            $where['staff'] = array("like","%".$_POST['staff']."%");
            $where['status'] = 1;
            $where['type'] = 1;
        }
        if($_POST['name_']){
            $where['name'] = array("like","%".$_POST['name_']."%");
            $where['status'] = 1;
            $where['type'] = 1;
        }
        $where['status'] = 1;
        $where['type'] = 1;
        $data = M('sys_deviceid')->where($where)->select();
        $this->assign("data",$data);
        $this->display();
    }
    public function statement(){
        $id = $_GET['id'];
        //设备基本信息
        $deviceid = M('sys_deviceid')->where(array('id'=>$id))->find();
        $this->assign("deviceid",$deviceid);

        //7天前
        $day_7 = date("Y-m-d",strtotime("-6 day"));

        //环境温度  最高
        $day_7_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_7_evn_desc = M("weather")->query($day_7_evn_desc);
        //环境温度  最低
        $day_7_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_7_evn_asc = M("weather")->query($day_7_evn_asc);
        //环境温度  平均
        $day_7_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_7%'";
        $day_7_evn_avg = M("weather")->query($day_7_evn_avg);

        //环境湿度  最高
        $day_7_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_7_wet_desc = M("weather")->query($day_7_wet_desc);
        //环境湿度  最低
        $day_7_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_7_wet_asc = M("weather")->query($day_7_wet_asc);
        //环境湿度  平均
        $day_7_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_7%'";
        $day_7_wet_avg = M("weather")->query($day_7_wet_avg);

        //土壤温度  最高
        $day_7_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_7_soil_desc = M("weather")->query($day_7_soil_desc);
        //土壤温度  最低
        $day_7_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_7_soil_asc = M("weather")->query($day_7_soil_asc);
        //土壤温度  平均
        $day_7_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_7%'";
        $day_7_soil_avg = M("weather")->query($day_7_soil_avg);

        //土壤温度  最高
        $day_7_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY soil_wet DESC LIMIT 1";
        $day_7_soil_wet_desc = M("weather")->query($day_7_soil_wet_desc);
        //土壤温度  最低
        $day_7_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY soil_wet ASC LIMIT 1";
        $day_7_soil_wet_asc = M("weather")->query($day_7_soil_wet_asc);
        //土壤温度  平均
        $day_7_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_7%'";
        $day_7_soil_wet_avg = M("weather")->query($day_7_soil_wet_avg);

        //光照强度  最高
        $day_7_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_7%' ORDER BY sunlight DESC LIMIT 1";
        $day_7_sunlight_desc = M("weather")->query($day_7_sunlight_desc);


        //拼装数组
        $day_7s = array(
            'time'=> $day_7,
            'evn_desc'=>$day_7_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_7_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_7_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_7_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_7_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_7_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_7_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_7_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_7_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_7_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_7_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_7_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_7_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_7s);exit;
        $this->assign("day_7s",$day_7s);

        //6天前
        $day_6 = date("Y-m-d",strtotime("-5 day"));

        //环境温度  最高
        $day_6_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_6_evn_desc = M("weather")->query($day_6_evn_desc);
        //环境温度  最低
        $day_6_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_6_evn_asc = M("weather")->query($day_6_evn_asc);
        //环境温度  平均
        $day_6_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_6%'";
        $day_6_evn_avg = M("weather")->query($day_6_evn_avg);

        //环境湿度  最高
        $day_6_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_6_wet_desc = M("weather")->query($day_6_wet_desc);
        //环境湿度  最低
        $day_6_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_6_wet_asc = M("weather")->query($day_6_wet_asc);
        //环境湿度  平均
        $day_6_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_6%'";
        $day_6_wet_avg = M("weather")->query($day_6_wet_avg);

        //土壤温度  最高
        $day_6_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_6_soil_desc = M("weather")->query($day_6_soil_desc);
        //土壤温度  最低
        $day_6_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_6_soil_asc = M("weather")->query($day_6_soil_asc);
        //土壤温度  平均
        $day_6_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_6%'";
        $day_6_soil_avg = M("weather")->query($day_6_soil_avg);

        //土壤温度  最高
        $day_6_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY soil_wet DESC LIMIT 1";
        $day_6_soil_wet_desc = M("weather")->query($day_6_soil_wet_desc);
        //土壤温度  最低
        $day_6_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY soil_wet ASC LIMIT 1";
        $day_6_soil_wet_asc = M("weather")->query($day_6_soil_wet_asc);
        //土壤温度  平均
        $day_6_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_6%'";
        $day_6_soil_wet_avg = M("weather")->query($day_6_soil_wet_avg);

        //光照强度  最高
        $day_6_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_6%' ORDER BY sunlight DESC LIMIT 1";
        $day_6_sunlight_desc = M("weather")->query($day_6_sunlight_desc);


        //拼装数组
        $day_6s = array(
            'time'=> $day_6,
            'evn_desc'=>$day_6_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_6_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_6_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_6_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_6_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_6_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_6_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_6_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_6_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_6_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_6_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_6_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_6_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_6s);exit;
        $this->assign("day_6s",$day_6s);

        //5天前
        $day_5 = date("Y-m-d",strtotime("-4 day"));
        //环境温度  最高
        $day_5_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_5_evn_desc = M("weather")->query($day_5_evn_desc);
        //环境温度  最低
        $day_5_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_5_evn_asc = M("weather")->query($day_5_evn_asc);
        //环境温度  平均
        $day_5_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_5%'";
        $day_5_evn_avg = M("weather")->query($day_5_evn_avg);

        //环境湿度  最高
        $day_5_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_5_wet_desc = M("weather")->query($day_5_wet_desc);
        //环境湿度  最低
        $day_5_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_5_wet_asc = M("weather")->query($day_5_wet_asc);
        //环境湿度  平均
        $day_5_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_5%'";
        $day_5_wet_avg = M("weather")->query($day_5_wet_avg);

        //土壤温度  最高
        $day_5_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_5_soil_desc = M("weather")->query($day_5_soil_desc);
        //土壤温度  最低
        $day_5_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_5_soil_asc = M("weather")->query($day_5_soil_asc);
        //土壤温度  平均
        $day_5_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_5%'";
        $day_5_soil_avg = M("weather")->query($day_5_soil_avg);

        //土壤温度  最高
        $day_5_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY soil_wet DESC LIMIT 1";
        $day_5_soil_wet_desc = M("weather")->query($day_5_soil_wet_desc);
        //土壤温度  最低
        $day_5_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY soil_wet ASC LIMIT 1";
        $day_5_soil_wet_asc = M("weather")->query($day_5_soil_wet_asc);
        //土壤温度  平均
        $day_5_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_5%'";
        $day_5_soil_wet_avg = M("weather")->query($day_5_soil_wet_avg);

        //光照强度  最高
        $day_5_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_5%' ORDER BY sunlight DESC LIMIT 1";
        $day_5_sunlight_desc = M("weather")->query($day_5_sunlight_desc);


        //拼装数组
        $day_5s = array(
            'time'=> $day_5,
            'evn_desc'=>$day_5_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_5_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_5_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_5_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_5_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_5_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_5_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_5_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_5_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_5_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_5_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_5_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_5_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_5s);exit;
        $this->assign("day_5s",$day_5s);

        //4天前
        $day_4 = date("Y-m-d",strtotime("-3 day"));
        //环境温度  最高
        $day_4_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_4_evn_desc = M("weather")->query($day_4_evn_desc);
        //环境温度  最低
        $day_4_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_4_evn_asc = M("weather")->query($day_4_evn_asc);
        //环境温度  平均
        $day_4_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_4%'";
        $day_4_evn_avg = M("weather")->query($day_4_evn_avg);

        //环境湿度  最高
        $day_4_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_4_wet_desc = M("weather")->query($day_4_wet_desc);
        //环境湿度  最低
        $day_4_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_4_wet_asc = M("weather")->query($day_4_wet_asc);
        //环境湿度  平均
        $day_4_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_4%'";
        $day_4_wet_avg = M("weather")->query($day_4_wet_avg);

        //土壤温度  最高
        $day_4_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_4_soil_desc = M("weather")->query($day_4_soil_desc);
        //土壤温度  最低
        $day_4_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_4_soil_asc = M("weather")->query($day_4_soil_asc);
        //土壤温度  平均
        $day_4_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_4%'";
        $day_4_soil_avg = M("weather")->query($day_4_soil_avg);

        //土壤温度  最高
        $day_4_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY soil_wet DESC LIMIT 1";
        $day_4_soil_wet_desc = M("weather")->query($day_4_soil_wet_desc);
        //土壤温度  最低
        $day_4_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY soil_wet ASC LIMIT 1";
        $day_4_soil_wet_asc = M("weather")->query($day_4_soil_wet_asc);
        //土壤温度  平均
        $day_4_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_4%'";
        $day_4_soil_wet_avg = M("weather")->query($day_4_soil_wet_avg);

        //光照强度  最高
        $day_4_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_4%' ORDER BY sunlight DESC LIMIT 1";
        $day_4_sunlight_desc = M("weather")->query($day_4_sunlight_desc);


        //拼装数组
        $day_4s = array(
            'time'=> $day_4,
            'evn_desc'=>$day_4_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_4_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_4_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_4_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_4_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_4_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_4_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_4_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_4_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_4_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_4_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_4_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_4_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_4s);exit;
        $this->assign("day_4s",$day_4s);

        //3天前
        $day_3 = date("Y-m-d",strtotime("-2 day"));
        //环境温度  最高
        $day_3_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_3_evn_desc = M("weather")->query($day_3_evn_desc);
        //环境温度  最低
        $day_3_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_3_evn_asc = M("weather")->query($day_3_evn_asc);
        //环境温度  平均
        $day_3_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_3%'";
        $day_3_evn_avg = M("weather")->query($day_3_evn_avg);

        //环境湿度  最高
        $day_3_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_3_wet_desc = M("weather")->query($day_3_wet_desc);
        //环境湿度  最低
        $day_3_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_3_wet_asc = M("weather")->query($day_3_wet_asc);
        //环境湿度  平均
        $day_3_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_3%'";
        $day_3_wet_avg = M("weather")->query($day_3_wet_avg);

        //土壤温度  最高
        $day_3_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_3_soil_desc = M("weather")->query($day_3_soil_desc);
        //土壤温度  最低
        $day_3_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_3_soil_asc = M("weather")->query($day_3_soil_asc);
        //土壤温度  平均
        $day_3_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_3%'";
        $day_3_soil_avg = M("weather")->query($day_3_soil_avg);

        //土壤温度  最高
        $day_3_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY soil_wet DESC LIMIT 1";
        $day_3_soil_wet_desc = M("weather")->query($day_3_soil_wet_desc);
        //土壤温度  最低
        $day_3_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY soil_wet ASC LIMIT 1";
        $day_3_soil_wet_asc = M("weather")->query($day_3_soil_wet_asc);
        //土壤温度  平均
        $day_3_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_3%'";
        $day_3_soil_wet_avg = M("weather")->query($day_3_soil_wet_avg);

        //光照强度  最高
        $day_3_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_3%' ORDER BY sunlight DESC LIMIT 1";
        $day_3_sunlight_desc = M("weather")->query($day_3_sunlight_desc);


        //拼装数组
        $day_3s = array(
            'time'=> $day_3,
            'evn_desc'=>$day_3_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_3_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_3_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_3_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_3_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_3_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_3_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_3_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_3_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_3_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_3_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_3_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_3_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_3s);exit;
        $this->assign("day_3s",$day_3s);

        //2天前
        $day_2 = date("Y-m-d",strtotime("-1 day"));
        //环境温度  最高
        $day_2_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_2_evn_desc = M("weather")->query($day_2_evn_desc);
        //环境温度  最低
        $day_2_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_2_evn_asc = M("weather")->query($day_2_evn_asc);
        //环境温度  平均
        $day_2_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_2%'";
        $day_2_evn_avg = M("weather")->query($day_2_evn_avg);

        //环境湿度  最高
        $day_2_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_2_wet_desc = M("weather")->query($day_2_wet_desc);
        //环境湿度  最低
        $day_2_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_2_wet_asc = M("weather")->query($day_2_wet_asc);
        //环境湿度  平均
        $day_2_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_2%'";
        $day_2_wet_avg = M("weather")->query($day_2_wet_avg);

        //土壤温度  最高
        $day_2_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_2_soil_desc = M("weather")->query($day_2_soil_desc);
        //土壤温度  最低
        $day_2_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_2_soil_asc = M("weather")->query($day_2_soil_asc);
        //土壤温度  平均
        $day_2_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_2%'";
        $day_2_soil_avg = M("weather")->query($day_2_soil_avg);

        //土壤温度  最高
        $day_2_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY soil_wet DESC LIMIT 1";
        $day_2_soil_wet_desc = M("weather")->query($day_2_soil_wet_desc);
        //土壤温度  最低
        $day_2_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY soil_wet ASC LIMIT 1";
        $day_2_soil_wet_asc = M("weather")->query($day_2_soil_wet_asc);
        //土壤温度  平均
        $day_2_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_2%'";
        $day_2_soil_wet_avg = M("weather")->query($day_2_soil_wet_avg);

        //光照强度  最高
        $day_2_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_2%' ORDER BY sunlight DESC LIMIT 1";
        $day_2_sunlight_desc = M("weather")->query($day_2_sunlight_desc);


        //拼装数组
        $day_2s = array(
            'time'=> $day_2,
            'evn_desc'=>$day_2_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_2_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_2_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_2_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_2_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_2_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_2_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_2_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_2_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_2_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_2_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_2_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_2_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_2s);exit;
        $this->assign("day_2s",$day_2s);

        //1天前
        $day_1 = date("Y-m-d",strtotime("0 day"));
        //环境温度  最高
        $day_1_evn_desc = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY evnironment_temperature DESC LIMIT 1";
        $day_1_evn_desc = M("weather")->query($day_1_evn_desc);
        //环境温度  最低
        $day_1_evn_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY evnironment_temperature ASC LIMIT 1";
        $day_1_evn_asc = M("weather")->query($day_1_evn_asc);
        //环境温度  平均
        $day_1_evn_avg  = "SELECT AVG(evnironment_temperature) FROM tb_weather WHERE time LIKE '%$day_1%'";
        $day_1_evn_avg = M("weather")->query($day_1_evn_avg);

        //环境湿度  最高
        $day_1_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY evnironment_wet DESC LIMIT 1";
        $day_1_wet_desc = M("weather")->query($day_1_wet_desc);
        //环境湿度  最低
        $day_1_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY evnironment_wet ASC LIMIT 1";
        $day_1_wet_asc = M("weather")->query($day_1_wet_asc);
        //环境湿度  平均
        $day_1_wet_avg  = "SELECT AVG(evnironment_wet) FROM tb_weather WHERE time LIKE '%$day_1%'";
        $day_1_wet_avg = M("weather")->query($day_1_wet_avg);

        //土壤温度  最高
        $day_1_soil_desc = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY soil_temperature DESC LIMIT 1";
        $day_1_soil_desc = M("weather")->query($day_1_soil_desc);
        //土壤温度  最低
        $day_1_soil_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY soil_temperature ASC LIMIT 1";
        $day_1_soil_asc = M("weather")->query($day_1_soil_asc);
        //土壤温度  平均
        $day_1_soil_avg  = "SELECT AVG(soil_temperature) FROM tb_weather WHERE time LIKE '%$day_1%'";
        $day_1_soil_avg = M("weather")->query($day_1_soil_avg);

        //土壤温度  最高
        $day_1_soil_wet_desc = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY soil_wet DESC LIMIT 1";
        $day_1_soil_wet_desc = M("weather")->query($day_1_soil_wet_desc);
        //土壤温度  最低
        $day_1_soil_wet_asc  = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY soil_wet ASC LIMIT 1";
        $day_1_soil_wet_asc = M("weather")->query($day_1_soil_wet_asc);
        //土壤温度  平均
        $day_1_soil_wet_avg  = "SELECT AVG(soil_wet) FROM tb_weather WHERE time LIKE '%$day_1%'";
        $day_1_soil_wet_avg = M("weather")->query($day_1_soil_wet_avg);

        //光照强度  最高
        $day_1_sunlight_desc = "select * from `tb_weather` WHERE time LIKE '%$day_1%' ORDER BY sunlight DESC LIMIT 1";
        $day_1_sunlight_desc = M("weather")->query($day_1_sunlight_desc);


        //拼装数组
        $day_1s = array(
            'time'=> $day_1,
            'evn_desc'=>$day_1_evn_desc[0]['evnironment_temperature']/10,   //环境温度最高
            'evn_asc'=>$day_1_evn_asc[0]['evnironment_temperature']/10,     //环境温度最低
            'evn_avg'=>$day_1_evn_avg[0]['avg(evnironment_temperature)']/10,//环境温度平均

            'wet_desc'=>$day_1_wet_desc[0]['evnironment_wet']/10,      //环境湿度最高
            'wet_asc'=>$day_1_wet_asc[0]['evnironment_wet']/10,       //环境湿度最低
            'wet_avg'=>$day_1_wet_avg[0]['avg(evnironment_wet)']/10,  //环境湿度平均

            'soil_desc'=>$day_1_soil_desc[0]['soil_temperature']/10,      //土壤温度最高
            'soil_asc'=>$day_1_soil_asc[0]['soil_temperature']/10,       //土壤温度最低
            'soil_avg'=>$day_1_soil_avg[0]['avg(soil_temperature)']/10,  //土壤温度平均

            'soil_wet_desc'=>$day_1_soil_wet_desc[0]['soil_wet']/10,      //土壤湿度最高
            'soil_wet_asc'=>$day_1_soil_wet_asc[0]['soil_wet']/10,       //土壤湿度最低
            'soil_wet_avg'=>$day_1_soil_wet_avg[0]['avg(soil_wet)']/10,  //土壤湿度平均

            'sunlight_desc'=>$day_1_soil_desc[0]['sunlight']/10,          //光照强度平均
        );
//        dump($day_1s);exit;
        $this->assign("day_1s",$day_1s);
//        dump($day_1s);exit;
        $this->display();
    }

}