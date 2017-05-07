<?php
namespace Ygl\Controller;
use Common\Common\General;
use Think\Controller;

class LinkController extends Controller {

    public function __construct(){
        parent::__construct();
        $this->checkToken();
    }

    public function checkToken(){
        $general = new General();
        if(!isset($_GET['account'])){
            $general->error(14);
        }
        if(!isset($_GET['token'])){
            $general->error(15);
        }
        $account = $_GET['account'];
        $token = $_GET['token'];
        if(!$general->checkToken($account, $token)){
            $general->error(16);
        }
    }
}