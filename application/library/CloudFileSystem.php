<?php
require_once(dirname(__FILE__).'/CloudFileSystem/uploader.php');

class CloudFileSystem {

	private $allowsize = 20971520; #20M
	private $allowtype = 'jpeg,jpg,gif,png,JPG,GIF,PNG,mp4,mov,3gp,apk';
	private $upload_field = 'upload_file';
	private $cloud = array();

	function __construct(){

		try{

			$this->cloud[] = new QiniuFileSystem($qiniu_bucket = 'allfree');
		}catch(Exception $ex){
			pushAlert($ex->getMessage());
		}
	}


	#字符串另存为方式
	function save($base64_string = '',$remote_file = ''){
		try{
			#防止创建2级目录
			$tmp_remote_file = str_replace('/', '.', $remote_file);
			$tmpFilename = '/tmp/'.$tmp_remote_file;
			$ifp = fopen($tmpFilename,"wb"); 
			$string = @base64_decode(str_pad(strtr($base64_string, '-_', '+/'), strlen($base64_string) % 4, '=', STR_PAD_RIGHT));
		    fwrite($ifp,$string); 
		    fclose($ifp);

		    $flag = 1;
			foreach ($this->cloud as $cloud) {
				$flag = $cloud->upload($tmpFilename,$remote_file) && $flag;
			}
			unlink($tmpFilename);
			return $flag;
			
		}catch(Exception $ex){
			throw new Exception("error_code:-20057");
			return false;
		}
	}
	
	#上传
	function upload($upload_field = '',$remote_file = ''){
		
		$uploader = new Helper_Uploader();
		if($uploader->existsFile($upload_field)){
			$postfile = $uploader->file($upload_field);
			if(!$postfile->isValid($this->allowtype, $this->allowsize)){
				throw new Exception("error_code:-20055");
				return false;
			}

			#上传文件至本地和远程云存储
			$flag = 1;
			foreach ($this->cloud as $cloud) {
				$flag = $cloud->upload($postfile->tmpFilename(),$remote_file) && $flag;
			}
			return $flag;
		}
	}

	#上传
	function upload_from_localfile($localfile = '',$remote_file = ''){
		
		$uploader = new Helper_Uploader();

		#上传文件至本地和远程云存储
		$flag = 1;
		foreach ($this->cloud as $cloud) {
			$flag = $cloud->upload($localfile,$remote_file) && $flag;
		}
		return $flag;
	}
	
	#删除,cloud_flag = 0 全部云平台, cloud_flag = 1 七牛云平台, cloud_flag = 2 又拍云平台
	function delete($remote_file,$cloud_flag = 0){
		$flag = 1;
		foreach ($this->cloud as $cloud) {
			if($cloud instanceof QiniuFileSystem && $cloud_flag != 2){
				$flag = $cloud->delete($remote_file) && $flag;
			}elseif($cloud instanceof YouPaiFileSystem && $cloud_flag != 1){
				$flag = $cloud->delete($remote_file) && $flag;
			}
		}
		return $flag;
	}
	
	#上传
	function upload_local($upload_field = '', $newanme = ''){
		
		$newpath = APPLICATION_PATH.$newanme;
		$this->mkdirs(dirname($newpath));
		$tmp_name = $_FILES[$upload_field]['tmp_name'];
		$attach_saved = false;
		if(@copy($tmp_name, $newpath) || (function_exists('move_uploaded_file') && @move_uploaded_file($tmp_name, $newpath)) || @rename($tmp_name, $newpath)) {
			@unlink($tmp_name);
			$attach_saved = true;
			return true;
		}
		if(!$attach_saved && is_readable($tmp_name)) {
			$fp = @fopen($tmp_name, 'rb');
			@flock($fp, 2);
			$attachedfile = @fread($fp, $_FILES[$upload_field]['size']);
			@fclose($fp);

			$fp = @fopen($newpath, 'wb');
			@flock($fp, 2);
			if(@fwrite($fp, $attachedfile)) {
				@unlink($tmp_name);
				$attach_saved = true;
				return true;
			}
			@fclose($fp);
		}
		if(!$attach_saved) {
			return false;
		}
	}
	function delete_local($remote_file){
		@unlink(APPLICATION_PATH.$remote_file);
		return ;
	}
	function mkdirs($dir) {
		if(!is_dir($dir)) {  
			if(!$this->mkdirs(dirname($dir))) {  
				return false;
			}
			if(!mkdir($dir, 0777)){  
				return false;  
			}  
		}  
		return true;  
	}
}