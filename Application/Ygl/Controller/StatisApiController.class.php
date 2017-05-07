<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 9:34
 */

namespace Ygl\Controller;
use Think\Controller;

use Admin\Model\StatisModel;
use Admin\Model\DeviceModel;
use Admin\Model\ParamModel;
use Think\Exception;

class StatisApiController extends Controller
{

    private $show_array =  array(
        0=>'soil_temperature1',
        1=>'evnironment_temperature',
        2=>'soil_wet1',
        3=>'evnironment_wet'
    );




    public function __construct()
    {

        parent::__construct();
        $this->statisModel = new StatisModel();
        $this->paramModel = new ParamModel();
        $this->deviceModel = new DeviceModel();

    }



    /*获取设备的经纬度*/
    public  function getDevicePlaceList()
    {

        $recent_table = $this->statisModel->recentTable();


//        $table_name = substr($recent_table['tablename'],-16);
//
//
//        //$temp = $this->statisModel->getOnlineDevceList($table_name);

        $where['a.delete'] = 0;

        $temp = $this->deviceModel->selectdevice($where);



        if($temp)
        {

            foreach($temp as $key=>$val)
            {
                $device_list[$key]['id'] = $val['id'];
                $device_list[$key]['device_code'] = $val['device_code'];
                $device_list[$key]['device_name'] = $val['device_name'];
                $device_list[$key]['area_name'] = $val['area_name'];
                $device_list[$key]['lat'] = $val['lat'];
                $device_list[$key]['lng'] = $val['lng'];
            }
            $result = array('ret'=>200,'data'=>$device_list,'message'=>'查询成功');

        }else{

            $result =array('ret'=>404,'data'=>array(),'message'=>'查询失败');
        }

       echo  json_encode($result);
    }



    /**
     *获取所有的设备列表
     * */

    public function  deviceList($where){
        $where['a.delete'] = 0;

        $temp = $this->deviceModel->selectdevice($where);


        if($temp) {

            foreach ($temp as $key => $val) {
                $device_list[$key]['id'] = $val['id'];
                $device_list[$key]['device_code'] = $val['device_code'];
                $device_list[$key]['device_name'] = $val['device_name'];
                $device_list[$key]['channel_number'] = $val['channel_number'];
                $device_list[$key]['area_name'] = $val['area_name'];
                $device_list[$key]['lat'] = $val['lat'];
                $device_list[$key]['lng'] = $val['lng'];
            }
        }


        return $device_list;


    }



    public function timeDataInfo()
    {
        $id = I('id');

        $where['a.id'] = $id;

        $recent_table = $this->statisModel->recentTable();
        $table_name = $recent_table['tablename'];
        $temp = $this->statisModel->realTimeData($table_name,$where);

       if($temp)
       {
           $result = array('ret'=>200,'data'=>$temp,'message'=>'查询成功');

       }else{

           $result = array('ret'=>404,'data'=>array(),'message'=>'查询失败');
       }

        $this->ajaxReturn($result);




    }




