<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\shopwap\loginService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\wapi;

class loginService extends \service\publicService
{
    public function do_login($code)
    {
        //wxee3d6d279578322b线上appid
        $seting = globaSetting();
        $appid  = $seting['xcx_appid'];
        $secret = $seting['xcx_appsecret'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code=".$code.'&grant_type=authorization_code';
        $res = http_get($url);
        $res = json_decode($res,true);
        if(empty($res['openid']) || empty($res['session_key'])){
            $this->error = $res['errmsg'];
            return false;
        }

        $expires_in   = TIMESTAMP + $res['expires_in'];

        /**
         * 生成第三方3rd_session，用于第三方服务器和小程序之间做登录态校验。为了保证安全性，3rd_session应该满足：
         * a.长度足够长。建议有2^128种组合，即长度为16B
         * b.避免使用rand（当前时间）然后rand()的方法，而是采用操作系统提供的真正随机数机制，比如Linux下面读取/dev/urandom设备
         * c.设置一定有效时间，对于过期的3rd_session视为不合法
         *
         * 以 $session3rd 为key，sessionKey+openId为value，写入memcached
         */
        $session3rd         = $this->session3rd(16);
        //未注册的注册该用户信息

        $this->set_session3rd_cache($session3rd,$res,$expires_in);

        return $res;
    }

    public function insertAndgetUserinfo($weixin_openid)
    {
        get_member_account();
        $info = mysqld_select("select id from ".table('weixinfans_xcx')." where weixin_openid={$weixin_openid}");
        if(empty($info)){
            //插入
        }
        return $info;
    }

    public function session3rd($len)
    {
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }else{
            trigger_error('Can not open /dev/urandom.');
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $len);
    }

    public function set_session3rd_cache($session3rd,$data,$expires_in)
    {
        $session3rd = "session3rd_".$session3rd;
        $cache_val = serialize(array('openid'=>$data['opedid'],'session_key'=>$data['session_key']));
        if(class_exists('Memcached')){
            $memcache  = new \Mcache();
            $memcache->set($session3rd,$cache_val,$expires_in);
        }else {
            ajaxReturnData(0,'请开启缓存!');
        }
    }

    public function get_session3rd_cache($session3rd)
    {
        $session3rd = "session3rd_".$session3rd;
        if(class_exists('Memcached')){
            $memcache  = new \Mcache();
            $data = $memcache->get($session3rd);
        }else {
            ajaxReturnData(0,'请开启缓存!');
        }
        if(empty($data)){
            return false;
        }
        return unserialize($data);
    }
}