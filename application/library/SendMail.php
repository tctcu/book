<?php
require(dirname(__FILE__).'/PHPMailer/email.class.php');

class SendMail {
	function send($to, $title, $content) {
		$mailto=$to;
		$mailsubject=$title;
		$mailbody=$content;
		$smtpserver     = "smtp.wzzsl.com";
		$smtpserverport = 25;
		$smtpusermail   = "postmaster@wzzsl.com";
		$smtpuser       = "postmaster@wzzsl.com";
		$smtppass       = "Wzzsl.com";
		$mailsubject    = "=?UTF-8?B?" . base64_encode($mailsubject) . "?=";
		$mailtype       = "HTML";
		$smtp           = new smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);
		//var_dump($smtp);die;
		$smtp->debug    = false;
		return $smtp->sendmail($mailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
	}
	
}