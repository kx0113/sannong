<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 8:57
 */

namespace Manage\Controller;


use Think\Controller;
use Manage\Model\AdminActionModel;

class MenuController extends Controller
{


    public function __construct()
    {

        parent::__construct();

        $this->menuModel = new AdminActionModel();


    }


    public function  menu_add()
    {
        //查询一级菜单
        $where = array();
        $where['level'] = 1;
        $parent = M("admin_action")->where($where)->select();

        //查询一级菜单

        $this->assign("one_parent",$parent);


        $this->display('menu_add');

    }


    //添加二级菜单


    public function addMenuInfo()
    {

        $id = I('parent');
        $action_name = I('action_name');
        $action_code = I('action_code');
        $action_url = I('action_url');

        $data['parent_id_new'] = $id;
        $data['action_name'] = $action_name;
        $data['action_code'] = $action_code;
        $data['action_url'] = $action_url;
        $data['is_display'] = 1;
        $data['level'] = 2 ;

        $where['parent_id_new'] = $id;
        $top = M('admin_action')->where($where)->max('is_top');
        $data['is_top'] = $top+1;
        $data['type'] = 1;


        try{

            M('admin_action')->add($data);

            $this->success('添加成功');


        }catch(Exception $e){

            $e->getMessage();

        }

    }


    public function addLevelThree()
    {

        //查询一级菜单
        $where = array();
        $where['level'] = 1;
        $one_parent = M("admin_action")->where($where)->select();


        $this->assign('one_parent',$one_parent);

        $this->display('menu_three_menu');



    }


    public function getTowMenu()
    {

        $id = I('id');

        //查询一级菜单
        $where = array();
        $where['level'] = 2;
        $where['parent_id_new'] = $id;

        $tow_parent = M("admin_action")->where($where)->select();

        if($tow_parent){

            $temp = array();

            foreach($tow_parent as $key=> $val)
            {
                $temp[$key]['id'] = $val['action_id'];
                $temp[$key]['action_name'] = $val['action_name'];
            }


            $this->ajaxReturn(array('ret'=>200,data=>$temp,'message'=>'成功'));


        }else{

            $this->ajaxReturn(array('ret'=>404,data=>array(),'message'=>'失败'));
        }


    }

    //添加三级菜单

    public function addLevelThreeInfo(){

        $one_id = I('parent');
        $tow_id = I('tow_parent');
        $action_name = I('action_name');
        $action_code = I('action_code');
        $action_url = I('action_url');

        $data['parent_id_new'] = trim($tow_id);
        $data['action_name'] = trim($action_name);
        $data['action_code'] = trim($action_code);
        $data['action_url'] = trim ($action_url);
        $data['is_display'] = 1;
        $data['level'] = 3 ;

        $where['parent_id_new'] = $tow_id;
        $top = M('admin_action')->where($where)->max('is_top');
        $data['is_top'] = $top+1;
        $data['type'] = 1;



        try{

            M('admin_action')->add($data);

            $this->success('添加成功');

        }catch(Exception $e){

            $this->error('添加失败');

            $e->getMessage();
        }


    }


    public function left_menu(){

        $where['a.level'] = array('eq',2);

        $menus = M("admin_action a")
            ->field('a.*,b.action_id as bid,b.action_name baction_name')
            ->join('left join sn_admin_action  as b on a.parent_id_new = b.action_id ')
            ->where($where)->select();

        $this->assign("menus",$menus);


        //查询一级菜单
        $where = array();
        $where['level'] = 1;
        $parent = M("admin_action")->where($where)->select();



        //查询二级菜单

        $where = array();
        $where['level'] = 2;
        $leve_tow_parent = M("admin_action")->where($where)->select();


        $this->assign('parent',$parent);


        $this->display();
    }



    public function getTowLevelMenu()
    {

        $id = I('id');

        $where['level'] = 2;
        $where['parent_id_new'] = $id;


        $temp = $this->menuModel->getMenuList($where);


        if($temp)
        {

            foreach($temp as $key=>$val)
            {

                $menu_list[$key]['action_id'] = $val['action_id'];
                $menu_list[$key]['action_name'] = $val['action_name'];

            }


            $this->ajaxReturn(array('ret'=>200,'data'=>$menu_list,'message'=>'查询成功'));


        }else{

            $this->ajaxReturn(array('ret'=>404,'data'=>array(),'message'=>'查询成功'));


        }






    }



