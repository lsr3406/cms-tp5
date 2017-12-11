<?php 

namespace app\admin\model;
use think\Model;
// use think\Db;
/**
* Admin
*/
class Admin extends model{
	
	/**
	 * find() 根据 id 查到相应的用户数据
	 * @param  integer $id 用户 id (主键)
	 * @return array     查到的用户数据
	 */
	public function find($id){
		
		if(!is_numeric($id))
			return null;
		return $this::get($id);
	}

	/**
	 * getAdmins() 获取当前需要显示的用户
	 * @param  integer $page     第几页
	 * @param  integer $pageSize 每页显示的数量
	 * @return array           用户列表
	 */
	public function getAdmins($page, $pageSize){
		$data = array();
		$data['status'] = array('neq', '-1');
		// 获取某一页的数据
		$list = $this->order("admin_id DESC")->
			where($data)->
			paginate($pageSize);

		return $list;
	}

	/**
	 * getAllAdmins() 获取当前所有的用户
	 * @return array           用户列表
	 */
	public function getAllAdmins(){
		$data = array();
		$data['status'] = array('neq', '-1');
		// 获取某一页的数据
		$list = $this->order("admin_id DESC")->
			where($data)->select();

		return $list;
	}

	/**
	 * insert() 向用户表中插入一条记录
	 * @param  array  $data 用户表记录的数据
	 * @return array       包含用户表记录信息的数组
	 */
	public function insert($data = array()){
		if(!is_array($data) || !$data)
			return 0;
		return $this::create($data,true);
	}

	public function updateById($id, $data){
		
		if(!is_numeric($id) || !$id)
			throw new Exception('ID不合法');
		if(!is_array($data) || !$data)
			throw new Exception('用户不合法');

		return $this::get($id)->save($data);
	}

	/**
	 * updateStatusById() 根据 id 修改用户的状态.
	 * @param  integer $id     用户 id (主键)
	 * @param  integer $status 用户状态 status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * @return integer         受影响记录的行数
	 */
	public function updateStatusById($id, $status){
		if(!isset($id) || !is_numeric($id))
			throw new Exception("ID 不合法");
		if(!isset($status))
			throw new Exception("status 不合法");
		$data = array('status' => $status);
		$res = $this->updateById($id, $data);
		return $res;
	}


	/**
	 * getAdminByUsername 根据用户名得到相应的数据, 用于后台登录校验
	 * @param  string $username 来自后台登录表单
	 * @return array           根据传入的 username 从数据库中查到的信息
	 */
	public function getAdminByUsername($username){
		// 返回一个一维数组
		return $this::where(array('username' => $username))->find();
	}

	public function checkPassword($password,$md5Password){
		
		if(md5(MD5_PRE.$password) != $md5Password)
			return false;
		return true;
	}

	public function updateLoginTime($id){
		return $this->updateById($id, array('lastlogintime' => time()));
	}

	public function getLastLoginUsers(){
		$time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$data = array(
			'status' => 1,
			'lastlogintime' => array('gt', $time),
		);
		$res = $this->where($data)->count();
		return $res;
	}

	
	public function getStatusAttr($status){
		switch ($status) {
			case '1':	return '开启';
			case '0':	return '关闭';
			case '-1':	return '已删除';
			default:	return '未知';
		}
	}

	public function setStatusAttr($status){
		switch ($status) {
			case '开启':	return '1';
			case '关闭':	return '0';
			case '已删除':	return '-1';
			default:	return '-2';
		}
	}

}

 ?>