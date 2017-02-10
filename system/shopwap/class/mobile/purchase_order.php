<?php
/*
    废弃用Cookie存产品数据，改用数据库存取数据 2016.12.15
*/
$title = '批发采购';
$is_login = is_vip_account();
$news = isset($cfg['news'])?$cfg['news']:'';
$news = explode("#", $news);
$issendfree = 0;
// 获取用户的参数
$member = get_vip_member_account(true, true);
$user_a = get_user_identity($member['mobile']);
$openid =$member['openid'];
// [parent_roler_id] => 2 [son_roler_id] => 3
// 验证用户是否是批发商
$rolers = mysqld_select("SELECT * FROM ".table('rolers')." WHERE id = ".$member['parent_roler_id']." and (type=2 or type=3) ");
if ( empty($member['parent_roler_id']) || !$rolers || empty($member['son_roler_id']) || $user_a['id'] != $member['son_roler_id'] ){
     header("location:".mobile_url('vip_logout'));
}
$addresslist  = mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and openid = :openid order by isdefault desc ", array(':openid' => $openid));
$promotion=mysqld_selectall("select * from ".table('shop_pormotions')." where type =1 and starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
$page = max(1, $_GP['page']);
$psize = max(20,$_GP['psize']);
$limit =  " limit ".($page-1)*$psize.','.$psize;
$condition = '';
if (!empty($_GP['keyword'])){
	 $_GP['keyword'] = trim($_GP['keyword']);
     switch ( $_GP['key_type'] ){
		 case 'title':
			 $condition = " and b.title like '%".$_GP['keyword']."%' ";
			 break;
		 default:
			 $condition = " and c.goodssn = '".$_GP['keyword']."' ";
			 break;
	 }
}
if ( !empty($_GP['brandbid']) ){
        $bid = intval($_GP['brandbid']);
		$condition .= " AND c.brand = '{$bid}'";
}
 if (!empty($_GP['p2'])) {
		$cid = intval($_GP['p2']);
		$condition .= " AND b.p2 = '{$cid}'";
} elseif (!empty($_GP['p1'])) {
		$cid = intval($_GP['p1']);
		$condition .= " AND b.p1 = '{$cid}'";
}
$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
if (! empty($category)) {
	$childrens = '';
	foreach ($category as $cid => $cate) {
		if (! empty($cate['parentid'])) {
			$childrens[$cate['parentid']][$cate['id']] = array(
				$cate['id'],
				$cate['name']
			);
		}
	}
}
// 根据用户的角色获取产品数据
if ( $user_a['type'] == 2 ){
	 // 找到批发权限
	 $gank   = mysqld_select("SELECT * FROM ".table('rolers')." WHERE type = 2 and pid = 0 ");
     $dish_list = mysqld_selectall("SELECT a.*,b.*,c.goodssn,c.thumb as good_img FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.deleted = 0 and b.status = 1 $condition and a.v1 = ".$gank['pid']." and a.v2 = ".$gank['id'].$limit);
	 $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish_vip') . " as a left join ".table('shop_dish')." as b on a.dish_id = b.id WHERE a.v1 = ".$gank['pid']." and a.v2 = ".$gank['id']."  $condition and b.deleted=0  AND b.status = '1' ");

     $best_list = mysqld_selectall("SELECT a.*,b.*,c.goodssn,c.thumb as good_img FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.ispurchase = 1 and b.deleted = 0 and b.status = 1 $condition and a.v1 = ".$gank['pid']." and a.v2 = ".$gank['id']);
     $currency = 2;
	 $brand_list = mysqld_selectall("SELECT * FROM ".table('shop_brand')." WHERE pifa = 1 ");
}else{
     $dish_list = mysqld_selectall("SELECT b.*,c.goodssn,c.thumb as good_img FROM ".table('shop_dish')." AS b  LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.total>0 and b.deleted = 0 and b.status = 1 $condition ".$limit);
	 $total = mysqld_selectcolumn("SELECT count(*) FROM ".table('shop_dish')." AS b LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.total>0 and b.deleted = 0 and b.status = 1 $condition ");
     $currency = 1;
	 $brand_list = mysqld_selectall("SELECT * FROM ".table('shop_brand')." WHERE daifa = 1 ");
}
if ( empty($dish_list) && !empty($_GP['keyword']) && $_GP['key_type'] == 'title') {
     $word = get_word($_GP['keyword']);
	 if ( !empty($word) ){
		 $condition = '';
		 foreach ($word as $word_value ) {
	         $keys[] = " b.title like '%".$word_value."%' ";
		 }
		 $keys = implode(' or ' , $keys);
		 $condition = ' and ('.$keys.')';
		 if ( !empty($_GP['brandbid']) ){
             $bid = intval($_GP['brandbid']);
		     $condition .= " AND c.brand = '{$bid}'";
         }
		 if (!empty($_GP['p2'])) {
			   $cid = intval($_GP['p2']);
			   $condition .= " AND b.p2 = '{$cid}'";
		 } elseif (!empty($_GP['p1'])) {
			   $cid = intval($_GP['p1']);
			   $condition .= " AND b.p1 = '{$cid}'";
		 }
		  // 根据用户的角色获取产品数据
			if ( $user_a['type'] == 2 ){
				 // 找到批发权限
				 $gank   = mysqld_select("SELECT * FROM ".table('rolers')." WHERE type = 2 and pid = 0 ");
				 $dish_list = mysqld_selectall("SELECT a.*,b.*,c.goodssn,c.thumb as good_img FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.deleted = 0 and b.status = 1 $condition and a.v1 = ".$gank['pid']." and a.v2 = ".$gank['id'].$limit);
				 $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish_vip') . " as a left join ".table('shop_dish')." as b on a.dish_id = b.id WHERE a.v1 = ".$gank['pid']." and a.v2 = ".$gank['id']."  $condition and b.deleted=0  AND b.status = '1' ");
				 $currency = 2;
			}else{
				 $dish_list = mysqld_selectall("SELECT b.*,c.goodssn,c.thumb as good_img FROM ".table('shop_dish')." AS b  LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.total>0 and b.deleted = 0 and b.status = 1 $condition ".$limit);
				 $total = mysqld_selectcolumn("SELECT count(*) FROM ".table('shop_dish')." AS b LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.total>0 and b.deleted = 0 and b.status = 1 $condition ");
				 $currency = 1;
			}
	 }
}
if ( !empty($_GP['brandbid']) ){
     $brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id = ".$_GP['brandbid']);
}
// 开始进行标记选中事件selected
// 进入数据库进行查询产品数据 
//$purchase_goods = new LtCookie();
$purchase = get_purchase_cart($openid, $user_a['type']);
// 已选商品数量
$max_purchase = count($purchase);
$best_purchase = array();
foreach( $dish_list as &$dish_list_value){
	  if ( isset( $purchase[$dish_list_value['id']] ) ){
			 $dish_list_value['selected'] = 1;
	  }else{
			 $dish_list_value['selected'] = 0;
	  }
	  unset($dish_list_value['content']);
	  $dish_list_value['price'] = $dish_list_value['marketprice'];
	  $dish_list_value['thumb'] = $dish_list_value['good_img'];
	  $dish_list_value = price_check($dish_list_value, $member['parent_roler_id'],$member['son_roler_id'], $user_a['type']);
	  $dish_list_value['currency'] = $currency;
}
unset($dish_list_value);
foreach( $best_list as &$best_list_value){
	  if ( isset( $purchase[$best_list_value['id']] ) ){
			 $best_list_value['selected'] = 1;
	  }else{
			 $best_list_value['selected'] = 0;
	  }
	  unset($best_list_value['content']);
	  $best_list_value['price'] = $best_list_value['marketprice'];
	  $best_list_value['thumb'] = $best_list_value['good_img'];
	  $best_list_value = price_check($best_list_value, $member['parent_roler_id'],$member['son_roler_id'], $user_a['type']);
	  $best_list_value['currency'] = $currency;
	  // 找出推荐的批发产品;
	  list($best_list_value['purchase_name'],  $best_list_value['purchase_desc']) = explode("#", $best_list_value['explain']);
	  if (empty($best_list_value['purchase_desc'])) {
             $best_list_value['purchase_desc'] = $best_list_value['purchase_name'];
		     $best_list_value['purchase_name'] = $best_list_value['title'];
	  }
	  $best_purchase[] = $best_list_value;
}
unset($best_list_value);
$pager  = pagination($total, $page, $psize,'.product-lists');
// 设置汇率
$exchange_rate = mysqld_select("SELECT * FROM ".table('config')." WHERE name = 'exchange_rate' limit 1 ");
if ( $exchange_rate ){
    $exchange_rate_value =  $exchange_rate['value'] > 5 ? $exchange_rate['value'] : 6.8972;
}else{
    $exchange_rate_value = 6.8972;
}
if (!empty($_POST['page'])){
            if ( is_array($dish_list) && !empty($dish_list) ){ foreach ( $dish_list as $dish_list_value ){ 
				if ( $dish_list_value["selected"] == 0 ){
                   $html_select = '<div class="col-action"><span class="col-check" onclick="colcheck(this)" id="'. $dish_list_value["id"] .'">选择</span></div>';
				}else{
                   $html_select = '<div class="col-action"><span class="col-check added" onclick="colcheck(this)" id="'. $dish_list_value["id"] .'">已选</span></div>';
			    }
				$html .='<li>
						<div class="product-img">
							<img class="product-img-click" product_id="'. $dish_list_value["id"] .'"  src="'. $dish_list_value["thumb"] .'" >
						</div>
						<div class="product-title">
							<div class="product-name" onclick="productName(this)" product_id="'. $dish_list_value["id"] .'">'. $dish_list_value["title"] .'</div>
							<div class="modal fade product-detail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">  
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header"> 
											<button type="button" class="close" data-dismiss="modal">
												<span aria-hidden="true">&times;</span>
												<span class="sr-only">Close</span>
											</button>
											<h4 class="modal-title" class="myModalLabel">'. $dish_list_value["title"] .'</h4>
										</div>
										<div class="modal-body">
											<div class="ajax-load"><img src="__RESOURCE__/recouse/images/ajax-loader.gif"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="product-price">
							<span><i>$</i>'. $dish_list_value["price"] .'</span>
						</div>
						<div class="col-num" cartid="'. $dish_list_value["id"] .'"> 
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-default btn-sm btn-reduce" type="button" onclick="reduceNum('. $dish_list_value["id"] .')">-</button>
								</span>
								<input type="tel" value="1" class="form-control input-sm pricetotal goodsnum " onblur="blurNum(this)"  cartid="'. $dish_list_value["id"] .'" id="goodsnum_'. $dish_list_value["id"] .'" price="'. $dish_list_value["price"] .'"  maxbuy="'. $dish_list_value["total"] .'">
								<span class="input-group-btn">
									<button class="btn btn-default btn-sm btn-add" type="button" onclick="addNum('. $dish_list_value["id"] .','. $dish_list_value["total"] .')">+</button>
								</span>
							</div>
						</div>'.$html_select.'</li>';
			 }} 
       echo $html;
       exit;
}
$op = $_GP['type']; 
switch ( $op ){
	case 'get_content':
		if ( empty($_GP['id']) ){
              die(showAjaxMess('1002', '商品参数异常')); 
     	}
        $content = mysqld_select("SELECT b.content,a.title FROM ".table('shop_dish')." as a LEFT JOIN ".table('shop_goods')." as b on a.gid = b.id WHERE a.id = ".$_GP['id']." limit 1");
		die(showAjaxMess('200', $content)); 
		break;
	case 'add_goods':
		// 批量添加
	    if ( empty($_GP['goods']) || !is_array($_GP['goods']) ){
           die(json_encode(array(
			   "result"=>1,
			   "info"=> count($_GP['goods'])
		   )));
		}
		foreach ($_GP['goods'] as $key=>$value){
			$model = model_good($key,$member['parent_roler_id'],$member['son_roler_id'],$user_a['type']);
			$model['num'] = $value;
			$goods[$key] = $model;
		}
        set_purchase_cart($goods,$openid,$user_a['type']);
		$purchase = get_purchase_cart($openid,$user_a['type']);
		$max_purchase = count($purchase);
		die(json_encode(array(
			   "result"=>0,
			   "max_purchase" => $max_purchase,
			   "info"=> '添加成功'
		    )));
	    exit;
		break;
	case 'get_ships':
		if ( !empty($_GP['id']) && $user_a['type'] == 2 ){
		     $cart_data = array(":openid"=>$openid, ":goodstype"=>$user_a['type'], ":goodsid"=>$_GP['id']);
		     $total = mysqld_select("SELECT * FROM ".table('shop_purchase_cart')." WHERE openid = :openid and goodstype = :goodstype and goodsid = :goodsid ", $cart_data);
             $query = mysqld_select("SELECT a.gid,a.thumb,a.pcate,a.issendfree, a.title,b.content,b.weight,b.coefficient FROM ".table('shop_dish')." as a left join ".table('shop_goods')." as b on a.gid=b.id where a.id =".$_GP['id']);
             $coefficient = $query['coefficient'] > 0 ? $query['coefficient'] :1.22;
		     $freight    =  $query['weight'] * $total['total'] * $coefficient * 2.2046 * 3.5 ;
			 die(json_encode(array(
			   "errno"=>200,
			   "shiprice"=> $freight
		     )));
     	}else{
             die(json_encode(array(
			   "errno"=>1002,
			   "info"=> '参数不正确'
		     )));
		}
		exit;
		break;
	case 'del_good':
		 if ( empty($_GP['id']) ){
           die(json_encode(array(
			   "result"=>1,
			   "info"=> '参数不正确'
		   )));
		 }
		 del_purchase_cart($_GP['id'], $openid, $user_a['type']);
		 $purchase = get_purchase_cart($openid,$user_a['type']);
		 $max_purchase = count($purchase);
		 die(json_encode(array(
			   "result"=>0,
			   "max_purchase" => $max_purchase,
			   "info"=> '删除成功'
		    )));
	    exit;
		break;
	case 'get_goods':
	    $purchase = get_purchase_cart($openid,$user_a['type']);
		$max_purchase = count($purchase);
		$totalprice = 0;
		$purchase_ship = 0;
		// 开始设置运费
		if ( $max_purchase > 0 ){
             foreach( $purchase as $key=>$purchase_value ){
                  $query = mysqld_select("SELECT a.gid,a.thumb,a.pcate,a.issendfree, a.title,b.content,b.weight,b.coefficient,b.thumb as good_img FROM ".table('shop_dish')." as a left join ".table('shop_goods')." as b on a.gid=b.id where a.id =".$key);
                  $coefficient = $query['coefficient'] > 0 ? $query['coefficient'] : 1.22;
		          $freight    =  $query['weight'] * $purchase_value['total'] * $coefficient * 2.2046 * 3.5 ;
				  $purchase_ship += $freight;
				  $purchase[$key]['freight'] = $freight;
				  $purchase[$key]['id'] = $purchase_value['goodsid'];
				  $purchase[$key]['price'] = $purchase_value['marketprice'];
				  $purchase[$key]['num'] = $purchase_value['total'];
				  $purchase[$key]['title'] = $query['title'];
				  $purchase[$key]['img'] = $query['good_img'];
				  $purchase[$key]['content'] = $query['content'];
				  $purchase[$key]['issendfree'] = $query['issendfree'];
				  $purchase[$key]['pcate'] = $query['pcate'];
				  $totalprice += $purchase[$key]['num'] * $purchase[$key]['price'];
			 }
		}
		if ( $user_a['type'] == 3 ){
				if(empty($issendfree)){
					   //========运费计算===============
							foreach($promotion as $pro){
								if($pro['promoteType']==1){
									if(($totalprice)>=$pro['condition']){
										$issendfree=1;		
									}
								} else if($pro['promoteType']==0){
									if($totaltotal>=$pro['condition']){
										$issendfree=1;	
									}
								}		
						}
				} 
				
				if ( $issendfree == 1 ){
                    $ships = 0;
				}else{
                    $ships = shipcost($purchase);
				    $ships = $ships['price'];
				}
		}else{
			    $totalprice = round(($totalprice* $exchange_rate_value),2);
                $ships = round(round($purchase_ship,3) * $exchange_rate_value,2);
		}	
		echo json_encode(array(
			 'result' => 0,
			 "max_purchase" => $max_purchase,
			 "shiprice" => $ships,
			 "goods_price"=> $totalprice,
			 "total_price"=>   $totalprice + $ships ,
			 'purchase'=>$purchase
		));
	    exit;
		break;
	default:
		break;
}
include themePage('purchase_order');