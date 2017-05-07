<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 16:01
 */

namespace Manage\Controller;


use Think\Controller;

use Manage\Controller\BaseSecondController;
use Manage\Controller\MenuController;

class IntellAdminController extends BaseSecondController
{

    public function __construct()
    {

        parent::__construct();

        $this->menuController = new MenuController;

        $left_menu =  $this->menuController->getLeftMenu(63);

        $this->assign('left_menu',$left_menu);

    }


    public function index()
    {

        $this->display('index');
    }
    /*
     * 应急通知
     */
    public function emer_warning_notcie(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("big_data");
        $where = array(
            'type'=>1,
            'status'=>4
        );
        $count = $m->where($where)->count();
        $page = getPage($count, 10);

        $big_data = $m->where($where)->order('id desc')->page($p.',10')->select();
        foreach($big_data as $k=>&$v){
            $v['content'] = htmlspecialchars_decode($v['content']);
            $v['images'] = '/Uploads'.'/'.$v['images'];
        }
        $this->assign('page', $page->show());
        $this->assign('liclass', 'contingency');
        $this->assign('aclass', 'emer_warning_notcie');
        //dump($big_data);exit;
        $this->assign('big_data', $big_data);
        $this->display();
    }
    public function important_project(){
        session_start();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $m = M("project");
        $count = $m->count();
        $page = getPage($count, 10);
        $project = $m->order('pid desc')->page($p.',10')->select();
        foreach($project as $k=>&$v){
            $v['introduce'] = htmlspecialchars_decode($v['introduce']);
            $v['pic'] = '/Uploads'.'/'.$v['pic'];
        }
        $this->assign('page', $page->show());
        $this->assign('liclass', 'keyproject');
        $this->assign('aclass', 'important_project');
        //dump($big_data);exit;
        $this->assign('project', $project);
        $this->display();
    }
}