<?php
require_once(dirname(__FILE__).'/QiNiu/rs.php');
require_once(dirname(__FILE__).'/QiNiu/io.php');

class QiniuFileSystem {

	private $qiniu_app_key = 'WKTc-qzD5nWaiim8DueHoXIQCV2D11j49-14_KZ7';
	private $qiniu_app_secret = 'k6M25MPsnlsSB-_L1X0d4axMEleHO_SzoiCTSE_c';
	private $bucket = null;

	function __construct($bucket = 'allfree'){
		if(empty($bucket)){
			throw new Exception("the bucket can not be empty");
		}
		$this->bucket = $bucket;
	}

	#获取upToken
	private function getUpToken(){
		Qiniu_SetKeys($this->qiniu_app_key, $this->qiniu_app_secret);
		$putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
		$upToken = $putPolicy->Token(null);
		if(!empty($upToken)){
			return $upToken;
		}else{
			return null;
		}
	}

	#上传
	public function upload($locate_path_file = "",$remote_file = ""){

		$upToken = $this->getUpToken();
		if(empty($upToken)){
			return false;
		}
		
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $remote_file, $locate_path_file, $putExtra);
		if ($err !== null) {
			#print_r($err);
		    return false;
		} else {
		    return true;
		}
	}

	#远程图片上传
	public function upload_remote($url='',$remote_file=""){

		$upToken = $this->getUpToken();
		if(empty($upToken)){
			return false;
		}
		$destBucket = $this->bucket;  //储存空间名
		$destKey = $remote_file;  //需要将下载的文件储存的名称
		$encodeUrl=Qiniu_Encode($url);

		$destEntry = "$destBucket:$destKey";
		$encodedEntry = QiNiu_Encode($destEntry);
		$apiHost = 'http://iovip.qbox.me';
		$apiPath = "/fetch/$encodeUrl/to/$encodedEntry";
		$requestBody = "";  //默认留空
		$mac = new Qiniu_Mac($this->qiniu_app_key, $this->qiniu_app_secret);
		$client = new Qiniu_MacHttpClient($mac);
		QiNiu_RS_Stat($client, $destBucket, $destKey);  //是否已存在空间

		list($ret, $err) = QiNiu_Client_CallWithForm($client, $apiHost . $apiPath, $requestBody);
		if ($err !== null) {
			return false;
		} else {
			return true;
		}
	}

	#删除文件
	public function delete($remote_file = ""){

		Qiniu_SetKeys($this->qiniu_app_key, $this->qiniu_app_secret);
		$client = new Qiniu_MacHttpClient(null);
		$err = Qiniu_RS_Delete($client, $this->bucket, $remote_file);
		if ($err !== null) {
		    return false;
		} else {
			return true;
		}
	}
}