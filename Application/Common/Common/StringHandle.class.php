<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 16:49
 */
namespace Common\Common;

class StringHandle {

    //截取字符
    public function sub_str($str, $length = 0, $append = true)
    {
        $str = $this->remove_label($str);
        $strlength = strlen($str);

        if ($length == 0 || $length >= $strlength)
        {
            return $str;
        }
        elseif ($length < 0)
        {
            $length = $strlength + $length;
            if ($length < 0)
            {
                $length = $strlength;
            }
        }

        if (function_exists('mb_substr'))
        {
            $newstr = mb_substr($str, 0, $length, 'utf-8');
        }
        elseif (function_exists('iconv_substr'))
        {
            $newstr = iconv_substr($str, 0, $length,'utf-8');
        }
        else
        {
            //$newstr = trim_right(substr($str, 0, $length));
            $newstr = substr($str, 0, $length);
        }

        if ($append && $str != $newstr)
        {
            $newstr .= '...';
        }

        return $newstr;
    }


    public function remove_label($str){
        $str = preg_replace( "@<script(.*?)</script>@is", "", $str );
        $str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str );
        $str = preg_replace( "@<style(.*?)</style>@is", "", $str );
        $str = preg_replace( "@<(.*?)>@is", "", $str );
        $str = preg_replace( "@&.*?;@is", "", $str );
        //$str = preg_replace( "@&ldquo;@is", "", $str );
        //$str = preg_replace( "@&rdquo;@is", "", $str );
        return trim($str);
    }
}