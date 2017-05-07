<?php

namespace Common\Model;
use Think\Model;

class ExpertModel extends Model {

    protected $tableName = 'expert';
    
    //检查是否存在该用户
    public function checkUser($mobile){
        $where=array('account' => $mobile);
        return $this->where($where)->select();
    }
    
    public function getUser($where = array(), $field = '*'){
        return $this->field($field)->where($where)->select();
    }
    
    public function expertAdd($data = array()){
        return $this->data($data)->add();
    }
    
    public function expertDelete($id){
        return $this->where("eid={$id}")->delete();
    }
    
    public function cateList($fid=0,$is_f_id=false){
        $sql = 'select c.id, c.pid fid, c.name, c.is_edit, c.corder, count(ac.id) has_children from sn_domain c left join
                sn_domain ac on ac.pid = c.id and ac.display = 1 where c.display = 1 group by c.id order by c.pid asc, c.corder asc';
        $cates = $this->db->query($sql);
        return $this->category_options($fid, $cates, $is_f_id);
    }

    //修改专家信息
    public function upExpert($where = array(), $data = array()){
        return $this->where($where)->save($data);
    }
    
    public function category_options($spec_cat_id, $arr,$is_f_id)
    {
        static $cat_options = array();
    
        if (isset($cat_options[$spec_cat_id]))
        {
            return $cat_options[$spec_cat_id];
        }
    
        if (!isset($cat_options[0]))
        {
            $level = $last_cat_id = 0;
            $options = $cat_id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $value['nbsp'] = str_repeat('&nbsp;', $level * 4);
                    $cat_id = $value['id'];
                    if ($level == 0 && $last_cat_id == 0)
                    {
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_cat_id  = $cat_id;
                        $cat_id_array = array($cat_id);
                        $level_array[$last_cat_id] = ++$level;
                        continue;
                    }
    
                    if ($value['fid'] == $last_cat_id)
                    {
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['name'];
                        unset($arr[$key]);
    
    
                        if ($value['has_children'] > 0)
                        {
                            if (end($cat_id_array) != $last_cat_id)
                            {
                                $cat_id_array[] = $last_cat_id;
                            }
                            $last_cat_id    = $cat_id;
                            $cat_id_array[] = $cat_id;
                            $level_array[$last_cat_id] = ++$level;
                        }
                    }
                    elseif ($value['fid'] > $last_cat_id)
                    {
                        break;
                    }
                }
    
                $count = count($cat_id_array);
                if ($count > 1)
                {
                    $last_cat_id = array_pop($cat_id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_cat_id != end($cat_id_array))
                    {
                        $last_cat_id = end($cat_id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_cat_id = 0;
                        $cat_id_array = array();
                        continue;
                    }
                }
    
                if ($last_cat_id && isset($level_array[$last_cat_id]))
                {
                    $level = $level_array[$last_cat_id];
                }
                else
                {
                    $level = 0;
                }
    
            }
    
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }
    
        if (!$spec_cat_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_cat_id]))
            {
                return array();
    
            }
    
            $spec_cat_id_level = $options[$spec_cat_id]['level'];
    
            foreach ($options AS $key => $value)
            {
                if ($key != $spec_cat_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }
    
            $spec_cat_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                    ($spec_cat_id_level > $value['level']))
                {
                    break;
                }
                else
                {
                    $spec_cat_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_cat_id] = $spec_cat_id_array;
    
            if($is_f_id==true){
                unset($spec_cat_id_array[$spec_cat_id]);
            }
            return $spec_cat_id_array;
        }
    }
    
    public function getCate($where = array(), $limit = 0, $offset = 0){
        $where['display'] = 1;
        $limit = intval($limit);
        $offset = intval($offset);
        if($limit){
            $query = $this->table('sn_domain')->where($where)
            ->order('corder', 'asc')->limit($offset, $limit)->select();
        }else{
            $query = $this->table('sn_domain')->where($where)
            ->order('corder', 'asc')->select();
        }
        return $query;
    }
    
    public function addCate($data = array()){
        return M('Domain')->add($data);
    }
    
    public function editCate($data = array(), $where = array()){
        return M('Domain')->where($where)->save($data);
    }

    public function getExpertWithDomain($where = array(), $limit = 0, $offset = 0, $field = '*'){
        $limit = intval($limit);
        $offset = intval($offset);
        return $this->field($field)->join('__DOMAIN__ on __DOMAIN__.id = __EXPERT__.did', 'LEFT')
            ->where($where)/*->order('addtime DESC')*/
            ->limit($limit.','.$offset)->select();
    }
}
