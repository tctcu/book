<?php
abstract class BaseController extends Yaf_Controller_Abstract{

    protected $_layout;
    protected $base_url = '';

    function init(){
        $this->_layout = Yaf_Registry::get('layout');

        $controllerName = strtolower($this->getRequest()->getControllerName());

        $this->base_url = '/'.strtolower($this->getRequest()->module).'/'.strtolower($this->getRequest()->controller).'/'.strtolower($this->getRequest()->action).'/';

        #判断是否登录
        if(!$this->user_is_logined()){
            if($controllerName !== 'index' ){
                $this->set_flush_message('必须登录后才能浏览内容');

                if(isset($_SERVER['REQUEST_URI'])){
                    $url = '/login/?redirect_url='.urlencode($_SERVER['REQUEST_URI']);
                }else{
                    $url = '/login/';
                }
                $this->redirect($url);
                return FALSE;
            }
        }

        $this->_layout->flush_message = $this->get_flsuh_message();

        $this->set_controller_action_toView();
        $this->set_user_toView();
    }

    #设置用户到session
    function set_current_user($user = array()){
        $session_user_key = Yaf_Registry::get('config')->get('product.session_user');
        Yaf_Session::getInstance()->set($session_user_key,$user);
    }

    #获得当前用户
    function get_current_user(){
        $session_user_key = Yaf_Registry::get('config')->get('product.session_user');
        return Yaf_Session::getInstance()->get($session_user_key);
    }

    #判断用户是否登录
    function user_is_logined(){
        $uid = Yaf_Session::getInstance()->get(Yaf_Registry::get('config')->get('product.session_user'));
        if(empty($uid)){
            return 0;
        }else{
            return 1;
        }
    }

    #设置信息
    function set_flush_message($message){
        Yaf_Session::getInstance()->set('flush_message',$message);
    }

    #提取信息
    function get_flsuh_message(){
        $message = Yaf_Session::getInstance()->get('flush_message');
        if(!empty($message)){
            Yaf_Session::getInstance()->del('flush_message');
            return $message;
        }else{
            return '';
        }
    }

    #web_control_menu动态菜单
    function set_controller_action_toView(){
        $this->_view->current_controller = $this->_layout->current_controller = strtolower($this->getRequest()->controller);
        $this->_view->current_action = $this->_layout->current_action = strtolower($this->getRequest()->action);
        $this->_view->current_module = $this->_layout->current_module = strtolower($this->getRequest()->module);
    }

    #设置用户登录信息
    function set_user_toView(){
        $this->_view->user_is_logined = $this->_layout->user_is_logined = $this->user_is_logined();
        $user_has_apps_ok = array();
        if($this->user_is_logined()){
            $session_user_key = Yaf_Registry::get('config')->get('product.session_user');
            $user = Yaf_Session::getInstance()->get($session_user_key);
            $this->_view->user = $this->_layout->user = $user;
        }
        $this->_view->user_has_apps = $user_has_apps_ok;
    }


    function referer(){
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', 'REFERER'));
        if (!empty($_SERVER[$temp])) return $_SERVER[$temp];

        if (function_exists('apache_request_headers'))
        {
            $headers = apache_request_headers();
            if (!empty($headers['REFERER'])) return $headers['REFERER'];
        }

        return false;
    }
}