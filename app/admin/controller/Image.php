<?php 

namespace app\admin\controller;
use think\Controller;

use app\admin\model\UploadImage;

/**
* Image
*/
class Image extends Controller
{
	private $_uploadObj;

	public function ajaxuploadimage(){
		
		$upload = new UploadImage;
		$res = $upload->imageUpload();
		return $res ? show(1, '上传成功', $res) : show(0, '上传失败');
	}

	public function kindUpload(){

		$upload = new UploadImage;
		$res = $upload->kindUpload();
		return $res ? show(0, '上传成功', $res) : show(1, '上传失败');
	}
}

 ?>