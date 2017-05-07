<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/10
 * Time: 14:44
 */

namespace Admin\Model;

use Admin\Model\BaseModel;
use Think\Exception;


class deviceModel extends BaseModel
{

    private $pageSize =10;
    /*
     * 添加设备
     * @param array $data 设备信息
     */
    public function adddevice($data)
    {
        if(!$data)
        {
            throw new Exception('$data 数据不能为空');

        }else{

            $this->add('sn_device',$data);
        }

    }

    /*
    * 更新设备
    * @param array $data 更改备信息
    * @param array $where 更新条件
    */
    public function updatedevice($where,$data)
    {
        $result = array();
        if($where && $data)
        {
            $result = $this->update('device',$where,$data);

        }else{

            throw new Exception('$data 和 $where数据不能为空');

        }
        return $result;




    }


    /*
    * 更新设备
    * @param array $where 更新条件
    */
    public function deletedevice($where)
    {
        if($where)
        {

            $this->delete('device',$where);

        }else{

            throw new Exception('$where数据不能为空');
        }


    }

    /*
    * 查找设备
    * @param array $where 查找条件
    */
    public function findOnedevice($where)
    {
        $result = array();
        if($where)
        {
            $result = M('device a ')
                ->field('a.*,b.area_name,c.name,c.phone')
                ->join('left join sn_area as b on a.area_id=b.id ')
                ->join('left join sn_device_admin as c on a.admin_id=c.id ')
                ->where($where)->find();
        }else{

            throw new Exception('$where数据不能为空');

        }




        return $result;


    }


    public function findInsectdevice($where)
    {
        $result = array();
        if($where)
        {
            $result = M('device a ')
                ->field('a.*,b.area_name,c.name,c.phone')
                ->join('sn_area as b on a.area_id=b.id ')
                ->join('sn_device_admin as c on a.admin_id=c.id ')
                ->where($where)->find();

        }else{

            throw new Exception('$where数据不能为空');

        }

        return $result;


    }

    /*
    * 查找多台设备
    * @param array $where 查找条件
    */
    public function selectdevice($where)
    {

        if($where)
        {

            $result = M('device as a')->field('a.device_name,a.channel_number,a.id,a.lat,a.lng,a.device_code,b.area_name')
                ->join('sn_area as b on a.area_id=b.id ')
                ->where($where)
                ->select();


        }else{

            throw new Exception('$where数据不能为空');

        }

        return $result;

    }


    /*获取设备的list*/
    public function getdeviceByCondition($where,$p)
    {

        $offset = $this->pageSize;
        $start  = $offset*($p-1);

        $result = array();
        $list = array();

        if($where && $p)
        {

            $count  = M('device a')->where($where)->count();
            $where['a.delete'] = 0;
            if(ceil($count/$offset) >=$p )
            {

                $list = M('device as a')->field('a.*,b.area_name,c.online,d.name,d.phone')
                    ->join('sn_area as b on a.area_id=b.id ')
                    ->join('sn_online_alarm as c on a.id=c.device_id')
                    ->join('left join sn_device_admin as d on a.admin_id=d.id')
                    ->where($where)
                    ->order('c.online desc')
                    ->limit($start,$offset)
                    ->select();

                $result =$this-> page($list,$count,$offset);


            }

        }else{

            throw new Exception('搜索监测设备的where 条件和页码p都要为真');
        }




        return $result;

    }


    public function getdeviceByCondition2($where)
    {

        $result = array();
        $list = array();


        if( $where )
        {
            $where['a.delete'] = 0;

            $list = M('device as a')->field('a.*,b.area_name,c.online,d.name,d.phone,a.lat,a.lng,a.altitude')
                ->join('sn_area as b on a.area_id=b.id ')
                ->join('sn_online_alarm as c on a.id=c.device_id')
                ->join('left join sn_device_admin as d on a.admin_id=d.id')
                ->where($where)
                ->order('c.online desc')
                ->select();


            $result['list']  = $list;

        }else{

            throw new Exception('搜索监测设备的where 条件和页码p都要为真');
        }




        return $result;

    }



