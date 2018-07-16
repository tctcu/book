<?php
abstract class AdminController extends BaseController{

	public function init(){
		parent::init();
		$user = $this->get_current_user();
		$module = $this->_view->current_module;
		$controller = $this->_view->current_controller;
		$action = $this->_view->current_action;
		if(empty($user) || $user['type'] < 1 || $user['status']<>1 ){
			$this->set_flush_message('登录账号异常');
			$this->redirect('/');
			exit;
		}

		$admin_roles_model = new AdminRolesModel();
		$check_access = $admin_roles_model->checkAccess($user['uid'], $module, $controller, $action);
		if(!$check_access) {
		 	$this->set_flush_message('没有权限');
			$this->redirect('/');
			exit;
		 }
		 
		$admin_menu_model = new AdminMenuModel;
		$this->_view->admin_menus = $admin_menu_model->getMenu($controller);
	}

	#获取分页参数
	public function getPagination($total_num = 0, $page = 1, $page_size = 20){
		$page_num = ceil($total_num / $page_size);
		$pagination = array(
			"record_count" => $total_num,
			"page_count" => $page_num,
			"first" => 1,
			"last" => $page_num,
			"next" => min($page_num, $page + 1),
			"prev" => max(1, $page - 1),
			"current" => $page,
			"page_size" => $page_size,
			"page_base" => 1,
		);
		return $pagination;
	}
}