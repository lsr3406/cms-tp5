<?php 

namespace app\admin\controller;
use think\Request;
use app\admin\controller\Common;
use app\admin\model\Admin as AdminModel;

/**
* Admin
*/
class Admin extends Common{

	public function index(){
		$this->checkAdmin();
	
		// 根据分页要求来获取数据
		$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;	// 暂没用到
		$pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : PAGE_SIZE; // 暂没用到
		
		$adminList = (new AdminModel)->getAdmins($page, $pageSize);
		$this->assign('adminList',$adminList);
		
		return $this->fetch('Admin/index');
	}

	public function add(Request $request){
		$this->checkAdmin();
		// 如果有 post 过来的数据, 则进入添加用户的业务逻辑
		if($request->post()){
			$data = $request->post();

			if (!isset($data['username']) || !trim($data['username']))
				return show(0, '用户名不能为空');
			if (!isset($data['password']) || !trim($data['password']))
				return show(0, '密码不能为空');
			if (!isset($data['confirmPassword']) || !trim($data['confirmPassword']))
				return show(0, '确认密码不能为空');
			if (!isset($data['realname']) || !trim($data['realname']))
				return show(0, '真实姓名不能为空');
			// 验证密码是否一致
			if($data['password'] != $data['confirmPassword'])
				return show(0, '两次输入的密码不一致');

			// 加密
			$data['password'] = md5(MD5_PRE.$data['password']);

			// 下面是添加用户的业务逻辑, 如果主表插入成功, 则继续插入副表
			$adminObj = (new AdminModel)->insert($data);
			if($adminObj)
				return show(1, '用户添加成功');
			return show(0, '用户添加失败');
		}

		// 没有 psot 过来的数据, 进入添加页面
		return $this->fetch('Admin/add');
	}

	/**
	 * setStatus() 设置用户当前所处的状态
	 * 接收 post 传来的数据, status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * 根据 id 直接修改数据表
	 * @return array       用于回调函数中弹出层的状态信息 show()
	 */
	public function setStatus(){
		$this->checkAdmin();
		try {
			if((isset($_POST['id']) && isset($_POST['status']))){
				$id = $_POST['id'];
				$status = $_POST['status'];
				$adminModel = new AdminModel;
				$res = $adminModel->updateStatusById($id, $status);
				if($res)
					return show(1, '操作成功');
				return show(0, '操作失败');
			}
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
		return show(0, '没有提交过来的数据');
	}

	public function checkAdmin(){
		if(session('adminUser')['username'] != 'admin'){
			die('非法操作');
		}
	}
}


 ?>