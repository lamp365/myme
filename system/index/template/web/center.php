<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1"/>
<title>首页</title>

<link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/bootstrap3/css/bootstrap.min.css" />   
<link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/index/css/c.css" />   
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/fontawesome3/css/font-awesome.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/third-party/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<style type="text/css">
    .payment-amount{
        width: 100%;
        height: 300px;
    }
    .payment-amount-area{
        float: left;
        width: 49%;
        margin-right: 1%;
        overflow: hidden;
    }
    .number-area{
        float: left;
        width: 49%;
        margin-top: 60px;
    }
    .number-area ul{
        float: left;
        width: 50%;
    }
    .number-area img{
        width: 50px;
        height: 50px;
    }
    .payment-amount-area-left{
        float: left;
        overflow: hidden;
        width: 25%;
    }
    .payment-amount-area ul{
        width: 75%;
        float: left;
        overflow: hidden;
        font-size: 13px;
        line-height: 1.5;
        text-align: left;
    }
    .payment-amount-area li{
        float: left;
        overflow: hidden;
        width: 25%;
        padding-left: 2%;
        box-sizing: border-box;
    }
    .payment-left{
        float: left;
        width: 8%;
        min-width: 50px;
        text-align: right;
    }
    .payment-left img{
        max-width: 50px;
        height: 50px;
    }
    .payment-right{
        float: left;
        width: 70%;
        text-align: left;
        padding-left: 5%;
        color: #000;
        box-sizing: border-box;
        font-size: 14px;
        line-height: 1.5;
    }
    .payment-right div{
        height: 25px;
        line-height: 25px;
    }
    .access_amount{
        color: #000;
        line-height: 1.5;
    }
    .number-area li{
        font-size: 14px;
        margin: 15px 0 0 15px;
        overflow: auto;
    }
    .access-amount-head li,.shop-car-head li{
        float: left;
        margin-right: 7px;
    }
    .product-name-left{
        width: 60px;
        float: left;
    }
    .product-name-right{
        width: auto;
        float: left;
    }
    .main-wrap{
        padding:20px;
    }
    .workbench .today-presentation,.workbench .pending-order{
        box-sizing: initial;
    }
    .access-amount-table{
        margin-top: 20px;
    }
    .product-name-left img{
        width: 60px;
        height: 60px;
    }
    .product-name-time{
        margin-top: 15px;
    }
    .access-amount-table #begintime,.access-amount-table #endtime,.access-amount-table .search-input{
        height: 30px;
        border-radius: 4px;
        border: 1px solid #adadad;
        padding-left: 5px;
    }
    .shop-car-head #shopbegintime,.shop-car-head #shopendtime,.shop-car-head .search-input{
        height: 30px;
        border-radius: 4px;
        border: 1px solid #adadad;
        padding-left: 5px; 
    }
    .access-amount-head ul,.shop-car-head ul{
        overflow: auto;
        margin-bottom: 0;
    }
    .number-area i{
        width: 20px;
        text-align: center;
        display: inline-block;
    }
    .main-t{
        padding-top: 0;
    }
    .payment-amount-area h3{
        text-align: left;
        padding: 10px 0;
        font-size: 22px;
        font-weight: bold;
    }
    .payment-li-float{
        float: left;
        width: 50%;
    }
