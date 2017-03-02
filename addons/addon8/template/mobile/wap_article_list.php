<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/swiper-3.3.1.min.css"/>
	<link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"  type="text/css" />
	<link rel='stylesheet' type='text/css' href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>

	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/swiper.js"></script>	

</head>

<style type="text/css">

	/*健康文化*/
	.healthy{
		width: 100%;
		overflow: hidden;
		background: #EDEDED;
		padding: 5%;
		box-sizing: border-box;
	}
	.healthy ul{
		width: 100%;
		overflow: hidden;
	}
	.healthy ul li{
		width: 100%;
		padding: 3%;
		box-sizing: border-box;
		border-radius: 5px;
		background: #fff;
		border: none;
		margin-bottom: 3%;
		position: relative;
	}

	.healthy ul li a img{
		width: 100%;
	}
	.healthy ul li p{
		color: #333;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		padding: 10px 5px 0 0px;
		font-size: 14px !important;
		font-weight: bold;
	}
	.healthy ul li .healthy-data{
		width: 48px;
		position: absolute;
		top: 0;
		right:0;
	}
	.healthy ul li span{
		position: absolute;
		top: 8px;
		right:12px;
		width: 30px;
		font-size: 8px;
		color: #fff;
		display: inline-block;
	}

	/*晒物笔记*/	
	div.wrap{
		width: 100%;								
		position: relative;
		background:rgb(244,244,244);
	}
	div.wrap div{				
		border: 1px solid #eee;		
		box-shadow: 0 0 3px 0 rgba(0,0,0,0.2);
		position: absolute;
		background: #fff;
		padding-bottom: 1%;
		box-sizing: border-box;
		width: 47%;
	}
	div.wrap div.newnote{
		visibility: hidden;
	}
	div.wrap div p{
		padding:3%;
		box-sizing: border-box;
		line-height: 18px;
	}
	div.wrap div p.title{
		color: #333;
		font-size: 14px;
		font-weight: bold;
	}
	div.wrap div p.detail{
		color: #7F7F7F;
		font-size: 12px;
		height: 74px;
		overflow: hidden;
	}	
	div.wrap div p.men img{
		width: 30px;
		height: 30px;
		border-radius: 50%;
	}
	div.wrap div p.men span{
		color: #7F7F7F;
		font-size: 10px;
		line-height: 30px;
		width: 75%;
		display: inline-block;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	div.wrap div h3 {
		line-height: 35px;
	}
	div.wrap div img {
		width: 100%;
	}

	/*wap的觅海头条二级页*/
	.mhnews-wap{
		width: 100%;
	}
	.mhnews-wap .mhheadline {
		background: #fff;
	}

	.header_title .art_active{
		color:#FF2D4B;
	}
	.header_title a{
		width: 30%;
		display: inline-block;
		font-family: "微软雅黑";
	}
	.mhheadline h3{
		background: #eee;height: 10px;
	}	
	.mhheadline ul li{
		padding:4%;
		width: 100%;
		overflow: hidden;
		box-sizing: border-box;
		border-bottom: solid 1px #eee;
	}
	.mhheadline ul li .content{
		width: 85%;
		float: right;
	}
	.mhheadline ul li .content .name{
		color: #333;
		font-size: 10px;
		width: 100%;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.mhheadline ul li .content .title{
		color: #333;
		font-weight: bold;
		font-family: "微软雅黑";
		font-size: 14px;		
	}
	.mhheadline ul li .content .detail{
		color: #7F7F7F;
		font-size: 12px;
		line-height: 20px;
		height: 40px;
		overflow: hidden;
		margin-top: 5px;
	}
	/*头像*/	
	.mhheadline .men{
		width: 15%;
		overflow: hidden;
		float: left;		
	}
	.mhheadline .men img{
		width: 40px;
		height: 40px;
		border-radius: 50%;
	}	
	/*文章图片*/
	.mhheadline ul li .imglist{
		width: 100%;		
		overflow: hidden;
		padding:2% 0 0 15%;
		box-sizing: border-box;
	}
	.mhheadline ul li .imglist .imgone{
		width: 100%;
		border: solid 1px #f8f8f8;
		box-sizing: border-box;
	}
	.mhheadline ul li .imglist .imgtwo{
		width: 49%;	
		border: solid 1px #f8f8f8;
		box-sizing: border-box;		
	}
	.mhheadline ul li .imglist .imgthree{
		width: 32%;
		border: solid 1px #f8f8f8;
		box-sizing: border-box;
	}
	/*热门视频*/
	.hot-video {
		width: 100%;
		overflow: hidden;
		padding: 10px 0 10px 0;
	}
	.hot-video ul{
		width: 100%;

	}	 
	.hot-video ul li{
		width: 33%;
		overflow: hidden;
		float: left;
		list-style: none;
		text-align:center;
		margin-left:3%;
		padding: 0;
		
	}
	.hot-video ul li img{
		width: 100%;  
		height:auto;
	   	vertical-align: middle;
	   	border-radius: 4px;
	}
	.hot-video ul li video{
		width: 100%;  
		height:auto;
	   	vertical-align: middle;
	}
	.hot_title{
		padding: 10px 0 0 10px;		
		font-size: 16px;
		font-weight: bold;
		line-height: 32px;
	}
	.hot_title .more-video{
		float: right;
		margin-right: 10px;
		color:#b7b7b7;
		font-size: 14px;
		line-height: 26px;
		font-weight: 400;
	}
	.swiper-slide{
		position: relative;
	}
	.swiper-wrapper .paly-icon{
	    position: absolute;
	    top: 1px;
	    right: 3px;
	    width: 25px;
	    height: 25px;
	}
	.swiper-wrapper .video-num{
    position: absolute;
    left: 6px;
    bottom: 44px;
    background: rgba(0,0,0,.5);
    color: #fff;
    padding: 2px 7px;
    border-radius: 50px;
	}
	.swiper-wrapper .video-num img{
	    float: left;
	    width: 13px;
	    margin-top: 4px;
	}
	.swiper-wrapper .video-num span{
		float: left;
		font-size: 12px;
		    margin-left: 5px;
	}
	.video-title{
		overflow: hidden;
	    text-overflow: ellipsis;
	    display: -webkit-box;
	    -webkit-line-clamp: 2;
	    -webkit-box-orient: vertical;
	    height: 40px;
	}
	.hot_title_small_title{
    padding-left: 4px;
    border-left: 2px solid #ff2741;
    line-height: 23px;
    height: 20px;
    display: inline-block;
	}
	.new-article-list{
		width: 96%;
		margin-left: 4%;
	}
	.new-article-list .list-left{
		width: 35%;
		float: left;
	}
	.new-article-list .list-left img{
		display: block;
		width: 100%;
		max-width: 100%;
		border-radius: 4px;
	}
	.new-article-list .list-right{
		width: 65%;
		float: left;
		    padding-left: 3%;
    box-sizing: border-box;
	}
	.article-title{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    height: 50px;
    font-weight: bold;
    color: #333;
    font-size: 16px;
    font-family: "微软雅黑";
	}
	.article-detail{
		overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    height: 43px;
    margin-top: 5px;
	}
	.mhheadline .new-article-list li{
		padding: 15px 0;
		border-bottom: none;
	}
	.head-banner img{
		width: 100%;
		max-height: 150px;
	}
	/*移动端1px处理*/
@media only screen and (-webkit-min-device-pixel-ratio:2),only screen and (min-device-pixel-ratio:2) {
 .good-content {
	border: none;
	background-image: -webkit-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: -moz-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: -o-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: linear-gradient(0,#eee,#eee 50%,transparent 50%);
	background-size: 100% 1px;
	background-repeat: no-repeat;
	background-position: bottom
	}
}
</style>

<body>
<div style="height: 100%;background: white;position: relative;">
	<div class="head-banner">
		<?php if(empty($banner)){ ?>
		<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201702/20170214130958a29102a4b73.jpg"/>
		<?php }else{ $query = parse_url($banner['link']); $parse_arr = convertUrlQuery($query['query']); ?>
		<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$parse_arr['headline_id']));?>"><img src="<?php echo download_pic($banner['thumb'],430,150,2);?>"/></a>
		<?php } ?>
	</div>
	<div class="top_header" style="border-bottom: none;background: #fff;display: none;">
		<div class="header_title" style="font-size: 16px;line-height: 45px;">
			<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'healty'))?>"  <?php if($_GP['op']=='healty' || empty($_GP['op'])){ echo "class='art_active'"; } ?> >健康文化</a>
			<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline'))?>"  <?php if($_GP['op']=='headline'){ echo "class='art_active'"; } ?>>觅海头条</a>
			<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'note'))?>"  <?php if($_GP['op']=='note'){ echo "class='art_active'"; } ?>>晒物笔记</a>
		</div>
	</div>

	<div class="mhnews-wap">

		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>">
							<div class="list-left">
								<img src="<?php echo download_pic($picarr[0],250,190,2);?>">
							</div>
							<div class="list-right">
								<div class="article-title"><?php echo $row['title'] ;?></div>
								<div class="article-detail"><?php echo $row['preview'] ;?></div>
							</div>
						</a>
					</li>
					<?php } ?>
				</ul>
				
			</div>
	
	</div>
