<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class UserSale extends Common
{
    	
    // 用户寄售和拍卖
	public function saleMyWorks($user_id,$params)
	{
		$sale_data = [
			'user_id' => $user_id,
			'type' => $params['sale_type'],//类型:1公开招标2固定价格3英式拍卖4荷兰拍卖
			// 'wallet_url' => $params['wallet_url'],
			// 'royalties' => $params['royalties'],
			// 'is_binding' => $params['is_binding'],
			'price' => $params['price'],
			'start_time' => strtotime($params['start_time']),
			'end_time' => strtotime($params['end_time']),
			'obj_id' => $params['id'],
			'price_type' => $params['price_type'],//1.以太币2平台币
			'obj_type' => $params['type'],//销售物品类型:1文章2音频3游戏4插画
			'gas_fee' => $params['gas_fee'],//服务费
			'fee_total' => $params['fee_total']//费用合计
		];

		if(empty($params['price']) || $params['price'] <= 0){
			return error_code(13328);
		}

		// if(empty($params['wallet_url'])){
		// 	return error_code(13329);
		// }

		// if(empty($params['royalties'])){
		// 	return error_code(13330);
		// }

		if(empty($params['end_time'])){
			return error_code(13331);
		}

		if(strtotime($params['start_time']) >= strtotime($params['end_time'])){
			return error_code(13332);
		}

		if(isset($params['sale_id']) && !empty($params['sale_id'])){
			// 编辑
			$old_price = $this->where('id',$params['sale_id'])->value('price');
			if($params['sale_type'] == 2 && $params['price'] >= $old_price){
				return error_code(10207);
			}

			$this->where('id',$params['sale_id'])->update($sale_data);
			$sale_id = $params['sale_id'];
		}else{
			// 新增
			$sale_id = $this->insertGetId($sale_data);
		}

		// 固定价格-授权寄售
		$wallect_mod = new \app\common\model\userWallet();
		if($params['sale_type'] == 2){
			$level = 2;
			if($params['type'] == 5){
				$level = 1;
			}

			$user_wallect = $wallect_mod->where('user_id',$user_id)->value('address');
			setApprovalForAll($user_wallect,$level);
		}else{
			// 拍卖
		}

		// 将商品发布市场
		$article = new \app\common\model\Article();
		$music = new \app\common\model\Files();
		$img = new \app\common\model\Pictures();
		$game = new \app\common\model\Game();
		$Goods = new \app\common\model\Goods();
		$work_mod = new \app\common\model\UserWorks();

		if($params['type'] == 1){
			// 文章
			$article->where('id',$params['id'])->update(['is_market' => 1,'price' => $params['price']]);
		}elseif($params['type'] == 2){
			// 音频
			$music->where('id',$params['id'])->update(['is_market' => 1,'price' => $params['price']]);
		}elseif($params['type'] == 3){
			// 游戏
			$game->where('g_id',$params['id'])->update(['is_market' => 1,'price' => $params['price']]);
		}elseif($params['type'] == 4){
			// 插画
			$img->where('id',$params['id'])->update(['is_market' => 1,'price' => $params['price']]);
		}else{
			// 角色
			$Goods->where('id',$params['id'])->update(['is_market' => 1,'price' => $params['price']]);
		}

		$work_mod->where(['obj_id' => $params['id'],'type' => $params['type']])->update(['is_market' => 1]);
		// $sale_goods_mod = new \app\common\model\UserSaleGoods();
		// if($params['is_binding'] && $sale_id){	
		// 	// 先删后加
		// 	$sale_goods_mod->where('sale_id',$sale_id)->delete();
		// 	$sale_goods = [];
		// 	if(isset($params['sale_arr']) && !empty($params['sale_arr'])){
		// 		foreach ($params['sale_arr'] as $key => $value) {
		// 			$temp_goods = [
		// 				'sale_id' => $sale_id,
		// 				'obj_id' => $value['id'],
		// 				'type' => $value['type']
		// 			];

		// 			$sale_goods[] = $temp_goods;
		// 		}

		// 		$sale_goods_mod->insertAll($sale_goods);
		// 	}
			
		// }else{
		// 	// 解除捆绑清空数据
		// 	$sale_goods_mod->where('sale_id',$sale_id)->delete();
		// }

		return ['status' => true,'msg' => ''];
	}

	// 获取寄售和拍卖数据
	public function getSaleMyWorks($user_id,$sale_id)
	{
		$info = $this->where('id',$sale_id)->find();
		$userWallet = new \app\common\model\userWallet();
		$user = new \app\common\model\User();

		$wallet_info = $userWallet->where('id',$user_id)->find();

		if(empty($info)){
			return error_code(10002);
		}

		$sale_data = [
			'sale_id'	=> $sale_id, 
			'user_id' => $user_id,
			'sale_type' => $info['type'],//类型:1公开招标2固定价格3英式拍卖4荷兰拍卖
			'wallet_url' => $wallet_info['wallet_url'],
			'royalties' => $user->where('id',$user_id)->value('royalties'),
			// 'is_binding' => $info['is_binding'],
			'price' => $info['price'],
			'start_time' => date('Y-m-d H:i:s', $info['start_time']),
			'end_time' => date('Y-m-d H:i:s', $info['end_time']),
			'id' => $info['obj_id'],
			'price_type' => $info['price_type'],
			'type' => $info['obj_type'],//销售物品类型:1文章2音频3游戏4插画5角色
			'gas_fee' => $info['gas_fee'],//服务费
			'fee_total' => $info['fee_total'],//费用合计
		];
		// $sale_goods_mod = new \app\common\model\UserSaleGoods();

		// $sale_arr = $sale_goods_mod->where('sale_id',$info['id'])->select();
		// if(!empty($sale_arr)){
			// $article = new \app\common\model\Article();
			// $music = new \app\common\model\Files();
			// $img = new \app\common\model\Pictures();
			// $game = new \app\common\model\Game();

			// if($info['obj_type'] == 1){
			// 	// 文章
			// 	$sale_temp = $article->where('id',$info['obj_id'])->find();
			// 	// $info['wallet_url'] = $sale_temp['wallet_url'];
			// 	// $info['royalties'] = $sale_temp['royalties'];
			
			// }elseif($info['obj_type'] == 2){
			// 	// 音频
			// 	$sale_temp = $music->where('id',$info['obj_id'])->find();
			// 	// $info['wallet_url'] = $sale_temp['wallet_url'];
			// 	// $info['royalties'] = $sale_temp['royalties'];
			
			// }elseif($info['obj_type'] == 3){
			// 	// 游戏
			// 	$sale_temp = $game->where('g_id',$info['obj_id'])->find();
			// 	// $info['wallet_url'] = $sale_temp['wallet_url'];
			// 	// $info['royalties'] = $sale_temp['royalties'];
			
			// }elseif($info['obj_type'] == 4){
			// 	// 插画
			// 	$sale_temp = $img->where('id',$info['obj_id'])->find();
			// 	// $info['wallet_url'] = $sale_temp['wallet_url'];
			// 	// $info['royalties'] = $sale_temp['royalties'];
			// }else{
			// 	// 角色
			// }
		// 	$list = [];
		// 	foreach ($sale_arr as $key => $value) {
		// 		if($value['type'] == 1){
		// 			// 文章
		// 			$a_temp = $article->where('id',$value['obj_id'])->field('id,title as name,code,price,cover,is_market')->find();
		// 			$a_new = [
		// 				'id' => $a_temp['id'],
		// 				'name' => $a_temp['name'],
		// 				'code' => $a_temp['code'],
		// 				'price' => $a_temp['price'],
		// 				'cover' => _sImage($a_temp['cover']),
		// 				'is_market' => $a_temp['is_market'],
		// 				'type' => 1,
		// 				'url' => ''
		// 			];
					
		// 			$list[] = $a_new;
		// 		}elseif($value['type'] == 2){
		// 			// 音频
		// 			$m_temp = $music->where('id',$value['obj_id'])->field('id,name,url,path as cover,code,price,is_market')->find();
		// 			$m_new = [
		// 				'id' => $m_temp['id'],
		// 				'name' => $m_temp['name'],
		// 				'code' => $m_temp['code'],
		// 				'price' => $m_temp['price'],
		// 				'cover' => _sImage($m_temp['cover']),
		// 				'is_market' => $m_temp['is_market'],
		// 				'url' => _sImage($m_temp['url']),
		// 				'type' => 2
		// 			];

		// 			$list[] = $m_new;
				
		// 		}elseif($value['type'] == 3){
		// 			// 游戏
		// 			$g_temp = $game->where('g_id',$value['obj_id'])->field('g_id as id,game_name as name,cover,code,price,is_market')->find();
		// 			$g_new = [
		// 				'id' => $g_temp['id'],
		// 				'name' => $g_temp['name'],
		// 				'code' => $g_temp['code'],
		// 				'price' => $g_temp['price'],
		// 				'cover' => _sImage($g_temp['cover']),
		// 				'is_market' => $g_temp['is_market'],
		// 				'url' => '',
		// 				'type' => 3
		// 			];

		// 			$list[] = $g_new;
		// 		}else{
		// 			// 插画
		// 			$p_temp = $img->where('id',$value['obj_id'])->field('id,name,cover,code,price,is_market')->find();
		// 			$p_new = [
		// 				'id' => $p_new['id'],
		// 				'name' => $p_new['name'],
		// 				'code' => $p_new['code'],
		// 				'price' => $p_new['price'],
		// 				'cover' => _sImage($p_new['cover']),
		// 				'is_market' => $p_new['is_market'],
		// 				'url' => '',
		// 				'type' => 4
		// 			];

		// 			$list[] = $p_new;
		// 		}

		// 	}

		// 	$info['list'] = $list;
		// }

		return ['status' => true,'msg' => '成功','data' => $sale_data];
	}

	// 取消招标
	public function cancelSale($user_id = '',$sale_id = '')
	{	
		if(empty($sale_id)){
			return error_code(10002);
		}

		$info = $this->where('id',$sale_id)->find();

		if(empty($info)){
			return error_code(10002);
		}else{
			// 将商品从市场下架
			$article = new \app\common\model\Article();
			$music = new \app\common\model\Files();
			$img = new \app\common\model\Pictures();
			$game = new \app\common\model\Game();
			$goods = new \app\common\model\Goods();
			$work_mod = new \app\common\model\UserWorks();

			if($info['obj_type'] == 1){
				// 文章
				$article->where('id',$info['obj_id'])->update(['is_market' => 0,'price' => 0]);
			}elseif($info['obj_type'] == 2){
				// 音频
				$music->where('id',$info['obj_id'])->update(['is_market' => 0,'price' => 0]);
			}elseif($info['obj_type'] == 3){
				// 游戏
				$game->where('g_id',$info['obj_id'])->update(['is_market' => 0,'price' => 0]);
			}elseif($info['obj_type'] == 4){
				// 插画
				$img->where('id',$info['obj_id'])->update(['is_market' => 0,'price' => 0]);
			}else{
				// 角色
				$goods->where('id',$info['obj_id'])->update(['is_market' => 0,'price' => 0]);
			}

			$work_mod->where(['obj_id' => $info['obj_id'],'type' => $info['obj_type']])->update(['is_market' => 0]);
			$res = $this->where('id',$sale_id)->update(['is_cancel' => 1,'status' => 2]);

			return $res ? ['status' => true,'msg' => '下架成功'] : error_code(10021);
		}

	}
}
