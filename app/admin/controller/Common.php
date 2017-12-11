<?php 

namespace app\admin\controller;
use think\Controller;

/**
* Common 公共的控制器
*/
class Common extends controller
{
	/**
	 * Common 对象构造方法
	 */
	function __construct(){
		parent::__construct();	// 首先初始化父类构造器
		$this->_init();
	}

	/**
	 * _init() 初始化
	 * @return void
	 */
	private function _init(){
		// 如果已经登录则跳转至登录页面
		$isLogin = $this->isLogin();
		if(!$isLogin){
			$this->redirect('/admin/Login/index');
		}
		$this->assign('adminUser', session('adminUser'));
	}

	/**
	 * isLogin() 判断管理员是否登录
	 * @return boolean true 表示已登录
	 */
	private function isLogin(){

		$adminUser = session('adminUser');
		
		if($adminUser && is_object($adminUser) && $adminUser['username'] )
			return true;
		return false;
	}




}


 ?>