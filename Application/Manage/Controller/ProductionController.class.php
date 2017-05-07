<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 15:58
 */

namespace Manage\Controller;

use Manage\Controller\BaseSecondController;

use Manage\Controller\MenuController;


class ProductionController extends  BaseSecondController
{

    public function __construct()
    {

        parent::__construct();

        $this->menuController = new MenuController;

        $left_menu =  $this->menuController->getLeftMenu(61);

        $this->assign('left_menu',$left_menu);
    }


    public function index()
    {

        $this->display('index');
    }





}