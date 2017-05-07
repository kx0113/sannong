<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 17:42
 */

namespace Common\Model;
use Think\Model;

class YglUserModel extends Model {

    protected $tableName = 'ygl_user';

    public function getUser($where = array(), $field = '*'){
        return $this->field($field)->where($where)->select();
    }

    public function getDomain(){
        $sql = 'select d.id, d.pid fid, d.name, d.is_edit, d.corder, count(dd.id) has_children from sn_domain d left join
                sn_domain dd on dd.pid = d.id and dd.display = 1 where d.display = 1 group by d.id order by d.pid asc, d.corder asc';
    }
	
    //添加用户
    public function addUser($data = array()){
        return $this->add($data);
    }
    //检查是否存在该用户
    public function checkUser($mobile){
        $where=array('account' => $mobile);
        return $this->where($where)->select();
    }

    //修改用户信息
    public function upUser($where = array(), $data = array()){
        return $this->where($where)->save($data);
    }
	
}