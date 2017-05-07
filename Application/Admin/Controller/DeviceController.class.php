<?php
/**
 * Created by PhpStorm.
 * User: 14928
 * Date: 2016/11/11
 * Time: 9:37
 */

namespace Admin\Controller;

use Admin\Controller\AdminBaseController;
use Admin\Model\DeviceModel;
use Admin\Model\AreaModel;
use Admin\Controller\DeviceAdminController;

use Think\Exception;

class DeviceController extends AdminBaseController
{

    private $deviceMoedl;

    public function __construct()
    {
        parent::__construct();
        $this->deviceModel = new DeviceModel();
        $this->areaModel = new AreaModel();
        $this->areaController =  new AreaController();
        $this->deviceAdminController = new DeviceAdminController();
        $this->assign('level_one_id',9);

    }



    /*
     * 展示设备列表
     * @return array $result 返回的数据
     */
    public function index()
    {
        $area_town = I('area_town');
        $area_village = I('village_area');
        $type = I('type');
        $keywords =trim(I('keywords'));
        $admin = I('admin');
        $p = I('p',1,'intval');


        /*获取乡镇一级区域*/
        $town_area_list = array();
        $village_area_list = array();
        $admin_list = array();
        $type_list = array();
        $town_area_info= array();
        $village_area_info = array();

        /*获取乡镇一级区域*/
        $town_area_list = $this->areaController->selectTownList();

        /*获取村区域*/

        if($area_town) {

            $area_one_where = array('id' => $area_town);
            $town_area_info = $this->areaModel->findArea($area_one_where);

            $village_area_list = $this->areaController->selectVillageById($area_town);

        }

        /*获取设备的类型*/

        $type_list = $this->deviceModel->getDeviceType();


        /*获取设备管理员*/

        $admin_list = $this->deviceAdminController->adminList();


        if($area_village ){

            $where['area_id'] = $area_village;
            $area_one_where = array('id'=>$area_village);
            $village_area_info = $this->areaModel->findArea($area_one_where);

        }else if($village_area_list){

            $count = count($village_area_list);
            $area_string = '';

            foreach($village_area_list  as $key=> $val){
                if($key <$count-1){

                    $area_string.=$val['id'].',';

                }else{

                    $area_string.= $val['id'];
                }

            };

            $where['area_id'] = array('in',$area_string);
        }


        if($type)
        {
            $where['type'] =  $type ;
        }

        if($keywords)
        {
            $where['device_name'] =  array('like',"%$keywords%");
        }

        if($admin)
        {

            $where['admin_name'] =  $admin;
        }


        $where['_string'] = '1=1';

        $list = $this->deviceModel->getDeviceByCondition($where,$p);


        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'device');
        $this->assign('admin_id',$admin);
        $this->assign('keywords',$keywords);
        $this->assign('type',$type);
        $this->assign('type_list',$type_list);
        $this->assign('admin_list',$admin_list);
        $this->assign('village_area_info',$village_area_info);
        $this->assign('town_area_info',$town_area_info);
        $this->assign('village_area_list',$village_area_list);
        $this->assign('town_area_list',$town_area_list);
        $this->assign('level_second_id',12);
        $this->assign('device_list',$list);
        $this->display('index');


    }


    /*
 * 展示设备列表
 * @return array $result 返回的数据
 */
    public function insectIndex()
    {
        $area_town = I('area_town');
        $area_village = I('village_area');
        $type = I('type');
        $keywords =trim(I('keywords'));
        $admin = I('admin');
        $p = I('p',1,'intval');


        /*获取乡镇一级区域*/
        $town_area_list = array();
        $village_area_list = array();
        $admin_list = array();
        $type_list = array();
        $town_area_info= array();
        $village_area_info = array();

        /*获取乡镇一级区域*/
        $town_area_list = $this->areaController->selectTownList();

        /*获取村区域*/

        if($area_town) {

            $area_one_where = array('id' => $area_town);
            $town_area_info = $this->areaModel->findArea($area_one_where);

            $village_area_list = $this->areaController->selectVillageById($area_town);

        }

        /*获取设备的类型*/

        $type_list = $this->deviceModel->getDeviceType();


        /*获取设备管理员*/

        $admin_list = $this->deviceAdminController->adminList();


        if($area_village ){

            $where['area_id'] = $area_village;
            $area_one_where = array('id'=>$area_village);
            $village_area_info = $this->areaModel->findArea($area_one_where);

        }else if($village_area_list){

            $count = count($village_area_list);
            $area_string = '';

            foreach($village_area_list  as $key=> $val){
                if($key <$count-1){

                    $area_string.=$val['id'].',';

                }else{

                    $area_string.= $val['id'];
                }

            }

            $where['area_id'] = array('in',$area_string);
        }


        if($type)
        {
            $where['type'] =  $type;
        }

        if($keywords)
        {
            $where['device_name'] =  array('like',"%$keywords%");
        }

        if($admin)
        {

            $where['admin_id'] =  $admin;
        }

        $where['_string'] = '1=1';
        $where['class'] = 'INSECTS';

        $list = $this->deviceModel->getInsectsByCondition($where,$p);



        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'device');
        $this->assign('level_second_id',10);
        $this->assign('admin_id',$admin);
        $this->assign('keywords',$keywords);
        $this->assign('type',$type);
        $this->assign('type_list',$type_list);
        $this->assign('admin_list',$admin_list);
        $this->assign('village_area_info',$village_area_info);
        $this->assign('town_area_info',$town_area_info);
        $this->assign('village_area_list',$village_area_list);
        $this->assign('town_area_list',$town_area_list);
        $this->assign('device_list',$list);

        $this->display('insects');


    }


    /*
     * 设备添加的进入页*/
    public function addIndex()
    {
        /*获取设备类型*/
        $result= $this->deviceModel->getDeviceType();

        /*获取第一季区域*/
        $firstAreaList = $this->areaController->selectAreaFirst();

        /*获取设备管理员*/

        $admin_list = $this->deviceAdminController->adminList();

        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'device');
        $this->assign('firstAreaList',$firstAreaList);
        $this->assign('admin_list',$admin_list);
        $this->assign('result',$result);

        $this->display('add');

    }


    /*
     * 病虫害设备添加的进入页
     * */
    public function insectsIndex()
    {
        $result= $this->deviceModel->getDeviceType();
        $town_area_list =  $this->areaController->selectTownList();
        $adminList = $this->deviceAdminController->adminList();
        $this->assign('town_area_list',$town_area_list);
        $this->assign('admin_list',$adminList);
        $this->display('addInsects');

    }


    /*
     * 设备更新的进入页*/
    public function updateIndex()
    {

        /*获取设备的id*/
        $id = I('id');

        /* 获取设备的基本信息*/
        $device_info  = $this->deviceModel->findOneDevice(array('a.id'=>$id));


        $result= $this->deviceModel->getDeviceType();



        $thridAreaInfo=  $this->areaModel->getThirdAreaInfo(array('a.id'=>11));
        $firstAreaList = $this->areaController->selectAreaFirst();
        $secondAreaList = $this->areaController->selectAreaSec($thridAreaInfo['cid']);
        $thirdAreaList = $this->areaController->selectAreaThird($thridAreaInfo['bid']);

        /*获取设备管理员*/

        $admin_list = $this->deviceAdminController->adminList();

        /*区域列表*/

        $area_info = $this->areaController->findOneArea($device_info['area_id']);
        $town_area_info['id'] = $area_info['parent_id'];
        $village_area_info['id'] = $device_info['area_id'];
        $type = $device_info['type']=='GREENHOUSE'?1:2;





        $this->assign('liclass', 'ygl');
        $this->assign('aclass', 'device');

        $this->assign('firstAreaList',$firstAreaList);
        $this->assign('secondAreaList',$secondAreaList);
        $this->assign('thirdAreaList',$thirdAreaList);
        $this->assign('thridAreaInfo',$thridAreaInfo);

        $this->assign('admin_list',$admin_list);
        $this->assign('lat',$device_info['lng'].','.$device_info['lat']);
        $this->assign('id',$device_info['id']);
        $this->assign('channel_number',$device_info['channel_number']);
        $this->assign('admin_list',$admin_list);
        $this->assign('type',$type);
        $this->assign('admin_id',$device_info['admin_id']);
        $this->assign('note',$device_info['note']);
        $this->assign('device_code',$device_info['device_code']);
        $this->assign('device_name',$device_info['device_name']);
        $this->assign('admin_id',$device_info['admin_id']);
        $this->display('update');

    }


    /*添加设备信息*/
    public function addDeviceInfo()
    {

        $type  = I('type');
        $device_code = I('device_code');
        $channel_number = I('channel_number');
        $device_name = I('device_name');
        $lat = I('lat');
        $areaThird = I('areaThird');
        $admin_id = I('admin');
        $note = I('note');

        if($type == 1){

            $device_data['type'] =  "INDOOR";

        }else if($type == 2){

            $device_data['type'] =  "OUTDOOR";
        }

        if($lat)
        {
            $latArrar =explode(',',$lat);
            $device_data['lng'] = $latArrar[0];
            $device_data['lat'] = $latArrar[1];
        }

        $device_data['device_name'] = $device_name;
        $device_data['area_id'] = $areaThird;
        $device_data['device_code'] = $device_code;
        $device_data['admin_id'] = $admin_id;
        $device_data['channel_number'] = $channel_number;
        $device_data['note'] = $note;
        $device_data['created_at'] = date("Y-m-d H:i:s",time());


        if($device_data){

            $this->deviceModel->startTrans();
            // 进行相关的业务逻辑操作

            try {

                $result = $this->deviceModel->add('device', $device_data);


                if(is_numeric($result)){

                    $this->success('设备添加成功','index');

                    //$this->onlineAlarmController->addOnlineAlarm($result);

                    $this->deviceModel->commit();

                }else{

                    $this->deviceModel->rollback();

                    throw new Exception('病虫害设备添加失败');
                }

            }catch(Exception $e){


                $message = "";

                //检查信息是不是有重复

                $device_where['device_name'] = $device_name;

                $device_info = $this->deviceModel->getOneInfo('device',$device_where);


                if($device_info){

                    $message = '在相同的区域不能存在相同的设备';
                    $state = "000002";

                }else{

                    $device_where = array();

                    $device_where['device_code'] = $device_code;

                    $device_info = $this->deviceModel->getOneInfo('device',$device_where);

                    if($device_info)
                    {

                        $message = '设备号是唯一的';
                        $state = "000001";

                    }

                }



                $message = $e->getMessage().$message;

                $this->assign('liclass', 'ygl');
                $this->assign('aclass', 'device');

                $this->assign('state',$state);
                $this->assign('type',$type);
                $this->assign('message',$message);
                $this->assign('admin_id',$admin_id);
                $this->assign('channel_number',$channel_number);
                $this->assign('remark',$note);
                $this->assign('device_code',$device_code);
                $this->assign('device_name',$device_name);
                $this->assign('admin',$admin_id );

                $this->addIndex();

            }

        }



    }




    /*
     * 获取一条设备信息
     * @var int $id 设备id号
     * @return $result 返回数组数据  */
    public function getOneDevice()
    {


    }


    /*
     * 保存设备信息数据
     * */
    public function saveDevice()
    {


    }

    /*
     * 更新某个设备信息
     * @var int $id 设备的id号
     *
     * */

    public function updateDeviceInfo()
    {
        $id = I('id');
        $type  = I('type');
        $device_code = I('device_code');
        $device_name = I('device_name');
        $admin_id= I('admin');
        $lat = I('lat');
        $channel_number = I('channel_number');
        $areaThird = I('areaThird');

        if($lat)
        {
            $latArrar =explode(',',$lat);
            $device_data['lng'] = $latArrar[0];
            $device_data['lat'] = $latArrar[1];
        }

        $note = I('note');

        if($type == 1){

            $device_data['type'] =  "GREENHOUSE";

        }else if($type == 2){

            $device_data['type'] =  "OUTDOOR";

        }

        $device_data['device_code'] = $device_code;
        $device_data['device_name'] = $device_name;
        $device_data['parent_id'] = 0;
        $device_data['admin_id'] = $admin_id;
        $device_data['note'] = $note;
        $device_data['channel_number'] = $channel_number;



        if($device_data){

            $this->deviceModel->startTrans();
            // 进行相关的业务逻辑操作

            try {

                    $where['id'] = $id;
                    $result = $this->deviceModel->updateDevice($where,$device_data);


                    if(is_numeric($result)){
                        $this->deviceModel->commit();
                        $this->success('设备更新成功');

                    }else{

                        $this->deviceModel->rollback();

                        throw new Exception('病虫害设备添加失败');
                    }


            }catch(Exception $e){


                $message = "";

                //检查信息是不是有重复

                $device_where['device_name'] = $device_name;
                $device_where['area_id'] = $areaThird;

                $device_info = $this->deviceModel->getOneInfo('device',$device_where);

                if($device_info){

                    $message = '在相同的区域不能存在相同名字的设备';
                    $state = "000002";

                }else{

                    $device_where = array();

                    $device_where['device_code'] = $device_code;

                    $device_info = $this->deviceModel->getOneInfo('device',$device_where);

                    if($device_info)
                    {

                        $message = '设备号是唯一的';
                        $state = "000001";

                    }

                }
                $message = $e->getMessage().$message;

                /*获取设备管理员*/

                $admin_list = $this->deviceAdminController->adminList();

                /*区域*/
                $thridAreaInfo=  $this->areaModel->getThirdAreaInfo(array('a.id'=>$areaThird));

                $firstAreaList = $this->areaController->selectAreaFirst();
                $secondAreaList = $this->areaController->selectAreaSec($thridAreaInfo['cid']);
                $thirdAreaList = $this->areaController->selectAreaThird($thridAreaInfo['bid']);


                $this->assign('type',$type);
                $this->assign('admin_list',$admin_list);
                $this->assign('state',$state);
                $this->assign('id',$id);
                $this->assign('message',$message);
                $this->assign('admin_id',$admin_id);
                $this->assign('note',$note);
                $this->assign('device_code',$device_code);
                $this->assign('device_name',$device_name);
                $this->assign('admin',$admin_id );
                $this->assign('channel_number',$channel_number );
                $this->assign('firstAreaList',$firstAreaList);
                $this->assign('secondAreaList',$secondAreaList);
                $this->assign('thirdAreaList',$thirdAreaList);
                $this->assign('thridAreaInfo',$thridAreaInfo);

                $this->display('update');

            }




        }


    }



    /*
     * 按条件搜索设备信息
     * $where 搜索设备条件
     */

    public function selectDevice()
    {


    }

    /*
     * 获取设备的类型
     * */
    public function deviceTypeAjax()
    {

        $result = array();
        $result= $this->deviceModel->getDeviceType();

        if ($result){

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'成功'));

        }else{

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'没有数据'));
        }


    }


    /*
    * 获取设备的类型
    * */
    public function deviceAdminAjax()
    {

        $result = array();
        $result= $this->deviceModel->getDeviceAdmin();

        if ($result){

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'成功'));

        }else{

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'没有管理员数据'));
        }




    }



    public function deviceInfo()
    {

        $this->display('info');

    }



    /*ajax获取设备列表*/
    public function ajaxDeviceList()
    {

        $village_area = I('village_area');

        $class = I('class');

        $admin = I('admin');

        $result = array();



        if($class == 1)
        {
            $where['class'] = 'BASE';

        }else if($class == 2){

            $where['class'] =  'STANDARD' ;

        }


        if($admin)
        {

            $where['admin_id'] =  $admin;
        }

        if($village_area)
        {

            $where['area_id'] = $village_area;
        }

        $where['_string'] = '1=1';


        try{

            $result = $this->deviceModel->selectDevice($where);


            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'查询成功'));

        }catch(Exception $e){

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'没有管理员数据'));

        }



    }


    public function ajaxInsectDeviceList()
    {

        $village_area = I('village_area');

        $admin = I('admin');

        $result = array();


        $where['class'] =  'INSECTS' ;


        if($admin)
        {

            $where['admin_id'] =  $admin;
        }

        if($village_area)
        {

            $where['area_id'] = $village_area;
        }

        $where['_string'] = '1=1';


        try{

            $result = $this->deviceModel->selectDevice($where);


            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'查询成功'));

        }catch(Exception $e){

            $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'没有管理员数据'));

        }



    }



    /*存储病虫害信息*/

    public function addInsectsDevice()
    {


        $type = I('type', '', 'intval');
        $parent_id = I("parent_id", '', 'intval');
        $device_code = I('device_code');
        $device_name = I('device_name');
        $town_area = I('town_area');
        $village_area = I('village_area');
        $staff = I('staff');
        $channel_number = I('channel_number');
        $staff_mobile = I('staff_mobile');
        $video_url = I('video_url');
        $note = I('remark');


        if ($type == 1) {

            $device_data['type'] = "GREENHOUSE";

        } else if ($type == 2) {

            $device_data['type'] = "OUTDOOR";

        }

        $device_data['device_code'] = $device_code;
        $device_data['device_name'] = $device_name;
        $device_data['area_id'] = $village_area;
        $device_data['admin_phone'] = $staff_mobile;
        $device_data['parent_id'] = $parent_id;

        $device_data['device_code'] = $device_code;
        $device_data['admin_id'] = $staff;
        $device_data['video_url'] = $video_url;
        $device_data['note'] = $note;
        $device_data['channel_number'] = $channel_number;


        $device_data = array_filter($device_data);
        $this->deviceModel->startTrans();
        // 进行相关的业务逻辑操作


        if ($device_data) {

            $device_data['class'] = 'INSECTS';
            $device_data['created_at'] = date("Y-m-d H:i:s", time());

            try {

                $result = $this->deviceModel->add('device', $device_data);

                if($result){
                    $this->deviceModel->commit();

                    $this->success('设备添加成功','insectIndex');

                }else{
                    $this->deviceModel->rollback();

                    throw new Exception('设备添加失败');
                }



            }catch(Exception $e) {

                //检查信息是不是有重复

                $device_where['device_name'] = $device_name;
                $device_where['area_id'] = $village_area;

                $device_info = $this->deviceModel->getOneInfo('device', $device_where);

                    if ($device_info) {

                        $state = '000002';

                    } else {



                        $device_where = array();

                        $device_where['device_code'] = $device_code;

                        $device_info = $this->deviceModel->getOneInfo('device', $device_where);


                        if ($device_info) {

                            $state = '000001';

                        }

                    }

                $town_area_info['id'] = $town_area;
                $village_area_info['id'] = $village_area;

                $village_list = $this->areaController->selectVillageById($town_area);

                $town_area_list =  $this->areaController->selectTownList();

                $adminList = $this->deviceAdminController->adminList();

                $this->assign('town_area_list',$town_area_list);
                $this->assign('admin_list',$adminList);
                $this->assign('channel_number',$channel_number);

                $this->assign('village_area_list',$village_list);
                $this->assign('type', $type);
                $this->assign('village_area_info', $village_area_info);
                $this->assign('town_area_info', $town_area_info);
                $this->assign('admin_id', $staff);
                $this->assign('state', $state);
                $this->assign('village_list', $village_list);
                $this->assign('device_code', $device_code);
                $this->assign('device_name', $device_name);
                $this->assign('village_area', $village_area);
                $this->assign('staff', $staff);
                $this->assign('staff_mobile', $staff_mobile);
                $this->assign('video_url', $video_url);
                $this->assign('remark', $note);

                $this->display('addInsects');







            }




        }

    }


    /*更新病虫害设备*/







    public function delDevice()
    {
        $id = I('id');

        $where['id'] = $id;

        $result = $this->deviceModel->delete('device',$where);

        //检查数据库存不存在的数据

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

    public function paramList()
    {

        $device_id = I('id');


        $where['device_id'] = $device_id;

        $param_array = $this->deviceModel->getDeviceParam($where);



        /* 获取设备的基本信息*/
        $device_info  = $this->deviceModel->findOneDevice(array('a.id'=>$device_id));

        $this->assign('device_info',$device_info);

        $this->assign('param_array',$param_array);

        $this->display('paramList');
    }



    /*设置参数*/



    public function setParam()
    {

        $id = I('id','','intval');
        $min = I('min');
        $max = I('max');
        $show = I('show');


        $where['id'] = $id;

        $data['min'] = trim($min);
        $data['max'] = trim($max);
        $data['show'] = trim($show);


        if($id)
        {

            try{

                $result = $this->deviceModel->updateDeviceParam($where,$data);


                if($result)
                {
                    $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'数据跟新成功'));

                }else{

                    $this->ajaxReturn(array('ret'=>200,'data'=>$result,'message'=>'数据跟新成功'));

                }

            }catch(Exception $e){

                $this->ajaxReturn(array('ret'=>500,'data'=>$result,'message'=>'程序错误'));

            }


        }else{

            $this->ajaxReturn(array('ret'=>500,'data'=>'','message'=>'id错误'));


        }







    }


    public function mapGetPoint()
    {

        $this->display('getPoint');

    }














}
