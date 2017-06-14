<?php
require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel/IOFactory.php';

order_auto_close();//自动更新一些订单为关闭
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$hasaddon16=false;
$normal_order_list = array();
$express_order_list = array();
$addon16=mysqld_select("SELECT * FROM " . table('modules') . " WHERE name = 'addon16' limit 1");
if(!empty($addon16['name']))
{
	if(file_exists(ADDONS_ROOT.'addon16/key.php'))
	{
		$normal_order_list = mysqld_selectall("SELECT * FROM " . table('addon16_printer') . " WHERE  printertype=0 order by isdefault desc");
		$express_order_list = mysqld_selectall("SELECT * FROM " . table('addon16_printer') . " WHERE  printertype=1 order by isdefault desc");
		$hasaddon16=true;
	}
}
$mess_list = array();
$_mess    =  mysqld_selectall("SELECT * FROM " . table('shop_mess'));
if ($operation == 'display') {
	$pindex = max(1, intval($_GP['page']));
	$psize = 10;
	$status = !isset($_GP['status']) ? 1 : $_GP['status'];
	$sendtype = !isset($_GP['sendtype']) ? 0 : $_GP['sendtype'];
	$condition = 'A.ordertype<>-2';   //批发订单不显示在订单列表里
	$param_ordersn=$_GP['ordersn'];
    //业务员只能查看跟自己有关联客户的订单
	if(isAgentAdmin()){
		$amdin_uid = $_SESSION['account']['id'];
		$condition .= " AND A.relation_uid={$amdin_uid}";
	}
	if (!empty($_GP['ordersn'])) {
		$condition .= " AND A.ordersn LIKE '%{$_GP['ordersn']}%'";
	}
    if (!empty($_GP['tag']) && $_GP['tag'] != -1) {
		$condition .= " AND A.tag ='".$_GP['tag']."'";
	}
	if (!empty($_GP['paytype'])) {
		$condition .= " AND A.paytypecode ='".$_GP['paytype']."'";
	}
	if (!empty($_GP['dispatch'])) {
		$condition .= " AND A.dispatch =".intval($_GP['dispatch']);
	}
	if (!empty($_GP['endtime'])) {
		$condition .= " AND A.createtime  <= ". strtotime($_GP['endtime']);
	}
	if (!empty($_GP['begintime'])) {
		$condition .= " AND A.createtime  >= ". strtotime($_GP['begintime']);
	}

	if (!empty($_GP['address_realname'])) {
		$condition .= " AND A.address_realname  LIKE '%{$_GP['address_realname']}%'";
	}
	if (!empty($_GP['address_mobile'])) {
		$condition .= " AND A.address_mobile  LIKE '%{$_GP['address_mobile']}%'";
	}

	$status_arr = array(-2,-4,14,34,-121,-321);//退货，退款 退货完成  退款完成 退款关闭  退货关闭 另外处理
	// 对于全部订单不显示关闭 和支付审核的订单
	if ( $status == '-99' ){
        $condition .= " AND A.status != -1 and A.status != -7 ";
	}else if( in_array($status,$status_arr)) {
		//不用处理
	}else{
		$condition .= " AND A.status = '" . intval($status) . "'";
	}
	if ($status == '3') {
		$condition .= " and ( A.status = 3 or A.status = -5 or A.status = -6)";
	}
	$dispatchs    = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
	$dispatchdata = array();
	if(is_array($dispatchs)) {
		foreach($dispatchs as $disitem) {
			$dispatchdata[$disitem['id']]=$disitem;
		}
	}
	$selectCondition="LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	if (!empty($_GP['report'])) {
		$selectCondition="";
	}

	if($status == -2){ //退款
		$sql    = "SELECT A.* FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=3 and C.status in (1,2) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=3 and C.status in (1,2)";
	}else if($status == -4){  //退货
		$sql   = "SELECT A.* FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=1 and C.status in (1,2,3) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=1 and C.status in (1,2,3)";
	}else if($status == 34){  //退款完成
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status=4 group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status=4";
	}else if($status == 14){  //退货完成
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status=4 group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status=4";
	}else if($status == -321){  //退款关闭
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status in (-1,-2) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status in (-1,-2)";
	}else if($status == -121){  //退货关闭
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status in (-1,-2) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where {$condition} AND B.type=1 and B.status in (-1,-2)";
	}else{
		$sql    = "SELECT A.* FROM " . table('shop_order') . " A  WHERE  {$condition} ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = 'SELECT COUNT(A.id) FROM ' . table('shop_order') . " A WHERE  {$condition}";
	}

	$list       = mysqld_selectall($sql);
	$remove_num = 0;
	if($status == 1){
		//移除组团中的订单，组团中,已经支付不要在待发货中显示,仓库人员会以为要发货
		//移除组团成功还没开奖的订单，不能再待发货中显示
		$listArr = remove_ongroup_order($list);
		$list       = $listArr['list'];
		$remove_num = $listArr['remove_num'];
	}

	$total = mysqld_selectcolumn($sqlNum);
	$total -= $remove_num;
	$pager = pagination($total, $pindex, $psize);
	foreach ( $list as $id => $item) {
		$sql  = "select o.total,o.aid,o.optionname, o.id as order_id,o.optionid,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type ";
		$sql .= " ,h.marketprice as dishprice,h.pcate,h.title,h.thumb,h.gid,h.draw,h.p1 from ".table('shop_order_goods')." as o ";
		$sql .= " left join ".table('shop_dish')." as h ";
		$sql .= " on o.goodsid=h.id ";
		$sql .= " where o.orderid={$item['id']}";
		$goods = mysqld_selectall($sql);
		$list[$id]['goods'] = $goods;
		// 关联业务员
		if ( $item['relation_uid'] > 0 ){
              $relation = mysqld_select("SELECT username FROM ".table('user')." WHERE id =".$item['relation_uid']);
			  $list[$id]['relation_name'] = $relation['username'];
		}
//		$list[$id]['isguest'] =$member['istemplate'];
	}

	getBackMonryOrGoodData($status,$list);

	if (!empty($_GP['report'])) {
		foreach ( $list as $id => $item) {
			$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$item['identity_id']));
			$list[$id]['identity']	= $identity['identity_number'];
		}
		$report='orderreport';
		require_once 'report.php';
		exit;
	}

	$payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");

	$hasaddon11=false;
	$addon11=mysqld_select("SELECT * FROM " . table('modules') . " WHERE name = 'addon11' limit 1");
	if(!empty($addon11['name']))
	{
		if(file_exists(ADDONS_ROOT.'addon11/key.php'))
		{
			$hasaddon11=true;
		}

	}
	if (empty($_GP['print'])){
		include page('order_list');
	}else{
		foreach($list as $key=>$item) {
			$orderid=intval($item['id']);
			$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
			if($order['hasbonus'])
			{

				$bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));

			}

			$dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0" );

			$payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
			$dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
			$dispatchdata=array();
			if(is_array($dispatchs)) {
				foreach($dispatchs as $disitem) {
					$dispatchdata[$disitem['id']]=$disitem;
				}
			}
			$goods = mysqld_selectall("SELECT g.id,o.total, g.title, g.status,g.thumb, g.weight, g.goodssn,g.productsn,g.marketprice,h.pcate,g.type,o.optionname,o.aid,o.optionid,o.price as orderprice FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.shopgoodsid=g.id "
				. " left join ". table('shop_dish') . " h on o.goodsid = h.id WHERE o.orderid='{$orderid}'");
			$list[$key]['order']['goods'] = $goods;
		}
		include page('order_print');
	}
}else if ($operation == 'detail') {
	$orderid=intval($_GP['id']);
	$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
	if($order['hasbonus'])
	{

		$bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));

	}

	$dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0 order by sort desc" );

	$payments     = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
	$dispatchs    = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
	$dispatchdata = array();
	if(is_array($dispatchs)) {
		foreach($dispatchs as $disitem) {
			$dispatchdata[$disitem['id']]=$disitem;
		}
	}

	$goods = mysqld_selectall("SELECT g.id,o.total, g.title, g.status,g.thumb, g.goodssn,g.productsn,g.marketprice,o.id as order_id,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.status as order_status, o.type as order_type FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.shopgoodsid=g.id "
		. " WHERE o.orderid='{$orderid}'");
	$order['goods'] = $goods;
	//确认是否可以展示发货按钮  部分团购商品已经支付，不能显示发货按钮
	$ishowSendBtn = checkGroupBuyCanSend($order);
	if (checksubmit('reset')) { //确认标记
		$retag = '';
		if(!empty($order['retag'])){
			$retag = json_decode($order['retag'],true);
		}
		$retag['beizhu'] = $_GP['retag'];
		$json_retag = json_encode($retag);

		mysqld_update('shop_order', array('tag' => $_GP['tag'], 'retag' => $json_retag), array('id' => $orderid));
		message('订单操作成功！', refresh(), 'success');
	}

	$weixin_wxfans = mysqld_selectall("SELECT * FROM " . table('weixin_wxfans') . " WHERE openid = :openid", array(':openid' => $order['openid']));
	$alipay_alifans = mysqld_selectall("SELECT * FROM " . table('alipay_alifans') . " WHERE openid = :openid", array(':openid' => $order['openid']));

	include page('order');

}else if($operation == 'confrimpay')  //确认支付
{
	if(empty($_GP['payreason']))
		message('请输入理由',refresh(),'error');

	$payreason = "确认支付：{$_GP['payreason']}";
	$order      = mysqld_select("select retag from ".table('shop_order')." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], $payreason);

	mysqld_update('shop_order', array('status' => -7,'retag'=>$json_retag,'paytime'=>time()), array('id' => $_GP['id']));
	message('操作成功，等待审核！', refresh(), 'success');


}else if($operation == 'confrimpay_success'){
	$payreason = "确认支付：审核成功";
	$order      = mysqld_select("select openid,price,retag,ordersn from ".table('shop_order')." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], $payreason);

	mysqld_update('shop_order', array('status' => 1,'retag'=>$json_retag), array('id' => $_GP['id']));
	$mark = PayLogEnum::getLogTip('LOG_SHOPBUY_TIP');
	member_gold($order['openid'],$order['price'],'usegold',$mark,false,$order['ordersn']);  //paylog
	message('订单付款已审核为成功！', refresh(), 'success');

}else if($operation == 'confrimpay_fail'){
	$payreason = "确认支付：审核失败";
	$order      = mysqld_select("select retag from ".table('shop_order')." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], $payreason);

	mysqld_update('shop_order', array('status' => 0,'retag'=>$json_retag), array('id' => $_GP['id']));
	message('订单付款已审核为失败！', refresh(), 'success');

}else if($operation == 'confirmsend'){     //确认发货
	$orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$_GP['id']}");
	$order         = mysqld_select("select * from ". table('shop_order') ." where id={$_GP['id']}");
	if ($_GP['express']=="-1" || empty($_GP['expresssn'])) {
		message('请选择快递并输入快递单号！');
	}
	$express=$_GP['express'];

	if(!isSureSendGoods($orderGoodInfo)){
		message('不能发货，该订单有部分商品还没处理完!',refresh(),'error');
	}
	if(!checkGroupBuyCanSend($order)){
		//如果不能发货
		message('不能发货，该团购订单有商品可能还在开奖中！',refresh(),'error');
	}
	$json_retag = setOrderRetagInfo($order['retag'], '发货：已经确认发货');
	$res = mysqld_update('shop_order', array(
		'status'     => 2,
		'retag'      => $json_retag,
		'express'    => $_GP['express'],
		'expresscom' => $_GP['expresscom'],
		'expresssn'  => $_GP['expresssn'],
		'sendtime'   =>time()  //发货时间
	), array('id' => $_GP['id']));

	if($res){

		//推送APP消息
		$num = 0;
		foreach($orderGoodInfo as $row){
			if(in_array($row['status'],array(-1,-2,0))){  //表示正常发货中
				$dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$row['goodsid']}");
				$title = $dishInfo['title'];
				$num++;
			}
		}
		//格式必须顶格
		$msg = "客官，宝贝已经启程啦，等着签收吧~~
订单编号：{$order['ordersn']}
购买商品：{$title}等{$num}件商品
物流公司：{$_GP['expresscom']}
物流单号：{$_GP['expresssn']}
签收后记得给好评哦~~";
		pushOrderImMessage(IM_ORDER_FROM_USER,$order['openid'],$msg);
		message('发货操作成功！', refresh(), 'success');
	}else{
		message('操作失败！', refresh(), 'error');
	}


}

else if($operation=='cancelsend')    //取消发货   不应该有这个按钮，经过李军同意去掉
{
	$orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$_GP['id']}");
	if(!isSureCancleGoods($orderGoodInfo)){
		message('不能取消发货，该订单有部分商品还没处理完或者已经收到货!',refresh(),'error');
	}
	$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], '发货：已经取消发货');
	mysqld_update('shop_order', array(
		'status' => 1,
		'retag'  => $json_retag,
		'sendtime'=>0,
		'express' =>'',
		'expresscom'=>'',
		'expresssn'=>''
	), array('id' => $_GP['id']));
	message('取消发货操作成功！', refresh(), 'success');

}
else if($operation == 'open')    //开启订单
{
	$orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$_GP['id']}");
	if(!isSureOpenGoods($orderGoodInfo))
		message("该订单的所有商品都退款退货了，不允许开启订单",refresh(),'error');

	$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], '开启订单：开启了订单');
	mysqld_update('shop_order', array('status' => 0,'retag'=>$json_retag,'closetime'=>0), array('id' => $_GP['id']));
	message('开启订单操作成功！', refresh(), 'success');

}

