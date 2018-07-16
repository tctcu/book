<?php
class UserController extends AdminController
{

	function init()
	{
		parent::init();
	}

	#用户列表
	function indexAction()
	{
		$condition = array();
		$condition['uid'] = !empty($_REQUEST['uid']) ? intval($_REQUEST['uid']) : '0';
		$condition['mobile'] = !empty($_REQUEST['mobile']) ? intval($_REQUEST['mobile']) : '0';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

		$page_size = 20;
		$user_model = new UserModel();
		$showlist = $user_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->showlist = $showlist;
		#分页处理
		$totalnum = $user_model->getListCount($condition);
		$pagination = $this->getPagination($totalnum, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_view->rowsets = $showlist;
		$this->_view->params = $condition;
		$this->_layout->meta_title = '用户表';
	}

	#添加/编辑用户
	function createAction(){
		$uid = !empty($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$user_model = new UserModel();

		if($uid > 0){
			$info = $user_model->getDataByUid($uid);
		}

		if($this->getRequest()->isPost()) {
			$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
			$mobile = isset($_REQUEST['mobile']) ? intval($_REQUEST['mobile']) : 0;

			if(empty($name) || empty($mobile)) {
				$this->set_flush_message('必填不能为空');
				$this->redirect('/admin/user/create?uid='.$uid);
				return FALSE;
			}

			$data = array(
				'name' => $name,
				'mobile' => $mobile,
			);

			if(!empty($info['uid'])) {
				try{
					$user_model->updateData($data, $info['uid']);
				}catch(Exception $e){
					$this->set_flush_message("修改用户失败");
					$this->redirect('/admin/user/create?uid='.$uid);
					return FALSE;
				}
			} else {
				$password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
				if(empty($password)) {
					$this->set_flush_message('密码不能为空');
					$this->redirect('/admin/user/create');
					return FALSE;
				}
				$salt = rand(1000,9999);
				$data['salt'] = $salt;
				$data['password'] = md5(md5($password).$salt);
				try{
					$user_model->addData($data);
				}catch(Exception $e){
					$this->set_flush_message("添加用户失败");
					$this->redirect('/admin/user/create');
					return FALSE;
				}
			}

			$this->set_flush_message("编辑/添加用户成功");
			$this->redirect('/admin/user/index');
			return FALSE;
		}
		$this->_view->info = $info;
		$this->_layout->meta_title = '编辑/添加用户';
	}

	#操作押金
	function pledgeAction(){
		$uid = !empty($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$user_pledge_model = new UserPledgeModel();
		$user_model = new UserModel();

		if($uid > 0){
			$show_list = $user_pledge_model->getAllByUid($uid);
			$info = $user_model->getDataByUid($uid);
		}else{
			$this->set_flush_message('非法请求');
			$this->redirect('/admin/user/index');
			return FALSE;
		}

		if($this->getRequest()->isPost()) {
			$money = isset($_REQUEST['money']) ? trim($_REQUEST['money']) : '';
			$type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;

			if(empty($money) || empty($type)) {
				$this->set_flush_message('必填不能为空');
				$this->redirect('/admin/user/pledge?uid='.$uid);
				return FALSE;
			}

			$data = array(
				'uid' => $uid,
				'money' => $money,
				'type' => $type,
			);

			try{
				$user_pledge_model->addData($data);
				if($type==1){//支付押金
					$update['pledge'] = 2;//押金已付
				}else{
					$update['pledge'] = 1;//押金未付
				}
				$user_model->updateData($update,$uid);
			}catch(Exception $e){
				$this->set_flush_message("添加押金操作记录失败");
				$this->redirect('/admin/user/pledge?uid='.$uid);
				return FALSE;
			}

			$this->set_flush_message("添加押金操作记录成功");
			$this->redirect('/admin/user/pledge?uid='.$uid);
			return FALSE;
		}
		$this->_view->rowsets = $show_list;
		$this->_view->info = $info;
		$this->_layout->meta_title = '添加押金操作记录';
	}
}