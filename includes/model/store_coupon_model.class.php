<?php
/**
 *模型层:优惠券
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_coupon_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'store_coupon';
		parent::__construct();
	}
}