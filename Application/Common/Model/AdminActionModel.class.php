<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23
 * Time: 15:01
 */

namespace Common\Model;
use Think\Model;

class AdminActionModel extends Model {
    protected $tableName = 'admin_action';

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
}