    public function getInsectsByCondition($where,$p)
    {

        $offset = $this->pageSize;
        $start  = $offset*($p-1);

        $result = array();
        $list = array();

        if($where && $p)
        {

            $count  = M('device')->where($where)->count();

            $where['a.delete'] = 0;
            if(ceil($count/$offset) >=$p )
            {

                $list =  M('device as a')->field('a.*,b.area_name,d.name,d.phone')
                    ->join('sn_area as b on a.area_id=b.id ')
                    ->join('left join  sn_device_admin as d on a.admin_id=d.id')
                    ->where($where)
                    ->limit($start,$offset)
                    ->select();


                $result =$this-> page($list,$count,$offset);

            }

        }else{

            throw new Exception('搜索监测设备的where 条件和页码p都要为真');
        }


        return $result;

    }




    /*获取设备的list*/
    public function getAlarmByCondition($where,$p)
    {

        $offset = $this->pageSize;
        $start  = $offset*($p-1);

        $result = array();
        $list = array();      

        $where['a.delete'] = 0;
        $where['alarm'] = 1;

        if($where && $p)
        {

            $count  = M('device a ')
                ->join('sn_online_alarm as c on a.id=c.device_id')
                ->where($where)->count();

            if(ceil($count/$offset) >=$p )
            {


                $list = M('device as a')->field('a.*,b.area_name,c.online,c.alarm')
                    ->join('sn_area as b on a.area_id=b.id ')
                    ->join('sn_online_alarm as c on a.id=c.device_id')
                    ->where($where)
                    ->limit($start,$offset)
                    ->select();

                $result =$this-> page($list,$count,$offset);


            }

        }else{

            throw new Exception('搜索监测设备的where 条件和页码p都要为真');
        }


        return $result;

    }



    public function getAlarmByCondition2($where)
    {

        $result = array();      
        
        $where['a.delete'] = 0;
        $where['alarm'] = 1;

        if($where)
        {



                $list = M('device as a')->field('a.*,b.area_name,c.online,c.alarm')
                    ->join('sn_area as b on a.area_id=b.id ')
                    ->join('sn_online_alarm as c on a.id=c.device_id')
                    ->where($where)
                    ->select();

                $result['list']  = $list;



        }else{

            throw new Exception('搜索监测设备的where 条件要为真');
        }


        return $result;

    }

    // 获取某个设备的
    public function getdeviceParam($where)
    {
      $result = array();
      if($where)
      {

          $result =  M('sensor_alarm as a')
          ->field('a.id,a.max as max_value,a.min as min_value,a.show,b.max,b.min,b.param_name,b.param_unit')
          ->join('tb_param as b on a.sensor_param_id= b.id')
          ->where($where)
          ->select();
        }



     return $result;


    }



    /*
     * 获取设备的类型
     * */


    public function getdeviceType()
    {
        $result = array();

        $result = M('device')->field('type')->group('type')->select();

        return $result;


    }


    /*
    * 获取设备的类型
    * */

    public function getdeviceAdmin()
    {
        $result = array();

        $result = M('device')->field('admin_name')->where("admin_name <> ''")->group('admin_name')->select();


        return $result;

    }



    /*获取病虫害设备*/
    public function getInsectsdevice($where)
    {

        $result =  array();


        if($where)
        {
            $where['a.delete'] = 0;
            $result =  M('device a')
                ->field('a.*,b.id as bid,b.device_name as bname,b.device_code as bcode,c.area_name,b.new_name as bnewName')
                ->join('left join sn_area as c on a.area_id=c.id ')
                ->where($where)
                ->find();

        }

        return $result;
    }



    public function updatedeviceParam($where,$data)
    {
         $result = array();

        if($where && $data)
        {

            try{

               $result =  $this->update('sensor_alarm',$where,$data);


            }catch(Exception $e){


                throw new Exception($e->getMessage());


            }


        }

       return $result;



    }



    public function getdeviceNum($where)
    {
      $num = 0;

      if($where){

        $where['delete'] = 0;
        $num =  M('device')->where($where)->count();

      }

      return $num;

    }


   public function selectAllData()
   {



     $result = M('device a ')
         ->field('a.*,b.area_name')
         ->join('sn_area as b on a.area_id=b.id ')
         ->select();


        return $result;

   }









}
