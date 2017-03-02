<?php
/*
member操作
*/
function get_user_identity($mobile=''){
	 $identity = array();
     if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
		$identity = mysqld_select("SELECT * FROM ".table('rolers')." WHERE id = ".$member['son_roler_id']." and pid = ".$member['parent_roler_id']);
     }
	 return $identity;
}
function save_vip_member_login($mobile='', $openid ='' ){
     if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
        if (! empty($member['openid'])) {
            $_SESSION[VIP_MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    
    if (! empty($openid)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
            ':openid' => $openid
        ));
        if (! empty($member['openid'])) {
            $_SESSION[VIP_MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    return '';
}
function save_member_login($mobile = '', $openid = '')
{
    if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
        if (! empty($member['openid'])) {
            $_SESSION[MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    
    if (! empty($openid)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
            ':openid' => $openid
        ));
        if (! empty($member['openid'])) {
            $_SESSION[MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    return '';
}

function member_login_qq($qq_openid)
{
    if (! empty($qq_openid)) {
        $qq_fans = mysqld_select("SELECT * FROM " . table('qq_qqfans') . " WHERE qq_openid=:qq_openid ", array(
            ':qq_openid' => $qq_openid
        ));
        if (! empty($qq_fans['qq_openid'])) {
            $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
                ':openid' => $qq_fans['openid']
            ));
            if (! empty($member['openid'])) {
                $_SESSION[MOBILE_ACCOUNT] = $member;
            } else {
                clearloginfrom();
                header("location:" . create_url('mobile', array(
                    'name' => 'shopwap',
                    'do' => 'regedit',
                    'third_login' => 'true'
                )));
            }
        }
    }
}

function member_login_alipay($alipay_openid)
{
    if (! empty($weixin_openid)) {
        $alipay_alifans = mysqld_select("SELECT * FROM " . table('alipay_alifans') . " WHERE alipay_openid=:alipay_openid ", array(
            ':alipay_openid' => $alipay_openid
        ));
        if (! empty($alipay_alifans['openid'])) {
            $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
                ':openid' => $alipay_alifans['openid']
            ));
            if (! empty($member['openid'])) {
                $_SESSION[MOBILE_ACCOUNT] = $member;
            }
        }
    }
}

function member_login_weixin($weixin_openid)
{
    if (! empty($weixin_openid)) {
        $weixin_wxfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(
            ':weixin_openid' => $weixin_openid
        ));
        if (! empty($weixin_wxfans['openid'])) {
            $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
                ':openid' => $weixin_wxfans['openid']
            ));
            if (! empty($member['openid'])) {
                $_SESSION[MOBILE_ACCOUNT] = $member;
            }
        }
    }
}