</div>
<!--用来存当前page-->
<input type="hidden" value="2" id="page"/>
<div class="ajax_next_page">
	<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif"/>
	正在加载
</div>
<?php include themePage('footer');?>

</body>
<script>
	var index = 1; //默认开关状态是打开

	$(function(){
		//滚动条到底部时就加载剩下数据
		$(window).scroll(function(){			
			if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
				Refresh();
			}
		})
	})
		
	function Refresh(){
		if( index == 1){
			$(".ajax_next_page").show();
			index = 0; //关闭开关
			var page = $("#page").val(); //第一次传的是2
			$.post("", {'page' : page,'nextpage' : 'ajax','op':'headline'}, function(s){
				if(s.errno != 200){
					//如果没有数据
					$(".ajax_next_page").hide();
				}else{
					$("#page").val(++page);
					var art_data = s.message;
					for(var i = 0;i < art_data.length;i++){
						//循环拼接 html下一页数据
						Load(art_data[i]);
					}
					index = 1;  //加载完后重新打开开关
					$(".ajax_next_page").hide();
				}
			}, 'json');

		}
	}

	//获取URL参数
	function request(paras,url)
	{
		url = decodeURI(url);
		var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
		var paraObj = {}
		for (i=0; j=paraString[i]; i++){
			paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
		}
		var returnValue = paraObj[paras.toLowerCase()];
		if(typeof(returnValue)=="undefined"){
			return "";
		}else{
			return returnValue;
		}
	}

function Load(art_data){	
		//觅海头条的append
		var url = "<?php echo mobile_url('article',array('op'=>'headline'));?>";
		url = url + "&id="+art_data.headline_id;
		var piclist = art_data.pic;
		var perpic = piclist.split(";"); //字符串截取，成为数组
		var picurl = perpic[0];
		var li ='<li class="good-content">'+
					'<a href="'+url+'">'+
						'<div class="list-left">'+
							'<img src="'+ picurl +'"/>'+
						'</div>'+
						'<div class="list-right">'+
							'<div class="article-title">'+art_data.title+'</div>'+
							'<div class="article-detail">'+art_data.preview+'</div>'+
						'</div>'+
					'</a>'+
				'</li>';
		$(".new-article-list").append(li);
	}
	//将后台返回的时间戳格式化为时间格式
	function Stringtotime(time){ 
		time = time *1000; 
	    var datetime = new Date();			    
	    datetime.setTime(time);			      
	    var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1; 			   
	    var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate(); 	      
	    return month + "/" + date;  
	}  
</script>

</html>
