<?php 

namespace app\admin\model;
use think\Request;
use think\File;
use think\Model;

/**
* UploadImage
*/
class UploadImage extends Model{
	private $_uploadObj;
	private $_uploadImageData;

	public function __construct(){

	}

	public function imageUpload(){
		// 获取上传的文件的信息
		$file = request()->file('file'); 
		//给定一个目录  
		$info = $file->move('upload');  
		if (isset($info) && $info->getPathname())
		    return '/'.$info->getPathname();
		return null;  
	}
	
	public function kindUpload(){
		// 获取上传的文件的信息
		$file = request()->file('file'); 
		//给定一个目录  
		$info = $file->move('kindUploadimage');  
		if (isset($info) && $info->getPathname())
		    return '/'.$info->getPathname();
		return null;  
	}
}

 ?>