function vip_member_login($mobile, $pwd)
{
	// 少一块VIP角色的用户判断，应该传参
    $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile  limit 1", array(
        ':mobile' => $mobile
    ));
   // 结合用户注册时间，和最后登录时间来双重判断用户的登录信息
   $createtime = $member['createtime'];
   $lastime    = $member['lastime'];
   $timeend   = 30 * 24  * 60 * 60;
   if ( ($createtime + $timeend) < time() ){
           $paytime = mysqld_select("SELECT * FROM ".table('shop_order'). " WHERE  status >= 1 and openid = :openid ORDER BY paytime desc", array(':openid'=>$member['openid']) );
		   if (( $paytime['paytime'] + $lastime) < time() ){
                 return -3;
		   }
   }
   // parent_roler_id 身份id 父级	son_roler_id
    if ( !empty($member['parent_roler_id']) && !empty($member['son_roler_id']) ){
          $check = mysqld_select("SELECT * FROM ".table('rolers')." WHERE id = ".$member['son_roler_id']." and pid = ".$member['parent_roler_id']." and (type = 2 or type = 3) ");
		  if ( !$check ){
             return -1;
		  }
	}else{
          return -2;
	}
    if (!empty($member['openid'])) {
        if ($member['status'] != 1) {
            return - 1;
        }
        if ($member['pwd'] == md5($pwd)) {
            save_vip_member_login($mobile);
            return $member['openid'];
        }
    }
    return '';
}
function member_login($mobile, $pwd)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
        ':mobile' => $mobile
    ));  
    if (! empty($member['openid'])) {
        if ($member['status'] != 1) {
            return - 1;
        }
        if ($member['pwd'] == md5($pwd)) {
            save_member_login($mobile);
            return $member['openid'];
        }
    }
    return '';
}
function vip_member_logout(){
    unset($_SESSION["vip_mobile_login_fromurl"]);
    if (! empty($_SESSION[VIP_MOBILE_ACCOUNT])) {
        $openid = $_SESSION[VIP_MOBILE_ACCOUNT]['openid'];
        $weixinopenid = $_SESSION[VIP_MOBILE_SESSION_ACCOUNT]['openid'];
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('weixin_wxfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'weixin_openid' => $weixinopenid
            ));
        }
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('alipay_alifans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'alipay_openid' => $weixinopenid
            ));
        }
        
        $openid = $_SESSION[VIP_MOBILE_ACCOUNT]['openid'];
        $qqopenid = "";
        if (! empty($_SESSION[MOBILE_QQ_OPENID])) {
            $qqopenid = $_SESSION[MOBILE_QQ_OPENID];
        } else {
            $qqopenid = $_SESSION[VIP_MOBILE_SESSION_ACCOUNT]['openid'];
        }
        
        if (! empty($openid) && ! empty($qqopenid)) {
            mysqld_update('qq_qqfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'qq_openid' => $qqopenid
            ));
        }
    }
    
    unset($_SESSION[MOBILE_QQ_OPENID]);
    unset($_SESSION[VIP_MOBILE_ACCOUNT]);
    header("location:" . create_url('site', array(
        'name' => 'public',
		'do'=>'purchase'
    )));
    exit();

}
function member_logout()
{
    unset($_SESSION["mobile_login_fromurl"]);
    if (! empty($_SESSION[MOBILE_ACCOUNT])) {
        $openid = $_SESSION[MOBILE_ACCOUNT]['openid'];
        $weixinopenid = $_SESSION[MOBILE_SESSION_ACCOUNT]['openid'];
        /*if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('weixin_wxfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'weixin_openid' => $weixinopenid
            ));
        }*/
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('alipay_alifans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'alipay_openid' => $weixinopenid
            ));
        }
        
        $openid = $_SESSION[MOBILE_ACCOUNT]['openid'];
        $qqopenid = "";
        if (! empty($_SESSION[MOBILE_QQ_OPENID])) {
            $qqopenid = $_SESSION[MOBILE_QQ_OPENID];
        } else {
            $qqopenid = $_SESSION[MOBILE_SESSION_ACCOUNT]['openid'];
        }
        
        if (! empty($openid) && ! empty($qqopenid)) {
            mysqld_update('qq_qqfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'qq_openid' => $qqopenid
            ));
        }
    }
    
    unset($_SESSION[MOBILE_QQ_OPENID]);
    unset($_SESSION[MOBILE_ACCOUNT]);
    header("location:" . create_url('mobile', array(
        'name' => 'shopwap',
        'do' => 'index'
    )));
    exit();
}
function get_vip_member_account($useAccount = true, $mustlogin = false ){
    if (empty($_SESSION[VIP_MOBILE_ACCOUNT]) && $mustlogin) { 
		header("location:".create_url('site', array('name' => 'public','do' => 'purchase')));
    }
    if ($mustlogin == true) {
        return $_SESSION[VIP_MOBILE_ACCOUNT];
    }
    if (! empty($_SESSION[VIP_MOBILE_ACCOUNT])) {
        return $_SESSION[VIP_MOBILE_ACCOUNT];
    }
    return get_session_account($useAccount);
}
function get_member_account($useAccount = true, $mustlogin = false)
{
    if (extension_loaded('Memcached')) {
        $mcache = new Mcache();
    }
    if (empty($_SESSION[MOBILE_ACCOUNT]) && $mustlogin) {
        //如果是手机端请求
        if($_GET['name']=='api')
        {
            if (!extension_loaded('Memcached')) {
                return false;
            }
            $be_logout = $mcache->be_logout($_REQUEST['device_code']);
            if ($be_logout == 2) {
                $_SESSION[MOBILE_ACCOUNT] = NULL;
                return 3;
            }
            $mAccount = $mcache->get_msession($_REQUEST['device_code']);
            if (!empty($mAccount)) {
                return $mAccount;
            }else{
                return false;
            }
        }
        //非手机端请求，跳转到登陆页
        else{	
            header("location:" . create_url('mobile', array(
                    'name' => 'shopwap',
                    'do' => 'login'
            )));
        }
        
       
        exit();
    }

    if ($_GET['name']=='api' AND extension_loaded('Memcached')) {
        $be_logout = $mcache->be_logout($_REQUEST['device_code']);
        if ($be_logout == 2) {
            $_SESSION[MOBILE_ACCOUNT] = NULL;
            return 3;
        }
    }

    if ($mustlogin == true) {
        return $_SESSION[MOBILE_ACCOUNT];
    }
    
    if (! empty($_SESSION[MOBILE_ACCOUNT])) {
        return $_SESSION[MOBILE_ACCOUNT];
    }
    
    return get_session_account($useAccount);
}

