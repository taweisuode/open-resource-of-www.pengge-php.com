<?php
return array(
	//'配置项'=>'配置值'
	'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息
	'DB_TYPE'=>'mysql',
	'DB_HOST'=>'localhost',
	'DB_NAME'=>'pengge-root',
	'DB_USER'=>'root',
	'DB_PWD'=>'vertrigo',
	'DB_PORT'=>'3306',
	'DB_PREFIX'=>'',	// 表前缀
	'SHOW_PAGE_TRACE'=>true,
	//'TMPL_EXCEPTION_FILE'=>'./Application/Home/View/Public/404/index.html',// 定义公共错误模板
	'ERROR_PAGE'=>'http://localhost/thinkphp/index.php/Home/Index/errorPage' // 定义错误跳转页面URL地址
);