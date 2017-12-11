<?php 

namespace app\index\controller;
use app\admin\model\News;
use app\admin\model\NewsContent;
use app\admin\model\Menu;
use app\admin\model\PositionContent;
use think\Cache;
use think\Controller;

/**
* Detail
*/
class Detail extends Controller{

	public function index($id = ''){

		if(!$id)
			abort('文章 id 不存在');
		$news = (new News)->find($id);
		// dump($news['status']);
		if(!$news)
			abort('文章不存在或已关闭');

		$news->setInc('count',1);
		$newsContent = (new NewsContent)->findByNewsId($id);
		$news['content'] = htmlspecialchars_decode($newsContent['content']);

		// 注册页面的基本信息
		$config = Cache::get('basic_web_config');
		$this->assign('config', $config);

		// 选择所有开启的前端导航(顶部导航)以及目前所处的前台导航
		$navs = (new Menu)->getBarMenus();
	    $this->assign('navs', $navs);
	    $this->assign('result', ['catid' => 0]);

        //注册广告位信息
        $ads = (new PositionContent)->getAllPositionContents(array('position_id'=>array('eq','7'), 'status'=>'1'));
        $this->assign('ads', $ads);
        
        // 注册新闻排行榜信息
        $hotCount = 10;
        $hotNews = (new News)->getHotNews(array(),$hotCount);
        $this->assign('hotNews', $hotNews);
        $this->assign('hotCount', $hotCount);

        // 注册新闻内容
        $this->assign('news', $news);

        return $this->fetch('index/detail');
	}

	public function view($id = ''){
		if(!getLoginUsername()){
			$this->redirect('/admin/Index/index');
		}
		return $this->index($id);
	}
}

 ?>