    public  function updateLeveTow()
    {
        
        
        $id = I('id');


        $where['action_id'] = $id;
        $menu_info  =  $this->menuModel->getMenuInfo($where);


        $where = array();
        $where['level'] = 1;
        $parent = M("admin_action")->where($where)->select();

        //查询一级菜单

        $this->assign("one_parent",$parent);

        $this->assign('menu_info',$menu_info);

        $this->display('update_tow');

        
    }



    public function headerMenuList()
    {

        $temp =  $this->menuModel->getLevelOne();

        $list = array();


        if($temp){

            foreach($temp as $key=>$val)
            {

                $list[$key]['action_id'] = $val['action_id'];
                $list[$key]['action_name'] = $val['action_name'];
                $list[$key]['action_code'] = $val['action_code'];
                $list[$key]['action_url'] = $val['action_url'];
            }

        }



        return $list;


    }


    public function getLeftMenu($where)
    {

        $temp = $this->menuModel->getLeftMenuList($where);

        $where['a.delete'] = 0;



        if($temp)
        {
            
            foreach($temp as $k=>$val)
            {
                if($val['level']==2)
                {
                    $list[$k]['level_tow']['action_id'] = $val['action_id'];
                    $list[$k]['level_tow']['action_code'] = $val['action_code'];
                    $list[$k]['level_tow']['action_name'] = $val['action_name'];
                    $list[$k]['level_tow']['action_url'] = $val['action_url'];
                }


                if($list && $val['level']==3)
                {

                    foreach($list as $kk => $vv)
                    {

                        if($vv['level_tow']['action_id'] == $val['parent_id_new'] )
                        {
                            $num = count($list[$kk]['child']);
                            $list[$kk]['child'][$num]['action_id'] = $val['action_id'];
                            $list[$kk]['child'][$num]['action_code'] = $val['action_code'];
                            $list[$kk]['child'][$num]['action_name'] = $val['action_name'];
                            $list[$kk]['child'][$num]['action_url'] = $val['action_url'];

                        }


                    }


                }

                
                
            }

            
        }




        return $list;



    }


    public function threeList(){

        $where['a.level'] = array('eq',3);
        $where['a.delete'] = 0;

        $menus = M("admin_action a")
            ->field('a.*,b.action_id as bid,a.action_id aid,b.parent_id_new,b.action_name baction_name,a.parent_id_new,b.action_id,c.action_name as caction_name')
            ->join('left join sn_admin_action  as b on a.parent_id_new = b.action_id ')
            ->join('left join sn_admin_action  as c on b.parent_id_new = c.action_id ')
            ->where($where)->select();



        $this->assign("menus",$menus);




        //查询一级菜单
        $where = array();
        $where['level'] = 1;
        $parent = M("admin_action")->where($where)->select();



        //查询二级菜单

        $where = array();
        $where['level'] = 2;
        $leve_tow_parent = M("admin_action")->where($where)->select();




        $this->assign('parent_one',$parent);
        $this->assign('parent_tow',$leve_tow_parent);


        $this->display('three_menu');






    }


    public function updateMenu()
    {


        $id = I('id');
        $action_name = I('action_name');
        $action_code = I('action_code');
        $action_url = I('action_url');
        $parent_id = I('parent_id');

        $data['parent_id_new'] = trim($parent_id);
        $data['action_name'] = trim($action_name);
        $data['action_code'] = trim($action_code);
        $data['action_url'] = trim ($action_url);


        $where['parent_id_new'] = $parent_id;
        $top = M('admin_action')->where($where)->max('is_top');
        $data['is_top'] = $top+1;



        try{

            $where = array();

            $where['action_id'] = $id;

            $result = $this->menuModel->updateMenu($where,$data);


            if($result)
            {
                $this->ajaxReturn(array('ret'=>200,'data'=>array(),'message'=>''));


            }else{
                $this->ajaxReturn(array('ret'=>404,'data'=>array(),'message'=>''));

            }



        }catch(Exception $e){
            

            $this->ajaxReturn(array('ret'=>404,'data'=>array(),'message'=> $e->getMessage()));


        }






    }
    
    
    
    



}