<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
  <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/global.css" media="all">

<body style="padding:10px;">
<!--商品搜索区域-->
<form class="layui-form layui-form-pane" action="" method="post">
  <div class="layui-form-item">
    <label class="layui-form-label">商品名称</label>
    <div class="layui-input-inline">
        <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入商品名称" class="layui-input" value="<?php echo $_GP['title'];?>">
    </div>
    <label class="layui-form-label">商品分类</label>
    <div class="layui-input-inline">
      <select name="oneCategory" lay-filter="oneCategory">
        <option value="">--请选择分类--</option>
        <?php
            foreach($dishCategoryData as $v){
                $selected = $_GP['oneCategory'] == $v['id']?'selected':'';
                echo "<option value='{$v['id']}' {$selected}>{$v['name']}</option>";
            }
        ?>
      </select>
    </div>
    
    <div class="layui-input-inline" id="twoClass">
        
        <?php
          if($_GP['twoCategory'] > 0)
          {
              $selectStr = '<select name="twoCategory" lay-filter="twoCategory">
          <option value="">--请选择分类--</option>';
              foreach($dishCategoryTwoData as $v)
              {
                  $selected = $_GP['twoCategory'] == $v['id']?'selected':'';
                  $selectStr .= "<option value='{$v['id']}' {$selected}>{$v['name']}</option>";
              }
               $selectStr .= '</select>';
               echo $selectStr;
          }
          
        ?>
    </div>
    
    <label class="layui-form-label">上架/下架</label>
    <div class="layui-input-inline">
      <select name="status" lay-filter="band">
        <option value="-1" <?php if($_GP['status'] ==-1 || $_GP['status']==null){ echo "selected";} ?>>--查看所有--</option>
          <option value="1" <?php if($_GP['status'] ==1){ echo "selected";} ?>>--已上架--</option>
          <option value="0" <?php if($_GP['status'] ==0 && $_GP['status']!=null){ echo "selected";} ?>>--已下架--</option>
      </select>
    </div>
    
    
    <label class="layui-form-label">限时购</label>
    <div class="layui-input-inline">
      <select name="is_ltc" lay-filter="band">
        <option value="0"   <?php if($_GP['is_ltc'] ==0){ echo "selected";} ?>>--查看所有--</option>
          <option value="1" <?php if($_GP['is_ltc'] ==1){ echo "selected";} ?>>--参与--</option>
          <option value="2" <?php if($_GP['is_ltc'] ==2){ echo "selected";} ?>>--不参与--</option>
      </select>
    </div>
    
    
    <div class="layui-inline">
        <button class="layui-btn" lay-submit="" lay-filter="demo1">搜索</button>
    </div>
  </div>
  <div class="layui-form-item">
    <!--<div class="layui-inline" style="margin-right: 6px;" >
    <label class="layui-form-label">销售价格</label>
      <div class="layui-input-inline" style="width: 100px;">
        <input type="text" name="price_min" placeholder="￥" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 100px;margin-right:0">
        <input type="text" name="price_max" placeholder="￥" autocomplete="off" class="layui-input">
      </div>
    </div>-->


    
  </div>

