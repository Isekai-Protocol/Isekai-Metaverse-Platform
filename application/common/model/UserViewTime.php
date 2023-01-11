<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class UserViewTime extends Common
{
    
	// 页面停留时长统计
	public function getPageTime()
	{

	    // $need_page = ['10002001','10002013','10002016','10002017','10002018','10002019','10002036'];
	
        // 全部用户
        $page_1 = $this->where('page_id','10002001')->sum('used_time');
        $page_2 = $this->where('page_id','10002013')->sum('used_time');
        $page_3 = $this->where('page_id','10002016')->sum('used_time');
        $page_4 = $this->where('page_id','10002017')->sum('used_time');
        $page_5 = $this->where('page_id','10002018')->sum('used_time');
        $page_6 = $this->where('page_id','10002019')->sum('used_time');
        $page_7 = $this->where('page_id','10002036')->sum('used_time');
	    
	    

	    $page = [$page_1,$page_2,$page_3,$page_4,$page_5,$page_6,$page_7];

	    $page_name = ['品牌直售','推荐','专辑','插画','音频','文章','游戏'];

	    return ['page' => $page_name, 'data' => $page];
	}

	// 页面点击次数
	public function getClickData($post)
	{
		
		$page_name = config('front.');
		
		$data = [];

		foreach ($page_name as $key => $value) {
			$data[] = [
				'page_name' => $value['page_name'],
				'total' => $this->where('page_name',$value['page_name'])->count()
			];
			
			if($post['limit'] == $key+1){
				break;
			}
			
		}
       	

       	return ['code' => 0,'count' => count($page_name),'data' => $data,'msg' => ''];
	}
}
