<?php
class WebController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    function bookListAction(){
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
        $this->_view->params = $condition;
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
