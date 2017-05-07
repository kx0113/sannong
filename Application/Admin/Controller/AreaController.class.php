<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/14
 * Time: 14:32
 */

namespace Admin\Controller;


use Admin\Model\AreaModel;
use Admin\Controller\AdminBaseController;

use Think\Exception;

class AreaController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->areaModel = new AreaModel();
        $this->assign('level_one_id',16);

    }



    /*
    * 区域，乡县 的列表
    * @param int $p 页码
    * @return array $result 返回数组
    */

    public function IndexArea()
    {
        $result =array();
        $where['level'] = '1';
        $p = I('p',1,'intavl');

        try{

            $result = $this->areaModel->selectAreaCondition($where,$p);


        }catch(Exception $e){

            print $e->getMessage();
        }


        $this->assign('area',$result);



    }


    /*
     * 添加区域
     * @var array $data 创建区域的数组*/
    public function addArea()
    {
        $data = array();

        try
        {
            $this->areaModel->addArea($data);

        }catch(Exception $e){

            echo $e->getMessage();

        }



    }

    /*
     * 删除区域
     * @var array $wehre 删除的条件 */
    public function  deleteArea()
    {
        $where = array('id'=>1);
        try
        {
            $this->areaModel->deleteArea($where);

        }catch(Exception $e){

            echo $e->getMessage();

        }



    }

    /*
     * 更新区域
     * @var array $where 更新条件
     * @var array $data 要更新的数据*/

    public function updateArea()
    {

        $where = array('id'=>1);

        $data = array();
        try
        {
            $this->areaModel->updateArea($where,$data);

        }catch(Exception $e){

            echo $e->getMessage();

        }


    }


    /*
    * 区块，公司，自定义的列表
    * @param int $p 页码
    * @return array $result 返回数组
    */
    public function  IndexRegion()
    {

        $result =array();
        $where['level'] = '2';
        $p = I('p',1,'intval');
        try{

            $result = $this->areaModel->selectAreaCondition($where,$p);

        }catch(Exception $e){

            print $e->getMessage();
        }



        $this->assign('region',$result);


    }

    /*
     * 添加区块
     * @var param $data 添加的数据 */


    public function addRegion()
    {

        $data = array();

        try
        {
            $this->areaModel->addArea($data);

        }catch(Exception $e){

            echo $e->getMessage();

        }


    }

    /*
     * 删除区块
     * @var array $where 条件*/


    public function deleteRegion()
    {

        $where = array('id'=>1);
        try
        {
            $this->areaModel->deleteArea($where);

        }catch(Exception $e){

            echo $e->getMessage();

        }

    }


    /*
     * 更新区块信息
     * @var array  $where  条件
     * @var array $data 数据  */
    public function updateRegion()
    {
        $where = array('id'=>1);

        $data = array();
        try
        {
            $this->areaModel->updateArea($where,$data);

        }catch(Exception $e){

            echo $e->getMessage();

        }


    }


    /*
     * 获取乡一级的的地区
     * */

    public function selectTownList()
    {

        $where = array('level'=>1);
        $res = array();
        $result = array();
        try
        {
            $res = $this->areaModel->selectArea($where);

        }catch(Exception $e){

            echo $e->getMessage();

        }


        if($res){

            foreach($res as $key=>$val){

                $result[$key]['area_name'] = $val['area_name'];
                $result[$key]['id'] = $val['id'];
            }

        }

        return $result;


    }

    /*
     * 获取村一级的区域
     * @var int $id 区域id
     * @return array $result 返回数组
     **/

    public function selectVillageList()
    {
        $id = I('id');
        $result = array();
        $result = $this->selectVillageById($id);
        return $result;
    }


    /*
 * 获取村一级的区域
 * @var array $where 查询条件
 * @return array $result 返回数组
 **/

    public function selectVillageById($id)
    {

        $where['level']=2;
        $where['parent_id'] = $id;
        $res = array();
        $result = array();
        try
        {
            $res = $this->areaModel->selectArea($where);

        }catch(Exception $e){

            echo $e->getMessage();
        }

        if($res){

            foreach($res as $key=>$val){

                $result[$key]['area_name'] = $val['area_name'];
                $result[$key]['id'] = $val['id'];
            }

        }



        return $result;
    }


    /*
     * ajax返回村一级的地区
     * @var int $where['parent_id']*/

    public function ajaxVillageArea()
    {

        $id = I('id');
        $where['level']=2;
        $where['parent_id'] = $id;


        $res = array();
        $result = array();
        try
        {
            $res = $this->areaModel->selectArea($where);

        }catch(Exception $e){

            $this->ajaxReturn(array('ret'=>400,'data'=>$result,'message'=>'失败:'.$e->getMessage()));

        }


        if($res){

            foreach($res as $key=>$val){

                $result[$key]['area_name'] = $val['area_name'];
                $result[$key]['id'] = $val['id'];
            }
            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'查询成功'));

        }else{

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'没有值'));

        }


    }


    /*
     * ajax 返回乡镇级的数据
     *
     */


    public function ajaxTownArea()
    {

        $where = array('level'=>1);
        $res = array();
        $result = array();
        try
        {
            $res = $this->areaModel->selectArea($where);

        }catch(Exception $e){

            $this->ajaxReturn(array('ret'=>400,'data'=>$result,'message'=>'失败:'.$e->getMessage()));

        }


        if($res){

            foreach($res as $key=>$val){

                $result[$key]['area_name'] = $val['area_name'];
                $result[$key]['id'] = $val['id'];
            }




            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'查询成功'));


        }else{

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'没有数据'));

        }




    }


   public function town()
   {

       $p = I('p',1,'intval');

       $where = array('level'=>1);
       $keywords = I('keywords');

       if($keywords)
       {
           $where['area_name'] = array('like'=>$keywords);
       }

       $where['_string'] = '1=1';




       $result = $this->areaModel->selectTownList($where,$p);

       $this->assign('second_id',80);
       $this->assign('town_list',$result);
       $this->assign('keywords',$keywords);
       $this->assign('level_second_id',17);

       $this->display('town');
   }


    public function townAdd()
    {

        $this->display('town');
    }




    public function addTownInfo()
    {

        $town_name =I('name');

        try{

            if($town_name)
            {
                $data['area_name'] = $town_name;
                $data['parent_id'] = 0;
                $data['created_at'] = date("Y-m-d H:i:s");

                $this->areaModel->startTrans();

                $result = $this->areaModel->addTownArea($data);

                if($result)
                {

                    $this->areaModel->commit();

                    $this->success('区域添加成功','town');

                }else{

                    $this->areaModel->rollback();

                    $this->error('区域添加失败');

                }
                // 进行相关的业务逻辑操作

            }else{

                throw new Exception('更新的父亲id和地块不能为FALSE');
            }


        }catch (Exception $e){

            $town_area_list = $this->selectTownList();
            $this->assign('town_area_list',$town_area_list);

            $where['area_name'] = $town_name ;

            $area_info = $this->areaModel->selectArea($where);


            $message =$e->getMessage();

            if($area_info)
            {
                $message = "这个区域已经存在";
            }

            $this->assign('message',$message);
            $this->assign('town_name',$town_name);
            $this->display('addTown');

        }

    }




    public function updateTown()
    {


        $area_id = I('id');
        $where = array('id'=>$area_id);
        $res = $this->areaModel->findArea($where);




        $this->assign('town_info',$res);

        $this->display('updateTown');
    }



    public function UpdateTownInfo()
    {

        $town_name =trim( I('name') );
        $id = I('id');
        $town_info = array();

        try{

            if($town_name && $id)
            {
                $data['area_name'] = $town_name;

                $where['id'] = $id;

                $town_info = $this->areaModel->findArea($where);
                if($town_info['area_name'] != $town_name ){

                    $this->areaModel->startTrans();

                    $result = $this->areaModel->updateArea($where,$data);

                    if($result)
                    {

                        $this->areaModel->commit();

                        $this->success('区域更新成功');

                    }else{

                        $this->areaModel->rollback();

                        $this->error('区域跟新失败');

                    }

                    // 进行相关的业务逻辑操作


                }else{

                    $this->success('区域更新成功');
                }


            }else{

                throw new Exception('更新的区域\区域的id要为true');
            }


        }catch (Exception $e){

            $where = array();
            $where['area_name'] = $town_name ;
            $where['id'] = array('neq',$id);

            $other_area_info = $this->areaModel->selectArea($where);

            $message =$e->getMessage();

            if($other_area_info)
            {
                $message = "这个区域已经存在,更新失败";
            }


            $this->assign('message',$message);
            $this->assign('town_info',$town_info);
            $this->display('updateTown');

        }

    }



    public function village()
    {
        $p = I('p',1,'intval');
        $town_id = I('area_town');
        $keywords = I('keywords');

        if($keywords)
        {
            $where['a.area_name'] = array('like','%'.$keywords.'%');
        }

        if($town_id)
        {

            $where['b.parent_id'] = $town_id;
        }

        $where['_string'] = '1=1';

        $result = $this->areaModel->selectVillageList($where,$p);

        /*获取乡镇一级区域*/
        $town_area_list = $this->selectTownList();

        $this->assign('town_area_list',$town_area_list);
        $this->assign('village_list',$result);
        $this->assign('level_second_id',18);
        $this->display('village');
    }




    public function addVillage()
    {
        $town_area_list = $this->selectTownList();
        $this->assign('town_area_list',$town_area_list);

        $this->display('addVillage');
    }


    public function addVillageInfo()
    {
        $parent_id = I('area_town');

        $village_name = I('name');

        $village_name = trim($village_name);



        if($parent_id && $village_name)
        {
            $data['area_name'] = $village_name;
            $data['parent_id'] = $parent_id;
            $data['delete'] = 0;
            $data['created_at'] = date('Y-m-s H:i:s');

            $this->areaModel->startTrans();
            // 进行相关的业务逻辑操作

            try{

                $result = $this->areaModel->addVillageArea($data);

                if($result)
                {

                    $this->areaModel->commit();

                    $this->success('区块添加成功','village');

                }else{

                    $this->areaModel->rollback();

                    $this->error('区块添加失败');

                }


            }catch (Exception $e){

                $this->success('区块添加失败 .原因'.$e->getMessage());

            }


        }else{

            $this->error('区块的父亲id和名字不能为空');


        }


    }



    public function updateVillage()
    {

        $area_id = I('id');
        $town_area_list = $this->selectTownList();

        $where = array('id'=>$area_id);
        $res = $this->areaModel->findArea($where);
        $this->assign('village_info',$res);
        $this->assign('town_area_list',$town_area_list);

        $this->display('updateVillage');
    }



    public function updateVillageInfo()
    {
        $parent_id = I('area_town');

        $village_name = I('name');

        $village_name = trim($village_name);

        $id = I('id');


        try{

            if($parent_id && $village_name && $id)
            {
                $data['area_name'] = $village_name;
                $data['parent_id'] = $parent_id;

                $this->areaModel->startTrans();

                $where['id'] = $id;

                $result = $this->areaModel->updateArea($where,$data);

                if($result)
                {

                    $this->areaModel->commit();

                    $this->success('区块更新成功','town');

                }else{

                    $this->areaModel->rollback();

                    $this->error('区块更新失败','town');

                }
                // 进行相关的业务逻辑操作

            }else{

                throw new Exception('更新的父亲id和地块不能为FALSE');
            }


        }catch (Exception $e){

            $town_area_list = $this->selectTownList();
            $this->assign('town_area_list',$town_area_list);

            $village_info['area_name'] = $village_name;
            $village_info['parent_id'] = $parent_id;

            $this->assign('village_info',$village_info);


            $this->assign('village_name',$village_name);



        }




    }

    public function findOneArea($id)
    {

        $where['id'] = $id;
        $result = $this->areaModel->findArea($where);

        return $result;
    }


    public function delArea()
    {
        $id = I('id');

        if($id)
        {
            $where['id'] = $id;
            $result = $this->areaModel->delete('area',$where);

            if($result==1)
            {

                $result = array('ret'=>200,
                    'data'=>array('status'=>'000000'),
                    'message'=>'删除成功'
                );


            }else{

                $result = array('ret'=>200,
                    'data'=>array('status'=>'000001'),
                    'message'=>'删除失败'
                );


            }
        }else{

            $result = array('ret'=>200,
                'data'=>array('status'=>'000002'),
                'message'=>'为空'
            );
        }



        $this->ajaxReturn($result);




    }


    /*
     * 选择一级的区域
     * */
    public function selectAreaFirst()
    {

        $where['level'] = 1;
        $where['delete'] = 0;

        $list = $this->areaModel->selectArea($where);
        if($list)
        {
            foreach($list as $key=>$val)
            {
                $result[$key]['id'] = $val['id'] ;
                $result[$key]['areaName'] = $val['area_name'] ;
            }


        }

        return $result;

    }

    /*
     * 选择二级区域
     * @param int $parentId 父亲id
     * */
    public function selectAreaSec($parentId)
    {

        $where['level'] = 2;
        $where['delete'] = 0;
        $where['parent_id'] = $parentId;
        $list = $this->areaModel->selectArea($where);

        if($list)
        {
            foreach($list as $key=>$val)
            {
                $result[$key]['id'] = $val['id'] ;
                $result[$key]['areaName'] = $val['area_name'] ;
            }


        }
        return $result;
    }
    /*
     * 选择三级区域
     * @param int $parentId 父亲id
     * */
    public function selectAreaThird($parentId)
    {

        $where['level'] = 3;
        $where['delete'] = 0;
        $where['parent_id'] = $parentId;

        $list = $this->areaModel->selectArea($where);

        if($list)
        {
            foreach($list as $key=>$val)
            {
                $result[$key]['id'] = $val['id'] ;
                $result[$key]['areaName'] = $val['area_name'] ;
            }


        }

        return $result;

    }

    /*查询第二级区域*/

    public function ajaxAreaSecond()
    {

        $id = I('id');

        try {

            $list = $this->selectAreaSec($id);

            if ($list) {
                $this->ajaxReturn(array('ret' => 200, 'data' => $list, 'message' => '数据查询成功'));
            } else {
                $this->ajaxReturn(array('ret' => 404, 'data' => array(), 'message' => '数据查询失败'));
            }


        } catch (Exception $e) {

            $this->ajaxReturn(array('ret' => 500, 'data' => array(), 'message' => '数据查询失败'));

        }
    }



    public function ajaxAreaThird()
    {

        $id = I('id');

        try{

            $list = $this->selectAreaThird($id);

            if($list)
            {
                $this->ajaxReturn(array('ret'=>200,'data'=>$list,'message'=>'数据查询成功'));
            }else{
                $this->ajaxReturn(array('ret'=>404,'data'=>array(),'message'=>'数据查询失败'));
            }


        }catch(Exception $e){

            $this->ajaxReturn(array('ret'=>500,'data'=>array(),'message'=>'数据查询失败'));

        }


    }

    /**
     * 获取所有的区域和等级
     *
     * @return array  $result
     * */
   
    






}
