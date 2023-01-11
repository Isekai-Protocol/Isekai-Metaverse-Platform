<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: sin <sin@jihainet.com>
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Area;
use app\common\controller\Base;
use think\facade\Cache;
use think\facade\Hook;
use org\Curl;
use app\common\model\Setting as SettingModel;

/**
 * old 此控制器不用api校验，不需要token登陆，就是单纯的标准的接口数据,用于取一些通用的信息
 * new 此控制器不校验token登陆但是最好也放到api体系了里面吧
 * Class Common
 * @package app\api\controller
 */
class Common extends Api
{
    /**
     * 加载方法
     */
    protected function initialize()
    {
        parent::initialize();
        //解决跨域问题
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式
    }


    /**
     * 取省市区的详细信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function area()
    {
        $areaModel = new Area();
        return $areaModel->getArea(input('param.id', 0));
    }


    /**
     * 取地区的下级列表
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function areaChildren()
    {
        $areaModel = new Area();
        return $areaModel->getAllChild(input('param.id'));
    }


    /**
     * 获取验证码，如果登陆失败次数太多，就需要验证码了
     * @return string
     */
    public function verify()
    {
        if (input('?param.id')) {
            $id = input('param.id');
        } else {
            $id = "";
        }
        return captcha_src($id);
    }


