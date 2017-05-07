<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 14:53
 */
namespace Ygl\Controller;
use Think\Controller;
use Common\Common\General;

class BaseController extends Controller {

    public function __construct(){
        parent::__construct();

        $this->checkToken();
    }

    protected function checkToken(){

        $general = new General();
        if(!isset($_POST['account'])){
            $general->error(14);
        }
        if(!isset($_POST['token'])){
            $general->error(15);
        }
        $account = $_POST['account'];
        $token = $_POST['token'];
        if(!$general->checkToken($account, $token)){
            $general->error(16);
        }
    }
}