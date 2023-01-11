<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: keinx <keinx@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Area;
use app\common\model\Balance;
use app\common\model\GoodsComment;
use app\common\model\Invoice;
use app\common\model\Setting;
use app\common\model\UserBankcards;
use app\common\model\UserPointLog;
use app\common\model\UserShip;
use app\common\model\UserTocash;
use app\common\model\UserToken;
use app\common\model\User as UserModel;
use app\common\model\GoodsBrowsing;
use app\common\model\GoodsCollection;
use app\common\model\UserWx;
use app\common\model\BillPayments;
use org\login\Alipayapp;
use org\login\Ttapp;
use org\login\Uniapp;
use org\login\Wxapp;
use org\login\Wxofficial;
use org\Poster;
use org\Share;
use org\share\UrlShare;
use think\facade\Cache;
use think\facade\Request;
use phpmailer\phpmailer;

/**
 * 用户
 * Class User
 * @package app\api\controller
 */
class User extends Api
{
    /**
     * 登陆/注册
     * @return array|mixed
     */
    public function login()
    {
        $platform = input('param.platform', 5);      //1就是h5登陆（h5端和微信公众号端），2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc,6.钱包登陆
        // 登陆方式:1.平台账号登陆2.钱包登陆
        $userModel = new UserModel();
        $data = input('param.');
        $type = $data['type'] ? $data['type'] : 1;//方式:1登陆2注册 
        $verify_type = $data['verify_type'] ? $data['verify_type'] : 1;//验证方式:1邮箱2手机
        return $userModel->toLogin($data, 2, $platform,$type,$verify_type);
    }


    /**
     * 短信验证码登陆，手机短信验证注册账号
     * mobile       手机号码，必填
     * code         手机验证码，必填
     * invitecode   邀请码，推荐人的邀请码 选填
     * password     注册的时候，可以传密码 选填
     * user_wx_id   第三方账号，绑定当前已有账号，第三方登录，微信公众号里的登陆，微信小程序登陆等需要绑定账户的时候，要传这个参数，这是第一次的时候需要这样绑定，以后就不需要了  选填
     * @return array
     */
    public function smsLogin()
    {
        $platform = input('param.platform', 1);
        $userModel = new UserModel();
        $data = input('param.');
        return $userModel->smsLogin($data, 2, $platform);
    }

    public function bindMobile()
    {
        $userModel = new UserModel();
        if (!input('?param.mobile')) {
            return error_code(11051);
        }
        if (!input('?param.code')) {
            return error_code(10013);
        }
        return $userModel->bindMobile($this->userId, input('param.mobile'), input('param.code'));
    }


    /**
     * 微信小程序创建用户，不登陆，只是保存登录态
     * @return array
     */
    public function wxappLogin1()
    {
        if (!input("?param.code")) {
            // $result['msg'] = error_code(10068, true);
            return error_code(10068);
        }
        $wxapp = new Wxapp();
        return $wxapp->codeToInfo(input('param.code'));
    }


