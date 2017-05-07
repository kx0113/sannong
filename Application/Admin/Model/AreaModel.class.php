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

class AreaModel extends BaseModel
{

     public  $areaName;

     private $pageSize = 10;

     /*
      * 删除区域
      * @param array $where 删除条件
      * 
      */
     public function deleteArea($where)
     {

          $this->delete('area',$where);

     }

     /*
     * 更新区域
     * @param array $where 更新条件
     * @param array $data 更新条件
     */
     public function updateArea($where,$data)
     {

          $result = $this->update('area',$where,$data);
          return $result;
     }

     /*
    * 添加一个区域区域
    * @param array $where 更新条件
    * @param array $data 更新条件
    */
     public function addArea($data)
     {

        $this->add('area',$data);

     }

     /*
     * 添加一个乡镇级区域
     * @param array $data 添加区域的数据
     */
     public function addTownArea($data)
     {
          $data['level'] = 1;
          $result = $this->add('area',$data);

          return  $result;

     }

     /*
   * 添加一个乡镇级区域
   * @param array $data 添加区域的数据
   */
     public function addVillageArea($data)
     {
          $data['level'] = 2;
          $result = $this->add('area',$data);
          return $result;

     }


     /*
      * 查找一个区域
      * @param array $where 查找条件
      */

     public function findArea($where)
     {
          $result = array();
          if($where)
          {
               $result = $this->getOneInfo('area',$where);

          }else{

               throw new Exception('$where 不能为空');

          }

          return $result;

     }



     /*
     * 查找一个村一级区域
     * @param array $where 查找条件
     */

     public function findTownArea($where)
     {
          $result = array();
          if($where)
          {
               $result = $this->getOneInfo('area',$where);

          }else{

               throw new Exception('$where 不能为空');

          }

          return $result;

     }


     /*
     * 查找多个区域
     * @param array $where 查找条件
     */

     public function selectArea($where)
     {
          $result = array();
          if($where)
          {
               $where['delete'] = 0;
               $result = $this->getMultInfo('area',$where);


          }else{

               throw new Exception('$where 不能为空');

          }

          return $result;

     }

     /*
      * 查找多个区域并且分页
      * @param array $where 查询条件
      * @param int  $p 第几页
      * @return array $result
      */

     public function selectAreaCondition($where,$p)
     {
          $result = array();
          if($where & $p)
          {

               $offset = $this->pageSize;
               $start  = ($p-1)*$offset;
               $list = array();
               $count = M('area')->where($where)->count();

               if(ceil($count/$offset) >= $p)
               {

                    $list = M('area')->where($where)->limit($start,$offset)->select();
                    $result = $this->page($list,$count,$offset);

               }else{

                    throw new Exception('区域搜索的页码不对。');
               }


          }else{
               throw new Exception('区域搜索的where 和页码 p 都要为真');
          }


          return $result;




     }


     /*获取父级的地点*/

     public function getParentArea($id)
     {

          $result =  array();

          if($id)
          {
               $where = array('a.id' => $id);

               $result =  M('area as a')->join('sn_area as b on a.parent_id = b.id')->where($where)->find();



          }else{


               throw new Exception('查询父亲地点的条件不能为空');
          }

          return $result;



     }


     public function selectTownList($where,$p)
     {

          $result = array();
          if($where && $p)
          {
               $area = M('area');
               $where['delete'] = 0;
               $offset = $this->pageSize;
               $start = ($p-1)*$offset ;

               $count = $area->where($where)->count();


               if(ceil($count/$offset) >= $p)
               {
                    $list  = $area->where($where)->limit($start,$offset)->select();

                    $result = $this->page($list,$count,$offset);

               }


          }else{

               throw new Exception('搜索监视器的where 条件和页码p都要为真');
          }


          return $result;

     }



     public function selectVillageList($where,$p)
     {

          $result = array();
          if($where && $p)
          {
               $area = M('area a');
               $offset = $this->pageSize;
               $start = ($p-1)*$offset ;

               $where['a.delete'] = 0;

               $count = $area->join('left join sn_area b on b.parent_id = a.id ')->where($where)->count();



               if(ceil($count/$offset) >= $p)
               {
                    $list  = $area->field('a.*,b.id as area_id,b.area_name as village_name')->join('left join  tb_area as b on b.parent_id = a.id ')
                                  ->where($where)->limit($start,$offset)->select();



                    $result = $this->page($list,$count,$offset);

               }


          }else{

               throw new Exception('搜索监视器的where 条件和页码p都要为真');
          }

          return $result;

     }

     public function getThirdAreaInfo($where)
     {

          $result = M('area as a')
              ->field('a.*,b.id as bid,b.area_name as bname,c.id as cid,c.area_name as cname')
              ->join('sn_area as b on a.parent_id = b.id')
              ->join('sn_area c on b.parent_id=c.id')
              ->where($where)
              ->find();

          return $result;
     }
     
     
     /**
      * 获取所有的区域
      *
      * @return array  $result
      *
      * */
     public function selectAllArea()
     {
          $where['delete'] = 0;
          $result =  M('area')->where($where)->select();
          return $result;
     }


     public function  areaAllList()
     {
          $tempArray = $this->selectAllArea();

          $areaList = array();

          if ($tempArray) {
               foreach ($tempArray as $val) {

                    if($val['level'] == 1){

                         $firstLevel = count($areaList);
                         $areaList[$firstLevel]['areaName'] = $val['area_name'];
                         $areaList[$firstLevel]['level'] = $val['level'];
                         $areaList[$firstLevel]['id'] = $val['id'];
                         $areaList[$firstLevel]['child'] = array();

                    }
                    if ($areaList) {
                         foreach ($areaList as $key=>$vv) {
                              if ($val['parent_id'] == $vv['id']) {

                                   $num = count($areaList[$key]['child']);
                                   $areaList[$key]['child'][$num]['id']= $val['id'];
                                   $areaList[$key]['child'][$num]['level']= $val['level'];
                                   $areaList[$key]['child'][$num]['areaName']= $val['area_name'];
                                   $areaList[$key]['child'][$num]['child']= array();


                              }

                              if ($areaList[$key]['child']){

                                   foreach ($areaList[$key]['child'] as $kk=>$kv) {

                                        $kNum = count($areaList[$key]['child'][$kk]['child']);
                                        if ($val['parent_id'] == $kv['id']) {
                                             $temp['id'] = $val['id'];
                                             $temp['level'] = $val['level'];
                                             $temp['areaName'] = $val['area_name'];
                                             $areaList[$key]['child'][$kk]['child'][$kNum] = $temp;

                                        }

                                   }

                              }

                         }

                    }

               }
          }

          return $areaList;

     }

     /**
      * 获取第三级的区域
      *
      * $param  array  $where
      */

     public function  secondThirdArea($where)
     {

          $result = M('area as a')
              ->field('a.*')
              ->join('sn_area as b on a.parent_id = b.id')
              ->where($where)
              ->select();


          return $result;


     }

     /**
      * 获取第三的区域
      *
      * $param  array  $where
      */

     public function  firstThirdArea($where)
     {

          $result = M('area as a')
              ->field('a.*')
              ->join('sn_area as b on a.parent_id = b.id')
              ->join('sn_area as c on b.parent_id = c.id')
              ->where($where)
              ->select();


          return $result;


     }











}