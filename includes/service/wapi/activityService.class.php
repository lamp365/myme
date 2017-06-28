<?php
/**
微信限时购活动
 */
namespace service\wapi;

class activityService extends \service\publicService
{
    //热搜词获取
    public function gethot($catid){
        $where = '';
        if ($catid > 0) {
            $where['classify_id'] = $catid;
        }
        $HotModel = new \model\shop_hottopic_model();
        $info = $HotModel->getAllShopHot($where,'*',"rand()");
        if($info){
            $hottopic = '';
            foreach ($info as $v){
                $hottopic .= $v['hottopic'].";";
            }
            if ($hottopic){
                $hottopic = rtrim($hottopic,";");
                $hottopic = explode(';', $hottopic);
                if (count($hottopic) > 16){
                    $num = 20;
                }else {
                    $num = count($hottopic);
                }
                for ($i = 0; $i < $num; $i++){
                    $data[$i] = $hottopic[$i];
                }
            }
        }
        
        return $data;
    }
    /**
     * 取一个活动未过期的时间段
     * @param $ac_list_id 区域码
     * @param $flag 为空则取当天的未过期，不为空则取第二天的
     */
    public function getActArea($ac_list_id,$flag=''){
        $actAreaModel = new \model\activity_area_model();
        //取时间段
        $activty_area = $actAreaModel->getAllActArea(array('ac_list_id'=>$ac_list_id));
        if (empty($activty_area)) return '';
        if (empty($flag)){
            $mydate = date("Y:m:d");
        }else {
            $mydate = date("Y-m-d",strtotime("+1 day"));
        }
        foreach ($activty_area as $key=>$val){
            $startDate = $mydate." ".date('H:i:s',$val['ac_area_time_str']);
            $endDate = $mydate." ".date('H:i:s',$val['ac_area_time_end']);
            $starttime = strtotime($startDate);
            $endtime = strtotime($endDate);
            if ($endtime <= time()) continue;//过期的筛选出
            $temp['ac_area_id'] = $val['ac_area_id'];
            $temp['ac_area_time_str'] = date("H:i",$starttime);
            $temp['ac_area_time_end'] = date("H:i",$endtime);
            $temp['status'] = 0;
            if (time() >= $starttime && time() <= $endtime){
                $temp['status'] = 1;
                $temp['section'] = $endtime-time();
            }else{
                $temp['section'] = $starttime-time();
            }
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 取当前活动的未过期的时间段
     * @param $ac_list_id 区域码
     * @num 取的个数
     *   */
    public function getActAreaNoExp($ac_list_id,$num = 0){
        if (empty($ac_list_id)) return '';
        $list = $this->getActArea($ac_list_id);
        if($list){
            if ($num == 0) return $list;
            
            if (count($list) >= $num){
                for ($i=0;$i<$num;$i++){
                    $return[$i] = $list[$i];
                }
            }else {
                $other = $num - count($list);
                $moreData = $this->getActArea($ac_list_id,1);
                if ($moreData){
                    for ($i=0;$i<$other;$i++){
                        $return1[$i] = $moreData[$i];
                    }
                }
                $return = array_merge($list,$return1);
            }
            return $return;
        } 
                
    }
}