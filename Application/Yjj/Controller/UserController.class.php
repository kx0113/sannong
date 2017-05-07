<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2
 * Time: 9:25
 */

namespace Yjj\Controller;
use Common\Common\General;

class UserController extends BaseController{

    /*
     * 更改手机号
     * @param account 用户账号
     * @param token 用户Token
     * @param newmob 用户新手机号
     * @param type 'user':普通用户; 'expert':专家用户
     * @return json
     */
    public function changeMobile(){
        $general = new General();
        $newmob = trim($_POST['newmob']);
        $account = trim($_POST['account']);
        $type = trim($_POST['type']);
        if(!$general->isMobile($newmob)){
            $general->error(2);
        }
        //检验手机号是否存在
        if($type == 'user'){
            $model = D('YjjUser');
            $user  = $model->getUser(array('mobile' => $newmob));
            if(!empty($user)){
                $general->error(26);
            }
            //修改操作
            $rel = $model->upUser(array('uname' => $account), array('mobile' => $newmob));
            if($rel !== false){
                $general->returnData();
            }else{
                $general->error(27);
            }
        }else if($type == 'expert'){
            $model = D('Expert');
            $user  = $model->getUser(array('mobile' => $newmob));
            if(!empty($user)){
                $general->error(26);
            }
            //修改操作
            $rel = $model->upExpert(array('account' => $account), array('mobile' => $newmob));
            if($rel !== false){
                $general->returnData();
            }else{
                $general->error(27);
            }
        }else{
            $general->error(28);
        }

    }
   //易家家搜索好友功能
    public function searchFriend(){
        $general = new General();
        if (IS_POST){
            if (isset($_POST['s_uname'])){
                $where['uname'] = trim($_POST['s_uname']);
            }else{
                $general->error(6);
            }
            $info = M('yjj_user')->field('uname,headimg,real_name,sex')->where($where)->select();
            if (!empty($info)){
                $info = $info[0];
                if ($info['sex'] == 1){
                    $info['sex'] = '男';
                }else{
                    $info['sex'] = '女';
                }
            }else{
                $general->error(9);
            }
            $general->returnData($info,'succees');
        }else{
            $general->error(6);
        }
    }
}