    /**
     * 微信小程序传过来了手机号码，那么取他的手机号码
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wxappLogin2()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        if (!input("?param.open_id")) {
            $result['msg'] = 'open_id';
            return $result;
        }
        if (!input("?param.iv")) {
            // $result['msg'] = error_code(10070, true);
            return error_code(10070);
        }
        if (!input("?param.edata")) {
            //加密的encryptedData数据，这是个加密的字符串
            // $result['msg'] = error_code(10071, true);
            return error_code(10071);
        }
        //如果新用户不需要手机号码登陆，但是有推荐人的话，校验推荐人信息
        $invitecode = Request::param('invitecode', false);
        if ($invitecode && $invitecode != '') {
            $userModel = new UserModel();
            $pid = $userModel->getUserIdByShareCode($invitecode);
            $pinfo = $userModel->where(['id' => $pid])->find();
            if ($pinfo) {
                $pid = $pinfo['id'];
            } else {
                //return error_code(10014);
                $pid = 0;
            }
        } else {
            $pid = 0;
        }
        $wxapp = new Wxapp();

        return $wxapp->updateWxInfo(input('param.open_id'), input('param.edata'), input('param.iv'), $pid);
    }


    /**
     * 支付宝小程序创建用户，不登陆，只是保存登录态
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function alipayappLogin1()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $code = Request::param('code', false);
        $user_info = Request::param('user_info', false);

        if (!$code) {
            // $result['msg'] = error_code(10068, true);
            return error_code(10068);
        }
        $aliPayApp = new Alipayapp();
        return $aliPayApp->codeToInfo($code, $user_info);
    }


    /**
     * 发送登陆注册短信，type为1注册，为2登陆
     * @return array|mixed
     */
    public function sms()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => '成功'
        ];
        $userModel = new UserModel();
        if (!input("?param.mobile")) {
            // $result['msg'] = error_code(11051, true);
            return error_code(11051);
        }

        if(!isMobile(input("param.mobile"))){
            return error_code(11057);
        }
        
        //code的值可以为loign，reg，veri
        if (!input("?param.code")) {
            // $result['msg'] = error_code(10068, true);
            return error_code(10068);
        }
        $code = input('param.code');
        $type = input('param.type');
        if ($type == 'bind') { //绑定会员，这个if迟早要拿掉，绑定的话，也发送login状态就行
            $code = 'login';
        }
        return $userModel->sms(input('param.mobile'), $code);
    }


    /**
     * 退出
     * @return array
     */
    public function logout()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if (!input("?param.token")) {
            // $result['msg'] = error_code(11001, true);
            return error_code(11001);
        }
        $userTokenModel = new UserToken();
        return $userTokenModel->delToken(input("param.token"));
    }


    //    /**
    //     * 注册，此接口迟早要废弃，建议直接使用smsLogin接口
    //     * @return array
    //     */
    //    public function reg()
    //    {
    //        $userModel = new UserModel();
    //        $data      = input('post.');
    //        return $userModel->smsLogin($data, 2);
    //    }


    /**
     * 微信公众号登陆，根据code，返回openid
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function officialLogin()
    {
        if (!input('?param.code')) {
            return error_code(10068);
        }
        $scope = input('param.scope', 1);        //公众号登陆类型，1是snsapi_userinfo，2是snsapi_base

        //如果新用户不需要手机号码登陆，但是有推荐人的话，校验推荐人信息
        $invitecode = Request::param('invitecode', false);
        if ($invitecode && $invitecode != '') {
            $userModel = new UserModel();
            $pid = $userModel->getUserIdByShareCode($invitecode);
            $pinfo = $userModel->where(['id' => $pid])->find();
            if ($pinfo) {
                $pid = $pinfo['id'];
            } else {
                $pid = 0;
                //return error_code(10014);
            }
        } else {
            $pid = 0;
        }
        $wx = new Wxofficial();
        return $wx->codeToInfo(input('param.code'), input('param.state'), $scope, $pid);
    }


    /**
     * 用户信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $userModel = new UserModel();
        $user_id = input('user_id',0);
        $my_id = 0;
        $token = input('token', ''); //token值 会员登录后传
        if(empty($user_id)){
            $user_id = getUserIdByToken($token); //获取user_id
            if(empty($user_id)){
                return error_code(14007);
            }
            // $user_id = $this->userId;
        }else{
            $my_id = getUserIdByToken($token);
        }

        return $userModel->getUserInfo($user_id,$my_id);
    }


    /**
     * 更换头像
     * @return array|mixed
     */
    public function changeAvatar()
    {
        $result = [
            'status' => false,
            'data' => input('param.'),
            'msg' => error_code(10004, true)
        ];
        if (!input("?param.avatar")) {
            return error_code(11003);
        }
        $userModel = new UserModel();
        if ($userModel->changeAvatar($this->userId, input('param.avatar'))) {
            $result['status'] = true;
            $result['data']['avatar'] = input('param.avatar');
            $result['msg'] = '保存成功';
        }
        return $result;
    }


    /**
     * 编辑用户信息
     * @return array|mixed
     */
    public function editInfo()
    {
        $sex = input('param.sex', '');
        $birthday = input('param.birthday', '');
        $nickname = input('param.nickname', '');
        $userModel = new UserModel();
        return $userModel->editInfo($this->userId, $sex, $birthday, $nickname);
    }

    // 设置用户信息
    public function updateUserInfo()
    {   
        $token = input('token', ''); //token值 会员登录后传
        $user_id = getUserIdByToken($token); //获取user_id
        if(empty($user_id)){
            return error_code(14006);
        }
        $data = input('param.');

        $userModel = new UserModel();
        return $userModel->setUserInfo($user_id,$data);
    }

    /**
     * 添加商品浏览足迹
     * @return array
     */
    public function addGoodsBrowsing()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if (!input("?param.goods_id")) {
            // $result['msg'] = error_code(10013, true);
            return error_code(10013);
        }

        $goodsBrowsingModel = new GoodsBrowsing();
        return $goodsBrowsingModel->toAdd($this->userId, input("param.goods_id"));
    }


    /**
     * 删除商品浏览足迹
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delGoodsBrowsing()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if (!input("?param.goods_ids")) {
            // $result['msg'] = error_code(10013, true);
            return error_code(10013);
        }
        $goodsBrowsingModel = new GoodsBrowsing();
        return $goodsBrowsingModel->toDel($this->userId, input("param.goods_ids"));
    }


    /**
     * 取得商品浏览足迹
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsBrowsing()
    {
        if (input("?param.limit")) {
            $limit = input("param.limit");
        } else {
            $limit = config('jshop.page_limit');
        }
        if (input("?param.page")) {
            $page = input("param.page");
        } else {
            $page = 1;
        }
        $goodsBrowsingModel = new GoodsBrowsing();
        return $goodsBrowsingModel->getList($this->userId, $page, $limit);
    }


    /**
     * 添加商品收藏（关注）
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsCollection()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        if (!input("?param.goods_id")) {
            // $result['msg'] = error_code(10013, true);
            return error_code(10013);
        }
        $goodsCollectionModel = new GoodsCollection();
        return $goodsCollectionModel->toDo($this->userId, input("param.goods_id"));
    }


    /**
     * 取得商品收藏记录（关注）
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsCollectionList()
    {
        if (input("?param.limit")) {
            $limit = input("param.limit");
        } else {
            $limit = config('jshop.page_limit');
        }
        if (input("?param.page")) {
            $page = input("param.page");
        } else {
            $page = 1;
        }
        $goodsCollectionModel = new GoodsCollection();
        return $goodsCollectionModel->getList($this->userId, $page, $limit);
    }


    // /**
    //  * 存储用户收货地址接口
    //  * @return array
    //  * @throws \think\Exception
    //  * @throws \think\db\exception\DataNotFoundException
    //  * @throws \think\db\exception\ModelNotFoundException
    //  * @throws \think\exception\DbException
    //  * @throws \think\exception\PDOException
    //  */
    // public function saveUserShip()
    // {
    //     //传入进来的数据
    //     $area_id = input('area_id');
    //     $user_name = input('user_name');
    //     $detail_info = input('detail_info');
    //     $tel_number = input('tel_number');
    //     $is_def = input('is_def');
    //     $user_id = $this->userId;

    //     $data['user_id'] = $user_id;
    //     $data['area_id'] = $area_id;
    //     $data['address'] = $detail_info;
    //     $data['name'] = $user_name;
    //     $data['mobile'] = $tel_number;
    //     $data['is_def'] = $is_def;

    //     //存储收货地址
    //     $model = new UserShip();
    //     $result = $model->saveShip($data);
    //     if ($result !== false) {
    //         $return_data = array(
    //             'status' => true,
    //             'msg' => '保存成功',
    //             'data' => $result
    //         );
    //     } else {
    //         $return_data = array(
    //             'status' => false,
    //             'msg' => error_code(10004,true),
    //             'data' => $result
    //         );
    //     }
    //     return $return_data;
    // }


    //     /**
    //      * 废弃，所有收货地址新增编辑都走   editShip 方法     
    //      * H5 添加收货地址
    //      * @return array
    //      * @throws \think\Exception
    //      * @throws \think\db\exception\DataNotFoundException
    //      * @throws \think\db\exception\ModelNotFoundException
    //      * @throws \think\exception\DbException
    //      * @throws \think\exception\PDOException
    //      */
    //     public function vueSaveUserShip()
    //     {
    //         $data['user_id'] = $this->userId;
    //         $data['area_id'] = input('param.area_id');
    //         $data['address'] = input('param.address');
    //         $data['name'] = input('param.name');
    //         $data['mobile'] = input('param.mobile');
    //         $data['is_def'] = input('param.is_def');
    //         $model = new UserShip();
    //         return $model->vueSaveShip($data);
    // //        if($result)
    // //        {
    // //            $return_data = [
    // //                'status' => true,
    // //                'msg' => '存储收货地址成功',
    // //                'data' => $result
    // //            ];
    // //        }
    // //        else
    // //        {
    // //            $return_data = [
    // //                'status' => false,
    // //                'msg' => '存储收货地址失败',
    // //                'data' => $result
    // //            ];
    // //        }
    // //        return $return_data;
    //     }


    /**
     * 获取收货地址详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShipDetail()
    {
        $id = input('param.id');
        $model = new UserShip();
        $result = $model->getShipById($id, $this->userId);
        if ($result) {
            $result['area_name'] = get_area($result['area_id']);
            $res = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $result
            ];
        } else {
            // $res = [
            //     'status' => false,
            //     'msg' => error_code(10002, true),
            //     'data' => ''
            // ];
            return error_code(10002);
        }
        return $res;
    }


    /**
     * 收货地址编辑
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editShip()
    {
        $data['name'] = input('param.name');
        $data['area_id'] = input('param.area_id');
        $data['address'] = input('param.address');
        $data['mobile'] = input('param.mobile');
        $data['is_def'] = input('param.is_def');
        $data['id'] = input('param.id');

        $model = new UserShip();
        return $model->editShip($data, $this->userId);
    }


    /**
     * 删除收货地址
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function removeShip()
    {
        if (!input('param.id')) {
            return error_code(10051);
        }
        $model = new UserShip();
        return $model->removeShip(input('param.id'), $this->userId);
    }


    /**
     * 设置默认地址
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setDefShip()
    {
        if (!input('param.id')) {
            return error_code(10051);
        }
        $model = new UserShip();
        return $model->setDefaultShip(input('param.id'), $this->userId);
    }


    /**
     * 获取用户收货地址列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserShip()
    {
        $user_id = $this->userId;
        $model = new UserShip();
        $list = $model->getUserShip($user_id);
        if ($list) {
            $return_data = array(
                'status' => true,
                'msg' => '获取用户收货地址成功',
                'data' => $list
            );
        } else {
            $return_data = array(
                'status' => true,
                'msg' => '用户暂无收货地址',
                'data' => $list
            );
        }
        return $return_data;
    }


    /**
     * 获取收货地址全部名称
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllName()
    {
        $id = input('id');
        return get_area($id);
    }


    /**
     * 获取最终地区ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAreaId()
    {
        $province_name = input('province_name');
        $city_name = input('city_name');
        $county_name = input('county_name');
        $postal_code = input('postal_code');
        $model = new Area();
        $area_id = $model->getThreeAreaId($county_name, $city_name, $province_name, $postal_code);
        if ($area_id) {
            $res = [
                'status' => true,
                'msg' => '获取成功',
                'data' => $area_id
            ];
        } else {
            $res = [
                'status' => false,
                'msg' => error_code(10025, true),
                'data' => $area_id
            ];
        }
        return $res;
    }


    /**
     * 支付
     * @return array|mixed
     */
    public function pay()
    {
        if (!input("?param.ids")) {
            return error_code(13100);
        }
        if (!input("?param.payment_code")) {
            return error_code(10055);
        }
        if (!input("?param.payment_type")) {
            return error_code(10051);
        }

        $token = input('token', ''); //token值 会员登录后传
        $user_id = getUserIdByToken($token); //获取user_id

        //支付的时候，有一些特殊的参数需要传递到支付里面，这里就是干这个事情的,key=>value格式的一维数组
        $data = input('param.');
        if (!isset($data['params'])) {
            $params = [];
        } else {
            $params = $data['params'];
        }
        // 验证订单支付状态
        $billPaymentsModel = new BillPayments();
        if(input("param.payment_type") == $billPaymentsModel::TYPE_ORDER){
            $orderModel = new \app\common\model\Order();
            $res = $orderModel->checkOrderStatus(input("param.ids"));
            if(!$res['status']){
                $res['status'] = true;
                return $res;
            }
        }
        $billPaymentsModel = new BillPayments();
        //生成支付单,并发起支付
        return $billPaymentsModel->pay(input('param.ids'), input('param.payment_code'), $user_id, input('param.payment_type'), $params);
    }


    /**
     * 订单评价接口
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderEvaluate()
    {
        if (!input('items/a')) {
            //缺少评价商品信息
            return error_code(13400);
        }
        if (!input('order_id')) {
            //没有order_id
            return error_code(13401);
        }

        $order_id = input('order_id');
        $items = input('items/a');

        //添加评价
        $model = new GoodsComment();
        $result = $model->addComment($order_id, $items, $this->userId);
        return $result;
    }


    /**
     * 获取用户默认收货地址
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDefaultShip()
    {
        $user_id = $this->userId;
        $model = new UserShip();
        $res = $model->getUserDefaultShip($user_id);
        return $res;
    }


    /**
     * 判断是否签到
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function isSign()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        $res = $userPointLog->isSign($user_id);
        return $res;
    }


    /**
     * 签到操作
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sign()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        $res = $userPointLog->sign($user_id);
        return $res;
    }


    /**
     * 获取签到信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSignInfo()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        return $userPointLog->getSignInfo($user_id);
    }


    /**
     * 获取用户积分
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserPoint()
    {
        $user_id = $this->userId;
        $order_money = Request::param('order_money', 0);
        $userModel = new UserModel();
        return $userModel->getUserPoint($user_id, $order_money);
    }


    /**
     * 获取我的银行卡列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankCardList()
    {
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->getMyBankcardsList($this->userId);
    }


    /**
     * 获取默认的银行卡
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultBankCard()
    {
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->defaultBankCard($this->userId);
    }


    /**
     * 添加银行卡
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addBankCard()
    {
        $bankCardsModel = new UserBankcards();
        $data = [
            'bank_name' => input('param.bankName'), //银行名
            'bank_code' => input('param.bankCode'), //银行编码
            'bank_area_id' => input('param.areaId/d'), //开户行地区
            'account_bank' => input('param.accountBank'), //开户行名称
            'account_name' => input('param.accountName'), //持卡人
            'card_number' => input('param.cardNumber'), //银行卡号
            'card_type' => input('param.cardType/d'), //银行卡类型
            'is_default' => input('param.isDefault', 2) //是否默认
        ];
        return $bankCardsModel->addBankcards($this->userId, $data);
    }


    /**
     * 删除银行卡
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function removeBankCard()
    {
        $card_id = input('param.id/d');
        if (!$card_id) return error_code(10051);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->delBankcards($this->userId, $card_id);
    }


    /**
     * 设置默认银行卡
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function setDefaultBankCard()
    {
        $card_id = input('param.id/d');
        if (!$card_id) return error_code(10051);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->setDefault($this->userId, $card_id);
    }


    /**
     * 获取银行卡信息
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankCardInfo()
    {
        $card_id = input('param.id/d');
        if (!$card_id) return error_code(10051);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->getBankcardInfo($this->userId, $card_id);
    }


    /**
     * 获取银行卡组织信息
     * @return array|mixed
     */
    public function getBankCardOrganization()
    {
        $card_code = input('param.card_code');
        if (!$card_code) return error_code(11017);
        $bankCardsModel = new UserBankcards();
        return $bankCardsModel->bankCardsOrganization($card_code);
    }


    /**
     * 用户修改密码
     * @return array|mixed
     */
    public function editPwd()
    {
        $user = new \app\common\model\User();
        $email_mod = new \app\common\model\Email();
        $sms = new \app\common\model\Sms();

        $new_pwd = input('param.newpwd');
        $cur_pwd = input('param.repwd');

        if (!$new_pwd) {
            return error_code(11013);
        }
        if (!$cur_pwd) {
            return error_code(11014);
        }

        if ($new_pwd != $cur_pwd) {
            return error_code(11025);
        }

        $code = input('param.code');
        if(!$code){
            return error_code(10013);
        }

        $email = input('param.email');

        if($email){
            // 邮箱
            if(!isEmail($email)){
                return error_code(11079);
            }

            $email_exist = $user->where('email',$email)->find();
            if(empty($email_exist)){
                return error_code(11081);
            }

            $e_res = $email_mod->where(['receive' => $email,'code' => $code,'type' => 3,'verify_status' => 2])->find();

            if(!$e_res){
                 return error_code(11180);
            }

            $userId = $email_exist['id'];
            $e_res = $email_mod->where(['receive' => $email,'code' => $code,'type' => 3,'verify_status' => 2])->order('id desc')->update(['verify_status' => 1]);
        }

        $mobile = input('param.mobile');

        if($mobile){
            
            // 邮箱
            if(!isMobile($mobile)){
                return error_code(11057);
            }

            $phone_exist = $user->where('mobile',$mobile)->find();
            if(empty($phone_exist)){
                return error_code(11081);
            }

            if (!$sms->check($mobile, $code, 'cwp')) {
                return error_code(11046);
            }

            $userId = $phone_exist['id'];
            $sms->where(['mobile' => $mobile,'code' => 'cwp','status' => 1])->update(['status' => 1]);
        }

        return $user->changePassword($userId, input('param.newpwd'), input('param.repwd'));
    }


    /**
     * 用户找回密码
     * @return array|mixed
     */
    public function forgetPwd()
    {
        if (!input('?param.mobile')) {
            return error_code(11051);
        }
        if (!input('?param.code')) {
            return error_code(10013);
        }
        if (!input('?param.newpwd')) {
            return error_code(11013);
        }
        if (!input('?param.repwd')) {
            return error_code(11014);
        }

        if (input('param.newpwd') != input('param.repwd')) {
            return error_code(11025);
        }

        $userModel = new userModel();
        return $userModel->forgetPassword(input('param.mobile'), input('param.code'), input('param.newpwd'));
    }


    /**
     * 获取我的余额明细
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userBalance()
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', config('jshop.page_limit'));
        $order = Request::param('order', 'ctime desc');
        $type = Request::param('type', 0);
        $balanceModel = new Balance();
        return $balanceModel->getBalanceList($this->userId, $order, $page, $limit, $type);
    }


    /**
     * 获取用户推荐列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recommend()
    {
        $page = input('param.page', 1);
        $limit = input('param.limit', config('jshop.page_limit'));
        $userModel = new UserModel();
        return $userModel->recommendList($this->userId, $page, $limit);
    }


    /**
     * 邀请码
     * @return array
     */
    public function sharecode()
    {
        $userModel = new UserModel();
        return $result = [
            'status' => true,
            'data' => $userModel->getShareCodeByUserId($this->userId),
            'msg' => ''
        ];
    }


    /**
     * 用户提现申请
     * @return array|mixed
     */
    public function cash()
    {
        $money = input('param.money/f', '0', 'remove_xss');
        $bankcard_id = input('param.cardId');
        if (!$money) return error_code(11018);
        if (!$bankcard_id) return error_code(11017);
        $userToCashModel = new UserTocash();
        return $userToCashModel->tocash($this->userId, $money, $bankcard_id);
    }


    /**
     * 获取用户提现记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cashList()
    {
        $page = input('param.page', 1);
        $limit = input('param.limit', config('jshop.page_limit'));
        $type = input('param.type', '');
        $userToCashModel = new UserTocash();
        return $userToCashModel->userToCashList($this->userId, $page, $limit, $type);
    }


    /**
     * 获取信任登录内容，标题，图标，名称，跳转地址
     * @return array|mixed
     */
    public function getTrustLogin()
    {
        $data = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        if (!input('?param.url')) {
            return error_code(10000);
        }
        $wx = new Wxofficial();

        $data['data'] = [
            'Wxofficial' => $wx->geturl(input('param.url') . "?type=Wxofficial")
        ];

        return $data;
    }


    /**
     * 是否开启积分
     * @return array
     */
    public function isPoint()
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => 2
        ];
        $settingModel = new Setting();
        $return['data'] = $settingModel->getValue('point_switch');
        return $return;
    }


    /**
     * 获取我的要求相关信息
     * @return array
     */
    public function myInvite()
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        //我的邀请码
        $code = $this->sharecode();
        $return['data']['code'] = $code['data'];
        //我邀请的人数
        $userModel = new UserModel();
        $where[] = ['pid', 'eq', $this->userId];
        $return['data']['number'] = $userModel->where($where)->count();
        //邀请赚的佣金
        $return['data']['money'] = 0;
        $balanceModel = new Balance();
        $balance = $balanceModel->getInviteCommission($this->userId);
        if ($balance['status']) {
            $return['data']['money'] = $balance['data'];
        }
        //是否有上级
        $userInfo = $userModel->get($this->userId);
        $is_superior = false;
        if ($userInfo['pid'] && $userInfo['pid'] != 0) {
            $is_superior = true;
        }
        $return['data']['is_superior'] = $is_superior;

        return $return;
    }


    /**
     * 设置我的上级邀请人
     * @return array
     * @throws \think\exception\DbException
     */
    public function activationInvite()
    {
        $code = Request::param('code');
        $userModel = new UserModel();
        return $userModel->setMyInvite($this->userId, $userModel->getUserIdByShareCode($code));
    }


    /**
     * 用户积分明细
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userPointLog()
    {
        $user_id = $this->userId;
        $userPointLog = new UserPointLog();
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        $res = $userPointLog->pointLogList($user_id, false, $page, $limit);
        return $res;
    }


    /**
     * 获取省市区信息
     * @return array
     */
    public function getAreaList()
    {
        $return = [
            'status' => true,
            'msg' => '获取成功',
            'data' => []
        ];
        $area = config('jshop.area_list');
        if (!file_exists($area)) {
            // $return['status'] = false;
            // $return['msg'] = error_code(10072, true);
            return error_code((10072));
        }
        $data = file_get_contents($area);
        echo $data;
        exit();
    }


    /**
     * 生成海报
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPoster()
    {
        $token = Request::param('token', false);
        if ($token) {
            $data['user_id'] = getUserIdByToken($token);
        } else {
            $data['user_id'] = 0;
        }
        $data['type'] = Request::param('type', 1); //分享类型 1=商品海报 2=邀请海报 3=拼团海报 4=店铺首页
        $data['id'] = Request::param('id', 0); //类型值 1商品海报就是商品ID 2邀请海报无需填 3拼团海报的时候就是商品ID 4店铺code
        $data['group_id'] = Request::param('group_id', 0); //拼团海报的时候是拼团规则的ID
        $data['team_id'] = Request::param('team_id', 0); //拼团海报的时候是拼团的团队ID
        $data['source'] = Request::param('source', 1); //来源 1=普通H5页面 2=微信小程序 3=微信公众号H5 4=头条系小程序
        $data['return_url'] = Request::param('return_url', ''); //返回URL地址
        $data['tt_platform'] = Request::param('tt_platform', false); //头条系小程序对应的具体平台

        $poster = new Poster();
        return $poster->posterGenerate($data);
    }


    /**
     * 新的分享，不管是二维码，还是地址，都走这个
     * page	场景值 1店铺首页，2商品详情页，3拼团详情页，4邀请好友（店铺页面,params里需要传store），5文章页面，6参团页面，7自定义页面，8智能表单，9团购秒杀
     * url 前端地址
     * params 参数，根据场景值不一样而内容不一样
     *  1
     *  2 goods_id:商品ID
     *  3 goods_id:商品ID
     *  4 store:店铺code
     *  5 article_id:文章ID，article_type:文章类型
     *  6 goods_id:商品ID，group_id:参团ID，team_id:拼团ID
     *  7 page_code:自定义页面code
     *  8 id:智能表单ID
     *  9 goods_id:商品ID，group_id:团购秒杀ID
     * type 类型，1url，2二维码，3海报
     * token 可以保存推荐人的信息
     * client 终端，1普通h5，2微信小程序，3微信公众号（h5），4头条系小程序，5pc网站，6阿里小程序
     * @return array|mixed
     */
    public function share()
    {
        $token = Request::param('token', false);
        if (input('?param.token')) {
            $user_id = getUserIdByToken($token);
        } else {
            $user_id = 0;
        }
        $page = input('param.page');
        $url = input('param.url');
        $params = input('param.params', [],'safe_filter'); //json_decode(input('param.params', ""), true);
        $type = input('param.type');
        $client = input('param.client');
        $share = new Share();
        return $share->get($client, $page, $type, $user_id, $url, $params);
    }


    /**
     * @return array|mixed
     */
    public function deshare()
    {
        if (!input('?param.code')) {
            return error_code(10000);
        }
        $share = new UrlShare();
        $re = $share->de_url(input('param.code'));
        if (!$re['status']) {
            return $re;
        }

        $obj = new \stdClass;
        $obj->data = $re['data'];
        Hook('deshare', $obj);
        $re['data'] = $obj->data;

        return $re;
    }


    /**
     * APP信任登录
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function uniAppLogin()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        if (!input("?param.type")) {
            // $result['msg'] = error_code(10003, true);
            return error_code(10003);
        }
        $data = input('param.');
        //        $userWxModel = new UserWx();
        $uniapp = new Uniapp();

        //如果新用户不需要手机号码登陆，但是有推荐人的话，校验推荐人信息
        if (input('?param.invitecode')) {
            $userModel = new \app\common\model\User();
            $pid = $userModel->getUserIdByShareCode(input('param.invitecode'));
            $pinfo = $userModel->where(['id' => $pid])->find();
            if ($pinfo) {
                $data['pid'] = $pinfo['id'];
            } else {
                $data['pid'] = 0;
                //return error_code(10014);
            }
        } else {
            $data['pid'] = 0;
        }

        return $uniapp->login($data);
    }


    /**
     * 分享URL生成
     * @return array
     */
    public function shareUrl()
    {
        $token = Request::param('token', false);
        if ($token) {
            $data['user_id'] = getUserIdByToken($token);
        } else {
            $data['user_id'] = 0;
        }
        $data['type'] = Request::param('type', 1); //分享类型 1=商品海报 2=邀请海报 3=拼团海报 4=店铺首页
        $data['id'] = Request::param('id', 0); //类型值 1商品海报就是商品ID 2邀请海报无需填 3拼团海报的时候就是商品ID 4店铺code
        $data['team_id'] = Request::param('team_id', 0); //拼团海报的时候是拼团的团队ID
        $data['return_url'] = Request::param('return_url', ''); //返回URL地址

        $poster = new Poster();
        return $poster->urlGenerate($data);
    }


    /**
     * 我的发票列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function myInvoiceList()
    {
        $page = Request::param('page', 1);
        $limit = Request::param('limit', config('jshop.page_limit'));
        $status = Request::param('status', false);
        $id = Request::param('id', 0);
        $invoiceModel = new Invoice();
        return $invoiceModel->myInvoiceList($this->userId, $page, $limit, $status, $id);
    }


    /**
     * 头条系小程序登录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ttLogin()
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];

        $code = Request::param('code', false);
        $user_info = Request::param('user_info', false);

        if (!$code) {
            // $result['msg'] = error_code(10003, true);
            return error_code(10003);
        }
        $ttApp = new Ttapp();
        return $ttApp->codeToInfo($code, $user_info);
    }


    /**
     * 收货地址地图经纬度逆向解析
     */
    public function addressMap()
    {
        $key      = input('key');
        $location = input('location');
        $poi      = input('get_poi');
        $url      = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $location . '&key=' . $key . '&get_poi=' . $poi;
        $data     = $this->map_curl($url);
        echo json_encode($data, 320);
        exit();
    }

    /***
     * 地图关键词搜索
     */
    public function mapSearch()
    {
        $key        = input('key');
        $keyword    = input('keyword');
        $boundary   = input('boundary');
        $orderby    = input('orderby', '_distance');
        $page_size  = input('page_size', 20);
        $page_index = input('page_index', 1);
        $url        = 'https://apis.map.qq.com/ws/place/v1/search?keyword=' . urlencode($keyword) . '&key=' . $key . '&boundary=' . $boundary . '&page_size=' . $page_size . '&page_index=' . $page_index . '&orderby=' . $orderby;
        $data       = $this->map_curl($url);
        echo json_encode($data, 320);
        exit();
    }

    /**
     * 收货地址地图curl方法，增加来源页面
     * @param $url
     * @return mixed
     */
    private function map_curl($url)
    {
        $ch = curl_init(); //初始化
        curl_setopt($ch, CURLOPT_URL, $url); //你要访问的页面
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']); //伪造来路页面
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否显示内容
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }


    // 获取推荐创作者
    public function getRecommendUser()
    {   
        $userModel = new UserModel();
        $is_recommend = input('is_recommend',1);
        return $userModel->getRecommendUser($is_recommend,input('p/d',1), input('n/d',20));
    }

    // 用户喜欢
    public function userCare()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);
        if(empty($user_id)){
            return error_code(14006);
        }
        $id = input('id');
        $type = input('type',1);
        $act = input('act',1);//1点赞2取消点赞

        $userCareModel = new \app\common\model\UserCare();

        return $userCareModel->addUserCare($user_id,$id,$type,$act);
    }

    // 校验验证码
    public function findCode()
    {
        $code = input('code');
        $type = input('type',1);//验证方式2手机1邮箱
        $value = input('value');

        $smsModel    = new \app\common\model\Sms();
        $email = new \app\common\model\Email();


        if (!isset($code) || empty($code)) {
            return error_code(10013);
        }

        if($type == 2){
            if (!isset($value) || empty($value)) {
                return error_code(11051);
            }

            if (!$smsModel->check($value, $code, 'reg')) {
                return error_code(11046);
            }

        }else{
            if (!isset($value) || empty($value)) {
                return error_code(11079);
            }

            $e_res = $email->where(['receive' => $value,'code' => $code,'type' => 1,'verify_status' => 2])->find();
            if(!$e_res){
                return error_code(11180);
            }
        }


        return ['status' => true,'msg'=>'成功'];
    }

    // 用户出价
    public function userPostPrice()
    {
        $userPriceModel = new \app\common\model\UserPrice();

        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $price = input('price');
        $start_time = strtotime(input('start_time'));
        $end_time = strtotime(input('end_time'));
        $type = input('type',1);
        $id = input('id');
        $receive_id = input('receive_id');

        if(empty($user_id)){
            return error_code(14006);
        }

        if(empty($price)){
            return error_code(15706);
        }

        if(!is_numeric($price) || $price < 0){
            return error_code(15709);
        }

        if(empty($start_time)){
            return error_code(15710);
        }

        if(empty($start_time)){
            return error_code(15711);
        }

        if(empty($id)){
            return error_code(15712);
        }

        $data = [
            'user_id' => $user_id,
            'price' => $price,
            'ctime' => time(),
            'type' => $type,
            'p_id' => $id,
            's_time' => $start_time,
            'e_time' => $end_time,
            'receive_id' => $receive_id
        ];

        $res = $userPriceModel->insert($data);
        // dump($res);die;
        //上架市场
        $articleModel = new \app\common\model\Article();
        $musicModel = new \app\common\model\Files();
        $gameModel = new \app\common\model\Game();
        $imgModel = new \app\common\model\Pictures();
        $goodsModel = new \app\common\model\Goods();

        if($type == 1){
            // 文章
            $now_price = $articleModel->where('id',$id)->value('price');
            if($now_price < $price){
                $articleModel->where('id',$id)->update(['price' => $price]); 
            }
            
        }elseif($type == 2){
            // 音频
            $now_price = $musicModel->where('id',$id)->value('price');
            if($now_price < $price){
                $musicModel->where('id',$id)->update(['price' => $price]); 
            }
        }elseif($type == 3){
            // 游戏
            $now_price = $gameModel->where('g_id',$id)->value('price');
            if($now_price < $price){
                $gameModel->where('g_id',$id)->update(['price' => $price]); 
            }

        }elseif($type == 4){
            // 插画
            $now_price = $imgModel->where('id',$id)->value('price');
            if($now_price < $price){
                $imgModel->where('id',$id)->update(['price' => $price]); 
            }
        }else{
            // 角色
            $now_price = $goodsModel->where('id',$id)->value('price');
            if($now_price < $price){
                $goodsModel->where('id',$id)->update(['price' => $price]); 
            }
        }

        return $res ? ['status' => true,'msg'=>'成功'] : error_code(10018);
    }

    // 个人中心
    public function getUserCenter()
    {
        $status = input('status',1);//1.已收藏2.已创建3.寄售4拍卖5出价6成交记录7喜欢8个人作品
        $user_id = input('user_id',0);//其他用户的user_id
        if(empty($user_id)){
            $token = Request::param('token', false);
            $user_id = getUserIdByToken($token);
        }
        
        if(empty($user_id)){
            return error_code(14006);
        }

        $page = input('page',1);
        $limit = input('limit',10);
        $keyword = input('keyword','');//关键词
        $is_draft = input('is_draft',-1);//是否存为草稿:0否1是-1全部

        $userModel = new UserModel();
        return $userModel->getUserProduct($user_id,$status,$page,$limit,$keyword,$is_draft);
    }

    // 发送谷歌邮件
    public function sendGoogleMail()
    {
        $email = new \app\common\model\Email();
        $toemail = input('email');//定义收件人的邮箱
        $type = input('type',1);//类型:1注册2验证3修改密码

        if(!isEmail($toemail)){
            return error_code(11079);
        }

        $code = rand(1000,9999);
        
        $mail = new PHPMailer();
        // dump($mail);die;

        $mail->isSMTP();// 使用SMTP服务
        // 方案1
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        // $mail->Host = "smtp.gmail.com";// 发送方的SMTP服务器地址
        // $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = "acg.isekai@gmail.com";// 发送方的gmail邮箱用户名，就是你申请gmail的SMTP服务使用的gmail邮箱
        $mail->Password = "food cauz ecwf btov";// 发送方的邮箱密码，注意用gmail邮箱这里填写的是应用专用密码
        // $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        // $mail->Port = 465;// 端口号

        // 方案2
        $mail->Host = 'relay-hosting.secureserver.net';
        $mail->Port =  25;
        // $mail->SMTPAuth = false;
        // $mail->SMTPSecure = false;

        // 方案3
        // $mail->SMTPSecure = 'tls';
        // $mail->Host = 'smtp.gmail.com';
        // $mail->Port = 587;

        $mail->setFrom("acg.isekai@gmail.com", "Verification code");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@gmail.com），Mailer是当做名字显示
        $mail->addAddress($toemail, $toemail);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@gmail.com)
        $mail->addReplyTo("acg.isekai@gmail.com", "Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址


        $mail->Subject = "Verification Code";// 邮件标题
        $mail->Body = "Your verification code is: {$code}. Please keep it safe.";// 邮件正文

        $res = $mail->send();
        $email_data = [
            'send' => $mail->Username,
            'receive' => $toemail,
            'subject' => $mail->Subject,
            'body' => $mail->Body,
            'code' => $code,
            'ctime' => time(),
            'send_status' => $res,
            'type' => $type
        ];

        $email->save($email_data);

        if (!$res) {// 发送邮件
            return [
                'status' => false,
                'msg' => $mail->ErrorInfo
            ];

        } else {
            return ['status' => true,'msg' => '邮件已发送,请注意查收'];
        }
            
    }

    // 邮箱订阅
    public function reserveEmail()
    {   
        $user = new \app\common\model\User();
        $email = input('email');//邮箱

        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);
        
        if(empty($user_id)){
            return error_code(14006);
        }

        if(!isEmail($email)){
            return error_code(11079);
        }

        $saveData = [
            'is_subscribe' => 1,
            'reserve_email' => $email
        ];

        $res = $user->where('id',$user_id)->update($saveData);
        return ['status' => true,'msg' => '成功'];
    }

    // 关注用户
    public function userLove()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);
        $type = input('type',1);//1.关注2.取消关注       

        if(empty($user_id)){
            return error_code(14006);
        }

        $to_user_id = input('to_user_id');//关注者id
        if(empty($to_user_id)){
            return error_code(10003);
        }

        $userLove = new \app\common\model\UserFollow();

        $loveData = [
            'user_id' => $user_id,
            'follow_id' => $to_user_id
        ];

        if($type == 1){
            $res = $userLove->insert($loveData);
        }else{
            $res = $userLove->where($loveData)->delete();
        }
        
        return $res ? ['status' => true,'msg' => ''] : error_code(10004);
    }

    // 号码验证
    public function veriPhone()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $phone = input('phone');
        $code = input('code');
        $user = new \app\common\model\User();

        return $user->checVeriPhone($user_id,$phone,$code);
    }

    // 寄售和拍卖
    public function saleWorks()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $sale_mod = new \app\common\model\UserSale();
        $data = input('param.');

        return $sale_mod->saleMyWorks($user_id,$data);
    }

    // 获取寄售和拍卖数据
    public function getSales()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $sale_mod = new \app\common\model\UserSale();
        $sale_id = input('sale_id');

        return $sale_mod->getSaleMyWorks($user_id,$sale_id);
    }

    //校验用户数据
    public function checkUserData()
    {
        $user = new \app\common\model\User();

        $phone = input('mobile');
        $email = input('email');
        $password = input('password');
        $first_name = input('first_name');
        $last_name = input('last_name');
        $verify_type = input('verify_type');
        $type = input('platform',5);//注册平台5平台6钱包

        if($verify_type == 1){
            // 邮箱
            if(!isEmail($email)){
                return error_code(11079);
            }

            $email_exist = $user->where('email',$email)->find();
            if(!empty($email_exist)){
                return error_code(11181);
            }

        }else{
            // 手机
            if(!isMobile($phone)){
                return error_code(11057);
            }

            $phone_exist = $user->where('mobile',$phone)->find();
            if(!empty($phone_exist)){
                return error_code(11058);
            }
        }

        if(!isset($first_name) || empty($first_name)){
            return error_code(11008);
        }

        if (!isset($last_name) || empty($last_name)) {
            return error_code(11008);
        }

        $name_exist = $user->where(['first_name'=>$first_name,'last_name' => $last_name])->find();
        if(!empty($name_exist)){
            return error_code(11185);
        }

        if($type == 5){
            // if (strlen($password) < 8 || strlen($password) > 16) {
            //     return error_code(11009);
            // }
            if(!preg_match("/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){8,}$/", $password)){
                return error_code(11187);
            }
        }
        
        return ['status' => true,'msg' => '验证通过'];
    }

    // 取消招标
    public function cancelSale()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $sale_mod = new \app\common\model\UserSale();
        $sale_id = input('sale_id');

        return $sale_mod->cancelSale($user_id,$sale_id);
    }

    // 钱包置顶
    public function setWallectDefault()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $wallet_mod = new \app\common\model\userWallet();

        $wallet_mod->setWallet();
    }

    // 判断钱包是否存在用户
    public function wallectExist()
    {
        $wallet_mod = new \app\common\model\userWallet();
        $address = input('address');//钱包地址

        if(empty($address)){
            return error_code(10003);
        }

        $userModel = new \app\common\model\User();

        $token = '';
        $user_id = $wallet_mod->where('address',$address)->value('user_id');
        if(!empty($user_id)){
            $userInfo = $userModel->where(array('id' => $user_id))->find();
            $token = $userModel->setSession($userInfo,2,6)['data'];   
        }

        return ['status' => true,'msg' => '成功','data' => $token];
    }

    // metamask验签
    public function checkSign()
    {
        $signVerify = new EthSign();
        $msg = 'Signning in to 192.168.0.25at1662538200';
        $address = input('address');//钱包地址
        $sign = input('sign');//签名
        $wallet_mod = new \app\common\model\userWallet();

        if(empty($address) || empty($sign)){
            return error_code(10003);
        }

        $result = $signVerify->verify($msg, $sign, $address);
        $userTokenModel = new \app\common\model\UserToken();

        $token = '';

        if($result){
            $user_id = $wallet_mod->where('address',$address)->value('user_id');
            $token = $userTokenModel->where('user_id',$user_id)->value('token');
        }

        return ['status' => true,'msg' => '验签通过','data' => $token];
    }

    // 链接新钱包
    public function addWallect()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $wallet_mod = new \app\common\model\userWallet();
        $address = input('address');//钱包地址

        if(empty($address)){
            return error_code(10206);
        }

        $wallect_info = $wallet_mod->where('address',$address)->find();
        if($wallect_info){
            return error_code(10208);
        }

        $wallect_data = [
            'user_id' => $user_id,
            'address' => $address,
            'ctime' => time(),
            'type' => 2
        ];

        $res = $wallet_mod->insert($wallect_data);

        return $res ? ['status' => true,'msg' => '添加成功'] : error_code(10004);
    }

    // 删除钱包
    public function breakWallect(){
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $wallet_mod = new \app\common\model\userWallet();

        $address = input('address');//钱包地址

        if(empty($address)){
            return error_code(10206);
        }

        $res = $wallet_mod->where(['user_id'=>$user_id,'address' => $address])->delete();

        return $res ? ['status' => true,'msg' => '删除成功'] : error_code(10023);
    }

    //修改用户二要素手机号验证
    public function checkUserVert()
    {
        $user = new \app\common\model\User();

        $phone = input('mobile');
        $code = input('code');

        return $user->checSetPhone($phone,$code);
    }

    // 购买卡券
    public function userBuyCard()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);
        $card_mod = new \app\common\model\UserCard();

        $number = input('number',1);

        return $card_mod->addCard($user_id,$number);
    }

    // 删除出价
    public function delPriceLog()
    {
        $userPrice = new \app\common\model\UserPrice();

        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);
        $id = input('price_id');

        $res = $userPrice->where(['user_id' => $user_id,'id' => $id])->delete();

        return $res ? ['status' => true,'msg' => '删除成功'] : error_code(10023);
    }

    // 同意出价
    public function agreePrice()
    {
        $business = new \app\common\model\Business();
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);
        $log_id = input('price_id');

        return $business->agreePrice($log_id,$user_id);
    }

    // 充值
    public function userRecharge()
    {
        $userWallet = new \app\common\model\userWallet();
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $address = input('address');
        $money = input('money');

        if(empty($address)){
            return error_code(10206);
        }

        if(empty($money)){
            return error_code(11020);
        }

        $res = $userWallet->where(['address' => $address,'user_id' => $user_id])->setInc('balance' , $money);
        
        return $res ? ['status' => true,'msg' => '充值成功'] : error_code(10018);
    }

    // 解锁内容
    public function unlockContent()
    {
        $lock_mod = new \app\common\model\UserUnlockLog();
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $id = input('id');
        $type = input('type');//1文章2音频3游戏4插画
        if(empty($id) || empty($type)){
            return error_code(10003);
        }

        return $lock_mod->unlockContent($id,$type,$user_id);
    }

    // 用户铸造NFT
    public function mintNft()
    {
        $token = Request::param('token', false);
        $user_id = getUserIdByToken($token);

        $id = input('id');
        $address = input('address');//钱包地址
        if(empty($address)){
            return error_code(10206);
        }

        $type = input('type');//1文章2音频3游戏4插画
        if(empty($id) || empty($type)){
            return error_code(10003);
        }

        return mintNFT($address,$id,$type);
    }
}
