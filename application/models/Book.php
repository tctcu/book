<?php
#书籍资料表
class BookModel extends MysqlModel {
    protected $_name = 'book';
    public $type = [
        '1' => '书籍',
        '2' => '期刊',
        '3' => '课件资料',
    ];

    public $category = [
        '婚姻家庭','少儿读物','灵命成长','神学','传记/见证','工作职场','文学作品','其他'
    ];


    function __construct(){
        parent::__construct();
    }

    #添加
    function addData($data){
        if(empty($data)){
            return false;
        }
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return $this->insert($data);
    }

    #更新
    function updateData($data, $id){
        if(empty($data) || empty($id)){
            return false;
        }
        $data['updated_at'] = time();
        return $this->update($data,"id = {$id}");
    }

    #查找单条信息
    function getData($id = 0){
        if(empty($id)){
            return false;
        }
        $where = $this->_db->quoteInto('id = ?',$id);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }
        return false;
    }

    function getListData($page = 1,$page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where `delete`=1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }
        if(!empty($condition['type'])){
            $sql .= " and type={$condition['type']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }
        if(!empty($condition['category'])){
            $sql .= " and category='{$condition['category']}' ";
        }

        if(!empty($condition['title'])){
            $sql .= " and title like '%{$condition['title']}%' ";
        }

        $sql .= " order by id desc ";

        $start = ($page -1 ) * $page_size;
        $sql .= " limit {$start}, {$page_size}";
        try{
            $data = $this->_db->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }

    function getListCount($condition = array()){
        $sql = " select count(*) as num from {$this->_name} where `delete`=1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }
        if(!empty($condition['type'])){
            $sql .= " and type={$condition['type']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }
        if(!empty($condition['category'])){
            $sql .= " and category='{$condition['category']}' ";
        }

        if(!empty($condition['title'])){
            $sql .= " and title like '%{$condition['title']}%' ";
        }

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }
}