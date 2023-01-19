<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\GoodsBrowsing;
use app\common\model\GoodsCat;
use app\common\model\GoodsComment;
use app\common\model\GoodsExtendCat;
use think\Db;
use think\facade\Cache;
use think\facade\Request;
use app\common\model\Goods as GoodsModel;
use app\common\model\Products;
use app\common\model\Brand;

/***
 * 商品相关接口
 * Class Goods
 * @package app\api\controller
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-23 19:45
 */
class Goods extends Api
{
    //商品允许出现字段，允许出现的字段跟查询的字段不太一样，允许查询的只能不能有：album、isfav、product、image_url
    private $goodsAllowedFields = [
        'id', 'bn', 'name', 'brief', 'price', 'mktprice', 'image_id', 'video_id', 'goods_cat_id', 'goods_type_id', 'brand_id', 'label_ids', 'is_nomal_virtual', 'marketable', 'stock', 'weight', 'unit', 'intro', 'spes_desc', 'comments_count', 'view_count', 'buy_count', 'uptime', 'downtime', 'sort', 'is_hot', 'is_recommend', 'ctime', 'utime', 'params'
    ];
    //货品允许字段
    private $productAllowedFields = [
        'id', 'goods_id', 'barcode', 'sn', 'price', 'mktprice', 'marketable', 'stock', 'spes_desc', 'is_defalut'
    ];

