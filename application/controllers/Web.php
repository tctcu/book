<?php
class WebController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    #判断用户登录
    private function check_user_login() {
        $user_model = new UserModel();
        $user_info  = $user_model->getDataByUid($_COOKIE['token']);
        if(empty($user_info)) {
            $time_out = time() -3600;
            setcookie("token", "token", $time_out);
            header('location:/web/login');exit;
        }
    }

    #设置cookie
    private function setCookie($token){
        $time_out = time() + 60 * 60 * 24 * 7;
        setcookie("token", $token, $time_out,'/');
    }


    #登录
    function loginAction(){
        if($this->getRequest()->isPost()) {
            $mobile = intval($_POST['mobile']);
            $password = addslashes(htmlspecialchars(trim($_POST['password'])));

            if($mobile && $password) {
                $user_model = new UserModel();
                $user_info = $user_model->getDataByMobile($mobile);

                if($user_info){
                    if(md5(md5($password) . $user_info['salt']) == $user_info['password']){
                        $this->setCookie($user_info['uid']);
                        header('location:/web/booklist');exit;
                    }
                    $this->_view->message = "密码不正确!";
                }
                $this->_view->message = "手机号不存在!";
            }
            $this->_view->message = "邮箱和密码不能为空!";
        }
    }

    #登出
    function loginOutAction(){
        $time_out = time()-3600;
        setcookie("token", "token", $time_out,'/');
        header('location:/web/login');exit;
    }

    #借阅书籍列表
    function bookListAction(){
        $this->check_user_login();
        $condition = array();
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
        $condition['title'] = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
        $condition['type'] = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
        $condition['status'] = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;
        $condition['category'] = isset($_REQUEST['category']) ? trim($_REQUEST['category']) : '';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

        if($id > 0){
            $condition['id'] = $id;
        }
        $page_size = 20;
        $book_model = new BookModel();
        $show_list = $book_model->getListData($page,$page_size,$condition);

        $this->_view->type = $book_model->type;
        $this->_view->category = $book_model->category;
        $this->_view->page = $page;
        $this->_view->show_list = $show_list;
        $this->_view->params = $condition;
    }

}
