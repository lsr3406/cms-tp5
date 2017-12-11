<?php 

namespace app\admin\model;
// use app\admin\model\PositionContent;
use think\Model;
use think\Image;

/**
* PositionContent 为兼容 TP5 的路由的 bug, 这里类名没有遵循驼峰命名法
*/
class PositionContent extends Model{

	// 模型时间戳功能开启, $createTime 和 $updateTime 若不设置, 默认为 create_time 和 update_time.若想关闭某一项, 可以设置其为 false
	protected $autoWriteTimestamp = true;

	/**
	 * find() 根据 id 查到相应的推荐位内容数据
	 * @param  integer $id 推荐位内容 id (主键)
	 * @return array     查到的推荐位内容数据
	 */
	public function find($id){
		
		if(!is_numeric($id))
			return null;
		return $this::get($id);
	}

	/**
	 * getPositionContents() 获取当前需要显示的推荐位内容
	 * @param  array $conds     查询条件
	 * @param  integer $page     第几页
	 * @param  integer $pageSize 每页显示的数量
	 * @return array           推荐位内容列表
	 */
	public function getPositionContents($conds, $page, $pageSize){
		$conds['status'] = array('neq', '-1');
		if(isset($conds['title']))
			$conds['title'] = array('like', '%' . $conds['title'] . '%');
		if(isset($conds['position_id']))
			$conds['position_id'] = intval($conds['position_id']);
		// 获取某一页的数据
		$list = $this->order("listorder DESC, id DESC")->
			where($conds)->
			paginate($pageSize);

		return $list;
	}

	/**
	 * getAllPositionContents() 获取当前所有的推荐位内容
	 * @return array           推荐位内容列表
	 */
	public function getAllPositionContents($conds = array(),$limit = null){

		$data = $conds;
		$data['status'] = isset($conds['status']) ? $conds['status'] : array('neq', '-1');
		// 获取某一页的数据
		$list = $this->order("listorder DESC, id DESC")->
			where($data);
		if(is_numeric($limit))
			$list = $list->limit($limit);
		$list = $list->select();

		return $list;
	}

	public function getContents($pid){
		
	}

	/**
	 * insert() 向推荐位内容表中插入一条记录
	 * @param  array  $data 推荐位内容表记录的数据
	 * @return array       包含推荐位内容表记录信息的数组
	 */
	public function insert($data = array()){
		if(!is_array($data) || !$data)
			return 0;

		$res1 = $this::create($data,true);
		if ($res1 && isset($data['thumb'])){
			$res2 = $this->thumbImage($res1['id']);
			return $res2;
		}
		return $res1;
	}

	public function updateById($id, $data=array()){
		
		if(!is_numeric($id) || !$id)
			throw new Exception('ID不合法');
		if(!is_array($data) || !$data)
			throw new Exception('推荐位内容不合法');

		$res1 = $this::get($id)->save($data);
		if ($res1!==false && isset($data['thumb'])){
			$res2 = $this->thumbImage($id);
			return $res2;
		}
		return $res1;
	}

	/**
	 * updateStatusById() 根据 id 修改推荐位内容的状态.
	 * @param  integer $id     推荐位内容 id (主键)
	 * @param  integer $status 推荐位内容状态 status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
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


	public function insertNewsList($id, $newsList){
		foreach ($newsList as $news) {
			$data = array(
				'position_id' => $id,
				'title' => $news['title'],
				'thumb' => $news['thumb'],
				'news_id' => $news['news_id'],
				'status' => '开启',
			);
			$res = $this->insert($data);
			if(!$res)
				return 0;
		}
		return 1;
	}

	/**
	 * updataPositionContentListorderById() 根据 id 更新对应推荐位内容的排序指标 listorder
	 * @param  integer $id    推荐位内容 id
	 * @param  integer $order 推荐位内容的排序指标
	 * @return integer         受影响记录的行数
	 */
	public function updataPositionContentListorderById($id, $order){

		if(!isset($id) || !is_numeric($id))
			throw new Exception("ID 不合法");
		if(!is_numeric($order))
			throw new Exception("order 不合法");
		$data = array('listorder' => $order);
		$res = $this->updateById($id, $data);
		return $res;
	}


	public function thumbImage($id){
		try {
			$imageUrl = $this->find($id)['thumb'];
			// dump($imageUrl);
			$image = Image::open('/var/www/public'.$imageUrl);			
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
		
		if(!preg_match('/([a-z0-9]{32})\.([a-zA-Z]{3,4})/', $imageUrl, $imageName))
			return true;

		$pid = $this->find($id)['position_id'];
		switch ($pid) {
			case 8:	// 首页大图
				$width = 900;
				$height = 675;
				break;
			case 6:	// 首页小图
				$width = 300;
				$height = 225;	
				break;
			case 7: // 广告
				return true;
			default:	// 
				return true;
		}
		$image = $image->thumbTo($width,$height);
		$imageNewName = $imageName[1].'_thumb.'.$imageName[2];	// 这一行可扩展, 设置名字
		$image->save('./images/newsThumb/'.$imageNewName, $imageName[2]);
		$data['thumb'] = '/images/newsThumb/'.$imageNewName;
		return $this::get($id)->save($data);
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