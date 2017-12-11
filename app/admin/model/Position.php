<?php 

namespace app\admin\model;
use think\Model;

/**
* Position
*/
class Position extends Model{

	// 模型时间戳功能开启, $createTime 和 $updateTime 若不设置, 默认为 create_time 和 update_time.若想关闭某一项, 可以设置其为 false
	protected $autoWriteTimestamp = true;

	/**
	 * find() 根据 id 查到相应的推荐位数据
	 * @param  integer $id 推荐位 id (主键)
	 * @return array     查到的推荐位数据
	 */
	public function find($id){
		
		if(!is_numeric($id))
			return null;
		return $this::get($id);
	}

	/**
	 * getPositions() 获取当前需要显示的推荐位
	 * @param  integer $page     第几页
	 * @param  integer $pageSize 每页显示的数量
	 * @return array           推荐位列表
	 */
	public function getPositions($page, $pageSize){
		$data = array();
		$data['status'] = array('neq', '-1');
		// 获取某一页的数据
		$list = $this->order("id DESC")->
			where($data)->
			paginate($pageSize);

		return $list;
	}

	/**
	 * getAllPositions() 获取当前所有的推荐位
	 * @return array           推荐位列表
	 */
	public function getAllPositions(){
		$data = array();
		$data['status'] = array('neq', '-1');
		// 获取某一页的数据
		$list = $this->order("id DESC")->
			where($data)->select();

		return $list;
	}

	/**
	 * insert() 向推荐位表中插入一条记录
	 * @param  array  $data 推荐位表记录的数据
	 * @return array       包含推荐位表记录信息的数组
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
			throw new Exception('推荐位不合法');

		return $this::get($id)->save($data);
	}

	/**
	 * updateStatusById() 根据 id 修改推荐位的状态.
	 * @param  integer $id     推荐位 id (主键)
	 * @param  integer $status 推荐位状态 status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
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

	public function getPositionCount(){
		$data = array(
			'status' => '1',
		);
		return $this->where($data)->count();
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