    /**
     * 获取店铺设置，此接口用于统一返回店铺的公开设置，然后本地缓存即可
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function jshopConf()
    {
        // if (!Cache::has("jshop_jshopconf")) {
            $config = getMultipleSetting('shop_logo,shop_name,shop_desc,shop_default_image,rate_fee,login_logo,pay_switch,adv_switch,album_switch,author_switch,music_switch,article_switch,game_switch,img_switch,pay_content_desc,number_product_desc,number_copy_desc,card_img,card_price,metamask_switch');

            $conf['shop_logo']          = _sImage($config['shop_logo']); //店铺logo
            $conf['shop_name']          = $config['shop_name'];  //店铺名称
            $conf['shop_desc']          = $config['shop_desc'];  //店铺描述
            // $conf['image_max']          = 5; //前端上传图片最多几张
            // $conf['store_switch']       = $config['store_switch'];    //开启门店自提状态
            // $conf['cate_style']         = $config['cate_style'];    //分类样式
            // $conf['cate_type']          = $config['cate_type'];  //H5分类类型
            // $conf['tocash_money_low']   = $config['tocash_money_low'];    //最低提现
            // $conf['tocash_money_rate']  = $config['tocash_money_rate'];  //服务费
            // $conf['point_switch']       = $config['point_switch'];    //是否开启积分功能
            // $conf['statistics']         = $config['statistics_code'];   //获取统计代码
            // $recommend_keys             = $config['recommend_keys'];
            // $conf['recommend_keys']     = explode(' ', $recommend_keys);    //搜索推荐关键字
            $conf['pay_switch']     = $config['pay_switch'];    //支付开关
            $conf['adv_switch']     = $config['adv_switch'];    //开启幻灯片
            $conf['album_switch']     = $config['album_switch'];    //开启精选专辑
            $conf['author_switch']     = $config['author_switch'];    //开启推荐创作者
            $conf['music_switch']     = $config['music_switch'];    //开启推荐音频
            $conf['article_switch']     = $config['article_switch'];    //开启同人文章
            $conf['game_switch']     = $config['game_switch'];    //开启推荐游戏
            $conf['img_switch']     = $config['img_switch'];    //开启推荐插画
            $conf['metamask_switch']  = $config['metamask_switch'];  //仅能metamask登录
            $conf['shop_default_image'] = _sImage($config['shop_default_image']);   //获取默认图片
            // $conf['shop_mobile']        = $config['shop_mobile'];  //店铺联系电话
            // $conf['open_distribution']  = get_addons_status('DistributionCenter');   //是否开启分销
            // if ($conf['open_distribution']) {
            //     $distributionSetting            = getAddonsConfigVal('DistributionCenter', 'setting');
            //     $conf['distribution_notes']     = isset($distributionSetting['notes']) ? $distributionSetting['notes'] : '';    //用户须知
            //     $conf['distribution_agreement'] = isset($distributionSetting['agreement']) ? $distributionSetting['agreement'] : '';    //分销协议
            //     $conf['distribution_store']     = isset($distributionSetting['distribution_store']) ? $distributionSetting['distribution_store'] : '';    //是否开启店铺
            // }
            $conf['pay_content_desc']       = $config['pay_content_desc'];  //内容体验发行说明
            $conf['number_product_desc']       = $config['number_product_desc'];  //数字藏品发行说明
            $conf['number_copy_desc']       = $config['number_copy_desc'];  //数字版权发行说明
            // $conf['show_inviter']      = $config['show_inviter'];    //是否显示邀请人信息
            $conf['share_title']       = $config['share_title'];  //分享标题
            $conf['share_desc']        = $config['share_desc'];    //分享描述
            $conf['share_image']       = _sImage($config['share_image']); //分享图片
            $conf['login_logo']       = _sImage($config['login_logo']); //登陆logo
            $conf['apikeyToken']       =  'PGB1W72547QD8UWY4FWV1MDXM9DPCIJMJD';//以太坊接口秘钥
            $conf['card_img']         = _sImage($config['card_img']); //卡券封面
            $conf['card_price']       = $config['card_price'];  //卡券价格
            // $conf['about_article_id']  = $config['about_article_id'];    //关于我们文章
            // $conf['ent_id']            = $config['ent_id'];    //客服ID
            // $conf['user_agreement_id'] = $config['user_agreement_id']; //用户协议
            // $conf['privacy_policy_id'] = $config['privacy_policy_id']; //隐私政策
            // $conf['shop_beian']        = $config['shop_beian']; //备案
            // $conf['language']          = getSetting('language');        //语言包，预留的口
            $conf['rate_fee']        = $config['rate_fee'];    //服务费率%
            $listModel = new \app\common\model\WallectPower();
            $address_list = $listModel->column('address');
            $conf['address_list'] = $address_list;

            $extendModel = new \app\common\model\GoodsExtend();
            $role_type = array_unique($extendModel->column('role_type'));
            $role_type = array_values($role_type);

            $figure = array_unique($extendModel->column('figure'));
            $figure = array_values($figure);

            $type_arr = [];
            for ($i=0; $i < count($role_type); $i++) { 
                // dump($role_type[$i]);
                $type_arr[] = [
                    'name' => $role_type[$i],
                    'falg' => false
                ];
            }

            $figure_arr = [];
            for ($j=0; $j < count($figure); $j++) { 
                // dump($role_type[$i]);
                $figure_arr[] = [
                    'name' => $figure[$j],
                    'falg' => false
                ];
            }
            // dump($type_arr);die;

            $conf['role'] = [
                [
                    'name' => '类型',
                    'falg' => true,
                    'list' => $type_arr
                        // [
                        //     'name' => '女标',
                        //     'falg' => false
                        // ],
                        // [
                        //     'name' => '男标',
                        //     'falg' => false
                        // ],
                        // [
                        //     'name' => '老头',
                        //     'falg' => false
                        // ],
                        // [
                        //     'name' => '哥布林',
                        //     'falg' => false
                        // ],
                        // [
                        //     'name' => '胖妞',
                        //     'falg' => false
                        // ],
                    
                    
                ],
                
               [
                   'name' => '头身',
                   'falg' => true,
                   'list' => $figure_arr
                   // [
                       // [
                       //     'name' => '5头身',
                       //     'falg' => false
                       // ],
                       // [
                       //     'name' => '6头身',
                       //     'falg' => false
                       // ],
                       // [
                       //     'name' => '7头身',
                       //     'falg' => false
                       // ],
                       // [
                       //     'name' => '8头身',
                       //     'falg' => false
                       // ],
                       // [
                       //     'name' => '9头身',
                       //     'falg' => false
                       // ],
                   // ]
                   
               ]
            ];//角色类型

            // dump($conf['role']);die;
            // $conf['role'][] = [
                

            // ];//角色头身

            //手机端商品详情页文字说明，如果为空就不显示
            // $goods_show_word = [];
            // if ($config['goods_show_word1'] != '') {
            //     $goods_show_word[] = $config['goods_show_word1'];
            // }
            // if ($config['goods_show_word2'] != '') {
            //     $goods_show_word[] = $config['goods_show_word2'];
            // }
            // $conf['goods_show_word'] = $goods_show_word;
            // Cache::set("jshop_jshopconf", $conf, 3600 * 12);
        // } else {
        //     $conf = Cache::get("jshop_jshopconf");
        // }
        
        return $conf;
        
        
    }

    // 设置语言
    public function setLanguage()
    {
        $lng = input('lng','cn');//cn中文en英文
        // $settingModel = new SettingModel();
        session('lng_'.$_SERVER['HTTP_COOKIE'],$lng);
        // dump(session('lng_'.$_SERVER['HTTP_COOKIE']));die;
        // return $settingModel->setValue('language'.gethostbyname(""), $lng);
        return ['status' => true,'msg' => '成功','data' => session('lng_'.$_SERVER['HTTP_COOKIE'])];
    }

    // 测试
    public function test()
    {
        $r = file_get_contents('chain_log.txt');
        echo $r;
        // 查询余额
        // $arr = [1,2,3,4,1,2,1];
        // dump(getArrCommon($arr));die;

        // $wallet_mod = new \app\common\model\MiningLog();
        // $wallet_mod->addUserMining(13);  
        // $wallet_info = $wallet_mod->where('user_id',16)->find();
        // $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        // $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        // $key = $key_1.$key_2;

        // dump($key);die;
        // $res = setApprovalForAll('0xd11cc280eea00f0be7eac16e8277637f8ca8d8c7',1);
        // dump($res);die;
        // $res = startSale('0x7a6a481ffdd53b9294188bda7e7e83fea0918881',1,'0x21305ac79e8193f16da766e666cbbed91c2473c8c8ff21d3136624c3058df7af',1);
        // dump($res);die;
        // metadata数据
        // $articleModel = new \app\common\model\Article();
        // $musicModel = new \app\common\model\Files();
        // $gameModel = new \app\common\model\Game();
        // $imgModel = new \app\common\model\Pictures();
        // $roleModel = new \app\common\model\Goods();
        // $extendModel = new \app\common\model\GoodsExtend();
        // $propModel = new \app\common\model\Prop();
        // $assembly_mod = new \app\common\model\Assembly();
        // $part_mod = new \app\common\model\GoodsPart();

        // $type = input('type',1);
        // if($type == 1){
        //     // 文章
        //     $list = $articleModel->field('title as name,cover as img_url,brief,price,code,royalties,file_url')->select();
        // }elseif($type == 2){
        //     // 音乐
        //     $list = $musicModel->field('name,path as img_url,price,code,royalties,url as file_url,type,desc')->select();

        // }elseif($type == 3){
        //     // 游戏
        //     $list = $gameModel->field('game_name as name,cover as img_url,price,code,royalties')->select();
        // }elseif($type == 4){
        //     // 插画
        //     $list = $imgModel->field('name,cover as img_url,price,code,royalties,desc,type')->select();
        // }else{
        //     // 角色
        //     $list = $roleModel->field('id,name,price,bn as code,royalties,brief,sex,work')->select();
        //     if($list){
        //         foreach ($list as $key => $value) {
        //             $roleInfo = $extendModel->where('goods_id',$value['id'])->find();
        //             $list[$key]['img_url'] = $roleInfo['role_img'];
        //             $list[$key]['work_type'] = $roleInfo['work_type'];
        //             $list[$key]['work_life'] = $roleInfo['work_life'];
        //             $list[$key]['history'] = $roleInfo['history'];
        //             $list[$key]['role_title_info'] = unserialize($roleInfo['role_title_info']);
        //             $list[$key]['character_info'] = unserialize($roleInfo['character_info']);
        //             $list[$key]['talent_info'] = unserialize($roleInfo['talent_info']);
        //             $list[$key]['potential_info'] = unserialize($roleInfo['potential_info']);
        //             $prop = $propModel->field('prop_img,role_img,type')->where('goods_id',$value['id'])->select();
        //             $list[$key]['prop'] = $prop;
        //             if($prop){
        //                 foreach ($prop as $k => $val) {
        //                     if($val['type'] == 1){
        //                         $list[$key]['prop'][$k]['prop_img'] = 'https://pzss.oss-cn-guangzhou.aliyuncs.com/static/uploads/images/2022/09/20/16636604266329718abbeb7.png';

        //                     }else{
        //                         $list[$key]['prop'][$k]['prop_img'] = _sImage($val['prop_img']);
        //                     }

        //                     $list[$key]['prop'][$k]['role_img'] = _sImage($val['role_img']);
        //                 }
        //             }

        //             // 组件
        //             $part_arr = [];
        //             $part_ids = $part_mod->where('goods_id',$value['id'])->column('part_id');
        //             if(!empty($part_ids)){
        //                 $info_part = $assembly_mod->whereIn('id',$part_ids)->select();
        //                 if($info_part){
        //                     foreach ($info_part as $pkey => $pvalue) {
        //                         $part_arr[] = [
        //                             'name' => $pvalue['part_name'],
        //                             'cate' => $pvalue['part_cate'],
        //                             'number' => computeRate($pvalue['id'])
        //                         ];  
        //                     }

        //                 }
        //             }

        //             $list[$key]['part'] = $part_arr;
        //         }
        //     }
        // }
        
        // foreach ($list as $lk => $lv) {
        //     $list[$lk]['img_url'] = _sImage($lv['img_url']);
        //     if(isset($lv['desc'])){
        //         $list[$lk]['brief'] = $lv['desc'];
        //         unset($list[$lk]['desc']);
        //     }

        // }

        // return ['data' => $list];
        // file_put_contents('new.txt', '测试接口'.input('time').'<br>',FILE_APPEND);
        // $this->saveUserTime();
        // $uniqueId = hash('fnv164', uniqid().microtime(true));
        // dump($uniqueId);die;
        // dump(setTokenRoyalty(2,'0xdd8f1fa6464340c68200a99288a8b35b4d659545e5b8406555eac37153892294','0xd11cc280eea00f0be7eac16e8277637f8ca8d8c7',10,'0xab9fa7ee32d31167c8fd4ec11d863c1af7800da4be7fcb9ef9eee04bef1f0b4a'));die;
        // $address = '0xf3654eb72a8d2e3a3cdb4753f8486999de870a71';
        // dump(formatWallet($address));die;

        // $apitoken = 'PGB1W72547QD8UWY4FWV1MDXM9DPCIJMJD';
        // $url = 'https://api.etherscan.io/api?module=account&action=addresstokennftbalance&address='.$address.'&page=1&offset=100&apikey='.$apitoken;
        // dump($url);die;
        // $roleModel = new \app\common\model\Goods();
        // $extendModel = new \app\common\model\GoodsExtend();
        // $imgModel = new \app\common\model\Images();
        // $goods_ids = $roleModel->where('sex',1)->column('id');

        // $img_ids = $extendModel->whereIn('goods_id',$goods_ids)->column('role_img');

        // for ($i=0; $i < count($img_ids); $i++) {
        //     $imgModel->where('id',$img_ids[$i])->delete();
        // }
        // die;
        
        // $imgModel->whereIn('id',$img_ids)->delete();
        // dump(getSetting('language'.gethostbyname("")));die;
        // $str = '爱你';
        // $f = 'zh';
        // $t = 'en';
        // $res = language($str,$f,$t);
        // dump($res);die;
        // dump(gtranslate('iloveyou'));die;
        // $file = 'https://pzss.oss-cn-guangzhou.aliyuncs.com/static/uploads/book/2022/08/03/165951966462ea42b0bcdbc.md';
        // echo file_get_contents($file);die;

        // $curl = new Curl();        
        // // 创建钱包
        // $makeWalletUrl = 'http://124.70.153.7:1234/api/v1/createWallet';
        // $res = $curl::get($makeWalletUrl); 
        // $result = json_decode($res);
        // dump($result);die;
        // // $res_data = $result->data;
        // $str = '"0x9be44fde4f46543f2be097dced3d5e1f2ce024cd98e8307aa88a18a1a957e6c3';
        // $s = str_split($str, strlen($str)/2);
        // // $r = getRandStr();
        // $r = 'b3sECKFeTVP5c4zRQwWdZ9mG=YXItlygkp62aAOoUSnJrLv+8qH0NxhM-fiBDu17j';

        // $s_r1 = eccodeStr($s[0],$r);
        // $s_r2 = eccodeStr($s[1],$r);

        // $s1 = base64_decode('IjB4OWJlNDRmZGU0ZjQ2NTQzZjJiZTA5N2RjZWQzZDVlYjNzRUNLRmVUVlA1YzR6UlF3V2RaOW1HPVlYSXRseWdrcDYyYUFPb1VTbkpyTHYrOHFIME54aE0tZmlCRHUxN2o=');
        // $s2 = base64_decode('MWYyY2UwMjRjZDk4ZTgzMDdhYTg4YTE4YTFhOTU3ZTZjYjNzRUNLRmVUVlA1YzR6UlF3V2RaOW1HPVlYSXRseWdrcDYyYUFPb1VTbkpyTHYrOHFIME54aE0tZmlCRHUxN2o=');
        // $s_1 = str_replace($r,'',$s1);
        // $s_2 = str_replace($r,'',$s2);

        // dump($s_1.$s_2);die;
        
        // $new_str = $s_d1.$s_d2;
        // dump($new_str);
        // die;
        // $res_data->privateKey
        // dump($res_data->privateKey);
        
        // dump($s);die;
        // 0x877feb1c2f915395a778c76dd24424fa80f71546

        // nft铸造
        // $mintNFT = 'http://192.168.0.50:1234/api/v1/mintNFT';
        // $mintNFT = 'http://124.70.153.7:1234/api/v1/mintNFT';

        // $uniqueId = hash('fnv164', uniqid().microtime(true));

        // $data = [
        //     'contractLevel' => 1,
        //     'address' => '0x9b794009f0eb76f3d26c9308ba6d14ab37f8c3d3',
        //     'uri' => $uniqueId,
        //     'privateKey' => '0x6f64eb67802fb51e83b1213941b213dcc79f8040bb941d543e995cec055db416'
        // ];

        // $res = $curl::post($mintNFT, $data);
        // dump($res);die;
        //nft交易
        // $safeUrl = 'http://124.70.153.7:1234/api/v1/safeTransfer'; 
        // $safeData = [
        //     'fromAddress' => '0xb761c9faef7e1b097506fc783d763b18a565b66e',
        //     'id' => '0x527d9489d9087667f0bd9c0885ae7f3ae8428759f01e998ea4ce901a8852b3cb',//nft铸造接口返回的data
        //     'privateKey' => '0xc689c2eda65706e007a1d58bac64732894c803e79e81e5718f4efdb2c4bf985b',
        //     'toAddress' => '0x877feb1c2f915395a778c76dd24424fa80f71546',
        //     'values' => 1
        // ];

        // $request_data = json_encode($safeData);
        // dump($safeData);die;
        // $res = $curl::post($safeUrl, $safeData);

        // 出售nft
        // $saleUrl = 'http://124.70.153.7:1234/api/v1/startSale';
        // $params = [
        //     'id' => '0x527d9489d9087667f0bd9c0885ae7f3ae8428759f01e998ea4ce901a8852b3cb',
        //     'num' => 1,
        //     'privateKey' => '0xc689c2eda65706e007a1d58bac64732894c803e79e81e5718f4efdb2c4bf985b'
        // ]; 

        // $res = $curl::post($saleUrl, $params);
        // dump($res);die;

        // 发送短信
        // $smsUrl = 'http://47.97.85.29:7862/sms?action=send';

        // $params = '&action=send&account=922138&password=EPKYPh&mobile=13533988641&content=你的验证码是:'.rand(1000,9999).'&extno=10690138&rt=json';

        // $params = urlencode($params);
        // $res = $curl::post($smsUrl, $params);

        // dump($res);die;
        // sendMsg('13533988641',rand(1000,9999));

        // $html = '<div class="title">角色情报<div class="wire"></div></div><div class="information"><div class="content"><div class="top u-f u-f-jsb"><div class="figure"><div class="u-f-item"><p class="figureName">利姆鲁·特恩佩斯特</p><div style="width:22px;height:30px;margin-left:9px"><img class="img" src="https://ec.wexiang.vip/source/img1/45.png" alt=""></div></div><p class=figureNameEng>Rimuru Tempest</p></div><div class=topRight><img class=img src=https://ec.wexiang.vip/source/img1/51.png alt=""><div class=topRightContent><p class=topRightContentName1>魔法师、剑士</p><p class=topRightContentName2>转生前魔法师、剑士</p></div></div></div><div class="cength u-f u-f-jsb"><div class="character u-f-column u-f-item u-f-justify"><div style=width:142px;height:143px><img class=img src=https://ec.wexiang.vip/source/img1/46.png alt=""></div><div class="characterList u-f"><div class=itemLeft>单纯</div><div style=visibility:hidden;border:none class=itemLeft>单纯</div></div><div class="characterList u-f"><div style=visibility:hidden;border:none class=itemLeft>异想天开</div><div class=itemRight>异想天开</div></div><div class="characterList u-f"><div class=itemLeft>单纯</div><div style=visibility:hidden;border:none class=itemLeft>单纯</div></div><div class="characterList u-f"><div style=visibility:hidden;border:none class=itemLeft>异想天开</div><div class=itemRight>异想天开</div></div><div class="characterList u-f"><div class=itemLeft>单纯</div><div style=visibility:hidden;border:none class=itemLeft>单纯</div></div><div class="characterList u-f"><div style=visibility:hidden;border:none class=itemLeft>异想天开</div><div class=itemRight>异想天开</div></div><div class="characterList u-f"><div class=itemLeft>单纯</div><div style=visibility:hidden;border:none class=itemLeft>单纯</div></div><div class="characterList u-f"><div style=visibility:hidden;border:none class=itemLeft>异想天开</div><div class=itemRight>异想天开</div></div><div class=characterImg><img class=img src=https://ec.wexiang.vip/source/img1/47.png alt=""><p class=characterText>特性</p></div></div><div class=talentSkill><div class=u-f><div class=talent><img class=img src=https://ec.wexiang.vip/source/img1/52.png alt=""><p>天赋</p></div><div class="talentList u-f"><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div></div></div><div class=u-f style=margin-top:30px><div class=talent><img class=img src=https://ec.wexiang.vip/source/img1/56.png alt=""><p>天赋</p></div><div class="talentList u-f"><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div><div class=talentItem><img class=img src=https://ec.wexiang.vip/source/img1/53.png alt=""><p class=talentItemName>眼疾手快</p></div></div></div></div></div><div class="bottom u-f-jsb"><div class=u-f><div class=designation><img class="bjcImg img" src=https://ec.wexiang.vip/source/img1/48.png alt=""><div class="u-f po u-f-item"><div style=width:17px;height:19px><img class=img src=https://ec.wexiang.vip/source/img1/49.png alt=""></div><div class=designationNmae>称号</div></div><div class=designationContent><div class="designationContentImg u-f-justify"><img class=img src=https://ec.wexiang.vip/source/img1/50.png alt=""><p>真·魔王</p></div><div class="designationContentItemImg u-f-justify"><img class=img src=https://ec.wexiang.vip/source/img1/55.png alt=""><p>哥布林之王</p></div><div class="designationContentItemImg u-f-justify"><img class=img src=https://ec.wexiang.vip/source/img1/55.png alt=""><p>哥布林之王</p></div><div class="designationContentItemImg u-f-justify"><img class=img src=https://ec.wexiang.vip/source/img1/55.png alt=""><p>哥布林之王</p></div></div></div><div class=course><img class="bjcImg img" src=https://ec.wexiang.vip/source/img1/57.png alt=""><div class="u-f po u-f-item"><div style=width:17px;height:19px><img class=img src=https://ec.wexiang.vip/source/img1/49.png alt=""></div><div class=designationNmae>历程</div></div><div class=courseText>拥有天然卷白发（动漫版为银发）、红瞳（漫画刚开始连载为蓝瞳，后改为红瞳）和死鱼般的眼睛。 衣着一般是黑色衣裤外罩配白底蓝花和服（共有四件，每天轮换着穿）， 腰上永远别着写有洞爷湖字样的木刀（只有一次没有，是被神乐偷拿走。 拥有天然卷白发（动漫版为银发）、红瞳（漫画刚开始连载为蓝瞳，后改为红瞳）和死鱼般的眼睛。 衣着一般是黑色衣裤外罩配白底蓝花和服（共有四件，每天轮换着穿）， 腰上永远别着写有洞爷湖字样的木刀（只有一次没有，是被神乐偷拿走。</div></div></div><div class=probability><ul class=probabilityList><li class="u-f-jsb probabilityListItem"><div class=u-f-item><p class=character>◆</p><p class=probabilityListItemName>史莱姆亚人</p></div><p class=probabilityListItemNub>0.001%</p></li><li class="u-f-jsb probabilityListItem"><div class=u-f-item><p class=character>◆</p><p class=probabilityListItemName>史莱姆亚人</p></div><p class=probabilityListItemNub>0.001%</p></li><li class="u-f-jsb probabilityListItem"><div class=u-f-item><p class=character>◆</p><p class=probabilityListItemName>史莱姆亚人</p></div><p class=probabilityListItemNub>0.001%</p></li><li class="u-f-jsb probabilityListItem"><div class=u-f-item><p class=character>◆</p><p class=probabilityListItemName>史莱姆亚人</p></div><p class=probabilityListItemNub>0.001%</p></li></ul></div></div></div></div><style>.title{font-size:60px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;padding-bottom:40px;text-shadow:.2rem 0 .5rem #6865EE,-.2rem 0 .5rem #6865EE,0 .2rem .5rem #6865EE,0 -.2rem .5rem #6865EE;z-index:9999;position:absolute;width:2000px;top:89px;left:114px}.wire{height:2px;background-image:linear-gradient(to right,#6865EE,#1B175A);position:relative;top:40px}.u-f,.u-f-item,.u-f-justify,.u-f-right,.u-f-bottom,.u-f-end,.u-f-jsb{display:flex}.u-f-bottom{align-items:flex-end}.u-f-item,.u-f-justify,.u-f-right{align-items:center}.u-f-justify{justify-content:center}.u-f-right{justify-content:flex-end}.u-f-around{justify-content:space-around}.u-f-jsb{justify-content:space-between}.u-f-end{justify-content:flex-end}.u-f-column{flex-direction:column}.u-f-wrap{flex-wrap:wrap}.img{width:100%;height:100%}.image{width:100%;height:100%;border-radius:50%}.cursor{cursor:pointer}.singleLine{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.multipleLines{overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-box-orient:vertical}.mt10{margin-top:10px}.loading{height:280px}.loading p{color:#fff;font-size:24px;margin-top:10px}.information{background:#000;height:calc(100vh - 120px);position:relative;overflow:hidden;z-index:999;padding:103px 190px 0 111px}.information .content{margin-top:98px}.information .content .top .figure{margin-top:13px}.information .content .top .figure .figureName{font-size:40px;font-family:Source Han Serif CN;font-weight:800;color:#FFF}.information .content .top .figure .figureNameEng{font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#B3B2FC;margin-top:11px}.information .content .top .topRight{width:566px;height:153px;position:relative}.information .content .top .topRight img{position:absolute}.information .content .top .topRight .topRightContent{position:absolute;top:29px;left:157px}.information .content .top .topRight .topRightContent .topRightContentName1{font-size:36px;font-family:Source Han Serif CN;font-weight:800;color:#FFF}.information .content .top .topRight .topRightContent .topRightContentName2{font-size:24px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;margin-top:30px}.information .content .cength .character .characterList .itemLeft{font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;border-bottom:3px solid #2D2282;border-right:3px solid #2D2282;padding:8px 30px 8px 8px;display:inline-block}.information .content .cength .character .characterList .itemRight{font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;border-left:3px solid #2D2282;border-bottom:3px solid #2D2282;padding:8px 8px 8px 30px;display:inline-block}.information .content .cength .character .characterImg{position:relative;width:58px;height:58px}.information .content .cength .character .characterImg .characterText{font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;text-align:center;line-height:58px;position:relative}.information .content .cength .character .characterImg img{position:absolute}.information .content .cength .talentSkill{padding-top:143px;padding-right:80px}.information .content .cength .talentSkill .talent{width:72px;height:113px;position:relative}.information .content .cength .talentSkill .talent img{position:absolute}.information .content .cength .talentSkill .talent p{position:relative;font-size:16px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;left:18px;top:84px}.information .content .cength .talentSkill .talentList{border-left:1px solid #524F7E;margin-left:16px;padding:0 13px;height:99px}.information .content .cength .talentSkill .talentList .talentItem{margin-right:13px}.information .content .cength .talentSkill .talentList .talentItem img{width:79px;height:83px}.information .content .cength .talentSkill .talentList .talentItem .talentItemName{font-size:16px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;text-align:center;margin-top:7px}.information .content .bottom{margin-top:17px}.information .content .bottom .designation{padding:15px 0 0 30px;width:328px;height:244px;position:relative}.information .content .bottom .designation .po{position:relative}.information .content .bottom .designation .bjcImg{position:absolute;top:0;left:0}.information .content .bottom .designation .designationNmae{position:relative;font-size:26px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;margin-left:9px}.information .content .bottom .designation .designationContent{margin:38px auto 0}.information .content .bottom .designation .designationContent .designationContentImg{width:299px;height:54px;position:relative;font-size:32px;font-family:Source Han Serif CN;font-weight:800;color:#FFF}.information .content .bottom .designation .designationContent .designationContentImg img{position:absolute;top:0}.information .content .bottom .designation .designationContent .designationContentImg p{position:relative;text-shadow:.2rem 0 .5rem #6865EE,-.2rem 0 .5rem #6865EE,0 .2rem .5rem #6865EE,0 -.2rem .5rem #6865EE}.information .content .bottom .designation .designationContent .designationContentItemImg{width:216px;height:4px;margin-top:28px;position:relative;margin-left:38px}.information .content .bottom .designation .designationContent .designationContentItemImg img{position:absolute;top:0}.information .content .bottom .designation .designationContent .designationContentItemImg p{position:relative;font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF}.information .content .bottom .probabilityList::-webkit-scrollbar{width:10px}.information .content .bottom .probabilityList::-webkit-scrollbar-track{border-radius:10px}.information .content .bottom .probabilityList::-webkit-scrollbar-thumb{background:#414090;border-radius:10px}.information .content .bottom .course{padding:15px 0 0 30px;width:469px;height:244px;position:relative;margin-left:16px}.information .content .bottom .course .bjcImg{position:absolute;top:0;left:0}.information .content .bottom .course .po{position:relative}.information .content .bottom .course .designationNmae{position:relative;font-size:26px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;margin-left:9px}.information .content .bottom .course .courseText{width:420px;padding-right:30px;height:171px;font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF;position:relative;margin-top:33px;line-height:30px;overflow:hidden;overflow-y:scroll}.information .content .bottom .course .courseText::-webkit-scrollbar{width:10px}.information .content .bottom .course .courseText::-webkit-scrollbar-track{border-radius:10px}.information .content .bottom .course .courseText::-webkit-scrollbar-thumb{background:#414090;border-radius:10px}.information .content .bottom .probability{width:385px;height:279px;padding:28px 24px 15px 24px;right:134px;position:relative;background-image:url(https://ec.wexiang.vip/source/img1/54.png)}.information .content .bottom .probability .probabilityList{width:100%;height:100%;overflow:hidden;overflow-y:scroll;position:relative}.information .content .bottom .probability .probabilityList .probabilityListItem{margin-bottom:20px;padding:0 50px 0 20px}.information .content .bottom .probability .probabilityList .probabilityListItem .character{font-size:18px;color:#fff;margin-right:11px}.information .content .bottom .probability .probabilityList .probabilityListItem .probabilityListItemName{font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF}.information .content .bottom .probability .probabilityList .probabilityListItem .probabilityListItemNub{font-size:18px;font-family:Source Han Serif CN;font-weight:800;color:#FFF}*{margin:0;padding:0}li{list-style:none}img{vertical-align:top;border:none}body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,fieldset,lengend,button,input,textarea,th,td{margin:0;padding:0}body,button,input,select,textarea{font:12px/1 Tahoma,Helvetica,Arial,"\5b8b\4f53",sans-serif}h1{font-size:18px}h2{font-size:16px}h3{font-size:14px}h4,h5,h6{font-size:100%}address,cite,dfn,em,var{font-style:normal}code,kbd,pre,samp,tt{font-family:"Courier New",Courier,monospace}small{font-size:12px}ul,ol{list-style:none}a{text-decoration:none}a:hover{text-decoration:underline}abbr[title],acronym[title]{border-bottom:1px dotted;cursor:help}button,input,select,textarea{font-size:100%}table{border-collapse:collapse;border-spacing:0}</style>';

        //设置pdf的尺寸
        // $height = 1440;
        // $width = 2560;
        // $img_file = 'http://'.$_SERVER['HTTP_HOST'].'/information.html';
        // dump($img_file);die;
        // file_put_contents('new.html', $html);
        // dump($pdf_name);die;
        // html2image($html,'new.png',$width,$height);

        // $image = imagecreate($width, $height);
        // $white = imagecolorallocate($image, 255, 255, 255);//填充背景颜色

        // $red = imagecolorallocate($image, 113, 30, 33);
        // imagestring($image,14,10,10,$html,$red);
        // $res = imagepng($image,'new.png');
        // dump($res);die;

        //批量修改数据
        /*修改画册所属*/
        // $userModel = new \app\common\model\User();
        // $user_ids = $userModel->where('id','<',58)->column('id');
        // dump($user_ids[$id_key]);