    public function  data()
    {

        $id = $_GET['id'];
        $sensorData = array();
        $deviceInfo = array();

        if($id) {
            $where = array('a.id' => $id);
            $deviceInfo =$this->deviceModel->findInsectDevice($where);
            $deviceInfo['date'] = date("Y-m-d H:i:s",time());
            $param_where['sensor_item'] = array('in',implode(',',$this->show_array));
            $param_info = $this->paramModel->selectParam($param_where);

            //判断是否每一个参数是否是超标。
            if($deviceInfo['type'] =='GREENHOUSE')
            {
                $deviceInfo['type'] ='棚室';

            }else if($deviceInfo['type'] =='OUTDOOR'){

                $deviceInfo['type'] ='室外';
            }

            if ($deviceInfo)
            {
                $where = array('deviceId' => $deviceInfo['device_code']);
                $table_info = $this->statisModel->recentTable();
                $tableName = substr($table_info['tablename'],-16);
                $tableName = 'weather_20170110';
                $data = $this->statisModel->findRealTimeData($tableName,$where);
                if($data)
                {
                    foreach($this->show_array as $key=>$val)
                    {
                        foreach($param_info as $vv)
                        {
                            if($vv['sensor_item'] == $val){

                                switch($val){

                                    case 'soil_temperature1':
                                    case 'soil_temperature2':
                                    case 'evnironment_temperature':
                                    case 'evnironment_wet':
                                    case 'soil_wet1':
                                    case 'soil_wet2':
                                        $sensorData[$key]['value']= $data[$val]/10;

                                        if( $data[$val]>40 ||  $data[$val]<-40){
                                           $sensorData[$key]['value']= 0;
                                        }

                                        break;
                                    default:
                                        $sensorData[$key]['value']= $data[$val];
                                }

                                $sensorData[$key]['max']= $vv['max'];
                                $sensorData[$key]['sensor_item']= $val;
                                $sensorData[$key]['min']= $vv['min'];
                                $sensorData[$key]['param_name']= $vv['param_name'];
                                $sensorData[$key]['param_unit']= $vv['param_unit'];
                                $sensorData[$key]['china_unit']= $vv['china_unit'];
                                $sensorData[$key]['param_unit']= $vv['param_unit'];

                            }

                        }

                    }

                }else{

                    foreach($this->show_array as $key=>$val)
                    {
                        foreach($param_info as $vv)
                        {
                            if($vv['sensor_item'] == $val){

                                $sensorData[$key]['value']= 0;
                                $sensorData[$key]['max']= $vv['max'];
                                $sensorData[$key]['sensor_item']= $val;
                                $sensorData[$key]['min']= $vv['min'];
                                $sensorData[$key]['param_name']= $vv['param_name'];
                                $sensorData[$key]['param_unit']= $vv['param_unit'];
                                $sensorData[$key]['china_unit']= $vv['china_unit'];
                                $sensorData[$key]['param_unit']= $vv['param_unit'];
                            }

                        }

                    }

                }

            }

        }



        $this->assign('time',date('Y-m-d H:i:s'));
        $this->assign("deviceInfo",$deviceInfo);
        $this->assign("sensorData",$sensorData);
        $this->display('data');



    }



/*获取视频数据*/
    public function getDeviceInfo()
    {
        $id = I('id');
        $where = array('a.id' => $id);
        $deviceInfo =$this->deviceModel->findOneDevice($where);
        $this->assign('deviceInfo',$deviceInfo);
        $this->display('video');

    }

    /**
     * 有条件的获取设备列表
     *
     * @var int  $level   区域等级
     * @var int  $id     区域id
     * @var int  $id     区域id
     *
     * */


    public function postDevicePlaceList()
    {
        $id = I('id');
        $level = I('level');
        $type = I('type');
        $areaString = '';


        if($type){

            $where['a.type'] = $type;
        }

        $areaModel = new \Admin\Model\AreaModel();

        switch ($level) {

            case '1':
                $areaArray = $areaModel->firstThirdArea(array('c.id'=>$id));
                break;
            case '2':
                $areaArray = $areaModel->secondThirdArea(array('b.id'=>$id));
                break;
            case '3':
                $where['a.area_id'] = $id;
                break;
            default:

                $where['_string'] = '1=1';
                break;

        }


        if ($areaArray) {
            foreach($areaArray as $val){

                $areaString.= $val['id'].',';
            }

            $areaString = trim($areaString,',');
            $where['a.area_id'] = array('in',$areaString);
        }

        $deviceList = $this->deviceList($where);


        if($deviceList)
        {
            $this->ajaxReturn(array('ret'=>200,'data'=>$deviceList,'message'=>'查询成功'));

        }else{

            $this->ajaxReturn(array('ret'=>204,'data'=>array(),'message'=>'数据为空'));
        }


    }


