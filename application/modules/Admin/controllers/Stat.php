<?php
class StatController extends AdminController
{

	function init()
	{
		parent::init();
	}

	#书籍出借排行
	function indexAction()
	{
		$condition = array();
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$page_size = 20;
		$user_borrow_model = new UserBorrowModel();
		$show_list = $user_borrow_model->getStatList($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $user_borrow_model->getStatCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_view->rowsets = $show_list;
		$this->_view->params = $condition;
		$this->_layout->meta_title = '书籍出借排行';
	}

	#押金记录
	function pledgeAction(){
		$condition = array();
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
		$condition['uid'] = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
		$condition['type'] = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
		$condition['user_name'] = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
		$condition['user_mobile'] = isset($_REQUEST['user_mobile']) ? intval($_REQUEST['user_mobile']) : '';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

		if($id > 0){
			$condition['id'] = $id;
		}
		$page_size = 20;
		$user_pledge_model = new UserPledgeModel();
		$show_list = $user_pledge_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $user_pledge_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_view->rowsets = $show_list;
		$this->_view->params = $condition;
		$this->_layout->meta_title = '押金记录';
	}
}