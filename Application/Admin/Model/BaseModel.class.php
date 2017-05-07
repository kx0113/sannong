<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/10
 * Time: 15:18
 */

namespace Admin\Model;

use Think\Exception;
use Think\Model;
class BaseModel extends  Model
{

    /*
    * 添加记录
    * @param string $tableName 表名
    * @param array   $data 要添加的数据
    * @return boolean $result 返回布尔值
    */
    public function add($tableName,$data)
    {


        $result = false;
        if ($tableName && is_array($data))
        {

          $result = M($tableName)->data($data)->add();
        }

      

        return $result;

    }


    /*
    * 更新记录
    * @param string $tableName 表名
    * @param array   $data 要添加的数据
    * @return boolean $result 返回布尔值
    */
    public function update($tableName,$where,$data)
    {
        $result = false;
        if ($tableName && is_array($where) && is_array($data))
        {


            $result = M($tableName)->where($where)->save($data);

           
        }

        return $result;


    }

    /*
    * 获取单条记录
    * @param string $tableName 表名
    * @param array   $where 要添加的数据
    * @return array  $result 返回的结果集
    */
    public function getOneInfo($tableName,$where)
    {
        $result = array();
        if ($tableName && is_array($where))
        {
            $result = M($tableName)->where($where)->find();

        }

        return $result;

    }


    /*
    * 获取多条条记录
    * @param string $tableName 表名
    * @param array   $where 要添加的数据
    * @return array  $result 返回的结果集
    */
    public function getMultInfo($tableName,$where)
    {

        $result = array();
        if ($tableName && is_array($where))
        {
            $result = M($tableName)->where($where)->select();

        }

        return $result;


    }

    /*
    * 删除记录
    * @param string $tableName 表名
    * @param array   $where 要添加的数据
    * @return boolean  $result 返回布尔值
    */
    public function delete($tableName,$where)
    {

        $result = false;
        if ($tableName && is_array($where))
        {
            $data['delete'] = 1;
            $result = M($tableName)->where($where)->data($data)->save();
            if($result>0)
            {
                $result = true;
            }else{
                $result = false;
            }

        }

        return $result;

    }


    /*
     * 分页的实现
     *
     * */

    public function page($list,$count,$pageSize)
    {

        $result =array();
        if($list && $count && $pageSize)
        {
            $Page       = new \Think\Page($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出

            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

            $result['list']  = $list;
            $result['count'] = $count;
            $result['page']  = $show;


        }


        return $result;


    }


    /*
     * 获取公司的某些字段*/
    public function getField($table,$fields)
    {
        $result = array();

        if($fields)
        {
            $result = M($table)->field('id,'.$fields)->select();
            
            

        }

        return $result;


    }


    /*
     * 获取公司的某个字段的所有数据
     * @param string $table 公司的名字
     * @param string $fields 查询字段
     * */


    public function selectFieldData($table,$fields)
    {

        $result = array();

        if($fields)
        {
            $result = M($table)->field($fields)->select();

        }

        return $result;

    }



    /*获取某个字段的最大值*/
    public function selectMaxData($table,$fields,$where)
    {

        $result = array();

     

        if($fields)
        {
            $result = M($table)->where($where)->max($fields);

        }

        return $result;



    }


    /*获取某个字段的最小值*/
    public function selectMinData($table,$fields,$where)
    {

        $result = array();

        if($fields)
        {
            $result = M($table)->where($where)->min($fields);


        }

        return $result;



    }


    /*获取平均值*/
    public function selectAvgData($table,$fields,$where)
    {

        $result = array();

        if($fields)
        {
            $result = M($table)->where($where)->avg($fields);

        }

        return $result;


    }


}