        // $articleModel = new \app\common\model\Article();
        // $musicModel = new \app\common\model\Files();
        // $gameModel = new \app\common\model\Game();
        // $imgModel = new \app\common\model\Pictures();
        // $workModel = new \app\common\model\UserWorks();

        // $list = $imgModel->select();
        // foreach ($list as $key => $value) {
            // $code = makeCode();
            // $imgModel->where('id',$value['id'])->update(['code' => $code]);
            // if(empty($value['user_id'])){
                // $id_key = array_rand($user_ids);
                // $imgModel->where('id',$value['id'])->update(['user_id' => $user_ids[$id_key]]);
            // }
        // }

        // die;
        // 文章
        // $art_list = $articleModel->select();
        // foreach ($art_list as $key => $value) {
            // $code = makeCode();
            // $articleModel->where('id',$value['id'])->update(['code' => $code]);
        //     $r_1 = $workModel->where(['obj_id' => $value['id'],'type' => 1])->find();
        //     if(!$r_1){
        //         $data1 = [
        //             'obj_id' => $value['id'],
        //             'type' => 1,
        //             'ctime' => time(),
        //             'is_market' => $value['is_market'],
        //             'user_id' => empty($value['user_id']) ? 0 : $value['user_id'],
        //             'is_draft' => 2
        //         ];

