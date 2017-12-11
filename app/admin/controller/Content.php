<?php 

namespace app\admin\controller;
use app\admin\controller\Common;
use app\admin\model\Menu as MenuModel;
use app\admin\model\Position as PositionModel;
use app\admin\model\PositionContent as PositionContentModel;
use app\admin\model\News;
use app\admin\model\NewsContent;
use think\Request;
use think\Exception;
use think\Config;

/**
* Content
*/
class Content extends Common{
	
	public function index(Request $request){

		$conds = array();
		$menuModel = new MenuModel;
		// 如果请求的数据有 type, 则返回相应类型的数据
		if( is_numeric($request->param('type')) && $request->param('type')){
			$conds['type'] = $request->param('type');
			$type = $menuModel->getMenuById($conds['type'])['name'];
		} else {
			$type = '所有';
		}
		$title = urldecode($request->param('title'));
		if($title)
			$conds['title'] = $title;
		$this->assign('titleSearched',$title);
		
		// 根据分页要求来获取数据
		$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;	// 暂没用到
		$pageSize = isset($_REQUEST['pageSize']) ? $_REQUEST['pageSize'] : PAGE_SIZE; // 暂没用到
		
		$newsList = (new News)->getNews($conds, $page, $pageSize);

		$webSiteMenu = $menuModel->getBarMenus();
		$positionList = (new PositionModel)->getAllPositions();

		
		$this->assign('webSiteMenu',$webSiteMenu);
		$this->assign('positionList',$positionList);
		$this->assign('newsList',$newsList);
		$this->assign('type',$type);
		
		return $this->fetch('Content/index');
	}

	public function add(Request $request){

		// 如果有 post 过来的数据, 则进入添加文章的业务逻辑
		if($request->post()){
			$data = $request->post();
			// 首先检查数据是否存在
			if(!isset($data['title']) || !trim($data['title']))
				return show(0, '标题不能为空');
			if(!isset($data['content']) || !trim($data['content']))
				return show(0, '文章内容不能为空');
			if(!isset($data['keywords']) || !trim($data['keywords']))
				return show(0, '关键字不能为空');
			if(!isset($data['description']) || !trim($data['description']))
				return show(0, '描述不能为空');
			// 如果 post 过来的内容中有 id 则进入修改的业务逻辑中
			if(isset($data['news_id']))
				return $this->save($data);

			// 下面是添加文章的业务逻辑, 如果主表插入成功, 则继续插入副表
			$news = new News;
			$newsObj = $news->insert($data);
			if($newsObj){
				$newsContent = new NewsContent;
				$newsContentData['content'] = $data['content'];
				$newsContentData['news_id'] = $newsObj->getLastInsID();
				$contentObj = $newsContent->insert($newsContentData);
				if(isset($contentObj))
					return show(1, '文章添加成功');
				return show(0, '主表添加成功, 副表添加失败');
			}
			return show(0, '文章添加失败');
		}

		// 下面的内容是用于显示添加页面的代码, 在 add() 方法没有接收到数据时执行
		// 获取所有开启的前端导航
		$webSiteMenu = (new MenuModel)->getBarMenus();

		// 从配置文件中获取标题颜色和 copyfrom
		$titleFontColor = Config::get()['title_font_color'];
		$copyfrom = Config::get()['copyfrom'];
		$this->assign('web_site_menu', $webSiteMenu);
		$this->assign('title_font_color', $titleFontColor);
		$this->assign('copyfrom', $copyfrom);

		return $this->fetch('Content/add');
	}

	public function edit(Request $request){
		$news_id = $request->param('id');
		if(!$news_id)
			$this->redirect('/admin/Content/index');
		$news = (new News)->find($news_id);
		if(!$news)
			$this->redirect('/admin/Content/index');

		$newsContent = (new NewsContent)->findByNewsid($news_id);
		$content = $newsContent ? $newsContent['content'] : '';

		$webSiteMenu = (new MenuModel)->getBarMenus();
		// 从配置文件中获取标题颜色和 copyfrom
		$titleFontColor = Config::get()['title_font_color'];
		$copyfrom = Config::get()['copyfrom'];
		$this->assign(array(
			'web_site_menu' => $webSiteMenu,
			'title_font_color' => $titleFontColor,
			'copyfrom' => $copyfrom,
			'news' => $news,
			'content' => $content
		));
		return $this->fetch('/Content/edit');
	}

	public function save($data){
		$newsId = $data['news_id'];
		unset($data['news_id']);
		try {
			$res1 = (new News)->updateById($newsId, $data);
			if($res1 === false)	return show(0,'文章主表修改失败');
			$res2 = (new NewsContent)->updateByNewsId($newsId, $data);
			if($res2 === false)	return show(0,'文章副表修改失败');
			return show(1,'修改成功');
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
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
			$news = new News;
			foreach ($listorder as $newsId => $v) {
				// 执行更新
				$res = $news->updataNewsListorderById($newsId, $v);
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

	/**
	 * setStatus() 设置文章当前所出的状态
	 * 接收 post 传来的数据, status 为 1 表示开启, 为 0 表示关闭, -1 表示已删除
	 * 根据 id 直接修改数据表
	 * @return array       用于回调函数中弹出层的状态信息 show()
	 */
	public function setStatus(){
		try {
			if((isset($_POST['id']) && isset($_POST['status']))){
				$id = $_POST['id'];
				$status = $_POST['status'];
				$news = new News;
				$res = $news->updateStatusById($id, $status);
				if($res)
					return show(1, '操作成功');
				return show(0, '操作失败');
			}
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
		return show(0, '没有提交过来的数据');
	}

	public function push(Request $request){
		$postData = $request->post();
		if(!$postData)
			return show(0,'没有提交过来的数据');
		if(!isset($postData['id']) || !is_numeric($postData['id']))
			return show(0,'请选择正确的推荐位');
		$pcid = $postData['id'];
		if(!isset($postData['push']) || !is_array($postData['push']) || !$postData['push'])
			return show(0,'请选择要推荐的文章');

		try {
			$newsList = (new News)->getByNewsIdIn($postData['push']);
			if(!$newsList)
				return show(0, '没有相关内容');
			$res = (new PositionContentModel)->insertNewsList($pcid, $newsList);
			if(!$res)
				return show(0, '添加失败');
			return show(1, '添加成功');
		} catch (Exception $e) {
			return show(0, $e->getMessage());
		}
	}

	public function showNews(Request $request){
		$id = $request->post('id');
		if (!$id)
			return show(0, '参数错误');
		
		$news = (new News)->find($id);
		if(!$news['news_id'])
			return show(0, '没有相关文章');

		$returnData = array(
			'title' => $news['title'],
			'thumb' => $news['thumb'],
		);
		return show(1, '', $returnData);

	}

}

 ?>