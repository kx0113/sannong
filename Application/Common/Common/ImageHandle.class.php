<?php


namespace Common\Common;
use Common\Model;

class ImageHandle {
    
    public function image($file,$small_w=0,$small_h=0,$is_small=false){
        $path = date("Ym");
        $max_file_size = 20000000;     			//上传文件大小限制, 单位BYTE
        $path_source = 'upload/'.$path.'/';
//         dump($path_source);die;
        $path_thumb = $path_source.'thumb/'; 		//原大图保存文件夹路径
        $path_small = $path_source.'small/';
    
        if($file["tmp_name"]==""){
            echo "没有临时文件";
            exit;
        }
    
        if($max_file_size < $file["size"])//检查文件大小
        {
            $max_file_size = $max_file_size/1000;
            echo "文件太大，超过 ".$max_file_size." KB!";
            exit;
        }
    
        if(!$this->check_img_type($file["type"]))//检查文件类型
        {
            echo "文件类型不符!".$file["type"];
            //die('acacac');
            exit;
        }
    
        $this->check_file_exists($path_source, $path_thumb, $path_small,$is_small);//路径是否存在
    //die('sdd');
        //$file['tmp_name'] = realpath($file['tmp_name']);
        $filename = $file["tmp_name"];//临时文件名
        $im_size = getimagesize($filename);//获得原图尺寸
        $src_w = $im_size[0];//原图宽度
        $src_h = $im_size[1];//原图高度
        $src_type = $im_size[2];//图片类型
        $pinfo = pathinfo($file["name"]);
        $filetype = $pinfo['extension'];
    
        $all_path = date("YmdHis").rand(1000,9999).".".$filetype;  //路径+文件名,目前以上传时间命名
        //var_dump($all_path);die();
       
        move_uploaded_file ($filename,$path_source.$all_path);//上传文件到上传文件夹
        if($is_small){
            copy($path_source.$all_path,$path_thumb.$all_path);
            $src_im = $this->img_resource($path_source.$all_path, $src_type);//根据来源文件的文件类型创建一个图像操作的标识符
            $this->create_img($path_small.$all_path, $src_type, $small_w, $small_h, $src_im, $src_w, $src_h);
        }
    
        return $all_path;
    
    }
    
    public function multi_upload($files, $directory = '', $small_w=0, $small_h=0, $is_small=false){
        $path = '';
        foreach($files as $key => $val){
            if($val['size'] > 0){
                $img = $this->image($val, $small_w, $small_h, $is_small);
                $path .= $directory.$img.',';
            }
        }
        return substr($path, 0, -1);
    }
    
    /*缩略图*/
    public function small_image($path,$filename,$small_w=0,$small_h=0){
    
        $path_source = __APP__.'upload/'.$path.'/';
        $path_thumb = $path_source.'thumb/'; 		//原大图保存文件夹路径
        $path_small = $path_source.'small/';
    
        $im_size = getimagesize($path_source.$filename);//获得原图尺寸
        $src_w = $im_size[0];//原图宽度
        $src_h = $im_size[1];//原图高度
        $src_type = $im_size[2];//图片类型
    
        $this->check_file_exists($path_source, $path_thumb, $path_small);//路径是否存在
    
        copy($path_source.$filename,$path_thumb.$filename);
        $src_im = $this->img_resource($path_source.$filename, $src_type);//根据来源文件的文件类型创建一个图像操作的标识符
        $this->create_img($path_small.$filename, $src_type, $small_w, $small_h, $src_im, $src_w, $src_h);
    
    }
    
    /**
     * 检查图片类型
     * @param   string  $img_type   图片类型
     * @return  bool
     */
    public function check_img_type($img_type)
    {
        return $img_type == 'image/pjpeg' ||
        $img_type == 'image/x-png' ||
        $img_type == 'image/png'   ||
        $img_type == 'image/gif'   ||
        $img_type == 'image/jpeg';
    }
    /**
     * 检查图片路径
     * @param   string  $path_source		原始图路径
     * @param   string  $path_big			大图路径
     * @param   string  $path_small   		小图路径
     * @param   string  $path_thumbnail		缩略图路径
     */
    public function check_file_exists($path_source, $path_thumb, $path_small,$is_small=true)
    {
        if(!file_exists($path_source))//检查上传目录是否存在，不存在创建
        {
            mkdir($path_source,0700);
        }
        if($is_small){
            if(!file_exists($path_small))//检查上传目录是否存在，不存在创建
            {
                mkdir($path_small,0700);
            }
            if(!file_exists($path_thumb))//检查上传目录是否存在，不存在创建
            {
                mkdir($path_thumb,0700);
            }
        }
    }
    
