<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace shopwap\controller;
use  shopwap\controller;

class login extends \shopwap\controller\base{

    //这个值等价于$_GP
    public $request = '';

    public function __construct()
    {
        parent::__construct();
        if ( checkIsLogin() ){
            header("location:" . to_member_loginfromurl());
        }

    }


    //没有op默认显示 index
    public function index()
    {
        $showqqlogin = false;
        $qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
        if ( ! empty($qqlogin['id'])) {
            $showqqlogin = true;
        }

        // 获取使用条款
        $use_page = getArticle(1,2);

        if ( !empty($use_page) ){
            $use_page = mobile_url('article',array('name'=>'addon8','id'=>$use_page[0]['id']));
        }else{
            $use_page = 'javascript:void(0)';
        }

        // 获取用户隐私
        $use_private = getArticle(1,3);
        if ( !empty($use_private) ){
            $use_private = mobile_url('article',array('name'=>'addon8','id'=>$use_private[0]['id']));
        }else{
            $use_private =  'javascript:void(0)';
        }

        //wap端关于我们
        $use_about = getArticle(1,5);
        if ( !empty($use_about) ){
            $use_about = mobile_url('article',array('name'=>'addon8','id'=>$use_about[0]['id']));
        }else{
            $use_about =  'javascript:void(0)';
        }
        include themePage('login');
    }


    //表单提交 操作登录
    public function do_login()
    {
        $_GP = $this->request;

        $loginService = new \service\shopwap\loginService();
        $res = $loginService->do_login($_GP);
        if($res){
            $url =   WEBSITE_ROOT;
            message('登录成功！',$url,'success');
        }else{
            message($loginService->getError(),refresh(),'error');
        }
    }


}