<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		i{
			color: red;
			font-weight: bolder;
		}
	</style>
<h3 class="header smaller lighter blue">虚拟用户管理&nbsp;&nbsp; <span style="font-size:14px;color: red">星号为必填</span></h3>
<p>
<form action="" method="post" class="form-horizontal bat_form" enctype="multipart/form-data" >
	<button type="button" class="bat_add btn btn-md btn-warning">批量添加100个</button>
</form>
</p>
<form action="<?php  echo web_url('dummy',array('op'=>'add'));?>" method="post" class="form-horizontal" enctype="multipart/form-data" >

	<table class="table" style="width:40%;" align="left">
		<tbody>
			<tr>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px"><i>*</i>用户名：</td>
				<td style="width:300px">
					<input name="realname"  type="text" value="" />
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px"><i>*</i>手机号码：</td>
				<td>
					<input name="mobile" type="text"   value="" />
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">email：</td>
				<td>
					<input name="email" type="text"   value="" />
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">头像：</td>
				<td>
					<input name="avatar" type="file"   value="" />
				</td>
			</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button type="submit" class="btn btn-md btn-info">确定创建</button></td>
		</tr>
		</tbody>
	</table>
				
	<div style="clear: both;height: 15px;"></div>
									
				
	</form>
		<h3 class="blue">	<span style="font-size:18px;"><strong>会员总数：<?php echo $total ?></strong></span></h3>
		
		<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;"><input type="checkbox"  value="" class="parent_box"></th>
					<th style="text-align:center;">序号</th>
					<th style="text-align:center;">手机号码</th>
					<th style="text-align:center;">用户名</th>
					<th style="text-align:center;">email</th>
					<th style="text-align:center;">头像</th>
					<th style="text-align:center;">创建时间</th>
					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) {
	 $j =1;
	 foreach($list as $v) { ?>
								<tr class="one_row">
									<td style="width: 50px;">
										<input class='child_box' type="checkbox" name="openid[]" value="<?php echo $v['openid'];?>">
									</td>
									<td  style="width: 50px;"><?php echo $j++;?></td>
									<td class="text-center mobile">
										<?php  echo $v['mobile'];?>
									</td>
										<td class="text-center realname">
												<?php  echo $v['realname'];?>
									</td>
										<td class="text-center email">
											<?php  echo $v['email'];?>
									</td>
									<td class="text-center avatar">
										<?php if(!empty($v['avatar'])) { ?>
										<img src="<?php  echo download_pic($v['avatar'],30,30);?>" width="30" height="30">
			 						　　　<?php } ?>
									</td>
									<td class="text-center">
										<?php  echo date('Y-m-d H:i:s',$v['createtime'])?>
									</td>

									<td class="text-center">
										<a  class="btn btn-xs btn-info edit_member" href="javascript:;" data-openid="<?php echo $v['openid'];?>"><i class="icon-edit"></i>编辑会员</a>&nbsp;
										<a class="btn btn-xs btn-danger" href="<?php  echo web_url('dummy',array('op'=>'delete','openid' => $v['openid']));?>"><i class="icon-edit"></i>删除会员</a>
									</td>
								</tr>
								<?php  } } ?>
  </tbody>
    </table>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form action="" method="post" class="form-horizontal edit_form" enctype="multipart/form-data" >
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">修改用户</h4>
				</div>
				<div class="modal-body" style="overflow: hidden">
						<table class="table" style="width:100%;" align="left">
							<tbody>
							<tr>
								<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px">用户名：</td>
								<td style="width:300px">
									<input name="realname"  type="text" value="" />
								</td>
							</tr>
							<tr>
								<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">手机号码：</td>
								<td>
									<input name="mobile" type="text"   value="" />
								</td>
							</tr>
							<tr>
								<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">email：</td>
								<td>
									<input name="email" type="text"   value="" />
								</td>
							</tr>
							<tr>
								<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">头像：</td>
								<td>
									<div style="height: 30px;margin-bottom: 10px;display: none" class="show_pic">
										<img src="" height="30" width="30" class="this_pic">
									</div>
									<input type="hidden" name="hide_avatar" value="">
									<input name="avatar" type="file"   value="" />
								</td>
							</tr>
							</tbody>
						</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="submit" class="btn btn-primary">提交更改</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
		</form>
	</div>

<script>
	$(".edit_member").click(function(){
		var openid = $(this).data('openid');
		var url = "<?php  echo web_url('dummy',array('op'=>'edit'));?>";
		url = url + '&openid='+openid;
		$("#myModal").modal('show');
		$(".edit_form").attr('action',url);
		var name = $(this).closest('.one_row').find(".realname").html();
		var mobile = $(this).closest('.one_row').find(".mobile").html();
		var email = $(this).closest('.one_row').find(".email").html();
		var avatar = $(this).closest('.one_row').find(".avatar img").attr('src');
		$(".edit_form input[name='realname']").val($.trim(name));
		$(".edit_form input[name='mobile']").val($.trim(mobile));
		$(".edit_form input[name='email']").val($.trim(email));
		$(".edit_form input[name='hide_avatar']").val($.trim(avatar));
		if($.trim(avatar) != ''){
				$(".show_pic").show();
				$(".this_pic").attr('src',$.trim(avatar));
		}
	})

	$(".bat_add").click(function(){
		if(confirm('确定批量添加')){
			var url = "<?php  echo web_url('dummy',array('op'=>'addbat'));?>";
			$(".bat_form").attr('action',url);
			$(".bat_form").submit();
		}
	})
</script>
		<?php  echo $pager;?>
<?php  include page('footer');?>