else if($operation =='close')    //关闭订单
{
	//退还余额和优惠卷 并关闭订单
	update_order_status($_GP['id'],-1);
	//记录管理员操作日志
	$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], '关闭订单：关闭了订单');
	mysqld_update('shop_order', array('retag'=>$json_retag), array('id' => $_GP['id']));
	message('订单关闭操作成功！', refresh(), 'success');
}

else if($operation=='finish')    //完成相当于确认收货
{
	$data = hasFinishGetOrder($_GP['id']);
	if($data['errno'] == 200){
		message($data['message'],refresh(),'success');
	}else{
		message($data['message'],refresh(),'error');
	}
}
else if($operation == 'modifyaddress')
{
	$id = $_GP['id'];
	if(empty($id))
		message('参数有误！',refresh(),'error');

	$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
	$json_retag = setOrderRetagInfo($order['retag'], '修改订单：修改了订单的收货人信息');
	mysqld_update('shop_order',array(
		'retag'			   => $json_retag,
		'address_realname' => $_GP['address_realname'],
		'address_mobile'   => $_GP['address_mobile'],
		'address_province' => $_GP['address_province'],
		'address_city'     => $_GP['address_city'],
		'address_area'     => $_GP['address_area'],
		'address_address'  => $_GP['address_address']
	),array('id'=>$id));
	message('修改成功！',refresh(),'success');
}
//查看清关材料
elseif($operation == 'identity')
{
	$orderid = intval($_GP['id']);

	//订单信息
	$order 		= mysqld_select("SELECT identity_id FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));

	//身份证信息
	$identity 	= mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$order['identity_id']));
	include page('order_identity');

}else if($operation == 'aftersale_detail'){  //退款详情
	if(empty($_GP['order_good_id'])){
		message('对不起参数有误！',refresh(),'error');
	}
	$orderid = $_GP['orderid'];

	$afterSale       = mysqld_select("select * from ". table('aftersales') ." where order_goods_id={$_GP['order_good_id']}");
	$afterSaleLog    = mysqld_selectall("select * from ". table('aftersales_log') ." where aftersales_id={$afterSale['aftersales_id']} order by log_id asc");
	$afterSaleDialog = mysqld_selectall("select * from ".table('aftersales_dialog')." where aftersales_id={$afterSale['aftersales_id']} order by id asc");
	$order        = mysqld_select("select id,type,status,price,taxprice,total from ". table('shop_order_goods') ." where id={$_GP['order_good_id']}");		//订单商品信息
	$orderInfo		= mysqld_select("select price,goodsprice,balance_sprice,freeorder_price from ". table('shop_order') ." where id={$orderid}");			//订单信息

	//物流货运
	$dispatchlist = mysqld_selectall("SELECT code,name FROM " . table('dispatch')." where sendtype=0" );
	$delivery_corp = $delivery_no = '';   //快递公司和单号
	if(!empty($afterSale['sendback_data'])){
		$sendback_data = unserialize($afterSale['sendback_data']);
		$delivery_name = $delivery_corp = $sendback_data['delivery_corp'] ;
		$delivery_no   = $sendback_data['delivery_no'];
		foreach($dispatchlist as $val){
			if($val['code'] == $delivery_corp){
				$delivery_name = $val['name'];
			}
		}
	}

	$picArr = '';
	if(!empty($afterSale['evidence_pic'])){
		$picArr = explode(";",$afterSale['evidence_pic']);
	}
	// `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '申请状态，-2为撤销申请，-1为审核驳回，0为未申请，1为正在申请，2为审核通过，3为退款成功',
	if($_GP['type'] == 'money'){
		$title = '退款';
		$statusArr = array('-2'=>'撤销申请','-1'=>'审核驳回','1'=>'申请退款','2'=>'审核通过','4'=>'退款成功');
	}else{
		$title = '退货';
		$statusArr = array('-2'=>'撤销申请','-1'=>'审核驳回','1'=>'申请退货','2'=>'审核通过','3'=>'买家退货','4'=>'退货成功');
	}

	include page('aftersale_detail');


}
else if($operation == 'aftersale_dialog')
{   //协商内容记录
	if(empty($_GP['aftersales_id'])){
		message('参数有误!',refresh(),'error');
	}
	if(empty($_GP['content'])){
		message('内容不能为空!',refresh(),'error');
	}
	$data = array(
		'aftersales_id' => $_GP['aftersales_id'],
		'role'		    => 1,
		'content'	    => $_GP['content'],
		'createtime'	=> date('Y-m-d H:i:s')
	);
	mysqld_insert('aftersales_dialog',$data);
	if(mysqld_insertid()){
		message('操作成功!',refresh(),'success');
	}else{
		message('操作失败!',refresh(),'error');
	}
}
else if($operation == 'aftersale_chuli')    //平台处理是否退换货
{
	if(empty($_GP['refund_price']) && empty($_GP['refund_gold']) && empty($_GP['refund_freeorder_price']) && $_GP['status'] == '2')
		message('退款现金或余额不能为空!',refresh(),'error');

	$afterSale = mysqld_select("select * from ". table('aftersales') ." where order_goods_id={$_GP['order_good_id']}");
	$orderInfo = mysqld_select("select id,price,goodsprice,balance_sprice,freeorder_price from ". table('shop_order') ." where id={$_GP['orderid']}");			//订单信息
	
	if(!empty($afterSale)){
		$data = array(
			'admin_explanation' => $_GP['admin_explanation'],
			'modifiedtime'      => date("Y-m-d H:i:s")
		);
		if($_GP['status'] == '2'){
			
			$arrRefund = filterRefundInfo($orderInfo,array('refund_price'=>$_GP['refund_price'],'refund_gold'=>$_GP['refund_gold'],'refund_freeorder_price'=>$_GP['refund_freeorder_price']));
			
			$data['refund_price']  			= $arrRefund['refund_price'];
			$data['refund_gold']  			= $arrRefund['refund_gold'];				//返还余额
			$data['refund_freeorder_price'] = $arrRefund['refund_freeorder_price'];		//返还免单余额
		}
		mysqld_update('aftersales',$data,array('aftersales_id'=>$afterSale['aftersales_id']));

		//插入一条log记录
		$arrLogContent                 = array();
		$arrLogContent['description']  = $_GP['admin_explanation'];

		$data = array(
			'aftersales_id'  => $afterSale['aftersales_id'],
			'order_goods_id' => $_GP['order_good_id'],
			'status' 		 => $_GP['status'],
			'content'        => serialize($arrLogContent),
			'createtime' 	 => date("Y-m-d H:i:s")
		);

		if($_GP['type'] == 'money') {  //表示退款
			if($_GP['status'] == 2){
				$data['title']  = "掌门，您好！卖家同意本次退款申请";
			}else{
				$data['title']  = "掌门，很遗憾...您的退款申请被拒绝";
			}
		}else{
			if($_GP['status'] == 2){
				$data['title']  = "掌门，您好！卖家同意本次退款退货申请";
			}else{
				$data['title']  = "掌门，很遗憾...您的退货退款申请被拒绝";
			}
		}

		mysqld_insert('aftersales_log',$data);

		if(mysqld_insertid()){
			mysqld_update('shop_order_goods',array('status'=>$_GP['status']),array('id'=>$_GP['order_good_id']));
			//加入订单操作日志
			$order_retag = mysqld_select("select o.retag,o.id from ".table('shop_order')." as o left join ".table('shop_order_goods')." as g on o.id=g.orderid where g.id={$_GP['order_good_id']}");
			$json_retag  = setOrderRetagInfo($order_retag['retag'], "售后处理：{$data['title']}");
			mysqld_update('shop_order',array('retag'=>$json_retag),array('id'=>$order_retag['id']));

			$url = web_url('order',array('op'=>'detail','id'=>$_GP['orderid']));

			//给APP买家推送消息
			$orderdata = mysqld_select("select a.openid,a.createtime,a.ordersn,b.goodsid,b.seller_openid,b.commision from ". table('shop_order') ." as a left join ". table('shop_order_goods') ." as b on a.id=b.orderid where a.id={$_GP['orderid']} and b.id={$_GP['order_good_id']}");
			$dishInfo  = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$orderdata['goodsid']}");
			$time      = date("Y-m-d H:i:s",$orderdata['createtime']);

			//格式必须顶格
			$msg  = "{$data['title']}

退款商品:{$dishInfo['title']}

订单编号:{$orderdata['ordersn']}

下单时间:{$time}";

			pushOrderImMessage(IM_ORDER_FROM_USER,$orderdata['openid'],$msg);


			//给APP卖家推送消息  可以删掉  没有卖家了  不用推送
			if(!empty($orderdata['seller_openid']) && !empty($orderdata['commision'])){
				//格式必须顶格
				$msg  = "老板，您好！{$data['title']}
订单编号：{$orderdata['ordersn']}
退款商品：{$dishInfo['title']}
下单时间：{$time}";
				pushOrderImMessage(IM_ORDER_FROM_USER,$orderdata['seller_openid'],$msg);
			}

			message('操作成功！',$url,'success');
		}
	}else{
		message('对不起，售后记录不存在!',refresh(),'error');
	}
}

