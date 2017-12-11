<?php 

namespace app\admin\controller;
use think\Config;
/**
* 
*/
class Cron {
	
	public function dumpmysql(){
		$mysqluser = Config::get('database')['username'];
		$mysqlpsw = Config::get('database')['password'];
		$mysqldatabase = Config::get('database')['database'];
		$shell = 'mysqldump -u'.$mysqluser.' -p'.$mysqlpsw.' '.$mysqldatabase.' > /tmp/cms/'.$mysqldatabase.''.date("Ymd").'.sql';
		exec($shell);
		return show(1, '数据库备份成功'.$mysqldatabase.''.date("Ymd").'.sql');
	}
}

 ?>