    private function allowedField($data, $type = 'goods')
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10028, true),
            'data'   => []
        ];

        if ($data == '' && $data != '*') {
            // $return_data['msg'] = error_code(10029,true);
            return error_code(10029);
        }
        if ($data != '*') {
            $tmpData = explode(',', $data);
            foreach ($tmpData as $key => $value) {
                if ($type == 'goods') {
                    if (!in_array($value, $this->goodsAllowedFields)) {
                        $return_data['msg'] .= ':' . $value;
                        return $return_data;
                    }
                } elseif ($type == 'product') {
                    if (!in_array($value, $this->productAllowedFields)) {
                        $return_data['msg'] .= ':' . $value;
                        return $return_data;
                    }
                }
            }
        }
        $return_data['status'] = true;
        $return_data['msg']    = '字段校检通过';
        return $return_data;
    }

    /**
     * 检查排序字段
     * @param $order
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-29 16:42
     */
    private function allowedOrder($order)
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10031, true),
            'data'   => []
        ];
        //        if(is_array($order)) {
        //            $return_data['msg'] = '排序字段不能为数组';
        //            return $return_data;
        //        }
        //        if(strpos($order,',') !== false) {
        //            $tmp_order = explode(',',$order);
        //            foreach($tmp_order as $k => $v) {
        //                $field = explode(' ',$v);
        //                if(count($field)<2) {
        //                    $return_data['msg'] = '排序缺失条件或字段';
        //                    return $return_data;
        //                }
        //                if(!in_array($field,$this->goodsAllowedFields)) {
        //                    $return_data['msg'] = '字段：' . $field[0] . '不在可排序字段内';
        //                    return $return_data;
        //                }
        //            }
        //
        //        }else {
        //            $field = explode(' ',$order);
        //
        //            if(count($field)<2) {
        //                $return_data['msg'] = '排序缺失条件或字段';
        //                return $return_data;
        //            }
        //            if(!in_array($field[0],$this->goodsAllowedFields)) {
        //                $return_data['msg'] = '字段：' . $field[0] . '不在可排序字段内';
        //                return $return_data;
        //            }
        //        }
        $return_data['status'] = true;
        $return_data['msg']    = '排序校检通过';
        return $return_data;
    }

    /**
     * 获取商品列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:46
     */
    public function getList()
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10027, true),
            'data'   => []
        ];
        $field       = input('field', '*');
        $page        = input('page/d', 1);
        $limit       = input('limit/d');
        $order       = input('order', 'sort asc,id desc');
        $filter      = []; //过滤条件
        $class_name['data']  = '';
        $where  = [];
        $whereRaw = ' 1=1 '; //扩展sql
        if (input('?param.where','','safe_filter')) {
            $postWhere = json_decode(input('param.where','','safe_filter'), true);

            //套餐商品
            if(isset($postWhere['is_combo']) && $postWhere['is_combo']){
                $where[] = ['g.is_combo', 'eq', $postWhere['is_combo']];
            }

            //判断商品搜索,
            if (isset($postWhere['search_name']) && $postWhere['search_name']) {
                $where[] = ['g.name|g.bn|g.brief', 'LIKE', '%' . $postWhere['search_name'] . '%'];
            }
            if (isset($postWhere['bn']) && $postWhere['bn']) {
                $where[] = ['g.bn', '=', $postWhere['bn']];
            }
            //商品分类,同时取所有子分类 todo 无限极分类时要注意
            if (isset($postWhere['cat_id']) && $postWhere['cat_id']) {
                $goodsCatModel = new GoodsCat();
                $cat_ids        = [];
                $childCats     = $goodsCatModel->getCatByParentId($postWhere['cat_id']);
                if (!$childCats->isEmpty()) {
                    $filter['child_cats'] = $childCats;
                }
                $cat_ids   = array_column($childCats->toArray(), 'id');
                $cat_ids[] = $postWhere['cat_id'];
                //$where[]  = ['g.goods_cat_id', 'in', $catIds];
                $class_name = $goodsCatModel->getNameById($postWhere['cat_id']);

                $goodsExtendCat = new GoodsExtendCat();
                $goods_ids = $goodsExtendCat->getGoodsIdByCat($cat_ids, true);
                if ($goods_ids) {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') or g.id in (' . implode(',', $goods_ids) . ') ) ';
                } else {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') ) ';
                }
            }
            //价格区间
            if (isset($postWhere['price_f']) && $postWhere['price_f']) {
                $where[] = ['g.price', '>=', $postWhere['price_f']];
            }
            if (isset($postWhere['price_t']) && $postWhere['price_t']) {
                $where[] = ['g.price', '<', $postWhere['price_t']];
            }
            if (isset($postWhere['recommend'])) {
                $where[] = ['g.is_recommend', 'eq', '1'];
            }
            if (isset($postWhere['hot'])) {
                $where[] = ['g.is_hot', 'eq', '1'];
            }
            //品牌筛选
            if (isset($postWhere['brand_id']) && $postWhere['brand_id']) {
                $where[] = ['g.brand_id', 'in', $postWhere['brand_id']];
            }
            //标签筛选
            if (isset($postWhere['label_id']) && $postWhere['label_id']) {
                $where[] = ['', 'exp', Db::raw('FIND_IN_SET(' . remove_xss($postWhere['label_id']) . ',g.label_ids)')];
            }
        }

        $goodsModel = new GoodsModel();
        $where[]    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];

        $return_data = $this->allowedField($field);
        if (!$return_data['status']) {
            return $return_data;
        }
        $return_data = $this->allowedOrder($order);
        if (!$return_data['status']) {
            return $return_data;
        }


        $page_limit = config('jshop.page_limit');
        $limit      = $limit ? $limit : $page_limit;

        $returnGoods = $goodsModel->getList($field, $where, $order, $page, $limit, $whereRaw);
        if ($returnGoods['status']) {
            $return_data['msg']                = '查询成功';
            $return_data['data']['list']       = $returnGoods['data'];
            $return_data['data']['total_page'] = $returnGoods['total'];
            $return_data['data']['filter']      = isset($returnGoods['filter']) ? array_merge($returnGoods['filter'], $filter) : [];
        }
        $return_data['data']['page']  = $page;
        $return_data['data']['limit'] = $limit;
        $return_data['data']['where'] = $postWhere;
        $return_data['data']['order'] = $order;
        $return_data['data']['class_name'] = $class_name['data'] ? $class_name['data'] : '';
        return $return_data;
    }

    /**
     * 获取商品明细
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-23 19:47
     */
    public function getDetial()
    {
        //$return_data = [
        //  'status' => false,
        //  'msg'    => error_code(10027, true),
        //  'data'   => []
        // ];
        $goods_id    = input('id/d', 0); //商品ID
        $token       = input('token', ''); //token值 会员登录后传
        if (!$goods_id) {
            return error_code(10027);
        }
        $field       = input('field', '*');
        $return_data = $this->allowedField($field);
        $goodsModel  = new GoodsModel();
        $returnGoods = $goodsModel->getGoodsDetial($goods_id, $field, $token);

        // if ($returnGoods['status']) {
        //     // $return_data['msg']  = '';
        //     // $return_data['data'] = $returnGoods['data'];
        //     return $returnGoods;
        // } else {
        //     $return_data['msg']    = $returnGoods['msg'];
        //     $return_data['status'] = false;
        // }
        return $returnGoods;
    }

    //app版的获取商品明细接口，因为多规格的传值问题，导致java解析不了多规格数据，在此做了转化
    public function appGetDetail()
    {
        $re = $this->getDetial();
        if ($re['data']['product']['default_spes_desc']) {
            $arr                                        = $re['data']['product']['default_spes_desc'];
            $re['data']['product']['default_spes_desc'] = [];
            foreach ($arr as $k => $v) {
                $n_v = [];
                foreach ($v as $vv) {
                    if (!isset($vv['is_default'])) {
                        $vv['is_default'] = false;
                    }
                    if (!isset($vv['product_id'])) {
                        $vv['product_id'] = 0;
                    }
                    $n_v[] = $vv;
                }
                $re['data']['product']['default_spes_desc'][] = [
                    'items'  => $k,
                    'fenlei' => $n_v
                ];
            }
        } else {
            $re['data']['product']['default_spes_desc'] = [];
        }
        return $re;
    }


    /**
     * 根据sku获取相关价格，库存等信息
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 10:09
     */
    public function getSkuDetial()
    {
        $return_data = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $spec_value  = input('spec', '');
        $goods_id    = input('id/d', 0); //商品ID
        $token       = input('token', ''); //token值 会员登录后传

        if (!$goods_id || !$spec_value) {
            return error_code(12701);
        }

        $goodsModel  = new GoodsModel();
        $returnGoods = $goodsModel->getGoodsDetial($goods_id, 'id,bn,name,image_id,goods_cat_id,goods_type_id,brand_id,spes_desc', $token);
        if ($returnGoods['status']) {
            $goods = $returnGoods['data'];
            if ($goods['products']) {
                $products = $goods['products'];
                foreach ($products as $key => $val) {
                    if ($val['spes_desc'] == $spec_value) {
                        //获取价格
                        $val['price']          = $goodsModel->getPrice($val);
                        $val['stock']          = $goodsModel->getStock($val);
                        $return_data['data']   = $val;
                        $return_data['msg']    = '获取成功';
                        $return_data['status'] = true;
                    }
                }
            }
        }
        return $return_data;
    }

    /**
     * 获取参数接口
     * @return array
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-02-02 11:18
     */
    public function getGoodsParams()
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10033, true),
            'data'   => []
        ];
        $goods_id    = input('id/d', 0); //商品ID
        $goodsModel  = new GoodsModel();
        $brandModel  = new Brand();
        $returnGoods = $goodsModel->getOne($goods_id, 'id,bn,name,brand_id,image_id,params,spes_desc');

        if ($returnGoods['status']) {
            $params = [];
            $data   = $returnGoods['data'];
            if ($data['params']) {
                $goodsParams = unserialize($data['params']);
                $goodsParams = array_filter($goodsParams);
                if ($goodsParams) {
                    foreach ($goodsParams as $key => $val) {
                        if (is_array($val)) {
                            $val      = implode('、', $val);
                            $params[] = [
                                'name'  => $key,
                                'value' => $val ? $val : ''
                            ];
                        } else {
                            $params[] = [
                                'name'  => $key,
                                'value' => $val ? $val : ''
                            ];
                        }
                    }
                }
            }
            $return_data['data']   = $params;
            $return_data['status'] = true;
            $return_data['msg']    = '查询成功';
        }
        return $return_data;
    }

    /**
     * 获取该货品相关信息
     * @param int $user_id
     * @return array
     */
    public function getProductInfo()
    {
        // $return_data = error_code(10033);
        $product_id  = input('id/d', 0); //货品ID
        $token       = input('token', ''); //token值 会员登录后传
        $type       = input('type', 'goods'); //商品类型,默认是商品
        if (!$product_id) {
            // $return_data['msg'] = error_code(14011, true);
            return error_code(14011);
        }

        $productsModel      = new Products();
        $user_id            = getUserIdByToken($token); //获取user_id
        $product            = $productsModel->getProductInfo($product_id, true, $user_id, $type);
        // $return_data['msg'] = $product['msg'];
        // if (!$product['status']) {
        //     return $return_data;
        // }
        // $return_data['data']   = $product['data'];
        // $return_data['status'] = true;
        return $product;
    }

    //app版的获取商品明细接口，因为多规格的传值问题，导致java解析不了多规格数据，在此做了转化
    public function appGetProductInfo()
    {
        $re = $this->getProductInfo();
        if ($re['data']['default_spes_desc']) {
            $arr                             = $re['data']['default_spes_desc'];
            $re['data']['default_spes_desc'] = [];
            foreach ($arr as $k => $v) {
                $n_v = [];
                foreach ($v as $vv) {
                    if (!isset($vv['is_default'])) {
                        $vv['is_default'] = false;
                    }
                    if (!isset($vv['product_id'])) {
                        $vv['product_id'] = 0;
                    }
                    $n_v[] = $vv;
                }
                $re['data']['default_spes_desc'][] = [
                    'items'  => $k,
                    'fenlei' => $n_v
                ];
            }
        } else {
            $re['data']['default_spes_desc'] = [];
        }
        return $re;
    }


    /**
     * 获取商品评价
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsComment()
    {
        $goods_id = input('goods_id');
        $page     = input('page', 1);
        $limit    = input('limit', 10);
        $order    = input('order');
        if (empty($goods_id)) {
            return error_code(13403);
        }
        $model = new GoodsComment();
        $res   = $model->getList($goods_id, $page, $limit, 1, $order);
        return $res;
    }


    /**
     * 获取某个分类的热卖商品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsCatHotGoods()
    {
        $cat_id = Request::param('cat_id');
        $limit  = Request::param('limit', 6);
        $model  = new GoodsModel();
        $res    = $model->getGoodsCatHotGoods($cat_id, $limit);
        return $res;
    }

    /**
     * 获取推荐商品
     */
    public function getPickGoods()
    {
        $return_data = [
            'status' => false,
            'msg'    => error_code(10027, true),
            'data'   => []
        ];
        $field       = input('field', 'id,bn,name,brief,price,mktprice,image_id,goods_cat_id,goods_type_id,brand_id,stock,unit,spes_desc,view_count,buy_count,label_ids');
        $page        = input('page/d', 1);
        $limit       = input('limit/d', 10);
        $order       = input('order', 'buy_count desc');
        $token       = input('token/s', '');
        $user_id     = getUserIdByToken($token); //获取user_id
        $lastLimit = 0;
        if ($user_id != 0) {
            //取浏览记录
            $goodsBrowsing = new GoodsBrowsing();
            $browsing      = $goodsBrowsing->getList($user_id, 1, $limit);
            if ($browsing['status'] && $browsing['data']['count'] > 0) {
                if (count($browsing['data']['list']) < $limit) {
                    $lastLimit = $limit - count($browsing['data']['list']);
                }
                $goodsIds = array_column($browsing['data']['list'], 'goods_id');
                $where[]  = ['g.id', 'in', $goodsIds];
            }
        }
        $filter      = []; //过滤条件
        $class_name['data']  = '';
        $whereRaw = '1 = 1';
        if (input('?param.where','','safe_filter')) {
            $postWhere = json_decode(input('param.where','','safe_filter'), true);
            //判断商品搜索,
            if (isset($postWhere['search_name']) && $postWhere['search_name']) {
                $where[] = ['g.name|g.bn|g.brief', 'LIKE', '%' . $postWhere['search_name'] . '%'];
            }
            if (isset($postWhere['bn']) && $postWhere['bn']) {
                $where[] = ['g.bn', '=', $postWhere['bn']];
            }
            //商品分类,同时取所有子分类 todo 无限极分类时要注意
            if (isset($postWhere['cat_id']) && $postWhere['cat_id']) {
                $goodsCatModel = new GoodsCat();
                $cat_ids        = [];
                $childCats     = $goodsCatModel->getCatByParentId($postWhere['cat_id']);
                if (!$childCats->isEmpty()) {
                    $filter['child_cats'] = $childCats;
                }
                $cat_ids   = array_column($childCats->toArray(), 'id');
                $cat_ids[] = $postWhere['cat_id'];
                //$where[]  = ['g.goods_cat_id', 'in', $catIds];
                $class_name = $goodsCatModel->getNameById($postWhere['cat_id']);

                $goodsExtendCat = new GoodsExtendCat();
                $goods_ids = $goodsExtendCat->getGoodsIdByCat($cat_ids, true);
                if ($goods_ids) {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') or g.id in (' . implode(',', $goods_ids) . ') ) ';
                } else {
                    $whereRaw .= ' and (g.goods_cat_id  in (' . implode(',', $cat_ids) . ') ) ';
                }
            }
            //价格区间
            if (isset($postWhere['price_f']) && $postWhere['price_f']) {
                $where[] = ['g.price', '>=', $postWhere['price_f']];
            }
            if (isset($postWhere['price_t']) && $postWhere['price_t']) {
                $where[] = ['g.price', '<', $postWhere['price_t']];
            }
            if (isset($postWhere['recommend'])) {
                $where[] = ['g.is_recommend', 'eq', '1'];
            }
            if (isset($postWhere['hot'])) {
                $where[] = ['g.is_hot', 'eq', '1'];
            }
            //品牌筛选
            if (isset($postWhere['brand_id']) && $postWhere['brand_id']) {
                $where[] = ['g.brand_id', 'in', $postWhere['brand_id']];
            }
            //标签筛选
            if (isset($postWhere['label_id']) && $postWhere['label_id']) {
                $where[] = ['', 'exp', Db::raw('FIND_IN_SET(' . remove_xss($postWhere['label_id']) . ',g.label_ids)')];
            }
        }

        $goodsModel = new GoodsModel();
        $where[]    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];

        $return_data = $this->allowedField($field);
        if (!$return_data['status']) {
            return $return_data;
        }
        $return_data = $this->allowedOrder($order);
        if (!$return_data['status']) {
            return $return_data;
        }

        $page_limit = config('jshop.page_limit');
        $limit      = $limit ? $limit : $page_limit;
        $returnGoods = $goodsModel->getList($field, $where, $order, $page, $limit, $whereRaw);
        if ($lastLimit > 0) {
            $where[]    = ['g.marketable', 'eq', $goodsModel::MARKETABLE_UP];
            $otherGoods = $goodsModel->getList($field, $where, $order, $page, $lastLimit, $whereRaw);
            $returnGoods['data'] = array_merge($returnGoods['data'], $otherGoods['data']);
        }
        if ($returnGoods['status']) {
            $return_data['msg']                = '查询成功';
            $return_data['data']['list']       = $returnGoods['data'];
            $return_data['data']['total_page'] = $returnGoods['total'];
            $return_data['data']['filter']      = isset($returnGoods['filter']) ? array_merge($returnGoods['filter'], $filter) : [];
        }
        $return_data['data']['page']  = $page;
        $return_data['data']['limit'] = $limit;
        $return_data['data']['where'] = $postWhere;
        $return_data['data']['order'] = $order;
        $return_data['data']['class_name'] = $class_name['data'] ? $class_name['data'] : '';
        return $return_data;
    }


    /*
     * 全部商品
     * */
    public function goodsall()
    {
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $order      = input('order', 10);
        $goodsModel = new GoodsModel();
        return $goodsModel->goods_all($page, $limit, $order);
    }


    /*
     * 上新商品
     * */
    public function newgoods()
    {
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $order      = input('order', 10);
        $goodsModel = new GoodsModel();
        return $goodsModel->newgoods($page, $limit, $order);
    }


    /*
     * 促销数据
     * */
    public function promotiongoods()
    {
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $order      = input('order', 10);
        $goodsModel = new GoodsModel();
        return $goodsModel->promotiongoods($page, $limit, $order);
    }

    /**
     * 获取销量排名前十的商品
     */
    public function salesRanking()
    {
        $goodsModel = new GoodsModel();
        return $goodsModel->salesRanking();
    }

    // 获取角色详情
    public function findGoodsDetail()
    {
        $goodsModel = new GoodsModel();
        $goods_id    = input('id/d', 0); //商品ID
        $user_id  = getUserIdByToken(input('token'));

        if (!$goods_id) {
            return error_code(10027);
        }

        return $goodsModel->getRoleDetialNew($goods_id,$user_id);
    }

    // 角色列表
    public function getRoleList()
    {
        $goodsModel = new GoodsModel();
        $page       = input('page', 1);
        $limit      = input('limit', 10);
        $type      = input('type', -1);//是否是市场:0否1是,-1全部
        $keyword = input('keyword','');//关键词
        $role_type = input('role_type','');//角色类型 空全部,女标，男标，老头，哥布林,胖妞
        $figure = input('figure','');//头身 空全部,5头身，6头身，7头身，8头身，9头身
        $sort = input('sort', -1);//-1默认1.最近上市2最近创建3.价格:从低到高4.价格:从高到底5.最久远的6.最近出售7.拍卖即将结束8.最高成交价
        
        if(input('?token') && !empty(input('token'))){
            $user_id  = getUserIdByToken(input('token'));
        }else{
            $user_id = 0;
        }

        return $goodsModel->getRoleList($page,$limit,$user_id,$type,$keyword,$role_type,$figure,$sort);
    }

    // 获取抽卡配置
    public function getDrawCardSet()
    {
        $cardModel = new \app\common\model\DrawCard();

        return $cardModel->getConfig();
    }

    // 用户抽卡
    public function drawCard()
    {
        $cardModel = new \app\common\model\DrawCard();

        $user_id  = getUserIdByToken(input('token'));
        if(empty($user_id)){
            return error_code(14006);
        }

        return $cardModel->userDraw($user_id);
    }

    // 获取同角色下的其他作品
    public function getRoleOther()
    {
        $goodsModel = new GoodsModel();
        // $page       = input('page', 1);
        // $limit      = input('limit', 10);
        $role_id = input('role_id',0);
        $id = input('id',0);
        $type = input('type',1);//推荐类型:1文章2音频3游戏4插画5角色
        $is_market = input('is_market',-1);//是否投放市场0否1是-1全部

        if(empty($role_id)){
            return ['status' => true,'msg' => '','data' => []];
        }

        return $goodsModel->getRoleAbout($role_id,$type,$is_market,$id);
    }

    // 角色详情html代码
    public function cutRole()
    {
        $id = input('id');
        $cutimg_name = 'cut_'.$id.'_img.webp';
        $host = 'http://'.$_SERVER['HTTP_HOST'];

        if(file_exists($cutimg_name)){
            return ['status' => false,'msg' => ''];
        }

        // $id = 62;
        $goodsModel = new GoodsModel();
        $user_id  = getUserIdByToken(input('token'));
        $goods_info = $goodsModel->getRoleDetialNew($id,$user_id);
        $html = '';

        if($goods_info['data']){
            $role_name = $goods_info['data']['name'];//角色名称
            // 性别
            if($goods_info['data']['sex'] == 1){
                // 男
                $sex_img = $host.'/source/img3/1.png';
            }elseif($goods_info['data']['sex'] == 2){
                // 女
                $sex_img = $host.'/source/img1/45.png';
            }else{
                // 无性
                $sex_img = $host.'/source/img3/2.png';
            }

            $role_name_eng = $goods_info['data']['name_eng'];//角色英文名称
            $role_work = $goods_info['data']['work'];//职业
            $role_work_img = $goods_info['data']['extend']['work_img'];//职业图标
            $after_work = $goods_info['data']['extend']['work_life'];//生前职业
            $img = _sImage($goods_info['data']['extend']['role_img']);//立绘

            if(!file_exists(basename($img))){
                if($goods_info['data']['extend']['role_type'] == '哥布林'){
                    @file_put_contents(basename($img), @file_get_contents($img.'?x-oss-process=image/crop,x_430,y_1420,w_4160,h_3600/format,webp'));
                }else{
                    @file_put_contents(basename($img), @file_get_contents($img.'?x-oss-process=image/crop,x_200,y_800,w_4600,h_6000/resize,h_1440,m_lfit/format,webp'));
                }
                
            }

            $temp_img_cover = 'http://'.$_SERVER['HTTP_HOST'].'/'.basename($img);
            

            $evil_data = $goods_info['data']['extend']['character_info_evil'];//邪恶
            $friendly_data = $goods_info['data']['extend']['character_info_friendly'];//善良
            $talentList = $goods_info['data']['extend']['talent_info'];//天赋
            $skillList = $goods_info['data']['extend']['potential_info'];//技能
            $fameList = $goods_info['data']['extend']['role_title_info'];//称号
            $history = $goods_info['data']['extend']['history'];//历程
            $part = $goods_info['data']['part'];//组件
            $position = $goods_info['data']['extend']['position_info'];//立场
            // dump($goods_info['data']);die;

            // 处理哥布林样式
            $role_calss = 'prize1';
            if($goods_info['data']['extend']['role_type'] == '哥布林'){
                $role_calss = 'prize2';
            }elseif($goods_info['data']['extend']['role_type'] == '男标'){
                $role_calss = 'prize3';
            }

            foreach ($talentList as $tk => $tv) {
                // dump(basename($tv['img']));die;
                if(!file_exists(basename($tv['img']))){
                    @file_put_contents(basename($tv['img']), @file_get_contents($tv['img']));
                }

                $talentList[$tk]['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.basename($tv['img']);
            }  

            foreach ($skillList as $sk => $sv) {
                if(!file_exists(basename($sv['img']))){
                    @file_put_contents(basename($sv['img']), @file_get_contents($sv['img']));
                }

                $skillList[$sk]['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.basename($sv['img']);
            }

            $html = '<div style="width:2560px;overflow: hidden;position: relative;">
                        <div class="title">角色情报<div class="wire"></div>
                        </div>
                        <div class="information">
                            <div class="content">
                                <div class="top u-f u-f-jsb">
                                    <div class="figure">
                                        <div class="u-f-item">
                                            <p class="figureName">'.$role_name.'</p>
                                            <div style="width:22px;height:30px;margin-left:9px"><img class="img"
                                                    src="'.$sex_img.'" alt=""></div>
                                        </div>
                                        <p class=figureNameEng>'.$role_name_eng.'</p>
                                    </div>
                                    <div class=topRight><img class="img" src="https://ec.wexiang.vip/source/img2/25.png" alt="">
                                        <img class="iconImg" src="'.$role_work_img.'" alt="">
                                        <div class=topRightContent>
                                            <p class=topRightContentName1>'.$role_work.'</p>
                                            <p class=topRightContentName2>'.$after_work.'</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="cength u-f u-f-jsb">
                                    <div class="character">
                                        <div class="flower ">
                                            <img src="'.$position['position_img'].'" alt="">
                                        </div>
                                        <div>
                                            <!-- 头 -->
                                            <p class="text" style="top: -25px;left: 90px;">'.$position['position'].'</p>
                                            <!-- 左边 -->
                                            <p class="text font16" style="top:170px;left: 20px;">'.$evil_data[0]['name'].'
                                            </p>
                                            <p class="text font16" style="top:242px;left: 35px;">'.$evil_data[1]['name'].'
                                            </p>
                                            <p class="text font16" style="top:310px;left: 7px;">'.$evil_data[2]['name'].'
                                            </p>
                                            <p class="text font16" style="top:385px;left: 10px;">'.$evil_data[3]['name'].'
                                            </p>
                                            <!-- 右边 -->
                                            <p class="text font16" style="top:205px;right: -10px;">
                                                '.$friendly_data[0]['name'].'
                                            </p>
                                            <p class="text font16" style="top:280px;right: -10px;">
                                                '.$friendly_data[1]['name'].'
                                            </p>
                                            <p class="text font16" style="top:350px;right: 10px;">
                                                '.$friendly_data[2]['name'].'
                                            </p>
                                            <p class="text font16" style="top:420px;right: 20px;">
                                                '.$friendly_data[3]['name'].'
                                            </p>
                                            <!-- 尾 -->
                                            <p class="peculiarity" style="bottom: -15px;left: 85px;">特性</p>
                                        </div>
                                    </div>
                                    <div class=talentSkill>
                                        <div class=u-f>
                                            <div class=talent><img class=img src=https://ec.wexiang.vip/source/img1/52.png alt="">
                                                <p>天赋</p>
                                            </div>
                                            <div class="talentList u-f">';

                                            foreach ($talentList as $key => $value) {
                                                $html .= '<div class=talentItem><img class=img src='.$value['img'].' alt="">
                                                        <p class=talentItemName>'.$value['title'].'</p>
                                                    </div>';
                                            }
                                                
                                            $html .= '</div>
                                        </div>
                                        <div class=u-f style=margin-top:30px>
                                            <div class=talent><img class=img src=https://ec.wexiang.vip/source/img1/56.png alt="">
                                                <p>天赋</p>
                                            </div>
                                            <div class="talentList u-f">';

                                            foreach ($skillList as $ke => $va) {
                                                $html .= '<div class=talentItem><img class=img src='.$va['img'].' alt="">
                                                        <p class=talentItemName>'.$va['title'].'</p>
                                                    </div>';
                                            }
                        
                                            $html .='</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bottom u-f-jsb">
                                    <div class=u-f>
                                        <div class=designation><img class="bjcImg img" src=https://ec.wexiang.vip/source/img1/48.png alt="">
                                            <div class="u-f po u-f-item">
                                                <div style=width:17px;height:19px><img class=img src=https://ec.wexiang.vip/source/img1/49.png
                                                        alt=""></div>
                                                <div class=designationNmae>称号</div>
                                            </div>
                                            <div class=designationContent>';

                                                foreach ($fameList as $k => $v) {
                                                    if($k > 0){
                                                        $class = 'designationContentItemImg';
                                                    }else{
                                                        $class = 'designationContentImg u-f-justify';
                                                    }
                                                    $html .='<div class="'.$class.'"><img class=img
                                                            src=https://ec.wexiang.vip/source/img1/50.png alt="">
                                                        <p style="text-align:center">'.$v['title'].'</p>
                                                    </div>';
                                                }
                                                
                                            $html .='</div>
                                        </div>
                                        <div class=course><img class="bjcImg img" src=https://ec.wexiang.vip/source/img1/57.png alt="">
                                            <div class="u-f po u-f-item">
                                                <div style=width:17px;height:19px><img class=img src=https://ec.wexiang.vip/source/img1/49.png
                                                        alt=""></div>
                                                <div class=designationNmae>历程</div>
                                            </div>
                                            <div class=courseText>'.$history.'</div>
                                        </div>
                                    </div>
                                    <div class=probability>
                                        <ul class=probabilityList>';

                                            foreach ($part as $p => $pv) {
                                                $html .='<li class="u-f-jsb probabilityListItem">
                                                    <div class=u-f-item>
                                                        <p class=character1>◆</p>
                                                        <p class=probabilityListItemName>'.$pv['name'].'</p>
                                                    </div>
                                                    <p class=probabilityListItemNub>'.$pv['number'].'%</p>
                                                </li>';
                                            }
                                            

                                        $html .='</ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <img class="'.$role_calss.'"
                            src="'.$temp_img_cover.'"
                            />
                         <img style="position: absolute;top: 0;width: 2560px;height: 1440px;" src="https://ec.wexiang.vip/backimg2k.webp" alt="">
                    </div>

                    <style>
                        .title {
                            font-size: 60px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            padding-bottom: 40px;
                            text-shadow: .2rem 0 .5rem #6865EE, -.2rem 0 .5rem #6865EE, 0 .2rem .5rem #6865EE, 0 -.2rem .5rem #6865EE;
                            z-index: 9;
                            position: absolute;
                            width: 2000px;
                            top: 89px;
                            left: 114px
                        }

                        .wire {
                            height: 2px;
                            background-image: linear-gradient(to right, #6865EE, #1B175A);
                            position: relative;
                            top: 40px
                        }

                        .u-f,
                        .u-f-item,
                        .u-f-justify,
                        .u-f-right,
                        .u-f-bottom,
                        .u-f-end,
                        .u-f-jsb {
                            display: flex
                        }

                        .u-f-bottom {
                            align-items: flex-end
                        }

                        .u-f-item,
                        .u-f-justify,
                        .u-f-right {
                            align-items: center
                        }

                        .u-f-justify {
                            justify-content: center
                        }

                        .u-f-right {
                            justify-content: flex-end
                        }

                        .u-f-around {
                            justify-content: space-around
                        }

                        .u-f-jsb {
                            justify-content: space-between
                        }

                        .u-f-end {
                            justify-content: flex-end
                        }

                        .u-f-column {
                            flex-direction: column
                        }

                        .u-f-wrap {
                            flex-wrap: wrap
                        }

                        .img {
                            width: 100%;
                            height: 100%
                        }

                        .image {
                            width: 100%;
                            height: 100%;
                            border-radius: 50%
                        }

                        .cursor {
                            cursor: pointer
                        }

                        .singleLine {
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap
                        }

                        .multipleLines {
                            overflow: hidden;
                            text-overflow: ellipsis;
                            display: -webkit-box;
                            -webkit-box-orient: vertical
                        }

                        .mt10 {
                            margin-top: 10px
                        }

                        .loading {
                            height: 280px
                        }

                        .loading p {
                            color: #fff;
                            font-size: 24px;
                            margin-top: 10px
                        }

                        .information {
                            height: calc(100vh - 120px);
                            position: relative;
                            overflow: hidden;
                            z-index: 999;
                            padding: 103px 190px 0 111px
                        }

                        .information .content {
                            margin-top: 98px
                        }

                        .information .content .top .figure {
                            margin-top: 13px
                        }

                        .information .content .top .figure .figureName {
                            font-size: 40px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF
                        }

                        .information .content .top .figure .figureNameEng {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #B3B2FC;
                            margin-top: 11px
                        }

                        .information .content .top .topRight {
                            width: 566px;
                            height: 153px;
                            position: relative
                        }

                        .information .content .top .topRight img {
                            position: absolute
                        }

                        .iconImg {
                               width: 200px;
                               height: 190px;
                               top: -25px;
                               left: -25px;
                        }

                        .information .content .top .topRight .topRightContent {
                            position: absolute;
                            top: 29px;
                            left: 157px
                        }

                        .information .content .top .topRight .topRightContent .topRightContentName1 {
                            font-size: 36px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF
                        }

                        .information .content .top .topRight .topRightContent .topRightContentName2 {
                            font-size: 24px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            margin-top: 30px
                        }

                        .information .content .cength .character .characterList .itemLeft {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            border-bottom: 3px solid #2D2282;
                            border-right: 3px solid #2D2282;
                            padding: 8px 30px 8px 8px;
                            display: inline-block
                        }

                        .information .content .cength .character .characterList .itemRight {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            border-left: 3px solid #2D2282;
                            border-bottom: 3px solid #2D2282;
                            padding: 8px 8px 8px 30px;
                            display: inline-block
                        }

                        .information .content .cength .character .characterImg {
                            position: relative;
                            width: 58px;
                            height: 58px
                        }

                        .information .content .cength .character .characterImg .characterText {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            text-align: center;
                            line-height: 58px;
                            position: relative
                        }

                        .information .content .cength .character .characterImg img {
                            position: absolute
                        }

                        .information .content .cength .talentSkill {
                            padding-top: 120px;
                            padding-right: 75px
                        }

                        .information .content .cength .talentSkill .talent {
                            width: 72px;
                            height: 113px;
                            position: relative
                        }

                        .information .content .cength .talentSkill .talent img {
                            position: absolute
                        }

                        .information .content .cength .talentSkill .talent p {
                            position: relative;
                            font-size: 16px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            left: 18px;
                            top: 84px
                        }

                        .information .content .cength .talentSkill .talentList {
                            border-left: 1px solid #524F7E;
                            margin-left: 16px;
                            padding: 0 13px;
                            height: 99px
                        }

                        .information .content .cength .talentSkill .talentList .talentItem {
                            margin-right: 13px
                        }

                        .information .content .cength .talentSkill .talentList .talentItem img {
                            width: 79px;
                            height: 83px
                        }

                        .information .content .cength .talentSkill .talentList .talentItem .talentItemName {
                            font-size: 16px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            text-align: center;
                            margin-top: 7px
                        }

                        .information .content .bottom {
                            margin-top: 60px
                        }

                        .information .content .bottom .designation {
                            padding: 15px 0 0 30px;
                            width: 328px;
                            height: 310px;
                            position: relative
                        }

                        .information .content .bottom .designation .po {
                            position: relative
                        }

                        .information .content .bottom .designation .bjcImg {
                            position: absolute;
                            top: 0;
                            left: 0
                        }

                        .information .content .bottom .designation .designationNmae {
                            position: relative;
                            font-size: 26px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            margin-left: 9px
                        }

                        .information .content .bottom .designation .designationContent {
                            margin: 38px auto 0
                        }

                        .information .content .bottom .designation .designationContent .designationContentImg {
                            width: 299px;
                            height: 54px;
                            position: relative;
                            font-size: 32px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF
                        }

                        .information .content .bottom .designation .designationContent .designationContentImg img {
                            position: absolute;
                            top: 0
                        }

                        .information .content .bottom .designation .designationContent .designationContentImg p {
                            position: relative;
                            text-shadow: .2rem 0 .5rem #6865EE, -.2rem 0 .5rem #6865EE, 0 .2rem .5rem #6865EE, 0 -.2rem .5rem #6865EE
                        }

                        .information .content .bottom .designation .designationContent .designationContentItemImg {
                            width: 216px;
                            height: 20px;
                            margin-top: 28px;
                            position: relative;
                            margin-left: 38px
                        }

                        .information .content .bottom .designation .designationContent .designationContentItemImg img {
                            position: absolute;
                            top: 0
                        }

                        .information .content .bottom .designation .designationContent .designationContentItemImg p {
                            position: relative;
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF
                        }

                        .information .content .bottom .probabilityList::-webkit-scrollbar {
                            width: 3px
                        }

                        .information .content .bottom .probabilityList::-webkit-scrollbar-track {
                            border-radius: 10px
                        }

                        .information .content .bottom .probabilityList::-webkit-scrollbar-thumb {
                            background: #414090;
                            border-radius: 10px
                        }

                        .information .content .bottom .course {
                            padding: 15px 0 0 30px;
                            width: 469px;
                            position: relative;
                            margin-left: 16px
                        }

                        .information .content .bottom .course .bjcImg {
                            position: absolute;
                            top: 0;
                            left: 0
                        }

                        .information .content .bottom .course .po {
                            position: relative
                        }

                        .information .content .bottom .course .designationNmae {
                            position: relative;
                            font-size: 26px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            margin-left: 9px
                        }

                        .information .content .bottom .course .courseText {
                            width: 420px;
                            padding-right: 30px;
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF;
                            position: relative;
                            margin-top: 60px;
                            line-height: 30px;
                            overflow: hidden;
                            
                        }

                        .information .content .bottom .course .courseText::-webkit-scrollbar {
                            width: 10px
                        }

                        .information .content .bottom .course .courseText::-webkit-scrollbar-track {
                            border-radius: 10px
                        }

                        .information .content .bottom .course .courseText::-webkit-scrollbar-thumb {
                            background: #414090;
                            border-radius: 10px
                        }

                        .information .content .bottom .probability {
                            width: 385px;
                            height: 280px;
                            padding: 28px 24px 15px 24px;
                            right: 135px;
                            position: relative;
                            background-image: url(https://ec.wexiang.vip/source/img1/54.png)
                        }

                        .information .content .bottom .probability .probabilityList {
                            width: 100%;
                            height: 100%;
                            overflow: hidden;
                            overflow-y: scroll;
                            position: relative
                        }

                        .information .content .bottom .probability .probabilityList .probabilityListItem {
                            margin-bottom: 20px;
                            padding: 0 50px 0 20px
                        }

                        .information .content .bottom .probability .probabilityList .probabilityListItem .character1 {
                            font-size: 18px;
                            color: #fff;
                            margin-right: 11px
                        }

                        .information .content .bottom .probability .probabilityList .probabilityListItem .probabilityListItemName {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF
                        }

                        .information .content .bottom .probability .probabilityList .probabilityListItem .probabilityListItemNub {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FFF
                        }

                        * {
                            margin: 0;
                            padding: 0
                        }

                        li {
                            list-style: none
                        }

                        img {
                            vertical-align: top;
                            border: none
                        }

                        body,
                        h1,
                        h2,
                        h3,
                        h4,
                        h5,
                        h6,
                        hr,
                        p,
                        blockquote,
                        dl,
                        dt,
                        dd,
                        ul,
                        ol,
                        li,
                        pre,
                        fieldset,
                        lengend,
                        button,
                        input,
                        textarea,
                        th,
                        td {
                            margin: 0;
                            padding: 0
                        }

                        body,
                        button,
                        input,
                        select,
                        textarea {
                            font: 12px/1 Tahoma, Helvetica, Arial, "\5b8b\4f53", sans-serif
                        }

                        h1 {
                            font-size: 18px
                        }

                        h2 {
                            font-size: 16px
                        }

                        h3 {
                            font-size: 14px
                        }

                        h4,
                        h5,
                        h6 {
                            font-size: 100%
                        }

                        address,
                        cite,
                        dfn,
                        em,
                        var {
                            font-style: normal
                        }

                        code,
                        kbd,
                        pre,
                        samp,
                        tt {
                            font-family: "Courier New", Courier, monospace
                        }

                        small {
                            font-size: 12px
                        }

                        ul,
                        ol {
                            list-style: none
                        }

                        a {
                            text-decoration: none
                        }

                        a:hover {
                            text-decoration: underline
                        }

                        abbr[title],
                        acronym[title] {
                            border-bottom: 1px dotted;
                            cursor: help
                        }

                        button,
                        input,
                        select,
                        textarea {
                            font-size: 100%
                        }

                        table {
                            border-collapse: collapse;
                            border-spacing: 0
                        }

                        .character {
                            width: 225px;
                            height: 518px;
                            position: relative;
                        }

                        .character .flower {
                            width: 225px;
                            position: absolute;
                            overflow: hidden;
                        }

                        .character .mask {
                            animation: animate 2s ease;
                        }

                        @keyframes animate {
                            from {
                                height: 0px;
                                top: 518px;
                            }

                            to {
                                height: 518px;
                                top: 0px;
                            }
                        }

                        .character .flower img {
                            width: 225px;
                            height: 518px;
                        }

                        .character .text {
                            position: absolute;
                            font-size: 24px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #FDFFFF;
                            text-shadow: .2rem 0rem .8rem #FDFFFF;
                        }

                        .character .font16 {
                            font-size: 18px;
                        }

                        .character .peculiarity {
                            font-size: 18px;
                            font-family: Source Han Serif CN;
                            font-weight: 800;
                            color: #999999;
                            position: absolute;
                        }

                        .prize1 {
                            position: absolute;
                            top: -15%;
                            width: 55%;
                            left:22%;
                            overflow: hidden;
                            z-index: 666;
                        }
                        .prize2 {
                            position: absolute;
                            top: -10%;
                            left:12%;
                            width: 70%;
                            overflow: hidden;
                            
                            z-index: 666;
                        }
                        .prize3 {
                            position: absolute;
                            top: -20%;
                            width: 50%;
                            left:22%;
                            overflow: hidden;
                            z-index: 666;
                        }
                    </style>';
            
        }

        
        return ['status'=>true,'msg' => '','data' => $html];

    }

    // 上传角色详情截图
    public function upCutRoleImg()
    {
        $id = input('id');
        $image = input('upfile');//base64

        if (strstr($image,",")){
            $image = explode(',',$image);
            $image = $image[1];
        }

        $prop_mod = new \app\common\model\Prop();

        $prop_info = $prop_mod->where('goods_id',$id)->where('type',1)->find();
        
        $cutimg_name = 'cut_'.$id.'_img.webp';

        if(!file_exists($cutimg_name)){
            $r = file_put_contents($cutimg_name, base64_decode($image));
        }

        if($prop_info){
            return ['status' => true,'msg' => '成功','data' => $prop_info['prop_img']];
        
        }else{

            $prop_data = [
                'goods_id' => $id,
                'name' => '名片',
                'prop_img' => 'http://'.$_SERVER['HTTP_HOST'].'/'.$cutimg_name,
                'type' => 1
            ];

            $res = $prop_mod->insert($prop_data);

            return ['status' => true,'msg' => '成功','data' => 'http://'.$_SERVER['HTTP_HOST'].'/'.$cutimg_name];
        }

    }
}
