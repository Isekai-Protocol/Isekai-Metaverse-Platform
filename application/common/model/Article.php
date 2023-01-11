<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\facade\Cache;
use think\model\concern\SoftDelete;
use think\Validate;

class Article extends Common
{
    const IS_PUB_YES = 1;   //发布
    const IS_PUB_NO = 2;    //暂不发布

    use SoftDelete;
    //时间自动存储
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';
    //软删除位
    protected $deleteTime = 'isdel';


    //验证规则
    protected $rule     =   [
        'title'         =>  'require|max:200',
        // 'brief'         =>  'max:100',
        'type_id'       =>  'require',
        'sort'          =>  'number',
        'price'          =>  'number',
    ];

    protected $msg          =   [
        'title.require'     =>  '文章标题必须填写',
        'title.max'         =>  '标题名称最多不能超过200个字符',
        // 'brief.max'         =>  '文章简介最多不能超过100个字符',
        'type_id.require'   =>  '请选择文章分类',
        'sort.number'       =>  '排序必须是数字类型',
        'price.number'       =>  '价格必须是数字类型',
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
        $list = $this->with('articleType')->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv,likes,words,chapter,code,is_market')->where($tableWhere['where'])->order($tableWhere['order'])->paginate($limit);
        foreach($list as $key => $val)
        {
            $list[$key]['cover'] = _sImage($val['cover']);
            $list[$key]['is_pub'] = config('params.article')['is_pub'][$val['is_pub']];
            $list[$key]['ctime'] = date('Y-m-d H:i:s',$val['ctime']);
            $list[$key]['utime'] = date('Y-m-d H:i:s',$val['utime']);
            $list[$key]['status'] = $workModel->where('obj_id',$val['id'])->where('type',1)->value('is_draft');
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

        $validate = new Validate($this->rule,$this->msg);
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
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
            addPlatformDraft($id,1);
        }
        Cache::clear();
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
        $validate = new Validate($this->rule,$this->msg);
        $result = ['status'=>true,'msg'=>'保存成功','data'=>''];
        if(!$validate->check($data))
        {
            $result['status'] = false;
            $result['msg'] = $validate->getError();
        } else {
            if(!$this->allowField(true)->save($data,['id'=>$data['id']]))
            {
                $result['status'] = false;
                $result['msg'] = error_code(10004,true);
            }
        }
        Cache::clear();
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
        $workModel = new \app\common\model\UserWorks();

        $where = [];
        if(isset($post['title']) && $post['title'] != ""){
            $where[] = ['title', 'like', '%'.$post['title'].'%'];
        }
        if(isset($post['utime']) && $post['utime'] != ""){
            $date_array = explode('到',$post['utime']);
            $sutime = strtotime($date_array[0].'00:00:00',time());
            $eutime = strtotime($date_array[1].'23:59:59',time());
            $where[] = ['utime', ['EGT',$sutime],['ELT',$eutime],'and'];
        }
        if(isset($post['type_id']) && $post['type_id'] != ""){
            $where[] = ['type_id', 'eq', $post['type_id']];
        }
        if(isset($post['is_pub']) && $post['is_pub'] != ""){
            $where[] = ['is_pub', 'eq', $post['is_pub']];
        }

        // 草稿不显示
        $ids = $workModel->where(['type' => 1,'is_draft' => 1])->column('obj_id');
        if(!empty($ids)){
            $where[] = ['id', 'NOT IN', $ids];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }


    /**
     * 获取文章列表
     * @param bool $type_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleList($type_id = 0, $page = 1, $limit = 10,$user_id = '',$type = -1,$min_price = 0,$max_price = 0,$status = -1,$sort = -1)
    {
        $result = [
            'status' =>  true,
            'msg'    =>  '获取成功',
            'data'   =>  []
        ];
        $articleTypeModel = new ArticleType();
        $label = new \app\common\model\Label();
        $user = new \app\common\model\User();
        $chapterModel = new \app\common\model\ArticleChapter();

        // $type_name = "文章分类";
        // if($type_id != 0){
        //     $info = $articleTypeModel->where('id',$type_id)->find();
        //     if($info){
        //         $type_name = $info['type_name'];
        //     }
        // }
        $where = [];
        
        if($min_price >= 0 && !empty($max_price)){
            $where[] = ['price','between',[$min_price,$max_price]];
        }

        $s_ids = nftStatus(1,$status,$type);

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

        if($type != -1){
            // $where[] = ['is_market','eq',$type];
            $ids = getMarketData($type,1);
            if(!empty($ids)){
                
                $ids = array_intersect($s_ids,$ids);
                
                // if($type == 1){
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
        
        // 发布状态
        // $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        if(!empty($type_id)){
             $where[] = ['type_id', 'eq', $type_id];
        }   

        // $workModel = new \app\common\model\UserWorks();
        // $work_ids = $workModel->where(['is_draft'=>2,'type' => 1])->column('obj_id');
        // if(!empty($work_ids)){
        //     $where[] = ['id','in',$work_ids];
        // }
        
        // dump($ids);die;

        

        // if($status != -1 && in_array(2, $status)){
        //     $where[] = ['is_new', 'eq', 1];
        // }   

        // $sort_type = getSetting('sort_type_1');
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

        $order = commonSort(1,$sort);//排序

        // $where[] = ['status','eq',1];
        $list = $this
            ->where($where)
            // ->field('id,title,cover,type_id,ctime,utime,sort,is_pub,pv,brief')
            ->order($order)
            ->page($page, $limit)
            ->select();

        $count = $this->where($where)
            ->count();

        if(!$list->isEmpty())
        {
            $list = $list->hidden(['is_pub', 'isdel']);
            foreach ($list as &$v)
            {
                $v['cover'] = _sImage($v['cover']).addImageSuffix(7);
                $v['ctime'] = getTime($v['ctime']);
                if(!empty($v['labels'])){
                    $temp_label = unserialize($v['labels']);
                    $v['labels'] = $label->whereIn('id',$temp_label)->select();
                }
                
                if(getLng() == 'en'){
                    $v['cate_name'] = $articleTypeModel->where('id',$v['type_id'])->value('type_name_eng');
                    $v['title'] = $v['title_eng'];
                    $v['brief'] = $v['brief_eng'];
                    // $v['auother'] = $v['auother_eng'];
                }else{
                    $v['cate_name'] = $articleTypeModel->where('id',$v['type_id'])->value('type_name');

                }

                if(!empty($v['user_id'])){
                    $v['auother'] = $user->where('id',$v['user_id'])->value('username');
                }else{
                    $v['auother'] = '平台';
                }

                //判断是否是已点赞
                if(!empty($user_id)){
                    $v['is_care'] = isUserCare($user_id,$v['id'],1);
                }else{
                    $v['is_care'] = false;
                }

                //文章章节列表
                $v['chapter'] = $chapterModel->where('article_id',$v['id'])->order('index asc')->select();
                if($v['chapter']){
                    foreach ($v['chapter'] as $ck => $cv) {
                        $v['chapter'][$ck]['content'] = mb_convert_encoding(@file_get_contents($cv['url']),'UTF-8','UTF-8,GBK,GB2312,BIG5');
                    }
                }

                $v['lock_info'] = getNftStatus($v['id'],1,$user_id);//是否需要付费体验
                $v['is_market'] = isMarket($v['id'],1);//是否是市场
                $v['price'] = getNftPrice($v['id'],1);      
            }
        }

        //子文章分类
        // $articleTypeModel = new ArticleType();
        // foreach ($list as &$v){
        //     $res = $articleTypeModel->where(['id'=>$v['type_id']])->find();
        //     $v['type_name'] = $res['type_name'];
        // }
        // $articleTypeList = $articleTypeModel->where('pid', $type_id)->order('sort asc')->select();

        $result['data'] = [
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
     * 获取指定id 的文章详情
     * @param $article_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleDetail($article_id,$user_id = '')
    {
        $result = error_code(10801); //文章不存在或已删除

        $goods = new \app\common\model\Goods();
        $sale = new \app\common\model\UserSale();
        $user = new \app\common\model\User();
        $chapterModel = new \app\common\model\ArticleChapter();

        $where[] = ['id', 'eq', $article_id];
        // $where[] = ['is_pub', 'eq', self::IS_PUB_YES];
        $data = $this
            ->where($where)
            ->find();

        if(!empty($data))
        {
            //$data['content'] = clearHtml($data['content'], ['width', 'height']);//清除文章中宽高
            $data['content'] = str_replace("clear: both;", "", $data['content']);
            $data['content'] = str_replace("<img", "<img style='max-width: 100%'", $data['content']);
            $typeModel = new ArticleType();
            if(getLng() == 'en'){
                $data['cate_name'] = $typeModel->where('id',$data['type_id'])->value('type_name_eng');
                $data['title'] = $data['title_eng'];
                $data['brief'] = $data['brief_eng'];
                // $data['auother'] = $data['auother_eng'];
            }else{
                $data['cate_name'] = $typeModel->where('id',$data['type_id'])->value('type_name');
            }
            
            if(!empty($data['user_id'])){
                $data['auother'] = $user->where('id',$data['user_id'])->value('username');
            }else{
                $data['auother'] = '平台';
            }
            
            $data['ctime'] = time_ago($data['ctime']);
            $add_pv = $this->where(['id' => $article_id])->update(['pv' => $data['pv'] + 1]);
            // if (!$add_pv) {
            //     return error_code(10037);
            // }
            if(!empty($data['labels'])){
                $label = new \app\common\model\Label();
                $labels = unserialize($data['labels']);
                $data['labels'] = $label->whereIn('id',$labels)->select();
            }else{
                $data['labels'] = [];
            }
            
            $data['cover'] = _sImage($data['cover']);

            //判断是否是已点赞
            if(!empty($user_id)){
                $data['is_care'] = isUserCare($user_id,$data['id'],1);
            }else{
                $data['is_care'] = false;
            } 
            
            // 判断是否出价
            // if(!empty($user_id)){
                $data['post_price_info'] = isUserPostPrice($user_id,1,$data['id']);
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
            $data['is_my_works'] = isMyProduct($data['id'],1,$user_id);
            // 获取作品id
            if($data['is_my_works']){
                $user_works = new \app\common\model\UserWorks();
                $map['type'] = 1;
                $map['obj_id'] = $article_id;
                $map['user_id'] = $user_id;
                $data['work_data'] = $user_works->where($map)->find();
            }else{
                $data['work_data'] = [];
            }

            $data['obj_type'] = 1;
            //获取拍卖id
            $data['sale_id'] = $sale->where(['obj_id' => $article_id,'obj_type' => 1,'is_cancel' => 0,'status' => 0,'user_id' => $user_id])->value('id');

            $data['cate_id'] = $data['type_id'];
            $data['file_type'] = '';

            $data['lock_info'] = getNftStatus($article_id,1,$user_id);//是否需要付费体验
            
            //文章章节列表
            $data['chapter'] = $chapterModel->where('article_id',$article_id)->order('index asc')->select();
            if($data['chapter']){
                foreach ($data['chapter'] as $ck => $cv) {
                    $data['chapter'][$ck]['content'] = mb_convert_encoding(@file_get_contents($cv['url']),'UTF-8','UTF-8,GBK,GB2312,BIG5');
                }
            } 

            // 是否可以出售
            $data['is_sale'] = isMyNft($article_id,1,$user_id);
            // 是否可以出价
            $data['isPrice'] = isPrice($article_id,1,$user_id);
            // 获取版税
            if(!empty($data['user_id'])){
                $data['royalties'] = $user->where('id',$data['user_id'])->value('royalties');
            }
            // 是否可以立即购买
            $data['is_buy'] = canBuyNft($article_id,1,$user_id);
            $data['chain'] = getOtherDetail($article_id,1);
            $data['is_market'] = isMarket($article_id,1);//是否市场
            // 取消拍卖和降低价格
            $data['is_cancel'] = isCancel($article_id,1,$user_id);
            // 拥有者
            $data['has_data'] = getNftMaster($article_id,1);
            // 版权拥有者
            $data['copy_data'] = getNftMasterCopy($article_id,1);
            // 显示购买选项
            $data['buy_option'] = showBuy($article_id,1);
            // 拍卖倒计时
            $data['sale_time'] = saleLastTime($article_id,1);
            $data['price'] = getNftPrice($article_id,1);
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

}
