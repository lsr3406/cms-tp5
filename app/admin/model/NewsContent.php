<?php 

namespace app\admin\Model;
use think\Model;
use think\Exception;

/**
* NewsContent
*/
class NewsContent extends Model{

	// 模型时间戳功能开启, $createTime 和 $updateTime 若不设置, 默认为 create_time 和 update_time.若想关闭某一项, 可以设置其为 false
	protected $autoWriteTimestamp = true;
	
	/**
	 * insert() 向文章副表中插入一条记录
	 * @param  array  $data 文章副表记录的数据
	 * @return array       包含文章副表记录信息的数组
	 */
	public function insert($data = array()){
		if(!is_array($data) || !$data)
			return 0;
		if(isset($data['content']) && $data['content'])
			$data['content'] = htmlspecialchars($data['content']);
		return $this::create($data,true);
	}

	/**
	 * find() 根据文章主表的 id 查到相应的文章内容
	 * @param  integer $newsid 主表 newsid (主键)
	 * @return array     查到的文章内容数据
	 */
	public function findByNewsid($newsid){
		
		if(!is_numeric($newsid))
			return null;
		return $this::where('news_id',$newsid)->find();
	}

	public function updateByNewsId($newsId, $data){
		
		if(!is_numeric($newsId) || !$newsId)
			throw new Exception('文章ID不合法');
		if(!is_array($data) || !$data)
			throw new Exception('文章不合法');
		if(!isset($data['content']) || !$data['content'])
			throw new Exception("文章内容不合法");
			
		$data['content'] = htmlspecialchars($data['content']);
		return $this->where('news_id',$newsId)->update(['content' => $data['content']]);

	}

}


 ?>