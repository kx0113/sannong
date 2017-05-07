<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/12/15
 * Time: 16:41
 */

namespace Manage\Controller;

use Manage\Controller\BaseSecondController;


class SysAdminController extends BaseSecondController
{

    public function __construct()
    {

        parent::__construct();
    }


    public function index()
    {




        $this->display('index');



    }

}