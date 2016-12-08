<?php defined('SYSTEM_IN') or exit('Access Denied');
       if ( empty( $member ) ){
             $member=get_member_account(false);
		     $member=member_get($member['openid']);
	       	 $openid =$memberi['openid'] ;
	    }
		$is_login=is_login_account();
		$article_foot = getArticle(4,1);
		$shop_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_menu')." where menu_type='fansindex' and type = 1 order by torder desc" );	
		$advtop = mysqld_select("select * from " . table('shop_adv') . " where enabled=1 and type = 1 and page = 4 order by displayorder desc");
		if ( empty($category) ){
				$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC");
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][$row['id']] = $row;
						unset($category[$index]);
					}
				}
		}
        $category = index_c_goods($category,4);

		if ( !function_exists(getHottpoic) ){
	       if (file_exists(WEB_ROOT . '/includes/hottpoic.func.php')) {
               require WEB_ROOT . '/includes/hottpoic.func.php';
           }
	    }
        $hot = getHottpoic(0);

?>

<!--[if lt IE 9]>
<div class="m-browserupdate">
<p>您的浏览器该退休啦！为了您的购物安全，觅海建议您升级浏览器：<a onclick="window._dapush('_trackEvent', '浏览器升级提示', '点击', 'chrome')" class="w-icn-14" target="_blank" href="http://mm.bst.126.net/download/ChromeSetup.exe" rel="nofollow">chrome浏览器</a>，<a onclick="window._dapush('_trackEvent', '浏览器升级提示', '点击', 'firefox')" class="w-icn-14 w-icn-14-2" target="_blank" href="http://www.firefox.com.cn/" rel="nofollow">火狐浏览器</a> 或 <a onclick="window._dapush('_trackEvent', '浏览器升级提示', '点击', 'IE')" class="w-icn-14 w-icn-14-3" target="_blank" href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie" rel="nofollow">最新IE浏览器</a></p>
</div>
<![endif]-->
<script type="text/javascript">
            var index = 0;
			$(document).ready(function(){
               if ( index == 0)
               {
				$('.f_category').mouseenter(function(){
					$('.catitmlst').show();
				});
				$('.f_category').mouseleave(function(){
					$('.catitmlst').mouseenter(function(){
						$('.catitmlst').show();
					});			
				});
				$('.f_category').mouseleave(function(){
					$('.catitmlst').hide();
				});
				$('.catitmlst').mouseleave(function(){
	                $('.catitmlst').hide();
				});
			   }
			});
timer_length = 200; // Milliseconds
border_opacity = false; // Use opacity on borders of rounded-corner elements? Note: This causes antialiasing issues


// supportsVml() borrowed from http://stackoverflow.com/questions/654112/how-do-you-detect-support-for-vml-or-svg-in-a-browser
function supportsVml() {
	if (typeof supportsVml.supported == "undefined") {
		var a = document.body.appendChild(document.createElement('div'));
		a.innerHTML = '<v:shape id="vml_flag1" adj="1" />';
		var b = a.firstChild;
		b.style.behavior = "url(#default#VML)";
		supportsVml.supported = b ? typeof b.adj == "object": true;
		a.parentNode.removeChild(a);
	}
	return supportsVml.supported
}


// findPos() borrowed from http://www.quirksmode.org/js/findpos.html
function findPos(obj) {
	var curleft = curtop = 0;

	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}

	return({
		'x': curleft,
		'y': curtop
	});
}

