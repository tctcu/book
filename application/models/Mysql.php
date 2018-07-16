<?php 
Abstract class MysqlModel extends Zend_Db_Table_Abstract{

	const MYSQL_MASTER = 1;
	const MYSQL_SLAVE = 0;

	private $adapter = null;
	protected $memcached = null;


	public function __construct(){
		$this->adapter = Yaf_Registry::get('mysql_cluster');
		parent::__construct($this->adapter->getAdapter(self::MYSQL_SLAVE));
	}


	protected function delPic($remote_pic){
        $cloud_obj = new CloudFileSystem();
        $cloud_obj->delete($remote_pic);
    }

     function addPic($upload_file = 'upload_file'){

        if(isset($_FILES[$upload_file]) && $_FILES[$upload_file]['error'] == 0 ){
            $ext_name = substr(strrchr($_FILES[$upload_file]['name'],'.'),1);
            try{
                $cloud_obj = new CloudFileSystem();
                $remote_pic = uniqid(rand(), true).".".$ext_name;
                if(isset($_FILES[$upload_file])){
                    $flag = $cloud_obj->upload($upload_file,$remote_pic);
                }
                return $remote_pic;

            }catch(Exception $ex){
                return false;
            }
        }else{
            return "";
        }
    }

	protected function setMaster($is_Master = 0){
		if($is_Master == self::MYSQL_MASTER){
			$this->_setAdapter($this->adapter->getAdapter(self::MYSQL_MASTER));
		}else{
			$this->_setAdapter($this->adapter->getAdapter(self::MYSQL_SLAVE));
		}
	}


	protected function throwException($error_code = 0){
		$error_msg = "error_code:{$error_code}";
		throw new Exception($error_msg);
	}


	



	

	#获取分页参数
	public function getPagination($totalnum = 0, $page = 1, $page_size = 20){
		$pagenum = ceil($totalnum / $page_size);
		$pagination = array(
			"record_count" => $totalnum,
			"page_count" => $pagenum,
			"first" => 1,
			"last" => $pagenum,
			"next" => min($pagenum, $page + 1),
			"prev" => max(1, $page - 1),
			"current" => $page,
			"page_size" => $page_size,
			"page_base" => 1,
		);
		return $pagination;
	}


	#上传到本地
	function uploadLocal($upload_field = '', $newanme = '', $rotate_flag = 0, $gen_clear = 0, $newwidth = 400, $newheight=400){

		$newpath = APPLICATION_PATH.$newanme;
		$this->mkdirs(dirname($newpath));
		$tmp_name = $_FILES[$upload_field]['tmp_name'];
		if($rotate_flag) {
			$is_rotate = $this->checkRotate($tmp_name);
		} else {
			$is_rotate = 0;
		}

		$attach_saved = false;
		if(@copy($tmp_name, $newpath) || (function_exists('move_uploaded_file') && @move_uploaded_file($tmp_name, $newpath)) || @rename($tmp_name, $newpath)) {
			@unlink($tmp_name);
			$attach_saved = true;
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
			}
			@fclose($fp);
		}
		if(!$attach_saved) {
			return false;
		} else {
			if($gen_clear) {
				$this->genClearImage($newpath, $newpath, $newwidth, $newheight, $is_rotate);
			}
			return true;
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
	function checkRotate($image_file) {
		$exif = exif_read_data($image_file);//获取exif信息
		if (isset($exif['Orientation']) && $exif['Orientation'] == 6) {
			//旋转
			return 1;
		} else {
			return 0;
		}
	}
	#处理图片精度和大小
	function genClearImage($newpath, $outpath, $newWidth=400, $newHeight=400, $is_rotate=0) {
		$originUrl = $newpath;
		$destUrl = $outpath;
		if (false !== $pos = strrpos($originUrl, ".")) {
			$originType = substr($originUrl, $pos + 1);
		}
		if ("gif" == strtolower ($originType)) {
			$originImg = imagecreatefromgif($originUrl);
		} else if ("jpg" == strtolower ($originType) || "jpeg" == strtolower ($originType)) {
			$originImg = imagecreatefromjpeg($originUrl);
		} else if ("png" == strtolower ($originType)) {
			$originImg = imagecreatefrompng($originUrl);
		} else {
			return true;
		}
		if($is_rotate == 1) {
			$originImg = imagerotate($originImg, -90, 0);
		}
		$originX = imagesx($originImg);
		$originY = imagesy($originImg);
		if (0 == $newWidth || 0 > $newWidth || $originX < $newWidth) {
			$newWidth = $originX;
		}
		if (0 == $newHeight || 0 > $newHeight || $originY < $newHeight) {
			$newHeight = $originY;
		}
		$scaleX = $originX / $newWidth;
		$scaleY = $originY / $newHeight;
		$scale = $scaleX > $scaleY ? $scaleX : $scaleY;
		$destImg = imagecreatetruecolor($originX / $scale, $originY / $scale);
		imagecopyresampled($destImg, $originImg, 0, 0, 0, 0, $originX / $scale, $originY / $scale, $originX, $originY);
		if (false !== $pos = strrpos($destUrl, ".")) {
			$destType = substr($destUrl, $pos + 1);
		}
		if ("gif" == strtolower ($destType)) {
			imagegif($destImg, $destUrl);
		} else if ("jpg" == strtolower ($destType) || "jpeg" == strtolower ($destType)) {
			imagejpeg($destImg, $destUrl);
		} else if ("png" == strtolower ($destType)) {
			imagepng($destImg, $destUrl);
		} else {
			return false;
		}
		return true;
	}
}