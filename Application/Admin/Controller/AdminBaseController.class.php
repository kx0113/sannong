<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2017/1/6
 * Time: 14:17
 */

namespace Admin\Controller;

use Manage\Controller\BaseController;

class AdminBaseController extends BaseController
{
    public function __construct()
    {

        parent::__construct();
        $this->assign('menus', session('menus'));
    }

}