function createBoxShadow(element, vml_parent) {
	var style = element.currentStyle['iecss3-box-shadow'] || element.currentStyle['-moz-box-shadow'] || element.currentStyle['-webkit-box-shadow'] || element.currentStyle['box-shadow'] || '';
	var match = style.match(/^(\d+)px (\d+)px (\d+)px/);
	if (!match) { return(false); }


	var shadow = document.createElement('v:roundrect');
	shadow.userAttrs = {
		'x': parseInt(RegExp.$1 || 0),
		'y': parseInt(RegExp.$2 || 0),
		'radius': parseInt(RegExp.$3 || 0) / 2
	};
	shadow.position_offset = {
		'y': (0 - vml_parent.pos_ieCSS3.y - shadow.userAttrs.radius + shadow.userAttrs.y),
		'x': (0 - vml_parent.pos_ieCSS3.x - shadow.userAttrs.radius + shadow.userAttrs.x)
	};
	shadow.size_offset = {
		'width': 0,
		'height': 0
	};
	shadow.arcsize = element.arcSize +'px';
	shadow.style.display = 'block';
	shadow.style.position = 'absolute';
	shadow.style.top = (element.pos_ieCSS3.y + shadow.position_offset.y) +'px';
	shadow.style.left = (element.pos_ieCSS3.x + shadow.position_offset.x) +'px';
	shadow.style.width = element.offsetWidth +'px';
	shadow.style.height = element.offsetHeight +'px';
	shadow.style.antialias = true;
	shadow.className = 'vml_box_shadow';
	shadow.style.zIndex = element.zIndex - 1;
	shadow.style.filter = 'progid:DXImageTransform.Microsoft.Blur(pixelRadius='+ shadow.userAttrs.radius +',makeShadow=true,shadowOpacity='+ element.opacity +')';

	element.parentNode.appendChild(shadow);
	//element.parentNode.insertBefore(shadow, element.element);

	// For window resizing
	element.vml.push(shadow);

	return(true);
}

function createBorderRect(element, vml_parent) {
	if (isNaN(element.borderRadius)) { return(false); }

	element.style.background = 'transparent';
	element.style.borderColor = 'transparent';

	var rect = document.createElement('v:roundrect');
	rect.position_offset = {
		'y': (0.5 * element.strokeWeight) - vml_parent.pos_ieCSS3.y,
		'x': (0.5 * element.strokeWeight) - vml_parent.pos_ieCSS3.x
	};
	rect.size_offset = {
		'width': 0 - element.strokeWeight,
		'height': 0 - element.strokeWeight
	};
	rect.arcsize = element.arcSize +'px';
	rect.strokeColor = element.strokeColor;
	rect.strokeWeight = element.strokeWeight +'px';
	rect.stroked = element.stroked;
	rect.className = 'vml_border_radius';
	rect.style.display = 'block';
	rect.style.position = 'absolute';
	rect.style.top = (element.pos_ieCSS3.y + rect.position_offset.y) +'px';
	rect.style.left = (element.pos_ieCSS3.x + rect.position_offset.x) +'px';
	rect.style.width = (element.offsetWidth + rect.size_offset.width) +'px';
	rect.style.height = (element.offsetHeight + rect.size_offset.height) +'px';
	rect.style.antialias = true;
	rect.style.zIndex = element.zIndex - 1;

	if (border_opacity && (element.opacity < 1)) {
		rect.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity='+ parseFloat(element.opacity * 100) +')';
	}

	var fill = document.createElement('v:fill');
	fill.color = element.fillColor;
	fill.src = element.fillSrc;
	fill.className = 'vml_border_radius_fill';
	fill.type = 'tile';
	fill.opacity = element.opacity;

	// Hack: IE6 doesn't support transparent borders, use padding to offset original element
	isIE6 = /msie|MSIE 6/.test(navigator.userAgent);
	if (isIE6 && (element.strokeWeight > 0)) {
		element.style.borderStyle = 'none';
		element.style.paddingTop = parseInt(element.currentStyle.paddingTop || 0) + element.strokeWeight;
		element.style.paddingBottom = parseInt(element.currentStyle.paddingBottom || 0) + element.strokeWeight;
	}

	rect.appendChild(fill);
	element.parentNode.appendChild(rect);
	//element.parentNode.insertBefore(rect, element.element);

	// For window resizing
	element.vml.push(rect);

	return(true);
}

