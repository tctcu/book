<?php class AdminMenuModel {

	public  $menu_list = array(
			'book' => array(
				'book' => array(
					'title' => '书籍管理',
					'style' => 'glyphicon glyphicon-list-alt',
					'href' => '/admin/book/index',
					'childs' => array(
						'index' => array(
							'title' => '书籍列表',
							'href' => '/admin/book/index/',
							'style' => 'glyphicon glyphicon-chevron-right'
						),
						'borrowList' => array(
							'title' => '书籍借阅记录列表',
							'href' => '/admin/book/borrowList/',
							'style' => 'glyphicon glyphicon-chevron-right'
						),

					)
				),

			),
			'user' => array(
				'user' => array(
					'title' => '用户管理',
					'style' => 'glyphicon glyphicon-registration-mark',
					'href' => '/admin/user/index',
					'childs' => array(
						'index' => array(
							'title' => '用户列表',
							'href' => '/admin/user/index/',
							'style' => 'glyphicon glyphicon-chevron-right'
						),

					)
				),
			),
			'stat' => array(
				'stat' => array(
					'title' => '数据统计',
					'style' => 'glyphicon glyphicon-certificate',
					'href' => '/admin/stat/index',
					'childs' => array(
						'index' => array(
							'title' => '书籍出借排行',
							'href' => '/admin/stat/index/',
							'style' => 'glyphicon glyphicon-chevron-right'
						),
						'pledge' => array(
							'title' => '押金记录',
							'href' => '/admin/stat/pledge/',
							'style' => 'glyphicon glyphicon-chevron-right'
						),


					)
				),
			),
			'adminuser' => array(
				'adminuser' => array(
					'title' => '后台管理',
					'style' => 'glyphicon glyphicon-user',
					'href' => '/admin/adminuser/index/',
					'childs' => array(
						'index' => array(
							'title' => '后台管理',
							'href' => '/admin/adminuser/index/',
							'style' => 'glyphicon glyphicon-chevron-right'
						),


					)
				),

			),

		);
	
	public function getMenu($controller){
		if(isset($this->menu_list[$controller])) {
			$ret = $this->menu_list[$controller];
		} else {
			$ret = array();
		}
		return $ret;
	}
	
}