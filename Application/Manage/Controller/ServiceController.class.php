<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 16:02
 */

namespace Manage\Controller;


use Think\Controller;

use Manage\Controller\BaseSecondController;
use Manage\Controller\MenuController;

class ServiceController extends BaseSecondController
{

    public function __construct()
    {

        parent::__construct();
        $this->menuController = new MenuController;
        $left_menu =  $this->menuController->getLeftMenu(64);

        $this->assign('left_menu',$left_menu);
    }



    public function index()
    {
        session_start();
        //var_dump(session());die();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $eptModel = M('Expert');
        $where = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['ename'] = array('like', "%{$key}%");
        }
        $count = $eptModel->where($where)->count();
        $infos = $eptModel->where($where)->order('eid desc')->page($p.',10')->select();
        if(!empty($infos)){
            foreach ($infos as &$val){
                if (!empty($val['did'])){
                    $arr = D('Expert')->getCate(array('id'=>$val['did']));
                    $val['ly'] = $arr[0]['name'];
                }
            }

            foreach ($infos as &$item){
                switch ($item['level']){
                    case 1:
                        $item['level'] = '国家级';
                        continue;
                    case 2:
                        $item['level'] = '省级';
                        continue;
                    case 3:
                        $item['level'] = '市级';
                        continue;
                    default:
                        $item['level'] = '未知';
                        continue;
                }
            }
        }
        $page = getPage($count, 10);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('liclass', 'diagnosis');
        $this->assign('aclass', 'index');
       // $this->assign('menus', session('menus'));
      //  $this->assign('liclass', 'service');
      //  $this->assign('aclass', 'index');
        $this->display();

    }
    public function videos(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M('Video');
        $where = array();
        $where1 = array();
        if($_GET['keyword']){
            $key = txt($_GET['keyword']);
            $where['title'] = array('like', "%{$key}%");
        }
        if($_GET['type']){
            $type = txt($_GET['type']);
            $where1['software'] = array('like', "%{$type}%");
        }
       // $where['software'] = 1;
        $count = $m->where($where)->where($where1)->count();
        $page = getPage($count, 10);
        $infos = $m->where($where)->where($where1)->order('addtime DESC')->limit($page->firstRow, $page->listRows)->select();
        $this->assign('cz',$type);
        $this->assign('infos', $infos);
        $this->assign('page', $page->show());
        $this->assign('menus', session('menus'));
        $this->assign('liclass', 'education');
        $this->assign('aclass', 'video_list');
      //  $this->assign('liclass', 'ygl');
      //  $this->assign('aclass', 'ygl_videos');
        $this->display();
    }
    public function vplay(){

        $vid = isset($_GET['vid']) ? intval($_GET['vid']) : 0;
        $where['id'] = $vid;
        $info = M('Video')->where($where)->find();
        if(empty($info)){
            $this->error('视频不存在');

        }else{
            $this->assign('vinfo', $info);
        }
        $this->display();
    }



}