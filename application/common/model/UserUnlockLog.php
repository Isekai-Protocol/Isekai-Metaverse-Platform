<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class UserUnlockLog extends Common
{
    
	// 解锁内容
	public function unlockContent($id = '',$type = 1,$user_id = '')
	{
		$work_mod = new \app\common\model\UserWorks();
		$wallect_mod = new \app\common\model\userWallet();

		$price = $work_mod->where(['obj_id' => $id,'type' => $type,'experience_type' => 2])->value('experience_price');
		if(!$price){
			return error_code(10002);
		}
		
		$rate = getSetting('rate_fee');
		$price = $price;

		$user_balance = $wallect_mod->where(['user_id' => $user_id,'type' => 1])->value('balance');
		if(empty($user_balance) || $user_balance < $price){
			// return error_code(10211);
			$user_balance = $wallect_mod->where(['user_id' => $user_id])->value('balance');
			if(empty($user_balance) || $user_balance < $price){
				return error_code(10211);
			}
		}

		// 扣除费用
		$wallect_mod->where(['user_id' => $user_id,'type' => 1])->setDec('balance',$price);

		// 扣除作者手续费
		$worker = $work_mod->where(['obj_id' => $id,'type' => $type])->find();
		if($worker){
			$wallect_mod->where(['user_id' => $worker['user_id'],'type' => 1])->setDec('balance',($price*$rate));
		}

		$lock_data = [
			'user_id' => $user_id,
			'nft_id' => $id,
			'type' => $type,
			'price' => $price
		];

		$res = $this->insert($lock_data);
		addBuyLog($id,$type,$user_id,1,$price);

		return $res ? ['status' => true,'msg' => '成功'] : error_code(10018);
	}
}
