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

class Files extends Common
{
    //验证规则
        protected $rule = [
            'name'        => 'require|max:50',
            'video_cover' => 'require'
        ];

        protected $msg = [
            'name.require'        => '视频标题必须填写',
            'name.max'            => '标题名称最多不能超过50个字符',
            'video_id.require'    => '请上传视频',
            'video_cover.require' => '请上传视频封面',
        ];

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post)
    {
        if(isset($post['limit'])){
            $limit = $post['limit'];
        }else{
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $config['page'] = $post['page'];
        $config['list_rows'] = $post['limit'];
        $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit,false,$config);
        $workModel = new \app\common\model\UserWorks();
        foreach($list as $key => $val)
        {
            $list[$key]['status'] = $workModel->where('obj_id',$val['id'])->where('type',2)->value('is_draft');
        }

        $data = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
        $re['code'] = 0;
        $re['msg'] = '';
        $re['count'] = $list->total();
        $re['data'] = $data;
        // $re['sql'] = $this->getLastSql();

        return $re;
    }

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%'.$post['name'].'%'];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'eq', $post['id']];
        }

        if (isset($post['type_id']) && $post['type_id'] != "") {
            $where[] = ['cat_id', 'eq', $post['type_id']];
        }

        if (isset($post['type']) && $post['type'] != "") {
            $where[] = ['type', 'eq', $post['type']];
        }

        $workModel = new \app\common\model\UserWorks();
        // 草稿不显示
        $ids = $workModel->where(['type' => 2,'is_draft' => 1])->column('obj_id');
        if(!empty($ids)){
            $where[] = ['id', 'NOT IN', $ids];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['ctime' => 'desc'];
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @author sin
     * @param $list
     * @return mixed
     */
    protected function tableFormat($list)
    {
        $cate = new \app\common\model\ArticleType();

        if(!$list->isEmpty()){
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

            foreach($list as $key=>$val){
                $list[$key]['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
                $list[$key]['path'] = _sImage($val['path']);
                $list[$key]['cate_name'] = $cate->where('id',$val['cat_id'])->value('type_name');
            }
        }
        return $list;
    }

    /*
     * 音频添加
     * */
    public function addData($data)
    {
        $validate = new Validate($this->rule, $this->msg);
        $result   = ['status' => true, 'msg' => '保存成功', 'data' => ''];
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            $data['ctime'] = time();
            
            if (!$this->allowField(true)->save($data)) {
                return error_code(10004);
            }
        }
        return $result;
    }


    /*
     * 音频编辑
     * */
    public function videoEdit($data)
    {
        $validate = new Validate($this->rule, $this->msg);
        $result   = ['status' => true, 'msg' => '保存成功', 'data' => ''];
        if (!$validate->check($data)) {
            $result['status'] = false;
            $result['msg']    = $validate->getError();
        } else {
            $data['ctime'] = time();
            $data['utime'] = time();
            unset($data['__Jshop_Token__'], $data['file']);
            $where = [
                'id' => $data['id']
            ];
            if (!$this->where($where)->update($data)) {
                return error_code(10004);
            }
        }
        return $result;
    }

    /**
     * 删除音频
     * @param $coupon_code
     * @return array
     */
    public function delMusic($id)
    {
        $result = error_code(10023);
        if ($this->where('id',$id)->delete())
        {
            $result['status'] = true;
            $result['msg'] = '删除成功';
        }
        return $result;
    }

    // 获取音频列表
    public function getAudioList($cat_id = 0,$page = 1,$limit = 20,$user_id = '',$type = 1,$is_market = -1,$min_price = 0,$max_price = 0,$status = -1,$sort = -1)
    {
        $map = [];
        $cate_mod = new ArticleType();

        if (!empty($cat_id)) {
            $map[] = ['cat_id','=',$cat_id];   
        }

        if($type != -1){
            if($type == 2){
                $map[] = ['type','eq',$type];
            }else{
                $map[] = ['type','in','1,3,4'];
            }
            
        }
        
        if($min_price >= 0 && !empty($max_price)){
            $map[] = ['price','between',[$min_price,$max_price]];
        }
        
        // if($is_market != -1){
        //     $map[] = ['is_market','eq',$is_market];
        // }


        $workModel = new \app\common\model\UserWorks();
        $userModel = new \app\common\model\User();

        // $work_ids = $workModel->where(['is_draft'=>2,'type' => 2])->column('obj_id');
        // if(!empty($work_ids)){
        //     $map[] = ['id','in',$work_ids];
        // }
        $s_ids = nftStatus(2,$status,$is_market);
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
        
        if($is_market != -1){
            $ids = getMarketData($is_market,2);
            if(!empty($ids)){
                $ids = array_intersect($s_ids,$ids);
                // if($is_market == 1){
                    // 市场
                    $map[] = ['id','IN',$ids];
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
            $map[] = ['id','IN',$s_ids];
        }

        // if($status != -1 && in_array(2, $status)){
        //     $map[] = ['is_new', 'eq', 1];
        // } 
        
        // $map[] = ['status','eq',1];
        // $sort_type = getSetting('sort_type_2');
        // if(!empty($sort_type)){
        //     if($sort_type == 1){
        //         // 时间
        //         $order = 'id desc';
        //     }elseif($sort_type == 2){
        //         // 热度
        //         $order = 'likes desc';
        //     }elseif($sort_type == 3){
        //         // 手动排序
        //         $order = 'sort asc';
        //     }else{
        //         $order = 'id desc';
        //     }
        // }else{
        //     $order = 'id desc';
        // }

        $order = commonSort(2,$sort);//排序

        $list = $this->where($map)->page($page,$limit)->order($order)->select();
        if($list){
            foreach ($list as $key => $value) {
                $file_type = substr($value['url'],strripos($value['url'],".")+1);
                if(in_array($file_type, ['mp4','webm'])){
                    $list[$key]['file_type'] = 2;//视频
                }else{
                    $list[$key]['file_type'] = 1;//音频
                }

                $list[$key]['path'] = _sImage($value['path']).addImageSuffix(6);
                if(!empty($value['cat_id'])){
                    if(getLng() == 'en'){
                        $list[$key]['cat_name'] = $cate_mod->where('id',$value['cat_id'])->value('type_name_eng');
                    }else{
                        $list[$key]['cat_name'] = $cate_mod->where('id',$value['cat_id'])->value('type_name');
                    }
                    
                }else{
                    $list[$key]['cat_name'] = '';
                    $list[$key]['cat_name_eng'] = '';
                }

                if(getLng() == 'en'){
                    $list[$key]['name'] = $value['name_eng'];
                    $list[$key]['desc'] = $value['desc_eng'];
                }

                //判断是否是已点赞
                if(!empty($user_id)){
                    $list[$key]['is_care'] = isUserCare($user_id,$value['id'],2);
                }else{
                    $list[$key]['is_care'] = false;
                }

                // 获取作者
                if(empty($value['user_id'])){
                    $list[$key]['author'] = '平台';
                }else{
                    $list[$key]['author'] = $userModel->where('id',$value['user_id'])->value('username');
                }
                
                $list[$key]['lock_info'] = getNftStatus($value['id'],2,$user_id);//是否需要付费体验
                $list[$key]['price'] = getNftPrice($value['id'],2); 
            }
        }

        $count = $this->where($map)->count();

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
        ];

        return $result;
    }

    // 音频详情
    public function getAudioInfo($id = '',$user_id = '')
    {
        $result = error_code(10002);

        if(empty($id)){
            return $result;
        }
        
        $where[] = ['id', 'eq', $id];

        $data = $this->where($where)->find();

        if(!empty($data))
        {
            $goods = new \app\common\model\Goods();
            $sale = new \app\common\model\UserSale();
            $user = new \app\common\model\User();

            $cate_mod = new ArticleType();
            $data['path'] = _sImage($data['path']);
            if(!empty($data['cat_id'])){
                if(getLng() == 'en'){
                     $data['cat_name'] = $cate_mod->where('id',$data['cat_id'])->value('type_name_eng');
                }else{
                    $data['cat_name'] = $cate_mod->where('id',$data['cat_id'])->value('type_name');
                }

            }else{
                $data['cat_name'] = '';
                $data['cat_name_eng'] = '';
            }

            $data['cate_id'] = $data['cat_id'];

            //判断是否是已点赞
            if(!empty($user_id)){
                $data['is_care'] = isUserCare($user_id,$data['id'],2);
            }else{
                $data['is_care'] = false;
            }

            if(getLng() == 'en'){
                $data['name'] = $data['name_eng'];
                $data['desc'] = $data['desc_eng'];
            }
            
            // 判断是否出价
            // if(!empty($user_id)){
                $data['post_price_info'] = isUserPostPrice($user_id,2,$data['id']);
            // }else{
                // $data['post_price_info'] = [];
            // }

            $file_type = substr($data['url'],strripos($data['url'],".")+1);
            if(in_array($file_type, ['mp4','webm'])){
                $data['file_type'] = 2;//视频
            }else{
                $data['file_type'] = 1;//音频
            }
            
            // 绑定的角色
            $data['role_info'] = [];
            if(!empty($data['role_id'])){
                $goods_res = $goods->getRoleDetialNew($data['role_id']);
                if($goods_res['status']){
                    $data['role_info'] = $goods_res['data'];
                }
            }

            // 判断是否是我的作品
            $data['is_my_works'] = isMyProduct($data['id'],2,$user_id);
            // 获取作品id
            if($data['is_my_works']){
                $user_works = new \app\common\model\UserWorks();
                $map['type'] = 2;
                $map['obj_id'] = $id;
                $map['user_id'] = $user_id;
                $data['work_data'] = $user_works->where($map)->find();
            }else{
                $data['work_data'] = [];
            }

            $data['obj_type'] = 2;
            $data['sale_id'] = $sale->where(['obj_id' => $id,'obj_type' => 2,'is_cancel' => 0,'status' => 0,'user_id' => $user_id])->value('id');

            // 获取作者
            $author = $user->where('id',$data['user_id'])->value('username');
            $data['author'] = empty($author) ? '平台' : $author;
            $data['lock_info'] = getNftStatus($id,2,$user_id);//是否需要付费体验
            // 是否可以出价
            $data['isPrice'] = isPrice($id,2,$user_id);
            // 获取版税
            if(!empty($data['user_id'])){
                $data['royalties'] = $user->where('id',$data['user_id'])->value('royalties');
            }
            // 是否可以出售
            $data['is_sale'] = isMyNft($id,2,$user_id);
            // 是否可以立即购买
            $data['is_buy'] = canBuyNft($id,2,$user_id);
            $data['chain'] = getOtherDetail($id,2);
            // 取消拍卖和降低价格
            $data['is_cancel'] = isCancel($id,2,$user_id);
            // 拥有者
            $data['has_data'] = getNftMaster($id,2);
            // 版权拥有者
            $data['copy_data'] = getNftMasterCopy($id,2);
            // 显示购买选项
            $data['buy_option'] = showBuy($id,2);
            // 拍卖倒计时
            $data['sale_time'] = saleLastTime($id,2);
            $data['price'] = getNftPrice($id,2); 

            $data['chapter'] = [];
            $data['labels'] = [];
            $result['status'] = true;
            $result['msg'] = '获取成功';
            $result['data'] = $data;
        }

        return $result;
    }
}