function to_member_loginfromurl()
{   
	if (!empty($_SESSION["vip_mobile_login_fromurl"])){
        $fromurl = $_SESSION["vip_mobile_login_fromurl"];
        unset($_SESSION["vip_mobile_login_fromurl"]);
        return $fromurl;
	}
    if (!empty($_SESSION["mobile_login_fromurl"])) {
        $fromurl = $_SESSION["mobile_login_fromurl"];
        unset($_SESSION["mobile_login_fromurl"]);
        return $fromurl;
    } else {
		return create_url('mobile', array(
            'name' => 'shopwap',
            'do' => 'shopindex'
        ));
    }
}

function member_get($openid)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid ", array(
        ':openid' => $openid
    ));
    
    return $member;
}

/**
 * 更新 credit和experience字段  只对积分操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid
 * @param $fee
 * @param $type  addcredit    usecredit
 * @param $remark
 * @return bool
 */
function member_credit($openid, $fee, $type, $remark)
{
    $add_arr = array('addcredit');
    $use_arr = array('usecredit');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            message("参数有误！",refresh(),'error');
        }else if(in_array($type,$use_arr)){
            //积分为负
            $fee = -1*floor($fee);
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => intval($fee),
            'account_fee' => $member['credit'] + $fee,
            'createtime' => TIMESTAMP,
            'openid' => $openid
        );
        mysqld_insert('member_paylog', $data);
        $credit     = max(0,$member['credit']+$fee);
        $experience = max(0,$member['experience']+$fee);
        mysqld_update('member', array(
            'credit' => $credit,
            'experience' => $experience
        ), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}

/**
 * 更新免单返现的字段  freeorder_gold
 * 只对 免单金额操作，  注意免单金额的使用，有一个免额过期时间，那么时间的判断是否可以使用免额，要在外部判断，正常是下单后判断免额没过期，订单总额扣除免额，再产生免额log
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid
 * @param $fee
 * @param $type  addgold or usegold
 * @param $remark
 * @return bool
 */
