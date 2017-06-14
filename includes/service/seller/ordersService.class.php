<?php
/**
订单的service层
 */
namespace service\seller;
use service\publicService;
class ordersService extends publicService
{
    public $storeid;
    public function __construct(){
        parent::__construct();
        $member=get_member_account();
        $this->storeid = $member['store_sts_id'];
    }

    const ORDER_CLOSED = -1;//已关闭
    const ORDER_NORNAL = 0;//普通状态，待付款
    const ORDER_HAVE_PAY = 1;//已付款
    const ORDER_HAVE_SEND = 2;//已发货
    const ORDER_HAVE_FINISHED = 3;//已完成
    
    const PAY_DELIVERY = 0;//货到付款
    const PAY_WEIXIN = 1;//微信支付
    const PAY_ALIPAY = 2;//支付宝
    const PAY_BALANCE = 3;//余额
    const PAY_INTERGAL = 4;//积分
    
    const OG_FAIL = -1;//申请审核失败
    const OG_NORMAL = 0;//正常状态
    const OG_REPLY = 1;//正在申请
    const OG_SUCCESS = 2;//审核通过
    const OG_RETURNED = 3;//买家已退货
    const OG_SUCCESS_RETURN = 4;//退款成功
    
    const OG_GOOD_MONEY = 1;//退款退货
    const OG_GOOD = 2;//换货
    const OG_MONEY = 3;//仅退款
    const OG_NONE = 4;//无
    //订单表的状态名称
    public static $status_name_order = array(

        self::ORDER_CLOSED => '已关闭',
        self::ORDER_NORNAL => '待付款',
        self::ORDER_HAVE_PAY => '已付款',
        self::ORDER_HAVE_SEND => '已发货',
        self::ORDER_HAVE_FINISHED => '已完成',
    );
    //订单商品的状态名称
    public static $status_name_og = array(
    
        self::OG_FAIL => '申请审核失败',
        self::OG_NORMAL => '正常状态',
        self::OG_REPLY => '正在申请',
        self::OG_SUCCESS => '审核通过',
        self::OG_RETURNED => '买家已退货',
        self::OG_SUCCESS_RETURN => '退款成功',
    );
    //订单商品的状态类型
    public static $type_name_og = array(
    
        self::OG_GOOD_MONEY => '退款退货',
        self::OG_GOOD => '换货',
        self::OG_MONEY => '仅退款',
        self::OG_NONE => '',//无
    );
    
    //付费类型的名称
    public static $pay_name_map = array(
        self::PAY_DELIVERY => '货到付款',
        self::PAY_WEIXIN => '微信支付',
        self::PAY_ALIPAY => '支付宝',
        self::PAY_BALANCE => '余额',
        self::PAY_INTERGAL => '积分',
    );
    
