<?php
#用户书籍借阅记录表
class UserBorrowModel extends MysqlModel {
    protected $_name = 'user_borrow';

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

    #根据条件查询数量
    function getCountByCondition($condition = array()){
        if(empty($condition)){
            return 0;
        }
        $sql = "select count(*) as num from {$this->_name} where 1 ";
        if(!empty($condition['uid'])){
            $sql .= " and uid={$condition['uid']} ";
        }
        if(!empty($condition['bid'])){
            $sql .= " and bid={$condition['bid']} ";
        }
        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
        }

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }

    function getListData($page = 1,$page_size =  20,$condition = array()){
        $sql = " select b.title as book_title,b.status as book_status,b.type as book_type,b.category as book_category,
                  u.name as user_name,u.mobile as user_mobile,
                  ub.* from {$this->_name} ub
                  LEFT join book b on ub.bid=b.id
                  LEFT JOIN user u on u.uid=ub.uid where 1=1 ";
        if(!empty($condition['uid'])){
            $sql .= " and ub.uid={$condition['uid']} ";
        }
        if(!empty($condition['bid'])){
            $sql .= " and ub.bid={$condition['bid']} ";
        }
        if(!empty($condition['status'])){
            if($condition['status']==3){//逾期未还
                $now = time();
                $sql .= " and ub.status=1 and ub.end_time<=". $now .' ';
            } else {
                $sql .= " and ub.status={$condition['status']} ";
            }
        }

        if(!empty($condition['book_type'])){
            $sql .= " and b.type={$condition['book_type']} ";
        }
        if(!empty($condition['book_title'])){
            $sql .= " and b.title like '%{$condition['book_title']}%' ";
        }

        if(!empty($condition['user_mobile'])){
            $sql .= " and u.mobile = '{$condition['user_mobile']}' ";
        }
        if(!empty($condition['user_name'])){
            $sql .= " and u.name like '%{$condition['user_name']}%' ";
        }

        $sql .= " order by ub.id desc ";

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
        $sql = " select count(*) as num from {$this->_name} ub
                  LEFT join book b on ub.bid=b.id
                  LEFT JOIN user u on u.uid=ub.uid where 1=1  ";
        if(!empty($condition['uid'])){
            $sql .= " and ub.uid={$condition['uid']} ";
        }
        if(!empty($condition['bid'])){
            $sql .= " and ub.bid={$condition['bid']} ";
        }
        if(!empty($condition['status'])){
            if($condition['status']==3){//逾期未还
                $now = time();
                $sql .= " and ub.status=1 and ub.end_time<=". $now .' ';
            } else {
                $sql .= " and ub.status={$condition['status']} ";
            }
        }

        if(!empty($condition['book_type'])){
            $sql .= " and b.type={$condition['book_type']} ";
        }
        if(!empty($condition['book_title'])){
            $sql .= " and b.title like '%{$condition['book_title']}%' ";
        }

        if(!empty($condition['user_mobile'])){
            $sql .= " and u.mobile = '{$condition['user_mobile']}' ";
        }
        if(!empty($condition['user_name'])){
            $sql .= " and u.name like '%{$condition['user_name']}%' ";
        }

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }

    #出借排行统计
    function getStatList($page = 1,$page_size =  20,$condition = array()){
        $sql = "select b.title as book_title,ub.bid,
                  count(*) as num from {$this->_name} ub
                  LEFT join book b on ub.bid=b.id where 1=1 ";

        $sql .= " group by ub.bid order by num desc ";

        $start = ($page -1 ) * $page_size;
        $sql .= " limit {$start}, {$page_size}";
        try{
            $data = $this->_db->fetchAll($sql);
        }catch(Exception $ex){
            $data = array();
        }
        return $data;
    }
    #出借排行统计总数
    function getStatCount($condition = array()){
        $sql = "select count(*) as num from(select distinct bid from {$this->_name} where 1=1 ";


        $sql .=" ) ub ";

        $result = $this->_db->fetchRow($sql);
        $num = 0;
        if(!empty($result['num'])) {
            $num = $result['num'];
        }
        return $num;
    }
}