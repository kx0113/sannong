<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/22
 * Time: 17:50
 */

namespace Manage\Controller;
use Think\Controller;
use Manage\Controller\MenuController;
class BaseController extends Controller{

    public function __construct(){
        parent::__construct();
        session_start();
        if($_SESSION['admin']['logname'] != 'manage'){
            if(!session('admin')){
                redirect('/Manage/Public/login');
            }

        }

        $this->menuController = new MenuController();
        $headList = $this->menuController->headerMenuList();

        $this->assign('headList',$headList);
        //权限控制;
        $where = array();
        $uri = __ACTION__;
        $pattern_url = "/^((?!edit).)*$/";
        $pattern_url1 = "/^((?!Edit).)*$/";
        $pattern_url2 = "/^((?!add).)*$/";
        $pattern_url3 = "/^((?!Add).)*$/";
        $pattern_url4 = "/^((?!mmxx).)*$/";
        $pattern_url5 = "/^((?!detai).)*$/";
        $pattern_url6 = "/^((?!message).)*$/";
        $pattern_url7 = "/^((?!gzhb).)*$/";
        $pattern_url8 = "/^((?!coor).)*$/";
        $pattern_url9 = "/^((?!Men).)*$/";
        $pattern_url10 = "/^((?!De).)*$/";
        $pattern_url11 = "/^((?!de).)*$/";
        $pattern_url12 = "/^((?!exper).)*$/";
        $pattern_url13 = "/^((?!liveSt).)*$/";
        $pattern_url14 = "/^((?!mergenc).)*$/";
        $pattern_url15 = "/^((?!Area).)*$/";
        $pattern_url16 = "/^((?!rou).)*$/";
        $pattern_url17 = "/^((?!jax).)*$/";
        if (preg_match($pattern_url, $uri)){
            if (preg_match($pattern_url1, $uri)){
                if (preg_match($pattern_url2, $uri)){
                    if (preg_match($pattern_url3, $uri)){
                        if (preg_match($pattern_url4, $uri)){
                            if (preg_match($pattern_url5, $uri)){
                                if (preg_match($pattern_url6, $uri)){
                                    if (preg_match($pattern_url7, $uri)){
                                        if (preg_match($pattern_url8, $uri)){
                                            if (preg_match($pattern_url9, $uri)){
                                                if (preg_match($pattern_url10, $uri)){
                                                    if (preg_match($pattern_url11, $uri)){
                                                        if (preg_match($pattern_url12, $uri)){
                                                            if (preg_match($pattern_url13, $uri)){
                                                                if (preg_match($pattern_url14, $uri)){
                                                                    if (preg_match($pattern_url15, $uri)) {
                                                                        if (preg_match($pattern_url16, $uri)) {
                                                                            if (preg_match($pattern_url17, $uri)) {
                                                                                $where['action_url'] = $uri;
                                                                                $res = M('admin_action')->field('action_id,action_code,action_name,action_url')->where($where)->select();
                                                                                if (empty($res)) {
    
                                                                                    $this->error('您请求的页面不存在');
                                                                                }
                                                                                $code = $res[0]['action_id'];
                                                                                $ss = $this->checkSS();
                                                                                foreach ($ss as $k => $v) {
                                                                                    $ss[$k] = str_replace('"', '', $ss[$k]);
                                                                                }
                                                                                if (!in_array($code, $ss)) {
                                                                                    $this->error('您没有访问权限');
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }        
                                                }   
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    public function checkSS(){
        $r = $_SESSION;
        $res = $this->array_get_by_key($r, 'action_id');
        return $res;
    }

    public function array_get_by_key(array $array, $string){
        if (!trim($string)) return false;
        preg_match_all("/\"$string\";\w{1}:(?:\d+:|)(.*?);/", serialize($array), $res);
        return $res[1];
    }
}