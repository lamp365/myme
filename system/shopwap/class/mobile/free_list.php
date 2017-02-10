<?php
        $member=get_member_account(True,True);
		$openid =$member['openid'] ;
		if ( !empty($_GP['ajax']) && $_GP['op'] = 'free_apply' ){
               $period = getLastWeekPeriod();		//上周一到周天的时间戳
				
				$configSql = 'SELECT free_id,category_id,free_starttime,free_endtime FROM ' . table('free_config');
				$configSql.= " where free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ";
				
				$freeConfig = mysqld_select($configSql);
				
				//超过周3申请
				if(date('N')>3)
				{
					$result['message'] 	= "请于本周三(含)前申请免单";
					$result['code'] 	= 0;
				}
				else{
					$result['message'] 	= "免单申请失败";
					$result['code'] 	= 0;
					
					if(!empty($freeConfig))
					{
						//免单订单商品
						$arrDish = getFreeDish($freeConfig,$member['openid']);
							
						if(!empty($arrDish))
						{
							$arrOrderGoodsId = array();
					
							foreach($arrDish as $value)
							{
								$arrOrderGoodsId[] = $value['order_goods_id'];
							}
					
					
							if(!empty($arrOrderGoodsId))
							{
								if(mysqld_query( "update " . table ( 'shop_order_goods' ) . " SET free_id=".$freeConfig['free_id'].",free_status=1 WHERE id in(".implode(",", $arrOrderGoodsId).") and free_status = 0 " ))
								{
									$result['message'] 	= "免单申请成功";
									$result['code'] 	= 1;
								}
							}
						}
					}
				}
				if ( $result['code'] == 1 ){
				    die(showAjaxMess('200', $result)); 
				}else{
                    die(showAjaxMess('1200', $result)); 
				}
		}
        $period = getLastWeekPeriod();		//上周一到周天的时间戳
		$arrDish= array();
		$configSql = 'SELECT f.category_id,f.free_starttime,f.free_endtime,c.name FROM ' . table('free_config').' f,' .table('shop_category'). " c ";
		$configSql.= " where f.category_id=c.id ";
		$configSql.= " and f.free_starttime='".$period['monday_time']."' and f.free_endtime='".$period['sunday_time']."' ";
		$freeConfig = mysqld_select($configSql);
		if(!empty($freeConfig)){
			 $arrDish = getFreeDish($freeConfig,$openid);
		}
	    $result['data']['free_info']= $freeConfig;
		$result['data']['dish_list_new']= $arrDish;

		$period = getLastWeekPeriod();		//上周一到周天的时间戳
		$configSql = 'SELECT f.category_id,f.free_starttime,f.free_endtime,c.name FROM ' . table('free_config').' f,' .table('shop_category'). " c ";
		$configSql.= " where f.category_id=c.id ";
		$configSql.= " and f.free_endtime<='".$period['monday_time']."' ";
		$configSql.= " order by f.free_endtime desc";
		$configSql.= " limit 0,12 ";		//固定最近12期记录
		//最近12期的免单记录
		$arrFreeConfig = mysqld_selectall($configSql);
		if(!empty($arrFreeConfig)){
			  foreach($arrFreeConfig as $key => $value){
					$arrFreeConfig[$key]['dish_list'] = getFreeDish($value,$member['openid']);
			  }
		}
		$result['data']['dish_list_old'] = $arrFreeConfig;
		include themePage('freepay');