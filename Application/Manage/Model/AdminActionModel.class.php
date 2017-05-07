<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 8:57
 */

namespace Manage\Model;


use Manage\Model\BaseController;
use Think\Exception;

class AdminActionModel extends BaseModel
{
     public  $tableName = 'admin_action';



    public  function getMenuList($where)
    {

        if($where){


            $result = M($this->tableName)->where($where)->select();

            return $result;

        }else{

            throw new Exception('菜单查询为空');
        }
    }



    public function getMenuInfo($where)
    {

        if($where)
        {
            $result = M($this->tableName)->where($where)->find();



            return $result;

        }else{

            throw new Exception('菜单查询为空');
        }
    }


    public function getLevelOne()
    {


        $where['level'] = 1;
        $where['delete'] = 0;
        $one_parent = M("admin_action")->where($where)->select();

        return $one_parent;

    }



    //根据部门获取权限
    public function getDeptAction($dept_id){
        $ids = M('Department')->field('action_id')->find($dept_id);
        $where = array('is_display' => 1, 'action_id' => array('in', $ids['action_id']));
        return $this->where($where)->order('is_top ASC')->select();
    }

    public function getAction($where = array(), $field = '*'){
        $where['display'] = 1;
        return $this->field($field)->where($where)->order('is_top asc')->select();
    }



    public function getYjjUsers(){

    }



    public function getLeftMenuList($id)
    {

        if($id)
        {

            $result =  M()->query("SELECT * FROM sn_admin_action WHERE	`parent_id_new` IN (SELECT `action_id` FROM `sn_admin_action` WHERE `parent_id_new` = ".$id.")or `action_id` IN (SELECT `action_id` FROM `sn_admin_action` WHERE `parent_id_new` = ".$id." )");
               

         




            return  $result;
            
        }else{
            throw new Exception('查询条件不对');
        }




    }



    public function getMenuId($where)
    {




    }


    public function updateMenu($where,$data)
    {

        $result = $this->update('admin_action',$where,$data);


        return $result;
    }


    

}