</style>
<script type="text/javascript">
	function hiddenall()
{
	 document.getElementById('container').style.display='none';
	   /* document.getElementById('container2').style.display='none';
	   document.getElementById('container3').style.display='none';*/
	
}
$(function () {
    
    $('#container').highcharts({
    	 credits: {
          enabled:false
				},
        chart: {
            type: 'column'
        },
        title: {
            text: '本周订单统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}￥</b>'
        },
        series: [{
            name: 'Population',  
             color: 'rgba(126,86,134,.9)',
            data: [
        		<?php  $index=0?>
            	<?php  if(is_array($chartdata1)) { foreach($chartdata1 as $item) { ?>
                ['<?php  echo $item['dates'];?>', <?php  echo $item['counts'];?>],	
          <?php  $index++?>
                	<?php  } } ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
    /*
    
      $('#container2').highcharts({
    	 credits: {
          enabled:false
				},
        chart: {
            type: 'column'
        },
        title: {
            text: '本月订单统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}￥</b>'
        },
        series: [{
            name: 'Population',  
             color: 'rgba(126,86,134,.9)',
            data: [
        		<?php  $index=0?>
            	<?php  if(is_array($chartdata2)) { foreach($chartdata2 as $item) { ?>
                ['<?php  echo $item['dates'];?>', <?php  echo $item['counts'];?>],	
          <?php  $index++?>
                	<?php  } } ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
    
    
    
      $('#container3').highcharts({
    	 credits: {
          enabled:false
				},
        chart: {
            type: 'column'
        },
        title: {
            text: '本年订单统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}￥</b>'
        },
        series: [{
            name: 'Population',  
             color: 'rgba(126,86,134,.9)',
            data: [
        		<?php  $index=0?>
            	<?php  if(is_array($chartdata3)) { foreach($chartdata3 as $item) { ?>
                ['<?php  echo $item['dates'];?>', <?php  echo $item['counts'];?>],	
          <?php  $index++?>
                	<?php  } } ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });*/
	  hiddenall();
	  document.getElementById('container').style.display='block';
      //支付金额初始化
    Highcharts.setOptions({
        colors: ['#ff426d', '#27b9e5', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
    $('#paymentAmount').highcharts({
        credits: {
            enabled:false
        },
        title: {
            text: '24小时累计图',
            x: -20 //center
        },
        subtitle: {
            text: '24小时累计图',
            x: -20
        },
        chart: {
            borderColor: '#f0f0f0',
            borderWidth: 1,
            type: 'line'
        },
        xAxis: {
            categories: ['0h', '6h', '12h', '18h', '24h']
        },
        yAxis: {
            title: {
                text: '支付金额 (K)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#ddd'
            }]
        },
        tooltip: {
            valueSuffix: 'k'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: '今日',
            data: [7.0, 6.9, 9.5, 14.5, 18.2]
        }, {
            name: '昨日',
            data: [1, 0.8, 5.7, 11.3, 17.0]
        }]
    });


});

		</script>
</head>
 <body onload="myheight()">
<div class="main-wrap">
			

	<div class="workbench">
		<!--begin map-->
	
		<!--end map-->
		<!--begin main-->
        <div style="width: 100%;overflow: hidden;">
            <div class="payment-amount-area">
                <h3>支付金额统计</h3>
                <div class="payment-amount" id="paymentAmount"></div>
            </div>
            <div class="number-area" style="margin-left: 1%">
                <ul>
                    <li style="overflow: hidden;">
                        <div class="payment-left">
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/payment_amount.png">
                        </div>
                        <div class="access_amount payment-right">
                            <div>支付金额</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-apple"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-android"></i>
                            <span>456789</span>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-desktop"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-mobile-phone"></i>
                            <span>456789</span>
                        </div>
                    </li>
                </ul>
                <ul>
                    <li style="overflow: hidden;">
                        <div class="payment-left">
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/access_amount.png">
                        </div>
                        <div class="access_amount payment-right">
                            <div>访客数量</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-apple"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-android"></i>
                            <span>456789</span>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-desktop"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-mobile-phone"></i>
                            <span>456789</span>
                        </div>
                    </li>
                </ul>
                <ul>
                    <li style="overflow: hidden;">
                        <div class="payment-left">
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/page_views.png">
                        </div>
                        <div class="access_amount payment-right">
                            <div>浏览量</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-apple"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-android"></i>
                            <span>456789</span>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-desktop"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-mobile-phone"></i>
                            <span>456789</span>
                        </div>
                    </li>
                </ul>
                <ul>
                    <li style="overflow: hidden;">
                        <div class="payment-left">
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/buyers_num.png">
                        </div>
                        <div class="access_amount payment-right">
                            <div>支付买家数</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-apple"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-android"></i>
                            <span>456789</span>
                        </div>
                    </li>
                    <li>
                        <div class="payment-li-float">
                            <i class="icon-desktop"></i>
                            <span>123</span>
                        </div>
                        <div class="payment-li-float">
                            <i class="icon-mobile-phone"></i>
                            <span>456789</span>
                        </div>
                    </li>
                </ul>
              
            </div>
        </div>
        <div class="panel panel-default access-amount-table">
            <!-- Default panel contents -->
            <div class="panel-heading access-amount-head">
                <ul>
                    <li><span class="btn btn-default btn-sm">最近7日</span></li>
                    <li><span class="btn btn-default btn-sm">最近30日</span></li>
                    <li><input name="begintime" class="begintime" id="begintime" type="text" value="" readonly="readonly" placeholder="开始时间" /></li>
                    <li style="line-height: 30px;">至</li>
                    <li><input name="endtime" class="endtime" id="endtime" type="text" value="" readonly="readonly" placeholder="结束时间"/></li>
                    <li><input class="search-input" type="text" name="" value="" placeholder="请输入商品名称或ID"></li>
                    <li><span class="btn btn-primary btn-sm search-btn">搜 索</span></li>
                </ul>
            </div>
            <!-- Table -->
            <table class="table">
                <tr>
                    <th>商品名称</th>
                    <th>浏览量</th>
                    <th>访客数</th>
                    <th>支付金额</th>
                    <th>支付买家数</th>
                    <th>支付转化率</th>
                    <th>操作</th>
                </tr>
                <tr class="access-amount-html">
                    <td>
                        <div class="product-name-left"><img src="http://hinrc.com/attachment/jpg/2016/08/534379437927161.jpg"></div>
                        <div class="product-name-right">
                            <div>ON欧普特蒙一水肌酸纯肌酸粉600g健身增健肌粉肌肉爆发补充能量抗疲劳</div>
                            <div class="product-name-time">发布时间2016-01-03 18:16:06</div>
                        </div>
                    </td>
                    <td>12</td>
                    <td>12</td>
                    <td>12</td>
                    <td>12</td>
                    <td>12%</td>
                    <td><a href="javascript:;">查看详情</a></td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading shop-car-head">
                <ul>
                    <li style="line-height: 30px;">购物车数据：</li>
                    <li><input name="begintime" id="shopbegintime" type="text" value="" readonly="readonly" placeholder="开始时间" /></li>
                    <li style="line-height: 30px;">至</li>
                    <li><input name="endtime" id="shopendtime" type="text" value="" readonly="readonly" placeholder="结束时间"/></li>
                    <li><input class="search-input" type="text" name="" value="" placeholder="请输入商品名称或ID"></li>
                    <li><span class="btn btn-primary btn-sm search-btn">查 询</span></li>
                </ul>
            </div>
            <!-- Table -->
            <table class="table">
                <tr>
                    <th>宝贝名称</th>
                    <th>加入购物车人数</th>
                    <th>收藏人数</th>
                </tr>
                <tr class="shop-car-html">
                    <td>
                        ON欧普特蒙一水肌酸纯肌酸粉600g健身增健肌粉肌肉爆发补充能量抗疲劳
                    </td>
                    <td>12</td>
                    <td>12</td>
                </tr>
            </table>
        </div>
		<div class="main-t clearfix" style="min-height:200px;">
			<div class="work-bench-r" >
		        <div class="pending-order">
		            <dl>
		                <dt><span class="title">待处理订单</span></dt>
		                <dd><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 1))?>">待发货：<?php echo $needsend_count ?>笔</a>￥<?php echo $needsend__price ?></dd>
		                <dd><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -4))?>">退换货：<?php echo $returnofgoods_count ?>笔</a>￥<?php echo $returnofgoods_price ?></dd>
		               <dd><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -2))?>">退款单：<?php echo $returnofmoney_count ?>笔</a>￥<?php echo $returnofmoney_price ?></dd>
		              
		            </dl>
		        </div>
		    </div>
		    <div class="work-bench-l" >
		        <!--begin 今日简报-->
		        <div class="today-presentation">
		            <dl>
		                <dt>
		                    <span class="totay-1">今日简报</span>
		                    <span class="totay-2">订单</span>
		                    <span class="totay-3">订单金额</span>
		                    <span class="totay-4">已退货单</span>
		                    <span class="totay-5">已退货金额</span>
		                </dt>
		                <dd>
		                    <span class="totay-1">今日</span>
		                    <span class="totay-2"><?php echo $todayordercount ?>笔</span>
		                    <span class="totay-3">￥<?php echo $todayorderprice ?></span>
		                    <span class="totay-4"><?php echo $todayordercount_re ?>笔</span>
		                    <span class="totay-5">￥<?php echo $todayorderprice_re ?></span>
		                </dd>
		                <dd>
		                    <span class="totay-1">本月</span>
		                    <span class="totay-2"><?php echo $monthordercount ?>笔</span>
		                    <span class="totay-3">￥<?php echo $monthorderprice ?></span>
		                    <span class="totay-4"><?php echo $monthordercount_re ?>笔</span>
		                    <span class="totay-5">￥<?php echo $monthorderprice_re ?></span>
		                </dd>
		                <dd>
		                    <span class="totay-1">本年</span>
		                    <span class="totay-2"><?php echo $yearordercount ?>笔</span>
		                    <span class="totay-3">￥<?php echo $yearorderprice ?></span>
		                    <span class="totay-4"><?php echo $yearordercount_re ?>笔</span>
		                    <span class="totay-5">￥<?php echo $yearorderprice_re ?></span>
		                </dd>
		            </dl>
		        </div>
		        <!--end 今日简报-->		        
		    </div>
			<!--begin 业务简报-->
		        <div class="business-presentation" >
		        	<dl>
		                <dt class="briefreporttab"><span class="title">业务简报</span>
		               <!-- 	<span class="briefreporttab-radios">
		                		
			                	<input type="radio" name="dateSegment" value="4" onclick="if(this.checked){hiddenall();document.getElementById('container').style.display='block';}" checked/>周
			                	<input type="radio" name="dateSegment" value="4"  onclick="if(this.checked){hiddenall();document.getElementById('container2').style.display='block';}" />月
			                	<input type="radio" name="dateSegment" value="6"  onclick="if(this.checked){hiddenall();document.getElementById('container3').style.display='block';}" />年
		                	</span>-->
			           
						</dt>
	                </dl>
	                <div class="order-unit">订货金额（元）</div>
	                <div id="container" style="width:98%;height:230px; margin: 0 auto"></div>
	               <!--   <div id="container2" style="width:98%;height:230px; margin: 0 auto"></div>
	               <div id="container3" style="width:98%;height:230px; margin: 0 auto"></div>-->
		        </div>
		        <!--end 业务简报-->
		</div>
    </div>
