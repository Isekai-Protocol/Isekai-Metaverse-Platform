<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;
use app\common\model\ArticleType as articleTypeModel;

class Game extends Common
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
        $list = $this->where($tableWhere['where'])->order('g_id desc')->paginate($limit);

        $cate_mod = new articleTypeModel();
        $workModel = new \app\common\model\UserWorks();

        foreach($list as $key => $val)
        {
            $list[$key]['cover'] = _sImage($val['cover']);
            $list[$key]['type_name'] = $cate_mod->where('id',$val['cate_id'])->value('type_name');
            $list[$key]['status'] = $workModel->where('obj_id',$val['g_id'])->where('type',3)->value('is_draft');
        }
        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
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
       
        if(!empty($data['labels'])){
            $data['labels'] = serialize($data['labels']);
        }
        
        $data['code'] = makeCode();
        unset($data['editorValue']);
        unset($data['labels']);
        unset($data['validate_form']);
        unset($data['__Jshop_Token__']);
        $id = $this->insertGetId($data);

        if (!$id)
        {
            $result['status'] = false;
            $result['msg'] = error_code(10004,true);
        }
        addPlatformDraft($id,3);

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
        
        if(!$this->allowField(true)->save($data,['g_id'=>$data['g_id']]))
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
        if(isset($post['title']) && $post['title'] != ""){
            $where[] = ['game_name', 'like', '%'.$post['title'].'%'];
        }
        
        if(isset($post['type_id']) && $post['type_id'] != ""){
            $where[] = ['cate_id', 'eq', $post['type_id']];
        }
        
        $workModel = new \app\common\model\UserWorks();
        // 草稿不显示
        $ids = $workModel->where(['type' => 3,'is_draft' => 1])->column('obj_id');
        if(!empty($ids)){
            $where[] = ['id', 'NOT IN', $ids];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        return $result;
    }


    /**
     * 获取游戏列表
     * @param bool $type_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGameList($type_id = 0, $page = 1, $limit = 10,$user_id = '',$type = -1,$min_price = 0,$max_price = 0,$status = -1,$sort = -1)
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        $cateModel = new ArticleType();
        $label = new \app\common\model\Label();

        // $type_name = "游戏分类";
        // if($type_id != 0){
        //     $info = $gameCateModel->where('c_id',$type_id)->find();
        //     if($info){
        //         $type_name = $info['cate_name'];
        //     }
        // }

        $where = [];
        if($min_price >= 0 && !empty($max_price)){
            $where[] = ['price','between',[$min_price,$max_price]];
        }
        // 发布状态
        // if($type != -1){
        //     $where[] = ['is_market', 'eq', $type];
        // }
        
        if(!empty($type_id)){
             $where[] = ['cate_id', 'eq', $type_id];
        }

        $workModel = new \app\common\model\UserWorks();
        $versionModel = new \app\common\model\GameVersion();
        // $work_ids = $workModel->where(['is_draft'=>2,'type' => 3])->column('obj_id');
        // if(!empty($work_ids)){
        //     $where[] = ['g_id','in',$work_ids];
        // }
        $s_ids = nftStatus(3,$status,$type);
        // dump($ids);die;

        // if($ids){
        //     $where[] = ['g_id','in',$ids];
        // }else{
        //     $result = [
        //         'list' => [],
        //         'count' => 0,
        //         'page' => $page,
        //         'limit' => $limit,
        //         // 'article_type' => $articleTypeList,
        //         // 'type_name' => $type_name
        //     ];

        //     return $result;
        // }

        if(!$s_ids){
            
            $result = [
                'list' => [],
                'count' => 0,
                'page' => $page,
                'limit' => $limit,
                // 'article_type' => $articleTypeList,
                // 'type_name' => $type_name
            ];

            return $result;
        }
        
        if($type != -1){
            $ids = getMarketData($type,3);
            if(!empty($ids)){
                $ids = array_intersect($s_ids,$ids);
                // if($is_market == 1){
                    // 市场
                    $where[] = ['g_id','IN',$ids];
                // }else{
                    // 探索
                    // $where[] = ['id','NOT IN',$ids];
                // }
            }else{
                $result = [
                    'list' => [],
                    'count' => 0,
                    'page' => $page,
                    'limit' => $limit,
                    // 'article_type' => $articleTypeList,
                    // 'type_name' => $type_name
                ];

                return $result;
            }
        }else{
            // 搜索
            $where[] = ['g_id','IN',$s_ids];
        }

        // $where[] = ['status','eq',1];
        // $sort_type = getSetting('sort_type_3');
        // if(!empty($sort_type)){
        //     if($sort_type == 1){
        //         // 时间
        //         $order = 'g_id desc';
        //     }elseif($sort_type == 2){
        //         // 热度
        //         $order = 'likes desc';
        //     }elseif($sort_type == 3){
        //         // 手动排序
        //         $order = 'sort asc';
        //     }else{
        //         $order = 'g_id desc';
        //     }
        // }else{
        //     $order = 'g_id desc';
        // }


        // if($status != -1 && in_array(2, $status)){
        //     $where[] = ['is_new', 'eq', 1];
        // }  

        $order = commonSort(3,$sort);//排序

        $list = $this
            ->where($where)
            ->page($page, $limit)
            ->order($order)
            ->select();

        $count = $this->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            // $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']).addImageSuffix(8);
                // $v['ctime'] = getTime($v['ctime']);
                if(!empty($v['labels'])){
                    $temp_label = unserialize($v['labels']);
                    $v['labels'] = $label->whereIn('id',$temp_label)->select();

                }
                
                if(getLng() == 'en'){
                    $v['cate_name'] = $cateModel->where('id',$v['cate_id'])->value('type_name_eng');
                    $v['game_name'] = $v['game_name_eng'];
                    $v['game_type'] = $v['game_type_eng'];
                    $v['game_content'] = $v['game_content_eng'];
                }else{
                    $v['cate_name'] = $cateModel->where('id',$v['cate_id'])->value('type_name');
                    $v['game_name'] = $v['game_name'];
                    $v['game_type'] = $v['game_type'];
                    $v['game_content'] = $v['game_content'];
                }

                //判断是否是已点赞
                if(!empty($user_id)){
                    $v['is_care'] = isUserCare($user_id,$v['g_id'],3);
                }else{
                    $v['is_care'] = false;
                } 

                // 获取游戏版本
                $v['game_version'] = $versionModel->where('g_id',$v['g_id'])->order('sort asc')->select();

                $v['lock_info'] = getNftStatus($v['g_id'],3,$user_id);//是否需要付费体验  
            }
        }

        //子文章分类
        // $articleTypeModel = new ArticleType();
        // foreach ($list as &$v){
        //     $res = $articleTypeModel->where(['id'=>$v['type_id']])->find();
        //     $v['type_name'] = $res['type_name'];
        // }
        // $articleTypeList = $articleTypeModel->where('pid', $type_id)->order('sort asc')->select();

        $result = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
            // 'article_type' => $articleTypeList,
            // 'type_name' => $type_name
        ];

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


    /**
     * 获取指定id 的游戏详情
     * @param $article_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGameInfo($game_id = '',$user_id = '')
    {
        $result = error_code(10002); //文章不存在或已删除
        if(empty($game_id)){
            return $result;
        }

        $goods = new \app\common\model\Goods();
        $versionModel = new \app\common\model\GameVersion();
        $sale = new \app\common\model\UserSale();
        $user = new \app\common\model\User();
        
        $where[] = ['g_id', 'eq', $game_id];
        // $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $data = $this
            ->where($where)
            ->find();

        if(!empty($data))
        {
            //$data['content'] = clearHtml($data['content'], ['width', 'height']);//清除文章中宽高
            $data['game_content'] = str_replace("clear: both;", "", $data['game_content']);
            $data['game_content'] = str_replace("<img", "<img style='max-width: 100%'", $data['game_content']);

            if(!empty($data['labels'])){
                $label = new \app\common\model\Label();
                $labels = unserialize($data['labels']);
                $data['labels'] = $label->whereIn('id',$labels)->select();
            }else{
                $data['labels'] = [];
            }
            
            $data['cover'] = _sImage($data['cover']);

            $gameCateModel = new ArticleType();

            $cate_info = $gameCateModel->where('id',$data['cate_id'])->find();

            if(getLng() == 'en'){
                $data['cate_name'] = $cate_info['type_name_eng'];
                $data['game_name'] = $data['game_name_eng'];
                $data['game_type'] = $data['game_type_eng'];
                $data['game_content'] = $data['game_content_eng'];
            }else{
                $data['cate_name'] = $cate_info['type_name'];
            }

            //判断是否是已点赞
            if(!empty($user_id)){
                $data['is_care'] = isUserCare($user_id,$data['g_id'],3);
            }else{
                $data['is_care'] = false;
            } 

            // 判断是否出价
            // if(!empty($user_id)){
                $data['post_price_info'] = isUserPostPrice($user_id,3,$data['g_id']);
            // }else{
                // $data['post_price_info'] = [];
            // }

            // 绑定的角色
            $data['role_info'] = [];
            if(!empty($data['role_id'])){
                $goods_res = $goods->getRoleDetialNew($data['role_id']);
                if($goods_res['status']){
                    $data['role_info'] = $goods_res['data'];
                }
            }

            // 判断是否是我的作品
            $data['is_my_works'] = isMyProduct($data['g_id'],3,$user_id);
            // 获取作品id
            if($data['is_my_works']){
                $user_works = new \app\common\model\UserWorks();
                $map['type'] = 3;
                $map['obj_id'] = $game_id;
                $map['user_id'] = $user_id;
                $data['work_data'] = $user_works->where($map)->find();
            }else{
                $data['work_data'] = [];
            }

            // 游戏版本
            $data['version'] = $versionModel->where('g_id',$game_id)->order('sort asc')->select();
            // 是否可以立即购买
            $data['is_buy'] = canBuyNft($game_id,3,$user_id);
            //上一篇，下一篇
            // $data['up'] = [];
            // $data['down'] = [];

            // $uwhere['is_pub'] = ['is_pub', 'eq', self::IS_PUB_YES];
            // $uwhere['type_id'] = ['type_id', 'eq', $data['type_id']];
            // $list = $this->field('id,title')->where($uwhere)->select();
            // if(count($list)>1){
            //     foreach($list as $k => $v){
            //         if($v['id'] == $data['id']){
            //             if($k == 0 || $k == count($list)-1){
            //                 if($k == 0){
            //                     $data['down'] = $list[$k+1];
            //                 }else{
            //                     $data['up'] = $list[$k-1];
            //                 }
            //             }else{
            //                 $data['up'] = $list[$k-1];
            //                 $data['down'] = $list[$k+1];
            //             }
            //         }
            //     }
            // }
            $data['obj_type'] = 3;
            $data['file_type'] = '';
            $data['id'] = $game_id;
            //获取拍卖id
            $data['sale_id'] = $sale->where(['obj_id' => $game_id,'obj_type' => 3,'is_cancel' => 0,'status' => 0,'user_id' => $user_id])->value('id');
            $data['chapter'] = [];
            $data['labels'] = [];
            $data['lock_info'] = getNftStatus($game_id,3,$user_id);//是否需要付费体验
            // 是否可以出售
            $data['is_sale'] = isMyNft($game_id,3,$user_id);
            // 是否可以出价
            $data['isPrice'] = isPrice($game_id,3,$user_id);
            // 获取版税
            if(!empty($data['user_id'])){
                $data['royalties'] = $user->where('id',$data['user_id'])->value('royalties');
            }
            $data['chain'] = getOtherDetail($game_id,3);
            // 取消拍卖和降低价格
            $data['is_cancel'] = isCancel($game_id,3,$user_id);
            // 拥有者
            $data['has_data'] = getNftMaster($game_id,3);
            // 版权拥有者
            $data['copy_data'] = getNftMasterCopy($game_id,3);
            // 显示购买选项
            $data['buy_option'] = showBuy($game_id,3);

            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }
        return $result;
    }


    /**
     * @return \think\model\relation\HasOne
     */
    public function articleType()
    {
        return $this->hasOne('ArticleType','id','type_id')->bind(['type_name']);
    }

    // 删除
    public function remove($id = '')
    {
        return $this->where('g_id',$id)->delete();
    }
}
