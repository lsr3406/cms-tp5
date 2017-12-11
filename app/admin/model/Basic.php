<?php 

namespace app\admin\model;
use think\Model;
use think\Cache;

/**
* Basic
*/
class Basic extends Model{

	public function getBasicConfig(){

		// 没有这个缓存则返回 null
		return Cache::get('basic_web_config');
	}

	public function setBasicConfig($data){
		
		$options = [
			'type' => 'File',	//	缓存类型为File
			'expire' =>	 0,	//	缓存有效期为永久有效
			'prefix' => '',	//缓存前缀, 默认为空
			'path' => APP_PATH.'runtime/cache/',	//	指定缓存目录
		];
		Cache::connect($options);

		// 第三个参数是缓存的有效时间, 0 为永久. 设置成功返回 true, 失败返回 false
		return Cache::set('basic_web_config',$data,0);
	}
}


 ?>