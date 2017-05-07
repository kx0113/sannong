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

class SysMenuModel extends BaseModel
{


   public function get_menus($where)
   {

     $result =  M("sys_menu")->where($where)->select();



     return $result;


   }














}