    /**
     * 根据来源文件的文件类型创建一个图像操作的标识符
     *
     * @access  public
     * @param   string      $img_file   图片文件的路径
     * @param   string      $mime_type  图片文件的文件类型
     * @return  resource    如果成功则返回图像操作标志符，反之则返回错误代码
     */
    public function img_resource($img_file, $mime_type)
    {
        switch ($mime_type)
        {
            case 1:
            case 'image/gif':
                $res = imagecreatefromgif($img_file);
                break;
    
            case 2:
            case 'image/pjpeg':
            case 'image/jpeg':
                $res = imagecreatefromjpeg($img_file);
                break;
    
            case 3:
            case 'image/x-png':
            case 'image/png':
                $res = imagecreatefrompng($img_file);
                break;
    
            default:
                return false;
        }
    
        return $res;
    }
    
    /**
     * 根据来源文件的文件类型创建一个图像操作的标识符
     *
     * @access  public
     * @param   string      $img_file   图片文件的路径
     * @param   string      $mime_type  图片文件的文件类型
     * @return  resource    如果成功则返回图像操作标志符，反之则返回错误代码
     */
    public function create_img($path, $src_type, $sltw, $slth, $src_im, $src_w, $src_h)
    {
        $simclearly = 5;//图片清晰度0-100，数字越大越清晰，文件尺寸越大
        $dst_sim = imagecreatetruecolor($sltw,$slth); //新建缩略图真彩位图
        imagecopyresampled($dst_sim,$src_im,0,0,0,0,$sltw,$slth,$src_w,$src_h); //原图图像写入新建真彩位图中
    
        switch($src_type)
        {
            case 1:
                imagegif($dst_sim, $path, $simclearly);//生成gif文件，图片清晰度0-100
                break;
            case 2:
                imagejpeg($dst_sim, $path, $simclearly);//生成jpg文件，图片清晰度0-100
                break;
            case 3:
                imagepng($dst_sim, $path, $simclearly);//生成png文件，图片清晰度0-100
                break;
            case 6:
                imagewbmp($dst_sim, $path);
                break;
        }
        imagedestroy($dst_sim);//释放缓存
    
        return $path;
    }
    
