<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 17:42
 */

namespace Common\Model;
use Think\Model;

class YjjUserModel extends Model {

    protected $tableName = 'yjj_user';
    //取得该用户的信息
    public function getUser($where = array(), $field = '*'){
        return $this->field($field)->where($where)->select();
    }
    //添加该用户
    public function addUser($data = array()){
        return $this->add($data);
    }
    //检查是否存在该用户
    public function checkUser($mobile){
        $where=array('uname' => $mobile);
        return $this->where($where)->select();
    }

    //修改用户信息
    public function upUser($where = array(), $data = array()){
        return $this->where($where)->save($data);
    }
}