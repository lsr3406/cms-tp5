<?php 

return array(
	'app_debug' =>	 true,
	'url_convert'            => false,	// 自动转换URL中的控制器和操作名
	'exception_tmpl' =>APP_PATH.'index/view/index/error.html',
	'database'               => [
		'type'            => 'mysql',
		'hostname'        => '127.0.0.1',
		'database'        => 'moon_cms',
		'username'        => 'root',
		'password'        => '123456',
		'hostport'        => '3306',
		'dsn'             => '',
		'params'          => [],
		'charset'         => 'utf8',
		'prefix'          => 'cms_',
		'debug'           => true,
		'deploy'          => 0,
		'rw_separate'     => false,
		'master_num'      => 1,
		'slave_no'        => '',
		'fields_strict'   => true,
		'resultset_type'  => 'array',
		'auto_timestamp'  => false,
		'datetime_format' => 'Y-m-d H:i:s',
		'sql_explain'     => false,
	],


	'view_replace_str' => [
		'__CSS__' => '/css',	// 上线时直接从 /front 开始写, 这里测试加了 /TP_test2
		'__JS__' => '/js',	// 上线时直接从 /front 开始写, 这里测试加了 /TP_test2
		'__IMAGE__' => '/images',	// 上线时直接从 /front 开始写, 这里测试加了 /TP_test2
	],
);

 ?>
