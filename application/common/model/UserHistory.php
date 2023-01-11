<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class UserHistory extends Common
{
    /**
     * 按天统计当天会员操作
     * @param $day
     * @return array
     */
    public function statisticsOrder($day,$type = 1)
    {
        $res        = [];
        for ($i = 0; $i < $day; $i++) {
            $where    = [];
            if($type == 2){
                $where[] = ['user_id','>',0];
            }
            $curr_day = date('Y-m-d');
            if ($i == 0) {
                $where[]  = ['ctime', '<', time()];
                $curr_day = date('Y-m-d');
            } else {
                $where[]  = ['ctime', '<', strtotime(date("Y-m-d", strtotime("-" . $i . " day")) . ' 23:59:59')];
                $curr_day = date("Y-m-d", strtotime("-" . $i . " day"));
            }
            $where[] = ['ctime', '>=', strtotime(date("Y-m-d", strtotime("-" . $i . " day")) . ' 00:00:00')];
            if($type == 2){
                $res[]   =
                    [
                        'nums' => $this->where($where)->group('user_id')->count(),
                        'day'  => $curr_day
                    ];
                }else{
                    $res[]   =
                        [
                            'nums' => $this->where($where)->group('user_id')->count()+$this->where($where)->group('http_cookie')->count(),
                            'day'  => $curr_day
                        ];
                }
            
        }

        $data = get_lately_days($day, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }

    // 页面访问统计
    public function getPageView($type = 1)
    {

        // $need_page = ['10002001','10002013','10002016','10002017','10002018','10002019','10002036'];
    
        if($type == 1){
            // 全部用户
            $page_1 = $this->where('page_id','10002001')->count();
            $page_2 = $this->where('page_id','10002013')->count();
            $page_3 = $this->where('page_id','10002016')->count();
            $page_4 = $this->where('page_id','10002017')->count();
            $page_5 = $this->where('page_id','10002018')->count();
            $page_6 = $this->where('page_id','10002019')->count();
            $page_7 = $this->where('page_id','10002036')->count();
        }else{
            // 独立用户
            $page_1 = $this->where('page_id','10002001')->group('http_cookie')->count();
            $page_2 = $this->where('page_id','10002013')->group('http_cookie')->count();
            $page_3 = $this->where('page_id','10002016')->group('http_cookie')->count();
            $page_4 = $this->where('page_id','10002017')->group('http_cookie')->count();
            $page_5 = $this->where('page_id','10002018')->group('http_cookie')->count();
            $page_6 = $this->where('page_id','10002019')->group('http_cookie')->count();
            $page_7 = $this->where('page_id','10002036')->group('http_cookie')->count();
        }
        

        $page = [$page_1,$page_2,$page_3,$page_4,$page_5,$page_6,$page_7];

        $page_name = ['品牌直售','推荐','专辑','插画','音频','文章','游戏'];

        return ['page' => $page_name, 'data' => $page];
    }
}
