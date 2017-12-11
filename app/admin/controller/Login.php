<?php 

namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;


/**
* login
*/
class Login extends controller{

	public function index(){
		
		return $this->fetch('Login/login');
	}
	
	public function check(){
		// return show(0, '密码错误');

		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if(!trim($username) || !trim($password))
			return show(0, '用户名或密码不能为空');
		
		/**
		 * 用户名和密码都不为空, 开始检验密码
		 */
		$admin = new Admin;

		$res = $admin->getAdminByUsername($username);

		if($res == null)
			return show(0, '用户名不存在');

		if(!$admin->checkPassword($password, $res['password']))
			return show(0, '密码错误');
		
		// 存入服务器
		session('adminUser', $res);
		(new Admin)->updateLoginTime($res['admin_id']);
		return show(1, '登录成功');
	}

	public function logout(){
		
		session('adminUser',null);
		$this->redirect('admin/Index/index');
	}
}

 ?>

