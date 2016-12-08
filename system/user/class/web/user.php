<?php
defined('SYSTEM_IN') or exit('Access Denied');
	  $operation = !empty($_GP['op']) ? $_GP['op'] : 'listuser';

	   if ($operation == 'listuser') {
	   		if(empty($_GP['id'])){
				$list = mysqld_selectall("select * from " . table('user'));
			}else{
				$sql = "select u.* from ".table('rolers_relation')." as r left join ".table('user')." as u on u.id=r.uid where r.rolers_id={$_GP['id']}";
				$list = mysqld_selectall($sql);
			}

		   $rolers = mysqld_selectall("select name,id from ".table('rolers')." where type=1");
			include page('listuser');
	  }

	  if ($operation == 'rule') {
		  $id      = $_GP['id'];
		  $account = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $id));
		  if (checksubmit('submit')) {
			   if(empty($id) || empty($account))
				{
					message('操作异常',refresh(),'error');
				}

			    $result = mysqld_delete('user_rule', array('uid'=> $account['id'],'menu_db_type'=>1));
				if(!empty($_GP['role_ids']))
				{
					foreach($_GP['role_ids'] as $role_id){
						$item = mysqld_select("select * from ". table('rule') ." where id={$role_id}");
						$data= array(
							'uid'		=> $account['id'],
							'modname'	=> $item['modname'],
							'moddo'		=> $item['moddo'],
							'modop'		=> $item['modop'],
							'role_id'	=> $item['id'],
							'cat_id'    => $item['cat_id'],
							'menu_db_type' => 1
						);
						mysqld_insert('user_rule', $data);
					}

				}
			    message('权限修改成功！',refresh(),'succes');
					
		  }else{

			  $allrule = getSystemRule();
			  $username =$account['username'];

			  $userRule = mysqld_selectall('SELECT * FROM '.table('user_rule')." WHERE  uid=:uid and menu_db_type=:menu_db_type" , array(
				  ':uid'		  => $id,
				  ':menu_db_type' => 1
			  ));
			  foreach($allrule as $key => $item){
				  foreach($userRule as  $rule){
					  if($item['id'] == $rule['role_id']){
						  $allrule[$key]['check']= 1;
					  }//不能else为0
				  }
			  }
			  $result = getRuleParentChildrenArr($allrule);
			  $parent = $result['parent'];
			  $children = $result['children'];

			  $DbFiledList       = getDbTablesInfo();
			  $DbFiledListJson   = json_encode($DbFiledList);
			  $userHasDbRule     = getUserHasDbRule($id);
			  $userHasDbRuleJson = empty($userHasDbRule) ? '' : json_encode($userHasDbRule);
		  }
		  include page('rule');
	  }

	  if ($operation == 'rule_field') {   //字段权限
		  $id      = $_GP['id'];
		  $account = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $id));
		  if(!empty($account)){
			//删掉之前的字段规则
			  mysqld_delete('user_rule',array('menu_db_type'=>2,'uid'=>$id));
			  //插入新的
			  if(isset($_GP['shop_goods']) && !empty($_GP['shop_goods'])){
				  $data = array(
					  'db_name'     => 'shop_goods',
					  'db_rule'     => json_encode($_GP['shop_goods']),
					  'menu_db_type'=>2,
					  'uid'         => $id
				  );
				  mysqld_insert('user_rule', $data);
			  }
			  if(isset($_GP['shop_dish']) && !empty($_GP['shop_dish'])){
				  $data = array(
					  'db_name'     => 'shop_dish',
					  'db_rule'     => json_encode($_GP['shop_dish']),
					  'menu_db_type'=>2,
					  'uid'         => $id
				  );
				  mysqld_insert('user_rule', $data);
			  }
			  die(showAjaxMess('200','高级权限设置成功！'));
		  }else{
			  die(showAjaxMess('1002','对不起，该用户不存在！'));
		  }
	  }

	  if ($operation == 'deleteuser') {
		    //查找之前是否有关联过一些渠道商
		    isRelationPurchase($_GP['id']);
	  		mysqld_delete('user', array('id'=>$_GP['id']));
		    mysqld_delete('user_rule', array('uid'=> $_GP['id']));
			message('删除成功',refresh(),'success');
	  }
	  if ($operation == 'changepwduser') {
	  		
					$account = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $_GP['id']));
					$username =$account['username'];
					$id =$account['id'];
					if (checksubmit('submit')) {
							if(!empty($account['id']))
							{
								if($_GP['newpassword']!=$_GP['confirmpassword'])
								{

										message('两次密码不一致！',refresh(),'error');
								}
								$data = array('mobile'=>$_GP['mobile']);
								if(!empty($_GP['newpassword'])){
									$data['password']  = md5($_GP['newpassword']);
								}
								 mysqld_update('user', $data,array('id'=> $account['id']));
								 message('资料修改成功！',create_url('site',array('name' => 'user','do' => 'user','op'=>'listuser')),'succes');
							}else
							{
								message($_GP['username'].'用户名已存在',refresh(),'error');
							}
					 	
					}
				include page('changepwd');
	  }
	  
	  if ($operation == 'adduser') {
			if (checksubmit('submit')) {
					if(empty($_GP['username'])||empty($_GP['newpassword']))
					{
						message('用户名或密码不能为空',refresh(),'error');
					}
					$account = mysqld_select('SELECT * FROM '.table('user')." WHERE  username=:username" , array(':username'=> $_GP['username']));

					if(empty($account['id']))
					{
						if($_GP['newpassword']!=$_GP['confirmpassword'])
						{

								message('两次密码不一致！',refresh(),'error');

						}
						$data= array('username'=> $_GP['username'],'password'=> md5($_GP['newpassword']),'createtime'=>time());
						if(!empty($_GP['mobile'])){
							$data['mobile']  = $_GP['mobile'];
						}
						 mysqld_insert('user', $data);
						$user_id = mysqld_insertid();
						if( $user_id && !empty($_GP['role_ids'])){
							foreach($_GP['role_ids'] as $role_id){
								$item = mysqld_select("select * from ". table('rule') ." where id={$role_id}");
								$data= array(
									'uid'		=> $user_id,
									'modname'	=> $item['modname'],
									'moddo'		=> $item['moddo'],
									'modop'		=> $item['modop'],
									'role_id'	=> $item['id'],
									'cat_id'    => $item['cat_id'],
									'menu_db_type'=> 1
								);
								mysqld_insert('user_rule', $data);
							}
						}

						$field_arr = empty($_GP['hide_user_filed'])? '' : json_decode($_POST['hide_user_filed'],true);
						if($user_id && !empty($field_arr)){
							foreach($field_arr as $tableKey => $row){
								if(empty($row)){
									continue;
								}
								$data = array(
									'db_name'     => $tableKey,
									'db_rule'     => json_encode($row),
									'menu_db_type'=>2,
									'uid'         => $user_id
								);
								mysqld_insert('user_rule', $data);
							}
						}

						message('新增用户成功！',web_url('user'),'succes');
					}else
					{
						message($_GP['username'].'用户名已存在',refresh(),'error');
					}
			 }else{   //submit结束
				$allrule = getSystemRule();
				$result = getRuleParentChildrenArr($allrule);
				$parent   = $result['parent'];
				$children = $result['children'];

				$DbFiledList     = getDbTablesInfo();
				$DbFiledListJson = json_encode($DbFiledList);
				include page('adduser');
			}
	  }


	if ($operation == 'menu') {  //菜单节点
		$act = $_GP['act'];
		switch($act){
			case 'post':  //添加编辑页面
				$parentMenu = $editMenu = array();
				$menu = mysqld_selectall("select moddescription,pid,id from ".table('rule') ." where pid=0");
				if(!empty($_GP['id'])){
					$editMenu = mysqld_select("select moddescription,moddo,modname,modop,pid,sort,id,cat_id,act_type from ".table('rule') ." where id={$_GP['id']}");
				}
				if(!empty($_GP['parent_id'])){
					$parentMenu = mysqld_select("select moddescription,moddo,modname,modop,pid,sort,id,cat_id,act_type from ".table('rule') ." where id={$_GP['parent_id']}");
				}
				include page('menuPost');
				break;

			case 'postData':  //提交表单
				$data = array(
					'moddescription' => $_GP['moddescription'],
					'moddo'    		=> $_GP['moddo'],
					'modname' 		=> $_GP['modname'],
					'modop'   		=> $_GP['modop'],
					'sort' 	   		=> $_GP['sort'],
					'act_type' 	   	=> $_GP['act_type'],
				);
				if(!empty($_GP['parent_id'])){
					$url  = web_url('user',array('op'=>'sonMenuList','id'=>$_GP['parent_id']));
				}else{
					$url  = web_url('user',array('op'=>'menudisplay'));
				}
				if(!empty($_GP['id'])){  //更新
					mysqld_update('user_rule',array('modop'=>$_GP['modop'],'modname'=>$_GP['modname'],'moddo'=>$_GP['moddo']),array('role_id'=>$_GP['id']));
					mysqld_update("rule",$data,array('id'=>$_GP['id']));
					cleanSystemRule();
					message('更新菜单成功！',$url,'succes');
				}else{  //添加
					if($_GP['cat_id'] == 0){
						message("对不起，请选择分类！",'','error');
					}
					$data['pid']     = $_GP['pid'];
					$data['cat_id']  = $_GP['cat_id'];
					mysqld_insert('rule',$data);
					cleanSystemRule();
					message('新增菜单成功！',$url,'succes');
				}
				break;

			case 'delete' :
				if(empty($_GP['id'])){
					message('对不起参数有误！','','error');
				}
				if(is_array($_GP['id'])){   //批量删除
					foreach($_GP['id'] as $id){
						$result  = mysqld_delete('rule',array('id'=>$id));
						$result2 = mysqld_delete('user_rule',array('role_id'=>$id));
					}
				}else{   //单个删除
					$result  = mysqld_delete('rule',array('id'=>$_GP['id']));
					$result2 = mysqld_delete('user_rule',array('role_id'=>$id));
				}

				if($result){
					cleanSystemRule();
					message("删除成功！");
				}
				break;

			default:

				break;
		}
	}

	if ($operation == 'sonMenuList') {//子节点
		$parentInfo = mysqld_select("select moddescription,id,cat_id from ". table('rule') ." where id={$_GP['id']}");
		$menu       = mysqld_selectall("select moddescription,concat(modname,'/',moddo,'/',modop) as url,pid,sort,id,cat_id,act_type from ".table('rule') ." where pid={$_GP['id']} order by sort asc,id asc");
		include page('sonMenuList');
	}

    if($operation == 'menudisplay'){
		$cat = MenuEnum::$getMenuEnumValues;
		$menu = mysqld_selectall("select moddescription,concat(modname,'/',moddo,'/',modop) as url,pid,sort,id,cat_id from ".table('rule') ." where pid=0 order by cat_id asc,sort asc,id asc");
		$data = array();
		if(!empty($menu)){
			foreach($menu as $row){
				if(array_key_exists($row['cat_id'],$cat)){
					$row['cat_name']       = $cat[$row['cat_id']];
					$data[$row['cat_id']][] = $row;
				}
			}
		}

		include page('menuNode');
	}

   if($operation == 'cleanMenu'){
	   cleanSystemRule();
	   message('清除缓存成功',refresh(),'success');
   }

  if($operation == 'rolerlist'){
	  $rolers = mysqld_selectall("select id,isdelete,name,createtime from ".table('rolers')." where type=1");
	  $users  = mysqld_selectall("select username,id from ".table('user'));
	  //查找所有已经角色分配过的用户
	  $rolers_relation = mysqld_selectall("select uid from ".table('rolers_relation'));
	  if(!empty($rolers_relation)){
		  $temp_data  = array();
		  foreach($rolers_relation as $key=>$row){
			  $temp_data[$row['uid']] = $row;
		  }
		  $rolers_relation = $temp_data;
	  }

	  //去除已经分配过的用户  这样避免每个用户被添加到多个角色里
	  foreach($users as $key => $user){
			if(array_key_exists($user['id'],$rolers_relation)){
				unset($users[$key]);
			}
	  }

	  $purchase= mysqld_selectall("select id,pid,name,type,createtime from ".table('rolers')." where type<>1 order by pid asc");
	  if (! empty($purchase)) {
		  $childrens = '';
		  foreach ($purchase as $key => $item) {
			  if (! empty($item['pid'])) {
				  $childrens[$item['pid']][] = $item;
				  unset($purchase[$key]);
			  }
		  }
	  }
	  include page('rolerlist');
  }

  if($operation == 'deleterolers'){
	  $rolers = mysqld_select("select id,pid,isdelete,type from ".table('rolers')." where id={$_GP['id']}");
	  if($rolers['type'] == 1){
		  //1代表后台管理员角色使用
		  if($rolers['isdelete'] == 0){ //不可删除
			  message('对不起，该角色不允许删除！',refresh(),'error');
		  }
	  }else{
		  //2代表渠道商这边身份使用  后期可能会扩展其他身份
		  if($rolers['pid'] == 0){ //不可删除
			  message('对不起，该身份不允许删除！',refresh(),'error');
		  }
	  }

	  mysqld_delete('rolers',array('id'=>$_GP['id']));
	  message('删除成功！',refresh(),'success');
  }
  if($operation == 'changerolers'){
	  if(empty($_GP['rolers_name']))
		  message('对不起，名字不能为空！',refresh(),'error');
	  mysqld_update('rolers',array('name'=>$_GP['rolers_name']),array('id'=>$_GP['id']));
	  message('修改成功！',refresh(),'success');
  }
  if($operation == 'addrolers'){
	  if(empty($_GP['rolers_name']))
		  message('对不起，名字不能为空！',refresh(),'error');
	  mysqld_insert('rolers',array(
		  'name'=>$_GP['rolers_name'],
		  'type'=>1,
		  'createtime'=>time(),
		  'modifiedtime'=>time()
	  ));
	  message('添加成功！',refresh(),'success');
  }

  if($operation == 'add_purchase_rolers'){
	  if($_GP['type'] == 0)
		  message('对不起请选择身份类型！',refresh(),'error');
	  if(empty($_GP['rolers_name']))
		  message('对不起，名称不能为空！',refresh(),'error');
	  mysqld_insert('rolers',array(
		  'name'=>$_GP['rolers_name'],
		  'pid' =>$_GP['pid'],
		  'type'=>$_GP['type'],
		  'createtime'=>time(),
		  'modifiedtime'=>time()
	  ));
	  message('添加成功！',refresh(),'success');
  }
  if($operation == 'showuser'){
	  $sql = "select u.id,u.username,r.rolers_id from ".table('rolers_relation')." as r left join ".table('user')." as u";
	  $sql .= " on u.id=r.uid where r.rolers_id={$_GP['id']}";
	  $users = mysqld_selectall($sql);
	 die(showAjaxMess(200,$users));
  }

  if($operation == 'add_rolers_relation')
  {
     //先删除再加入   id是rolers表中id
	  mysqld_delete("rolers_relation",array('rolers_id'=>$_GP['id']));
	  foreach($_GP['uids'] as $key => $uid){
		  mysqld_insert('rolers_relation',array('uid'=>$uid,'rolers_id'=>$_GP['id'],'createtime'=>time()));
	  }
	  message('操作成功！',refresh(),'success');
  }

 if($operation == 'fenpei_rolers')
 {
	 if(empty($_GP['uid']))
		 message('对不起，参数有误',refresh(),'error');
	 if( empty($_GP['rolers_id']))
		 message('对不起，你没有选择角色',refresh(),'error');

	 //查找之前是否有关联过一些渠道商
	 isRelationPurchase($_GP['uid']);
	 //先删除之前分配的
	 mysqld_delete('rolers_relation',array('uid'=>$_GP['uid']));
	 mysqld_insert('rolers_relation',array(
			'rolers_id' => $_GP['rolers_id'],
			'uid'       => $_GP['uid'],
			'createtime'=> time(),
	 ));
	 message('操作成功！',refresh(),'success');
 }

 if($operation == 'rolercate'){
	 //根据type获取对应的顶级角色分类
	 $roler = mysqld_select("select name,id,type from ".table('rolers')." where type={$_GP['type']} and pid=0");
	 if(empty($roler)){
		//则前端需要创建一个顶级分类
		 die(showAjaxMess(1002,'无分类'));
	 }else{
		//则前端会显示该顶级分类
		 die(showAjaxMess(200,$roler));
	 }
 }

function isRelationPurchase($uid){
	$mobile = '';
	$purchase = mysqld_select("select mobile from ".table('member')." where relation_uid={$uid}");
	if(!empty($purchase))
		$mobile = $purchase['mobile'];

	if(!empty($mobile)){
		message("对不起，关联了用户{$mobile},请先去修改！");
	}
}