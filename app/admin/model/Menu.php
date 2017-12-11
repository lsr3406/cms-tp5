<?php 

namespace app\admin\model;
use think\Model;
use think\Exception;

/**
* MenuModel
*/
class Menu extends Model{
	
	/**
	 * insert() 插入数据
	 * @param  array  $data 需要存入数据库的菜单信息
	 * @return object       与新增的数据有关的对象
	 */
	public function insert($data = array()){
		if(!isset($data) || !is_array($data)){
			return 0;
		}
		return $this::create($data,true);
	}

	/**
	 * getMenus() 获取当前页面表格中需要展示的所有菜单数据
	 * @param  array $data     菜单的约束条件
	 * @param  integer $page     需要请求的的页码, 从 1 开始
	 * @param  integer $pageSize 每页的数据的条数, 默认为 10, 可在 public 下的入口文件中修改
	 * @return object           当前页的所有菜单数据, 用于展示和制作分页按钮
	 */
	public function getMenus($data, $page, $pageSize=PAGE_SIZE){
		// 首先确定不是删除
		$data['status'] = array('neq', -1);
		// 获取某一页的数据
		$list = $this::where($data)->
			order("listorder DESC, menu_id DESC")->
			paginate($pageSize);

		return $list;
	}

	/**
	 * getMenuById() 根据 id 查到相应的菜单数据
	 * @param  integer $id 菜单 id (主键)
	 * @return array     查到的菜单数据
	 */
	public function getMenuById($id){
		
		if (!isset($id) || !is_numeric($id)) {
			return array();
		}
		// 获取数据并返回
		return $this::get($id);
	}

	/**
	 * updateMenuById() 保存修改后的数据, 根据 id 进行数据库的改操作
	 * @param  integer $id   菜单 id (主键)
	 * @param  array $data 新的菜单数据
	 * @return integer       受影响记录的行数
	 */
	public function updateMenuById($id, $data){
		
		if (!isset($id) || !is_numeric($id))
			throw new Exception("ID 不合法");
		if (!isset($data) || !is_array($data))
			throw new Exception("数据不合法");
			
		// 获取数据并返回受影响记录的行数
		return $this::where('menu_id', $id)->
			update($data);
	}

	/**
	 * updateStatusById() 根据 id 修改菜单的状态.
	 * @param  integer $id     菜单 id (主键)
	 * @param  integer $status 菜单状态 status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * @return integer         受影响记录的行数
	 */
	public function updateStatusById($id, $status){
		if(!isset($id) || !is_numeric($id))
			throw new Exception("ID 不合法");
		if(!isset($status) || !is_numeric($status))
			throw new Exception("status 不合法");
		$data = array('status' => $status);
		$res = $this->updateMenuById($id, $data);
		return $res;
	}

	/**
	 * updataMenuListorderById() 根据 id 更新对应菜单的排序指标 listorder
	 * @param  integer $id    菜单 id
	 * @param  integer $order 菜单的排序指标
	 * @return integer         受影响记录的行数
	 */
	public function updataMenuListorderById($id, $order){
		if(!isset($id) || !is_numeric($id))
			throw new Exception("ID 不合法");
		if(!is_numeric($id))
			throw new Exception("order 不合法");
		$data = array('listorder' => $order);
		$res = $this->updateMenuById($id, $data);
		return $res;
	}

	/**
	 * getAdminMenus() 获取 admin 模块下的已经开启且为后台模块的菜单, 用于后台页面侧边栏的显示
	 * @return array 查到的菜单数据
	 */
	public function getAdminMenus(){		
		// 选出所有开启的后台菜单
		$data = array(
			'status' => 1,
			'type' => 1
		);
		return $this::where($data)->
			order('listorder DESC, menu_id DESC')->
			select();
	}

	/**
	 * getBarMenus() 获取 admin 模块下的已经开启且为前台模块的菜单, 用于前台显示和添加文章界面的下拉菜单
	 * @return array 查到的菜单数据
	 */
	public function getBarMenus(){
		// 选出所有开启的前端导航
		$data = array(
			'status' => 1,
			'type' => 0
		);
		return $this::where($data)->
			order('listorder DESC, menu_id DESC')->
			select();
	}


	/**
	 * 模型修改器
	 */

	public function getTypeAttr($type){
		if($type == 0)	return '前端导航';
		if($type == 1)	return '后台菜单';
	}

	public function getStatusAttr($status){
		if ($status == 1)	return '开启';
		if ($status == 0)	return '关闭';
	}
}


 ?>