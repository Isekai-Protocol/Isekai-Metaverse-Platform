<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class UserCard extends Common
{	
	// 用户购买卡券
    public function addCard($user_id,$number = 1)
    {
        $orderModel = new \app\common\model\Order();
    	for ($i=0; $i < $number; $i++) { 
    		$card_data = [
    			'user_id' => $user_id,
    			'code' => makeCode(),
    		];

            $card_id = $this->insertGetId($card_data);
            if($card_id){
                $res = $orderModel->makeOrder($card_id,$user_id,6);
                // dump($res);
            }
    	}

    	return ['status' => true,'msg' => '购买成功'];
    }

}