function member_freegold($openid, $fee, $type, $remark)
{
    $add_arr = array('addgold');
    $use_arr = array('usegold');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            message("参数有误！",refresh(),'error');
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee' => $member['freeorder_gold'] + $fee,
            'createtime' => TIMESTAMP,
            'openid' => $openid
        );
        //以免扣掉时为负数
        $gold  = max(0,$member['freeorder_gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array('freeorder_gold' => $gold ), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}
/**
 * 更新gold字段   只对金额操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid
 * @param $fee
 * @param $type     addgold  usegold
 * @param $remark
 * @param $update 有些地方不一定要更新gold，只需要有记录。如下单后，钱是第三方的，但是会记录一个paylog,这时候不是扣除余额，不能进行更新
 * @return bool
 */
function member_gold($openid, $fee, $type, $remark,$update=true)
{
    $add_arr = array('addgold');
    $use_arr = array('usegold');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            message("参数有误！",refresh(),'error');
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee' => $member['gold'] + $fee,
            'createtime' => TIMESTAMP,
            'openid' => $openid
        );
        $gold  = max(0,$member['gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        if($update){
            mysqld_update('member', array( 'gold' => $gold), array(
                'openid' => $openid
            ));
        }
        return true;
    }
    return false;
}

/**
 * 更新冻结字段freeze_gold   只对佣金操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid          卖家openid
 * @param $friend_openid   买家openid
 * @param $fee
 * @param $type  addgold_byoder or usegold_byoder
 * @param $remark
 * @return bool
 */
function member_commisiongold($openid, $friend_openid,$fee, $type, $remark)
{
    $add_arr = array('addgold_byoder');
    $use_arr = array('usegold_byoder');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            message("参数有误！",refresh(),'error');
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee'   => $member['freeze_gold'] + $fee,
            'createtime'    => TIMESTAMP,
            'openid'        => $openid,
            'friend_openid' => $friend_openid
        );
        //以免扣掉时为负数
        $freeze_gold  = max(0,$member['freeze_gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array( 'freeze_gold' => $freeze_gold), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}

/**
 * 更新gold字段  只对邀请好友时操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid          邀请人openid
 * @param $friend_openid   当前被邀请openid
 * @param $fee
 * @param $type  addgold_byinvite  usegold_byinvite
 * @param $remark
 * @return bool
 */
function member_invitegold($openid,$friend_openid, $fee, $type, $remark)
{
    $add_arr = array('addgold_byinvite');
    $use_arr = array('usegold_byinvite');
    $member  = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入",refresh(),'error');
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            message("参数有误！",refresh(),'error');
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee'   => $member['gold'] + $fee,
            'createtime'    => TIMESTAMP,
            'openid'        => $openid,
            'friend_openid' => $friend_openid,
        );
        //以免扣掉时为负数
        $gold  = max(0,$member['gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array( 'gold' => $gold), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}
/**
 * @param $openid
 * @param $name
 * @param $face
 * @return string
 * @content 有的评论来自后台录入的，有假的用户名和头像。
 */
function getUserFaceAndName($openid,$name,$face){
    if(empty($name)){
        //名字是空的 头像也一定是空，说明该用户是真实用户评论
        $user = mysqld_select("select realname,avatar,mobile from ". table('member') ." where openid='{$openid}'");
        if(!empty($user['realname'])){
            $name = substr_cut($user['realname']);
        }else{
            $name = substr_cut($user['mobile']);
        }
        if(empty($user['avatar'])){  //返回头像和用户名
            $face = WEBSITE_ROOT. "themes/default/__RESOURCE__/recouse/images/userface.png";
        }else{
            $face = download_pic($user['avatar'],'40',40,1);
        }
    }else{
        $name = substr_cut($name);
        if(empty($face)){
            $face = WEBSITE_ROOT. "themes/default/__RESOURCE__/recouse/images/userface.png";
        }else{
            $face = download_pic($face,'40',40,2);
        }
    }
    return array('face'=>$face,'username'=>$name);
}
//将用户名或者手机号进行处理，中间用星号表示
function substr_cut($str){
    //获取字符串长度
    $strlen = mb_strlen($str, 'utf-8');
    //如果字符创长度小于等于2
    if($strlen<=2){
        $firstStr = mb_substr($str, 0, 1, 'utf-8');
        $xing     = str_repeat("*",1);
        return $firstStr.$xing;
    }else{
        if(is_numeric($str)){
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($str, 0, 2, 'utf-8');
            $lastStr = mb_substr($str, -2, 2, 'utf-8');
            $xing    = str_repeat("*",3);
        }else{
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($str, 0, 1, 'utf-8');
            $lastStr = mb_substr($str, -1, 1, 'utf-8');
            $xing    = str_repeat("*",3);
        }
        return $firstStr.$xing.$lastStr;
    }
}

/**
 * @param $uid
 * @return string
 * @content 获取管理员名字
 */
function getAdminName($uid){
    $name = '';
    if(!empty($uid)){
        $users = mysqld_select("select username from ".table('user')." where id={$uid}");
        if(!empty($users))
            $name = $users['username'];
    }
    return $name;
}

/**
 * @param $uid
 * @content获取管理员被设置的角色是哪个
 */
function getAdminRolers($uid){
    $roler = '';
    if(!empty($uid)){
        $users = mysqld_select("select ro.name from ".table('rolers_relation')." as r left join ". table('rolers')." as ro on ro.id=r.rolers_id where r.uid={$uid}");
        if(!empty($users))
            $roler = $users['name'];
    }
    return $roler;
}

/**
 * 获取所有的业务员
 */
function getAllAgent(){
    //获取业务员角色
    $rolers = mysqld_select("select id from ".table('rolers')." where isdelete=0 and type=1");
    $users  = '';
    if(!empty($rolers)){
        $sql = "select u.id,u.username from ".table('rolers_relation')." as r left join ".table('user')." as u";
        $sql .= " on u.id=r.uid where r.rolers_id={$rolers['id']}";
        $users = mysqld_selectall($sql);
    }
    return $users;
}

/**
 * @return bool
 * 是否是一个业务员管理员
 */
function isAgentAdmin(){
    $amdin_uid = $_SESSION['account']['id'];
    $info = mysqld_select("select rolers_id from ".table('rolers_relation')." where uid={$amdin_uid}");
    if(empty($info)){  //说明还没分配过
        return false;
    }
    //获取业务员角色
    $rolers = mysqld_select("select id from ".table('rolers')." where isdelete=0 and type=1");
    if($rolers['id'] == $info['rolers_id']){
        //如果是该业务员
        return true;
    }else{
        //如果分配的不是业务员角色 则不算是业务员
        return false;
    }
}

/**
 * @param $str
 * @param $uid
 * @return string
 * 判断是否是自己所关联的渠道商，不是打星号不显示
 */
function isSelfAgent($str,$uid){
    $amdin_uid = $_SESSION['account']['id'];
    if(isAgentAdmin()){
        //是一个业余员
        if($amdin_uid != $uid){
            //并且关联的渠道商 不是自己的客户 打星号
            $str = substr_cut($str);
        }
    }
    return $str;
}

/**
 * @return int
 * @return int
 * 验证用户是否登录
 * get_member_account 该方法可以进行获取用户是否登录，但是很多时候会自动跳转到登录，
 * 一些场合，不需要跳转故再加一个方法
 */
function checkIsLogin(){
    //微信端 如果从 get_member_account 中获取 得到的openid就是weixin_openid
    //还不能判断就是登陆了
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        //微信端存的 key 是 MOBILE_SESSION_ACCOUNT  登录后存的key 是 MOBILE_ACCOUNT
        if (! empty($_SESSION[MOBILE_ACCOUNT])) {
            return $_SESSION[MOBILE_ACCOUNT]['openid'];
        }else{
            return '';
        }
    }else{
        //非微信端存的 可能有临时的 前面带有 _t
        $member = get_member_account(false);
        if(empty($member)){
            return '';
        }else{
            $openid     = $member['openid'];
            $openid_arr = explode('_t', $openid);
            if(count($openid_arr) == 2){
                //是临时用户
                return '';
            }else{
                return $openid;
            }
        }
    }
}

/**
 * @return int
 * @return int
 * 验证用户是否APP首次登录，并赠送积分
 * 
 */
function ifApp($openid=''){
    if ( !empty($openid) ){
        $member = mysqld_select("SELECT * FROM " . table('member') . " where ifapp = 0 and openid=:openid limit 1", array(
            ':openid' => $openid
        )); 
		if ($member){
			mysqld_update('member', array('ifapp'=>1), array('openid'=>$openid));
            member_credit($openid, 50, 'addcredit', '首次登陆APP积分赠送50');
		}
	}
}

/**
 * 该方法少用，尽量使用以下
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 *
 * 如果使用，请小心type类型，type的值，参照以上五个方法，并且金额fee支出(use)  要记为负数
 * 同时还要注意有friend_openid这个字段，如佣金 与邀请的 就必须存值，故该方法要小心使用
 *
 * 记录用户账单的收支情况
 * @param $openid :用户ID
 * @param $fee:收支费用
 * @param $account_fee:用户账号剩余金额
 * @param unknown $type:收支类型
 * @param unknown $remark :收支备注
 */
function insertMemberPaylog($openid, $fee,$account_fee, $type, $remark)
{
	$data = array('remark' 			=> $remark,
					'type' 			=> $type,
					'fee' 			=> $fee,
					'account_fee' 	=> $account_fee,
					'createtime' 	=> TIMESTAMP,
					'openid' 		=> $openid
	);

	return mysqld_insert('member_paylog', $data);
}

/**
 * @content 获得用户的余额
 * @param $gold  余额
 * @param $free_gold  免单返现金额
 * @param $free_time  免单使用期限
 * @return mixed
 */
function getMemberBalance($gold,$free_gold,$free_time){
    if(time()>$free_time){
        return $gold;
    }else{
        $total = $gold+$free_gold;
        return $total;
    }
}

/**
 * 当有人注册的时候，分享者的觅友统计数要累加
 * @param $recommend_openid
 */
function recommend_frend_count($recommend_openid){
    if($recommend_openid){
        $sql = "update ".table('member')." set `friend_count`=friend_count+1 where openid={$recommend_openid}";
        mysqld_query($sql);
    }
}