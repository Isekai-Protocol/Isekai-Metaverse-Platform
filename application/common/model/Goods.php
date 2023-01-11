<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\model\Products;
use think\Exception;
use think\model\concern\SoftDelete;
use app\common\model\GoodsImages;
use app\common\model\UserToken;
use app\common\model\GoodsCollection;
use app\common\model\GoodsCat;
use app\common\model\Setting;
use think\Db;


/**
 * 商品类型
 * Class GoodsType
 * @package app\common\model
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 20:09
 */
class Goods extends Common
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = 'utime';

    const MARKETABLE_UP = 1; //上架
    const MARKETABLE_DOWN = 2; //下架
    const VIRTUAL_YES = 2; //虚拟商品
    const VIRTUAL_NO = 1; //普通商品
    const HOT_YES = 1; //热卖
    const HOT_NO = 2; //非热卖


    //商品禁止输出字段
    private $goodsForbidFields = [
        'costprice', 'freeze_stock'
    ];
    //货品禁止输出字段
    private $productForbidFields = [
        'costprice', 'freeze_stock'
    ];


    public function tableData($post, $isPage = true)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $query      = $this::with('defaultImage,goodsCat,goodsType')
            ->field($tableWhere['field'])->where($tableWhere['where'])->whereOr($tableWhere['whereOr'])->order($tableWhere['order']);

        $goods_extend_mod = new \app\common\model\GoodsExtend();
        $prop_mod = new \app\common\model\Prop();

        if ($isPage) {
            $list        = $query->paginate($limit);
            $data        = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $re['count'] = $list->total();
        } else {
            $list        = $query->select();
            $data        = $this->tableFormat($list);         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $re['count'] = count($list);
        }

        foreach ($data as $key => $value) {
            $extend = $goods_extend_mod->where('goods_id',$value['id'])->find();
            $data[$key]['image'] = _sImage($extend['role_img']).'?x-oss-process=image/resize,w_1920,m_lfit/format,webp';
            $data[$key]['role_id'] = $extend['role_id'];
            $data[$key]['prop'] = $prop_mod->where(['goods_id' => $value['id'],'type' => 1])->value('prop_img');
        }

        $re['code'] = 0;
        $re['msg']  = '';
        $re['data'] = $data;
        return $re;
    }

    // 获取导出数据-发版角色
    public function getPostCsv($post)
    {
        // $post['field'] = 'id,name,post_version,bn,royalties';
        $tableWhere = $this->tableWhere($post);
        $ids = $this->where($tableWhere['where'])->column('id');
        $list = [];
        for ($i=0; $i < count($ids); $i++) { 
            $temp_data = $this->getRoleDetialNew($ids[$i]);
            // dump($temp_data);die;
            $role_title  = '';//称号
            foreach ($temp_data['data']['extend']['role_title_info'] as $key => $value) {
                $role_title .= $value['title'].',';
            }

            // 天赋
            $talent_info = '';
            foreach ($temp_data['data']['extend']['talent_info'] as $k => $v) {
                $talent_info .= $v['title'].':'.$v['img'].';';
            }

            // 潜能
            $potential_info = '';
            foreach ($temp_data['data']['extend']['potential_info'] as $ke => $ve) {
                $potential_info .= $ve['title'].':'.$ve['img'].';';
            }

            // 组件
            $part = '';
            foreach ($temp_data['data']['part'] as $k2 => $v2) {
                $part .= $v2['cate'].':'.$v2['name'].'('.$v2['number'].');';
            }

            $list[] = [
                'id' => $temp_data['data']['id'],
                'name' => $temp_data['data']['name'],
                'work' => $temp_data['data']['work'],
                'sex' => $temp_data['data']['sex'] == 1 ? '男' : '女',
                'bn' => $temp_data['data']['bn'],
                'royalties' => $temp_data['data']['royalties'],
                'role_img' => $temp_data['data']['role_img'],
                'character' => $temp_data['data']['character'],
                'role_title' => $role_title,
                'history' => $temp_data['data']['extend']['history'],
                'talent' => $talent_info,
                'potential' => $potential_info,
                'role_type' => $temp_data['data']['extend']['role_type'],
                'figure' => $temp_data['data']['extend']['figure'],
                'friendly' => $friendly,
                'evil' => $evil,
                'position' => $temp_data['data']['extend']['position_info']['position'],
                'prop_card' => $temp_data['data']['prop_card']['card_img'],
                'expression' => $temp_data['data']['expression'][0]['prop_img'],
                'part' => $part
               
            ];

            // dump($list);die;
        }

        // $list      = $this->field($tableWhere['field'])->where($tableWhere['where'])->whereOr($tableWhere['whereOr'])->order($tableWhere['order'])->select();
        // $goods_extend_mod = new \app\common\model\GoodsExtend();
        // $prop_mod = new \app\common\model\Prop();

        // foreach ($list as $key => $value) {
        //     $extend = $goods_extend_mod->where('goods_id',$value['id'])->find();
        //     $list[$key]['image'] = _sImage($extend['role_img']).'?x-oss-process=image/resize,w_1920,m_lfit/format,webp';
        //     $list[$key]['prop'] = $prop_mod->where(['goods_id' => $value['id'],'type' => 1])->value('prop_img');
        // }

        $new_list  = [];
        foreach ($list as $k3 => $v3) {
            $new_list[] = [$v3['id'],$v3['name'],$v3['work'],$v3['sex'],$v3['bn'],$v3['royalties'],$v3['role_img'],$v3['character'],$v3['role_title'],$v3['history'],$v3['talent'],$v3['potential'],$v3['role_type'],$v3['figure'],$v3['position'],$v3['prop_card'],$v3['expression'],$v3['part'],$v3['address'],$v3['token_id'],$v3['type'],$v3['ethereum'],$v3['rate_fee']];
        }

        return $new_list;
    }

    /**
     * 默认排序
     * @param $post
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-11 16:32
     */
    protected function tableWhere($post)
    {
        $where = $whereOr = [];
        if (isset($post['marketable']) && $post['marketable'] != "") {
            $where[] = ['marketable', 'eq', $post['marketable']];
        }
        if (isset($post['is_recommend']) && $post['is_recommend'] != '') {
            $where[] = ['is_recommend', 'eq', $post['is_recommend']];
        }
        if (isset($post['is_hot']) && $post['is_hot'] != '') {
            $where[] = ['is_hot', 'eq', $post['is_hot']];
        }
        if (isset($post['is_post']) && $post['is_post'] != '') {
            $where[] = ['is_post', 'eq', $post['is_post']];
        }
        if (isset($post['version']) && $post['version'] != '') {
            $where[] = ['post_version', 'eq', $post['version']];
        }
        if (isset($post['name']) && $post['name'] != "") {
            $where[] = ['name', 'like', '%' . $post['name'] . '%'];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'in', $post['id']];
        }
        if (isset($post['warn']) && $post['warn'] == "true") {
            $SettingModel      = new Setting();
            $goods_stocks_warn = $SettingModel->getValue('goods_stocks_warn');
            $goods_stocks_warn = $goods_stocks_warn ? $goods_stocks_warn : '10';
            $productModel      = new Products();
            //$baseFilter[] = ['(stock - freeze_stock)', 'lt', $goods_stocks_warn];
            $goodsIds = $productModel->field('goods_id')->where(Db::raw('IF(stock < freeze_stock, 0, stock - freeze_stock)<' . $goods_stocks_warn))->group('goods_id')->select();
            if (!$goodsIds->isEmpty()) {
                $goodsIds = array_column($goodsIds->toArray(), 'goods_id');
                $where[]  = ['id', 'in', $goodsIds];
            } else {
                $where[] = ['id', 'in', 0];
            }
        }
        if (isset($post['goods_type_id']) && $post['goods_type_id'] != "") {
            $where[] = ['goods_type_id', 'eq', $post['goods_type_id']];
        }
        if (isset($post['brand_id']) && $post['brand_id'] != "") {
            $where[] = ['brand_id', 'eq', $post['brand_id']];
        }
        if (isset($post['bn']) && $post['bn'] != "") {
            $where[] = ['bn', 'like', '%' . $post['bn'] . '%'];
        }

        if (isset($post['last_cat_id']) && $post['last_cat_id'] != "") {
            $where[]        = ['goods_cat_id', 'eq', $post['last_cat_id']];
            $goodsExtendCat = new GoodsExtendCat();
            $gids           = $goodsExtendCat->getGoodsIdByCat($post['last_cat_id']);
            if ($gids) {
                $whereOr[] = ['id', 'in', $gids];
            }
        }
        if (isset($post['goods_cat_id']) && $post['goods_cat_id'] != "" && !$post['last_cat_id']) { //取出来所有子分类进行查询
            if ($post['goods_cat_id']) {
                $goodsCatModel = new GoodsCat();
                $catIds        = [];
                $childCats     = $goodsCatModel->getCatByParentId($post['goods_cat_id']);
                $catIds        = array_column($childCats->toArray(), 'id');
                $catIds[]      = $post['goods_cat_id'];
                $where[]       = ['goods_cat_id', 'in', $catIds];

                $goodsExtendCat = new GoodsExtendCat();
                $gids           = $goodsExtendCat->getGoodsIdByCat($catIds);
                if ($gids) {
                    $whereOr[] = ['id', 'in', $gids];
                }
            }
        }
        if (!isset($post['field'])) {
            $post['field'] = "*";
        }

        $result['where']   = $where;
        $result['whereOr'] = $whereOr;
        $result['field']   = $post['field'];
        $result['order']   = ['sort'=>'ASC','id'=>'DESC'];
        return $result;
    }

    /**
     * 保存商品
     * User:wjima
     * Email:1457529125@qq.com
     * @param array $data
     * @return mixed
     */
    public function doAdd($data = [])
    {
        unset($data['stock']);
        $goodsid = $this->allowField(true)->insertGetId($data);
        return $goodsid ? $goodsid : 0;
    }

    protected function tableFormat($list)
    {
        $productModel = new Products();
        foreach ($list as $key => $val) {
            $list[$key]['image'] = _sImage($val['image_id']);
            if ($val['label_ids']) {
                $list[$key]['label_ids'] = getLabel($val['label_ids']);
            }
            $stock               = $productModel->where([['goods_id', '=', $val['id']]])->sum('stock');
            $freeze_stock        = $productModel->where([['goods_id', '=', $val['id']]])->sum('freeze_stock');
            $list[$key]['stock'] = ($stock - $freeze_stock) > 0 ? ($stock - $freeze_stock) : 0;
            if (isset($val['brand_id']) && $val['brand_id']) {
                $brand = $val->brand;
                $list[$key]['brand_name'] =$brand['name'];
            }
            $list[$key]['sku_num'] = $productModel->where([['goods_id', '=', $val['id']]])->count();//判断是多规格还是单规格
        }
        return $list;
    }

    /**
     * 更新商品信息
     * @param       $goods_id
     * @param array $data
     * @return false|int
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:37
     */
    public function updateGoods($goods_id, $data = [])
    {
        return $this->allowField(true)->update($data, ['id' => $goods_id]);
    }

    /**
     * 查询商品列表信息
     * @param string $fields 查询字段
     * @param array $where 查询条件
     * @param string $order 查询排序
     * @param int $page 当前页码
     * @param int $limit 每页数量
     * @param string $whereRaw
     * @return array
     */
    public function getList($fields = '*', $where = [], $order = 'id desc', $page = 1, $limit = 10, $whereRaw = '')
    {

        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];

        if ($fields != '*') {
            $tmpData = explode(',', $fields);
            if (in_array('products', $tmpData)) {
                $key = array_search('products', $tmpData);
                unset($tmpData[$key]);
            }
            $fields = implode(',', $tmpData);
        }
        $order_desc = []; //订单描述
        if ($order) {
            $order_array = explode(',', $order);
            foreach ((array)$order_array as $key => $val) {
                $tmp_order                 = explode(' ', trim($val));
                $order_desc[$tmp_order[0]] = $tmp_order[1];
            }
        }
        $order_desc['id'] = 'desc';

        $list  = $this
            ->alias('g')
            ->field($fields)
            ->where($where)
            ->whereRaw($whereRaw)
            ->order($order_desc)
            ->page($page, $limit)
            ->select();

        $total = $this
            ->alias('g')
            ->field($fields)
            ->where($where)
            ->whereRaw($whereRaw)
            ->count();


        /*取出标签、品牌、分类*/
        if (!$this->checkWhere($where)) {

            $label_ids = $this
                ->alias('g')
                ->field('label_ids')
                ->where($where)
                ->whereRaw($whereRaw)
                ->group('label_ids')
                ->select();
            if (!$label_ids->isEmpty()) {
                $label_arr = [];
                foreach ($label_ids->toArray() as $key => $value) {
                    $label     = explode(',', $value['label_ids']);
                    $label_arr = array_merge((array)$label_arr, (array)$label);
                }
                $label_arr  = array_unique($label_arr);
                $labelModel = new Label();
                $label_ids  = $labelModel->where([['id', 'in', implode(',', $label_arr)]])->select();
            }


            $result['filter']['label_ids'] = $label_ids;
            $result['filter']['brand_ids'] = $this
                ->field('g.brand_id,b.name')
                ->alias('g')
                ->leftJoin('brand b', 'b.id = g.brand_id')
                ->where($where)
                ->whereRaw($whereRaw)
                ->where([['g.brand_id', '<>', '0']])
                ->group('g.brand_id')
                ->select();

            /*$result['filter']['type_ids']  = $this
                ->field('g.goods_type_id,gt.name')
                ->alias('g')
                ->leftJoin('goods_type gt', 'g.goods_type_id = gt.id')
                ->where($where)
                ->where([['g.goods_type_id', '<>', '0']])
                ->group('goods_type_id')
                ->select();*/

            $result['filter']['goods_cat'] = $this
                ->field('g.goods_cat_id,gc.name')
                ->alias('g')
                ->leftJoin('goods_cat gc', 'g.goods_cat_id = gc.id')
                ->where($where)
                ->whereRaw($whereRaw)
                ->where([['g.goods_cat_id', '<>', '0']])
                ->group('goods_cat_id')
                ->select();
        }
        if (!$list->isEmpty()) {
            foreach ($list as $key => $value) {
                $goods = $this->getGoodsDetial($value['id'], $fields);

                if ($goods['status']) {
                    $list[$key] = $goods['data'];
                }
                //                $image_url = _sImage($value['image_id']);
                //                $list[$key]['image_url'] = $image_url;
                //                $list[$key]['label_ids'] = getLabel($value['label_ids']);
            }

            $result['data'] = $list->toArray();
        }
        $result['total'] = ceil($total / $limit);
        return $result;
    }

    /***
     * 获取商品详情
     * @param $gid
     * @param string $fields
     * @param string $token
     * @param string $type goods:商品 pintuan:拼团 group:团购 skill:秒杀 bargain:砍价
     * @param array $params 扩展字段，可传团购秒杀id等
     * @return array
     */
    public function getGoodsDetial($gid, $fields = '*', $token = '', $type = 'goods', $params = [])
    {
        $result        = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];
        $productsModel = new Products();
        $preModel      = '';
        if ($fields == '*') {
            $preModel = 'goodsCat';
        } else {
            if (stripos($fields, 'goods_cat_id') !== false) {
                $preModel .= 'goodsCat';
            }
        }
        $list = $this::with($preModel)->field($fields)->where(['id' => $gid])->find();

        if ($list) {
            if (isset($list['brand_id']) && $list['brand_id']) {
                $list->brand;
            }
            //$list = $list->toArray();
            //$list['products'] = $this->products($list['id']);

            if (isset($list['image_id'])) {
                $image_url         = _sImage($list['image_id']);
                $list['image_url'] = $image_url;
            }

            if (isset($list['label_ids'])) {
                $list['label_ids'] = getLabel($list['label_ids']);
            } else {
                $list['label_ids'] = [];
            }
            //取默认货品
            $default_product = $productsModel->where(['goods_id' => $gid, 'is_defalut' => $productsModel::DEFALUT_YES])->find();
            if (!$default_product) {
                return error_code(10000);
            }
            $user_id      = getUserIdByToken($token); //获取user_id
            $product_info = $productsModel->getProductInfo($default_product['id'], true, $user_id, $type, $params);
            if (!$product_info['status']) {
                return $product_info;
            }

            $list['product'] = $product_info['data'];
            $list['price']   = $list['product']['price'];
            if ($list['spes_desc']) {
                $list['spes_desc'] = unserialize($list['spes_desc']);
            }
            //取出图片集
            $imagesModel = new GoodsImages();
            $images      = $imagesModel->where(['goods_id' => $list['id']])->order('sort asc')->select();
            $album       = [];
            if (isset($list['image_url'])) {
                $album[] = $list['image_url'];
            }
            if (!$images->isEmpty()) {
                foreach ($images as $v) {
                    $album[] = _sImage($v['image_id']);
                }
            }
            $list['album'] = $album;

            //获取当前登录是否收藏
            $list['isfav'] = $this->getFav($list['id'], $user_id);

            //图片处理
            if (isset($list['intro'])) {
                $list['intro'] = clearHtml($list['intro'], ['width', 'height']);
                $list['intro'] = str_replace("<img", "<img style='max-width: 100%;float:left'", $list['intro']);
                $list['intro'] = str_replace('<p', "<p style='overflow:hidden'", $list['intro']);
            }
            $list           = $this->filterFields($list, 'goods');
            $result['data'] = $list;
        }
        return $result;
    }


    /**
     * 获取默认规格
     * @param $specDefault //默认规格
     * @param $specKey //当前规格名称
     * @param $specValue //当前规格值
     * @return string
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-31 11:32
     */
    private function getDefaultSpec($specDefault, $specKey, $specValue)
    {
        $isDefault = '2';
        foreach ((array)$specDefault as $key => $val) {
            if ($val['sku_name'] == $specKey && $val['sku_value'] == $specValue) {
                $isDefault = '1';
            }
        }
        return $isDefault;
    }

    /**
     * 获取商品下面所有货品
     * @param $goods_id
     * @param bool $isPromotion
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function products($goods_id, $isPromotion = true)
    {
        $productModel = new Products();
        $pids         = $productModel->field('id')->where(['goods_id' => $goods_id])->select();
        $products     = [];

        if (!$pids->isEmpty()) {
            foreach ($pids as $key => $val) {
                $productInfo = $productModel->getProductInfo($val['id'], $isPromotion);
                if ($productInfo['status']) {
                    $products[$key] = $productInfo['data'];
                } else {
                    $products[$key] = [];
                }
            }
        }
        return $products;
    }

    /**
     * 获取goods表图片对应图片地址
     * @return \think\model\relation\HasOne
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-29 16:26
     */
    public function defaultImage()
    {
        return $this->hasOne('Images', 'id', 'image_id')->field('id,url')->bind(['image_url' => 'url']);
    }

    /**
     * 获取品牌信息
     * @return \think\model\relation\HasOne
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-31 11:43
     */
    public function brand()
    {
        return $this->hasOne('Brand', 'id', 'brand_id')->field('id,name,logo')->bind(['brand_name' => 'name']);
    }

    /**
     * 获取分类名称
     * @return \think\model\relation\HasOne
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-31 11:46
     */
    public function goodsCat()
    {
        return $this->hasOne('GoodsCat', 'id', 'goods_cat_id')->field('id,name')->bind(['cat_name' => 'name']);
    }

    /**
     * 获取类型名称
     * @return \think\model\relation\HasOne
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-03 8:55
     */
    public function goodsType()
    {
        return $this->hasOne('GoodsType', 'id', 'goods_type_id')->field('id,name')->bind(['type_name' => 'name']);
    }

    /**
     * 获取销售价
     * @param $product
     * @return mixed
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 10:26
     */
    public function getPrice($product, $user_id = '')
    {
        $price = $product['price'];

        //获取会员优惠
        $grade_price = [];
        if ($user_id) {
            $user_grade                    = get_user_info($user_id, 'grade');
            $priceData['grade_info']['id'] = $user_grade;
            $goodsGradeModel               = new GoodsGrade();
            $goodsGrade                    = $goodsGradeModel->getGradePrice($product['goods_id']);
            $userGradeModel                = new UserGrade();
            if ($goodsGrade['status']) {
                foreach ($goodsGrade['data'] as $key => $val) {
                    $grade_price[$key]               = $val;
                    $userGrade                       = $userGradeModel->where(['id' => $val['grade_id']])->field('name')->find();
                    $grade_price[$key]['grade_name'] = isset($userGrade['name']) ? $userGrade['name'] : '';
                    if ($user_grade && $user_grade == $val['grade_id']) {
                        $price                           = ($product['price'] - $val['grade_price']) > 0 ? $product['price'] - $val['grade_price'] : 0;
                        $priceData['grade_info']['name'] = $grade_price[$key]['grade_name'];
                    }
                    $grade_price[$key]['grade_price'] = ($product['price'] - $val['grade_price']) > 0 ? $product['price'] - $val['grade_price'] : 0;
                }
            }
        } else {
            $priceData['grade_info'] = [];
        }
        $priceData['grade_price'] = $grade_price;
        $priceData['price']       = $price;
        return $priceData;
    }

    /**
     * 获取可用库存。库存机制：商品下单 总库存不变，冻结库存加1， 商品发货：冻结库存减1，总库存减1，   商品退款：总库存不变，冻结库存减1, 商品退款：总库存加1，冻结库存不变, 可销售库存：总库存-冻结库存
     * @param $product
     * @return mixed
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 10:30
     */
    public function getStock($product)
    {
        return ($product['stock'] - $product['freeze_stock']) > 0 ? $product['stock'] - $product['freeze_stock'] : 0;
    }

    /**
     * 库存改变机制。库存机制：商品下单 总库存不变，冻结库存加1， 商品发货：冻结库存减1，总库存减1，   商品退款&取消订单：总库存不变，冻结库存减1, 商品退货：总库存加1，冻结库存不变, 可销售库存：总库存-冻结库存
     * @param $product_id
     * @param string $type
     * @param int $num
     * @return array
     * @throws \think\Exception
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 10:34
     */
    public function changeStock($product_id, $type = 'order', $num = 0)
    {
        $result = error_code(12703);
        if ($product_id === '') {
            //            $result['msg'] = '货品ID不能为空';
            return error_code(14011);
        }

        $productModel = new Products();
        $where        = [];
        $where[]      = ['id', 'eq', $product_id];
        $product      = $productModel->where($where)->field('goods_id')->find();

        switch($type) {
            case 'order': //下单
                $exp     = Db::raw('IF(stock < freeze_stock, 0, stock - freeze_stock)-' . $num . '>=0');
                $where[] = [0, 'exp', $exp];
                $this->where(['id' => $product['goods_id']])->setInc('buy_count', $num);
                $res = $productModel->where($where)->setInc('freeze_stock', $num);
                break;
            case 'send': //发货
                $where[] = ['freeze_stock', '>=', $num];
                $res     = $productModel->where($where)->setDec('stock', $num);
                if ($res !== false) {
                    $res = $productModel->where($where)->setDec('freeze_stock', $num);
                } else {
                    //                    $result['msg'] = '库存更新失败';
                    return $result;
                }
                break;
            case 'refund': //退款
                $res = $productModel->where($where)->setDec('freeze_stock', $num);
                break;
            case 'return': //退货
                //$this->where(['id' => $product['goods_id']])->setDec('buy_count', $num);
                $res = $productModel->where($where)->setInc('stock', $num);
                break;
            case 'cancel': //取消订单
                //$this->where(['id' => $product['goods_id']])->setDec('buy_count', $num);
                $res = $productModel->where($where)->setDec('freeze_stock', $num);
                break;
            default:
                $res = $productModel->where($where)->setInc('freeze_stock', $num);
                break;
        }
        if ($res > 0) {
            $result['msg']    = '库存更新成功';
            $result['status'] = true;
            return $result;
        } else {
            //            $result['msg'] = '库存不足';
            return error_code(12702);
        }

        return $result;
    }

    /**
     * 无数据转换
     * @param $goods_id
     * @param string $fields
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOne($goods_id, $fields = '*')
    {
        $result = error_code(12700);
        $data   = $this->where(['id' => $goods_id])->field($fields)->find();
        if ($data) {
            $goodsImagesModel = new goodsImages();
            $images           = $goodsImagesModel->getAllImages($data->id);
            $tmp_image        = [];
            if ($images['status']) {
                foreach ((array)$images['data'] as $key => $val) {
                    $images['data'][$key]['image_path'] = _sImage($val['image_id']);
                }
                $tmp_image[]    = [
                    'goods_id'   => $data['id'],
                    'image_id'   => $data['image_id'],
                    'image_path' => _sImage($data['image_id']),
                ];
                $images['data'] = array_merge((array)$images['data'], (array)$tmp_image);
                $images['data'] = array_reverse($images['data']);
            } else {
                //单图
                $tmp_image[]    = [
                    'goods_id'   => $data['id'],
                    'image_id'   => $data['image_id'],
                    'image_path' => _sImage($data['image_id']),
                ];
                $images['data'] = $tmp_image;
            }
            $data['products'] = $this->products($goods_id, false);

            $data['images']   = $images['data'];
            $result['data']   = $data;
            $result['msg']    = '查询成功';
            $result['status'] = true;
        }
        return $result;
    }

    /**
     * 判断是否收藏过
     * @param int $goods_id
     * @param string $user_id
     * @return string
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-03 8:36
     */
    public function getFav($goods_id = 0, $user_id = '')
    {
        $favRes = 'false';
        if ($user_id) {
            $goodsCollectionModel = new GoodsCollection();
            $isfav                = $goodsCollectionModel->check($user_id, $goods_id);
            if ($isfav) {
                $favRes = 'true';
            }
        }
        return $favRes;
    }

    /**
     * 删除商品
     * @param int $goods_id
     * @return array
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delGoods($goods_id = 0)
    {
        $result = error_code(12700);
        $goods  = $this::get($goods_id);
        if (!$goods) {
            return $result;
        }

        $this->startTrans();

        $res = $this->where(['id' => $goods_id])->delete();
        if (!$res) {
            $this->rollback();
            //            $result['msg'] = '商品删除失败';
            return error_code(12704);
        }
        $productsModel = new Products();
        $delProduct    = $productsModel->where(['goods_id' => $goods_id])->delete(true);
        $this->commit();
        hook('deletegoodsafter', $goods); //删除商品后增加钩子

        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }

    /**
     * 批量上下架
     * @param $ids
     * @param string $type
     * @return static
     */
    public function batchMarketable($ids, $type = 'up')
    {

        if ($type == 'up') {
            $marketable = self::MARKETABLE_UP;
        } elseif ($type == 'down') {
            $marketable = self::MARKETABLE_DOWN;
        }
        $data = [
            'marketable'   => $marketable,
            $type . 'time' => time(),
        ];
        return $this::where('id', 'in', $ids)->update($data);
    }

    /**
     * 获取csv数据
     * @param $post
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCsvData($post)
    {
        $result    = error_code(10083);
        $header    = $this->csvHeader();
        $goodsData = $this->tableData($post, false);
        if ($goodsData['count'] > 0) {
            $tempBody = $goodsData['data'];
            $body     = [];
            $i        = 0;
            foreach ($tempBody as $key => $val) {
                //$product = $val->products;
                $product = $this->products($val['id'], false);
                if ($val['spes_desc']) { //规格数据处理
                    $tempSpec  = unserialize($val['spes_desc']);
                    $spes_desc = '';
                    foreach ($tempSpec as $tsKey => $tsVal) {
                        $spes_desc = $spes_desc . '|' . $tsKey . ':';
                        if (is_array($tsVal)) {
                            foreach ($tsVal as $sk => $sv) {
                                $spes_desc = $spes_desc . $sv . ',';
                            }
                            $spes_desc = substr($spes_desc, 0, -1);
                        } else {
                            $spes_desc = $spes_desc . $tsVal;
                        }
                    }
                    $spes_desc        = substr($spes_desc, 1);
                    $val['spes_desc'] = $spes_desc;

                }
                if (count($product) > 1) {//多规格
                    foreach ($product as $productKey => $productVal) {
                        $i++;
                        if ($productKey != 0) {
                            unset($val);
                        }
                        $val['sn']                = $productVal['sn'];
                        $val['price']             = $productVal['price'];
                        $val['costprice']         = $productVal['costprice'];
                        $val['mktprice']          = $productVal['mktprice'];
                        $val['stock']             = $productVal['stock'];
                        $val['product_spes_desc'] = $productVal['spes_desc'];
                        $val['is_defalut']        = $productVal['is_defalut'];
                        $val['is_spec']           = '1';//多规格
                        foreach ($header as $hk => $hv) {
                            if (isset($val[$hv['id']]) && isset($hv['modify'])) {
                                if ($val[$hv['id']] && function_exists($hv['modify'])) {
                                    $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                                }
                            } elseif (isset($val[$hv['id']])) {
                                $body[$i][$hk] = $val[$hv['id']];
                            } else {
                                $body[$i][$hk] = '';
                            }
                        }

                    }
                } else {//单规格
                    $val['is_spec'] = '2';
                    $i++;
                    $val['sn']                = $product[0]['sn'];
                    $val['price']             = $product[0]['price'];
                    $val['costprice']         = $product[0]['costprice'];
                    $val['mktprice']          = $product[0]['mktprice'];
                    $val['stock']             = $product[0]['stock'];
                    $val['product_spes_desc'] = $product[0]['spes_desc'];
                    $val['is_defalut']        = $product[0]['is_defalut'];
                    foreach ($header as $hk => $hv) {
                        if (isset($val[$hv['id']]) && isset($hv['modify'])) {
                            if ($val[$hv['id']] && function_exists($hv['modify'])) {
                                $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                            }
                        } elseif (isset($val[$hv['id']])) {
                            $body[$i][$hk] = $val[$hv['id']];
                        } else {
                            $body[$i][$hk] = '';
                        }
                    }
                }
            }
            $result['status'] = true;
            $result['msg']    = '导出成功';
            $result['data']   = $body;
            return $result;
        } else {
            //失败，导出失败
            return $result;
        }
    }

    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id'   => 'name',
                'desc' => '商品名称',
            ],
            [
                'id'     => 'bn',
                'desc'   => '商品编号',
                'modify' => 'convertString'
            ],
            [
                'id'   => 'brief',
                'desc' => '商品简介',
            ],
            [
                'id'   => 'image_id',
                'desc' => '商品主图',
            ],
            [
                'id'   => 'cat_name',
                'desc' => '商品分类',
            ],
            [
                'id'   => 'type_name',
                'desc' => '商品类型',
            ],
            [
                'id'   => 'brand_name',
                'desc' => '品牌名称',
            ],
            [
                'id'     => 'is_nomal_virtual',
                'desc'   => '是否实物',
                'modify' => 'getBool'
            ],
            [
                'id'     => 'marketable',
                'desc'   => '是否上架',
                'modify' => 'getMarketable',
            ],
            [
                'id'   => 'weight',
                'desc' => '商品重量',
            ],
            [
                'id'   => 'unit',
                'desc' => '商品单位',
            ],
            [
                'id'   => 'intro',
                'desc' => '商品详情',
            ],
            [
                'id'   => 'spes_desc',
                'desc' => '商品规格',
            ],
            [
                'id'   => 'params',
                'desc' => '商品参数',
                //'modify'=>'getParams', //todo 格式化商品参数

            ],
            [
                'id'   => 'sort',
                'desc' => '商品排序',
            ],
            [
                'id'     => 'is_recommend',
                'desc'   => '是否推荐',
                'modify' => 'getBool'
            ],
            [
                'id'     => 'is_hot',
                'desc'   => '是否热门',
                'modify' => 'getBool'

            ],
            [
                'id'     => 'is_spec',
                'desc'   => '是否多规格',
                'modify' => 'getBool'
            ],
            [
                'id'     => 'label_ids',
                'desc'   => '商品标签',
                'modify' => 'getExportLabel'
            ],
            [
                'id'     => 'ctime',
                'desc'   => '创建时间',
                'modify' => 'getTime'
            ],
            [
                'id'     => 'utime',
                'desc'   => '更新时间',
                'modify' => 'getTime'
            ],
            [
                'id'   => 'product_spes_desc',
                'desc' => '货品规格',
            ],
            [
                'id'     => 'sn',
                'desc'   => '货品编码',
                'modify' => 'convertString'
            ],
            [
                'id'   => 'price',
                'desc' => '货品价格',
            ],
            [
                'id'   => 'costprice',
                'desc' => '成本价',
            ],
            [
                'id'   => 'mktprice',
                'desc' => '市场价',
            ],
            [
                'id'   => 'stock',
                'desc' => '货品库存',
            ],
            [
                'id'     => 'is_defalut',
                'desc'   => '是否默认货品',
                'modify' => 'getBool'
            ]
        ];
    }

    /**
     * 商品列表页统计商品相关 todo 警戒库存设置
     * @param array $baseFilter
     * @return array
     */
    public function staticGoods($baseFilter = [])
    {

        $total        = $this->where($baseFilter)->count('id');
        $baseFilter[] = ['marketable', 'eq', self::MARKETABLE_UP];


        $totalMarketableUp   = $this->where($baseFilter)->count('id');
        $baseFilter1[]       = ['marketable', 'eq', self::MARKETABLE_DOWN];
        $totalMarketableDown = $this->where($baseFilter1)->count('id');
        //警戒库存
        $SettingModel = new Setting();

        $goods_stocks_warn = $SettingModel->getValue('goods_stocks_warn');
        $goods_stocks_warn = $goods_stocks_warn ? $goods_stocks_warn : '10';
        unset($baseFilter['marketable']);
        $productModel = new Products();

        $totalWarn = $productModel->where(Db::raw('IF(stock < freeze_stock, 0, stock - freeze_stock)<' . $goods_stocks_warn))->group('goods_id')->count('id');
        return [
            'totalGoods'          => $total,
            'totalMarketableUp'   => $totalMarketableUp,
            'totalMarketableDown' => $totalMarketableDown,
            'totalWarn'           => $totalWarn,
        ];
    }

    /**
     * 获取重量
     * @param $product_id
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWeight($product_id)
    {
        $where[] = ['id', 'eq', $product_id];
        $goods   = model('common/Products')->field('goods_id')
            ->where($where)
            ->find();
        if ($goods['goods_id'] != 0) {
            $wh[] = ['id', 'eq', $goods['goods_id']];

            $weight = $this->field('weight')
                ->where($wh)
                ->find();
        } else {
            $weight['weight'] = 0;
        }
        return $weight['weight'] ? $weight['weight'] : 0;
    }

    /**
     * 导出验证
     * @param array $params
     * @return array
     */
    public function exportValidate(&$params = [])
    {
        $result = [
            'status' => true,
            'data'   => [],
            'msg'    => '验证成功',
        ];
        return $result;
    }

    /**
     * 获取某个分类的热卖商品
     * @param $cat_id
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsCatHotGoods($cat_id, $limit = 6)
    {
        $return                 = error_code(10025);
        $where[]                = ['is_hot', 'eq', self::HOT_YES];
        $where[]                = ['marketable', 'eq', self::MARKETABLE_UP];
        $where[]                = ['goods_cat_id', 'eq', $cat_id];
        $return['data']['list'] = $this->field('id,name,image_id,price,brief')
            ->where($where)
            ->limit(0, $limit)
            ->order('ctime DESC')
            ->select();

        $catModel               = new GoodsCat();
        $catName                = $catModel->getNameById($cat_id);
        $return['data']['name'] = $catName['data'];

        if ($return['data']['list'] !== false) {
            if (count($return['data']['list']) > 0) {
                foreach ($return['data']['list'] as $k => &$v) {
                    $v['image_url'] = _sImage($v['image_id']);
                }
            }
            $return['status'] = true;
            $return['msg']    = '获取成功';
        }
        return $return;
    }


    /**
     * 过滤商品输出字段
     * @param $data
     * @param string $type
     * @return array
     */
    private function filterFields($data, $type = 'goods')
    {
        if ($data) {
            if (!is_array($data)) {
                $data = $data->toArray();
            }
            foreach ($data as $key => $value) {
                if ($type == 'goods') {
                    if ($key == 'product') {
                        $data[$key] = $this->filterFields($value, 'product');
                    } else {
                        if (in_array($key, $this->goodsForbidFields)) {
                            unset($data[$key]);
                        }
                    }
                } elseif ($type == 'product') {
                    if (in_array($key, $this->productForbidFields)) {
                        unset($data[$key]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 判断是否返回筛选
     * @param array $where
     * @return bool
     */
    private function checkWhere($where = [])
    {
        $noFilter = [
            'g.brand_id', 'g.price', 'g.label_id', 'g.goods_cat_id'
        ];
        foreach ((array)$where as $key => $val) {
            if (in_array($val[0], $noFilter) || $val[0] == '') {
                return true;
            }
        }
        return false;
    }


    /**
     * 获取商品名称
     * @param $goods_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsName($goods_id)
    {
        $where[] = ['id', 'eq', $goods_id];
        $info    = $this->field('name')
            ->where($where)
            ->find();
        return $info['name'] ? $info['name'] : '';
    }


    /**
     * 获取单规格商品列表
     * @param $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSingleGoodsList($field)
    {
        $return = error_code(10037);

        $where[] = ['marketable', 'eq', self::MARKETABLE_UP];
        $where[] = ['spes_desc', 'eq', ''];

        $return['data'] = $this->field($field)
            ->where($where)
            ->order('sort ASC')
            ->select();

        if ($return['data'] !== false) {
            $return['status'] = true;
            $return['msg']    = '成功';
        }

        return $return;
    }


    /*
     * 获取全部商品
     * */
    public function goods_all($page, $limit, $order)
    {
        $return              = error_code(10037);
        $where['marketable'] = '1';
        $goodsData           = $this->where($where)
            ->order($order)
            ->page($page, $limit)
            ->select();
        if (!$goodsData) {
            return error_code(10025);
        }
        foreach ($goodsData as &$v) {
            $image      = Db::table('jshop_images')
                ->field('id,url,ctime')
                ->where('id', $v['image_id'])
                ->select();
            $v['image'] = $image[0]['url'];
        }
        $return['msg']    = '获取成功';
        $return['status'] = true;
        $return['data']   = $goodsData;
        return $return;
    }


    /*
     * 获取上新商品
     * */
    public function newgoods($page, $limit, $order)
    {
        $return              = error_code(10037);
        $where['marketable'] = '1';
        $goodsData           = $this->where($where)
            ->order($order)
            ->page($page, $limit)
            ->select();
        if (!$goodsData) {
            return  error_code(10025);
        }
        foreach ($goodsData as &$v) {
            $image      = Db::table('jshop_images')
                ->field('id,url,ctime')
                ->where('id', $v['image_id'])
                ->select();
            $v['image'] = $image[0]['url'];
        }
        $return['msg']    = '获取成功';
        $return['status'] = true;
        $return['data']   = $goodsData;
        return $return;
    }


    /*
     * 促销商品
     * */
    public function promotiongoods($page, $limit, $order)
    {
        $return                = error_code(10037);
        $where['marketable']   = '1';
        $where['is_recommend'] = '1';
        $goodsData             = $this->field('id,name,price,image_id')
            ->where($where)
            ->order($order)
            ->page($page, $limit)
            ->select();
        if (!$goodsData) {
            return error_code(12705);
        }
        foreach ($goodsData as &$v) {
            $image      = Db::table('jshop_images')
                ->field('id,url,ctime')
                ->where('id', $v['image_id'])
                ->page($page, $limit)
                ->select();
            $v['image'] = $image[0]['url'];
        }
        $return['msg']    = '获取促销商品成功';
        $return['status'] = true;
        $return['data']   = $goodsData;
        return $return;
    }


    /*
     * 关联动态商品
     * */
    public function assocGoods($article_id)
    {
        $data = $this->field('id,name,price,image_id')
            ->where([
                'id' => [$article_id]
            ])->select();
        foreach ($data as &$v) {
            $v['image'] = _sImage($v['image_id']);
        }
        return $data;
    }

    /**
     * 获取销量排名前十的商品
     *
     * @return array
     */
    public function salesRanking()
    {
        $result = [
            'status' => true,
            'msg' => '',
            'data' => []
        ];
        $data = $this->field(['id', 'name', 'image_id', 'buy_count', 'price'])
            ->where('marketable', '=', self::MARKETABLE_UP)
            ->order('buy_count', 'desc')->order('sort')->limit(10)->select();
        foreach ($data as &$val) {
            $val['image'] = _sImage($val['image_id']);
        }
        $result['data'] = $data;
        return $result;
    }

    // 获取角色,废弃的旧方法
    public function getRoleDetial($gid)
    {
        $result        = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];
        $productsModel = new Products();
        $cate_mod = new \app\common\model\ArticleType();

        $field = 'id,bn,name,brief,image_id,intro,params,work,rarity,sex,name_eng,cate_id,price';
        $info = $this->field($field)->where(['id' => $gid])->find();
        
        if ($info) {
            
            $info['cate_name'] = $cate_mod->where('id',$info['cate_id'])->value('type_name'); 
            if (isset($info['image_id'])) {
                $image_url         = _sImage($info['image_id']);
                $info['image_url'] = $image_url;
            }

            $info['params'] = unserialize($info['params']);
            $new_params = [];
            $temp_params = [];

            if(!empty($info['params'])){
                foreach ($info['params'] as $p => $r) {
                    // if(is_array($info['params'][$p])){
                    //     $temp_params[$p] = implode(',',$r);
                    // }else{
                        $temp_params[$p] = $r;
                    // }

                    $new_params = $temp_params;
                }
            }
            
            $info['params'] = $new_params;
            
            $product_list = $productsModel->field('specs_name,specs_type,image_id')->where(['goods_id' => $gid])->select();
            
            $new_products = [];
            foreach ($product_list as $key => $value) {
                // $product_list[$key]['image'] = _sImage($value['image_id']);
                $new_products[$value['specs_type']][] = $value;
            }
            // dump($new_products);
            // die;
        
            $last_products = [];

            foreach ($new_products as $ke => $val) {
                foreach ($val as $k => $v) {
                    $last_products[$v['specs_type']][] = [
                        'specs_name' => $v['specs_name'],
                        'img' => _sImage($v['image_id'])
                        
                    ];
                    unset($v['specs_type']);
                }
            }
       
            $info['attr'] = $last_products;

            // 角色道具
            $propMod = new \app\common\model\Prop();
            $goods_prod = $propMod->where('goods_id',$info['id'])->order('sort asc')->select();

            foreach ($goods_prod as $key => $value) {
                $goods_prod[$key]['cover'] = _sImage($value['cover']);
                $goods_prod[$key]['role_img'] = _sImage($value['role_img']);
                $goods_prod[$key]['prop_img'] = _sImage($value['prop_img']);
            }
            $info['expression'] = $goods_prod;

            $info['post_price_info'] = [
                'state' => false,
                'price' => 0,
                'e_time' => 0,
                'history' => []
            ];

            $info['role_info'] = [];
            //     $product_info = $productsModel->getProductInfo($default_product['id'], true, $user_id, $type, $params);
            //     if (!$product_info['status']) {
            //         return $product_info;
            //     }

            //     $list['product'] = $product_info['data'];
            //     $list['price']   = $list['product']['price'];
            //     if ($list['spes_desc']) {
            //         $list['spes_desc'] = unserialize($list['spes_desc']);
            //     }
            //     //取出图片集
            //     $imagesModel = new GoodsImages();
            //     $images      = $imagesModel->where(['goods_id' => $list['id']])->order('sort asc')->select();
            //     $album       = [];
            //     if (isset($list['image_url'])) {
            //         $album[] = $list['image_url'];
            //     }
            //     if (!$images->isEmpty()) {
            //         foreach ($images as $v) {
            //             $album[] = _sImage($v['image_id']);
            //         }
            //     }
            //     $list['album'] = $album;

            //     //获取当前登录是否收藏
            //     $list['isfav'] = $this->getFav($list['id'], $user_id);

            //     //图片处理
            //     if (isset($list['intro'])) {
            //         $list['intro'] = clearHtml($list['intro'], ['width', 'height']);
            //         $list['intro'] = str_replace("<img", "<img style='max-width: 100%;float:left'", $list['intro']);
            //         $list['intro'] = str_replace('<p', "<p style='overflow:hidden'", $list['intro']);
            //     }
            //     $list           = $this->filterFields($list, 'goods');
            $result['data'] = $info;
        }
        return $result;
    }

    // 获取角色
    public function getRoleDetialNew($gid,$user_id = '')
    {
        $result        = [
            'status' => true,
            'data'   => [],
            'msg'    => ''
        ];
        
        $extend_mod = new \app\common\model\GoodsExtend();
        $part_mod = new \app\common\model\GoodsPart();
        $assembly_mod = new \app\common\model\Assembly();
        $sale = new \app\common\model\UserSale();

        $info = $this->where('id',$gid)->field('id,name,work,sex,bn,price,name_eng,royalties')->find();
      
        if ($info) {
            if(getLng() == 'en'){
                $info['name'] = $info['name_eng'];
            }
            $info['price'] = empty($info['price']) ? 0 : $info['price']; 
            $extend_data = $extend_mod->where('goods_id',$gid)->find();
            if($extend_data){
                $info['role_img'] = _sImage($extend_data['role_img']);
                $extend_data['history'] = html_entity_decode($extend_data['history']);
                // 称号排序
                $role_title_info = unserialize($extend_data['role_title_info']);
                $role_sort = array_column(array_values($role_title_info), 'sort');
                array_multisort($role_sort,SORT_DESC,$role_title_info);
                $extend_data['role_title_info'] = $role_title_info;

                $extend_data['character_info'] = unserialize($extend_data['character_info']);//特性
                
                if($extend_data['role_type'] == '老头'){
                    $info['new_img'] = $info['role_img'].'?x-oss-process=image/crop,x_430,y_1620,w_4160,h_5000/format,webp';
                }elseif($extend_data['role_type'] == '哥布林'){
                    $info['new_img'] = $info['role_img'].'?x-oss-process=image/crop,x_430,y_1420,w_4160,h_3600/format,webp';
                }elseif($extend_data['role_type'] == '胖妞'){
                    $info['new_img'] = $info['role_img'].'?x-oss-process=image/crop,x_330,y_1620,w_4160,h_4600/format,webp';
                }elseif($extend_data['role_type'] == '女标'){
                    $info['new_img'] = $info['role_img'].'?x-oss-process=image/crop,x_530,y_1620,w_4160,h_5200/format,webp';
                }else{
                    $info['new_img'] = $info['role_img'].'?x-oss-process=image/crop,x_530,y_1620,w_4160,h_6600/format,webp';
                }
                 


                // 职业类目1士2农3工4商
                if($extend_data['work_type'] == 1){
                    // 士
                    $work_img = 'https://ec.wexiang.vip/character/scholar.png';
                }elseif($extend_data['work_type'] == 2){
                    // 农
                    $work_img = 'https://ec.wexiang.vip/character/farmer.png';

                }elseif($extend_data['work_type'] == 3){
                    // 工
                    $work_img = 'https://ec.wexiang.vip/character/worker.png';
                }else{
                    // 商
                    $work_img = 'https://ec.wexiang.vip/character/businessman.png';
                }

                $extend_data['work_img'] = $work_img;
                $extend_data['item_10'] = 'https://ec.wexiang.vip/character/item_10.png';

                $character_1 = [];
                $character_2 = [];

                if($extend_data['character_info']){
                    $temp_character = [];
                    foreach ($extend_data['character_info'] as $key => $value) {
                        if($value['value'] <= 0){
                            $character_2[] = $value;//恶
                        }else{
                            $character_1[] = $value;//善
                        }

                        $temp_character[] = $value['name'];
                    }

                    $info['character'] = implode('、', $temp_character);
                    $extend_data['character_info_friendly'] = $character_1;
                    $extend_data['character_info_evil'] = $character_2;
                }else{
                    $info['character'] = '';
                }

                // 天赋
                $talent_info = unserialize($extend_data['talent_info']);
                if($talent_info){
                    foreach ($talent_info as $key1 => $value1) {
                        $talent_info[$key1]['img'] = _sImage($value1['img']);
                    }
                }

                $extend_data['talent_info'] = $talent_info;

                // 潜能
                $potential_info = unserialize($extend_data['potential_info']);
                if($potential_info){
                    foreach ($potential_info as $key2 => $value2) {
                        $potential_info[$key2]['img'] = _sImage($value2['img']);
                    }
                }

                $extend_data['potential_info'] = $potential_info;

                // 立场
                $extend_data['position_info'] = calRolePosition($gid);
            }

            $info['extend'] = $extend_data;

            // 组件
            $part_arr = [];
            $part_ids = $part_mod->where('goods_id',$gid)->column('part_id');
            if(!empty($part_ids)){
                $info_part = $assembly_mod->whereIn('id',$part_ids)->select();
                if($info_part){
                    foreach ($info_part as $pkey => $pvalue) {
                        $part_arr[] = [
                            'name' => $pvalue['part_name'],
                            'cate' => $pvalue['part_cate'],
                            'number' => computeRate($pvalue['id'])
                        ];  
                    }

                }
            }

            $info['part'] = $part_arr;

            $info['obj_type'] = 5;
            // 角色道具
            $propMod = new \app\common\model\Prop();
            $goods_prod = $propMod->where('goods_id',$info['id'])->where('type',2)->order('sort asc')->select();

            foreach ($goods_prod as $key => $value) {
                $goods_prod[$key]['cover'] = _sImage($value['cover']);
                $goods_prod[$key]['role_img'] = _sImage($value['role_img']);
                $goods_prod[$key]['prop_img'] = _sImage($value['prop_img']);
                $goods_prod[$key]['head_img'] = _sImage($value['role_img']).'?x-oss-process=image/crop,x_1900,y_1900,w_1200,h_1200/format,webp';
            }

            $info['prop_count'] = $propMod->where('goods_id',$info['id'])->where('type',2)->count();
            $info['prop_card'] = [
                'prop_card' => 'https://pzss.oss-cn-guangzhou.aliyuncs.com/static/uploads/images/2022/10/28/1666929247635b525fc1272.png',
                'card_img' => _sImage($propMod->where('goods_id',$info['id'])->where('type',1)->value('prop_img'))
            ];

            $info['post_price_info'] = isUserPostPrice($user_id,5,$info['id']);
            $info['lock_info'] = getNftStatus($info['id'],5,$user_id);
            $info['chapter'] = [];
            $info['expression'] = $goods_prod;
            $info['role_info'] = [];
            // 我是否抽中了该角色
            $info['is_sale'] = isMyNft($gid,5,$user_id);
            // 是否可以出价
            $info['isPrice'] = isPrice($gid,5,$user_id);
            //获取拍卖id
            $info['sale_id'] = $sale->where(['obj_id' => $gid,'obj_type' => 5,'status' => 0])->order('id desc')->value('id');
            // 是否可以立即购买
            $info['is_buy'] = canBuyNft($gid,5,$user_id);
            $info['chain'] = getOtherDetail($gid,5);
            // 取消拍卖和降低价格
            $info['is_cancel'] = isCancel($gid,5,$user_id);
            // 拥有者
            $info['has_data'] = getNftMaster($gid,5);
            $info['is_market'] = isMarket($gid,5);//是否是市场
            // 拍卖倒计时
            $info['sale_time'] = saleLastTime($gid,5);

            $info['obj_type'] = 5; 
            $result['data'] = $info;
        }
        return $result;
    }

    // 角色列表
    public function getRoleList($page = 1,$limit = 10,$user_id = 0,$type = 0,$keyword = '',$role_type = '',$figure = '',$sort = -1)
    {
        $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [
                'list'  => [],
                'count' => 0,
                'page'  => $page,
                'limit' => $limit
            ]
        ];

        $productsModel = new Products();
        $cate_mod = new \app\common\model\ArticleType();
        $extend_mod = new \app\common\model\GoodsExtend();
        $draw_mod = new \app\common\model\DrawLog();

        //仅显示抽出来的角色
        $where = [];
        
        $ids = $draw_mod->column('goods_id');
        $where[] = ['id','in',$ids];

        if($type >= 0){
            $where[] = ['is_market','eq',$type];
        }
        
        if(!empty($keyword)){
            $where[] = ['name','like','%'.$keyword.'%'];
        }

        $role_ids_1 = [];
        $role_ids_2 = [];

        if(!empty($role_type)){
            $role_ids_1 = $extend_mod->whereIn('role_type',$role_type)->column('goods_id');
            // if(!empty($role_ids_1)){
            //     $where[] = ['id','IN',$role_ids_1];
            // }
        }else{
            $role_ids_1 = $extend_mod->column('goods_id');
        }

        if(!empty($figure)){
            $role_ids_2 = $extend_mod->whereIn('figure',$figure)->column('goods_id');
            // if(!empty($role_ids_2)){
            //     $where[] = ['id','IN',$role_ids_2];
            // }
        }else{
            $role_ids_2 = $extend_mod->column('goods_id');
        }

        $new_role_ids = array_intersect($role_ids_1,$role_ids_2);

        if(!empty($role_type) || !empty($figure)){
            if(!empty($new_role_ids)){
                $where[] = ['id','IN',$new_role_ids];
            }else{
                return $result;
            }
        }
        
        $order = commonSort(5,$sort);

        $field = 'id,bn,name,brief,image_id,intro,params,work,rarity,sex,name_eng,is_market,price,cate_id,likes';
        $list = $this->field($field)->where($where)->order($order)->page($page, $limit)->select();

        $result['data']['count'] = $this->where($where)->count();
        if ($list) {
            foreach ($list as $key => $info) {
                $list[$key]['price'] = empty($info['price']) ? 0 : $info['price'];
                if(getLng() == 'en'){
                    $list[$key]['name'] = $info['name_eng'];
                    $list[$key]['cate_name'] = $cate_mod->where('id',$info['cate_id'])->value('type_name_eng');
                }else{
                    $list[$key]['cate_name'] = $cate_mod->where('id',$info['cate_id'])->value('type_name');
                }
                

                // if (isset($info['image_id'])) {
                //     $image_url         = _sImage($info['image_id']);
                //     $list[$key]['image_url'] = $image_url;
                // }
                $image_url = $extend_mod->where('goods_id',$info['id'])->value('role_img');
                $list[$key]['image_url'] = _sImage($image_url);
                $list[$key]['head_img'] = _sImage($image_url).'?x-oss-process=image/crop,x_1900,y_1900,w_1200,h_1200/format,webp';

                $list[$key]['params'] = unserialize($info['params']);

                $product_list = $productsModel->field('specs_name,specs_type,image_id')->where(['goods_id' => $info['id']])->select();

                $new_products = [];
                foreach ($product_list as $ke => $value) {
                    // $product_list[$key]['image'] = _sImage($value['image_id']);
                    $new_products[$value['specs_type']][] = $value;
                }

                $last_products = [];

                foreach ($new_products as $k => $v) {
                    foreach ($v as $k1 => $v1) {
                        $last_products[$v1['specs_type']][] = [
                            'specs_name' => $v1['specs_name'],
                            'img' => _sImage($v1['image_id'])
                            
                        ];
                        unset($v1['specs_type']);
                    }
                }

                $list[$key]['attr'] = $last_products;

                // 角色道具
                $propMod = new \app\common\model\Prop();
                $goods_prod = $propMod->where('goods_id',$info['id'])->select();

                foreach ($goods_prod as $k2 => $v2) {
                    $goods_prod[$k2]['cover'] = _sImage($v2['cover']);
                    $goods_prod[$k2]['role_img'] = _sImage($v2['role_img']);
                }
                $list[$key]['expression'] = $goods_prod;

                //判断是否是已点赞
                if(!empty($user_id)){
                    $list[$key]['is_care'] = isUserCare($user_id,$info['id'],5);
                }else{
                    $list[$key]['is_care'] = false;
                } 

                $result['data']['list'] = $list;

            }
            
        }
        return $result;
    }

    // 获取角色相关作品
    public function getRoleAbout($role_id = 0,$type = 1,$is_market = -1,$id = 0)
    {
        $articleModel = new \app\common\model\Article();
        $musicModel = new \app\common\model\Files();
        $gameModel = new \app\common\model\Game();
        $imgModel = new \app\common\model\Pictures();
        $cateModel = new \app\common\model\ArticleType();

        if($is_market >= 0){
            $map['is_market'] = $is_market;
        }

        $map['role_id'] = $role_id;

        $list = [];

        if($type == 1){
            // 文章
            if(!empty($id)){
                $list[] = $articleModel->field('id,title as name,cover,type_id as cate_id,code,price,sort,is_market,title_eng as name_eng')->where($map)->where('id','neq',$id)->order('sort asc')->select();
            }else{
                $list[] = $articleModel->field('id,title as name,cover,type_id as cate_id,code,price,sort,is_market,title_eng as name_eng')->where($map)->order('sort asc')->select();
            }

            foreach ($list as $key => $value) {
                foreach ($value as $k => $v) {
                    $list[$key][$k]['type'] = 1;
                }
            }
            // if($list){
            //     foreach ($list as $key => $value) {
            //         $list[$key]['name'] = $value['title'];
            //         $list[$key]['cover'] = _sImage($value['cover']);
            //         $list[$key]['cate_name'] = $cateModel->where('id',$value['type_id'])->value('type_name');

            //         unset($list[$key]['title']);
            //         unset($list[$key]['type_id']);
            //     }
            // }

        }elseif($type == 2){
            //音频
            if(!empty($id)){
                $list[] = $musicModel->field('id,name,path as cover,cat_id as cate_id,code,price,sort,is_market,name_eng')->where($map)->where('id','neq',$id)->order('sort asc')->select();
            }else{
                $list[] = $musicModel->field('id,name,path as cover,cat_id as cate_id,code,price,sort,is_market,name_eng')->where($map)->order('sort asc')->select();
            }
            foreach ($list as $key => $value) {
                foreach ($value as $k => $v) {
                    $list[$key][$k]['type'] = 2;
                }
            }
            // dump($musicModel->getLastSql());die;
            // if($list){
            //     foreach ($list as $key => $value) {
            //         $list[$key]['cover'] = _sImage($value['path']);
            //         $list[$key]['cate_name'] = $cateModel->where('id',$value['cat_id'])->value('type_name');
                    
            //         unset($list[$key]['cat_id']);
            //         unset($list[$key]['path']);
            //     }
            // }

        }elseif($type == 3){
            // 游戏
            if(!empty($id)){
                $list[] = $gameModel->field('g_id as id,game_name as name,cover,cate_id,code,price,sort,is_market,game_name_eng as name_eng')->where($map)->where('g_id','neq',$id)->order('sort asc')->select();
            }else{
                $list[] = $gameModel->field('g_id as id,game_name as name,cover,cate_id,code,price,sort,is_market,game_name_eng as name_eng')->where($map)->order('sort asc')->select();
            }
            
            foreach ($list as $key => $value) {
                foreach ($value as $k => $v) {
                    $list[$key][$k]['type'] = 3;
                }
            }
            // if($list){
            //     foreach ($list as $key => $value) {
            //         $list[$key]['cover'] = _sImage($value['cover']);
            //         $list[$key]['cate_name'] = $cateModel->where('id',$value['cate_id'])->value('type_name');
                    
            //         unset($list[$key]['cate_id']);
            //     }
            // }

        }elseif($type == 4){
            // 插画
            if(!empty($id)){
                $list[] = $imgModel->field('id,name,cover,cate_id,code,price,sort,is_market,name_eng')->where($map)->where('id','neq',$id)->order('sort asc')->select();
            }else{
                $list[] = $imgModel->field('id,name,cover,cate_id,code,price,sort,is_market,name_eng')->where($map)->order('sort asc')->select();
            }
            foreach ($list as $key => $value) {
                foreach ($value as $k => $v) {
                    $list[$key][$k]['type'] = 4;
                }
            }
            // if($list){
            //     foreach ($list as $key => $value) {
            //         $list[$key]['cover'] = _sImage($value['cover']);
            //         $list[$key]['cate_name'] = $cateModel->where('id',$value['cate_id'])->value('type_name');
                    
            //         unset($list[$key]['cate_id']);
            //     }
            // }

        }else{
            $list_1 = $articleModel->field('id,title as name,cover,type_id as cate_id,code,price,sort,is_market,title_eng as name_eng')->where($map)->order('sort asc')->select();
            foreach ($list_1 as $key => $value) {
                // foreach ($value as $k => $v) {
                    $list_1[$key]['type'] = 1;
                // }
            }
            $list_temp[] = $list_1;
            // dump($list);die;
            $list_2 = $musicModel->field('id,name,path as cover,cat_id as cate_id,code,price,sort,is_market,name_eng')->where($map)->order('sort asc')->select();
            foreach ($list_2 as $key => $value) {
                // foreach ($value as $k => $v) {
                    $list_2[$key]['type'] = 2;
                // }
            }

            $list_temp[] = $list_2;

            $list_3 = $gameModel->field('g_id as id,game_name as name,cover,cate_id,code,price,sort,is_market,game_name_eng as name_eng')->where($map)->order('sort asc')->select();
            foreach ($list_3 as $key => $value) {
                // foreach ($value as $k => $v) {
                    $list_3[$key]['type'] = 3;
                // }
            }
            $list_temp[] = $list_3;
            $list_4 = $imgModel->field('id,name,cover,cate_id,code,price,sort,is_market,name_eng')->where($map)->order('sort asc')->select();
            foreach ($list_4 as $key => $value) {
                // foreach ($value as $k => $v) {
                    $list_4[$key]['type'] = 4;
                // }
            }
            
            $list_temp[] = $list_4;

            $list = [];
            foreach ($list_temp as $key => $value) {
                if(!empty($value[0])){
                    // dump($value);
                    $list[] = $value;
                }
            }

            
            // $list[] = $new_list;
            // $list = array_values($list);
        }
        // dump(array_values($list));die;
        $new_arr = [];

        if($type == 5){
            $i = 0;
            foreach ($list as $key => $item) {
                
                // dump($i);
                // if(!empty($item)){

                    foreach ($item as $ik => $iv) {
                        if(getLng() == 'en'){
                            $new_arr[$i]['name'] = $iv['name_eng'];
                            $new_arr[$i]['cate_name'] = $cateModel->where('id',$iv['cate_id'])->value('type_name_eng');
                        }else{
                            $new_arr[$i]['name'] = $iv['name'];
                            $new_arr[$i]['cate_name'] = $cateModel->where('id',$iv['cate_id'])->value('type_name');
                        }
                        // foreach ($item as $ke => $iv) {
                            $new_arr[$i]['id'] = $iv['id'];
                            
                            $new_arr[$i]['cate_id'] = $iv['cate_id'];
                            $new_arr[$i]['code'] = $iv['code'];
                            $new_arr[$i]['price'] = $iv['price'];
                            $new_arr[$i]['type'] = $iv['type'];
                            $new_arr[$i]['is_market'] = $iv['is_market'];
                            $new_arr[$i]['cover'] = _sImage($iv['cover']);
                            
                             $i++;
                        }
                        

                    // }
                // }    
               
            }
        }else{
            foreach ($list as $key => $item) {
                
                if(!empty($item)){

                    // foreach ($item as $ik => $v) {
                        foreach ($item as $ke => $iv) {
                            if(getLng() == 'en'){
                                $new_arr[$ke]['name'] = $iv['name_eng'];
                                $new_arr[$ke]['cate_name'] = $cateModel->where('id',$iv['cate_id'])->value('type_name_eng');
                            }else{
                                $new_arr[$ke]['name'] = $iv['name'];
                                $new_arr[$ke]['cate_name'] = $cateModel->where('id',$iv['cate_id'])->value('type_name');
                            }
                            $new_arr[$ke]['id'] = $iv['id'];
                            
                            $new_arr[$ke]['cate_id'] = $iv['cate_id'];
                            $new_arr[$ke]['code'] = $iv['code'];
                            $new_arr[$ke]['price'] = $iv['price'];
                            $new_arr[$ke]['type'] = $iv['type'];
                            $new_arr[$ke]['is_market'] = $iv['is_market'];
                            $new_arr[$ke]['cover'] = _sImage($iv['cover']);
                            
                        }
                        

                    // }
                }
            }
        }
        
            

        return  ['status' => true,'msg' => '成功','data' => $new_arr];
    }
}
