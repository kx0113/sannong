<?php

namespace Common\Model;
use Think\Model;
use Common\Common\General;

class YjjYingyongModel extends Model {
    
    protected $tableName = 'policy';
    
    public function articleList($where = array(),$page){
        return $this->field('id,dept_id,title,introduce,picture,addtime,is_top')->where($where)->order('id desc')->page($page,10)->select();
        
    }
    
    public function xqwList($where = array(),$page){
        return $this->table('sn_play')->field('id,cate_id,name,address,picture,tel,is_top')->where($where)->order('id desc')->page($page,10)->select();
    
    }
    
    public function businessList($where1=array(),$where2=array(),$page){
        $sj = date("Y-m-d");
        $where3 = array('is_check'=>2);
        $where3['expire_date'] = array('gt',$sj);
        return $this->table('sn_business')->field('id,cate_id,type,title,content,addtime,pictures,account,tel,account')->where($where1)->where($where2)->where($where3)->order('id desc')->page($page,10)->select();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}