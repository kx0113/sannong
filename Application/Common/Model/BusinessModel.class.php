<?php

namespace Common\Model;
use Think\Model;
use Common\Common\General;

class BusinessModel extends Model {

    protected $tableName = 'business';

    public function num($where = array()){
        $sj = date("Y-m-d");
        return $this->where('expire_date <'.$sj)->where('is_check = 2')->where($where)->order('id desc')->count();
    }
    
    public function getInfo($where=array(),$p){
        $sj = date("Y-m-d");
        return $this->where('expire_date > '.$sj)->where('is_check = 2')->where($where)->order('id desc')->page($p.',10')->select();
    }
    
    public function unSPnum(){
        return $this->where('is_check = 1')->order('id asc')->count();
    }
    
    public function getUnSP($p){
        return $this->where('is_check = 1')->order('id asc')->page($p.',10')->select();    
    }
    
    public function changeStatus($id,$status){
        $where = array('id'=>$id);
        $data = array('is_check'=>$status);
        return $this->where($where)->save($data);
    }
    
    public function Detail($id){
        $where=array('id'=>$id);
        return $this->where($where)->select();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}