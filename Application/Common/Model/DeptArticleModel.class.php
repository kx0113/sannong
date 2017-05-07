<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/8
 * Time: 16:30
 */

namespace Common\Model;
use Think\Model;

class DeptArticleModel extends Model {

    protected $tableName = 'dept_article';

    public function getInfos($where = array(), $limit = 0, $offset = 0, $field = '*'){
        $limit = intval($limit);
        $offset = intval($offset);
        return $this->field($field)->join('__MENU__ on __MENU__.mid = __DEPT_ARTICLE__.menu_id', 'LEFT')
                    ->where($where)->order('addtime DESC')
                    ->limit($limit.','.$offset)->select();
    }
    
    public function findOne($where=array(),$table){
        return $this->table($table)->field('picture')->where($where)->select();
    }
    
    public function ddnjList($where,$p){
        return $this->table('sn_ddnj')->field('sn_ddnj.id,cate_id,username,tel,number_plate,license_number,sex,sn_ddnj.addtime,longitude,latitude,sn_ddnj_cate.name')->join('sn_ddnj_cate ON sn_ddnj.cate_id = sn_ddnj_cate.id')->where($where)->order('id desc')->page($p.',10')->select();
    }
    
    public function getYjjInfos($where = array(), $limit = 0, $offset = 0, $field = '*'){
        $limit = intval($limit);
        $offset = intval($offset);
        return $this->table('sn_yjj_dept_article')->field($field)->join('__YJJMENU__ on __YJJMENU__.mid = __YJJ_DEPT_ARTICLE__.menu_id', 'LEFT')
        ->where($where)->order('addtime DESC')
        ->limit($limit.','.$offset)->select();
    }
    
    
}