</div>
     <?php  include page('footer');?>
     <script type="text/javascript">
    function myheight(){
        var myheight1 = $(".main-wrap").height()+120;
        $("#main",window.parent.document).height(myheight1);
    }
    laydate({
        elem: '#begintime',
        istime: true, 
        event: 'click',
        format: 'YYYY-MM-DD hh:mm:ss',
        istoday: true, //是否显示今天
        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
    });
    laydate({
        elem: '#endtime',
        istime: true, 
        event: 'click',
        format: 'YYYY-MM-DD hh:mm:ss',
        istoday: true, //是否显示今天
        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
    });
    laydate({
        elem: '#shopbegintime',
        istime: true, 
        event: 'click',
        format: 'YYYY-MM-DD hh:mm:ss',
        istoday: true, //是否显示今天
        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
    });
    laydate({
        elem: '#shopendtime',
        istime: true, 
        event: 'click',
        format: 'YYYY-MM-DD hh:mm:ss',
        istoday: true, //是否显示今天
        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
    });
    laydate.skin("molv"); 
    //搜索查询ajax请求
    function searchBtn(){
        $(".access-amount-head .search-btn").on("click",function(){
            var begintime = $("#begintime").val();
            var endtime = $("#endtime").val();
            var search_input = $(".access-amount-head .search-input").val();
            var access_amount_html = "";
            $.post("",{},function(data){
                if(data.errno==200){
                    $(".access-amount-html").html("");
                    access_amount_html += "<td><div class='product-name-left'><img src='http://hinrc.com/attachment/jpg/2016/08/534379437927161.jpg'></div>"+
            "<div class='product-name-right'><div>ON欧普特蒙一水肌酸纯肌酸粉600g健身增健肌粉肌肉爆发补充能量抗疲劳</div><div class='product-name-time'>"+
            "发布时间2016-01-03 18:16:06</div></div></td><td>12</td><td>12</td><td>12</td><td>12</td><td>12%</td><td><a href='javascript:;'>查看详情</a></td>"
                }else{
                    alert(data.errno);
                }
                $(".access-amount-html").html(access_amount_html);
            },"json")
        });
        $(".shop-car-head .search-btn").on("click",function(){
            var begintime = $("#shopbegintime").val();
            var endtime = $("#shopendtime").val();
            var search_input = $(".shop-car-head .search-input").val();
            var shop_car_html = "";
            $.post("",{},function(data){
                if(data.errno==200){
                    $(".shop-car-html").html("");
                    shop_car_html += "<td>ON欧普特蒙一水肌酸纯肌酸粉600g健身增健肌粉肌肉爆发补充能量抗疲劳</td><td>12</td><td>12</td></tr>"
                }else{
                    alert(data.errno);
                }
                $(".shop-car-html").html(shop_car_html);
            },"json")
        });
    }
    searchBtn();
     </script>
     </body>
</html>