    /**
     *获取区列表区域列表
     * 
     * @return json 
     * */
    public function getAreaList()
    {

        $areaModel= new \Admin\Model\AreaModel();
        $areaList = $areaModel->areaAllList();

        if ($areaList) {

            $this->ajaxReturn(array('ret'=>200,'data'=>$areaList,'message'=>'查询成功'));

        }else{

            $this->ajaxReturn(array('ret'=>204,'data'=>array(),'message'=>'查询失败'));
        }
        


    }


    /**
     * 跳转详情页
     * @var  int id
     * */
   /* public function timeDataInfo2()
    {

        $deviceModel = new DeviceModel();
        $id = $_GET['id'];
        $device_code = $_GET['device_code'];
        $sensor_item = I('sensor_item');
        $sensor_item = $sensor_item ? $sensor_item : 'evnironment_temperature';



        $sensorData = array();
        $deviceInfo = array();

        if ($id || $device_code) {

            if ($id) {
                $where = array('a.id' => $id);

            } elseif ($device_code) {

                $where['a.device_code'] = $device_code;

            }



            $deviceInfo = $deviceModel->findOneDevice($where);


            $deviceInfo['date'] = date("Y-m-d H:i:s", time());
            $param_where['sensor_item'] = array('in', implode(',', $this->show_array));
            $param_info = $this->paramModel->selectParam($param_where);



            //判断是否每一个参数是否是超标。
            if ($deviceInfo['type'] == 'GREENHOUSE') {
                $deviceInfo['type'] = '棚室';

            } else if ($deviceInfo['type'] == 'OUTDOOR') {

                $deviceInfo['type'] = '室外';
            }

            if ($deviceInfo) {
                $where = array('deviceId' => $deviceInfo['device_code']);

                $table_info = $this->statisModel->recentTable();
                $tableName = substr($table_info['tablename'], -16);

                $data = $this->statisModel->findRealTimeData($tableName, $where);


                if ($data) {

                    $lat = $this->latAndLng($data['lat']);
                    $lng = $this->latAndLng($data['lng']);
                    $windDirecetion = $this->windDirection($data['wind_direction']);

                    foreach ($this->show_array as $key => $val) {

                        foreach ($param_info as $vv) {

                            if ($vv['sensor_item'] == $val) {

                                switch ($val) {

                                    case 'soil_temperature1':
                                    case 'soil_wet1':
                                        $sensorData[$key]['value'] = $data[$val] / 10;

                                        if ($sensorData[$key]['value'] > 65 || $sensorData[$key]['value'] < -65) {
                                            $sensorData[$key]['value'] = 0;
                                        }
                                        break;

                                    case 'soil_temperature2':
                                    case 'soil_wet2':
                                        $sensorData[$key]['value'] = $data[$val] / 10;

                                        if ($sensorData[$key]['value'] > 65 || $sensorData[$key]['value'] < -65) {
                                            $sensorData[$key]['value'] = 0;
                                        }
                                        break;
                                    case 'evnironment_temperature':
                                        $sensorData[$key]['value'] = $data[$val] / 10;

                                        if ($sensorData[$key]['value'] > 65 || $sensorData[$key]['value'] < -65) {
                                            $sensorData[$key]['value'] = 0;
                                        }

                                        break;
                                    case 'evnironment_wet':
                                        $sensorData[$key]['value'] = $data[$val] / 10;
                                        break;

                                    case 'air_press':
                                        $sensorData[$key]['value'] = $data[$val] / 10;
                                        break;

                                    case "lat":

                                        $sensorData[$key]['value'] = $lat . 'N,' . $lng . 'E';

                                        break;
                                    case "lng":
                                        $sensorData[$key]['value'] = $lat . 'N,' . $lng . 'E';
                                        break;

                                    case "wind_direction":
                                        $sensorData[$key]['value'] = $windDirecetion;

                                        break;
                                    case "wind_speed":



                                        if ($data[$val] > 30 || $data[$val] < 0) {
                                            $sensorData[$key]['value'] = 4;
                                        }else{

                                            $sensorData[$key]['value'] = $data[$val];

                                        }
                                        break;

                                    default:
                                        $sensorData[$key]['value'] = $data[$val];
                                }

                                $sensorData[$key]['max'] = $vv['max'];
                                $sensorData[$key]['sensor_item'] = $val;
                                $sensorData[$key]['min'] = $vv['min'];
                                $sensorData[$key]['param_name'] = $vv['param_name'];
                                $sensorData[$key]['param_unit'] = $vv['param_unit'];
                                $sensorData[$key]['china_unit'] = $vv['china_unit'];
                                $sensorData[$key]['param_unit'] = $vv['param_unit'];


                                if($sensor_item == $val)
                                {
                                    $time_data = $sensorData[$key];
                                }

                            }


                        }

                    }


                } else {

                    foreach ($this->show_array as $key => $val) {
                        foreach ($param_info as $vv) {
                            if ($vv['sensor_item'] == $val) {
                                switch ($val) {

                                    case "lat":
                                        $sensorData[$key]['value'] = '0N,0E';
                                        break;
                                    case "lng":
                                        $sensorData[$key]['value'] = '0N,0E';
                                        break;
                                    default:
                                        $sensorData[$key]['value'] = 0;
                                }
                                $sensorData[$key]['max'] = $vv['max'];
                                $sensorData[$key]['sensor_item'] = $val;
                                $sensorData[$key]['min'] = $vv['min'];
                                $sensorData[$key]['param_name'] = $vv['param_name'];
                                $sensorData[$key]['param_unit'] = $vv['param_unit'];
                                $sensorData[$key]['china_unit'] = $vv['china_unit'];
                                $sensorData[$key]['param_unit'] = $vv['param_unit'];

                                if($sensor_item == $val)
                                {
                                    $time_data = $sensorData[$key];
                                }

                            }


                        }

                    }


                }

            }


        }


        //获取设备通道号

        foreach ($sensorData as $key => $val) {

            if ($val['sensor_item'] == 'lng') {
                unset($sensorData[$key]);

            }

            if ($val['sensor_item'] == $sensor_item) {
                $time_data = $sensorData[$key];

            }

            if ($sensor_item == 'lat') {

                $time_data['lat'] = $lat . 'N';
                $time_data['lng'] = $lng . 'E';

            }


        }




        //获取病虫害信息
//
//        if ($insects_device) {
//
//            $where['device_id'] = $insects_device['id'];
//
//            $insect_info = $this->insectsInfoModel->selectInsectsInfo($where);
//
//            if ($insect_info) {
//                foreach ($insect_info as $ey => $val) {
//                    $insect_info[$ey]['comment'] = htmlspecialchars_decode($val['comment']);
//
//                }
//
//
//            }
//
//        }


        //获取历史数据
        $end = date('Y-m-d', time());
        $start = date("Y-m-d", strtotime("-1 year"));


        $year_data = $this->getOneSensorItem($start, $end, $sensor_item, $deviceInfo['device_code']);

        //获取一个周，一个月，半年，一年的数据




        if ($year_data) {

            $week = strtotime("-1 week");
            $month = strtotime("-1 month");
            $halfYear = strtotime("-6 month");


            foreach ($year_data as $val) {
                $time = strtotime($val['date']);
                if ($time > $halfYear) {

                    if ($time > $week) {

                        $weekData[] = $val;
                    }

                    if ($time > $month) {

                        $mouthData[] = $val;

                    }

                    $halfYearData[] = $val;

                }


            }


        }


        $this->assign('time', date('Y-m-d H:i:s'));
        $this->assign('level_one_id', 1);
        $this->assign("deviceInfo", $deviceInfo);
        $this->assign('insect_info', $insect_info);
        $this->assign("insects_device", $insects_device);
        $this->assign('yearData', $year_data);
        $this->assign('weekData', $weekData);
        $this->assign('mouthData', $mouthData);
        $this->assign('halfYearData', $halfYearData);
        $this->assign('id', $id);
        $this->assign('sensor_item', $sensor_item);
        $this->assign('time_data', $time_data);
        $this->assign("sensorData", $sensorData);


        $this->display('detail');

    }*/



