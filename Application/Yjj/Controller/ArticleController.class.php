<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 11:57
 */

namespace Yjj\Controller;
use Common\Common\General;
use Think\Controller;
use Common\Common\StringHandle;

class ArticleController extends Controller {

    /*
     * 获取培训信息列表
     * @param page 页码，默认1
     * @return json
     */
    public function traininfo(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = 10;
        $limit = ($page - 1) * $offset;
        $where = array();
        $where['software'] = 2;
        $m = M('Traininginfo');
        $infos = $m->field('id, image, title, address, stime,im')
            ->where($where)
            ->order('addtime DESC')
            ->limit($limit, $offset)
            ->select();
        foreach($infos as $k => $v){
            $infos[$k]['image'] = '/Uploads/'.$v['image'];
            $infos[$k]['stime'] = substr($v['stime'], 5, 11);
        }
        $general = new General();
        $general->returnData($infos);
    }
    /*
     * 获取视频课件列表
     * @param dept 部门id（可选）
     * @param page 页码（可选），默认1
     * @return json
     */
    public function getVideos(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = 10;
        $limit = ($page - 1) * $offset;
        $where = array();
        $where1 = array();
        $where1['software'] = 2;
        if(isset($_GET['dept'])){
            $where['dept_id'] = intval($_GET['dept']);
        }
        $m = M('Video');
        $file_sub_path=$_SERVER['DOCUMENT_ROOT'].'/';
        $infos = $m->field('id, dept, title, image, addtime, url,text,text_name,url3,source_type')
            ->where($where)
            ->where($where1)
            ->order('addtime DESC')
            ->limit($limit, $offset)
            ->select();
        foreach($infos as $k => $v){
            if($v['source_type'] == 2){
                if($v['text'] == ''){
                    $infos[$k]['url2'] = '';
                }else{
                    $file_path=$file_sub_path.$v['text'];
                    $file_name = $v['text_name'];
                    $file_path = '124.133.16.116:8110'.substr($file_path,-42);
                   
                    mb_convert_encoding($v['text_name'], "gb2312", "UTF-8");
                    $type = pathinfo($file_name);
                    $extension = $type['extension'];
                    if($extension == 'xls'){
                        $infos[$k]['url2'] = 'http://'.$file_path;
                    }elseif($extension == 'rar'){
                        $infos[$k]['url2'] = 'http://'.$file_path;
                    }elseif($extension == 'zip'){
                        $infos[$k]['url2'] = 'http://'.$file_path;
                     }elseif($extension == 'doc'){
                        $infos[$k]['url2'] = 'http://'.$file_path;
                     }elseif($extension == 'ppt'){
                        $infos[$k]['url2'] = 'http://'.$file_path;
                    }elseif($extension == 'pdf'){
                        $infos[$k]['url2'] = 'http://'.$file_path;
                    }
                    else{
                        $file_path = base64_encode($file_path);
                        $infos[$k]['url2'] = 'http://'."124.133.16.116:8110/Yjj/Yingyong/down/file_path/$file_path/file_name/$file_name";
                    }
                }
            }
            $infos[$k]['image'] = '/Uploads/'.$v['image'];
            $infos[$k]['addtime'] = substr($v['addtime'], 5, 11);
            unset($infos[$k]['text']);
            unset($infos[$k]['text_name']);
        }
        $general = new General();
        $general->returnData($infos);
    }

    /*
     * 获取直播列表
     * @param page 页码（可选，默认1）
     * @return json
     */
    public function getLives(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = 10;
        $limit = ($page - 1) * $offset;
        $where = array('status' => 2);
        $where1 = array('software' => 2);
        $data = M('Live')->field('id, image, title, jianjie, url')
            ->where($where)
            ->where($where1)
            ->order('_order ASC')
            ->limit($limit, $offset)
            ->select();
        foreach($data as $key => $val){
            if($val['image']){
                $data[$key]['image'] = '/Uploads/'.$val['image'];
            }
        }
        $general = new General();
        $general->returnData($data);
    }
    /* 
     * 易家家部门文章列表页
     * 
     *  */
    public function getInfo(){
        $general = new General();
        if(!isset($_POST['dept'])){
            $general->error(13);
        }
        $where = array();
        $dept = intval($_POST['dept']);
        $where['sn_yjj_dept_article.dept_id'] = $dept;
        $menu = intval($_POST['menu']);
        if($menu){
            $where['menu_id'] = $menu;
        }
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = 10;
        $limit = ($page - 1) * $offset;
        $model = D('DeptArticle');
        $infos = $model->getYjjInfos($where, $limit, $offset, 'aid, title, picture, content, addtime,info');
        $shandle = new StringHandle();
        foreach($infos as $k => $v){
            $infos[$k]['picture'] = '/Uploads/'.$v['picture'];
            $infos[$k]['content'] = $shandle->sub_str(html_entity_decode($v['content']), 30);
            $infos[$k]['addtime'] = substr($v['addtime'], 5, 11);
        }
        $general->returnData($infos);
    }

    /*
     *获取部门菜单
     * @param dept_id 部门id
     * @return json
     */
    public function getMenu(){
        $general = new General();
        if(!isset($_POST['dept'])){
            $general->error(13);
        }
        $dept = intval($_POST['dept']);
//        $dept = 52;
        $menus = M('yjjmenu')->field('mid, mname, pid, url')
        ->where(array('dept_id' => $dept))
        ->order('morder ASC')
        ->select();
        $menus = listToTree($menus, 'mid', 'pid');
        foreach($menus as $key=>&$v){
            $a = $v['_child'];
            if(empty($v['_child'])){
                $v['_child'] = array();
            }
        }
        if(!$menus){
            $menus = array();
        }
        $general->returnData($menus);
    }
/*
 *
 * 易家家部门文章详情
 */
    public function dept_article_detail(){
        $general = new General();
        if (empty($_GET['id'])) {
            $general->error(6);
        } else {
            $where = array();
            $where['aid'] = $_GET['id'];
        }

        $info = M('yjj_dept_article')->where(array('aid'=>$_GET['id']))->find();
        if (empty($info)) {
            $general->error(104);
        }else{
            $info['content'] = htmlspecialchars_decode($info['content']);
        }
        $this->assign('infos', $info);
        $this->display();
    }
}