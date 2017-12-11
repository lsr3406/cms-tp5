<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;	// think 是框架目录下的 library 下的 think

// ThinkPHP 引导文件
// 加载基础文件
require __DIR__ . '/base.php';


// 执行应用
// App 下的 run() 方法会返回 response 对象, 然后执行 send() 方法返回给 http 请求
App::run()->send();