    /**app订单主页接口封装
     * 
     *  */
    public function OrderIndexPage(){
        //所有订单
        $all_order = mysqld_select("SELECT count(*) as num from ".table('shop_order')." where sts_id=:sts_id",array('sts_id'=>$this->storeid));
        //待支付
        $wait_pay = mysqld_select("SELECT count(*) as num from ".table('shop_order')." where sts_id=:sts_id and status=0",array('sts_id'=>$this->storeid));
        //已付款，待发货
        $wait_send = mysqld_select("SELECT count(*) as num from ".table('shop_order')." where sts_id=:sts_id and status=1",array('sts_id'=>$this->storeid));
        //已发货
        $has_send = mysqld_select("SELECT count(*) as num from ".table('shop_order')." where sts_id=:sts_id and status=2",array('sts_id'=>$this->storeid));
        //已收货
        $has_get = mysqld_select("SELECT count(*) as num from ".table('shop_order')." where sts_id=:sts_id and status=3",array('sts_id'=>$this->storeid));
        //退换单处理中
        $returning = mysqld_select("SELECT count(distinct orderid) as num from ".table('shop_order_goods')." where sts_id=:sts_id and (status=2 or status=3)",array('sts_id'=>$this->storeid));
        //退换单完成
        $returned = mysqld_select("SELECT count(distinct orderid) as num from ".table('shop_order_goods')." where sts_id=:sts_id and (status=-1 or status=4)",array('sts_id'=>$this->storeid));
        //退单待处理
        $return_wait = mysqld_select("SELECT count(distinct orderid) as num from ".table('shop_order_goods')." where sts_id=:sts_id and status=1",array('sts_id'=>$this->storeid));
        return array(
            'all_order' => $all_order['num'],
            'wait_pay' => $wait_pay['num'],
            'wait_send' => $wait_send['num'],
            'has_send' => $has_send['num'],
            'has_get' => $has_get['num'],
            'returning' => $returning['num'],
            'returned' => $returned['num'],
            'return_wait' => $return_wait['num'],
        );
    }
    /**
     * 订单列表页面封装
     * */
    public function OrderListsPage($_GP){
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit'])?$_GP['limit']:10;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $status = !isset($_GP['status']) ? '' : $_GP['status'];
        $sql = "SELECT a.id,a.sts_id,a.openid,a.ordersn,a.price,a.status,a.paytypecode,a.dispatchprice,c.nickname,a.createtime,a.balance_sprice,a.remark,a.retag,c.mobile FROM " . table('shop_order') . " a LEFT JOIN " . table('member') ." c ON a.openid=c.openid  WHERE a.sts_id=:sts_id";
        $sqlNum = "SELECT count(*) as num from ".table('shop_order')." as a where a.sts_id=:sts_id";
        $condition='';
        //默认显示全部
        
        if(!empty($status) || $status == '0'){
            $condition .= " and a.status=".$status;
            $sqlNum .=$condition;
        }
        $condition .= " ORDER BY a.createtime DESC LIMIT ".$limit.",".$psize;
        $sql .=$condition;
        $order_lists = mysqld_selectall($sql,array('sts_id'=>$this->storeid));
        $total = mysqld_select($sqlNum,array('sts_id'=>$this->storeid));
        $pager = pagination($total['num'], $pindex, $psize);
        foreach ($order_lists as $key=>$v){
            $retagInfo = json_decode($v['retag'],1);
            unset($order_lists[$key]['retag']);
            $order_lists[$key]['beizhu'] = $retagInfo['beizhu'];
            $order_lists[$key]['status_name'] = ordersService::$status_name_order[$v['status']];
            $order_lists[$key]['paytype_name'] = ordersService::$pay_name_map[$v['paytypecode']];
            $order_lists[$key]['price'] = FormatMoney($v['price'],0);
            $order_lists[$key]['dispatchprice'] = FormatMoney($v['dispatchprice'],0);
        }
        $result = array(
            'order_lists' => $order_lists,
            'total' => $total['num'],
            'pager' => $pager,
        );
        return $result;
    }
    /**
     * 订单详细页面的封装
     * @param intval $id  */
    public function OrderDetailPage($id){
        $info = $this->getOrderInfo($id);
        if (!empty($info)){
            $nickname = member_get($info['openid'],"nickname");
            $info['nickname'] = $nickname['nickname'];
            $info['status_name']=ordersService::$status_name_order[$info['status']];
            if ($info['price']) $info['price'] = FormatMoney($info['price'],0);
            if ($info['balance_sprice']) $info['balance_sprice'] = FormatMoney($info['balance_sprice'],0);
            //后台操作备注信息
            if ($info['retag']){
                $retagInfo = json_decode($info['retag'],1);
                $info['beizhu'] = $retagInfo['beizhu'];
                if ($retagInfo['recoder']){
                    $retagArr = explode(';',$retagInfo['recoder']);
                    foreach ($retagArr as $key=>$v){
                        $recoderInfo = explode("-", $v);
                        //后台操作人员信息获取
                        $recoderInfoName = member_get($recoderInfo[0],"nickname");
                        $recoderInfo[0] = $recoderInfoName['nickname'];
                        $flag[] = $recoderInfo;
                    }
                    $info['recoder'] = $flag;
                    unset($info['retag']);
                }
            }
            $info['goods'] = $this->getOrderGoodsDetail($id);
        }
        return $info;
    }
    /**
     * 退换单的列表页面封装
     * 
     *   */
    public function returnListPage($_GP){
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit'])?$_GP['limit']:10;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $sql = "select o.ordersn,o.id as orderid,o.openid,o.createtime,og.id as odgid,og.price,og.type,og.status,og.reply_return_time,og.dishid,og.spec_key_name,og.total as goods_num from ". table('shop_order_goods') ." as og left join ".table('shop_order') ." as o on og.orderid=o.id where og.sts_id=:sts_id ";
        if ($_GP['status'] == 4){
            $sql .=" and (og.status=-1 or og.status=4)";//退单完成 退单失败
        }elseif ($_GP['status'] == 1){
            $sql .=" and og.status=1 ";//待处理
        }elseif($_GP['status'] == -2){//进行中
            $sql .= " and (og.status=2 or og.status=3) ";
        }else {
            $sql .= " and (og.status=1 or og.status=2 or og.status=3) ";//pc端待处理和进行中在一起
            
        }
        $total = count(mysqld_selectall($sql,array('sts_id'=>$this->storeid)));
        $sql .= " ORDER BY og.createtime DESC LIMIT ".$limit.",".$psize;
        $returnList = mysqld_selectall($sql,array('sts_id'=>$this->storeid));
        if ($returnList){
            foreach ($returnList as $key=>$v){
                $nickname = member_get($v['openid'],"nickname");
                $returnList[$key]["nickname"] = $nickname['nickname'];
                $returnList[$key]['price'] = FormatMoney($v['price'],0);
                $returnList[$key]['status_name'] = ordersService::$status_name_og[$v['status']];
                $returnList[$key]['type_name'] = ordersService::$type_name_og[$v['type']];
            }
        }
        $pager = pagination($total, $pindex, $psize);
        
        $result = array(
            'returnList' => $returnList,
            'total' => $total,
            'pager' => $pager,
        );
        return $result;
    }
    /**
     * 修改收货人信息页面封装
     * @param array $data  */
    public function modifyAddress($data = array()){
        if (!is_array($data) || empty($data['id'])) return false;
        $flag = mysqld_update('shop_order',array(
	        'retag'			   => $data['retag'],
	        'address_realname' => $data['address_realname'],
	        'address_mobile'   => $data['address_mobile'],
	        'address_province' => $data['address_province'],
	        'address_city'     => $data['address_city'],
	        'address_area'     => $data['address_area'],
	        'address_address'  => $data['address_address']
	    ),array('id'=>$data['id'],'sts_id'=>$this->storeid));
        return $flag;
    }
    /**
     *订单发货页面封装
     * @param array $data  
     * @param $is_dispatch '1选择物流，2不选择第三方物流，线下配送'
     * */
    public function AddSendInfo($data = array(),$is_dispatch = 1){
        if (!is_array($data) || empty($data['id'])) return false;
        if ($is_dispatch == 1){
            $insertData = array(
                'status'     => 2,
                'retag'      => $data['retag'],
                'express'    => $data['express'],
                'expresscom' => $data['expresscom'],
                'expresssn'  => $data['expresssn'],
                'sendtime'   =>time(),  //发货时间
                'is_dispatch'   =>1,
            );
        }elseif ($is_dispatch == 2){
            $insertData = array(
                'status'     => 2,
                'retag'      => $data['retag'],
                'sendtime'   =>time(),  //发货时间
                'is_dispatch'   =>2,
            );
        }
        $flag = mysqld_update('shop_order',$insertData,array('id'=>$data['id'],'sts_id'=>$this->storeid));
        return $flag;
    }
    /**
     * 根据订单id，在shop_order表获取订单信息
     *
     * @param intval $id 订单id
     * @param string $param 参数
     * */
    public function getOrderInfo($id,$param="*"){
        if ($id){
            $order = mysqld_select("select {$param} from ". table('shop_order') ." where id=:id and sts_id=:sts_id",array('id'=>$id,'sts_id'=>$this->storeid));
        }
        return $order;
    }
    /**
     * 根据订单id，在shop_order_goods表获取该订单的所有商品信息
     * @param intval $orderid orderid
     * @param string $param 参数
     * */
    public function getOrderGoodsInfo($orderid,$param="*"){
        if ($orderid){
            $order = mysqld_selectall("select {$param} from ". table('shop_order_goods') ." where orderid=:orderid and sts_id=:sts_id",array('orderid'=>$orderid,'sts_id'=>$this->storeid));
        }
        return $order;
    }
    /**
     *根据订单id，在shop_order_goods表和shop_dish表获取该订单的所有商品详细信息
     * @param intval $orderid orderid
     **/
    public function getOrderGoodsDetail($orderid){
        if ($orderid){
            $goods = mysqld_selectall("SELECT g.title,g.thumb,g.marketprice,g.productprice,g.goodssn,o.id as order_shop_id,o.total,o.price as orderprice,o.status as order_status, o.type as order_type,o.spec_key_name FROM " . table('shop_order_goods') . " as o left join " . table('shop_dish') . " g on o.dishid=g.id "
                . " WHERE o.orderid=:orderid and o.sts_id=:sts_id",array('orderid'=>$orderid,'sts_id'=>$this->storeid));
            if ($goods){
                foreach ($goods as $key=>$v){
                    $goods[$key]['status_name'] = ordersService::$status_name_og[$v['order_status']];
                    $goods[$key]['type_name'] = ordersService::$type_name_og[$v['order_type']];
                }
            }
        }
        return $goods;
    }
    /**
     * 根据订单id，获取退单的信息
     * @param $orderid 订单id  */
    public function afterReturn($orderid) {
        if ($orderid){
            $info = mysqld_select("SELECT * FROM ".table('aftersales')." where order_goods_id=:order_goods_id",array('order_goods_id'=>$orderid));
            if (!empty($info)) return $info;
        }
    }
    /**
     * 添加卖家备注
     * @param $data array()
     *  */
    public function addOrderRemark($data = array()){
        if (!empty($data) && is_array($data)){
            $info = $this->getOrderInfo($data['id'],'retag');
            if (empty($info['retag'])){
                $retag['beizhu'] = $data['remark'];
            }else {
                $retag = json_decode($info['retag'],1);
                $retag["beizhu"] = $data['remark'];
            }
            $retag = json_encode($retag);
            $json_retag = setOrderRetagInfo($retag, '修改备注：已经修改了备注信息');
            $res = mysqld_update('shop_order',array('retag'=>$json_retag),array('id'=>$data['id']));
            if ($res) return true;
        }
    }
    /**更新 order_goods表
     * $condition  array 条件
     * $data array 更改的数据
     *   */
    public function updateOrderGoods($condition=array(),$data=array()){
        if (!empty($data) && !empty($condition)){
            $condition['sts_id']=$this->storeid;
            $res = mysqld_update('shop_order_goods',$data,$condition);
            return $res;
        }
    }
    /**
     * 根据条件获得售后表的信息,获取一条
     * $condition 条件
     * $param 参数
     *   */
    public function getAftersalesByCon($condition,$param="*"){
        if (!empty($condition)){
            $info = getOne('aftersales',$param,$condition);
            if ($info) return $info;
        }
    }
    /**
     * 根据条件获得售后日志表的信息,获取一条
     * $condition
     * $param 参数
     *   */
    public function getAftersalesLogByCon($condition,$param="*"){
        if (!empty($condition)){
            $info = getOne('aftersales_log',$param,$condition);
            if ($info) return $info;
        }
    }
    /**
     * 根据条件获得订单商品表的信息,获取一条
     * $condition
     * $param 参数
     *   */
    public function getOrderGoodsByCon($condition,$param="*"){
        if (!empty($condition)){
            $info = getOne('shop_order_goods',$param,$condition);
            if ($info) return $info;
        }
    }
}