<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\controller;

use api\controller;

class subaccount extends base
{
    private $memberInfo   = array();
    private $subAccountObj   = array();

    public function __construct()
    {
        error_reporting(E_ERROR);
        
        $this->memberInfo       = get_member_account();
        $this->subAccountObj    = new \service\seller\subAccountService();
        $this->weixin           = new \WeixinTool();
        parent::__construct();
        
    }
    
    public function index(){
        
    }
    
    //子账户信息
    public function subaccountInfo(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,outgold,realname,nickname,avatar');
        
        $redata['memberInfo']['gold'] = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold'] = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['wait_glod'] = FormatMoney($redata['memberInfo']['wait_glod'],2);
        $redata['memberInfo']['outgold'] = FormatMoney($redata['memberInfo']['outgold'],2);
        
        $result = $this->weixin->get_xcx_erweima($this->memberInfo['openid'],2);
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = $this->subAccountObj->getTotalIncome($this->memberInfo['openid']);
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        $redata['memberInfo']['qrcode'] = $result['message'];
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
}
?>