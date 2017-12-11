<?php 

namespace app\admin\controller;
use app\admin\controller\Common;
use app\admin\model\Menu as MenuModel;
use think\Page;
use think\Config;
use think\Request;
use think\Exception;
/**
* Menu
*/
class Menu extends Common
{
	function __construct(){
		parent::__construct();	
	}

	/**
	 * add() 添加菜单数据
	 * 有 post 提交过来的数据时进入修改页面, 没有时进入添加页面
	 * @return  void       显示添加页面, 若有 post 则显示修改页面
	 */
	public function add(){
		// 有提交过来的数据, 执行相关的业务逻辑
		if($_POST){
			// 首先检测提交过来的数据是否合法
			if(!isset($_POST['name']) || !trim($_POST['name']))
				return show(0, '菜单名不能为空');
			
			if(!isset($_POST['m']) || !trim($_POST['m']))
				return show(0, '模块名不能为空');
			
			if(!isset($_POST['c']) || !trim($_POST['c']))
				return show(0, '控制器名不能为空');
			
			if(!isset($_POST['f']) || !trim($_POST['f']))
				return show(0, '方法名不能为空');

			// 如果 post 过来的数据有 id 则修改数据
			if (isset($_POST['menu_id'])) 
				return $this->save($_POST);
			
			$menuModel = new MenuModel;
			// 向数据库中插入数据
			$res = $menuModel->insert($_POST);
			
			// 显示结果并返回
			if($res){
				return show(1, '添加成功');
			} else {
				return show(0, '添加失败');
			}

		} else {
			// 没有提交过来的数据, 进入添加页面			
			return $this->fetch('Menu/add');
		}
	}

	/**
	 * index() 菜单管理控制器的首页
	 * 根据请求的地址参数中的 type 区分前端导航和后台菜单
	 * 根据分页要求来获取数据
	 * 最后将当前页的菜单信息注册并显示
	 * @return void      显示菜单管理首页
	 */
	public function index(Request $request){

		$data = array();

		// 如果请求的数据有 type, 则返回相应类型的数据
		if( is_numeric($request->param('type')) && ($request->param('type') == 0 || $request->param('type') == 1) ){
			$data['type'] = $request->param('type');
			$type = $data['type'] == 0 ? '前端导航' : '后台菜单';
		} else {
			unset($data['type']);
			$type = '所有';
		}

		// 根据分页要求来获取数据
		$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;	// 暂没用到
		$pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : PAGE_SIZE; // 暂没用到
		$menuModel = new MenuModel;
		$menus = $menuModel->getMenus($data, $page, $pageSize);

		// 向页面中注册变量		
		$this->assign(array(
			'menus' => $menus,
			'type' => $type
		));

		return $this->fetch('Menu/index');
	}

	/**
	 * edit() 编辑菜单信息
	 * 当在前端点击编辑时, 由服务器 Menu控制器 的 add() 方法调用. 
	 * 根据请求参数中的 id 判断当前是要修改哪一个菜单信息
	 * 查找相应数据, 注册并显示
	 * @return void      显示编辑页面
	 */
	public function edit(Request $request){
		
		$menuId = $request->param('id');
		$menuModel = new MenuModel;
		$menu = $menuModel->getMenuById($menuId);
		
		$this->assign('menu', $menu);
		return $this->fetch('Menu/edit');
	}

	/**
	 * save() 保存菜单信息. 在编辑页面中点击确认修改时调用
	 * @param  array $data 页面中填写的信息, 已由 js 做了简单整理
	 * @return array       用于回调函数中弹出层的状态信息 show()
	 */
	public function save($data){
		$menuId = $data['menu_id'];
		unset($data['menu_id']);

		try {
			$menuModel = new MenuModel;
			$res = $menuModel->updateMenuById($menuId, $data);
			return $res ? show(1, '更新成功') : show(0, '更新失败');
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
	}

	/**
	 * setStatus() 设置菜单当前所出的状态
	 * 接收 post 传来的数据, status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * 根据 id 直接修改数据表
	 * @return array       用于回调函数中弹出层的状态信息 show()
	 */
	public function setStatus(){
		try {
			if((isset($_POST['id']) && isset($_POST['status']))){
				$id = $_POST['id'];
				$status = $_POST['status'];
				$menuModel = new MenuModel;
				$res = $menuModel->updateStatusById($id, $status);
				if($res)
					return show(1, '操作成功');
				return show(0, '操作成功');
			}
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
		return show(0, '没有提交过来的数据');
	}

	/**
	 * listorder() 当点击更新排序按钮时调用
	 * 根据 post 的数据重新将排序数据存入数据
	 * @return void      用于回调函数中弹出层的状态信息 show()
	 */
	public function listorder(){

		/*$jumpUrl = $_SERVER['HTTP_REFERER'];*/
		if(!isset($_POST))
			return show(0, '不能排序'/*, array('jump_url'=>$jumpUrl)*/);
		
		$listorder = $_POST;
		$errors = array();

		try {
			$menuModel = new MenuModel;
			foreach ($listorder as $menuId => $v) {
				// 执行更新
				$res = $menuModel->updataMenuListorderById($menuId, $v);
				if($res === false)
					$errors[] = $menuId;
			}
		} catch (Exception $e) {
			return show(0, $e->getMessage()/*, array('jump_url'=>$jumpUrl)*/);			
		}

		if($errors)
			return show(0, '排序失败-'.implode(',', $errors)/*, array('jump_url'=>$jumpUrl)*/);
		return show(1, '排序成功'/*, array('jump_url'=>$jumpUrl)*/);
	}





}


 ?>