<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/21
 * Time: 14:44
 */

namespace Common\Model;
use Think\Model;

class WeatherModel extends Model {

    protected $connection = 'DB_DEVICE';
    protected $trueTableName = 'tb_weather';

    //获取监控数据
    public function getData($devices, $field = '*'){
        $sql = 'select '.$field.' from
        (select '.$field.' from tb_weather order by id desc) tb
        where deviceid
        in ('.$devices.')
        group by deviceid';
        return $this->query($sql);
    }

    //根据部门获取监控数据
    public function getDataByDept($dept, $field = '*'){
        //获取设备号
        $where = array('dept' => intval($dept));
        $devices = $this->getDevice($where);
        if(empty($devices)){
            return false;
        }
        $device_str = '';
        foreach($devices as $val){
            $device_str .= $val['deviceid'].',';
        }
        $device_str = substr($device_str, 0, -1);
        $data = $this->getData($device_str, $field);
        foreach($data as $key => $val){
            foreach($devices as $k => $v){
                if($val['deviceid'] == $v['deviceid']){
                    $data[$key]['name'] = $v['name'];
                    $data[$key]['lng'] = $v['lng'];
                    $data[$key]['lat'] = $v['lat'];
                    unset($devices[$k]);
                    break;
                }
            }
        }
        return $data;
    }

    //获取设备信息
    public function getDevice($where = array()){
        return M('Device')->where($where)->select();
    }

    //根据设备号获取数据信息
    public function getDataByDeviceid($deviceid, $stime, $etime, $field = '*'){
        $sql = 'select '.$field.',left(time, 13) t_hour from tb_weather
                where time between "'.$stime.'" and "'.$etime
                .'" and deviceid = "'.$deviceid.'" group by t_hour';
        return $this->query($sql);
    }
}