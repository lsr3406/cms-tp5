<?php 

namespace app\admin\controller;
use app\admin\controller\Common;
use app\admin\model\Basic as BasicModel;
use think\Request;

/**
* Basic
*/
class Basic extends Common{
	
	public function index(){
		
		// 获取缓存中的基本管理信息并注册
		$data = (new BasicModel)->getBasicConfig();

		$this->assign('basicWebConfig', $data);
		return $this->fetch('Basic/index');

	}

	public function save(Request $request){

		$postData = $request->post();
		if ($postData)
			return (new BasicModel)->setBasicConfig($postData) ? show(1, '设置成功', '') : show(0, '设置失败');
	}
}


 ?>