        //         $workModel->insert($data1);
        //     }
        // }

        // 音频
        // $music_list = $musicModel->select();
        // foreach ($music_list as $key1 => $value1) {
            // $code = makeCode();
            // $musicModel->where('id',$value1['id'])->update(['code' => $code]);
        //     $r_2 = $workModel->where(['obj_id' => $value1['id'],'type' => 2])->find();
        //     if(!$r_2){
        //         $data2 = [
        //             'obj_id' => $value1['id'],
        //             'type' => 2,
        //             'ctime' => time(),
        //             'is_market' => $value1['is_market'],
        //             'user_id' => empty($value1['user_id']) ? 0 : $value1['user_id'],
        //             'is_draft' => 2
        //         ];

        //         $workModel->insert($data2);
        //     }
        // }

        // 游戏
        // $game_list = $gameModel->select();
        // foreach ($game_list as $key2 => $value2) {
            // $code = makeCode();
            // $gameModel->where('g_id',$value2['g_id'])->update(['code' => $code]);
        //     $r_3 = $workModel->where(['obj_id' => $value2['g_id'],'type' => 3])->find();
        //     if(!$r_3){
        //         $data3 = [
        //             'obj_id' => $value2['g_id'],
        //             'type' => 3,
        //             'ctime' => time(),
        //             'is_market' => $value2['is_market'],
        //             'user_id' => empty($value2['user_id']) ? 0 : $value2['user_id'],
        //             'is_draft' => 2
        //         ];

