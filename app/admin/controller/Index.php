<?php 

namespace app\admin\controller;
use app\admin\controller\Common;
use app\admin\model\Admin;
use app\admin\model\News;
use app\admin\model\Position;
use think\Config;
use think\Request;
use think\View;

/**
* ºóÌ¨
*/
class Index extends Common{
	
	public function index(){

		if(!session('adminUser'))
			return $this->fetch('Login/index');
		$adminUser = session('adminUser');
		
		$maxCountNews = (new News)->getMaxCount();

		$newsCount = (new News)->getNewsCount();
		$positionCount = (new Position)->getPositionCount();
		$loginUsersCount = (new Admin)->getLastLoginUsers();


		$this->assign(array(
			'adminUser' => $adminUser,
			'maxCountNews' => $maxCountNews,
			'newsCount' => $newsCount,
			'positionCount' => $positionCount,
			'loginUsersCount' => $loginUsersCount,
		));

		return $this->fetch('Index/index');
	}

	
}

 ?>