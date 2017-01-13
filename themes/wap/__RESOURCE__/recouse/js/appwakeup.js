//url是获取 app版本下载地址的路劲
//首先唤醒app，唤醒不起来则加入提示条，引导下载。并且点击提示条可以下载app
function appWakeUp(url,isshow_down_tip){
    //先唤醒app
    app_wake_to_up();

    if(isshow_down_tip){   //是否显示提醒下载 提示条
        //显示下载提示条
        app_show_tip();
        document.getElementById("appdownloadlink").onclick = function(){
            //点击则下载app
            app_click_to_down(url);
        };
    }

}

//点击下载app版本
function appDownLoad(url){
    //先唤醒app

    var ua2 = navigator.userAgent.toLowerCase();
    if( ua2.match(/MicroMessenger/i) == 'micromessenger'){
        app_click_to_down(url);
    }else{
        app_wake_to_up();
        //点击则下载app
        setTimeout(function(){
            app_click_to_down(url)
        },300);     
    }
}


function app_wake_to_up(){
    //将URL参数以&分割成一个数组
    var url_param = window.location.search.split("&");
    console.log("url_param="+url_param);
    var i = 0,j = 0,k = 0;
    var regex = /id=|do=/;
    var regex2 = /id|do/;
    var regex3 = /^[0-9]*$/;
    var param_arr1 = "";
    var param_arr2 = "";
    var param = [];
    var param_result = "";
    var last_param_result = "";
    var android_param = "";
    var AndroidAgreement = "";
    //在参数数组中提取id 和 do类型
    for( i ; i < url_param.length; i++ ){
        if(url_param[i].search(regex)==0){
            param_arr1 += url_param[i]+"=";
        }
    }
    console.log("param_arr1="+param_arr1);
    param_arr2 = param_arr1.split("=");
    //将参数拼接成type = id的形式 例如article=18
    for( j ; j < param_arr2.length ; j++ ){
        if( param_arr2[j].search(regex2)==-1 && param_arr2[j]!=""){
            param.push(param_arr2[j]);
        }
    }
    console.log("param_arr2="+param_arr2);
    //如何参数第一个是数字就将数组逆序
    if(regex3.test(param[0])){
        param.reverse()
    }
    console.log("param="+param);
    if( param[0] == "detail" ){
        AndroidAgreement ="mihai://prodetail.hinrc.com/openwith?"+"dish_id="+param[1];
    }else if( param[0] == "article" ){
        AndroidAgreement ="mihai://articledetail.hinrc.com/openwith?"+"article_id="+param[1];
    }
    
    for( k ; k < param.length; k++){
       param_result += param[k]+"=";
    }
    console.log("param_result="+param_result);
    last_param_result = param_result.substr(0,param_result.length-1);
    console.log("last_param_result="+last_param_result);

    var iPhoneAgreement = "mihaiweb://"+last_param_result;

    //IOS
    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
        //if(navigator.userAgent.indexOf("Safari") ==-1){
            console.log("iPhoneAgreement="+iPhoneAgreement);
            window.location.href = iPhoneAgreement;
       // }
    }else if(navigator.userAgent.match(/android/i)){
        alert(AndroidAgreement);
        window.location.href = AndroidAgreement;
    }
}
function app_click_to_down(url){
    var ua = navigator.userAgent.toLowerCase();
    var iPhoneUrl = "";
    var AndroidUrl = "";
    var Apply_AndroidUrl = "";
    var Apply_iPhoneUrl = "";

    var last_url = "";
    $.ajax({
        url:url,
        type: "POST",
        async: false,
        dataType:'json',
        success:function(response,xml){
            if(response.errno==200){
                var obj = response.message;
                iPhoneUrl = obj.iPhoneUrl;
                AndroidUrl = obj.AndroidUrl;
                Apply_iPhoneUrl = obj.Apply_iPhoneUrl;
                Apply_AndroidUrl = obj.Apply_AndroidUrl;
                if( ua.match(/MicroMessenger/i) == 'micromessenger'){
                    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                       last_url = Apply_iPhoneUrl; 
                    }else if(navigator.userAgent.match(/android/i)){
                       last_url = Apply_AndroidUrl;  
                    }
                }else if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                    last_url = iPhoneUrl;
                }else if(navigator.userAgent.match(/android/i)){
                    last_url = AndroidUrl;
                }
            }
            
        }
    });
    window.location.href = last_url;
}
/**
 * 每个内容页显示一条提示下载app
 */
function app_show_tip(){
    var div = document.createElement("div");
    var img = document.createElement("img");
    var wx_nav    = document.getElementsByClassName("wx_nav")[0];
    var foot_menu = document.getElementsByClassName("foot_menu")[0];
    var divlink = document.createElement("div");
    var close_div = document.createElement("div");
    if(wx_nav){
        div.className = "appdownload-hasfooter2";
    }else if(foot_menu){
        div.className = "appdownload-hasfooter";
    }else{
        div.className = "appdownload-nofooter";
    }
    div.id = "appdownload";
    divlink.id = "appdownloadlink";
    close_div.id = "closeLoad";
    img.src ="./images/down_banner.png";
    div.appendChild(img);
    div.appendChild(divlink);
    div.appendChild(close_div);
    document.body.appendChild(div);
    var appdownload_div = document.getElementById("appdownload");
    document.getElementById("closeLoad").onclick = function(){
        document.body.removeChild(appdownload_div);
    }
}

/**
 * 评论以及购买商品时提示 引导下载
 * @param url
 * @param tip
 */
function tipUserToDown(url,tip){
    /**
     <div id="downapp_box"></div>
     <div id="downapp">
     <p class='show_tip'>此活动商品必须下载APP才能购买哦</p>
     <p class='down_btn'>
        <span class='next_down'>下次下载</span>
        <span class='liji_down'>立即下载</span>
     </p>
     </div>
     */
    if(tip == 1){
        var show = '要下载APP才可以评论以及看到更多奇趣的东西哦~';
    }else if(tip ==2 ){
        var show = '此活动商品必须下载APP才能购买哦~';
    }

    var div_box = document.getElementById("downapp_box");
    var div_nei = document.getElementById("downapp");

    if(div_box ===  null){
        var div_box = document.createElement("div");
        var div_nei = document.createElement("div");
        div_box.id    = 'downapp_box';
        div_nei.id    = 'downapp';

        var p1 = "<p class='show_tip'>"+ show +"</p>";
        var p1 = p1 + " <p class='down_btn'> " +
            "<span class='next_down' onclick='appdown_to_next()'>我再想想</span>"+
            "<span class='liji_down' onclick=\"app_click_to_down('"+ url +"')\" >火速下载</span>"+
            "</p>";

        document.body.appendChild(div_box);
        document.body.appendChild(div_nei);
        var appdownload_div = document.getElementById("downapp");
        appdownload_div.innerHTML = p1;
    }else{
        div_box.style.display = '';
        div_nei.style.display = '';
    }

}

function appdown_to_next(){
    var div_box = document.getElementById("downapp_box");
    var div_nei = document.getElementById("downapp");
    div_box.style.display = 'none';
    div_nei.style.display = 'none';
}
