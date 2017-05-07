<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/10
 * Time: 13:31
 */

namespace Admin\Model;

use Admin\Model\BaseModel;
use Think\Exception;

class StatisModel extends BaseModel
{


    /*
    * 添加一个区域区域
    * @param array $where 更新条件
    * @param array $data 更新条件
    */
    public function addStatis($data)
    {

        $this->add('statis',$data);


    }


    public function  getRecentTable()
    {

        $result = $this->db(2,'DB_CONFIG2')->table("sn_tablelog")->order('time desc')->find();


        return $result;



    }


    /*
   * 获取 实时数据 现在先获取 数据库 最新的数据
   * @param array $where 条件
   * @reurn array $result 返回数组
   * */
    public function  findRealTimeData($table_name,$where)
    {

        /*获取当前的最新的数据表*/

        if($where)
        {


            $table_info = $this->db(2,'DB_CONFIG2')->table('tb_tablelog')->where(array('tablename'=>'tb_'.$table_name))->find();

            if($table_info){

                $result = $this->db(2,'DB_CONFIG2')->table('tb_'.$table_name)->where($where)->order('insert_time')->find();

            }else{

                throw new Exception('今天没有产生表');
            }


        }else{

            throw new Exception('查询实时数据 的条件不能为空');
        }


        return $result;




    }



    public function selectSensor($tableName)
    {
        $result = array();

        $result = M($tableName.' a')->join('tb_device b on a.deviceId = b.device_code')
                                    ->field('a.deviceId,b.id')
                                    ->group('a.deviceId')
                                    ->select();

        return $result;


    }



    /*查询一天的数据统计*/

    public function selectStatisData($where)
    {
        $statis_array = M('statis')->where($where)->select();

        return $statis_array;

    }

    public function selectStatisDataOneItem($where)
    {

        $statis_array = M('statis as a')
            ->field('a.id aid,b.id bid,a.avg,a.min,a.max,a.date,b.param_name,b.param_unit,a.device_code')
            ->join('left join  sn_param as b on a.param_id = b.id')
            ->where($where)
            ->select();

        return $statis_array;

    }



    public function selectDeviceCode($tableName)
    {
        try{


            $result  = $this->db(2,'DB_CONFIG2')->table('$tableName')->field('deviceId as deviceCode')->group('deviceId')->select();




        }catch(Exception $e){

            throw new Exception('数据查询失败'.$e->getMessage());



        }




        return $result;



    }


    //获取当前的最近的表
    public function recentTable()
    {
        $result = $this->db(2,'DB_CONFIG2')->table("tb_tablelog")->order('time desc')->find();



        return $result;


    }


    public function selectSensorData($table,$where,$num)
    {

     
        $subSql =  M($table.' as a')
            ->field('a.*,b.id as bid')
            ->where($where)
            ->join('tb_device as b on a.deviceid = b.device_code')
            ->order('insert_time desc')
            ->limit(0,$num)
            ->buildSql();


        $result =  M()->query('select *,COUNT(deviceId) as num from  '.$subSql.' `temp` group by deviceId order by insert_time asc');




        return $result;

    }



    //获取设备的

    public function getDeviceAlarm($where)
    {


        $result = M('sensor_alarm a')
            ->field('a.*,b.sensor_item')
            ->join('tb_param b on a.sensor_param_id=b.id')->where($where)->select();

        return $result;


    }


    //更新传感器每一个参数状态

    public  function  updateSensorAlarm($where,$data)
    {

        if($where && $data)
        {

            try{


                 $this->update('sensor_alarm',$where,$data);



            }catch(Exception $e){


                throw new Exception($e->getMessage());



            }


        }else{

            throw new Exception('更新参数警告标的条件和数据都要为真');



        }


    }



    //更新传感器每一个参数状态

    public  function  updateOnlineAlarm($where,$data)
    {

        if($where && $data)
        {

            try{

                $this->update('online_alarm',$where,$data);



            }catch(Exception $e){


                throw new Exception($e->getMessage());



            }


        }else{

            throw new Exception('更新设备在线警告的条件和数据都要为真');



        }


    }




    public function getOnlineDevceList($realTimeTable)
    {

        if($realTimeTable){

            try{

                $result = M($realTimeTable.' as a ')
                    ->field("b.id,b.device_name,deviceId,a.lng as lng ,a.lat as lat,c.area_name")
                    ->join('tb_device AS b ON b.device_code = a.deviceId')
                    ->join('tb_area AS c ON b.area_id = c.id')
                    ->group(' deviceId')->select();


            }catch (Exception $e){

                echo $e->getMessage();
            }

            return $result;

        }else{
            throw new Exception('实时数据表不能为空');
        }



    }


    public function realTimeData($realTimeTable,$where)
    {

        /*获取当前的最新的数据表*/

        $result = array();
        if($where)
        {

                $result = M('device  as a ')
                    ->field('a.device_name,a.video_url,d.video_url as insert_video,b.*,c.area_name')
                    ->join($realTimeTable.' as b on a.device_code=b.deviceId')
                    ->join('tb_area as c on c.id=a.area_id')
                    ->join(' left join tb_device as d on d.parent_id=a.id')
                    ->where($where)
                    ->order('insert_time')
                    ->find();


        }else{

            throw new Exception('查询实时数据 的条件不能为空');
        }


        return $result;


    }



    public function getFieldAvg($wehre)
    {

      $avg = 0;

      if($wehre)
      {
          $avg =   M('statis')->where($wehre)->avg('avg');

      }

      return $avg;


    }

    public function getFieldMax($wehre)
    {

      $max = 0;

      if($wehre)
      {
          $max =   M('statis')->where($wehre)->max('max');

      }

      return $max;


    }


    public function getFieldMin($wehre)
    {

      $min = 0;

      if($wehre)
      {
          $min =   M('statis')->where($wehre)->min('min');

      }

      return $min;


    }


    /*获取某个字段的最大值*/
    public function selectMaxData($table,$fields,$where)
    {

        $result = array();

        if($fields)
        {
            $result = $result = $this->db(2,'DB_CONFIG2')->table('tb_'.$table)->where($where)->max($fields);

        }

        echo print_r($result);

        return $result;



    }


    /*获取某个字段的最小值*/
    public function selectMinData($table,$fields,$where)
    {



        $result = array();

        if($fields)
        {
            $result = $this->db(2,'DB_CONFIG2')->table('tb_'.$table)->where($where)->min($fields);


        }

        return $result;



    }


    /*获取平均值*/
    public function selectAvgData($table,$fields,$where)
    {

        $result = array();

        if($fields)
        {
            $result = $this->db(2,'DB_CONFIG2')->table('tb_'.$table)->where($where)->avg($fields);

        }

        return $result;


    }
























}
