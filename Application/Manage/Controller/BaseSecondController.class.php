<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 16:17
 */

namespace Manage\Controller;

use Think\Controller;
use Manage\Controller\MenuController;

class BaseSecondController extends Controller
{
    public function __construct(){


        parent::__construct();

        $this->menuController = new MenuController();

        $headList = $this->menuController->headerMenuList();


        session_start();

        if($_SESSION['admin']['logname'] != 'manage'){
            if(!session('admin')){
                redirect('/Manage/Public/login');
            }

        }




        $this->assign('headList',$headList);

    }




}