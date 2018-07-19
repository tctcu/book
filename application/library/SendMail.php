<?php
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');

class SendMail {
	function send($to, $title, $content) {
		$mail = new PHPMailer(true); 
		$mail->IsSMTP(); 
		$mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码 
		$mail->SMTPAuth = true; //开启认证 
		$mail->Port = 25; 
		$mail->Host = "smtp.qiye.aliyun.com";
		$mail->Username = "postmaster@wzzsl.com";
		$mail->Password = "Wzzsl.com";
		$mail->From = "postmaster@wzzsl.com";
		$mail->FromName = "管理员";
		$mail->AddAddress($to); 
		#$mail->AddAddress($to); 抄送
		$mail->Subject = $title; 
		$mail->Body = $content; 
		$mail->WordWrap = 80; // 设置每行字符串的长度 
		$mail->IsHTML(true); 
		$ret = $mail->Send();
		return $ret;
	}
	
}