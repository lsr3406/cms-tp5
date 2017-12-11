<?php 

namespace app\admin\Model;
use think\Model;
use think\Exception;

/**
* News
*/
class News extends Model{

	// 模型时间戳功能开启, $createTime 和 $updateTime 若不设置, 默认为 create_time 和 update_time.若想关闭某一项, 可以设置其为 false
	protected $autoWriteTimestamp = true;
	
	/**
	 * insert() 向文章主表中插入一条记录, 其中用户名来自当前登录的用户
	 * @param  array  $data 文章主表记录的数据
	 * @return array       包含文章主表记录信息的数组
	 */
	public function insert($data = array()){
		if(!is_array($data) || !$data)
			return 0;
		$data['username'] = getLoginUsername();
		return $this::create($data,true);
	}

	/**
	 * gwtNews() 获取当前需要显示的文章
	 * @param  array $data     查询条件
	 * @param  integer $page     第几页
	 * @param  integer $pageSize 每页显示的数量
	 * @return array           文章列表
	 */
	public function getNews($conds, $page, $pageSize){
		$data = array();
		if(isset($conds['title']))
			$data['title'] = array('like', '%' . $conds['title'] . '%');
		if(isset($conds['type']))
			$data['catid'] = intval($conds['type']);
		$data['status'] = array('neq', '-1');
		// 获取某一页的数据
		$list = $this::where($data)->
			order("listorder DESC, news_id DESC")->
			paginate($pageSize);

		return $list;
	}

	/**
	 * gwtLatestNews() 获取最新的的文章
	 * @param  array $data     查询条件
	 * @param  integer $limit     要获得的文章数量
	 * @return array           文章列表
	 */
	public function getLatestNews($conds, $pageSize=2){
		$data = array();
		if(isset($conds['title']))
			$data['title'] = array('like', '%' . $conds['title'] . '%');
		if(isset($conds['type']))
			$data['catid'] = intval($conds['type']);
		$data['status'] = array('eq', '1');
		// 获取某一页的数据
		$list = $this::where($data)->
			order("create_time DESC, news_id DESC")->
			paginate($pageSize);

		return $list;
	}

	/**
	 * gwtHotNews() 获取最热的的文章
	 * @param  array $data     查询条件
	 * @param  integer $limit   要获得的文章数量
	 * @return array           文章列表
	 */
	public function getHotNews($conds, $limit){
		$data = array();
		if(isset($conds['title']))
			$data['title'] = array('like', '%' . $conds['title'] . '%');
		if(isset($conds['type']))
			$data['catid'] = intval($conds['type']);
		$data['status'] = array('eq', '1');
		// 获取某一页的数据
		$list = $this::where($data)->
			order("count DESC, news_id DESC")->
			limit($limit)->
			select();

		return $list;
	}

	/**
	 * find() 根据 id 查到相应的文章数据
	 * @param  integer $id 文章 id (主键)
	 * @return array     查到的文章数据
	 */
	public function find($id){
		
		if(!is_numeric($id))
			return null;
		return $this::get($id);
	}

	public function updateById($id, $data){
		
		if(!is_numeric($id) || !$id)
			throw new Exception('文章ID不合法');
		if(!is_array($data) || !$data)
			throw new Exception('文章不合法');
		if(isset($data['content']))
			unset($data['content']);

		return $this::get($id)->save($data);
	}

	/**
	 * updataNewsListorderById() 根据 id 更新对应文章的排序指标 listorder
	 * @param  integer $id    文章 id
	 * @param  integer $order 文章的排序指标
	 * @return integer         受影响记录的行数
	 */
	public function updataNewsListorderById($id, $order){
		if(!isset($id) || !is_numeric($id))
			throw new Exception("ID 不合法");
		if(!is_numeric($order))
			throw new Exception("order 不合法");
		$data = array('listorder' => $order);
		$res = $this->updateById($id, $data);
		return $res;
	}

	/**
	 * updateStatusById() 根据 id 修改文章的状态.
	 * @param  integer $id     文章 id (主键)
	 * @param  integer $status 文章状态 status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
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

	public function getByNewsIdIn($idList){
		$data = array(
			'news_id' => array('in', implode(',',$idList))
		);
		return $this->where($data)->select();
	}

	public function getMaxCount(){
		$data = array(
			'status' => '1',
		);
		return $this->where($data)->order('count DESC')->limit(1)->find();
	}

	public function getNewsCount(){
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