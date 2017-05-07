<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 15:59
 */

namespace Manage\Controller;
use Manage\Controller\BaseSecondController;
use Manage\Controller\MenuController;

class ManageController extends BaseSecondController{
    public function __construct(){
        parent::__construct();
        $this->menuController= new  MenuController();
        
        $left_menu =  $this->menuController->getLeftMenu(62);
        $this->assign('left_menu',$left_menu);
        $this->assign('header_value','manage');

    }
    /*
     * 大数据
     * by King
     * 2016-12-16
     * */
    public function index(){
        $where1 = array();
        $where2 = array();
        $where3 = array();
        if ($_GET['date1'] !=''){
            $where1['adddate'] = array('egt',$_GET['date1']);
        }else{
            $where1['adddate'] = array('egt',date('Y-m-d',strtotime('-6 day')));
        }
        if ($_GET['date2'] !=''){
            $where3['adddate'] = array('elt',$_GET['date2']);
        }else{
            $tday = date('Y-m-d');
            $where3['adddate'] = array('elt',$tday);
        }
        if (isset($_GET['dept_id'])){
            $where2['dept_id'] = $_GET['dept_id'];
        }else{
            $where2['dept_id'] = 1;
        }
        $num = (strtotime($where3['adddate'][1])-strtotime($where1['adddate'][1]))/86400+1;
        $sc_time = array();
        for ($s=0;$s<=$num;$s++){
            $sc_time[] = date('Y-m-d',(strtotime($where1['adddate'][1])+$s*86400));
        }

        $fix = array('is_display'=>1);
        $c = M('jgqs_cates');
        $dis = $c->field('id,name')->where($where2)->where($fix)->order('id desc')->limit(5)->select();

        if (!empty($dis)){
            $cate = array();
            $ids = array();
            foreach ($dis as $val){
                $cate[] = $val['name'];
                $ids[] = $val['id'];
            }

        }else{
            $cate = array();
            $ids = array();
        }
        $where4 = array();
        $m = M('jgqs');
        foreach ($ids as $k=>$item){
            $where4['cate_id'] = $item;
            $infos[$k] = $m->field('cate_id,cate_name,price,adddate')->where($where1)->where($where3)->where($where4)->order('adddate asc')->select();
        }

        $temp = $infos;
        foreach ($infos as $k=>$item){
            foreach($item as $key=>$val){
                $infos[$k][$key] = '';
            }
        }
        foreach ($temp as $key=>$val){
            foreach ($val as $kk=>$vv){
                $real_key = (strtotime($vv['adddate'])-strtotime($where1['adddate'][1]))/86400;
                $infos[$key][$real_key] = $vv;
            }
        }
        foreach ($infos as $key=>$val){
            for ($i=0;$i<$num;$i++){
                if (empty($infos[$key][$i])){
                    $infos[$key][$i] = array('cate_id'=>'','cate_name'=>'','price'=>'','adddate'=>'');
                }
            }
            ksort($infos[$key]);
        }
        //var_dump($cate);die();
        $this->assign('cz',$where2['dept_id']);
        $this->assign('cate',$cate);
        $this->assign('sc_time',$sc_time);
        $this->assign('infos',$infos);


        $this->assign('liclass','bigdata');

        $this->assign('aclass','1111');

        $this->display('index');




    }
}