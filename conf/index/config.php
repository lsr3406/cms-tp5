<?php 

	return array(
		// 'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',
		

		// 没起作用
		'http_exception_template'    => [
			404 =>  APP_PATH.'index/view/index/error.html',
			500 =>  APP_PATH.'index/view/index/error.html',
		],
	);

 ?>