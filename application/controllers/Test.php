<?php
class TestController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    function addPicAction(){
        if($this->getRequest()->isPost()) {
            $model = new CommonModel();
            if (!empty($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {
                $pic = $model->addPic($upload_file = 'upload_file');
                if (!empty($pic)) {
                   echo CommonModel::IMAGE_URL.$pic;
                } else {
                  echo '上传失败';die;
                }
            }
        }
    }

    function mailAction(){
        $mail = new SendMail();
        $to = "xm0563@qq.com";
        $subject = "Test mail";
        $message = "Hello! This is a simple email message.";
         var_dump( $mail->send($to,$subject,$message));
        echo "Mail Sent.";die;
    }


    private function log($content='') {
        $fp = fopen('/tmp/test_book.log','a');
        if(!$fp){
            return ;
        }
        fwrite($fp, $content);
        fclose($fp);
    }


}
