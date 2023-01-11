<?php

// +----------------------------------------------------------------------
// | 接口设置
// +----------------------------------------------------------------------

return [
    //用户
    'user' => [
        'code'   => 'User',
        'method' => [
            'login'                   => [
                'code'     => 'login',
                'is_login' => false
            ],
            'smslogin'                => [
                'code'     => 'smsLogin',
                'is_login' => false
            ],
            'bindMobile'              => [
                'code'     => 'bindMobile',
                'is_login' => true
            ],
            'wxapplogin1'             => [
                'code'     => 'wxappLogin1',
                'is_login' => false
            ],
            'wxapplogin2'             => [
                'code'     => 'wxappLogin2',
                'is_login' => false
            ],
            'alipayapplogin1'         => [
                'code'     => 'alipayappLogin1',
                'is_login' => false
            ],
            'sms'                     => [
                'code'     => 'sms',
                'is_login' => false
            ],
            'logout'                  => [
                'code'     => 'logout',
                'is_login' => true
            ],
            'reg'                     => [
                'code'     => 'reg',
                'is_login' => false
            ],
            'officiallogin'           => [
                'code'     => 'officialLogin',
                'is_login' => false
            ],
            'info'                    => [
                'code'     => 'info',
                'is_login' => false
            ],
            'changeavatar'            => [
                'code'     => 'changeAvatar',
                'is_login' => true
            ],
            'editinfo'                => [
                'code'     => 'editInfo',
                'is_login' => true
            ],
            'addgoodsbrowsing'        => [
                'code'     => 'addGoodsBrowsing',
                'is_login' => true
            ],
            'delgoodsbrowsing'        => [
                'code'     => 'delGoodsBrowsing',
                'is_login' => true
            ],
            'goodsbrowsing'           => [
                'code'     => 'goodsBrowsing',
                'is_login' => true
            ],
            'goodscollection'         => [
                'code'     => 'goodsCollection',
                'is_login' => true
            ],
            'goodscollectionlist'     => [
                'code'     => 'goodsCollectionList',
                'is_login' => true
            ],
            // 'saveusership'            => [
            //     'code'     => 'saveUserShip',
            //     'is_login' => true
            // ],
            'getshipdetail'           => [
                'code'     => 'getShipDetail',
                'is_login' => true
            ],
            'setdefship'              => [
                'code'     => 'setDefShip',
                'is_login' => true
            ],
            'editship'                => [
                'code'     => 'editShip',
                'is_login' => true
            ],
            'removeship'              => [
                'code'     => 'removeShip',
                'is_login' => true
            ],
            'getusership'             => [
                'code'     => 'getUserShip',
                'is_login' => true
            ],
            'getallname'              => [
                'code'     => 'getAllName',
                'is_login' => false
            ],
            'getareaid'               => [
                'code'     => 'getAreaId',
                'is_login' => false
            ],
            'pay'                     => [
                'code'     => 'pay',
                'is_login' => false,
            ],
            'orderevaluate'           => [
                'code'     => 'orderEvaluate',
                'is_login' => true,
            ],
            'getuserdefaultship'      => [
                'code'     => 'getUserDefaultShip',
                'is_login' => true,
            ],
            'issign'                  => [
                'code'     => 'isSign',
                'is_login' => true
            ],
            'sign'                    => [
                'code'     => 'sign',
                'is_login' => true
            ],
            'getuserpoint'            => [
                'code'     => 'getUserPoint',
                'is_login' => true
            ],
            'getsetting'              => [
                'code'     => 'getSetting',
                'is_login' => false
            ],
            'getdefaultbankcard'      => [
                'code'     => 'getDefaultBankCard',
                'is_login' => true
            ],
            'getbankcardlist'         => [
                'code'     => 'getBankCardList',
                'is_login' => true
            ],
            'addbankcard'             => [
                'code'     => 'addBankCard',
                'is_login' => true
            ],
            'removebankcard'          => [
                'code'     => 'removeBankCard',
                'is_login' => true
            ],
            'setdefaultbankcard'      => [
                'code'     => 'setDefaultBankCard',
                'is_login' => true
            ],
            'getbankcardinfo'         => [
                'code'     => 'getBankCardInfo',
                'is_login' => true
            ],
            'getbankcardorganization' => [
                'code'     => 'getBankCardOrganization',
                'is_login' => false
            ],
            'editpwd'                 => [
                'code'     => 'editPwd',
                'is_login' => false
            ],
            'forgetpwd'               => [
                'code'     => 'forgetPwd',
                'is_login' => false
            ],
            'balancelist'             => [
                'code'     => 'userBalance',
                'is_login' => true
            ],
            'recommend'               => [
                'code'     => 'recommend',
                'is_login' => true
            ],
            'sharecode'               => [
                'code'     => 'sharecode',
                'is_login' => true
            ],
            'cash'                    => [
                'code'     => 'cash',
                'is_login' => true
            ],
            'cashlist'                => [
                'code'     => 'cashList',
                'is_login' => true
            ],
            'gettrustlogin'           => [ //获取信任登录接口
                'code'     => 'getTrustLogin',
                'is_login' => false
            ],
            'trustcallback'           => [ //获取用户信息接口
                'code'     => 'trustCallBack',
                'is_login' => false
            ],
            'trustbind'               => [  //账号绑定接口
                'code'     => 'trustBind',
                'is_login' => false
            ],
            'ispoint'                 => [
                'code'     => 'isPoint',
                'is_login' => false
            ],
            'myinvite'                => [
                'code'     => 'myInvite',
                'is_login' => true
            ],
            'activationinvite'        => [
                'code'     => 'activationInvite',
                'is_login' => true
            ],
            'userpointlog'            => [
                'code'     => 'userPointLog',
                'is_login' => true
            ],
            'getsigninfo'             => [
                'code'     => 'getSignInfo',
                'is_login' => true
            ],
            'getarealist'             => [
                'code'     => 'getAreaList',
                'is_login' => false
            ],
            'getposter'               => [
                'code'     => 'getPoster',
                'is_login' => false
            ],
            'uniapplogin'             => [
                'code'     => 'uniAppLogin',
                'is_login' => false
            ],
            'shareurl'                => [
                'code'     => 'shareUrl',
                'is_login' => false
            ],
            'myinvoicelist'           => [
                'code'     => 'myInvoiceList',
                'is_login' => true
            ],
            'ttlogin'                 => [
                'code'     => 'ttLogin',
                'is_login' => false
            ],
            'share'                   => [
                'code'     => 'share',
                'is_login' => false
            ],
            'deshare'                 => [
                'code'     => 'deshare',
                'is_login' => false
            ],
            //腾讯地图H5获取地图信息接口
            'addressmap'=>[
                'code'     => 'addressMap',
                'is_login' => false
            ],
            'mapsearch'=>[
                'code'     => 'mapSearch',
                'is_login' => false
            ],
            'getrecommenduser'=>[
                'code'     => 'getRecommendUser',
                'is_login' => false
            ],
            'usercare'=>[
                'code'     => 'userCare',
                'is_login' => true
            ],
            'findcode'=>[
                'code'     => 'findCode',
                'is_login' => false
            ],
            'userpostprice'=>[
                'code'     => 'userPostPrice',
                'is_login' => true
            ],
            'getusercenter'=>[
                'code'     => 'getUserCenter',
                'is_login' => false
            ],
            'updateuserinfo'=>[
                'code'     => 'updateUserInfo',
                'is_login' => true
            ],
            'sendgooglemail'=>[
                'code'     => 'sendGoogleMail',
                'is_login' => false
            ],
            'reserveemail'=>[
                'code'     => 'reserveEmail',
                'is_login' => true
            ],
            'userlove'=>[
                'code'     => 'userLove',
                'is_login' => true
            ],
            'veriphone'=>[
                'code'     => 'veriPhone',
                'is_login' => true
            ],
            'saleworks'=>[
                'code'     => 'saleWorks',
                'is_login' => true
            ],
            'getsales'=>[
                'code'     => 'getSales',
                'is_login' => true
            ],
            'checkuserdata'=>[
                'code'     => 'checkUserData',
                'is_login' => false
            ],
            'cancelsale'=>[
                'code'     => 'cancelSale',
                'is_login' => true
            ],
            'checksign'=>[
                'code'     => 'checkSign',
                'is_login' => false
            ],
            'wallectexist'=>[
                'code'     => 'wallectExist',
                'is_login' => false
            ],
            'addwallect'=>[
                'code'     => 'addWallect',
                'is_login' => true
            ],
            'breakwallect'=>[
                'code'     => 'breakWallect',
                'is_login' => true
            ],
            'checkuservert'=>[
                'code'     => 'checkUserVert',
                'is_login' => true
            ],
            'userbuycard'=>[
                'code'     => 'userBuyCard',
                'is_login' => true
            ],
             'delpricelog'=>[
                'code'     => 'delPriceLog',
                'is_login' => true
            ],
             'agreeprice'=>[
                'code'     => 'agreePrice',
                'is_login' => true
            ],
            'userrecharge'=>[
                'code'     => 'userRecharge',
                'is_login' => true
            ],
            'unlockcontent'=>[
                'code'     => 'unlockContent',
                'is_login' => true
            ],
            'mintNft'=>[
                'code'     => 'mintNft',
                'is_login' => true
            ]
        ]
    ],

    'advert' => [
        'code'   => 'Advert',
        'method' => [
            'positionList'     => [
                'code'     => 'carouselSeatList',
                'is_login' => false
            ],
            'getAdvertList'    => [
                'code'     => 'getAdvertList',
                'is_login' => false
            ],
            'getcarousellists' => [
                'code'     => 'getCarouselLists',
                'is_login' => false
            ]
        ],
    ],

    'articles' => [
        'code'   => 'Articles',
        'method' => [
            'getArticleType'   => [
                'code'     => 'getArticleType',
                'is_login' => false
            ],
            'getArticleList'   => [
                'code'     => 'getArticleList',
                'is_login' => false
            ],
            'getArticleDetail' => [
                'code'     => 'getArticleDetail',
                'is_login' => false
            ],
            'getweixinmessage' => [
                'code'     => 'getWeixinMessage',
                'is_login' => false
            ],
            'leftarticletype'  => [
                'code'     => 'leftArticleType',
                'is_login' => false
            ],
            'search'       => [
                'code'     => 'search',
                'is_login' => false
            ],
            'getcateinfo'  => [
                'code'     => 'getCateInfo',
                'is_login' => false
            ],
            'addalbum'     => [
                'code'     => 'addAlbum',
                'is_login' => true
            ],
            'removecate'     => [
                'code'     => 'removeCate',
                'is_login' => true
            ],
            'checkdelcate'     => [
                'code'     => 'checkDelCate',
                'is_login' => true
            ],
            'getchapterInfo'     => [
                'code'     => 'getchapterInfo',
                'is_login' => true
            ],
        ]
    ],

    'brand' => [
        'code'   => 'Brand',
        'method' => [
            'brandList' => [
                'code'     => 'brandList',
                'is_login' => false
            ]
        ]
    ],

    'coupon' => [
        'code'   => 'Coupon',
        'method' => [
            'couponlist'   => [
                'code'     => 'couponList',
                'is_login' => false
            ],
            'coupondetail' => [
                'code'     => 'couponDetail',
                'is_login' => false
            ],
            'getcoupon'    => [
                'code'     => 'getCoupon',
                'is_login' => true,
            ],
            'usercoupon'   => [
                'code'     => 'userCoupon',
                'is_login' => true
            ],
            'getcouponkey' => [
                'code'     => 'getCouponKey',
                'is_login' => true
            ]
        ]
    ],

    'notice'     => [
        'code'   => 'Notice',
        'method' => [
            'noticeList' => [
                'code'     => 'noticeList',
                'is_login' => false
            ],
            'noticeType' => [
                'code'     => 'noticeType',
                'is_login' => false
            ],
            'noticeInfo' => [
                'code'     => 'noticeInfo',
                'is_login' => false
            ],
            'audiolist' => [
                'code'     => 'audioList',
                'is_login' => false
            ],
            'audioinfo' => [
                'code'     => 'audioInfo',
                'is_login' => false
            ],
            'getgameList' => [
                'code'     => 'getGameList',
                'is_login' => false
            ],
            'gameinfo' => [
                'code'     => 'gameInfo',
                'is_login' => false
            ],
            'addisekai' => [
                'code'     => 'addIseKai',
                'is_login' => true
            ],
            'isekaiinfo' => [
                'code'     => 'isekaiInfo',
                'is_login' => true
            ],
            'deliseKai' => [
                'code'     => 'delIseKai',
                'is_login' => true
            ],
            'helpCate' => [
                'code'     => 'helpCate',
                'is_login' => false
            ],
            'searchall' => [
                'code'     => 'searchAll',
                'is_login' => false
            ],
            'clearsearch' => [
                'code'     => 'clearSearch',
                'is_login' => false
            ]
        ]
    ],

    //购物车
    'cart'       => [
        'code'   => 'Cart',
        'method' => [
            'add'              => [
                'code'     => 'add',
                'is_login' => true
            ],
            'del'              => [
                'code'     => 'del',
                'is_login' => true
            ],
            'getlist'          => [
                'code'     => 'getList',
                'is_login' => true
            ],
            'setnums'          => [
                'code'     => 'setNums',
                'is_login' => true
            ],
            'getnumber'        => [
                'code'     => 'getNumber',
                'is_login' => true
            ],
            'batchsetcart'     => [
                'code'     => 'batchSetCart',
                'is_login' => true
            ],
            'getlistselectall' => [
                'code'     => 'getListSelectAll',
                'is_login' => true
            ]
        ]
    ],

    //商品分类
    'categories' => [
        'code'   => 'Categories',
        'method' => [
            'gettopcat'   => [
                'code'     => 'getTopCat',
                'is_login' => false
            ],
            'getchildcat' => [
                'code'     => 'getChildCat',
                'is_login' => false
            ],
            'getallcat'   => [
                'code'     => 'getAllCat',
                'is_login' => false
            ],
            'getname'     => [
                'code'     => 'getName',
                'is_login' => false
            ]
        ]
    ],

    //订单模块
    'order'      => [
        'code'   => 'Order',
        'method' => [
            'cancel'            => [
                'code'     => 'cancel',
                'is_login' => true
            ],
            'del'               => [
                'code'     => 'del',
                'is_login' => true
            ],
            'details'           => [
                'code'     => 'details',
                'is_login' => true
            ],
            'confirm'           => [
                'code'     => 'confirm',
                'is_login' => true
            ],
            'create'            => [
                'code'     => 'create',
                'is_login' => true
            ],
            'getship'           => [
                'code'     => 'getShip',
                'is_login' => true
            ],
            'getorderlist'      => [
                'code'     => 'getOrderList',
                'is_login' => true
            ],
            'getorderstatusnum' => [
                'code'     => 'getOrderStatusNum',
                'is_login' => true
            ],
            'aftersaleslist'    => [
                'code'     => 'aftersalesList',
                'is_login' => true
            ],
            'aftersalesinfo'    => [
                'code'     => 'aftersalesInfo',
                'is_login' => true,
            ],
            'aftersalesstatus'  => [
                'code'     => 'aftersalesStatus',
                'is_login' => true
            ],
            'addaftersales'     => [
                'code'     => 'addAftersales',
                'is_login' => true
            ],
            'sendreship'        => [
                'code'     => 'sendReship',
                'is_login' => true
            ],
            'iscomment'         => [
                'code'     => 'isComment',
                'is_login' => true
            ],
            'logistics'         => [
                'code'     => 'logistics',
                'is_login' => true
            ],
            'logisticbyapi'     => [
                'code'     => 'logisticsByApi',
                'is_login' => false
            ],
            'gettaxcode'        => [
                'code'     => 'getTaxCode',
                'is_login' => false
            ],
            'addorder'        => [
                'code'     => 'addOrder',
                'is_login' => true
            ]
        ]
    ],

    //商品
    'goods'      => [
        'code'   => 'Goods',
        'method' => [
            'getlist'             => [
                'code'     => 'getList',
                'is_login' => false
            ],
            'getdetial'           => [
                'code'     => 'getDetial',
                'is_login' => false
            ],
            'appgetdetial'        => [
                'code'     => 'appGetDetail',
                'is_login' => false
            ],
            'getskudetial'        => [
                'code'     => 'getSkuDetial',
                'is_login' => false
            ],
            'getgoodsparams'      => [
                'code'     => 'getGoodsParams',
                'is_login' => false
            ],
            'getproductinfo'      => [
                'code'     => 'getProductInfo',
                'is_login' => false
            ],
            'appgetproductinfo'   => [
                'code'     => 'appGetProductInfo',
                'is_login' => false
            ],
            'getgoodscomment'     => [
                'code'     => 'getGoodsComment',
                'is_login' => false
            ],
            'getgoodscathotgoods' => [
                'code'     => 'getGoodsCatHotGoods',
                'is_login' => false
            ],
            'getpickgoods'        => [
                'code'     => 'getPickGoods',
                'is_login' => false
            ],
            'goodsall'            => [
                'code'     => 'goodsall',
                'is_login' => false
            ],
            'newgoods'            => [
                'code'     => 'newgoods',
                'is_login' => false
            ],
            'promotiongoods'      => [
                'code'     => 'promotiongoods',
                'is_login' => false
            ],
            'salesRanking' => [
                'code' => 'salesRanking',
                'is_login' => false
            ],
            'findgoodsdetail' => [
                'code' => 'findGoodsDetail',
                'is_login' => false
            ],
            'drawcard' => [
                'code' => 'drawCard',
                'is_login' => true
            ],
            'getrolelist' => [
                'code' => 'getRoleList',
                'is_login' => false
            ],
            'getdrawcardset' => [
                'code' => 'getDrawCardSet',
                'is_login' => false
            ],
            'getroleother' => [
                'code' => 'getRoleOther',
                'is_login' => false
            ],
            'cutrole' => [
                'code' => 'cutRole',
                'is_login' => true
            ],
            'upcutroleimg' => [
                'code' => 'upCutRoleImg',
                'is_login' => true
            ],
        ]
    ],

    //支付单
    'payments'   => [
        'code'   => 'Payments',
        'method' => [
            'getlist'  => [
                'code'     => 'getList',
                'is_login' => false
            ],
            'getinfo'  => [
                'code'     => 'getInfo',
                'is_login' => true
            ],
            'checkpay' => [
                'code'     => 'checkPay',
                'is_login' => false
            ]
        ]
    ],

    'images'      => [
        'code'   => 'Images',
        'method' => [
            'upload' => [
                'code'     => 'upload',
                'is_login' => false
            ],
            'getpaintings' => [
                'code'     => 'getPaintings',
                'is_login' => false
            ],
            'uploadaudio' => [
                'code'     => 'uploadAudio',
                'is_login' => false
            ],
            'uploadarticle' => [
                'code'     => 'uploadArticle',
                'is_login' => false
            ],
            'getpaintinginfo' => [
                'code'     => 'getPaintingInfo',
                'is_login' => false
            ]

        ]
    ],

    //商户门店列表
    'store'       => [
        'code'   => 'Store',
        'method' => [
            'getdefaultstore'  => [
                'code'     => 'getDefaultStore',
                'is_login' => false
            ],
            'getstorelist'     => [
                'code'     => 'getStoreList',
                'is_login' => false
            ],
            'isclerk'          => [
                'code'     => 'isClerk',
                'is_login' => true
            ],
            'storeladinglist'  => [
                'code'     => 'storeLadingList',
                'is_login' => true
            ],
            'ladinginfo'       => [
                'code'     => 'ladingInfo',
                'is_login' => true
            ],
            'lading'           => [
                'code'     => 'lading',
                'is_login' => true
            ],
            'getstoreswitch'   => [
                'code'     => 'getStoreSwitch',
                'is_login' => false
            ],
            'ladingdel'        => [
                'code'     => 'ladingDel',
                'is_login' => true
            ],
            'getinviteqrcode'  => [
                'code'     => 'getInviteQRCode',
                'is_login' => false
            ],
            'getrecommendkeys' => [
                'code'     => 'getRecommendKeys',
                'is_login' => false
            ]
        ]
    ],

    //团购&秒杀
    'group'       => [
        'code'   => 'Group',
        'method' => [
            'getlist'        => [
                'code'     => 'getList',
                'is_login' => false
            ],
            'getgoodsdetial' => [
                'code'     => 'getGoodsDetial',
                'is_login' => false
            ],
            'getproductinfo' => [
                'code'     => 'getProductInfo',
                'is_login' => false
            ]
        ]
    ],

    // h5 微信浏览器里分享
    'weixinshare' => [
        'code'   => 'WeiXinShare',
        'method' => [
            'share' => [
                'code'     => 'share',
                'is_login' => false
            ]
        ]
    ],

    // 表单
    'form'        => [
        'code'   => 'Form',
        'method' => [
            'getformdetial' => [
                'code'     => 'getFormDetial',
                'is_login' => false
            ],
            'addsubmit'     => [
                'code'     => 'addSubmit',
                'is_login' => false
            ],

        ],
    ],
    'appplus'     => [
        'code'   => 'AppPlus',
        'method' => [
            'checkversion' => [
                'code'     => 'checkVersion',
                'is_login' => false
            ]
        ]
    ],
    'pages'       => [ //首页布局可视化接口
        'code'   => 'Pages',
        'method' => [
            'getpageconfig' => [
                'code'     => 'getPageConfig',
                'is_login' => false
            ],
            //获取购买记录，非当前会员，首页滚动提示用的。
            'getrecod'      => [
                'code'     => 'getRecod',
                'is_login' => false
            ],
            'gethomepageconfig' => [
                'code'     => 'getHomePageConfig',
                'is_login' => false
            ],
        ]
    ],
    'pintuan'     => [
        'code'   => 'Pintuan',
        'method' => [
            'list'          => [
                'code'     => 'getList',
                'is_login' => false
            ],
            'goodsinfo'     => [
                'code'     => 'getGoodsInfo',
                'is_login' => false
            ],
            'productinfo'   => [
                'code'     => 'getProductInfo',
                'is_login' => false
            ],
            'pintuanrecord' => [
                'code'     => 'pintuanRecord',
                'is_login' => false,
            ],
            'pintuanteam'   => [
                'code'     => 'pintuanTeam',
                'is_login' => false
            ]

        ]
    ],
    'common'      => [
        'code'   => 'Common',
        'method' => [
            'area'         => [
                'code'     => 'area',
                'is_login' => false
            ],
            'areachildren' => [
                'code' => 'areaChildren',
                'is_login' => false
            ],
            'verify'       => [
                'code'     => 'verify',
                'is_login' => false
            ],
            'jshopconf' => [
                'code' => 'jshopConf',
                'is_login' => false
            ],
            'jshopaddons' => [
                'code' => 'addons',
                'is_login' => false
            ],
            'setlanguage' => [
                'code' => 'setLanguage',
                'is_login' => false
            ],
            'Test' => [
                'code' => 'test',
                'is_login' => false
            ],
            'getdatabytype' => [
                'code' => 'getDataByType',
                'is_login' => true
            ],
            'Test2' => [
                'code' => 'test2',
                'is_login' => false
            ],
            'checksaletime' => [
                'code' => 'checkSaleTime',
                'is_login' => false
            ],
            'userlogin' => [
                'code' => 'userLogin',
                'is_login' => false
            ],
            'binconvert' => [
                'code' => 'binConvert',
                'is_login' => false
            ],
            'getpagelinks' => [
                'code' => 'getPageLinks',
                'is_login' => false
            ],
            'setmerkleroot' => [
                'code' => 'setMerkleRoot',
                'is_login' => false
            ],
            'sourcelist' => [
                'code' => 'sourceList',
                'is_login' => true
            ],
            'uploadscript' => [
                'code' => 'uploadScript',
                'is_login' => true
            ],
            'getscriptbyid' => [
                'code' => 'getScriptById',
                'is_login' => false
            ],
            'getsourcebyid' => [
                'code' => 'getSourceById',
                'is_login' => false
            ],
            'saveuseract' => [
                'code' => 'saveUserAct',
                'is_login' => false
            ],
            'businesslist' => [
                'code' => 'businessList',
                'is_login' => false
            ],
            'saveusertime' => [
                'code' => 'saveUserTime',
                'is_login' => false
            ],
            'getmenulist' => [
                'code' => 'getMenuList',
                'is_login' => false
            ],
            'aboutus' => [
                'code' => 'aboutUs',
                'is_login' => false
            ],
            'datasync' => [
                'code' => 'dataSync',
                'is_login' => true
            ],
            'cardinfo' => [
                'code' => 'cardInfo',
                'is_login' => false
            ],
        ]
    ],
    //砍价
    'bargain'     => [
        'code'   => 'Bargain',
        'method' => [
            'list'              => [
                'code'     => 'getList',
                'is_login' => false
            ],
            'goodsinfo'         => [
                'code'     => 'getBargainDetial',
                'is_login' => true
            ],
            'dobargain'         => [
                'code'     => 'doBargain',
                'is_login' => true
            ],
            //获取价格信息
            'getprice'          => [
                'code'     => 'getUserBargainPrice',
                'is_login' => false
            ],
            'add'               => [
                'code'     => 'add',
                'is_login' => true
            ],
            'getuserbargainlog' => [
                'code'     => 'getUserBargainLog',
                'is_login' => true
            ],
            'canclebargain'     => [
                'code'     => 'cancleBargain',
                'is_login' => true
            ]
        ]
    ]
];
