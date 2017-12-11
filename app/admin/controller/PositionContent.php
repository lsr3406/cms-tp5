<?php 

namespace app\admin\controller;
use app\admin\model\Position as PositionModel;
use app\admin\model\PositionContent as PositionContentModel;
use app\admin\controller\Common;
use think\Cache;
use think\Request;

/**
* PositionContent
*/
class PositionContent extends Common{

	public function index(Request $request){

		$conds = array();
		$positionId = $request->param('position');
		
		if (is_numeric($positionId) && $positionId) {
			// 筛选指定的推荐位的内容
			$conds['position_id'] = $positionId;
			$positionName = getPositionNameByPid($positionId);
		} else {
			$positionName = '';
		}
		
		$title = urldecode($request->param('title'));
		if($title)
			$conds['title'] = $title;
		
		$this->assign('positionName',$positionName);
		$this->assign('titleSearched',$title);
	
		// 根据分页要求来获取数据
		$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;	// 暂没用到
		$pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : PAGE_SIZE; // 暂没用到
		
		$positionList = (new PositionModel)->getAllPositions();
		$positionContentList = (new PositionContentModel)->getPositionContents($conds, $page, $pageSize);
		$this->assign('positionList',$positionList);
		$this->assign('positionContentList',$positionContentList);
		
		
		return $this->fetch('PositionContent/index');
	}

	public function add(Request $request){
		// 如果有 post 过来的数据, 则进入添加推荐位内容的业务逻辑
		if($request->post()){
			$data = $request->post();
			// 文章 id 和 url 不能同时为空
			if((!isset($data['news_id']) || !trim($data['news_id'])) && (!isset($data['url']) || !trim($data['url'])))
				return show(0, '文章 id 和 url 不能同时为空');
			if (!isset($data['title']) || !trim($data['title']))
				return show(0, '请填写标题');
			if (!isset($data['position_id']) || !is_numeric($data['position_id']) || !$data['position_id'])
				return show(0, '请选择正确的推荐位');
			
			// 如果 post 过来的内容中有 id 则进入修改的业务逻辑中
			if(isset($data['id']))
				return $this->save($data);

			// 下面是添加推荐位内容的业务逻辑, 如果主表插入成功, 则继续插入副表
			$positionContentObj = (new PositionContentModel)->insert($data);
			if($positionContentObj)
				return show(1, '推荐位内容添加成功');
			return show(0, '推荐位内容添加失败');
		}

		// 没有 psot 过来的数据, 进入添加页面
		$positionList = (new PositionModel)->getAllPositions();
		$this->assign('positionList',$positionList);
		// 下面的内容是用于显示添加页面的代码, 在 add() 方法没有接收到数据时执行
		return $this->fetch('PositionContent/add');
	}

	public function edit(Request $request){
		$id = $request->param('id');
		if(!$id)
			$this->redirect('/admin/Content/index');

		$positionList = (new PositionModel)->getAllPositions();
		$positionContent = (new PositionContentModel)->find($id);
		$this->assign('positionList',$positionList);
		$this->assign('positionContent' ,$positionContent);

		return $this->fetch('PositionContent/edit');
	}

	public function save($data){
		$id = $data['id'];
		unset($data['id']);
		try {
			$res = (new PositionContentModel)->updateById($id, $data);
			if($res === false)
				return show(0,'推荐位内容修改失败');
			return show(1,'修改成功');
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
	}

	/**
	 * setStatus() 设置推荐位内容当前所处的状态
	 * 接收 post 传来的数据, status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * 根据 id 直接修改数据表
	 * @return array       用于回调函数中弹出层的状态信息 show()
	 */
	public function setStatus(){
		try {
			if((isset($_POST['id']) && isset($_POST['status']))){
				$id = $_POST['id'];
				$status = $_POST['status'];
				$positionContentModel = new PositionContentModel;
				$res = $positionContentModel->updateStatusById($id, $status);
				if($res)
					return show(1, '操作成功');
				return show(0, '操作失败');
			}
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
		return show(0, '没有提交过来的数据');
	}


	/**
	 * listorder() 当点击更新排序按钮时调用
	 * 根据 post 的数据重新将排序数据存入数据库
	 * @return void      用于回调函数中弹出层的状态信息 show()
	 */
	public function listorder(){

		/*$jumpUrl = $_SERVER['HTTP_REFERER'];*/
		if(!isset($_POST))
			return show(0, '不能排序'/*, array('jump_url'=>$jumpUrl)*/);
		
		$listorder = $_POST;
		$errors = array();
		try {
			$positionContentModel = new PositionContentModel;
			foreach ($listorder as $id => $v) {
				// 执行更新
				$res = $positionContentModel->updataPositionContentListorderById($id, $v);
				if($res === false)
					$errors[] = $res;
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