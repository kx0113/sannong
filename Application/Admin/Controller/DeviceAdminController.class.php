<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/23
 * Time: 17:44
 */

namespace Admin\Controller;


use Admin\Controller\AdminBaseController;
use Admin\Model\DeviceAdminModel;
use Think\Exception;


class DeviceAdminController extends AdminBaseController
{

    public function __construct()
    {

        parent::__construct();
        $this->deviceAdminModel = new DeviceAdminModel();

        $this->assign('level_one_id',3);


    }


    /*
    * 添加设备管理员
    */

    public function index()
    {


        $p = I('p', 1, 'intval');

        $keywords = trim(I('keywords'));
        /*获取管理员的类型*/

        if ($keywords) {
            $where['name'] = array('like', '%' . $keywords . '%');

        }

        $where['_string'] = '1=1';


        $deviceAdmin = $this->deviceAdminModel->getAdminConditory($where, $p);
        $this->assign('level_one_id',16);
        $this->assign('level_second_id',19);
        $this->assign('deviceAdmin', $deviceAdmin);
        $this->display('index');

    }


    /*获取设备管理员的id和name*/

    public function adminList()
    {
        $result = array();

        $temp = $this->deviceAdminModel->getAdminList();


        if ($temp) {

            foreach ($temp as $key => $val) {

                $result[$key]['id'] = $val['id'];
                $result[$key]['name'] = $val['name'];
            }


        }

        return $result;


    }


    /*添加设备管理员*/
    public function addAdmin()
    {


        $this->display('addAdmin');

    }


    public function addAdminInfo()
    {

        $name = trim(I('name'));

        $phone = trim(I('phone'));

        $email = trim(I('email'));


        if ($name) {

            $data['name'] = $name;
        }

        if ($phone) {
            $data['phone'] = $phone;

        }

        if ($email) {
            $data['email'] = $email;

        }

        $data = array_filter($data);

        try {

            if ($data) {
                $this->deviceAdminModel->startTrans();

                $data['created_at'] = date('Y-m-d H:i:s', time());
                $data['updated_at'] = date('Y-m-d H:i:s', time());
                $data['delete'] = 0;


                $result = $this->deviceAdminModel->addAdmin($data);


                if (is_numeric($result)) {


                    $this->deviceAdminModel->commit();

                    $this->success('数据添加成功','index');


                } else {


                    $this->deviceAdminModel->rollback();

                    throw new Exception('管理员信息添加是失败');

                }


            } else {

                throw new Exception('管理员信息不能为空');
            }


        } catch (Exception $e) {


            $where['name'] = $name;
            $message = '';

            $admin_array = $this->deviceAdminModel->getAdminInfo($where);

            if ($admin_array) {
                $message = '你添加的管理员存在';
            }


            $admin_info['name'] = $name;
            $admin_info['phone'] = $phone;
            $admin_info['email'] = $email;

            $this->assign('message', $e->getMessage() . $message);

            $this->assign('admin_info', $admin_info);

            $this->display('addAdmin');


        }


    }


    public function updateAdmin()
    {

        $id = I('id');

        $where['id'] = $id;

        $admin_info = $this->deviceAdminModel->getAdminInfo($where);


        $this->assign('admin_info', $admin_info);

        $this->display('updateAdmin');

    }


    public function updateAdminInfo()
    {


        $id = I('id');

        $name = trim(I('name'));

        $phone = trim(I('phone'));

        $email = trim(I('email'));


        if ($name) {

            $data['name'] = $name;
        }

        if ($phone) {
            $data['phone'] = $phone;

        }

        if ($email) {
            $data['email'] = $email;

        }

        $data = array_filter($data);

        try {

            if ($data && $id) {
                $where = array('id' => $id);

                //获取管理员信息

                $admin_info = $this->deviceAdminModel->getAdminInfo($where);


                if ($admin_info['name'] == $data['name'] && $admin_info['phone'] == $data['phone'] && $data['email']==$admin_info['email']) {


                    $this->success('数据添加成功','index');

                } else {

                    $this->deviceAdminModel->startTrans();

                    $result = $this->deviceAdminModel->updateAdmin($where, $data);

                    echo $result;

                    if (is_numeric($result)) {

                        $this->deviceAdminModel->commit();

                        $this->success('数据gen新成功','index');


                    } else {


                        $this->deviceAdminModel->rollback();

                        throw new Exception('管理员信息添加是失败');

                    }

                }


            } else {

                throw new Exception('管理员信息不能为空');
            }


        } catch (Exception $e) {

            $admin_info = array();

            $where['name'] = $name;
            $where['id'] = array('neq', $id);
            $message = '';

            $admin_array = $this->deviceAdminModel->getAdminInfo($where);

            if ($admin_array) {
                $message = '管理员已经存在';
            }else{
                $message = $e->getMessage();
            }

            echo $message;


            $admin_info['name'] = $name;
            $admin_info['phone'] = $phone;
            $admin_info['email'] = $email;

            $this->assign('message',$message);

            $this->assign('admin_info', $admin_info);

            $this->display('updateAdmin');


        }


    }



    public function deleteAdmin()
    {
        $id = I('id');

        $where['id'] = $id;


        $result = $this->deviceAdminModel->delAdmin($where);

        if($result==1)
        {

            $result = array('ret'=>200,
                'data'=>array('status'=>'000000'),
                'message'=>'删除成功'
            );


        }else{

            $result = array('ret'=>200,
                'data'=>array('status'=>'000001'),
                'message'=>'删除失败'
            );


        }

        $this->ajaxReturn($result);



    }


    


}