</form>
<!-- tab切换开始 -->
  <div class="layui-tab layui-tab-card">
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
          <div class="product-table">
            <table class="layui-table">
              <thead>
                <tr>
                  <th width="80">商品图片</th>
                  <th>商品名称</th>
                  <th>商品价格</th>
                  <th>库存</th>
                  <th>佣金</th>
                  <th>新品</th>
                  <th>推荐</th>
                  <th>排序</th>
                  <th>操作</th>
                </tr> 
              </thead>
              <tbody>
                <?php
                  foreach($list as $v){
                ?>  
                <tr>
                    <td class="product-img"><img src="<?php echo download_pic($v['thumb'],150);?>"></td>
                  <td><?php echo $v['title'];?></td>
                  <td><?php echo FormatMoney($v['marketprice'],0);?></td>
                  <td><?php echo $v['store_count'];?></td>
                  <td><?php 
                        echo ($v['marketprice']/100)*($v['commision']/100);
                      ?>元</td>
                  <td>
                      <?php
                        if($v['isnew']){
                             echo "<span class='layui-btn layui-btn-small layui-btn-warm' onclick='javascript:changeIsNew({$v['id']});' id='isnewobj_{$v['id']}' data-id='{$v['id']}'>是</span>";
                        }else{
                             echo "<span class='layui-btn layui-btn-small layui-btn-danger' onclick='javascript:changeIsNew({$v['id']});' id='isnewobj_{$v['id']}' data-id='{$v['id']}'>否</span>";
                        }
                      ?>
                      <input type="hidden" name="isnew_<?php echo $v['id'];?>" id="isnew_<?php echo $v['id'];?>" value="<?php echo $v['isnewc'];?>" >
                  </td>
                  <td>
                      <?php
                      if($v['isrecommand']){
                          echo "<span class='layui-btn layui-btn-small layui-btn-warm' onclick='javascript:isRecommand({$v['id']});' id='isrecommandobj_{$v['id']}' data-id='{$v['id']}'>是</span>";
                      }else{
                          echo "<span class='layui-btn layui-btn-small layui-btn-danger' onclick='javascript:isRecommand({$v['id']});' id='isrecommandobj_{$v['id']}' data-id='{$v['id']}'>否</span>";
                      }
                      ?>
                      <input type="hidden" name="isrecommand_<?php echo $v['id'];?>" id="isrecommand_<?php echo $v['id'];?>" value="<?php echo $v['isrecommandc'];?>" >
                  </td>
                  <td>
                      <div class="layui-input-inline" style="width: 70px;text-align: center">
                          <input type="number" name="sort" placeholder="排序" autocomplete="off" class="layui-input" value="<?php echo $v['sort']; ?>" onblur="javascript:changeSort(<?php echo $v['id'];?>,this);">
                      </div>
                  </td>
                  <td>
                      <a class="layui-btn layui-btn-small" href="<?php echo mobile_url('product',array('op'=>'editproduct','dish_id'=>$v['id'])); ?>">编辑</a>
                      <?php if($v['status']){ ?>
                      <div class="layui-btn layui-btn-small layui-btn-danger" onclick='javascript:isStatus(<?php echo $v['id'];?>);'  id='isstatusobj_<?php echo $v['id'];?>' data-id="<?php echo $v['id'];?>">设置下架</div>
                      <?php }else{ ?>
                      <div class="layui-btn layui-btn-small layui-btn-warm"  onclick='javascript:isStatus(<?php echo $v['id'];?>);'   id='isstatusobj_<?php echo $v['id'];?>' data-id="<?php echo $v['id'];?>">设置上架</div>
                      <?php } ?>
                      <input type="hidden" name="isstatus_<?php echo $v['id'];?>" id="isstatus_<?php echo $v['id'];?>" value="<?php echo $v['isstatusc'];?>" >
                      <?php
                        if(empty($v['active_info']) && $v['status'] == 1)
                        {
                      ?>
                      <div class="layui-btn layui-btn-small layui-btn-warm addltc" data-id="<?php echo $v['id'];?>">参与限时购</div>
                      <?php
                        }
                        elseif ($v['status'] == 1){
                      ?>
                      <div class="layui-btn layui-btn-small layui-btn-warm editltc" data-id="<?php echo $v['active_info']['ac_dish_id'];?>">编辑限时购</div>
                      <div class="layui-btn layui-btn-small layui-btn-warm delltc" data-id="<?php echo $v['active_info']['ac_dish_id'];?>">限时购下架</div>
                      <?php
                        }
                      ?>
                  </td>
                </tr>
              <?php
                  }
              ?>
                
              </tbody>
            </table>
          </div> 
          <div id="demo1"><!-- 分页的div -->
              <?php echo $pager;?>
          </div>
      </div>
        <!--
        <div class="layui-tab-item">
            
        </div>
        <div class="layui-tab-item">
            
        </div>
        <div class="layui-tab-item">
            
        </div>-->
    </div>
  </div>
<!-- tab切换结束 -->

