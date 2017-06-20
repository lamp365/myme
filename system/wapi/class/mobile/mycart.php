<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;

class mycart extends base
{
   public function index()
   {
       $_GP =  $this->request;
       $service  = new \service\wapi\mycartService();
       $cartlist = $service->cartlist();
       ajaxReturnData(1,'请求成功',$cartlist);
   }

    public function addCart()
    {
        $_GP =  $this->request;
        $dishid  = intval($_GP['id']);
        if(empty($dishid)){
            ajaxReturnData(0,'请选择商品');
        }
        $total   = intval($_GP['buy_num']);
        $total   = empty($total) ? 1 : $total;

        $service  = new \service\wapi\mycartService();
        $cartotal = $service->addCart($dishid,$total);
        if(!$cartotal){
           ajaxReturnData(0,$service->getError());
        }

        ajaxReturnData(1,'操作成功！',$cartotal);
    }

    public function updateCart()
    {
        $_GP    =  $this->request;

        $id  = intval($_GP['id']);
        $num = intval($_GP['buy_num']);
        if(empty($id) || empty($num) || $num<0){
            ajaxReturnData(0,'参数有误！');
        }
        $service  = new \service\wapi\mycartService();
        $cartotal = $service->updateCart($id,$num);
        if(!$cartotal){
            ajaxReturnData(0,$service->getError());
        }
        ajaxReturnData(1,'操作成功！');
    }

    public function del()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $_GP = $this->request;
        $id  = intval($_GP['id']);
        mysqld_delete('shop_cart', array(
            'session_id' => $openid,
            'id' => $id
        ));
        ajaxReturnData(1,'已经移除！');
    }

    public function batdel()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $_GP = $this->request;
        if(empty($_GP['ids'])){
            ajaxReturnData(0,'参数有误！');
        }else{
            foreach($_GP['ids'] as $id){
                mysqld_delete('shop_cart', array(
                    'session_id' => $openid,
                    'id' => $id
                ));
            }
            ajaxReturnData(1,'删除成功！');
        }
    }

    public function clean()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        mysqld_delete('shop_cart', array(
            'session_id' => $openid
        ));
        ajaxReturnData(1,'已全部移除！');
    }
    //所选择了哪些商品进行购买
    public function topay()
    {
        $_GP    = $this->request;
        $service  = new \service\wapi\mycartService();
        $res = $service->topay($_GP['ids']);
        if(!$res){
            ajaxReturnData(0,$service->getError());
        }else{
            ajaxReturnData(1,'操作成功！');
        }
    }
}