    public function getOneSensorItem($start, $end, $sensor_item, $device_code)
    {

        // $sensor_item = 'soil_temperature1';
        // $device_code = '2016091085110053';

        $list = array();

        $sensor_where['date'] = array(array('egt', $start), array('elt', $end));
        $sensor_where['b.sensor_item'] = $sensor_item;
        $sensor_where['a.device_code'] = $device_code;



        $statis_info = $this->statisModel->selectStatisDataOneItem($sensor_where);

        $table_name = 'weather_' . (date('Ymd'));
        //获取设备 的设备号

        
        $device_array = $this->statisModel->selectDeviceCode($table_name);

       

        if ($statis_info) {
            //evnironment_temperature 环境温度

            foreach ($statis_info as $val) {

                $count = count($list);
                $list[$count]['date'] = $val['date'];
                $list[$count]['max'] = $val['max'];
                $list[$count]['avg'] = $val['avg'];
                $list[$count]['min'] = $val['min'];
                $list[$count]['param_name'] = $val['param_name'];
                $list[$count]['param_unit'] = $val['param_unit'];
            }           

            $w_where['deviceId'] = $device_code;
            $max = $this->statisModel->selectMaxData($table_name, $sensor_item, $w_where);
            
            $min = $this->statisModel->selectMinData($table_name, $sensor_item, $w_where);
            $avg = $this->statisModel->selectAvgData($table_name, $sensor_item, $w_where);
            $count = count($list);
            $list[$count]['date'] = date("Y-m-d", time());;
            $list[$count]['max'] = $val['max'];
            $list[$count]['avg'] = $val['avg'];
            $list[$count]['min'] = $val['min'];
            $list[$count]['avg'] = $val['avg'];


        } else {

            $end_time = strtotime($end);
            $start_time = strtotime($start);

            $week_num = ($end_time - $start_time) / (60 * 60 * 24);
            for ($i = $start_time; $i < $start_time; $i + 60 * 60 * 24) {

                $list[$key]['data'][$num]['date'] = date('Y-m-d H:i:s', $i);
                $list[$key]['data'][$num]['max'] = 0;
                $list[$key]['data'][$num]['min'] = 0;
                $list[$key]['data'][$num]['avg'] = 0;
                $num = $num + 1;
            }


        }



        return $list;

    }