else if($operation == 'sureBackMoney')     //财务确认退钱
{   // 确认退款
	$order_good_id = $_GP['order_good_id'];
	$order_id      = $_GP['order_id'];
	$aftersales   = mysqld_select("select aftersales_id,refund_price,refund_gold,refund_freeorder_price from ". table('aftersales') ." where order_goods_id={$order_good_id}");
	$orderInfo    = mysqld_select("select * from ". table('shop_order') ." where id={$order_id}");

	if(empty($aftersales)||empty($orderInfo))
		message('对不起，记录不存在!',refresh(),'error');

	//修改订单状态
	$res = mysqld_update('shop_order_goods', array('status' => 4), array('id' => $order_good_id));
	//加入订单操作日志
	$json_retag  = setOrderRetagInfo($orderInfo['retag'], "售后处理：财务确认打款");
	mysqld_update('shop_order', array('retag' => $json_retag), array('id' => $orderInfo['id']));
	if($res){
		
		//有现金退款时
		if($aftersales['refund_price']>0)
		{
			//paylog记录
			$mark = PayLogEnum::getLogTip('LOG_BACK_THIRD_TIP');
			member_gold($orderInfo['openid'],$aftersales['refund_price'],'addgold',$mark,false,$orderInfo['ordersn']);
		}

		$orderAllGood = mysqld_selectall("select id,status,type from ". table('shop_order_goods') ." where orderid={$order_id}");
		$num = 0;
		foreach($orderAllGood as $row){
			if($row['type'] != 0 && $row['status'] == 4)
				$num ++;
		}
		if($num == count($orderAllGood))  //如果商品全部都发生退款退货则，关闭该总订单状态
			mysqld_update('shop_order', array('status' => -1,'closetime'=>time()), array('id' => $order_id));

		//记录售后日志
		$xinxi = "卖家已经给您退款现金¥{$aftersales['refund_price']}元; 返还余额：{$aftersales['refund_gold']}; 返还免单余额：{$aftersales['refund_freeorder_price']}";
		$descript = array('description'=>$xinxi);
		$data = array(
			'aftersales_id'  => $aftersales['aftersales_id'],
			'order_goods_id' => $order_good_id,
			'status' 		 => 4,
			'title'          => '财务已经退款',
			'content'        => serialize($descript),
			'createtime' 	 => date("Y-m-d H:i:s")
		);
		mysqld_insert('aftersales_log',$data);

		//返还免单金额
		returnPriceToMember($orderInfo,$aftersales);

		//给买家APP推送消息
		$order_good = mysqld_select("select * from ". table('shop_order_goods') ." where id={$order_good_id}");
		$dishInfo   = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$order_good['goodsid']}");
		$time       = date("Y-m-d H:i:s",$orderInfo['createtime']);
		//格式必须顶格
		$msg  = "报告，掌门！已经退款成功~~

退款商品:{$dishInfo['title']}
";
		
		if($aftersales['refund_price']>0)
			$msg.= "退款金额:{$aftersales['refund_price']}元
";
		
		if($aftersales['refund_gold']>0)
			$msg.= "返还余额:{$aftersales['refund_gold']}元
";
		
		if($aftersales['refund_freeorder_price']>0)
			$msg.= "返还免单:{$aftersales['refund_freeorder_price']}元
";
		
$msg.= "订单编号:{$orderInfo['ordersn']}
下单时间:{$time}";
		pushOrderImMessage(IM_ORDER_FROM_USER,$orderInfo['openid'],$msg);

		//如果有卖家openid则，则推送APP给卖家说明佣金被扣   可以删除了，没有卖家 无需推送
		if(!empty($order_good['seller_openid']) && !empty($order_good['commision'])){
			//格式必须顶格
			$msg  = "老板，您好！顾客已经退款成功~~
订单编号：{$orderInfo['ordersn']}
购买商品：{$dishInfo['title']}
退款金额：{$aftersales['refund_price']}
返还余额：{$aftersales['refund_gold']}
返还免单余额：{$aftersales['refund_freeorder_price']}
下单时间：{$time}";
			pushOrderImMessage(IM_ORDER_FROM_USER,$order_good['seller_openid'],$msg);
		}
		message('退款操作成功！', refresh(), 'success');
	}else{
		if($_GP['type'] == 'money')
			mysqld_update('shop_order_goods', array('status' => 2), array('id' => $order_good_id));
		else
			mysqld_update('shop_order_goods', array('status' => 3), array('id' => $order_good_id));
		message('操作失败',refresh(),'error');
	}

}elseif ($operation == 'refundbat') {
	$myxls = '';
    if ($_FILES['myxls']['error'] != 4) {
        $upload = file_upload($_FILES['myxls'], false, NULL, NULL,$type='other');
        if (is_error($upload)) {
            message($upload['message'], '', 'error');
        }
        $myxls = $upload['path'];

        if (!file_exists($myxls)) {
			message('文件上传失败，请重试!',refresh(),'error');
		}

		//根据不同类型分别操作
		if($upload['extention'] == 'xlsx' || $upload['extention'] == 'xls') {
			$reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
		}elseif($upload['extention'] == 'csv') {
			$reader = PHPExcel_IOFactory::createReader('CSV')
			  ->setDelimiter(',')
			  ->setInputEncoding('GBK') //不设置将导致中文列内容返回boolean(false)或乱码
			  ->setEnclosure('"')
			  ->setLineEnding("\r\n")
			  ->setSheetIndex(0);
		}else{
			message('文件格式不正确!',refresh(),'error');
		}

		$PHPExcel = $reader->load($myxls); // 载入excel文件
		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数

		// 循环读取每个单元格的数据
		for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
			$sn = $sheet->getCell('A'.$row)->getValue();
			if ($upload['extention'] == 'csv') {
				$sn = substr($sn,1);
				$sn = explode('-',$sn);
				$sn = $sn[0];
			}
			$dataset[] = $sn;
		}

		// 批量确认退款
		foreach ($dataset as $dav) {
			$o = mysqld_selectall("SELECT b.id as order_good_id, b.orderid, b.status as bstatus FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE a.ordersn='".$dav."'");
			if (empty($o)) {
				continue;
			}
			foreach ($o as $ok => $ov) {
				if ($ov['bstatus'] != '2') {
					continue;
				}
				//财务确认退钱流程
				$order_good_id = $ov['order_good_id'];
				$order_id      = $ov['orderid'];
				$aftersales   = mysqld_select("select aftersales_id,refund_price,refund_gold,refund_freeorder_price from ". table('aftersales') ." where order_goods_id={$order_good_id}");
				$orderInfo    = mysqld_select("select * from ". table('shop_order') ." where id={$order_id}");

				if(empty($aftersales)||empty($orderInfo))
					continue;

				//修改订单状态
				$res = mysqld_update('shop_order_goods', array('status' => 4), array('id' => $order_good_id));
				//加入订单操作日志
				$json_retag  = setOrderRetagInfo($orderInfo['retag'], "售后处理：财务确认打款");
				mysqld_update('shop_order', array('retag' => $json_retag), array('id' => $orderInfo['id']));

				if($res) {
					//paylog记录
					$mark  = PayLogEnum::getLogTip('LOG_BACK_THIRD_TIP');
					member_gold($orderInfo['openid'],$aftersales['refund_price'],'addgold',$mark,false,$orderInfo['ordersn']);
					
					//返还余额及免单金额
					returnPriceToMember($orderInfo,$aftersales);

					//扣除积分
				    member_credit($orderInfo['openid'],$orderInfo['price'],'usecredit','订单:'.$orderInfo['ordersn'].'退货扣除积分');

					$orderAllGood = mysqld_selectall("select id,status,type from ". table('shop_order_goods') ." where orderid={$order_id}");
					$num = 0;
					foreach($orderAllGood as $row){
						if($row['type'] != 0 && $row['status'] == 4)
							$num ++;
					}
					if($num == count($orderAllGood))  //如果商品全部都发生退款退货则，关闭该总订单状态
						mysqld_update('shop_order', array('status' => -1,'closetime'=>time()), array('id' => $order_id));

					//记录售后日志
					$xinxi = "卖家已经给您退款¥{$aftersales['refund_price']}元";
					$descript = array('description'=>$xinxi);
					$data = array(
						'aftersales_id'  => $aftersales['aftersales_id'],
						'order_goods_id' => $order_good_id,
						'status' 		 => 4,
						'title'          => '财务已经退款',
						'content'        => serialize($descript),
						'createtime' 	 => date("Y-m-d H:i:s")
					);
					mysqld_insert('aftersales_log',$data);


					//给买家APP推送消息
					$order_good = mysqld_select("select * from ". table('shop_order_goods') ." where id={$order_good_id}");
					$dishInfo   = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$order_good['goodsid']}");
					$time       = date("Y-m-d H:i:s",$orderInfo['createtime']);
					//格式必须顶格
					$msg  = "客官，您好！已经退款成功~~
订单编号：{$orderInfo['ordersn']}
购买商品：{$dishInfo['title']}
退款金额：{$aftersales['refund_price']}
下单时间：{$time}";
					pushOrderImMessage(IM_ORDER_FROM_USER,$orderInfo['openid'],$msg);

					//如果有卖家openid则，则推送APP给卖家说明佣金被扣  可以删除 没有卖家 无需推送
					if(!empty($order_good['seller_openid']) && !empty($order_good['commision'])){
						//格式必须顶格
						$msg  = "老板，您好！顾客已经退款成功~~
订单编号：{$orderInfo['ordersn']}
购买商品：{$dishInfo['title']}
退款金额：{$aftersales['refund_price']}
下单时间：{$time}";
						pushOrderImMessage(IM_ORDER_FROM_USER,$order_good['seller_openid'],$msg);
					}
				}
			}
		}
    }else{
    	message('请上传退款表单!',refresh(),'error');
    }

}else if($operation == 'getAdminName'){
	$uid   = $_GP['uid'];
	$admin = 'xxx';
	if(!empty($uid)){
		$admin = getAdminName($uid);
	}
	die(showAjaxMess(200,$admin));
}elseif ($operation == 'audit') {
	$pindex = max(1, intval($_GP['page']));
	$psize = 10;
	$selectCondition = "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	if (!empty($_GP['report'])) {
		$selectCondition="";
	}
	$sql    = "SELECT A.* FROM " . table('shop_order') . " A  WHERE A.status=-7 ORDER BY  A.createtime DESC ".$selectCondition;
	$sqlNum = 'SELECT COUNT(A.id) FROM ' . table('shop_order') . " A WHERE A.status=-7";
	$list   = mysqld_selectall($sql);
	$total  = mysqld_selectcolumn($sqlNum);
	$pager = pagination($total, $pindex, $psize);
	foreach ( $list as $id => $item) {
		$sql  = "select o.total,o.aid,o.optionname, o.id as order_id,o.optionid,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type ";
		$sql .= " ,h.marketprice as dishprice,h.pcate,h.title,h.thumb,h.gid,h.draw,h.p1 from ".table('shop_order_goods')." as o ";
		$sql .= " left join ".table('shop_dish')." as h ";
		$sql .= " on o.goodsid=h.id ";
		$sql .= " where o.orderid={$item['id']}";
		$goods = mysqld_selectall($sql);
		$list[$id]['goods'] = $goods;
//		$list[$id]['isguest'] =$member['istemplate'];
	}

	include page('order_audit');
}


