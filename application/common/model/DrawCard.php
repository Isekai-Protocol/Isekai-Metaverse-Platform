<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class DrawCard extends Common
{

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
        $tableWhere = $this->tableWhere($post);
        $list = $this->where($tableWhere['where'])->paginate($limit);
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        // dump($this->getLastSql());die;
        foreach ($data as $key => $value) {
            $data[$key]['stime'] = date('Y-m-d H:i:s',$value['stime']);
            $data[$key]['etime'] = date('Y-m-d H:i:s',$value['etime']);
        }
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        return $re;
    }


    /**
     *  添加文章数据   方法
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        $theDate = explode(' 到 ',$data['utime']);
        $theDate2 = explode(' 到 ',$data['stime']);
        if(count($theDate) == 2){
            $data['stime'] = strtotime($theDate[0]);
            $data['etime'] = strtotime($theDate[1]);
        }
        if(count($theDate2) == 2){
            $data['start_time'] = strtotime($theDate2[0]);
            $data['end_time'] = strtotime($theDate2[1]);
        }
        if (!$this->allowField(true)->save($data))
        {
            $result['status'] = false;
            $result['msg'] = error_code(10004,true);
        }

        return $result;
    }


    /**
     *  文章编辑更新
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        $theDate = explode(' 到 ',$data['utime']);
        $public_time = $data['stime'];
        if(count($theDate) == 2){
            $data['stime'] = strtotime($theDate[0]);
            $data['etime'] = strtotime($theDate[1]);
        }else{
            $data['stime'] = 0;
            $data['etime'] = 0;
        }

        $theDate2 = explode(' 到 ',$public_time);

        if(count($theDate2) == 2){
            $data['start_time'] = strtotime($theDate2[0]);
            $data['end_time'] = strtotime($theDate2[1]);
        }else{
            $data['start_time'] = 0;
            $data['end_time'] = 0;
        }

        $card_data = [
            'version' => $data['version'],
            'desc' => $data['desc'],
            'rule' => $data['rule'],
            'stime' => $data['stime'],
            'etime' => $data['etime'],
            'title' => $data['title'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'white_limit' => $data['white_limit'],
            'public_limit' => $data['public_limit'],
        ];
        if(!$this->where('id',$data['id'])->update($card_data))
        {
            $result['status'] = false;
            $result['msg'] = error_code(10004,true);
        }

        return $result;
    }


    /**
     * where 搜索条件
     * author: tianyu
     * @param $post
     *
     * @return mixed
     */
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['version']) && $post['version'] != ""){
            $where[] = ['version', 'eq', $post['version']];
        }

        if(input('?param.date')){
            $theDate = explode(' 到 ',input('param.date'));
            if(count($theDate) == 2){
                $where[] = ['stime', '>=', strtotime($theDate[0])];
                $where[] = ['etime', '<=', strtotime($theDate[1])];
            }
        }

        $result['where'] = $where;
        $result['field'] = "*";
        return $result;
    }


    /**
     * 文章搜索
     * @param $search_name
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search($search_name, $page = 1, $limit = 10){

        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        // 发布状态
        $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $where[] = ['title|brief', 'like', '%'.$search_name.'%'];

        $list = $this->where($where)
            ->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv,brief')
            ->order('sort asc')
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)->count();

        if(!$list->isEmpty())
        {
            $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']);
                $v['ctime'] = getTime($v['ctime']);
            }
        }

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
        ];

        return $result;

    }

    // 删除
    public function remove($id = '')
    {
        return $this->where('id',$id)->delete();
    }

    // 获取抽卡配置
    public function getConfig()
    {
        $data = [];

        $config = $this->find();
        $config['cover'] = _sImage($config['cover']);
        // $config['stime'] = date('Y-m-d H:i:s',$config['stime']);
        // $config['etime'] = date('Y-m-d H:i:s',$config['etime']);
        // $config['start_time'] = date('Y-m-d H:i:s',$config['start_time']);
        // $config['end_time'] = date('Y-m-d H:i:s',$config['end_time']);

        $roleModel = new \app\common\model\CardRole();
        $goodsModel = new \app\common\model\Goods();
        $logModel = new \app\common\model\DrawLog();
        $powerModel = new \app\common\model\WallectPower();

        // 数据统计
        // 统计角色总数
        $config['role_total'] = $goodsModel->count();
        // 统计已抽角色
        $config['lose_role'] = $logModel->count();

        // 白名单总数
        $config['white_total'] = $powerModel->count()*$config['white_limit'];
        $config['white_success'] = 0;

        // 公售总数
        $config['public_total'] = $config['role_total']-$config['white_total'];
        $config['public_success'] = $config['lose_role']-$config['white_success'];

        // 获取卡券信息
        $card_config = getMultipleSetting('card_img,card_price');
        $config['card_img'] = _sImage($card_config['card_img']);
        $config['card_price'] = $card_config['card_price'];

        // 格式化时间
        $config['white_time_txt'] = '';
        $config['public_time_txt'] = '';

        if(time() > $config['etime']){
            $config['white_time_txt'] = '已结束';
        }else{
            if(time() > $config['stime']){
                $time_com = $config['etime'] - time();
                $config['white_time_txt'] = '结束于'.secondConversion($time_com);
            }else{
                $time_com = $config['stime'] - time();
                $config['white_time_txt'] = '开始于'.secondConversion($time_com);
            }
        }

        if(time() > $config['end_time']){
            $config['public_time_txt'] = '已结束';
        }else{
            if(time() > $config['start_time']){
                $time_com_2 = $config['end_time']-time();
                $config['public_time_txt'] = '结束于'.secondConversion($time_com_2);
            }else{
                $time_com_2 = $config['stime']-time();
                $config['public_time_txt'] = '开始于'.secondConversion($time_com_2);
            }
        }

        // $roleList = $roleModel->select();

        // foreach ($roleList as $key => $value) {
        //     $roleList[$key]['role_info'] = $goodsModel->getRoleDetial($value['goods_id'])['data'];
        // }

        $data = [
            'config' => $config,
            // 'list' => $roleList
        ];

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data' => $data
        ];
    }

    // 用户抽卡
    public function userDraw($user_id = '')
    {
        $roleModel = new \app\common\model\CardRole();
        $drawModel = new \app\common\model\DrawCard();
        $logModel = new \app\common\model\DrawLog();
        $orderModel = new \app\common\model\Order();
        $goodsModel = new \app\common\model\Goods();
        $orderItemModel = new \app\common\model\OrderItems();
        $now_time = time();

        // 消耗卡券
        $card_mod = new \app\common\model\UserCard();
        $card_id = $card_mod->where(['user_id'=>$user_id,'is_used'=>0])->value('id');
        // if(empty($card_id)){
        //     return error_code(12025);
        // }

        // $card_mod->where('id',$card_id)->update(['is_used'=>1,'use_time' => date('Y-m-d H:i:s',$now_time)]);

        // 删除卡券购买记录
        // $orderItemModel->where(['goods_id'=>$card_id,'type' => 6])->delete();

        $draw_version = $drawModel->where('stime','<=',$now_time)->where('etime','>',$now_time)->column('version');
        // dump($drawModel->getLastSql());die;
        // dump($draw_version);die;
        $draw_ids = $goodsModel->whereIn('post_version',$draw_version)->column('id');
        // dump($draw_ids);die;
        $ids = $logModel->column('goods_id');
        // $where[] = ['goods_id','in',$draw_ids];

        // if($ids){
        //     $where[] = ['goods_id','not in',$ids];//排除已抽过的角色
        // }

        $ids = array_diff($draw_ids,$ids);
        // if(!empty($ids)){
        $where[] = ['goods_id','in',$ids];
        // }else{
        //     return error_code(10212);
        // }

        $list = $roleModel->select();
        if($list){
            $new_data = [];
            foreach ($list as $key => $value) {
                $new_data[$value['goods_id']] = $value['chance'];
            }
        }else{
            return error_code(10212);
        }

        $res = randomSelect($new_data);

        // 记录抽卡记录
        $log_data = [
            'user_id' => $user_id,
            'goods_id' => $res,
            'ctime' => time(),
            'draw_id' => 1
        ];

        $logModel->insert($log_data);


        // 下单
        $orderModel->makeOrder($res,$user_id,5);

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data' => $res
        ];
    }
}