        //         $workModel->insert($data3);
        //     }
        // }

        // // 插画
        // $img_list = $imgModel->select();
        // foreach ($img_list as $key3 => $value3) {
        //     $r_4 = $workModel->where(['obj_id' => $value3['id'],'type' => 4])->find();
        //     if(!$r_4){
        //         $data4 = [
        //             'obj_id' => $value3['id'],
        //             'type' => 4,
        //             'ctime' => time(),
        //             'is_market' => $value3['is_market'],
        //             'user_id' => empty($value3['user_id']) ? 0 : $value3['user_id'],
        //             'is_draft' => 2
        //         ];

        //         $workModel->insert($data4);
        //     }
        // }
        // 0x6171238faa84cb9b938900acce9ff487a8757da9a906ef89c800c2d38c6240
        // d320b1abe3b087afac8be6ed09db2c8bf19ae9388fbcda8b35f93e766a8abd5f
        // die;
        // return ['status'=>true,'msg' => '','data' => $html];
        // $signVerify = new EthSign();
        // $msg = 'Signning in to 192.168.0.25at1662522600';
        // $address = "0x7B56C1B90676b493CedB556129724f7Ca5b0dB08";//钱包地址
        // $sign = '0x841bf1fb4e9b146e71f3a0443fc4bce8cc6deecb571ee8048099d9a8b2c332ae1893adbe17fd3216c84302200368b0a97952295f3f7874c27c5ae50bcd0de71c1b';
        // $sign="0xb319edba9d9d8c12a83d3a6e2a072fa5813dd8a5e9460ac3a475ab40a9988ddd059eed15f8c748a2b18c303b2a290a40c464b0a72274dfe1a96820bb02617b4f1b";
        // $msg="123456";
        // $address="0xc9fa719138a0d8fec944ed2bdc6d191e3ef08721";
        // $result = $signVerify->verify($msg, $sign, $address);
        // dump($result);
        // $str_url = '/brandDirectSelling';
        // $config_page = config('front.');
        
        // foreach ($config_page as $key => $value) {
        //     if($str_url == $value['url']){

