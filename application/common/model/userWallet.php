<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class userWallet extends Common
{
    // 保存用户钱包
	public function saveUserWallet($address = '',$user_id = '',$private_key = '',$mnemonic_word = '',$public_key = '')
	{
		// 加密串
		$code = getRandStr();

		$key = str_split($private_key, strlen($private_key)/2);

		$key_1 = $key[0];
		$key_2 = $key[1];

		$data = [
			'user_id' => $user_id,
			'address' => $address,
			'mnemonicWord' => $mnemonic_word,
			'privateKey_1' => eccodeStr($key_1,$code),
			'privateKey_2' => eccodeStr($key_2,$code),
			'salt' => $code,
			'publicKey' => $public_key,
			'ctime' => time(),
			'type' => 1,
			'is_default' => 1
		];

		return $this->insert($data);
	}

}
