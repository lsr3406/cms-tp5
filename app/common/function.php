<?php 

/**
 * 公用的方法
 */

/**
 * show() 用于服务器端向前端返回消息, 一般由 js 回调函数接收
 * @param  integer $status  服务器端任务执行的状态, 1 为成功, 0 为 失败
 * @param  string $message 与状态相关的信息, 用于在前端显示
 * @param  array  $data    希望前端使用的一些数据
 * @return void          最后以 json 的格式返回给前端, 在 js 中注意写清 'JSON' 格式
 */
function show($status, $message, $data = array()){
	$result = array(
		'status' => $status,
		'message' => $message,
		'data' => $data,
	);
	exit(json_encode($result));
}

/**
 * getMd5Password() 传入一个明文密码, 得到一个加上一定前缀并经 32 位 md5 加密之后的密文
 * @param  string $password 用户输入的密码
 * @return string           处理后可直接存入数据库的密码
 */
function getMd5Password($password){
	return md5(MD5_PRE.$password);
}

/**
 * getAdminMenuUrl() 根据后台菜单的信息得到相应的 url, 用于访问该菜单的相关内容
 * @param  array $nav 后台菜单, 与数据库中的菜单表中的记录一致
 * @return string      在 TP5 中用于访问菜单的 url, 包括模块名 admin, 控制器名和方法名
 */
function getAdminMenuUrl($nav){
	$url = '/admin/' . $nav['c'] . '/' . $nav['f'];
	return $url;
}

/**
 * getActive() 根据当前控制器和侧边栏的菜单对应的控制器, 判断一个菜单是否应加上高亮类
 * @param  string $navController 菜单的控制器名
 * @return string                active 或为空
 */
function getActive($navController){
	$request = request();
	$c = $request->controller();
	if(strtolower($navController) == strtolower($c))
		return 'active';
	return '';
}

/**
 * getLoginUsername() 获取当前登录的管理员用户名
 * @return string 用户名
 */
function getLoginUsername(){
	// return $_SESSION['adminUser']['username'] ? $_SESSION['adminUser']['username'] : '';
	return session('adminUser')['username'] ? session('adminUser')['username'] : '';
}

/**
 * getCatNameById() 根据 catid(文章栏目编号) 获取相应的栏目名称
 * @param  array $webSiteMenu 前端导航栏目数组
 * @param  integer $catid       导航编号
 * @return string              前端导航名称
 */
function getCatNameById($webSiteMenu, $catid){
	foreach ($webSiteMenu as $menu) {
		$wsm[$menu['menu_id']] = $menu['name'];
	}
	return isset($wsm[$catid]) ? $wsm[$catid] : '';
}

/**
 * getCopyfromNameById() 根据编号获取文章来源
 * @param  integer $cfid        来源编号
 * @return string              文章来源
 */
function getCopyfromNameById($cfid){
    $copyfrom = think\Config::get('copyfrom');
    // dump($copyfrom);
	return isset($copyfrom[$cfid]) ? $copyfrom[$cfid] : '';
}

/**
 * isThumb() 给出缩图资源路径, 判断是否有缩图, 返回 html 代码直接显示
 * @param  string  $thumb 图片资源路径
 * @return string        显示在后台文章列表的内容, 有或无
 */
function isThumb($thumb) {
    if($thumb) {
        return '有';
    }
    return '<span style="color:gray">无</span>';
}

/**
+----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
+----------------------------------------------------------
 * @static
 * @access public
+----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    $len = substr($str);
    if(function_exists("mb_substr")){
        if($suffix)
            return mb_substr($str, $start, $length, $charset)."...";
        else
            return mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr')) {
        if($suffix && $len>$length)
            return iconv_substr($str,$start,$length,$charset)."...";
        else
            return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}

function getPositionNameByPid($pid){
    if (!is_numeric($pid) || !$pid) {
        return null;
    }
    $position = new app\admin\model\Position;
    $name =  $position->get($pid)['name'];
    return $name ? $name : '';
}

 ?>