    public function latAndLng($value)
    {
        $latAndLng = "暂无";

        if ($value) {

            $temp = explode('.', $value);

            $latAndLng = $temp[0];
            $temp = explode('.', ($value - $latAndLng) * 60);

            $minute = $temp[0];
            $second = (($value - $latAndLng) * 60 - $minute) * 60;

            $second = round($second, 0);
            $latAndLng = $latAndLng . '°' . $minute . "′" . $second . '"';

        }

        return $latAndLng;


    }

    public function windDirection($value)
    {

        $result = '暂无';

        if ($value < 23 && $value > 0) {
            $result = '北风';

        } elseif ($value < 68 && $value > 23) {

            $result = '东北风';


        } elseif ($value < 113 && $value > 68) {

            $result = '东风';


        } elseif ($value < 158 && $value > 113) {

            $result = '东南风';

        } elseif ($value < 203 && $value > 158) {

            $result = '南风';


        } elseif ($value < 248 && $value > 203) {

            $result = '西南风';

        } elseif ($value < 293 && $value > 248) {

            $result = '西风';

        } elseif ($value < 338 && $value > 293) {

            $result = '西北风';

        } elseif ($value > 338) {

            $result = '北风';
        }

        return $result;


    }


    public function getExpertReportInfo()
    {

        //获取获取领域的服务人数

        $end   = date('Y-m-d H:i:s',time());
        $start =  date('Y-m-d H:i:s',strtotime("-1 year"));
        $weekarray = array();
        $weekTable = array();


        $where['a.addtime'] = array(array('egt', $start), array('elt', $end));

        $commentArray = $this->selectComment($where);

        //获取领域
        $domain = M('domain')->where(array('display'=>'1'))->select();

        if($commentArray)
        {

            //一年的数据
            foreach($domain as $key=>$val)
            {
                $domainData[$key]['domainName'] = $val['name'];

                foreach($commentArray as $kk=>$vv)
                {

                    if($val['id'] == $vv['did'])
                    {
                        $domainData[$key]['date'][] = date("Y-m-d",strtotime($vv['addtime']));

                    }
                }

            }

        }

        //一年的数组

        $startTime = strtotime("-1 year");  //获取本月第一天时间戳

        $i=1;
        do{
            $temp = $startTime+$i*86400;
            $timeArray[] = date('Y-m-d',$temp);
            $i+=1;

        }while($temp<time());

        $weekTime = date('Y-m-d',strtotime('-1 week'));
        $mouthTime = date('Y-m-d',strtotime('-1 month'));




        if($domainData){

            foreach($domainData  as $key=>$val)
            {

                $weekData[$key]['domainName'] = $val['domainName'];
                $mouthData[$key]['domainName'] = $val['domainName'];
                $yearData[$key]['domainName'] = $val['domainName'];
                sort($val['date']);
                foreach($timeArray as $kk=> $vv)
                {
                    $num = 0;

                    foreach($val['date'] as $vk)
                    {
                        if(strtotime($vv) == strtotime($vk)){
                            $num = $num+1;
                        }

                    }

                    if(strtotime($vv)>strtotime($weekTime)){

                        $weekarray[] = $vv;
                        $weekData[$key]['data'][$kk]['date'] = $vv;
                        $weekData[$key]['data'][$kk]['num'] = $num;

                    }

                    if(strtotime($vv) > strtotime($mouthTime)){

                        $mouthData[$key]['data'][$kk]['date'] = $vv;
                        $mouthData[$key]['data'][$kk]['num'] = $num;
                    }

                    $yearData[$key]['data'][$kk]['date'] = $vv;
                    $yearData[$key]['data'][$kk]['num'] = $num;


                }






            }


        }

        if($weekData)
        {

            foreach($weekarray as $key=> $val)
            {
                foreach($weekData as $vv)
                {

                    foreach($vv['data'] as $vk)
                    {

                        $count = count($weekTable[$key]['data']);
                        if($val == $vk['date'] ){
                            $weekTable[$key]['date'] = $val;
                            $weekTable[$key]['data'][$count]['num'] = $vk['num'];
                            $weekTable[$key]['data'][$count]['domainName'] = $vv['domainName'];

                        }

                    }

                }


            }


        }




        $this->assign('weekTable',$weekTable);
        $this->assign('weekData',$weekData);
        $this->assign('mouthData',$mouthData);
        $this->assign('yearData',$yearData);
        $this->display('expertDetail');


    }


    public function selectComment($where)
    {
        //获取获取领域的服务人数
        $commentArray = M('expert_comment as a')
            ->field('a.addtime,did')
            ->join('sn_expert as b on a.expert_id = b.eid')
            ->select();

        return $commentArray;

    }


    public function  getLatLng(){

        $id = I('id');
        $where['id'] = $id;
        $result = M('device')->where($where)->find();

        if($result){
            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'messgae'=>'查询成功'));

        }else{
            $this->ajaxReturn(array('ret'=>404,'data'=>$result,'messgae'=>'查询成功'));

        }


    }











}
