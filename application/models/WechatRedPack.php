<?php
#微信新春红包记录表(2017)
class WechatRedPackModel extends MysqlModel {
    protected $_name = 'wechat_red_pack';


    #状态说明
    public static $STATUS = array(
        'INIT' => 0,        // 初始新增记录
        'REQFAIL' => 1,     // 请求失败
        'REQSUCC' => 2,     // 请求成功,需要扣用户账户
        'REQTIMEOUT' => 3,  // 请求超时
    );

    function __construct(){
        parent::__construct();
    }
    #删除
    function deleteData($id = 0){
        $this->setMaster(self::MYSQL_MASTER);
        $row = $this->fetchRow("id = {$id}");
        if(empty($row)){
            return false;
        }
        $this->delete("id = {$id}");

    }

    #添加
    function addData($data){
        $this->setMaster(self::MYSQL_MASTER);
        return $this->insert($data);
    }
    #更新
    function updateData($data,$id){
        $this->setMaster(self::MYSQL_MASTER);
        $this->update($data,"id = {$id}");
    }

    #查找单条信息
    function getDataById($id = 0){
        $this->setMaster(self::MYSQL_MASTER);
        $where  = $this->_db->quoteInto('id = ?',$id);
        $data = $this->fetchRow($where);
        if(!empty($data)){
            return $data->toArray();
        }else{
            return array();
        }
    }


    function getListData($page = 1,$page_size =  20,$condition = array()){
        $sql = " select * from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
        }

        if(!empty($condition['status'])){
            $sql .= " and status={$condition['status']} ";
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
        $sql = " select count(*) as num from {$this->_name} where 1 ";
        if(!empty($condition['id'])){
            $sql .= " and id={$condition['id']} ";
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

    function checkApply($openid,$batch){
        $sql = " select * from {$this->_name} where openid='".$openid."' and batch={$batch} limit 1";
        try{
            $data = $this->_db->fetchRow($sql);
        }catch(Exception $ex){
            $data = array();
        }

        return $data;
    }

    function getTotalMoney($status,$batch){
        $sql = "select sum(money) as total from  {$this->_name} where status={$status} and batch={$batch} ";

        $result = $this->_db->fetchRow($sql);

        $total = 0;
        if(!empty($result['total'])) {
            $total = $result['total'];
        }
        return $total;
    }
}