<input type="hidden" name="total" id="total" value="<?php echo $total;?>">       
<div id="alterModal" class="alertModalBox"></div>
<script src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
layui.use(['laypage', 'layer','form','element'], function(){
  var $ = layui.jquery, form = layui.form();
  element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
  //全选
  form.on('checkbox(allChoose)', function(data){
    var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
    child.each(function(index, item){
      item.checked = data.elem.checked;
    });
    form.render('checkbox');
  });

  var laypage = layui.laypage,layer = layui.layer;
  
  layui.use('form', function(){
    var form = layui.form();
    form.on('select(oneCategory)', function(data){
        var val = parseInt(data.value);
        //var weburl = '/seller/product/parCategory.html';
        var weburl = "<?php echo mobile_url('product',array('op'=>'parCategory')); ?>";
        var twoCategoryHtml = '';
        $.post(weburl,{'pid':val},function(data){
            twoCategoryHtml = '<select name="twoCategory" lay-filter="twoCategory"><option value="">--请选择分类--</option>';
            for(var i in data){
                if (data.hasOwnProperty(i)) { //filter,只输出man的私有属性
                    //console.log(i,":",data[i]);
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['id'] +'">'+ data[i]['name'] +'</option>';
                };
            }
            twoCategoryHtml = twoCategoryHtml + '</select>';
            $('#twoClass').html(twoCategoryHtml);
            form.render();
        },"json");
    });
  });
  //加入限时购
  $('.addltc').on('click',function(){
      var idval = $(this).attr('data-id');
      var url= '<?php echo mobile_url('product',array('op'=>'addLtc')) ?>';
      $.ajaxLoad(url,{id:idval},function(){
          $('#alterModal').modal('show');
      });
  });
  //编辑限时购
  $('.editltc').on('click',function(){
      
      var idval = $(this).attr('data-id');
      var url= '<?php echo mobile_url('product',array('op'=>'addLtc')) ?>';
      $.ajaxLoad(url,{ac_dish_id:idval},function(){
          $('#alterModal').modal('show');
      });
  });
  
  //删除限时购
  $('.delltc').on('click',function(){
      var idval = $(this).attr('data-id');
      var url= '<?php echo mobile_url('product',array('op'=>'delLtc')) ?>';
      $.post(url,{'ac_dish_id':idval},function(redata){
          if(redata == 1){
        	  layer.open({
        		  content: '下架成功',
              offset: ['28%', '40%'],
    	  		  end: function () {
      	  			location.reload();//刷新
            	  }
        		});
          }else{
        	  layer.msg('下架失败');
          }
      },"json");
      
  });
});

function changeIsNew(id){
    //ar weburl = '/seller/product/changeDishIsNew.html';
    var data = $('#isnew_'+id).val();
    var weburl = "<?php echo mobile_url('product',array('op'=>'changeDishIsNew')); ?>";
    $.post(weburl,{'dish_id':id,'isnew':data},function(redata){
        if(data == 1)
        {
            $('#isnewobj_'+id).attr("class","layui-btn layui-btn-small layui-btn-warm");
            $('#isnewobj_'+id).html('是');
            $('#isnew_'+id).val(0);
        }
        else{
            $('#isnewobj_'+id).attr("class","layui-btn layui-btn-small layui-btn-danger");
            $('#isnewobj_'+id).html('否');
            $('#isnew_'+id).val(1);
        }
    },"json");
}

function isRecommand(id){
    //var weburl = '/seller/product/changeDishRecommand.html';
    var data = $('#isrecommand_'+id).val();
    var weburl = "<?php echo mobile_url('product',array('op'=>'changeDishRecommand')); ?>";
    $.post(weburl,{'dish_id':id,'isrecommand':data},function(redata){
        if(data == 1)
        {
            $("#isrecommandobj_"+id).attr("class","layui-btn layui-btn-small layui-btn-warm");
            $('#isrecommandobj_'+id).html('是');
            $('#isrecommand_'+id).val(0);
        }
        else{
            $("#isrecommandobj_"+id).attr("class","layui-btn layui-btn-small layui-btn-danger");
            $('#isrecommandobj_'+id).html('否');
            $('#isrecommand_'+id).val(1);
        }
    },"json");
}

function isStatus(id){
    //var weburl = '/seller/product/changeDishStatus.html';
    var data = $('#isstatus_'+id).val();
    var weburl = "<?php echo mobile_url('product',array('op'=>'changeDishStatus')); ?>";
    $.post(weburl,{'dish_id':id,'status':data},function(redata){
        if(redata.errno == 1){
        	layer.open({
        		  content: redata.message,
              offset: ['28%', '40%'],
      	  		  end: function () {
      	  			location.reload();//刷新
            	  }
        		});
            }else{
            	layer.open({
            		  content: redata.message,
                  offset: ['28%', '40%']
            		});
            }
    },"json");  
}

function changeSort(id,data){
    //var weburl = '/seller/product/upChangeOrder.html';
    var weburl = "<?php echo mobile_url('product',array('op'=>'upChangeOrder')); ?>";
    $.post(weburl,{'dish_id':id,'sort':$(data).val()},function(redata){
        //location.reload();
    },"json");
}
</script>

</body>
</html>