/**
 * 对现金、余额等进行过滤
 *
 * @param  $orderInfo : array 订单信息
 * @param  $arrRefund : array 返还的金额数组
 *
 * @return $arrRefund : array 过滤后的金额数组
 */
function filterRefundInfo($orderInfo,$arrRefund)
{
	//同一笔订单中已有的退款记录
	$arrAftersales 	= mysqld_selectall("SELECT og.id,a.refund_price,a.refund_gold,a.refund_freeorder_price FROM ".table('shop_order_goods')." og,".table('aftersales')." a WHERE og.id=a.order_goods_id and og.orderid=".$orderInfo['id']);
	
	
	#################### 现金  start####################
	//使用了现金时
	if (!empty($orderInfo['price'])) {
	
		//扣除已有的现金退款记录
		if(!empty($arrAftersales))
		{
			foreach($arrAftersales as $value)
			{
				$orderInfo['price'] = $orderInfo['price']-(float)$value['refund_price'];
			}
		}
		
		//退回现金大于下单时使用的现金金额时
		if($arrRefund['refund_price']>$orderInfo['price'])
		{
			$arrRefund['refund_price'] = $orderInfo['price'];
		}
	}
	else{
		$arrRefund['refund_price'] = 0.00;
	}
	
	//返还现金比0.00元少时
	if($arrRefund['refund_price']<0.00)
	{
		$arrRefund['refund_price'] = 0.00;
	}
	#################### 现金  end####################
	
	
	
	#################### 余额  start####################
	//使用了余额时
	if ($orderInfo['balance_sprice']>0) {

		//扣除已有的余额退款记录
		if(!empty($arrAftersales))
		{
			foreach($arrAftersales as $value)
			{
				$orderInfo['balance_sprice'] = $orderInfo['balance_sprice']-(float)$value['refund_gold'];
			}
		}
		
		//退回免单余额大于下单时使用的免单金额时
		if($arrRefund['refund_gold']>$orderInfo['balance_sprice'])
		{
			$arrRefund['refund_gold'] = $orderInfo['balance_sprice'];
		}
		
	}
	else{
		$arrRefund['refund_gold'] = 0.00;
	}

	//返还余额比0.00元少时
	if($arrRefund['refund_gold']<0.00)
	{
		$arrRefund['refund_gold'] = 0.00;
	}
	#################### 余额  end####################
	
	
	
	#################### 免单余额  start####################
	//使用了免单余额时
	if ($orderInfo['freeorder_price']>0) {
	
		//扣除已有的余额退款记录
		if(!empty($arrAftersales))
		{
			foreach($arrAftersales as $value)
			{
				$orderInfo['freeorder_price'] = $orderInfo['freeorder_price']-(float)$value['refund_freeorder_price'];
			}
		}
		
		//退回免单余额大于下单时使用的免单金额时
		if($arrRefund['refund_freeorder_price']>$orderInfo['freeorder_price'])
		{
			$arrRefund['refund_freeorder_price'] = $orderInfo['freeorder_price'];
		}
	}
	else{
		$arrRefund['refund_freeorder_price'] = 0.00;
	}
	
	//返还免单余额比0.00元少时
	if($arrRefund['refund_freeorder_price']<0.00)
	{
		$arrRefund['refund_freeorder_price'] = 0.00;
	}
	#################### 免单余额  end####################
	

	return $arrRefund;
}