    /*
     * 功能：PHP图片水印 (水印支持图片或文字)
     * 参数：
     * $groundImage 背景图片，即需要加水印的图片，暂只支持GIF,JPG,PNG格式；
     * $waterPos 水印位置，有10种状态，0为随机位置；
     * 1为顶端居左，2为顶端居中，3为顶端居右；
     * 4为中部居左，5为中部居中，6为中部居右；
     * 7为底端居左，8为底端居中，9为底端居右；
     * $waterImage 图片水印，即作为水印的图片，暂只支持GIF,JPG,PNG格式；
     * $waterText 文字水印，即把文字作为为水印，支持ASCII码，不支持中文；
     * $textFont 文字大小，值为1、2、3、4或5，默认为5；
     * $textColor 文字颜色，值为十六进制颜色值，默认为#FF0000(红色)；
     *
     * 注意：Support GD 2.0，Support FreeType、GIF Read、GIF Create、JPG 、PNG
     * $waterImage 和 $waterText 最好不要同时使用，选其中之一即可，优先使用 $waterImage。
     * 当$waterImage有效时，参数$waterString、$stringFont、$stringColor均不生效。
     * 加水印后的图片的文件名和 $groundImage 一样。
     */
    public function imagewatermark($groundImage,$waterPos=0,$waterImage="",$waterText="",$textFont=5,$textColor='#FF0000',$transparent)
    {
        $isWaterImage = FALSE;
        $formatMsg = "暂不支持该文件格式，请用图片处理软件将图片转换为GIF、JPG、PNG格式。";
    
    
        //读取水印文件
        if(!empty($waterImage) && file_exists($waterImage))
        {
            $isWaterImage = TRUE;
            $water_info = getimagesize($waterImage);
            $water_w = $water_info[0];//取得水印图片的宽
            $water_h = $water_info[1];//取得水印图片的高
            	
            switch($water_info[2])//取得水印图片的格式
            {
                case 1:$water_im = imagecreatefromgif($waterImage);break;
                case 2:$water_im = imagecreatefromjpeg($waterImage);break;
                case 3:$water_im = imagecreatefrompng($waterImage);break;
                default:die($formatMsg);
            }
        }
    
        //读取背景图片
        if(!empty($groundImage) && file_exists($groundImage))
        {
            $ground_info = getimagesize($groundImage);
            $ground_w = $ground_info[0];//取得背景图片的宽
            $ground_h = $ground_info[1];//取得背景图片的高
            	
            switch($ground_info[2])//取得背景图片的格式
            {
                case 1:$ground_im = imagecreatefromgif($groundImage);break;
                case 2:$ground_im = imagecreatefromjpeg($groundImage);break;
                case 3:$ground_im = imagecreatefrompng($groundImage);break;
                default:die($formatMsg);
            }
        }
        else
        {
            die("需要加水印的图片不存在！");
        }
    
        //水印位置
        if($isWaterImage)//图片水印
        {
            $w = $water_w;
            $h = $water_h;
            $label = "图片的";
        }
        else//文字水印
        {
            $temp = imagettfbbox(ceil($textFont*5),0,"./cour.ttf",$waterText);//取得使用 TrueType 字体的文本的范围
            $w = $temp[2] - $temp[6];
            $h = $temp[3] - $temp[7];
            unset($temp);
            $label = "文字区域";
        }
        if( ($ground_w<$w) || ($ground_h<$h) )
        {
            echo "需要加水印的图片的长度或宽度比水印".$label."还小，无法生成水印！";
            return;
        }
        switch($waterPos)
        {
            case 0://随机
                $posX = rand(0,($ground_w - $w));
                $posY = rand(0,($ground_h - $h));
                break;
            case 1://1为顶端居左
                $posX = 0;
                $posY = 0;
                break;
            case 2://2为顶端居中
                $posX = ($ground_w - $w) / 2;
                $posY = 0;
                break;
            case 3://3为顶端居右
                $posX = $ground_w - $w;
                $posY = 0;
                break;
            case 4://4为中部居左
                $posX = 0;
                $posY = ($ground_h - $h) / 2;
                break;
            case 5://5为中部居中
                $posX = ($ground_w - $w) / 2;
                $posY = ($ground_h - $h) / 2;
                break;
            case 6://6为中部居右
                $posX = $ground_w - $w;
                $posY = ($ground_h - $h) / 2;
                break;
            case 7://7为底端居左
                $posX = 0;
                $posY = $ground_h - $h;
                break;
            case 8://8为底端居中
                $posX = ($ground_w - $w) / 2;
                $posY = $ground_h - $h;
                break;
            case 9://9为底端居右
                $posX = $ground_w - $w;
                $posY = $ground_h - $h;
                break;
            default://随机
                $posX = rand(0,($ground_w - $w));
                $posY = rand(0,($ground_h - $h));
                break;
        }
    
        //设定图像的混色模式
        imagealphablending($ground_im, true);
    
        if($isWaterImage)//图片水印
        {
            imagecopymerge($ground_im, $water_im, $posX, $posY, 0, 0, $water_w,$water_h,$transparent);//拷贝水印到目标文件
        }
        else//文字水印
        {
            if( !emptyempty($textColor) && (strlen($textColor)==7) )
            {
                $R = hexdec(substr($textColor,1,2));
                $G = hexdec(substr($textColor,3,2));
                $B = hexdec(substr($textColor,5));
            }
            else
            {
                die("水印文字颜色格式不正确！");
            }
            imagestring ( $ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate($ground_im, $R, $G, $B));
        }
    
        //生成水印后的图片
        @unlink($groundImage);
        switch($ground_info[2])//取得背景图片的格式
        {
            case 1:imagegif($ground_im,$groundImage);break;
            case 2:imagejpeg($ground_im,$groundImage);break;
            case 3:imagepng($ground_im,$groundImage);break;
            default:die($errorMsg);
        }
    
        //释放内存
        if(isset($water_info)) unset($water_info);
        if(isset($water_im)) imagedestroy($water_im);
        unset($ground_info);
        imagedestroy($ground_im);
    }
    
    /**
     * 读取图片
     */
    public function File($path = '',$not_dir=false){
        $tmp_list =array();
        if(empty($path)){
            $path = date('Ym');
        }
        $sub_dir = __APP__.'upload/'.$path.'/';
        if(!file_exists($sub_dir)){
            $path_thumb = $sub_dir.'thumb/'; 		//原大图保存文件夹路径
            $path_small = $sub_dir.'small/';
            $this->check_file_exists($sub_dir,$path_thumb,$path_small);
        }
        $dir = opendir($sub_dir);
        while (($file = readdir($dir)) !== false)
        {
            if($file == '.' || $file == '..') {
                continue;
            } else if(is_dir($sub_dir.$file)){
                continue;
            }else { //如果是文件,直接输出
                if($not_dir){
                    $tmp_list[] = $file;
                }else{
                    $tmp_list[] = $path.'/'.$file;
                }
            }
        }
        return $tmp_list;
    }
    
    /*读取文件夹*/
    public function File_dir(){
        $tmp_dir =array();
        $sub_dir = __APP__.'upload/';
        $dir = opendir($sub_dir);
        while (($file = readdir($dir)) !== false)
        {
            if($file == '.' || $file == '..') {
                continue;
            } else if(is_dir($sub_dir.$file)){
                $tmp_dir[] = $file;
            }else { //如果是文件,直接输出
                continue;
            }
        }
        return $tmp_dir;
    }
}