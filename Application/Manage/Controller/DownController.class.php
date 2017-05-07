<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/17
 * Time: 11:08
 */

namespace Manage\Controller;
use Manage\Controller\BaseSecondController;
use Manage\Controller\MenuController;

class DownController extends  BaseSecondController
{

    public function __construct(){

        parent::__construct();
        $this->menuController= new  MenuController();
        $left_menu =  $this->menuController->getLeftMenu(78);
        $this->assign('left_menu',$left_menu);
        $this->assign('header_value','phoneDown');

    }


    public  function index()
    {

        $this->display('index');
    }



}