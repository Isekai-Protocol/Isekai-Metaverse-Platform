<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class Business extends Common
{
    
	// 同意出价
	public function agreePrice($log_id = '',$user_id = '')
	{
		if(empty($log_id)){
			return error_code(10003);
		}

		// 判断
		$price_mod = new \app\common\model\UserPrice();
		$sale_mod = new \app\common\model\UserSale();

		$log_data = $price_mod->where('id',$log_id)->find();

		if(empty($log_data)){
			return error_code(10002);
		}

		if($user_id != $log_data['receive_id']){
			return error_code(10010);
		}

		// 下单
		$order_mod = new \app\common\model\Order();
		$res = $order_mod->makeOrder($log_data['p_id'],$log_data['user_id'],$log_data['type']);

		if($res){
			// 删除出价
			$price_mod->where('id',$log_id)->delete();

			// 修改交易状态
			$sale_mod->where(['obj_id' => $log_data['p_id'],'obj_type' => $log_data['type']])->update(['status' => 1]);

			// 改变作品所属
			// $articleModel = new \app\common\model\Article();
			// $musicModel = new \app\common\model\Files();
			// $gameModel = new \app\common\model\Game();
			// $imgModel = new \app\common\model\Pictures();

			// if($log_data['type'] == 1){
			// 	$articleModel->where('id',$log_data['p_id'])->update(['user_id' => $log_data['user_id']]);
			// }elseif($log_data['type'] == 2){
			// 	$musicModel->where('id',$log_data['p_id'])->update(['user_id' => $log_data['user_id']]);
			// }elseif($log_data['type'] == 3){
			// 	$gameModel->where('g_id',$log_data['p_id'])->update(['user_id' => $log_data['user_id']]);
			// }else{
			// 	$imgModel->where('id',$log_data['p_id'])->update(['user_id' => $log_data['user_id']]);
			// }

			// 交易记录
			$business_data = [
				'user_id' => $log_data['user_id'],
				'price' => $log_data['price'],
				'receive_id' => $user_id,
				'obj_id' => $log_data['p_id'],
				'type' => $log_data['type']
			];

			$res = $this->insert($business_data);
			// makeMyNft($log_data['p_id'],$log_data['type'],$log_data['user_id']);

			return $res ? ['status' => true,'msg' => '成功'] : error_code(10018);
		}
		
	}

	/**
	 * @param $post
	 *
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
	public function tableData($post)
	{
	    if(isset($post['limit'])){
	        $limit = $post['limit'];
	    }else{
	        $limit = config('paginate.list_rows');
	    }
	   
	    $list = $this->order('id desc')->paginate($limit);
	    $user_mod = new \app\common\model\User();
	    $article_mod = new \app\common\model\Article();
	    $music_mod = new \app\common\model\Files();
	    $game_mod = new \app\common\model\Game();
	    $img_mod = new \app\common\model\Pictures();
	    $role_mod = new \app\common\model\Goods();

	    foreach($list as $key => $val)
	    {
	        $list[$key]['buy_name'] = $user_mod->where('id',$val['user_id'])->value('username');
	        $list[$key]['sale_name'] = $user_mod->where('id',$val['receive_id'])->value('username');
	        if($val['type'] == 1){
	        	$list[$key]['bus_type'] = '小说';
	        	$list[$key]['obj_name'] = $article_mod->where('id',$val['obj_id'])->value('title');
	        }elseif($val['type'] == 2){
	        	$list[$key]['bus_type'] = '音频';
	        	$list[$key]['obj_name'] = $music_mod->where('id',$val['obj_id'])->value('name');
	        }elseif($val['type'] == 4){
	        	$list[$key]['bus_type'] = '插画';
	        	$list[$key]['obj_name'] = $img_mod->where('id',$val['obj_id'])->value('name');
	        }elseif($val['type'] == 5){
	        	$list[$key]['bus_type'] = '角色';
	        	$list[$key]['obj_name'] = $role_mod->where('id',$val['obj_id'])->value('name');
	        }else{
	        	$list[$key]['bus_type'] = '游戏';
	        	$list[$key]['obj_name'] = $game_mod->where('g_id',$val['obj_id'])->value('name');
	        }

	        // $list[$key]['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
	        
	    }
	    $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
	    $re['code'] = 0;
	    $re['msg'] = '';
	    $re['count'] = $list->total();
	    $re['data'] = $data;
	    return $re;
	}
}
