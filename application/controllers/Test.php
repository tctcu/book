<?php
class TestController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    function aAction(){
        $model = new WechatModel();
        $token_arr = $model->getAccessToken();
        $access_token = $token_arr['access_token'];
        $user_info_arr =$model->getUserInfo($access_token,'oFQjBwgoBiyamvvvwYW42U3hooHI');
        echo '<pre>';
        print_r($user_info_arr);die;
        echo 'hahha';
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
