<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;
use think\Validate;

class Pictures extends Common
{
    protected $rule = [
        'name'          => 'require|max:50',
    ];
    protected $msg = [
        'name.require'  => '请输入插画名称',
        'name.max'      => '插画名称长度最大50位',
    ];


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

        $workModel = new \app\common\model\UserWorks();
        $tableWhere = $this->tableWhere($post);
        $list = $this->field($tableWhere['field'])->order('id desc')->where($tableWhere['where'])->paginate($limit);
        foreach($list as $key => $val)
        {
            $list[$key]['status'] = $workModel->where('obj_id',$val['id'])->where('type',4)->value('is_draft');
        }

        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型

        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;

        return $re;
    }



    /**
     *  添加插画
     * User:tianyu
     * @param $data
     * @return array
     */
    public function addData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data'=> [],
            'token'  => \think\facade\Request::token('__Jshop_Token__', 'sha1')
        ];
        $validate = new Validate($this->rule,$this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            $data['code'] = makeCode();
            unset($data['editorValue']);
            unset($data['labels']);
            unset($data['validate_form']);
            unset($data['__Jshop_Token__']);
            $id = $this->insertGetId($data);
            if (!$id){
            
                //                $result['status'] = false;
                //                $result['msg'] = '保存失败';
                return error_code(10004);
            }

            addPlatformDraft($id,4);
        }
        return $result;
    }


    /**
     *  修改插画
     * User:tianyu
     * @param $data
     * @return array
     */
    public function saveData($data)
    {
        $result = [
            'status' => true,
            'msg' => '保存成功',
            'data' => []
        ];
        $validate = new Validate($this->rule,$this->msg);
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if (!$this->allowField(true)->save($data, ['id' => $data['id']])) {
                //                $result['status'] = false;
                //                $result['msg'] = '保存失败';
                return error_code(10004);
            }
        }
        return $result;
    }


    //where搜索条件
    protected function tableWhere($post)
    {
        $where = [];
        if(isset($post['name']) && $post['name'] != ""){
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }

        if(isset($post['type_id']) && $post['type_id'] != ""){
            $where[] = ['cate_id', 'eq',$post['type_id']];
        }

        $workModel = new \app\common\model\UserWorks();
        // 草稿不显示
        $ids = $workModel->where(['type' => 4,'is_draft' => 1])->column('obj_id');
        if(!empty($ids)){
            $where[] = ['id', 'NOT IN', $ids];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        return $result;
    }


    /**
     * @param $list
     *
     * @return mixed
     */
    protected function tableFormat($list)
    {   
        $user = new \app\common\model\User();
        $cate_mod = new \app\common\model\ArticleType();

        foreach($list as &$val){
            $val['cover'] = _sImage($val['cover']);
            if(!empty($val['user_id'])){
                $val['user_name'] = $user->where('id',$val['user_id'])->value('username');
            }else{
                $val['user_name'] = '平台';
            }

            $val['type_name'] = $cate_mod->where('id',$val['cate_id'])->value('type_name');
        }
        return $list;
    }


    /**
     * 获取全部品牌
     * @return array|\PDOStatement|string|\think\Collection
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-12 17:32
     */
    public function getAllBrand()
    {
        $filter = [];
        $data = $this->field('id,name')
            ->where($filter)
            ->order('sort asc')
            ->select();

        return $data;
    }

    /**
     * 根据名称获取品牌信息
     * @param string $name
     * @param bool $isForce 没有名称时，是否添加
     * @return int
     */
    public function getInfoByName($name = '',$isForce = false)
    {
        if (!$name) {
            return false;
        }
        $brand_id = 0;
        $brand = $this->field('id')->where([['name', 'like', '%' . $name . '%']])->find();
        if (!$brand && $isForce) {
            $this->save([
                'name' => $name,
            ]);
            $brand_id = $this->getLastInsID();
        } elseif ($brand) {
            $brand_id = $brand['id'];
        }
        return $brand_id;
    }

    // 删除
    public function remove($id = '')
    {
        return $this->where('id',$id)->delete();
    }

    /**
     * 获取插画列表
     * @param bool $user_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function picturesList($user_id = 0, $page = 1, $limit = 10,$type = -1,$is_my = 0,$cate_id = 0,$is_market = -1,$min_price = 0,$max_price = 0,$status = -1,$sort = -1)
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        
        $user = new \app\common\model\User();
        $care = new \app\common\model\UserCare();

        if($type == -1){
            $order = commonSort(4,$sort);//排序
            // $sort_type = getSetting('sort_type_4');

            // if(!empty($sort_type)){
            //     if($sort_type == 1){
            //         // 时间
            //         $sort = 'id desc';
            //     }elseif($sort_type == 2){
            //         // 热度
            //         $sort = 'likes desc';
            //     }elseif($sort_type == 3){
            //         // 手动排序
            //         $sort = 'sort asc';
            //     }else{
            //         $sort = 'id desc';
            //     }
            // }else{
            //     $sort = 'id desc';
            // }
        }else{
            if($type == 3){
                $like_ids = $care->where(['type' => 4,'user_id' => $user_id])->column('obj_id');
            }else{
                $like_ids = [];
            }

            switch ($type) {
                case 1:
                    $order = 'likes desc,sort';
                    break;
                case 2:
                    $order = 'id desc,sort';
                    break;
                default:
                    $where[] = ['id','IN',$like_ids];
                    break;
            }
        }
        
        
        $articleTypeModel = new ArticleType();

        $where = [];

        if($min_price >= 0 && !empty($max_price)){
            $where[] = ['price','between',[$min_price,$max_price]];
        }

        if(!empty($user_id) && $is_my == 1){
             $where[] = ['user_id', 'eq', $user_id];
        }

        if(!empty($cate_id)){
            $where[] = ['cate_id', 'eq', $cate_id];
        }

        // $workModel = new \app\common\model\UserWorks();
        // $work_ids = $workModel->where(['is_draft'=>2,'type' => 4])->column('obj_id');
        // if(!empty($work_ids)){
        //     $where[] = ['id','in',$work_ids];
        // }

        $s_ids = nftStatus(4,$status,$is_market);
        // dump($ids);die;

        if(!$s_ids){
            
            $result['data'] = [
                'list' => [],
                'count' => 0,
                'page' => $page,
                'limit' => $limit,
                // 'article_type' => $articleTypeList,
                // 'type_name' => $type_name
            ];

            return $result;
        }

        // if($status != -1 && in_array(2, $status)){
        //     $where[] = ['is_new', 'eq', 1];
        // }  
        
        if($is_market != -1){
            $ids = getMarketData($is_market,4);
            if(!empty($ids)){
                $ids = array_intersect($s_ids,$ids);
                // if($is_market == 1){
                    // 市场
                    $where[] = ['id','IN',$ids];
                // }else{
                    // 探索
                    // $where[] = ['id','NOT IN',$ids];
                // }
            }else{
                $result['data'] = [
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
            $where[] = ['id','IN',$s_ids];
        }

        // $where[] = ['status','eq',1];

        $list = $this
            ->where($where)
            ->page($page, $limit)
            ->order($order)
            ->select();

        $count = $this->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            foreach ($list as $k => $v)
            {
                $list[$k]['cover'] = _sImage($v['cover']).addImageSuffix(9);
                $user_info = $user->where('id',$v['user_id'])->field('username,avatar,id')->find();
               
                $list[$k]['user_id'] = $user_info['id'] ? $user_info['id'] : 0;
                $list[$k]['username'] = $user_info['username'] ? $user_info['username'] : '平台';
                $list[$k]['avatar'] = $user_info['avatar'] ? $user_info['avatar'] : _sImage();
                
                if(getLng() == 'en'){
                    $list[$k]['cate_name'] = $articleTypeModel->where('id',$v['cate_id'])->value('type_name_eng');
                    $list[$k]['name'] = $v['name_eng'];
                    $list[$k]['desc'] = $v['desc_eng'];
                }else{
                    $list[$k]['cate_name'] = $articleTypeModel->where('id',$v['cate_id'])->value('type_name'); 
                }

                //判断是否是已点赞
                if(!empty($user_id)){
                    $list[$k]['is_care'] = isUserCare($user_id,$v['id'],4);
                }else{
                    $list[$k]['is_care'] = false;
                } 

                $list[$k]['lock_info'] = getNftStatus($v['id'],4,$user_id);//是否需要付费体验 
                $list[$k]['is_market'] = isMarket($v['id'],4);//是否是市场
                $list[$k]['price'] = getNftPrice($v['id'],4); 
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
     * 获取指定id 的插画详情
     * @param $article_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pictureDetail($picture_id,$user_id = '')
    {
        $result = error_code(10002);
        $user = new \app\common\model\User();
        $articleTypeModel = new ArticleType();
        $goods = new \app\common\model\Goods();
        $sale = new \app\common\model\UserSale();
        
        $where[] = ['id', 'eq', $picture_id];
        $data = $this
            ->where($where)
            ->find();

        if(!empty($data))
        {
            $data['cover'] = _sImage($data['cover']);
            $user_info = $user->where('id',$data['user_id'])->field('username,avatar,id')->find();
            
            $data['user_id'] = $user_info['id'] ? $user_info['id'] : 0;
            $data['username'] = $user_info['username'] ? $user_info['username'] : '平台';
            $data['avatar'] = $user_info['avatar'] ? $user_info['avatar'] : _sImage();
            if(getLng() == 'en'){
                $data['cate_name'] = $articleTypeModel->where('id',$data['cate_id'])->value('type_name_eng'); 
                $data['name'] = $data['name_eng'];
                $data['desc'] = $data['desc_eng'];
            }else{
                $data['cate_name'] = $articleTypeModel->where('id',$data['cate_id'])->value('type_name');  
            }
            
            //判断是否是已点赞
            if(!empty($user_id)){
                $data['is_care'] = isUserCare($user_id,$data['id'],4);
            }else{
                $data['is_care'] = false;
            } 

            // 判断是否出价
            // if(!empty($user_id)){
                $data['post_price_info'] = isUserPostPrice($user_id,4,$data['id']);
            // }else{
                // $data['post_price_info'] = [];
            // }

            // 是否可以立即购买
            $data['is_buy'] = canBuyNft($picture_id,4,$user_id);
            // 绑定的角色
            $data['role_info'] = [];
            if(!empty($data['role_id'])){
                $goods_res = $goods->getRoleDetialNew($data['role_id']);
                if($goods_res['status']){
                    $data['role_info'] = $goods_res['data'];
                }
            }

            // 判断是否是我的作品
            $data['is_my_works'] = isMyProduct($data['id'],4,$user_id);
            // 获取作品id
            if($data['is_my_works']){
                $user_works = new \app\common\model\UserWorks();
                $map['type'] = 4;
                $map['obj_id'] = $picture_id;
                $map['user_id'] = $user_id;
                $data['work_data'] = $user_works->where($map)->find();
            }else{
                $data['work_data'] = [];
            }

            $data['obj_type'] = 4;
            $data['file_type'] = '';
            $data['lock_info'] = getNftStatus($picture_id,4,$user_id);//是否需要付费体验

            //获取拍卖id
            $data['sale_id'] = $sale->where(['obj_id' => $picture_id,'obj_type' => 4,'is_cancel' => 0,'status' => 0,'user_id' => $user_id])->value('id');
            $data['chapter'] = [];
            $data['labels'] = [];

            // 是否可以出售
            $data['is_sale'] = isMyNft($picture_id,4,$user_id);
            // 是否可以出价
            $data['isPrice'] = isPrice($picture_id,4,$user_id);
            // 获取版税
            if(!empty($data['user_id'])){
                $data['royalties'] = $user->where('id',$data['user_id'])->value('royalties');
            }
            
           
            $data['chain'] = getOtherDetail($picture_id,4);
            $data['is_market'] = isMarket($picture_id,4);//是否市场

            // 取消拍卖和降低价格
            $data['is_cancel'] = isCancel($picture_id,4,$user_id);
            // 拥有者
            $data['has_data'] = getNftMaster($picture_id,4);
            // 拥有者
            $data['copy_data'] = getNftMasterCopy($picture_id,4);
            // 显示购买选项
            $data['buy_option'] = showBuy($picture_id,4);
            // 拍卖倒计时
            $data['sale_time'] = saleLastTime($picture_id,4);

            $data['price'] = getNftPrice($picture_id,4);
            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }
        return $result;
    }
}