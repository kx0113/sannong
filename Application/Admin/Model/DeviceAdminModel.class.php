<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/23
 * Time: 17:42
 */

namespace Admin\Model;


use Admin\Model\BaseModel;
use Think\Exception;

class DeviceAdminModel extends BaseModel
{

    private  $pageSize = 10;


    public function getAdminConditory($where,$p)
    {
        $result = array();

        if($where && $p)
        {
            $deviceAdmin = M('device_admin');
            $offset = $this->pageSize;
            $start = ($p-1)*$offset ;
            $count = $deviceAdmin->where($where)->count();

            $where['delete'] = 0;

            if(ceil($count/$offset) >= $p)
            {
                $list  = $deviceAdmin->where($where)->limit($start,$offset)->select();


                $result = $this->page($list,$count,$offset);

            }


        }else{

            throw new Exception('搜索管理设备管理员where 条件和页码p都要为真');
        }

        return $result;

    }


    /*添加管理员 */
    public function addAdmin($data)
    {


        $result = $this->add('device_admin',$data);



        return  $result;

    }


    /*删除设备管理员*/
    public function delAdmin($where)
    {

        $result = $this->delete('device_admin',$where);


         return $result;

    }


    /*更新设备管理员*/
    public function updateAdmin($where,$data)
    {

        $result = $this->update('device_admin',$where,$data);
        return $result;

    }


     /*获取一个管理员的信息*/
    public function getAdminInfo($where)
    {

        if($where)
        {
            $result = $this->getOneInfo('device_admin',$where);

        }else{

            throw new Exception('$where 不能为空');

        }

        return $result;


    }


    public function getAdminList()
    {
        $where['delete'] = array('neq','1');

        $result = M('device_admin')->where($where)->select();

        return $result;


    }



    //获取设备管理员的数目


    public function getAdminNum($where)
    {
      $num = 0;

      if($where){

        $where['delete'] = 0;
        $num =  M('device_admin')->where($where)->count();

      }

      return $num;

    }




}