function createTextShadow(element, vml_parent) {
	if (!element.textShadow) { return(false); }

	var match = element.textShadow.match(/^(\d+)px (\d+)px (\d+)px (#?\w+)/);
	if (!match) { return(false); }


	//var shadow = document.createElement('span');
	var shadow = element.cloneNode(true);
	var radius = parseInt(RegExp.$3 || 0);
	shadow.userAttrs = {
		'x': parseInt(RegExp.$1 || 0) - (radius),
		'y': parseInt(RegExp.$2 || 0) - (radius),
		'radius': radius / 2,
		'color': (RegExp.$4 || '#000')
	};
	shadow.position_offset = {
		'y': (0 - vml_parent.pos_ieCSS3.y + shadow.userAttrs.y),
		'x': (0 - vml_parent.pos_ieCSS3.x + shadow.userAttrs.x)
	};
	shadow.size_offset = {
		'width': 0,
		'height': 0
	};
	shadow.style.color = shadow.userAttrs.color;
	shadow.style.position = 'absolute';
	shadow.style.top = (element.pos_ieCSS3.y + shadow.position_offset.y) +'px';
	shadow.style.left = (element.pos_ieCSS3.x + shadow.position_offset.x) +'px';
	shadow.style.antialias = true;
	shadow.style.behavior = null;
	shadow.className = 'ieCSS3_text_shadow';
	shadow.innerHTML = element.innerHTML;
	// For some reason it only looks right with opacity at 75%
	shadow.style.filter = '\
		progid:DXImageTransform.Microsoft.Alpha(Opacity=75)\
		progid:DXImageTransform.Microsoft.Blur(pixelRadius='+ shadow.userAttrs.radius +',makeShadow=false,shadowOpacity=100)\
	';

	var clone = element.cloneNode(true);
	clone.position_offset = {
		'y': (0 - vml_parent.pos_ieCSS3.y),
		'x': (0 - vml_parent.pos_ieCSS3.x)
	};
	clone.size_offset = {
		'width': 0,
		'height': 0
	};
	clone.style.behavior = null;
	clone.style.position = 'absolute';
	clone.style.top = (element.pos_ieCSS3.y + clone.position_offset.y) +'px';
	clone.style.left = (element.pos_ieCSS3.x + clone.position_offset.x) +'px';
	clone.className = 'ieCSS3_text_shadow';


	element.parentNode.appendChild(shadow);
	element.parentNode.appendChild(clone);

	element.style.visibility = 'hidden';

	// For window resizing
	element.vml.push(clone);
	element.vml.push(shadow);

	return(true);
}

function ondocumentready(classID) {
	if (!supportsVml()) { return(false); }

  if (this.className.match(classID)) { return(false); }
	this.className = this.className.concat(' ', classID);

	// Add a namespace for VML (IE8 requires it)
	if (!document.namespaces.v) { document.namespaces.add("v", "urn:schemas-microsoft-com:vml"); }

	// Check to see if we've run once before on this page
	if (typeof(window.ieCSS3) == 'undefined') {
		// Create global ieCSS3 object
		window.ieCSS3 = {
			'vmlified_elements': new Array(),
			'update_timer': setInterval(updatePositionAndSize, timer_length)
		};

		if (typeof(window.onresize) == 'function') { window.ieCSS3.previous_onresize = window.onresize; }

		// Attach window resize event
		window.onresize = updatePositionAndSize;
	}


	// These attrs are for the script and have no meaning to the browser:
	this.borderRadius = parseInt(this.currentStyle['iecss3-border-radius'] ||
	                             this.currentStyle['-moz-border-radius'] ||
	                             this.currentStyle['-webkit-border-radius'] ||
	                             this.currentStyle['border-radius'] ||
	                             this.currentStyle['-khtml-border-radius']);
	this.arcSize = Math.min(this.borderRadius / Math.min(this.offsetWidth, this.offsetHeight), 1);
	this.fillColor = this.currentStyle.backgroundColor;
	this.fillSrc = this.currentStyle.backgroundImage.replace(/^url\("(.+)"\)$/, '$1');
	this.strokeColor = this.currentStyle.borderColor;
	this.strokeWeight = parseInt(this.currentStyle.borderWidth);
	this.stroked = 'true';
	if (isNaN(this.strokeWeight) || (this.strokeWeight == 0)) {
		this.strokeWeight = 0;
		this.strokeColor = fillColor;
		this.stroked = 'false';
	}
	this.opacity = parseFloat(this.currentStyle.opacity || 1);
	this.textShadow = this.currentStyle['text-shadow'];

	this.element.vml = new Array();
	this.zIndex = parseInt(this.currentStyle.zIndex);
	if (isNaN(this.zIndex)) { this.zIndex = 0; }

	// Find which element provides position:relative for the target element (default to BODY)
	vml_parent = this;
	var limit = 100, i = 0;
	do {
		vml_parent = vml_parent.parentElement;
		i++;
		if (i >= limit) { return(false); }
	} while ((typeof(vml_parent) != 'undefined') && (vml_parent.currentStyle.position != 'relative') && (vml_parent.tagName != 'BODY'));

	vml_parent.pos_ieCSS3 = findPos(vml_parent);
	this.pos_ieCSS3 = findPos(this);

	var rv1 = createBoxShadow(this, vml_parent);
	var rv2 = createBorderRect(this, vml_parent);
	var rv3 = createTextShadow(this, vml_parent);
	if (rv1 || rv2 || rv3) { window.ieCSS3.vmlified_elements.push(this.element); }

	if (typeof(vml_parent.document.ieCSS3_stylesheet) == 'undefined') {
		vml_parent.document.ieCSS3_stylesheet = vml_parent.document.createStyleSheet();
		vml_parent.document.ieCSS3_stylesheet.addRule("v\\:roundrect", "behavior: url(#default#VML)");
		vml_parent.document.ieCSS3_stylesheet.addRule("v\\:fill", "behavior: url(#default#VML)");
		// Compatibility with IE7.js
		vml_parent.document.ieCSS3_stylesheet.ie7 = true;
	}
}

function updatePositionAndSize() {
	if (typeof(window.ieCSS3.vmlified_elements) != 'object') { return(false); }

	for (var i in window.ieCSS3.vmlified_elements) {
		var el = window.ieCSS3.vmlified_elements[i];

		if (typeof(el.vml) != 'object') { continue; }

		for (var z in el.vml) {
			//var parent_pos = findPos(el.vml[z].parentNode);
			var new_pos = findPos(el);
			new_pos.x = (new_pos.x + el.vml[z].position_offset.x) + 'px';
			new_pos.y = (new_pos.y + el.vml[z].position_offset.y) + 'px';
			if (el.vml[z].style.left != new_pos.x) { el.vml[z].style.left = new_pos.x; }
			if (el.vml[z].style.top != new_pos.y) { el.vml[z].style.top = new_pos.y; }

			var new_size = {
				'width': parseInt(el.offsetWidth + el.vml[z].size_offset.width),
				'height': parseInt(el.offsetHeight + el.vml[z].size_offset.height)
			}
			if (el.vml[z].offsetWidth != new_size.width) { el.vml[z].style.width = new_size.width +'px'; }
			if (el.vml[z].offsetHeight != new_size.height) { el.vml[z].style.height = new_size.height +'px'; }
		}
	}

	if (event && (event.type == 'resize') && typeof(window.ieCSS3.previous_onresize) == 'function') { window.ieCSS3.previous_onresize(); }
}
</script>
<script type="text/javascript">
// 对浏览器的UserAgent进行正则匹配，不含有微信独有标识的则为其他浏览器
var useragent = navigator.userAgent;
/*if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
    // 这里警告框会阻塞当前页面继续加载
    alert('已禁止本次访问：您必须用微信打开本页面！');
    // 以下代码是用javascript强行关闭当前页面
    var opened = window.open('about:blank', '_self');
    opened.opener = null;
    opened.close();
}
*/
$(document).ready(function($) {
		$("#submit").click(function() {
			if ($("#search_word").val()) {
				$("#searchForm").submit();
			} else {
				alert("请输入关键词！");
				return false;
			}
		});
	});

</script>
<div class="navtop" style="display:none;padding:0;background:none;height:100px;">
      <a href="<?php echo $advtop['link'];?>" target="_blank" ><img src="<?php echo $advtop['thumb'];?>" height="100"/></a>
</div>
<div class="navtop">
   <div class="center">
       <div class="le"> 
            您好，欢迎来到觅海环球购，关注微信公众号更多优惠随时查看！
	   </div>
	   <div class="re">
	          <a href="<?php echo mobile_url('merchant',array('name'=>'shopwap')) ?>" target="_blank">商家入驻</a>
		  <?php 
             if ( !empty($member['mobile']) ){
		  ?>
               <a href="<?php  echo mobile_url('logout',array('name'=>'shopwap')); ?>">安全退出</a>
			   <a href="<?php  echo mobile_url('fansindex',array('name'=>'shopwap')); ?>" target="_blank" >个人中心</a>
			<?php
                }else{
			?>
                <a href="<?php  echo mobile_url('login',array('name'=>'shopwap')); ?>">登录</a>
				<a href="<?php  echo mobile_url('regedit',array('name'=>'shopwap')); ?>" target="_blank" >注册</a>
			<?php
			}
			?>
			<a href="<?php  echo mobile_url('mycart',array('name'=>'shopwap')); ?>" style="background:#E83729;color:#fff;padding:0 10px;" target="_blank" >购物车</a>
			  <a href="javascript:void(0)" class="weixin" style="position:relative;z-index:1000;">关注公众微信号
			<div class="weixins" style="z-index:1000;position:absolute;right:0;top:60px;width:100%;height:60px;background:#fff;">
			    <img src="images/weixin.jpg" width="100%" style="z-index:1000;float:right;"/>
			</div>
		  </a>
	   </div>
   </div>
</div>
<div class="nav" style="overflow: visible;height: 104px;box-sizing: border-box;">
    <a href="index.php" style="margin:0 60px 0 0;float:left;"><img src="<?php echo $cfg['shop_logo']; ?>" height="60" /></a>
    <div class="WX_search1" id="mallHead" >
		  <form class="WX_search_frm1" action="index.php" id="searchForm"
			name="searchForm">
			<input type="hidden" name="mod" value="mobile" /><input
				type="hidden" name="op" value="dish" /> <input
				type="hidden" name="do" value="goodlist" /> <input type="hidden"
				name="name" value="shopwap" /> <input name="keyword"
				id="search_word" class="WX_search_txt hd_search_txt_null"
				placeholder="请输入商品名进行搜索！" ptag="37080.5.2" type="search"
				AUTOCOMPLETE="off"/>
			<div class="WX_me">
				<a href="javascript:;" id="submit" class="WX_search_btn_blue">搜索</a>
			</div>
		   </form>
		
		   <ul class="keyword">
		        <?php foreach($hot as $keyword){ ?>
                     <li><a href="<?php echo $keyword['url']; ?>"><?php echo $keyword['name']; ?></a></li>
				<?php } ?>
		   </ul>
		   
		    <!--搜索框的相关搜索数据-->
		   <ul id="search-related" class="srelated">

		   </ul>
	   </div>
</div>
<nav class="topTabbox" style="position: relative;z-index: 100;">
    <div class="nav2" style="padding:0;height:40px;background:none;">
     <ul style="float:right;">
	 <li class="f_category">
	 <div class="lineicon"><i></i><i></i><i></i></div><span>所有分类</span>
	 </li>
    <?php foreach($shop_menu_list as $value){ ?>
         <li><a href="<?php echo $value['url']; ?>" target="_blank" ><?php echo $value['tname']; ?></a></li>
	<?php }?>
	</ul>
    <ul class="catitmlst j-catmenu">
    <?php foreach($category as $value){ ?>
         <li style="height: 40px;">
		        <a href="<?php  echo mobile_url('goodlist', array('name'=>'shopwap','pcate' => $value['id'],'op'=>'dish')); ?>" target="_blank" >
				<img class="icon" src="<?php echo $value['thumb']; ?>">
				<span class="t"><?php echo $value['name']; ?></span></A>
				<i class="icon-angle-right"></i>
				<em class="vcenter"></em>
				<em class="seg"></em>
				<div class="m-ctgcard f-cb"></div>
				
				<div class="c_category">
					<ul class="c2_category">
					<?php if (is_array($children[$value['id']])){ foreach( $children[$value['id']] as $c_value ){ ?>
						  <li><a href="<?php  echo mobile_url('goodlist', array('name'=>'shopwap','p2' => $c_value['id'],'op'=>'dish')); ?>" target="_blank" ><img  src="<?php echo $c_value['thumb']; ?>" height="40"><?php echo $c_value['name']; ?></a></li>
					<?php }} ?>
					</ul>
					<div class="c2_bander">
                              <?php if (is_array($value['best'])){ $count =0 ;foreach( $value['best'] as $b_value ){ $count++; if ($count <=2){ ?>
						             <div style="text-align:center;"><a href="<?php  echo mobile_url('detail', array('id' => $b_value['id'],'op'=>'dish','name'=>'shopwap')); ?>"><img  src="<?php echo $b_value['img']; ?>" width="185px"></a></div>
					          <?php }} }?>
					</div>
				</div>
         </li>
	<?php }?>
	 
</ul> 
     <div class="topimg" style="position:absolute;right:150px;bottom:0;">
          <a href="<?php echo $advtop['link'];?>" target="_blank" ><img  src="<?php echo $advtop['thumb'];?>" /></a>
	</div>
	</div>
</nav>

<!--以下是搜索框的控制-->
<script>
	
	var timeout = 0;  //用来延迟请求服务器	
	
	//输入框内容改变，就调用check（）
	$("#search_word").on("input propertychange",function(){
		clearTimeout(timeout);		
		timeout = setTimeout(function(){				
			check();					
		},800);
		
	})
	

	//点击空白处，相关搜索消失，但是点击每个相关搜索，不消失
	document.onclick = function(e){
		var event = e || window.event;
		var ele = event.srcElement || event.target;
		if(ele.className != "per-srelated"){
			$("#search-related").hide();
		}
			
	}
	//如果输入框有输入，且相关数据不为空时显示
	function check(){
//		$("#search-related").html('');  加了这句，使得输入时会出现闪动
		getrelated();
		if($("#search_word").val() == ""){
			$("#search-related").hide();
		}else if($(".srelated li").length != 0){
			$("#search-related").show();
		}
	}
	
	 
	//获取相关数据
	function getrelated(){		
		var svalue = $("#search_word").val();   //获取搜索框的输入内容
		var url = "<?php echo mobile_url('search',array('op'=>'ajax_keyword'));?>";
		$.post(url, {'keyword' : svalue}, function(s) {			
		   //没有相关数据时，不显示                           
	       if(s.errno == 1002 ){
	       	    $("#search-related").hide();
	       }else{
			   var list = s.message;
			   var html = '';
			   for(var i=0;i<list.length;i++){
				   html +="<li class='per-srelated'>"+list[i].title+"</li>"
			   }
	       	    $("#search-related").html(html);
	       	    $("#search-related").show();
	       }
		}, 'json');
         
	}
	
	//点击相关搜索的li，将li的内容显示在搜索框内,并且收起相关
	$(document).delegate('.srelated li','click',function(){
		var val = $(this).text();
		$("#search_word").val(val);
		$("#search-related").hide();
	})
	
</script>
