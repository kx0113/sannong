<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/10
 * Time: 16:49
 */

namespace Admin\Model;


use Think\Exception;

class ParamModel extends  BaseModel
{

    /*
     * 添加参数
     * @param $data 添加的数据
     * */
    public function  addParam($data)
    {
        if($data)
        {

            $this->add('param',$data);

        }else{

            throw new Exception('$data 数据不能为空');

        }


    }
    


    /*
     * 删除参数的数据
     * @param array $where 删除数据的条件
     */

    public function delParam($where)
    {
        if($where)
        {
            $this->delete('param',$where);

        }else{

            throw new Exception('$where 条件不能为空');
        }

    }

    /*
   * 修改参数的数据
   * @param array $where 删除数据的条件
   */

    public function updateParam($where,$data)
    {
        if($where)
        {
            $this->update('param',$where,$data);

        }else{

            throw new Exception('$where 和 $data都不能为空');
        }

    }

   /*
    * 查找参数的数据
    * @param array $where 删除数据的条件
    */

    public function findParam($where)
    {
        $result = array();
        if($where)
        {
            $result = $this->update('param',$where);

        }else{

            throw new Exception('$where 都不能为空');
        }

        return $result;

    }



   /*
    * 修改参数的数据
    * @param array $where 删除数据的条件
    */

    public function selectParam($where)
    {
        $result = array();
        if($where)
        {
            $result = $this->getMultInfo('param',$where);

        }else{

            throw new Exception('$where 不能为空');
        }

       

        return $result;

    }


    public function selectParamFields($fields)
    {
        $result = array();
        if($fields)
        {

            $result = M('param')->field('id,'.$fields)->select();

        }

        return $result;





    }





}