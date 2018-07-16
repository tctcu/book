<?php
class BookController extends AdminController
{

	function init()
	{
		parent::init();
	}
	#书籍资料列表
	function indexAction()
	{
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

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $book_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_view->type = $book_model->type;
		$this->_view->category = $book_model->category;
		$this->_view->rowsets = $show_list;
		$this->_view->params = $condition;
		$this->_layout->meta_title = '书籍列表页';

	}
	#编辑/新增书籍
	function createAction()
	{
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$book_model = new BookModel();

		$info = [];
		if($id > 0){
			$info = $book_model->getData($id);
		}

		if($this->getRequest()->isPost()) {
			$title = !empty($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
			$author = !empty($_REQUEST['author']) ? trim($_REQUEST['author']) : '';
			$press = !empty($_REQUEST['press']) ? trim($_REQUEST['press']) : '';
			$version = !empty($_REQUEST['version']) ? trim($_REQUEST['version']) : '';
			$category = !empty($_REQUEST['category']) ? trim($_REQUEST['category']) : '';
			$price = !empty($_REQUEST['price']) ? trim($_REQUEST['price']) : '';
			$type = !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : 1;

			$data = array(
				'title' => $title,
				'author' => $author,
				'press' => $press,
				'version' => $version,
				'category' => $category,
				'type' => $type,
				'price' => $price,
			);

			if($info['id']) {
				$book_model->updateData($data, $info['id']);
			} else {
				$book_model->addData($data);
			}

			$this->set_flush_message("编辑/添加活动成功");
			$this->redirect('/admin/book/index/');
			return FALSE;
		}

		$this->_view->type = $book_model->type;
		$this->_view->category = $book_model->category;
		$this->_view->info = $info;
		$this->_layout->meta_title = '编辑/添加书籍';
	}

	#删除书籍
	function deleteAction(){
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';

		if($id){
			$book_model = new BookModel();
			$data['delete'] = 2;//软删除
			$book_model->updateData($data, $id);
		}
		$str = '书籍已删除';
		$this->set_flush_message($str);
		$this->redirect($this->referer());
		return FALSE;
	}

	#切换书籍状态
	function checkStatusAction(){
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
		$status = !empty($_REQUEST['status']) ? intval($_REQUEST['status']) : '1';
		if($id){
			$book_model = new BookModel();
			$data['status'] = $status;
			$book_model->updateData($data, $id);
		}
		$str = $status == 1 ? '书籍已上架' : '书籍已下架';
		$this->set_flush_message($str);
		$this->redirect($this->referer());
		return FALSE;
	}

	#借阅记录
	function borrowListAction(){
		$condition = array();

		$condition['uid'] = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : '';
		$condition['bid'] = isset($_REQUEST['bid']) ? intval($_REQUEST['bid']) : '';
		$condition['status'] = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;
		$condition['book_title'] = isset($_REQUEST['book_title']) ? trim($_REQUEST['book_title']) : '';
		$condition['book_type'] = isset($_REQUEST['book_type']) ? intval($_REQUEST['book_type']) : '';
		$condition['user_name'] = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
		$condition['user_mobile'] = isset($_REQUEST['user_mobile']) ? intval($_REQUEST['user_mobile']) : '';

		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$page_size = 20;
		$user_borrow_model = new UserBorrowModel();
		$show_list = $user_borrow_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->show_list = $show_list;
		#分页处理
		$total_num = $user_borrow_model->getListCount($condition);
		$pagination = $this->getPagination($total_num, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$book_model = new BookModel();
		$this->_view->type = $book_model->type;
		$this->_view->rowsets = $show_list;
		$this->_view->params = $condition;
		$this->_layout->meta_title = '书籍借阅记录列表页';
	}

	#书籍借阅
	function borrowAction(){
		$uid = !empty($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;

		$user_model = new UserModel();
		if($uid > 0){
			$info = $user_model->getDataByUid($uid);
		}else{
			$this->set_flush_message('非法请求');
			$this->redirect('/admin/user/index');
			return FALSE;
		}

		if($this->getRequest()->isPost()) {
			$bid = isset($_REQUEST['bid']) ? intval($_REQUEST['bid']) : 0;
			$start_time = isset($_REQUEST['start_time']) ? strtotime($_REQUEST['start_time']) : 0;
			$end_time = isset($_REQUEST['end_time']) ? strtotime($_REQUEST['end_time']) : 0;

			if(empty($bid) || empty($start_time) || empty($end_time)) {
				$this->set_flush_message('必填不能为空');
				$this->redirect('/admin/book/borrow?uid='.$uid);
				return FALSE;
			}
			if($start_time >= $end_time){
				$this->set_flush_message('借阅预计归还时间不能早于开始时间');
				$this->redirect('/admin/book/borrow?uid='.$uid);
				return FALSE;
			}

			$book_model = new BookModel();
			$book_info = $book_model->getData($bid);
			if(empty($book_info['id'])){
				$this->set_flush_message('该书本不存在');
				$this->redirect('/admin/book/borrow?uid='.$uid);
				return FALSE;
			}
			if($book_info['status']<>1){
				$this->set_flush_message('该书本不可借出(已下架或已借出等)');
				$this->redirect('/admin/book/borrow?uid='.$uid);
				return FALSE;
			}

			$data = array(
				'uid' => $uid,
				'status' => 1,//借阅
				'bid' => $bid,
				'start_time' => $start_time,
				'end_time' => $end_time,
			);
			$user_borrow_model = new UserBorrowModel();

			try{
				$user_borrow_model->addData($data);
				$update['status'] = 3;//已出借
				$book_model->updateData($update,$bid);
			}catch(Exception $e){
				$this->set_flush_message("书籍借阅失败");
				$this->redirect('/admin/book/borrow?uid='.$uid);
				return FALSE;
			}

			$this->set_flush_message("书籍借阅成功");
			$this->redirect('/admin/book/borrowList?uid='.$uid);
			return FALSE;
		}

		$this->_layout->javascript_block = array(
			'/js/jquery.datetimepicker.hour.js'
		);

		$this->_layout->css_block = array(
			'/css/jquery.datetimepicker.css'
		);
		$this->_view->info = $info;
		$this->_layout->meta_title = '书籍借阅';
	}

	#书籍归还
	function backAction(){
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		if($id > 0){
			$user_borrow_model = new UserBorrowModel();
			$borrow_info = $user_borrow_model->getData($id);
			if(empty($borrow_info['uid'])){
				$this->set_flush_message('非法请求,无书可还');
				$this->redirect('/admin/user/index');
				return FALSE;
			}
		}else{
			$this->set_flush_message('非法请求');
			$this->redirect('/admin/user/index');
			return FALSE;
		}

		$user_model = new UserModel();
		$info = $user_model->getDataByUid($borrow_info['uid']);
		$uid = $info['uid'];

		$bid = $borrow_info['bid'];
		$book_model = new BookModel();
		$book_info = $book_model->getData($bid);
		if(empty($book_info['id'])){
			$this->set_flush_message('归还书本不存在');
			$this->redirect('/admin/book/borrow?uid='.$uid);
			return FALSE;
		}
		if($this->getRequest()->isPost()) {
			$return_time = isset($_REQUEST['return_time']) ? strtotime($_REQUEST['return_time']) : 0;

			if(empty($return_time)) {
				$this->set_flush_message('必填不能为空');
				$this->redirect('/admin/book/back?uid='.$uid);
				return FALSE;
			}

			$data = array(
				'status' => 2,//归还
				'return_time' => $return_time,
			);

			try{
				$user_borrow_model->update($data,$id);
				$update['status'] = 1;//可出借
				$book_model->updateData($update,$bid);
			}catch(Exception $e){
				$this->set_flush_message("书籍归还失败");
				$this->redirect('/admin/book/back?uid='.$uid);
				return FALSE;
			}

			$this->set_flush_message("书籍归还成功");
			$this->redirect('/admin/book/borrowList?uid='.$uid);
			return FALSE;
		}

		$this->_layout->javascript_block = array(
			'/js/jquery.datetimepicker.hour.js'
		);

		$this->_layout->css_block = array(
			'/css/jquery.datetimepicker.css'
		);
		$this->_view->id = $id;
		$this->_view->info = $info;
		$this->_layout->meta_title = '书籍归还';
	}

	#活动审核记录
	function recordAction(){
		$this->_layout->javascript_block = array(
			'/js/jquery.datetimepicker.hour.js'
		);

		$this->_layout->css_block = array(
			'/css/jquery.datetimepicker.css'
		);
		$condition = array();
		$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : '0';
		$condition['aid'] = !empty($_REQUEST['aid']) ? intval($_REQUEST['aid']) : '0';
		$condition['uid'] = !empty($_REQUEST['uid']) ? intval($_REQUEST['uid']) : '0';
		$condition['type'] = !empty($_REQUEST['type']) ? intval($_REQUEST['type']) : '0';
		$condition['mobile'] = !empty($_REQUEST['mobile']) ? intval($_REQUEST['mobile']) : '0';
		$condition['begintime'] = !empty($_REQUEST['begintime']) ? strtotime($_REQUEST['begintime']) : '0';
		$condition['endtime'] = !empty($_REQUEST['endtime']) ? strtotime($_REQUEST['endtime']) : '0';

		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

		if($id > 0){
			$condition['id'] = $id;
		}
		$page_size = 20;
		$activity_record_model = new ActivityRecordModel();
		$showlist = $activity_record_model->getListData($page,$page_size,$condition);

		$this->_view->page = $page;
		$this->_view->showlist = $showlist;
		#分页处理
		$totalnum = $activity_record_model->getListCount($condition);
		$condition['begintime'] = !empty($_REQUEST['begintime']) ? $_REQUEST['begintime'] : '';
		$condition['endtime'] = !empty($_REQUEST['endtime']) ? $_REQUEST['endtime'] : '';
		$pagination = $this->getPagination($totalnum, $page, $page_size);
		$this->_view->page = $page;
		$this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

		$this->_view->rowsets = $showlist;

		$this->_view->params = $condition;
		$this->_view->show_menu = 'activity';
		$this->_layout->meta_title = '活动审核记录';
	}

}