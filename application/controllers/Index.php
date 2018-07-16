<?php
class IndexController extends BaseController
{
    private $_mysql_cluster = array();

    function init(){
        parent::init();
        $this->_mysql_cluster = Yaf_Registry::get('mysql_cluster');
    }

    #后台主页
    public function indexAction(){
        $this->_layout->javascript_block = array(
            '/js/chart.js',
            '/js/jquery.timer.js');
        $this->_layout->meta_title = '主页';
    }


    #登陆
    public function loginAction(){
        $this->_layout->meta_title = '登录';
        $redirect_url = isset($_GET['redirect_url']) ? addslashes(htmlspecialchars(trim($_GET['redirect_url']))): '';
        $this->_view->redirect_url = $redirect_url;

        if($this->getRequest()->isPost()) {
            $mobile = intval($_POST['mobile']);
            $password = addslashes(htmlspecialchars(trim($_POST['password'])));

            if($mobile && $password) {
                $admin_user_model = new AdminUserModel();
                $user_info = $admin_user_model->getDataByMobile($mobile);

                if(empty($user_info)){
                    $this->set_flush_message('手机号不存在');
					$this->redirect('/login/');
					exit;
                }

                if(md5(md5($password).$user_info['salt']) <> $user_info['password']){
                    $this->set_flush_message('密码不正确');
                    $this->redirect('/login/');
                    exit;
                }

                $user_data_fileds = array('mobile' => '','uid'=> '','name' => '', 'status' => '', 'type' => 0);
                foreach ($user_data_fileds as $key => $value) {
                    $user_data_fileds[$key] = $user_info[$key];
                }

                $this->set_current_user($user_data_fileds);

                if(isset($_POST['redirect_url'])){
                    $this->redirect(trim($_POST['redirect_url']));
                }else{
                    $this->redirect('/');
                }
                exit;
            } else {
                $this->_view->message = "邮箱和密码不能为空!";
            }
        }
    }

    #注销
    public function logoutAction(){
        $this->set_current_user();
        $this->redirect('/');
        return FALSE;
    }
}


