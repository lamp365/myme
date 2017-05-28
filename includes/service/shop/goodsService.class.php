<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\shop\goodsService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\shop;

class goodsService extends \service\publicService
{
    public function check_data_beforadd($data)
    {
        if(empty($data['title'])){
            $this->error = '宝贝标题不能为空！';
            return false;
        }
        if(empty($data['productprice']) || empty($data['marketprice']) ){
            $this->error = '价格不能为空！';
            return false;
        }
        if($data['type'] != 0){
            if(empty($data['timestart']) || empty($data['timeend'])){
                $this->error = '活动时间不能为空！';
                return false;
            }
        }
        if(empty($data['transport_id'])){
            $this->error = '运费模板不能为空！';
            return false;
        }
        if(empty($data['total'])){
            $this->error = '请设置库存！';
            return false;
        }
        if(empty($data['id'])){  //添加的时候 图片必须给
            if(empty($_FILES['thumb']['name'])){
                $this->error = '商品主图不能为空！';
                return false;
            }
        }

        //团购商品时
        if(intval($data['type'])==1 && isset($data['team_buy_count']) && empty($data['team_buy_count'])) {
            $this->error = '请设置成团人数！';
            return false;
        } elseif (intval($data['type'])==1 && isset($data['draw']) && $data['draw'] == 1 && empty($_GP['team_draw_num'])) {
            $this->error = '请设置抽奖人数！';
            return false;
        }

        return true;
    }

    //添加产品的时候 c操作图片
    public  function actGoodsPicture($id,$_GP)
    {
        $hsdata = array();
        if (!empty($_GP['attachment-new'])) {
            foreach ($_GP['attachment-new'] as $index => $row) {
                if (empty($row)) {
                    continue;
                }
                $hsdata[$index] = array(
                    'attachment' => $_GP['attachment-new'][$index],
                );
            }
            $cur_index = $index + 1;
        }
        if (!empty($_GP['attachment'])) {
            foreach ($_GP['attachment'] as $index => $row) {
                if (empty($row)) {
                    continue;
                }
                $hsdata[$cur_index + $index] = array(
                    'attachment' => $_GP['attachment'][$index]
                );
            }
        }
        mysqld_delete('shop_dish_piclist', array('goodid' => $id));
        foreach ($hsdata as $row) {
            $data = array(
                'goodid' => $id,
                'picurl' =>$row['attachment']
            );
            mysqld_insert('shop_dish_piclist', $data);
        }


    }
    
    //添加dish到shop_goods
    public  function addDishToShopGoods($dish_id,$shop_cat_array=array('p1'=>'','p2'=>''))
    {
        $info = mysqld_select("select * from " . table('shop_dish') . " where id=:id  limit 1", array(":id" =>$dish_id));
        if(!$info){
            $this->error = "id匹配不到数据";
            return false;
        }
        if( $info['is_already_in_shop'] ){
            $this->error = "已经在产品库中";
            return false;
        }
        $data = array(
            'pcate'        => intval($shop_cat_array['p1']),
            'ccate'        => intval($shop_cat_array['p2']),
            'type'         => $info['type'],
            'brand'        => $info['brand'],
            'gtype_id'     => $info['gtype_id'],
//                    'coefficient'  => $info['type'],
            'status'       => intval($info['status']),
            'sort'         => intval($info['sort']),
            'title'        => $info['title']?$info['title']:'',
//            'subtitle'     => $info['subtitle']?$info['subtitle']:'',
//            'thumb'        => $info['thumb']?$info['thumb']:'',
            'description'  => $info['description']?$info['description']:'',
            'content'      => $info['content']?$info['content']:'',
//            'goodssn'      => $info['goodssn']?$info['goodssn']:'',
//                    'weight'       => $_GP['productsn'],
//                    'unit'         => $_GP['marketprice'],
//                    'weight'       => $_GP['weight'],
            'marketprice'  => $info['marketprice'],
            'productprice' => $info['productprice'],
            'store_count'  => $info['store_count'],
            'totalcnf'     => $info['totalcnf'],
            'sales'        => $info['sales_num'],
            'createtime'   => TIMESTAMP,
//                    'credit'       => intval($_GP['isfirst']),
            'isnew'        => $info['isnew']?$info['isnew']:0,
            'issendfree'   => $info['issendfree']?$info['issendfree']:0,
            'ishot'        => $info['ishot']?$info['ishot']:0,
            'isfirst'      => $info['isfirst']?$info['isfirst']:0,
            'isjingping'   => $info['isjingping']?$info['isjingping']:0,
            'isdiscount'   => $info['isdiscount']?$info['isdiscount']:0,
            'isrecommand'  => $info['isrecommand']?$info['isrecommand']:0,
            'istime'       => $info['istime']?$info['istime']:0,
            'timestart'    => $info['timestart']?$info['timestart']:0,
            'timeend'      => $info['timeend']?$info['timeend']:0,
            'deleted'      => $info['deleted']?$info['deleted']:0,
        );
        $info['thumb'] && $data['thumb'] = $info['thumb'];
//        ppd($data);
        mysqld_insert('shop_goods',$data);
        $id = mysqld_insertid();
        if($id){
            $effect = mysqld_update('shop_dish',array('is_already_in_shop'=>1), array('id' => $info['id']));
        }
        return $id;
    }
    
    public function actGoodsAttr($id,$attritem)
    {
        if(empty($id))  return '';
        if(empty($attritem)){
            //清掉属性 属性可以请掉
            mysqld_delete("goods_attr",array('goods_id'=>$id));
            return '';
        }
        foreach($attritem as $attr_key => $item){
            //去除空的值，没有输入的属性项
            foreach($item as $key => $val){
                if(empty($val)){
                    unset($item[$key]);
                }
            }

            $attr_key_arr = explode('@',$attr_key);
            if(count($attr_key_arr) == 2){
                //则是修改
                $goods_attr_id = $attr_key_arr[0];
                $attr_id       = $attr_key_arr[1];
                if(empty($item)){
                    //删除 掉
                    mysqld_delete("goods_attr",array('goods_attr_id'=>$goods_attr_id));
                }else{
                    //更改
                    $attr_value   = implode(' / ',$item);
                    $up_data      = array('attr_value'  => $attr_value,);
                    mysqld_update('goods_attr',$up_data,array('goods_attr_id'=>$goods_attr_id));
                }
            }else{
                //则是新添加
                $attr_id      = $attr_key;
                if(!empty($item)){
                    $attr_value   = implode(' / ',$item);
                    $up_data          = array(
                        'goods_id'    => $id,
                        'attr_id'     => $attr_id,
                        'attr_value'  => $attr_value,
                    );
                    mysqld_insert('goods_attr',$up_data);
                }
            } //else  end
        }// foreach end
    }

    public function actGoodsSpec($id,$specitem)
    {
        if(empty($id)){
            return '';
        }
        mysqld_delete("goods_spec_price",array('dish_id'=>intval($id)));
        if(empty($specitem)){
            return '';
        }
        foreach($specitem as $spec_key => $item){
            $spec_key_str  = $spec_key;
            $insert_data  = array(
                'dish_id'      => $id,
                'spec_key'     => $spec_key_str,
                'key_name'     => $item['key_name'],
                'marketprice'  => $item['marketprice'],
                'productprice' => $item['productprice'],
                'total'        => intval($item['total']),
                'productsn'    => $item['productsn'],
                'createtime'   => time(),
            );
            mysqld_insert('goods_spec_price',$insert_data);
        }//end foreach
    }
}