        //     }
        // }
    }

    //插件配置列表，插件是否开启，也通过此接口判断
    public function addons(){
        $result = [
            'status' => true,
            'data' => [],
            'msg' => ''
        ];
        $obj = new \stdClass;
        Hook('apiAddonsConf', $obj);
        $result['data'] = $obj->data;
        return $result;
    }

    // 依据类型获取数据
    public function getDataByType()
    {
        $type = input('type');//专辑类型:1文章2音频3游戏4插画

        $page = input('page',1);
        $limit = input('limit',10);
        $cate_id = input('cate_id','');
        $token = input('token');
        $user_id = getUserIdByToken($token);

        $no_id_arr = input('select_ids',[]);//选中的id,排除
        $no_ids = [];

        if(!empty($no_id_arr)){
            foreach ($no_id_arr as $key => $value) {
                $no_ids[] = intval($value['id']);
            }
           
        }
       
        $article = new \app\common\model\Article();
        $files = new \app\common\model\Files();
        $pictures = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();
        $works = new \app\common\model\UserWorks();
        // $my_ids = $works->where('user_id',$user_id)->where('type',$type)->column('obj_id');
        $other_ids = $works->where('user_id','neq',$user_id)->where('type',$type)->column('obj_id');
        $my_ids = $works->where('type',$type)->whereIn('is_draft',[0,1,3])->column('obj_id');
        $_ids = array_merge($my_ids,$other_ids);
        
        $no_ids = array_merge($_ids,$no_ids);
        
        // if(!empty($my_ids)){
            // $no_ids = $other_ids+$no_ids;
        // }
       
        // $no_ids = getArrCommon($no_ids);
        // dump($no_ids);die;

        // if(empty($no_ids)){
        //     return ['status' => true,'data' => ['list' => [],'count' => 0,'page' => $page,'limit' => $limit]];
        // }

        if($type == 1){
            if(!empty($cate_id)){
                if(!empty($no_ids)){
                    
                    $list = $article->field('id,title as name,cover,code,price,file_url')->whereNotIn('id',$no_ids)->where('type_id',$cate_id)->order('sort asc')->page($page,$limit)->select();
                    
                    $count = $article->whereNotIn('id',$no_ids)->whereIn('type_id',$cate_id)->count();
                }else{
                    $list = [];
                    $count = 0;
                }
            }else{
                if(!empty($no_ids)){
                    $list = $article->field('id,title as name,cover,code,price,file_url')->whereNotIn('id',$no_ids)->where('type_id','eq',0)->order('sort asc')->page($page,$limit)->select();
                    
                    $count = $article->whereNotIn('id',$no_ids)->where('type_id','eq',0)->count();
                }else{
                    $list = [];
                    $count = 0;
                }
            }
            
 
        }elseif($type == 2){
            if(!empty($cate_id)){
                if(!empty($no_ids)){
                    $list = $files->field('id,name,path as cover,code,price,url as file_url')->whereNotIn('id',$no_ids)->where('cat_id',$cate_id)->order('sort asc')->page($page,$limit)->select();
                    $count = $files->whereNotIn('id',$no_ids)->where('cat_id',$cate_id)->count();
                
                }else{
                    $list = [];
                    $count = 0;
                }

            }else{
                if(!empty($no_ids)){
                    $list = $files->field('id,name,path as cover,code,price,url as file_url')->whereNotIn('id',$no_ids)->where('cat_id',0)->order('sort asc')->page($page,$limit)->select();
                    $count = $files->whereNotIn('id',$no_ids)->where('cat_id',0)->count();
                
                }else{
                    $list = [];
                    $count = 0;
                }
            }

        }elseif($type == 3){
            if(!empty($cate_id)){
                if(!empty($no_ids)){
                    $list = $game->field('g_id as id,game_name as name,cover,code,price')->whereNotIn('g_id',$no_ids)->where('cate_id',$cate_id)->order('sort asc')->page($page,$limit)->select();
                    foreach ($list as $k => $v) {
                        $list[$k]['file_url'] = '';
                    }
                    $count = $game->whereNotIn('g_id',$no_ids)->where('cate_id',$cate_id)->count();
                }else{
                    $list = [];
                    $count = 0;
                }
            }else{
                if(!empty($no_ids)){
                    $list = $game->field('g_id as id,game_name as name,cover,code,price')->whereNotIn('g_id',$no_ids)->where('cate_id',0)->order('sort asc')->page($page,$limit)->select();
                    foreach ($list as $k => $v) {
                        $list[$k]['file_url'] = '';
                    }
                    $count = $game->whereNotIn('g_id',$no_ids)->where('cate_id',0)->count();
                }else{
                    $list = [];
                    $count = 0;
                }
            }
            

        }else{
            if(!empty($cate_id)){
                if(!empty($no_ids)){
                    $list = $pictures->field('id,name,cover,code,price,path as file_url')->whereNotIn('id',$no_ids)->where('cate_id',$cate_id)->order('sort asc')->page($page,$limit)->select();
                    $count = $pictures->whereNotIn('id',$no_ids)->where('cate_id',$cate_id)->count();
                }else{
                    $list = [];
                    $count = 0;
                }

            }else{
                if(!empty($no_ids)){
                    $list = $pictures->field('id,name,cover,code,price,path as file_url')->whereNotIn('id',$no_ids)->where('cate_id',0)->order('sort asc')->page($page,$limit)->select();
                    $count = $pictures->whereNotIn('id',$no_ids)->where('cate_id',0)->count();
                }else{
                    $list = [];
                    $count = 0;
                }
            }
            
        }

        if($list){
            foreach ($list as $key => $value) {
                $list[$key]['cover'] = _sImage($value['cover']).'?x-oss-process=image/resize,w_188,m_lfit/format,webp';
                if(!empty($value['file_url'])){
                    $list[$key]['file_url'] = _sImage($value['file_url']);
                }
            }
        }

        return ['status' => true,'data' => ['list' => $list,'count' => $count,'page' => $page,'limit' => $limit]];
    }

    // 卡券详情
    public function cardInfo()
    {
        $id = input('id');

        if(empty($id)){
            return error_code(10002);
        }
        $card_config = getMultipleSetting('card_img,card_price');

        $userCardModel = new \app\common\model\UserCard();
        $cardInfo = $userCardModel->where('id',$id)->find();

        $cardInfo['id'] = $cardInfo['id'];
        $cardInfo['price'] = $card_config['card_price'];
        $cardInfo['name'] = '卡券';
        $cardInfo['is_market'] = 0;
        $cardInfo['type'] = 6;
        $cardInfo['sn'] = $cardInfo['code'];
        $cardInfo['image_url'] = _sImage($card_config['card_img']);
        $cardInfo['chain'] = getOtherDetail($cardInfo['id'],6);

        return ['status' => true,'msg' => '','data' => $cardInfo];

    }

    public function test2()
    {
        $book = "https://pzss.oss-cn-guangzhou.aliyuncs.com/static/uploads/book/2022/08/18/166081234662fdfc3aab573.md";
        $res = file_get_contents($book);
        $str = explode('回 ',$res);
        // $arr = [];

        for ($i=1; $i < count($str); $i++) {
            $s = explode(PHP_EOL, $str[$i]);
            $a = $s[0];//章节
            unset($s[0]);
            $b = $s;
            $c = $b[count($b)];
            unset($b[count($b)]);
            // dump($s[0]);
            dump($a);//内容
            // dump(explode('第',$c)[1]);//第几回
            // $ss = explode('第', $str[$i]);
            // if($ss){
                // dump(substr($ss[0], 0,3*16-3));
                // $a = explode(PHP_EOL, $ss[0]);
                // dump($a);
            // }

        }die;
        // explode('', string)
        // dump(getFromStr('第','回',$res));die;
        // $data = explode('**', $res);

        // unset($data[0]);
        // unset($data[1]);
        // unset($data[2]);
        // unset($data[3]);
        // unset($data[4]);

        // foreach ($data as $key => $value) {
            // dump($data);
        // }die;
        // $apikeyToken = 'PGB1W72547QD8UWY4FWV1MDXM9DPCIJMJD';
        // $address = '0x036ab762f087985d44fab83715576a2e15358e5f';//'0x39a8443a6a6917371565c410c133133201d3a843';

        // $url = "https://api.etherscan.io/api?module=account&action=balance&address=$address&tag=latest&apikey=$apikeyToken";
        // dump($url);die;
        // $curl = new Curl();
        // $res = $curl::get($url);
        // dump(file_get_contents($url));die;
    }

    // unity登陆
    public function userLogin()
    {
        $acount = input('acount');
        $password = input('password');

        $user = new \app\common\model\User;

        if(empty($acount) || empty($password)){
            return error_code(10003);
        }

        $userInfo = $user->where('mobile',$acount)->whereOr('email',$acount)->find();

        if(empty($userInfo)){
            return error_code(11004);
        }else{
            $pwd = $user->enPassword($password,$userInfo['ctime']);
            $userLastInfo = $user->where('id',$userInfo['id'])->where('password',$pwd)->find();

            if($userLastInfo){
                return $user->setSession($userLastInfo,2,5);
            
            }else{
                return error_code(11033);
            }
        }

    }

    // 拍卖到期作品下架定时检查任务
    public function checkSaleTime()
    {
        $sale = new \app\common\model\UserSale();
        $article = new \app\common\model\Article();
        $music = new \app\common\model\Files();
        $img = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();
        $price = new \app\common\model\UserPrice();
        $business = new \app\common\model\Business();

        $now_time = time();
        $over_work = $sale->where('end_time','elt',$now_time)->select();

        if(!empty($over_work)){
            foreach ($over_work as $key => $value) {
                // 拍卖结束，自动成交
                $price_info = $price->where('p_id' , $value['obj_id'])->where('type',$value['obj_type'])->where('e_time','elt',$now_time)->order('price desc')->find();
                if(!empty($price_info)){
                    $business->agreePrice($price_info['id'],$price_info['receive_id']);
                    $sale->where('id',$value['id'])->update(['status' => 1]);
                }else{
                    $sale->where('id',$value['id'])->update(['status' => 2]);
                }

                // if($value['obj_type'] == 1){
                //     // 文章
                //     $article->where('id',$value['obj_id'])->update(['is_market' => 0,'price' => 0]);
                
                // }elseif($value['obj_type'] == 2){
                //     // 音频
                //     $music->where('id',$value['obj_id'])->update(['is_market' => 0,'price' => 0]);
               
                // }elseif($value['obj_type'] == 3){
                //     // 游戏
                //     $game->where('g_id',$value['obj_id'])->update(['is_market' => 0,'price' => 0]);
                
                // }else{
                //     // 插画
                //     $img->where('id',$value['obj_id'])->update(['is_market' => 0,'price' => 0]);
                // }

                
            }
        }


    }

    /*数据埋点*/
    // 用户页面访问记录
    public function saveUserAct()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);
        $mining_mod = new \app\common\model\MiningLog();
        $mining_mod->addUserMining($user_id);

        $res = saveUserActions($user_id,input('page_url'),input('action_id'),input('action_name'));

        return $res ? ['status' => true,'msg' => '记录成功'] : ['status' => false,'msg' => '记录失败']; 
    }

    // 用户页面访问时长
    public function saveUserTime()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);
        // file_put_contents('new.txt', '结束时间:'.input('leave_time').'开始时间:'.input('enter_time').'<br>',FILE_APPEND);
        $res = saveUserTimes($user_id,input('page_url'),input('enter_time'),input('leave_time'));

        return $res ? ['status' => true,'msg' => '记录成功'] : ['status' => false,'msg' => '记录失败']; 
    }

    // 交易活动记录
    public function businessList()
    {
        $page = input('page',1);
        $limit = input('limit',10);
        $id = input('id');
        $type = input('type',1);

        $sale_log_mod = new \app\common\model\NftSaleHistory();
        
        $list = $sale_log_mod->where(['nft_id' => $id,'type' => $type])->order('id desc')->page($page,$limit)->select();
        if($list){
            foreach ($list as $key => $value) {
                $list[$key]['ctime'] = date('Y-m-d',$value['ctime']);
                $list[$key]['fromAddress'] = formatWallet($value['fromAddress']);
                $list[$key]['toAddress'] = formatWallet($value['toAddress']);
                $list[$key]['tokenId'] = formatWallet($value['tokenId']);
            }
        }

        $count = $sale_log_mod->where(['nft_id' => $id,'type' => $type])->count();

        return ['status' => true,'msg' => '','data' => $list,'count' => $count];
    }

    // 读取菜单
    public function getMenuList()
    {
        $menu_mod = new \app\common\model\FrontMenu();

        $list = $menu_mod->where(['pid' => 0,'status' => 1])->order('sort asc')->select();
        $new_arr = [
            [
                'icon' => 'https://ec.wexiang.vip/source/img/4.png',
                'url' => '/'
            ]
        ];

        if($list){
            foreach ($list as $key => $value) {
                $temp = [];
                $temp['icon'] = _sImage($value['menu_img']);
                if(getLng() == 'en'){
                    $temp['name'] = $value['menu_eng_name'];
                }else{
                    $temp['name'] = $value['menu_name'];
                }
                
                $temp['url'] = $value['path'];

                $menu_child = $menu_mod->where(['pid' => $value['id'],'status' => 1])->order('sort asc')->select();
                if(isset($menu_child[0])){
                    $temp['falg'] = true;
                    foreach ($menu_child as $ck => $cv) {
                        $temp['list'][] = [
                            'name' => getLng() == 'en' ? $cv['menu_eng_name'] : $cv['menu_name'],
                            'url' => $cv['path']
                        ];

                    }

                }else{
                    $temp['falg'] = false;
                }

                array_push($new_arr, $temp);
            }

        }

        return ['status' => true,'msg' => '','data' => $new_arr];
    }

    // 关于我们
    public function aboutUs()
    {
        $set_mod = new \app\common\model\Document();
        $id = input('id');

        $info = $set_mod->where('id',$id)->find();
        if(getLng() == 'en'){
            $info['title'] = $info['title_eng'];
        }
        
        return ['status' => true,'msg' => '','data' => $info];
    }

    // 数据分析
    public function dataSync()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);
        $page = input('page', 1);//页码
        $limit = input('limit', 10);//每页条数
        $type = input('type', 1);//1.内容体验2.数字藏品3.数字版权
        $start_at = input('start','2022-11-10');//开始时间
        $end_at = input('end','2022-11-30');//结束时间

        // if(empty($start_at) || empty($end_at)){
        //     return error_code(15002);
        // }

        if($end_at <= $start_at){
            return error_code(10078);
        }

        $buy_mod = new \app\common\model\BuyLog();

        $date_from = strtotime($start_at);
        $date_to = strtotime($end_at);
        $data = [];
        $total = 0;

        for ($i=$date_from; $i <= $date_to; $i+=86400) {
            $income = $buy_mod->where('worker_id',$user_id)->where('ctime','between time',[date('Y-m-d',$i),date('Y-m-d',$i+86400)])->where('type',$type)->sum('income');
            $works_fee = $buy_mod->where('worker_id',$user_id)->where('ctime','between time',[date('Y-m-d',$i),date('Y-m-d',$i+86400)])->where('type',$type)->sum('works_fee');


            $data['series'][] = $income+$works_fee;
            $data['xAxis'][] = date('m-d',$i);
            $total += $income+$works_fee;
        }
        
        $data['total'] = $total;

        $list['list'] = $buy_mod->field('obj_id,obj_type,price,serice_fee,copy_fee,income,ctime,works_fee')->where('worker_id',$user_id)->where('ctime','between time',[$start_at,$end_at])->where('type',$type)->page($page,$limit)->select();

        $list['count'] = $buy_mod->where('worker_id',$user_id)->where('ctime','between time',[$start_at,$end_at])->where('type',$type)->count();
        
        if($list['list']){
            $articleModel = new \app\common\model\Article();
            $musicModel = new \app\common\model\Files();
            $gameModel = new \app\common\model\Game();
            $imgModel = new \app\common\model\Pictures();

            foreach ($list['list'] as $key => $value) {
                if($value['obj_type'] == 1){
                    // 文章
                    $art = $articleModel->where('id',$value['obj_id'])->find();
                    $list['list'][$key]['cover'] = _sImage($art['cover']);
                    $list['list'][$key]['title'] = $art['title'];
                    $list['list'][$key]['code'] = $art['code'];
                }elseif($value['obj_type'] == 2){
                    // 音频
                    $file = $musicModel->where('id',$value['obj_id'])->find();
                    $list['list'][$key]['cover'] = _sImage($file['path']);
                    $list['list'][$key]['title'] = $file['name'];
                    $list['list'][$key]['code'] = $file['code'];
                }elseif($value['obj_type'] == 3){
                    // 游戏
                    $game = $gameModel->where('g_id',$value['obj_id'])->find();
                    $list['list'][$key]['cover'] = _sImage($game['cover']);
                    $list['list'][$key]['title'] = $game['game_name'];
                    $list['list'][$key]['code'] = $game['code'];
                }elseif($value['obj_type'] == 4){
                    $img = $imgModel->where('id',$value['obj_id'])->find();
                    $list['list'][$key]['cover'] = _sImage($img['cover']);
                    $list['list'][$key]['title'] = $img['name'];
                    $list['list'][$key]['code'] = $img['code'];
                }else{

                }

                $list['list'][$key]['ctime'] = date('Y-m-d H:i:s',$value['ctime']);
            }
        }


        // $data['series'] = [1,2,3,4,5,6,7];
        // $data['xAxis'] = ['9-10', '9-15', '9-20', '9-25', '9-30', '10-05', '10-10'];

        return ['status' => true,'msg' => '','data' => $data,'list' => $list];
    }

    // 代币实时换算
    public function binConvert()
    {
        $bin_num = input('price',1);//币

        $curl = new Curl();
        $binUrl = 'https://x.szsing.com/v2/quote/price/m_rate';
        $res = $curl::get($binUrl);
        $result = json_decode($res);
        $bin_data = $result->data->binance;
        $data = $bin_num/$bin_data->ETH;

        return ['status' => true,'msg' => '','data' => number_format($data,2)];
    }

    // 获取页脚
    public function getPageLinks()
    {
        $links = new \app\common\model\Links();

        $data = $links->getLinksList();

        return ['status' => true,'msg' => '','data' => $data];
    }

    //设置默克尔树
    public function setMerkleRoot()
    {
        // $token = input('token');
        // $user_id = getUserIdByToken($token);

        $root = file_get_contents("php://input");
        $root_json = json_decode($root);
        return setMerkleRoot($root_json->root);
    }

    // 获取素材 unity端
    public function sourceList()
    {
        $token = input('token');
        $user_id = getUserIdByToken($token);
        $page = input('page', 1);//页码
        $limit = input('limit', 10);//每页条数
        $type = input('type', 1);//类型:1小说2插画3立绘4头像5背景6角色配音7背景音乐8环境音效9角色

        $true_type = 0;

        if(in_array($type, [1])){
            $parent_type = 1;
        
        }elseif(in_array($type, [2,3,4,5])){
            $parent_type = 4;
        
        }elseif(in_array($type, [6,7,8])){
            $parent_type = 2;
        }else{
            $parent_type = 5;
        }   
        

        $article = new \app\common\model\Article();
        $articleChapter = new \app\common\model\ArticleChapter();
        $music = new \app\common\model\Files();
        $img = new \app\common\model\Pictures();
        $role = new \app\common\model\Goods();
        $game = new \app\common\model\Game();
        $user = new \app\common\model\User();
        $draw_mod = new \app\common\model\DrawLog();
        $extend_mod = new \app\common\model\GoodsExtend();

        $user_info = $user->where('id',$user_id)->find();
        if(empty($user_info)){
            return error_code(11004);
        }

        $workModel = new \app\common\model\UserWorks();

        $map = [];
        
        if($parent_type == 1){
            // 小说
            $work_ids = $workModel->where(['is_draft'=>2,'type' => 1])->column('obj_id');
            if(!empty($work_ids)){
                $map[] = ['id','in',$work_ids];
            }
            $list = $article->field('id,title as name,cover,code,file_url as url')->where($map)->order('id desc')->page($page,$limit)->select();
            foreach ($list as $key => $value) {
                $list[$key]['cover'] = _sImage($value['cover']).addImageSuffix(7); 
                $list[$key]['type'] = $type;
                $list[$key]['chapter'] = $articleChapter->field('url,name,index')->where('article_id',$value['id'])->select();   
            }

            $total = $article->where($map)->count();
        
        }elseif($parent_type == 2){
            // 音频
            $work_ids = $workModel->where(['is_draft'=>2,'type' => 2])->column('obj_id');
            if(!empty($work_ids)){
                $map[] = ['id','in',$work_ids];
            }

            if($type == 6){
                $true_type = 2;
            
            }elseif($type == 7){
                $true_type = 3;
            
            }else{
                $true_type = 4;
            }

            $map[] = ['type','eq',$true_type];

            $list = $music->field('id,name,url,path as cover,code')->where($map)->order('id desc')->page($page,$limit)->select();
            foreach ($list as $key => $value) {
                $list[$key]['cover'] = _sImage($value['cover']).addImageSuffix(6); 
                $list[$key]['type'] = $type;    
            }
            $total = $music->where($map)->count();
        
        }elseif($parent_type == 3){
            // 游戏

        }elseif($parent_type == 4){
            // 插画

            // 立绘-增加角色图片数据
            $work_ids = $workModel->where(['is_draft'=>2,'type' => 4])->column('obj_id');
            if(!empty($work_ids)){
                $map[] = ['id','in',$work_ids];
            }

            if($type == 2){
                $true_type = 1;
            
            }elseif($type == 3){
                $true_type = 2; 
            
            }elseif($type == 4){
                $true_type = 4;
            }else{
                $true_type = 3;
            }

            if($type == 3 || $type == 4){
                if($type == 4){
                    $map[] = ['type','IN',[2,4]];
                }else{
                    $map[] = ['type','eq',$true_type];
                }
                
                $list_img = [];
                $list = $img->field('id,name,cover,code,path as url')->where($map)->order('id desc')->select();

                foreach ($list as $key => $value) {
                    $list[$key]['cover'] = _sImage($value['cover']);    
                    $list[$key]['url'] = _sImage($value['cover']);  
                    $list[$key]['type'] = $type;   
                }

                for ($j=0; $j < count($list); $j++) { 
                    $list_img[] = [
                        'name' => $list[$j]['name'],
                        'cover' => $list[$j]['cover'].'?x-oss-process=image/resize,h_1920,m_lfit',
                        'code' => $list[$j]['code'],
                        'url' => $list[$j]['url'].'?x-oss-process=image/resize,h_1920,m_lfit',
                        'type' => $type,
                        'id' => $list[$j]['id']
                    ];
                }
                
                $ids = $draw_mod->column('goods_id');
                
                $temp_goods = [];
                for ($i=0; $i < count($ids); $i++) { 
                    $info = $role->field('name,bn as code')->where('id',$ids[$i])->order('id desc')->find();
                    $temp_goods[] = [
                        'name' => $info['name'],
                        'cover' => _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img')).'?x-oss-process=image/resize,h_1920,m_lfit',
                        'code' => $info['code'],
                        'url' => _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img')).'?x-oss-process=image/resize,h_1920,m_lfit',
                        'type' => $type,
                        'id' => $ids[$i]
                    ];
                    // $temp_goods[]['name'] = $info['name'];
                    // $temp_goods[$i]['code'] = $info['code'];
                    // $temp_goods[$i]['cover'] = _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img'));
                    // $temp_goods[$i]['url'] = _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img'));
                    // $temp_goods[$i]['type'] = $type;
                }

                // dump(gettype($list_img));die;
                // $new_list = [];

                // foreach ($temp_goods as $key => $value) {

                //     $new_list[] = array_push($list, $value);
                // }
                // dump($temp_goods);
                // dump($list);die;
                
                $last_list = array_merge_recursive($temp_goods,$list_img);

                $n_count = count($last_list);
                $start = ($page-1) * $limit;

                $new_data = array_slice($last_list,$start,$limit);

                return ['status' => true,'msg' => '获取成功','data' => $new_data,'count' => $n_count];
            }else{
                // 过场: 插画+背景 = 背景
                if($type == 5){
                    $map[] = ['type','IN',[1,3]];
                }else{
                    $map[] = ['type','eq',$true_type];
                }

                $list = $img->field('id,name,cover,code,path as url,type')->where($map)->order('id desc')->page($page,$limit)->select();
                foreach ($list as $key => $value) {
                    $list[$key]['cover'] = _sImage($value['cover']).'?x-oss-process=image/resize,h_1920,m_lfit';    
                    $list[$key]['url'] = _sImage($value['cover']);
                    // if($value['type'] == 1){
                    //     $list[$key]['type'] = 2;
                    // }elseif($value['type'] == 3){
                    //     $list[$key]['type'] = 5;
                    // }else{
                        $list[$key]['type'] = $type;
                    // }
                       
                }
                $total = $img->where($map)->count();
            }
            
        }else{
            // 角色
            //仅显示抽出来的角色
            // $where = [];
            
            $ids = $draw_mod->page($page,$limit)->column('goods_id');

            $nn = array_unique(array_filter($draw_mod->column('goods_id')));
            $total = $role->whereIn('id',$nn)->count();

            for ($i=0; $i < count($ids); $i++) { 
                $info = $role->field('name,bn as code')->where('id',$ids[$i])->find();
                $list[$i]['name'] = $info['name'];
                $list[$i]['code'] = $info['code'];
                $list[$i]['cover'] = _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img'));
                $list[$i]['url'] = _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img'));
                $list[$i]['type'] = $type;
                $list[$i]['id'] = $ids[$i];


                // $data = $role->getRoleDetialNew($ids[$i]);
                // if(!empty($data)){
                //     $list[] = $data['data'];
                // }
                
            }

        }

        return ['status' => true,'msg' => '获取成功','data' => $list,'count' => $total];
    }

    // 上传剧本
    public function uploadScript()
    {
        // $script = input('script','');
        $script = $_POST['script'];
        // dump($script);die;
        if(empty($script)){
            return error_code(10213);
        }

        $script_mod = new \app\common\model\Script();
        $token = input('token');
        $user_id = getUserIdByToken($token);

        $res = $script_mod->insertGetId(['script' => $script,'user_id' => $user_id]);

        return $res ? ['status' => true,'msg' => '上传成功','data' => $res] : error_code(10035);
    }

    // 根据剧本id 拉取剧本
    public function getScriptById()
    {
        $script_mod = new \app\common\model\Script();
        // $token = input('token');
        // $user_id = getUserIdByToken($token);
        $id = input('id');

        if(empty($id)){
            return error_code(10002);
        }

        $info = $script_mod->where('id',$id)->value('script');
        if(empty($info)){
            return error_code(10002);
        }

        return ['status' => true,'msg' => '获取成功','data' => $info];
    }


    // 根据资源id 拉取资源
    public function getSourceById()
    {
        $article = new \app\common\model\Article();
        $music = new \app\common\model\Files();
        $img = new \app\common\model\Pictures();
        $role = new \app\common\model\Goods();
        $game = new \app\common\model\Game();
        $articleChapter = new \app\common\model\ArticleChapter();
        $extend_mod = new \app\common\model\GoodsExtend();

        // $token = input('token');
        // $user_id = getUserIdByToken($token);
        $id = input('id','');
        $type = input('type','');//类型:1小说2插画3立绘4头像5背景6角色配音7背景音乐8环境音效9角色

        if(empty($id)){
            return error_code(10002);
        }

        if(empty($type)){
            return error_code(10003);
        }

        $true_type = 0;

        if(in_array($type, [1])){
            $parent_type = 1;
        
        }elseif(in_array($type, [2,3,4,5])){
            $parent_type = 4;
        
        }elseif(in_array($type, [6,7,8])){
            $parent_type = 2;
        }else{
            $parent_type = 5;
        }   

        if($parent_type == 1){
            // 小说
            $info = $article->field('id,title as name,cover,code,file_url as url')->where('code',$id)->find();
            // foreach ($list as $key => $value) {
                if($info){
                    $info['cover'] = _sImage($info['cover']).addImageSuffix(7); 
                    $info['type'] = $type;
                    $info['chapter'] = $articleChapter->field('url,name,index')->where('article_id',$info['id'])->select(); 
                }else{
                    return error_code(10002);
                }
                  
            // }

            // $total = $article->where($map)->count();
        
        }elseif($parent_type == 2){
            // 音频
            // $work_ids = $workModel->where(['is_draft'=>2,'type' => 2])->column('obj_id');
            // if(!empty($work_ids)){
            //     $map[] = ['id','in',$work_ids];
            // }

            // if($type == 6){
            //     $true_type = 2;
            
            // }elseif($type == 7){
            //     $true_type = 3;
            
            // }else{
            //     $true_type = 4;
            // }

            // $map[] = ['type','eq',$true_type];

            $info = $music->field('id,name,url,path as cover,code')->where('code',$id)->find();
            // foreach ($list as $key => $value) {
                if($info){
                    $info['cover'] = _sImage($info['cover']).addImageSuffix(6); 
                    $info['type'] = $type;
                }else{
                    return error_code(10002);
                }
                  
            // }
            // $total = $music->where($map)->count();
        
        }elseif($parent_type == 3){
            // 游戏

        }elseif($parent_type == 4){
            // 插画

            // 立绘-增加角色图片数据
            // $work_ids = $workModel->where(['is_draft'=>2,'type' => 4])->column('obj_id');
            // if(!empty($work_ids)){
            //     $map[] = ['id','in',$work_ids];
            // }

            // if($type == 2){
            //     $true_type = 1;
            
            // }elseif($type == 3){
            //     $true_type = 2; 
            
            // }elseif($type == 4){
            //     $true_type = 4;
            // }else{
            //     $true_type = 3;
            // }

            // $map[] = ['type','eq',$true_type];

            // if($type == 3){
            //     $list_img = [];
                if($type == 3){
                    $info = $img->field('id,name,cover,code,path as url')->where('code',$id)->find();
                    if(!$info){
                        $info = $role->field('id,name,bn as code')->where('bn',$id)->find();
                        if($info){
                            $info['name'] = $info['name'];
                            $info['code'] = $info['code'];
                            $info['cover'] = _sImage($extend_mod->where('goods_id',$info['id'])->value('role_img')).'?x-oss-process=image/resize,h_1920,m_lfit';
                            $info['url'] = _sImage($extend_mod->where('goods_id',$info['id'])->value('role_img')).'?x-oss-process=image/resize,h_1920,m_lfit';
                            $info['type'] = $type;
                            $info['id'] = $info['id'];
                        }else{
                            return error_code(10002);
                        }
                    }else{
                        $info['cover'] = _sImage($info['cover']).'?x-oss-process=image/resize,h_1920,m_lfit';    
                        $info['url'] = _sImage($info['cover']);  
                        $info['type'] = $type; 
                    }
                }else{
                    $info = $img->field('id,name,cover,code,path as url')->where('code',$id)->find();
                    if($info){
                        $info['cover'] = _sImage($info['cover']).'?x-oss-process=image/resize,h_1920,m_lfit';    
                        $info['url'] = _sImage($info['cover']);  
                        $info['type'] = $type; 
                    }else{
                        return error_code(10002);
                    }
                }
                

            //     foreach ($list as $key => $value) {
                      
            //     }

            //     for ($j=0; $j < count($list); $j++) { 
            //         $list_img[] = [
            //             'name' => $list[$j]['name'],
            //             'cover' => $list[$j]['cover'],
            //             'code' => $list[$j]['code'],
            //             'url' => $list[$j]['url'],
            //             'type' => $list[$j]['type'],
            //             'id' => $list[$j]['id']
            //         ];
            //     }
                
            //     $ids = $draw_mod->column('goods_id');
                
            //     $temp_goods = [];
            //     for ($i=0; $i < count($ids); $i++) { 
            //         $info = $role->field('name,bn as code')->where('id',$ids[$i])->find();
            //         $temp_goods[] = [
            //             'name' => $info['name'],
            //             'cover' => _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img')),
            //             'code' => $info['code'],
            //             'url' => _sImage($extend_mod->where('goods_id',$ids[$i])->value('role_img')),
            //             'type' => '9',
            //             'id' => $ids[$i]
            //         ];

            //     }
  
            //     $last_list = array_merge_recursive($temp_goods,$list_img);

            //     $n_count = count($last_list);
            //     $start = ($page-1) * $limit;

            //     $new_data = array_slice($last_list,$start,$limit);

            //     return ['status' => true,'msg' => '获取成功','data' => $new_data,'count' => $n_count];
            // // }else{
                
            //     $list = $img->field('id,name,cover,code,path as url')->where($map)->page($page,$limit)->select();
            //     foreach ($list as $key => $value) {
            //         $list[$key]['cover'] = _sImage($value['cover']).addImageSuffix(9);    
            //         $list[$key]['url'] = _sImage($value['cover']);  
            //         $list[$key]['type'] = $type;   
            //     }
            //     $total = $img->where($map)->count();
            // }
            
        }else{
            // 角色
            //仅显示抽出来的角色
            // $where = [];
            
            // $ids = $draw_mod->page($page,$limit)->column('goods_id');

            // $nn = array_unique(array_filter($draw_mod->column('goods_id')));
            // $total = $role->whereIn('id',$nn)->count();

            // for ($i=0; $i < count($ids); $i++) { 
                $info = $role->field('id,name,bn as code')->where('bn',$id)->find();
                if($info){
                    $info['name'] = $info['name'];
                    $info['code'] = $info['code'];
                    $info['cover'] = _sImage($extend_mod->where('goods_id',$info['id'])->value('role_img')).'?x-oss-process=image/resize,h_1920,m_lfit';
                    $info['url'] = _sImage($extend_mod->where('goods_id',$info['id'])->value('role_img')).'?x-oss-process=image/resize,h_1920,m_lfit';
                    $info['type'] = $type;
                    $info['id'] = $info['id'];
                }else{
                    return error_code(10002);
                }
               
                
            // }

        }

        return ['status' => true,'msg' => '获取成功','data' => $info];
    }
}