<?php
require_once(dirname(__FILE__).'/CloudFileSystem/uploader.php');

class CloudFileSystem{
	private $size = 20971520; #20M
	private $type = 'jpeg,jpg,gif,png,JPG,GIF,PNG,mp4,mov,3gp,apk';

	#文件上传至七牛
	function upload_file($upload_field = '',$remote_file = ''){
		$uploader = new Helper_Uploader();
		if($uploader->existsFile($upload_field)){
			$host_file = $uploader->file($upload_field);
			if(!$host_file->isValid($this->type, $this->size)){
				return false;
			}
			$qiniu = new Qiniu();
			$qiniu->putFile($remote_file,$host_file->tmpFilename());
			return $remote_file;
		}
		return false;
	}

	#视频上传至七牛
	function upload_video($upload_field = '',$remote_file = ''){
		$uploader = new Helper_Uploader();
		if($uploader->existsFile($upload_field)){
			$host_file = $uploader->file($upload_field);
			if(!$host_file->isValid($this->type, $this->size)){
				return false;
			}
			$qiniu = new Qiniu();
			$result = $qiniu->putFile($remote_file,$host_file->tmpFilename(),'video');
			$return = array();
			if(isset($result[0]['key'])){
				$return['link'] = $result[0]['key'];
				$return['persistentId'] = $result[0]['persistentId'];//进程ID用于回调更新封面图
			}
			return $return;
		}
		return false;
	}

	#base64上传至七牛
	function upload_base64($base64_string = '',$remote_file = ''){
		$tmp_remote_file = str_replace('/', '.', $remote_file);//防止创建2级目录
		$tmpFilename = '/tmp/'.$tmp_remote_file;
		$ifp = fopen($tmpFilename,"wb");
		$string = @base64_decode(str_pad(strtr($base64_string, '-_', '+/'), strlen($base64_string) % 4, '=', STR_PAD_RIGHT));
		fwrite($ifp,$string);
		fclose($ifp);
		$qiniu = new Qiniu();
		$qiniu->putFile($remote_file,$tmpFilename);
		unlink($tmpFilename);
		return $remote_file;
	}

	#远程url上传至七牛
	function upload_fetch($url,$remote_file=''){
		if(empty($remote_file)){
			$ext_name = substr(strrchr($url,'.'),1);
			$remote_file = uniqid(rand(), true) . "." . $ext_name;
		}

		if($url && $remote_file) {
			$qiniu = new Qiniu();
			$result = $qiniu->manager('fetch',$remote_file, $url);
			return $result;
		}
		return false;
	}

	#删除指定七牛文件
	function delete($remote_file){
		if($remote_file) {
			$qiniu = new Qiniu();
			$result = $qiniu->manager('delete',$remote_file);
			return $result;
		}
		return false;
	}


}