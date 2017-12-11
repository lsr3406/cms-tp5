<?php 

namespace app\index\controller;
use app\admin\model\Menu;
use app\admin\model\News;
use app\admin\model\PositionContent;
use think\Controller;
use think\Cache;

/**
* Cat
*/
class Cat extends Controller{

	public function index($id=''){
		if(!$id)
			abort(404, 'id 不存在');
		$menu = (new Menu)->getMenuById($id);
		if(!$menu || $menu['status'] != '开启')
			abort(404, '栏目不存在或已关闭');

		// 存在该栏目, 注册变量
    	// 注册页面的基本信息
    	$config = Cache::get('basic_web_config');
    	$this->assign('config', $config);

    	// 选择所有开启的前端导航(顶部导航)以及目前所处的前台导航
    	$navs = (new Menu)->getBarMenus();
        $this->assign('navs', $navs);
		$this->assign('result', ['catid' => $menu['menu_id']]);

        // 注册新闻排行榜信息
        $hotCount = 10;
        $hotNews = (new News)->getHotNews(array(),$hotCount);
        $this->assign('hotNews', $hotNews);
        $this->assign('hotCount', $hotCount);
		//注册广告位信息
        $ads = (new PositionContent)->getAllPositionContents(array('position_id'=>array('eq','7'), 'status'=>'1'));
        $this->assign('ads', $ads);
        // 注册新闻列表信息
        $listCount = 5;
        $listNews = (new News)->getLatestNews(array('type'=>$id),$listCount);
        $this->assign('listNews', $listNews);
    	$this->assign('listCount', $listCount);

		return $this->fetch('cat/index');
	}
}


 ?>