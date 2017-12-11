<?php 

namespace app\admin\controller;
use app\admin\controller\Common;
use app\admin\model\Position as PositionModel;
use think\Request;

/**
* Position
*/
class Position extends Common{

	public function index(Request $request){
	
		// 根据分页要求来获取数据
		$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;	// 暂没用到
		$pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : PAGE_SIZE; // 暂没用到
		
		$positionList = (new PositionModel)->getPositions($page, $pageSize);
		
		$this->assign('positionList',$positionList);
		
		return $this->fetch('Position/index');
	}

	public function add(Request $request){
		// 如果有 post 过来的数据, 则进入添加推荐位的业务逻辑
		if($request->post()){
			$data = $request->post();
			// 首先检查数据是否存在
			if(!isset($data['name']) || !trim($data['name']))
				return show(0, '推荐位名字不能为空');
			if(!isset($data['description']) || !trim($data['description']))
				return show(0, '描述不能为空');
			// 如果 post 过来的内容中有 id 则进入修改的业务逻辑中
			if(isset($data['id']))
				return $this->save($data);

			// 下面是添加推荐位的业务逻辑, 如果主表插入成功, 则继续插入副表
			$positionObj = (new PositionModel)->insert($data);
			if($positionObj)
				return show(1, '推荐位添加成功');
			return show(0, '推荐位添加失败');
		}

		// 下面的内容是用于显示添加页面的代码, 在 add() 方法没有接收到数据时执行
		return $this->fetch('Position/add');
	}

	public function edit(Request $request){
		$id = $request->param('id');
		if(!$id)
			$this->redirect('/admin/Content/index');
		$position = (new PositionModel)->find($id);
		$this->assign('position' ,$position);
		return $this->fetch('Position/edit');
	}

	public function save($data){
		$id = $data['id'];
		unset($data['id']);
		try {
			$res = (new PositionModel)->updateById($id, $data);
			if($res === false)
				return show(0,'推荐位修改失败');
			return show(1,'修改成功');
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
	}

	/**
	 * setStatus() 设置推荐位当前所处的状态
	 * 接收 post 传来的数据, status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * 根据 id 直接修改数据表
	 * @return array       用于回调函数中弹出层的状态信息 show()
	 */
	public function setStatus(){
		try {
			if((isset($_POST['id']) && isset($_POST['status']))){
				$id = $_POST['id'];
				$status = $_POST['status'];
				$positionModel = new PositionModel;
				$res = $positionModel->updateStatusById($id, $status);
				if($res)
					return show(1, '操作成功');
				return show(0, '操作失败');
			}
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
		return show(0, '没有提交过来的数据');
	}



}


 ?>