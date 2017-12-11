<?php
namespace app\index\controller;
use app\admin\model\Menu;
use app\admin\model\PositionContent;
use app\admin\model\News;
use think\Config;
use think\Request;
use think\Cache;
use think\Controller;

class Index extends Controller{

    public function index($type=''){

    	// 注册页面的基本信息
    	$config = Cache::get('basic_web_config');
    	$this->assign('config', $config);

    	// 选择所有开启的前端导航(顶部导航)以及目前所处的前台导航
    	$navs = (new Menu)->getBarMenus();
        $this->assign('navs', $navs);
        $this->assign('result', ['catid' => 0]);

        // 注册首页大图信息和小图信息
        $bigImageNews = (new PositionContent)->getAllPositionContents(array('position_id'=>array('eq','8'), 'status'=>'1'));
        $smallImageNews = (new PositionContent)->getAllPositionContents(array('position_id'=>array('eq','6'), 'status'=>'1'),3);
        $this->assign('bigImageNews', $bigImageNews[0]);
        $this->assign('smallImageNews', $smallImageNews);

        //注册广告位信息
        $ads = (new PositionContent)->getAllPositionContents(array('position_id'=>array('eq','7'), 'status'=>'1'));
        $this->assign('ads', $ads);
        

        // 注册新闻列表新闻信息
        $listCount = 6;
        $listNews = (new News)->getLatestNews(array(),$listCount);
        $this->assign('listNews', $listNews);
    	$this->assign('listCount', $listCount);

        // 注册新闻排行榜信息
        $hotCount = 10;
        $hotNews = (new News)->getHotNews(array(),$hotCount);
        $this->assign('hotNews', $hotNews);
        $this->assign('hotCount', $hotCount);

        if($type == 'buildHtml'){
            // 建立静态文件
            Cache::set('index',$this->fetch('index/index'));
        } else {
            // return  $this->fetch();
            return Cache::get('index');
        }

    }

    public function buildHtml(){
        $this->index('buildHtml');
        return show(1, '首页缓存生成成功');
    }


}
