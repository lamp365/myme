<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.field {
		max-height: 320px;
		overflow: hidden;
	}
	.field>div {
		border: 1px solid #e5e5e5;
		border-radius: 3px;
		overflow-y: auto;
		max-height: 300px;
	}
	.field .tit {
		padding-left: 15px;
		background: #F9F9F9;
		border-bottom: 1px solid #e5e5e5;
		height: 28px;
		line-height: 28px;
	}
	.field p {
		border-bottom: 1px solid #e5e5e5;
		height: 28px;
		line-height: 28px;
		padding-left: 15px;
		margin-bottom: 0px;
		cursor: pointer;
	}
	.z_none {
		height: 28px;
		line-height: 28px;
		padding-left: 15px;
	}
	.modal-title span{
		color: red;
	}
</style>

<ul id="myTab" class="nav nav-tabs">
	<li class="active">
		<a href="#admin" data-toggle="tab">
			管理员身份
		</a>
	</li>
	<li>
		<a href="#home" data-toggle="tab">
			会员身份
		</a>
	</li>
</ul>
<div id="myTabContent" class="tab-content">
	<div class="tab-pane fade in active" id="admin">
		<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">管理员身份列表</h3> <a  class="btn btn-primary add_rolers" href="javascript:;" >新增管理员身份</a>
		<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
			<thead >
			<tr>
				<th style="text-align:center;min-width:150px;">身份名称</th>
				<th style="text-align:center;min-width:150px;">创建时间</th>
				<th style="text-align:center; min-width:60px;">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($rolers)) { foreach($rolers as $item) { ?>
				<tr class="one_row">
					<td style="text-align:center;" class="one_name"><span><?php  echo $item['name'];?></span></td>
					<td style="text-align:center;">
						<?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
					<td style="text-align:center;">
<!--						<a class="btn btn-xs btn-info show_user"  data-id="--><?php //echo $item['id'];?><!--" href="javascript:;"><i class="icon-edit"></i>管理用户</a>-->
						&nbsp;&nbsp;
						<a class="btn btn-xs btn-info edit_name" data-id="<?php echo $item['id'];?>" href="javascript:;"><i class="icon-edit"></i>修改名字</a>&nbsp;&nbsp;
						<?php if($item['isdelete'] == 1){ ?>
							<a class="btn btn-xs btn-danger" href="<?php  echo web_url('user', array('op'=>'deleterolers','id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
						<?php } ?>
					</td>
				</tr>
			<?php  } } ?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane fade" id="home">
		<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">会员身份列表</h3>
		<a class="btn btn-primary add_purchase_rolers" href="javascript:;">新增会员身份</a>
		<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
			<thead>
			<tr>
				<th style="text-align:center;min-width:150px;">身份名称</th>
				<th style="text-align:center;min-width:150px;">所属类型</th>
				<th style="text-align:center;min-width:150px;">创建时间</th>
				<th style="text-align:center; min-width:60px;">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($purchase)) { foreach($purchase as $item) { ?>
				<tr class="one_row">
					<td style="text-align:left;" class="one_name"><strong><span><?php  echo $item['name'];?></span></strong>[父级栏目]</td>
					<td style="text-align:center;">
						<?php if($item['type'] == 2) echo '渠道商'; ?>
					</td>
					<td style="text-align:center;">
						<?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
					<td style="text-align:center;">
						<a class="btn btn-xs btn-info edit_name" data-id="<?php echo $item['id'];?>" href="javascript:;"><i class="icon-edit"></i>修改名字</a>&nbsp;&nbsp;
						<?php if($item['pid'] != 0){ ?>
							<a class="btn btn-xs btn-danger" href="<?php  echo web_url('user', array('op'=>'deleterolers','id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
						<?php } ?>
					</td>
				</tr>
				<?php if(is_array($childrens[$item['id']])){ ?>
				<?php foreach($childrens[$item['id']] as $child){ ?>
						<tr class="one_row">
							<td style="text-align:left;padding-left: 30px;" class="one_name">|---<span><?php  echo $child['name'];?></span></td>
							<td style="text-align:center;">
								<?php if($child['type'] == 2) echo '渠道商'; ?>
							</td>
							<td style="text-align:center;">
								<?php  echo date('Y-m-d H:i:s', $child['createtime'])?></td>
							<td style="text-align:center;">
								<a class="btn btn-xs btn-info edit_name" data-id="<?php echo $child['id'];?>" href="javascript:;"><i class="icon-edit"></i>修改名字</a>&nbsp;&nbsp;
								<?php if($child['pid'] != 0){ ?>
									<a class="btn btn-xs btn-danger" href="<?php  echo web_url('user', array('op'=>'deleterolers','id' => $child['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
								<?php } ?>
							</td>
						</tr>
				<?php } ?>
				<?php } ?>
			<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>



<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form action="" method="post">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">修改名称</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="name">名称</label>
					<input type="text" class="form-control" name="rolers_name" id="edit_name" placeholder="请输入名称">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				<button type="submit" class="btn btn-primary">确认修改</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
	</form>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form action="<?php  echo create_url('site', array('name' => 'user','do' => 'user','op' => 'addrolers'))?>" method="post">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">添加管理员身份</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name">名称</label>
						<input type="text" class="form-control" name="rolers_name"  placeholder="请输入名称">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="submit" class="btn btn-primary">确认添加</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
	</form>
</div>

<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form action="" method="post">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">管理用户 (<span></span>)</h4>
			</div>
			<div class="modal-body">
				<div class="field">
					<div class="pull-left" style="width: 45%">
						<div class="tit">未分配管理员</div>
						<?php
							if(!empty($users)){
								foreach($users as $item){
									echo "<p data-uid='{$item['id']}'>{$item['username']}</p>";
								}
							}else{
								echo '<div class="z_none">暂无</div>';
							}
						?>
					</div>
					<div class="pull-right" style="width: 45%">
						<div class="tit">已关联管理员</div>
						<div class="z_none">暂无</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_modal">关闭</button>
				<button type="submit" class="btn btn-primary sure_add_users">确认添加</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
	</form>
</div>

<div class="modal fade" id="add_purchase_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form action="<?php  echo create_url('site', array('name' => 'user','do' => 'user','op' => 'add_purchase_rolers'))?>" method="post">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">添加会员身份</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name">身份类型</label>
						<select name="type" class="form-control" onchange="showCate(this)">
							<option value="0">请选择类型</option>
							<option value="2">渠道商</option>
						</select>
					</div>
					<div class="form-group allcate" style="display: none">
						<label for="name">所属分类</label>
						<select name="pid" id="pid" class="form-control">
						</select>
					</div>
					<div class="form-group">
						<label for="name">名称</label>
						<input type="text" class="form-control" name="rolers_name"  placeholder="请输入名称">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="submit" class="btn btn-primary">确认添加</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
	</form>
</div>

<script>
	$(".edit_name").click(function(){
		var id = $(this).data('id');
		$("#editModal").modal('show');
		var name = $(this).closest(".one_row").find(".one_name span").html();
		name     = $.trim(name);
		var url = "<?php  echo web_url('user', array('op'=>'changerolers'))?>";
		url = url +"&id="+id;
		$("#editModal form").attr('action',url);
		$("#edit_name").val(name);
	})
	$(".add_rolers").click(function(){
		$("#addModal").modal('show');
	})

	$(".show_user").click(function(){
		var url = "<?php  echo web_url('user', array('op'=>'showuser'))?>";
		var id = $(this).data('id');
		url = url +"&id="+id;
		var html = '';
		$.getJSON(url,function(data){
			var obj = data.message;
			console.log(obj);
			if(obj.length > 0){
				for(var i= 0;i< obj.length;i++){
					var info = obj[i];
					 html += "<p data-uid='"+ info.id +"'>"+ info.username + "</p><input type='hidden' name='uids[]' value='"+ info.id +"'>";
				}
				$(".field .pull-right .z_none").remove();
				$(".field .pull-right").append(html);
			}
		},'json');
		var name = $(this).closest(".one_row").find(".one_name").html();
		$("#showModal").modal('show');
		var url = "<?php  echo web_url('user', array('op'=>'add_rolers_relation'))?>";
		url = url +"&id="+id;
		$("#showModal form").attr('action',url);
		$("#showModal .modal-title span").html($.trim(name));
	})

	$(document).delegate(".field .pull-left p","click",function(){
		var uid = $(this).data('uid');
		var name = $(this).html();
		var isContinue = true;
		$(".field .pull-right p").each(function(){   //已经存在的不用再次添加
			var name2 = $(this).html();
			if($.trim(name)== $.trim(name2)){
				isContinue = false;
			}
		});
		if(!isContinue){
			return;
		}
		var html = "<p data-uid='"+ uid +"'>"+ name + "</p><input type='hidden' name='uids[]' value='"+ uid +"'>";
		$(".field .pull-right .z_none").remove();
		$(html).appendTo($(".field .pull-right"));
	})

	$(".close_modal").click(function(){
		$(".field .pull-right p").remove();
		$(".field .pull-right input").remove();
		$(".field .pull-right").append('<div class="z_none">暂无</div>');

		$(".field .pull-left p").each(function(){
			if($(this).data('mark') == 'del'){
				$(this).remove();
			}
		});
		$("#showModal").modal('hide');
	})

	$(document).delegate(".field .pull-right p","click",function(){
		var uid = $(this).data('uid');
		var name = $(this).html();
		var isContinue = true;
		$(".field .pull-left p").each(function(){   //已经存在的不用再次添加
			var name2 = $(this).html();
			if($.trim(name)== $.trim(name2)){
				isContinue = false;
			}
		});
		if(isContinue){
			var html = "<p data-mark='del' data-uid='"+ uid +"'>"+ name + "</p>";
			$(".field .pull-right .z_none").remove();
			$(html).appendTo($(".field .pull-left"));
		}


		$(this).next().remove();
		$(this).remove();
	})

	$(".add_purchase_rolers").click(function(){
		$("#add_purchase_Modal").modal('show');
	})

	function showCate(obj){
		var type = $(obj).val();
		if(type == 0){
			//隐藏掉分类
			$("#pid").html('');
			$(".allcate").hide();
		}else{
			//查找该类型的分类数据
			var url ="<?php echo web_url('user',array('op'=>'rolercate'));?>";
			var url = url+"&type="+type;
			$.getJSON(url,function(data){
				$(".allcate").show();
				if(data.errno == 200){
					var obj = data.message;
					var html = "<option value='"+obj.id+"'>"+ obj.name +"</option>";
				}else{
					var html = "<option value='0'>顶级分类</option>";
				}
				$("#pid").html(html);
			},'json');
		}
	}

</script>
<?php  include page('footer');?>
