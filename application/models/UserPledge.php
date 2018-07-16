<?php
#用户押金操作记录表
class UserPledgeModel extends MysqlModel {
    protected $_name = 'user_pledge';

    function __construct(){
        parent::__construct();
    }

    #添加
    function addData($data){
        if(empty($data)){
            return false;
        }
        $data['created_at'] = time();
        return $this->insert($data);
    }

    #通过uid查找信息
    function getAllByUid($uid = 0){
        if(empty($uid)){
            return false;
        }
        $sql = "select * from {$this->_name} where uid={$uid} order by id desc";
        $data = $this->_db->fetchAll($sql);

        if(empty($data)){
            $data = array();
        }
        return $data;
    }


    function getListData($page = 1,$page_size =  20,$condition = array()){
        $sql = " select u.name as user_name,u.mobile as user_mobile,
                  up.* from {$this->_name} up
                  LEFT JOIN user u on u.uid=up.uid where 1=1 ";
        if(!empty($condition['uid'])){
            $sql .= " and up.uid={$condition['uid']} ";
        }
        if(!empty($condition['type'])){
            $sql .= " and up.type={$condition['type']} ";
        }

        if(!empty($condition['user_mobile'])){
            $sql .= " and u.mobile = '{$condition['user_mobile']}' ";
        }
        if(!empty($condition['user_name'])){
            $sql .= " and u.name like '%{$condition['user_name']}%' ";
        }

        $sql .= " order by up.id desc ";

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
        $sql = " select count(*) as num from {$this->_name} up
                  LEFT JOIN user u on u.uid=up.uid where 1=1  ";
        if(!empty($condition['uid'])){
            $sql .= " and up.uid={$condition['uid']} ";
        }
        if(!empty($condition['type'])){
            $sql .= " and up.type={$condition['type']} ";
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
}