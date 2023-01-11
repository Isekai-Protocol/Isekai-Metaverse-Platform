<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class MiningLog extends Common
{
    
	// 添加挖矿记录
	public function addUserMining($user_id = '')
	{
		$time_mod = new \app\common\model\UserViewTime();
		$wallect_mod = new \app\common\model\userWallet();
		// 判断用户今日是否已经挖过矿

		// 获取今日时间
		$start_today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$end_today = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

		$is_send = $this->where('ctime','between',[$start_today,$end_today])->find();

		// 获取配置
		
		// 矿总量
		$bin_total = !empty(getSetting('coin_total')) ? getSetting('coin_total') : 0;
		// 统计当前发放数量
		$now_count = $this->sum('number');

		if($now_count >= $bin_total){
			// 达到发币总量，停止发放
			return false;
		}

		//1.点击次数
		$click_num = !empty(getSetting('click_num')) ? getSetting('click_num') : 0;
		// 2.停留时长
		$stay_time = !empty(getSetting('stay_time')) ? getSetting('stay_time')*60 : 0;

		// 发矿范围
		// 1.开始范围
		$start_val = !empty(getSetting('currency_s')) ? getSetting('currency_s') : 0;
		// 2.结束范围
		$end_val = !empty(getSetting('currency_e')) ? getSetting('currency_e') : 0;

		if($is_send){
			return false;//今日已挖过矿
		}else{
			// 推荐页面停留时长超过10分钟
			$used_time = $time_mod->where('user_id',$user_id)->where('enter_time','between',[$start_today,$end_today])->where('page_id',10002013)->sum('used_time');
// 			dump($used_time);
			// 10分钟 == 600秒
			
			// 需要30次的点击行为
			$click_count = $time_mod->where('user_id',$user_id)->where('enter_time','between',[$start_today,$end_today])->count();
// 			dump($click_count);die;
			if($used_time >= $stay_time && $click_count >= $click_num){
				// 发矿
				$price = rand($start_val,$end_val);
                // dump($price);die;
				$wallect_id = $wallect_mod->where(['user_id' => $user_id,'type' => 1])->find();
				// dump($wallect_id);die;
				if($wallect_id){
					$res = $wallect_mod->where('id',$wallect_id['id'])->setInc('balance',$price);
					$log_data = [
						'user_id' => $user_id,
						'number' => $price,
						'ctime' => time()
					];
					$this->insert($log_data);
					
				}
			}	

		}

	}
}