/**
 * 返还余额及免单金额
 * 
 * @param $orderInfo:array 订单信息数组
 * @param $aftersales: 退款信息数组
 * 
 */
function returnPriceToMember($orderInfo,$aftersales)
{
	//有返还余额或者免单金额时
	if ($aftersales['refund_gold']>0 || $aftersales['refund_freeorder_price']>0 ) {
		
		//用户信息
		$mem = mysqld_select("SELECT gold,freeorder_gold_endtime,freeorder_gold FROM ".table('member')." WHERE openid='".$orderInfo['openid']."'");				
		
		if($aftersales['refund_gold']>0)
		{
			$memberData['gold'] = $mem['gold']+$aftersales['refund_gold'];
			
			//记录用户账单的余额收支情况
			insertMemberPaylog($orderInfo['openid'], $aftersales['refund_gold'],$memberData['gold'], 'addgold',PayLogEnum::getLogTip('LOG_BACK_CASH_TIP'),$orderInfo['ordersn']);
		}
		
		if($aftersales['refund_freeorder_price']>0)
		{
			$freeorder_gold_endtime = strtotime('Sunday')+24*3600-1;						//周天的23:59:59
				
			//已有本期免单金额时
			if($mem['freeorder_gold_endtime']==$freeorder_gold_endtime)
			{
				$memberData['freeorder_gold'] 			= $aftersales['refund_freeorder_price']+$mem['freeorder_gold'];
				$memberData['freeorder_gold_endtime'] 	= $freeorder_gold_endtime;
			}
			else{
				$memberData['freeorder_gold'] 			= $aftersales['refund_freeorder_price'];
				$memberData['freeorder_gold_endtime'] 	= $freeorder_gold_endtime;
			}
				
			//记录用户账单的免单金额收支情况
			insertMemberPaylog($orderInfo['openid'], $aftersales['refund_freeorder_price'],$memberData['freeorder_gold'], 'addgold',PayLogEnum::getLogTip('LOG_BACK_FREE_TIP'),$orderInfo['ordersn']);
		}
		
		mysqld_update ('member',$memberData,array('openid' =>$orderInfo['openid']));
	}
}