<?php
return array(

    /* 默认配置 */
    'DEFAULT_M_LAYER'       =>  'Model',            // 默认的模型层名称
    'DEFAULT_C_LAYER'       =>  'Controller',       // 默认的控制器层名称
    'DEFAULT_V_LAYER'       =>  'Tpl',              // 默认的视图层名称
    'DEFAULT_LANG'          =>  'zh-cn',            // 默认语言
    'DEFAULT_THEME'         =>  '',                 // 默认模板主题名称
    'DEFAULT_MODULE'        =>  'Yjj',              // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index',            // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index',            // 默认操作名称
    'DEFAULT_CHARSET'       =>  'utf-8',            // 默认输出编码
    'DEFAULT_TIMEZONE'      =>  'PRC',              // 默认时区
    'DEFAULT_AJAX_RETURN'   =>  'JSON',             // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_JSONP_HANDLER' =>  'jsonpReturn',      // 默认JSONP格式返回的处理方法
    'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数...

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  => ture,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 2,       // URL访问模式,REWRITE模式
    'URL_PATHINFO_DEPR'     => '/',	    // PATHINFO模式下，各参数之间的分割符号
    'URL_PATHINFO_FETCH'    =>   'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
    'URL_HTML_SUFFIX'       => '.html',  // URL伪静态后缀设置


    /* COOKIE设置 */
    'COOKIE_EXPIRE'         =>  0,          // Cookie有效期
    'COOKIE_DOMAIN'         =>  '',         // Cookie有效域名
    'COOKIE_PATH'           =>  '/',        // Cookie路径
    'COOKIE_PREFIX'         =>  '',         // Cookie前缀 避免冲突


    /* SESSION设置 */
    'SESSION_AUTO_START'    =>  false,      // 是否自动开启Session
    'SESSION_OPTIONS'       =>  array(
                            'expire' => 3600*6,
                            'use_trans_sid'       =>  1,  //跨页传递
                            'use_only_cookies'    =>  0,
                                    ),      // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE'          =>  '',         // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX'        =>  '',         // session 前缀


    /* 数据库配置信息 */
    'LOAD_EXT_CONFIG'	=>'db',


    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       =>  7200,                  // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   =>  false,              // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      =>  false,              // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     =>  '',                 // 缓存前缀
    'DATA_CACHE_TYPE'       =>  'File',             // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       =>  TEMP_PATH,          // 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'     =>  false,              // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       =>  1,                  // 子目录缓存级别


    /* 模板引擎设置 */
    'TMPL_L_DELIM'          => '<{',                                    // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          => '}>',                                    // 模板引擎普通标签结束标
    'TMPL_CONTENT_TYPE'     =>  'text/html',                            // 默认模板输出类型
    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl',     // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl',     // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  THINK_PATH.'Tpl/think_exception.tpl',   // 异常页面的模板文件
    'TMPL_DETECT_THEME'     =>  false,                                  // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX'  =>  '.html',                                // 默认模板文件后缀
    'TMPL_FILE_DEPR'        =>  '/',                                    //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符

);