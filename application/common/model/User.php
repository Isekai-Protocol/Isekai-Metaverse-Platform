<?php

namespace app\common\model;

use org\QRcode;
use org\Wx;
use think\model\concern\SoftDelete;
use think\Validate;
use think\Db;
use org\Curl;

class User extends Common
{
    use SoftDelete;
    protected $deleteTime = 'isdel';

    protected $autoWriteTimestamp = true;
    protected $updateTime = 'utime';


    const STATUS_NORMAL = 1;        //用户状态 正常
    const STATUS_DISABLE = 2;       //用户状态 停用

    const SEX_BOY = 1;
    const SEX_GIRL = 2;
    const SEX_OTHER = 3;

    //protected $resultSetType = 'collection';

    protected $rule = [
        //'username' => 'length:6,20|alphaDash',
        'mobile'   => ['regex' => '^1[3|4|5|6|7|8|9][0-9]\d{4,8}$'],
        'sex'      => 'in:1,2,3',
        'nickname' => 'length:2,50',
        'password' => 'length:6,20',
        'p_mobile' => ['regex' => '^1[3|4|5|6|7|8|9][0-9]\d{4,8}$'],
    ];
    protected $msg = [
        //'username.length' => '用户名长度6~20位',
        //'username.alphaDash' => '用户名只能是字母、数字或下划线组成',
        'mobile'   => '请输入一个合法的手机号码',
        'sex'      => '请选择合法的性别',
        'nickname' => '昵称长度为2-50个字符',
        'password' => '密码长度6-20位',
        'p_mobile' => '邀请人栏请输入一个合法的手机号码',
    ];


    /**
     * 用户账户密码登陆
     * @param array $data 用户登陆信息
     * @param int $loginType 1就是默认的，存session，2就是返回user_token
     * @param int $platform 平台id，主要和session有关系 1就是默认的平台，，2就是微信小程序平台，当需要放回user_token的时候，会用到此字段
     *
     */
    public function toLogin($data, $loginType = 1, $platform = 5,$type = 1,$verify_type = 1)
    {
        $result = array(
            'status' => false,
            'data'   => '',
            'msg'    => ''
        );

        // 登陆方式:1.平台账号登陆2.钱包登陆
        if($platform == 5){
            // 平台
            // if (!isset($data['mobile']) || !isset($data['password'])) {
            //     //            $result['msg'] = '请输入手机号码或者密码';
            //     return error_code(11031);
            // }
            if($type == 1){
                //登陆 
                if($verify_type == 1){
                //     if (!isset($data['value']) || empty($data['value'])) {
                //         return error_code(11079);
                //     }

                    if (!isEmail($data['email'])) {
                        return error_code(11079);
                    }
                }else{
                    if (!isMobile($data['mobile'])) {
                        return error_code(11057);
                    }
                //     if (!isset($data['value']) || empty($data['value'])) {
                //         return error_code(11051);
                //     }

                //     if (!isMobile($data['value'])) {
                //         return error_code(11057);
                //     }
                }
            }else{
                // 注册
                if($verify_type == 1){
                    if (!isset($data['email']) || empty($data['email'])) {
                        return error_code(11079);
                    }

                    if (!isEmail($data['email'])) {
                        return error_code(11079);
                    }
                }else{
                    if (!isset($data['mobile']) || empty($data['mobile'])) {
                        return error_code(11051);
                    }

                    if (!isMobile($data['mobile'])) {
                        return error_code(11057);
                    }
                }
            }

            if (!isset($data['password']) || empty($data['password'])) {
                return error_code(11009);
            }


            //校验验证码
            // if (session('?login_fail_num')) {
            //     if (session('login_fail_num') >= config('jshop.login_fail_num')) {
            //         if (!isset($data['captcha']) || $data['captcha'] == '') {
            //             return error_code(10013);
            //         }
                    // if (!captcha_check($data['captcha'])) {
            //             return error_code(10012);
            //         };
            //     }
            // }
            // if($type == 1){
                //登陆
                if($verify_type == 1){
                    $userInfo = $this->where('email',$data['email'])->find();
                    if($type == 1){
                        if (!$userInfo) {
                            return error_code(11032);
                        }

                        $userInfo = $this->where(array('email' => $data['email'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
                        if (!$userInfo) {
                            return error_code(11033);
                        }
                    }
                   
                }else{
                    $userInfo = $this->where('mobile',$data['mobile'])->find();
                    if($type == 1){
                        if (!$userInfo) {
                            return error_code(11032);
                        }
                        $userInfo = $this->where(array('mobile' => $data['mobile'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
                        if (!$userInfo) {
                            return error_code(11033);
                        }
                    }
                   
                } 
                
            // }else{
                // 注册
                // $userInfo = $this->where('email',$data['email'])->find();
                // $userInfo = $this->where(array('email' => $data['email'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
            // }

            
            if($type == 1){
                return $this->setSession($userInfo, $loginType, $platform);
                
            }else{
                // 注册
                if (!$userInfo) {
                    // 注册需要输入用户名
                    if (!isset($data['first_name']) || empty($data['first_name'])) {
                        return error_code(11008);
                    }

                    if (!isset($data['last_name']) || empty($data['last_name'])) {
                        return error_code(11008);
                    }

                    // 请输入验证码
                    if (!isset($data['code']) || empty($data['code'])) {
                        return error_code(10013);
                    }

                    $email = new \app\common\model\Email();
                    $smsModel   = new Sms();

                    if($verify_type == 1){
                        $e_res = $email->where(['receive' => $data['email'],'code' => $data['code'],'type' => 1,'verify_status' => 2])->find();
                        if(!$e_res){
                            return error_code(11180);
                        }
                    }else{
                        if (!$smsModel->check($data['mobile'], $data['code'], 'reg')) {
                            return error_code(11046);
                        }
                    }
                    

                    // }else{
                    //     if (!$smsModel->check($data['mobile'], $data['code'], 'reg')) {
                    //         return error_code(11046);
                    //     }
                    // }

                    // if (strlen($data['password']) < 8 || strlen($data['password']) > 16) {
                    //     return error_code(11009);
                    // }

                    if(!preg_match("/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){8,}$/", $data['password'])){
                        return error_code(11187);
                    }
                    // if(!isset($data['curpwd']) || empty($data['curpwd'])){
                    //     return error_code(11014);
                    // }

                    // if($data['password'] != $data['curpwd']){
                    //     return error_code(11044);
                    // }

                    // 创建用户
                    //没有此用户，创建此用户
                    $userData['username'] = $data['first_name'].' '.$data['last_name'];
                    $userData['first_name'] = $data['first_name'];
                    $userData['last_name'] = $data['last_name'];
                    if($verify_type == 1){
                        $userData['email'] = $data['email'];

                    }else{
                        $userData['mobile'] = $data['mobile'];

                    }

                    //如果没有头像和昵称，那么就取系统头像和昵称吧
                    if (isset($data['avatar'])) {
                        $userData['avatar'] = $data['avatar'];
                    } else {
                        // $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                        $userData['avatar'] = _sImage('');
                    }
                    // if (isset($data['nickname'])) {
                    //     $userData['nickname'] = $data['nickname'];
                    // } else {
                    //     $userData['nickname'] = format_mobile($data['mobile']);
                    // }
                    // if (isset($data['invitecode']) && $data['invitecode']) {
                    //     $pid   = $this->getUserIdByShareCode($data['invitecode']);
                    //     $pinfo = model('common/User')->where(['id' => $pid])->find();
                    //     if ($pinfo) {
                    //         $userData['pid'] = $pid;
                    //     } else {
                    //         //error_code(10014);
                    //         $userData['pid'] = 0;
                    //     }
                    // }

                    $userData['ctime'] = time();
                    // if (isset($data['password'])) {
                        //判断密码是否符合要求
                        
                        $userData['password'] = $this->enPassword($data['password'], $userData['ctime']);
                    // } 

                    
                        
                    //取默认的用户等级
                    $userGradeModel = new UserGrade();
                    $userGradeInfo  = $userGradeModel->where('is_def', $userGradeModel::IS_DEF_YES)->find();
                    if ($userGradeInfo) {
                        $userData['grade'] = $userGradeInfo['id'];
                    }


                    $user_id = $this->insertGetId($userData);
                    if (!$user_id) {
                        return error_code(10019);
                    }
                    $userInfo = $this->where(array('id' => $user_id))->find();
                    
                    // 修改验证码状态
                    if($verify_type == 1){
                        $email->where('id',$e_res['id'])->update(['verify_status' => 1]);
                    }else{
                        $smsModel->where(['mobile' => $data['mobile'],'code' => 'reg','status' => 1])->update(['status' => 2]);
                    }


                    hook('newuserreg', $userInfo);
                    hook("adminmessage",array('user_id'=>$user_id,"code"=>"user_register","params"=>$userInfo));

                    if ($userInfo['status'] == self::STATUS_NORMAL) {
                        $result = $this->setSession($userInfo, $loginType, $platform);            //根据登陆类型，去存session，或者是返回user_token
                    } else {
                        return error_code(11022);
                    }

                    // 注册平台钱包
                    $curl = new Curl();
                    $makeWalletUrl = 'http://124.71.137.54:1234/api/v1/createWallet';
                    $res = $curl::get($makeWalletUrl);
                    addLog($makeWalletUrl,[],$res);
                    $user_wallet = new \app\common\model\userWallet();

                    $wallet_str = json_decode($res);
                    if($wallet_str->code == 20000){
                        $str_data = $wallet_str->data;
                        $user_wallet->saveUserWallet($str_data->address,$user_id,$str_data->privateKey,$str_data->mnemonicWord,$str_data->publicKey);
                    }

                    return $result;
                }else{
                    return error_code(11185);
                }
            }


            //判断是否是用户名登陆
            // $userInfo = $this->where(array('mobile' => $data['mobile'], 'password' => $this->enPassword($data['password'], $userInfo->ctime)))->find();
            // if ($userInfo) {
            //     if ($userInfo['status'] == self::STATUS_NORMAL) {
            //         $result = $this->setSession($userInfo, $loginType, $platform);            //根据登陆类型，去存session，或者是返回user_token
            //     } else {
            //         return error_code(11022);
            //     }
            // } else {
            //     //写失败次数到session里
            //     if (session('?login_fail_num')) {
            //         session('login_fail_num', session('login_fail_num') + 1);
            //     } else {
            //         session('login_fail_num', 1);
            //     }
            //     return error_code(11033);
            // }

        }else{
            /* 钱包注册*/ 
            if(!isset($data['wallet_url']) || empty($data['wallet_url'])){
                return error_code(10206);
            }

            if($verify_type == 1){
                if (!isset($data['email']) || empty($data['email'])) {
                    return error_code(11079);
                }

                if (!isEmail($data['email'])) {
                    return error_code(11079);
                }
            }else{
                if (!isset($data['mobile']) || empty($data['mobile'])) {
                    return error_code(11051);
                }

                if (!isMobile($data['mobile'])) {
                    return error_code(11057);
                }
            }

            if (!isset($data['first_name']) || empty($data['first_name'])) {
                return error_code(11008);
            }

            if (!isset($data['last_name']) || empty($data['last_name'])) {
                return error_code(11008);
            }

            // 请输入验证码
            if (!isset($data['code']) || empty($data['code'])) {
                return error_code(10013);
            }

            $email = new \app\common\model\Email();
            $smsModel   = new Sms();

            $userData['username'] = $data['first_name'].'·'.$data['last_name'];
            $userData['first_name'] = $data['first_name'];
            $userData['last_name'] = $data['last_name'];
            if($verify_type == 1){
                $userData['email'] = $data['email'];

            }else{
                $userData['mobile'] = $data['mobile'];

            }
            if (isset($data['avatar'])) {
                $userData['avatar'] = $data['avatar'];
            } else {
                // $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                $userData['avatar'] = _sImage('');
            }
            $userData['ctime'] = time();
            $user_id = $this->insertGetId($userData);
            if (!$user_id) {
                return error_code(10019);
            }
            $userInfo = $this->where(array('id' => $user_id))->find();
            $wallet_mod = new \app\common\model\userWallet();
            $wallet_data = [
                'user_id' => $user_id,
                'address' => $data['wallet_url'],
                'ctime' => time(),
                'type' => 2,
                'is_default' => 1
            ];

            $wallet_mod->insert($wallet_data);

            hook('newuserreg', $userInfo);
            hook("adminmessage",array('user_id'=>$user_id,"code"=>"user_register","params"=>$userInfo));

            if ($userInfo['status'] == self::STATUS_NORMAL) {
                $result = $this->setSession($userInfo, $loginType, $platform);            //根据登陆类型，去存session，或者是返回user_token
            } else {
                return error_code(11022);
            }
            return $result;
        }

        return $result;
    }

    /**
     * 手机短信验证码登陆，同时兼有手机短信注册的功能，还有第三方账户绑定的功能
     * @param $data
     * @param int $loginType 登陆类型，1网页登陆，存session，2接口登陆，返回token
     * @param int $platform
     * @return array
     */
    public function smsLogin($data, $loginType = 1, $platform = 1)
    {
        if (!isset($data['mobile'])) {
            return error_code(11051);
        }
        if (!isset($data['code'])) {
            return error_code(10013);
        }

        //判断是否是用户名登陆
        $smsModel    = new Sms();
        $userWxModel = new UserWx();

        if (!$smsModel->check($data['mobile'], $data['code'], 'reg')) {
            return error_code(11046);
        }

        $userInfo = $this->where(array('mobile' => $data['mobile']))->find();
        if (!$userInfo) {
            //没有此用户，创建此用户
            $userData['mobile'] = $data['mobile'];

            //判断是否是小程序里的微信登陆，如果是，就查出来记录，取他的头像和昵称
            if (isset($data['user_wx_id'])) {
                $user_wx_info = $userWxModel->where(['id' => $data['user_wx_id']])->find();
                if ($user_wx_info) {
                    if (!isset($data['avatar'])) {
                        $data['avatar'] = $user_wx_info['avatar'];
                    }
                    if (!isset($data['nickname'])) {
                        $data['nickname'] = $user_wx_info['nickname'];
                    }
                    //取性别
                    if (!isset($data['sex'])) {
                        $uData['sex'] = $user_wx_info['gender'] == 0 ? 3 : $user_wx_info['gender'];
                    }
                }
            }
            //如果没有头像和昵称，那么就取系统头像和昵称吧
            if (isset($data['avatar'])) {
                $userData['avatar'] = $data['avatar'];
            } else {
                $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                $userData['avatar'] = _sImage('');
            }
            if (isset($data['nickname'])) {
                $userData['nickname'] = $data['nickname'];
            } else {
                $userData['nickname'] = format_mobile($data['mobile']);
            }
            if (isset($data['invitecode']) && $data['invitecode']) {
                $pid   = $this->getUserIdByShareCode($data['invitecode']);
                $pinfo = model('common/User')->where(['id' => $pid])->find();
                if ($pinfo) {
                    $userData['pid'] = $pid;
                } else {
                    //error_code(10014);
                    $userData['pid'] = 0;
                }
            }

            $userData['ctime'] = time();
            if (isset($data['password'])) {
                //判断密码是否符合要求
                if (!isset($data['password'][5]) || isset($data['password'][16])) {
                    return error_code(11009);
                }
                $userData['password'] = $this->enPassword($data['password'], $userData['ctime']);
            } else {
                $userData['password'] = "";
            }

            //取默认的用户等级
            $userGradeModel = new UserGrade();
            $userGradeInfo  = $userGradeModel->where('is_def', $userGradeModel::IS_DEF_YES)->find();
            if ($userGradeInfo) {
                $userData['grade'] = $userGradeInfo['id'];
            }


            $user_id = $this->insertGetId($userData);
            if (!$user_id) {
                return error_code(10019);
            }
            $userInfo = $this->where(array('id' => $user_id))->find();
            hook('newuserreg', $userInfo);
            hook("adminmessage",array('user_id'=>$user_id,"code"=>"user_register","params"=>$userInfo));
        } else {
            //如果有这个账号的话，判断一下是不是传密码了，如果传密码了，就是注册，这里就有问题，因为已经注册过
            if (isset($data['password'])) {
                return error_code(11019);
            }
        }
        //判断是否是小程序里的微信登陆，如果是，就给他绑定微信账号
        if (isset($data['user_wx_id'])) {
            $userWxModel->save(['user_id' => $userInfo['id']], ['id' => $data['user_wx_id']]);
        }

        if ($userInfo['status'] == self::STATUS_NORMAL) {
            $result = $this->setSession($userInfo, $loginType, $platform);            //根据登陆类型，去存session，或者是返回user_token
        } else {
            return error_code(11022);
        }

        return $result;
    }

    /**
     * 用户绑定新手机号码，当没有绑定手机号码，又必须绑定的时候，会报11027错误码，然后需要请求这个接口绑定手机号码，也可以做更改手机号码的接口
     * @param $user_id
     * @param $mobile
     * @param $code
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bindMobile($user_id, $mobile, $code)
    {
        $result = array(
            'status' => false,
            'data'   => '',
            'msg'    => ''
        );
        //判断是否是用户名登陆
        $smsModel    = new Sms();

        if (!$smsModel->check($mobile, $code, 'bind')) {
            return error_code(11046);
        }
        //校验手机号码是否已经绑定用户
        $info = $this->where('mobile', $mobile)->find();
        if ($info) {
            return error_code(11028);
        }

        $userInfo = $this->where('id', $user_id)->find();
        if ($userInfo) {
            $userInfo->mobile = $mobile;
            $userInfo->save();
            $result['status'] = true;
            return $result;
        } else {
            return error_code(11081);
        }
    }

    /**
     * 登陆注册的时候，发送短信验证码
     */
    public function sms($mobile, $code)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => '成功'
        ];

        if(!isMobile($mobile)){
            return error_code(11182);
        }

        $userInfo = $this->where(array('mobile' => $mobile))->find();
        if ($code == 'reg') {
            //注册，目前暂时没有走注册流程，注册和登陆是一样的接口，都是login，这里要尤其注意
            if ($userInfo) {
                return error_code(11047);
            }
            //判断账号状态
            //            if($userInfo->status != self::STATUS_NORMAL) {
            //                $result['msg'] = '此账号已停用';
            //                return $result;
            //            }
        } elseif ($code == 'login') {
            //登陆
        } elseif ($code === 'veri') {
            // 找回密码
        } elseif ($code === 'bind') {
            // 账户绑定手机号码
        }elseif($code === 'set'){
            // 更换号码
            // if ($userInfo) {
            //     return error_code(11183);
            // }
        }elseif($code === 'cwp'){
            // 找回密码
        } else {
            //其他业务逻辑
            return error_code(10080);
        }

        //没问题了，就去发送短信验证码
        $smsModel = new Sms();
        return $smsModel->send($mobile, $code, []);
    }

    /**
     *设置用户登录信息或者更新用户登录信息
     * User:tianyu
     * @param $userInfo
     * @param $data
     * @param $loginType            登陆类型1是存session，主要是商户端的登陆和网页版本的登陆,2就是token
     * @param int $platform 1就是普通的登陆，主要是vue登陆，2就是微信小程序登陆，3是支付宝小程序，4是app，5是pc，写这个是为了保证h5端和小程序端可以同时保持登陆状态
     * @param int $type 1的话就是登录,2的话就是更新
     * @return array
     */
    public function setSession($userInfo, $loginType, $platform = 1, $type = 1)
    {
        $result = [
            'status' => false,
            'data'   => '',
            'msg'    => ''
        ];
        //判断账号状态
        if ($userInfo->status != self::STATUS_NORMAL) {
            return error_code(11022);
        }


        switch ($loginType) {
            case 1:
                session('user', $userInfo->toArray());
                $result['status'] = true;
                break;
            case 2:
                $userTokenModel = new UserToken();
                $result         = $userTokenModel->setToken($userInfo['id'], $platform);
                break;
        }

        if ($type == 1) {
            $userLogModel = new UserLog();        //添加登录日志
            $userLogModel->setLog($userInfo['id'], $userLogModel::USER_LOGIN);
        }
        return $result;
    }

    public function editInfo($id, $sex = '', $birthday = '', $nickname = '', $avatar = '')
    {
        $data   = [];

        if ($sex != '') {
            $data['sex'] = $sex;
        }
        if ($birthday != '') {
            $data['birthday'] = $birthday;
        }
        if ($nickname != '') {
            $data['nickname'] = htmlentities($nickname);
        }
        if ($avatar != '') {
            $data['avatar'] = $avatar;
        }
        $re = $this->save($data, ['id' => $id]);
        if ($re !== false) {
            //$userLogModel = new UserLog();
            //$userLogModel->setLog($id,$userLogModel::USER_EDIT);
            $result['status'] = true;
            $result['msg']    = '保存成功';
            $result['data'] = '';
            return $result;
        } else {
            return error_code(10005);
        }
    }

    // 设置用户信息
    public function setUserInfo($user_id = '',$data = array())
    {
        $user = $this->where('id',$user_id)->find();
        $smsModel = new Sms();
        $email = new \app\common\model\Email();

        if(empty($user)){
            return error_code(11004);
        }

        $save_data = [];

        // 姓名
        if((isset($data['first_name']) && !empty($data['first_name'])) &&  (isset($data['last_name']) && !empty($data['last_name']))){
            $save_data['first_name'] = $data['first_name']; 
            $save_data['last_name'] = $data['last_name']; 
        }

        // 昵称
        if(isset($data['username']) && !empty($data['username'])){
            $name_num = $this->where('username',$data['username'])->where('id','neq',$user_id)->count();
            if($name_num > 0){
                return error_code(11011);
            }

            $save_data['username'] = $data['username'];//昵称
        }

        // 头像
        if(isset($data['avatar'])){
            $save_data['avatar'] = $data['avatar'];
        }

        // 背景
        if(isset($data['back_img'])){
            $save_data['back_img'] = $data['back_img'];
        }

        // 链接
        if(isset($data['links'])){
            $save_data['links'] = $data['links'];
        }

        // 是否订阅
        if(isset($data['is_subscribe'])){
            $save_data['is_subscribe'] = $data['is_subscribe'];
        }

        // 是否开启二要素验证
        if(isset($data['is_open_two_vertify'])){
            $save_data['is_open_two_vertify'] = $data['is_open_two_vertify'];
        }

        // 订阅的邮箱
        if(isset($data['reserve_email'])){
            $save_data['reserve_email'] = $data['reserve_email'];
        }

        // 密码
        // if(isset($data['password']) && !empty($data['password'])){
            if(isset($data['new_password']) && !empty($data['new_password'])){
                if(isset($data['cur_password']) && !empty($data['cur_password'])){
                    if (strlen($data['new_password']) < 8 || strlen($data['new_password']) > 16) {
                        return error_code(11009);
                    }
                    if($data['cur_password'] != $data['new_password']){
                        return error_code(11025);
                    }else{
                        $save_data['password'] = $this->enPassword($data['new_password'], $user['ctime']);
                    }
                }else{
                    return error_code(11014);
                }
            }
        // }

        // 手机号码 修改密码验证
        if(isset($data['mobile']) && !empty($data['mobile'])){
            if(isset($data['code'])){
                if(!empty($data['code'])){
                    if (!$smsModel->check($data['mobile'], $data['code'], 'set')) {
                        return error_code(11046);
                    }
                }else{
                    return error_code(10013);
                }

                // $save_data['mobile'] = $data['mobile'];
            }
        }

        // 邮箱 修改密码验证
        if(isset($data['email']) && !empty($data['email'])){
            if($data['email'] != $user['email']){
                $e_res = $email->where(['receive' => $data['email'],'code' => $data['code'],'type' => 1,'verify_status' => 2])->find();
                if(!$e_res){
                   return error_code(11180);
                }
            }
            
        }

        // 二要素验证
        if(isset($data['is_open_two_vertify']) && $data['is_open_two_vertify'] == 1){
            if($data['two_verify_type'] == 1){
                // 验证手机
                if(!isMobile($data['two_verify'])){
                    return error_code(11057);
                }

                if(isset($data['two_verify']) && !empty($data['two_verify'])){
                    // if($user['two_verify'] == $data['two_verify']){
                    //     return error_code(11186);
                    // }

                    if(isset($data['verify_code']) && !empty($data['verify_code'])){
                        if (!$smsModel->check($data['two_verify'], $data['verify_code'], 'veri')) {
                            return error_code(11046);
                        }

                        $save_data['two_verify'] = $data['two_verify'];
                    }else{
                        return error_code(10013);
                    }
                }else{
                    return error_code(11051);
                }
            }elseif($data['two_verify_type'] == 2){
                // 验证邮箱
                if(!isEmail($data['two_verify_email'])){
                    return error_code(11079);
                }

                if(isset($data['two_verify_email']) && !empty($data['two_verify_email'])){
                    // if($user['two_verify_email'] == $data['two_verify_email']){
                    //     return error_code(11186);
                    // }

                    if(isset($data['verify_code']) && !empty($data['verify_code'])){
                        if (!$smsModel->check($data['verify_code'], $data['verify_code'], 'veri')) {
                            return error_code(11046);
                        }

                        $save_data['two_verify_email'] = $data['two_verify_email'];
                    }else{
                        return error_code(10013);
                    }
                }else{
                    return error_code(11079);
                }
            }
        }
        

        // 收税钱包地址和版税设置
        if(isset($data['royalties']) && !empty($data['royalties'])){
            // if($data['royalties'] <= 0){
            //     return error_code(13330);
            // }
            if(isset($data['wallect']) && !empty($data['wallect'])){
                $save_data['royalties'] = $data['royalties'];
                $save_data['wallet_url'] = $data['wallect'];

                
            }else{
                return error_code(10206);
            }
            
            setDefaultRoyalty($data['wallet_url'],$data['royalties'],2);
        }
        
        // dump($save_data);die;
        if(empty($save_data)){
            return error_code(10005);
        }

        $res = $this->save($save_data,['id' => $user_id]);
        if($res){
            $result['status'] = true;
            $result['msg']    = '保存成功';
            $result['data'] = '';
            return $result;
        }else{
            return error_code(10005);
        }
    }

    // 验证手机号码
    public function checVeriPhone($user_id,$phone,$code)
    {
        $userInfo = $this->where('id',$user_id)->find();
        if(empty($userInfo)){
            return error_code(11004);
        }

        if(!isMobile($phone)){
            return error_code(11182);
        }

        if($userInfo['mobile'] != $phone){
            return error_code(11184);
        }

        $smsModel = new Sms();

        if (!$smsModel->check($phone, $code, 'veri')) {
            return error_code(11046);
        }

        return ['status' => true,'msg' => '验证通过'];
    }

    // 验证手机号码
    public function checSetPhone($phone,$code)
    {

        if(!isMobile($phone)){
            return error_code(11182);
        }

        $smsModel = new Sms();

        if (!$smsModel->check($phone, $code, 'veri')) {
            return error_code(11046);
        }

        return ['status' => true,'msg' => '验证通过'];
    }

    /**
     * 密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    public function enPassword($password, $ctime)
    {

        return md5(md5($password) . $ctime);
    }

    protected function tableWhere($post)
    {
        $where = [];
        if (isset($post['sex']) && $post['sex'] != "") {
            $where[] = ['sex', 'eq', $post['sex']];
        }
        if (isset($post['id']) && $post['id'] != "") {
            $where[] = ['id', 'in', $post['id']];
        }
        if (isset($post['username']) && $post['username'] != "") {
            $where[] = ['username', 'like', '%' . $post['username'] . '%'];
        }
        if (isset($post['mobile']) && $post['mobile'] != "") {
            $where[] = ['mobile|username', 'eq', $post['mobile']];
        }
        if (isset($post['birthday']) && $post['birthday'] != "") {
            $where[] = ['birthday', 'eq', $post['birthday']];
        }
        if (isset($post['nickname']) && $post['nickname'] != "") {
            $where[] = ['nickname', 'like', '%' . $post['nickname'] . '%'];
        }
        if (isset($post['status']) && $post['status'] != "") {
            $where[] = ['status', 'eq', $post['status']];
        }
        if (isset($post['pmobile']) && $post['pmobile'] != "") {
            $pwhere[] = ['mobile|username', 'like', "%" . $post['pmobile'] . "%"];
            $user      = $this->field('id')->where($pwhere)->select();
            if (!$user->isEmpty()) {
                $user = array_column($user->toArray(), 'id');
                $where[] = ['pid', 'in', $user];
            } else {
                $where[] = ['pid', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            }

            //            if ($puser_id = get_user_id($post['pmobile'])) {
            //                $where[] = ['pid', 'eq', $puser_id];
            //            } else {
            //                $where[] = ['pid', 'eq', '99999999'];       //如果没有此用户，那么就赋值个数值，让他查不出数据
            //            }
        }
        if (isset($post['grade']) && $post['grade'] != "") {
            $where[] = ['grade', 'in', $post['grade']];
        }
        if (isset($post['mobileOrUser']) && $post['mobileOrUser'] != "") {
            $where[] = ['username|mobile', 'like', '%' . $post['mobileOrUser'] . '%'];
        }

        $result['where'] = $where;
        $result['field'] = "*";
        $result['order'] = "id desc";
        return $result;
    }

    /**
     * 根据查询结果，格式化数据
     * @param $list
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function tableFormat($list)
    {
        foreach ($list as $k => $v) {
            if ($v['sex']) {
                $list[$k]['sex'] = config('params.user')['sex'][$v['sex']];
            }
            if ($v['status']) {
                $list[$k]['status'] = config('params.user')['status'][$v['status']];
            }
            if ($v['pid']) {
                $list[$k]['pid_name'] = get_user_info($v['pid'], 'nickname');
            }
            if ($v['ctime']) {
                $list[$k]['ctime'] = getTime($v['ctime']);
            }
            if (isset($v['avatar']) && $v['avatar']) {
                $list[$k]['avatar'] = _sImage($v['avatar']);
            }
        }
        return $list;
    }

    /**
     * 返回layui的table所需要的格式
     * @author sin
     * @param $post
     * @return mixed
     */
    public function tableData($post, $isPage = true)
    {
        if (isset($post['limit'])) {
            $limit = $post['limit'];
        } else {
            $limit = config('paginate.list_rows');
        }
        $tableWhere = $this->tableWhere($post);
        $user_wallet = new \app\common\model\userWallet();

        if ($isPage) {
            if($post['type'] == 1){
                // 平台注册
                $user_ids = $user_wallet->where('type',1)->column('user_id');
                // metamask注册
                $m_user_ids = $user_wallet->where('type',2)->column('user_id');
                if(!empty($m_user_ids)){
                    $u_user_ids = $this->whereNotIn('id',$m_user_ids)->column('id');
                    $user_ids = $user_ids+$u_user_ids;
                }
                
            }else{
                // metamask注册
                $user_ids = $user_wallet->where('type',2)->column('user_id');
            }

            $list = $this->with(['grade', 'userWx'])->field($tableWhere['field'])->where($tableWhere['where'])->whereIn('id',$user_ids)->order($tableWhere['order'])->paginate($limit);
            // $re['sql'] = $this->getLastSql();
            $data        = $this->tableFormat($list->getCollection());         //返回的数据格式化，并渲染成table所需要的最终的显示数据类型
            $re['count'] = $list->total();
        } else {
            $list = $this->field($tableWhere['field'])->where($tableWhere['where'])->order($tableWhere['order'])->select();
            if (!$list->isEmpty()) {
                $data = $this->tableFormat($list->toArray());
            }
            $re['count'] = count($list);
        }

        $re['code'] = 0;
        $re['msg']  = '';
        // 获取钱包
        

        foreach ($data as $key => $value) {
            $wallet = $user_wallet->where('user_id',$value['id'])->field('address,type,id')->order('is_default desc')->select();
            $data[$key]['wallet_url'] = isset($wallet[0]['address']) ? $wallet[0]['address'] : '';
        }

        $re['data'] = $data;

        return $re;
    }


    public function changeAvatar($id, $image_url)
    {
        $data['avatar'] = $image_url;
        $where['id']    = $id;
        if ($this->save($data, $where)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取用户的信息
     * @return array|null|\PDOStatement|string|\think\Model
     */
    //    public function getUserInfo($user_id)
    //    {
    //        $data = $this->where('id', $user_id)->find();
    //        if ($data) {
    //            $data['state']    = $data['status'];
    //            $data['status']   = config('params.user')['status'][$data['status']];
    //            $data['p_mobile'] = $this->getUserMobile($data['pid']);
    //            return $data;
    //        } else {
    //            return "";
    //        }
    //    }

    public function getUserInfo($user_id,$my_id = 0)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $userInfo = $this->field('id,username,mobile,avatar,ctime,wallet_url as wallect,status,remarks,is_recommend,back_img,links,is_subscribe,reserve_email,email,first_name,last_name,royalties,two_verify,is_open_two_vertify')
            ->where(array('id' => $user_id))
            ->find();

        if ($userInfo) {
            if(empty($userInfo['avatar'])){
                $img_id = getSetting('shop_default_image');//系统默认图片
                if (!$img_id) {
                    $img_id = config('jshop.default_image');//默认图片
                }
            }else{
                $img_id = $userInfo['avatar'];
            }
            
            $userLove = new \app\common\model\UserFollow();

            $temp_love = $userLove->where(['user_id' => $my_id,'follow_id' => $user_id])->find();
            // dump($userLove->getLastSql());die;
            $userInfo['is_love'] = $temp_love ? true : false;

            $userInfo['image_id'] = $img_id;
            $userInfo['avatar'] = _sImage($userInfo['avatar']);
            $userInfo['back_img'] = _sImage($userInfo['back_img']);
            $userInfo['reserve_email'] = $userInfo['email'];

            $userInfo['ctime'] = date('Y年m月',$userInfo['ctime']);

            // 钱包信息
            $user_wallet = new \app\common\model\userWallet();
            $userInfo['wallet'] = $user_wallet->where('user_id',$user_id)->field('address,type,id,balance')->order('is_default desc')->select();
            foreach ($userInfo['wallet'] as $key => $value) {
                $userInfo['wallet'][$key]['wallet_format'] = formatWallet($value['address']);
                $nft_total = balanceOf($value['address']);
                $userInfo['wallet'][$key]['nft_total'] = isset($nft_data['data']) ? $nft_data['data'] : 0;
            }

            $userInfo['wallet_url'] = formatWallet($userInfo['wallet'][0]['address']); 

            $userInfo['reg_type'] = !empty($userInfo['mobile']) ? 1 : 2;
            // 卡券数量
            $card_mod = new \app\common\model\UserCard();
            $userInfo['card_total'] = $card_mod->where(['user_id' => $user_id,'is_used' => 0])->count();

            $result['data'] = $userInfo;
            $result['status'] = true;


        } else {
            return  error_code(11004);
        }
        return $result;
    }

    //忘记密码，找回密码
    public function forgetPassword($mobile, $code, $newPwd)
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];

        if (empty($code)) {
            return error_code(10013);
        }
        $smsModel = new Sms();
        if (!$smsModel->check($mobile, $code, 'veri')) {
            return error_code(10012);
        }
        $userInfo = $this->where(['mobile' => $mobile])->find();
        if (!$userInfo) {
            $result['msg'] = '没有此手机号码';
            return error_code(11032);
        }
        return $this->editPwd($userInfo['id'], $newPwd, $userInfo['ctime']);
    }

    //修改用户密码，如果用户之前没有密码，那么就不校验原密码
    public function changePassword($user_id, $newPwd, $password = "")
    {
        $result = [
            'status' => false,
            'msg' => '',
            'data' => ''
        ];
        //修改密码验证原密码
        $user = $this->get($user_id);
        if (!$user) {
            return error_code(10000);
        }

        // if ($user['password']) {
            // if (!$password) {
            //     return error_code(11012);
            // }
            // if ($user['password'] !== $this->enPassword($password, $user['ctime'])) {
            //     return error_code(11045);
            // }
        // }
        return $this->editPwd($user_id, $newPwd, $user['ctime']);
    }


    /**
     *  修改密码
     * @param $user_id
     * @param $pwd
     * @return array
     */
    private function editPwd($user_id, $newPwd, $ctime)
    {
        $result = [
            'status' => true,
            'msg'    => '',
            'data'   => ''
        ];

        if ($newPwd == '' || strlen($newPwd) < 6 || strlen($newPwd) > 16) {
            return error_code(11009);
        }

        $res_pwd = $this->save([
            'password' => $this->enPassword($newPwd, $ctime)
        ], ['id' => $user_id]);

        if (!$res_pwd) {
            return error_code(11044);
        }

        $result['msg'] = '密码修改成功!';
        return $result;
    }


    /**
     *
     *  获取用户的推荐列表
     * @param $user_id
     * @param $page
     * @param $limit
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recommendList($user_id, $page = 1, $limit = 10)
    {
        $data  = $this
            ->field('nickname, avatar, mobile, ctime')
            ->where('pid', $user_id)
            ->page($page, $limit)
            ->select();
        $count = $this
            ->field('nickname, avatar, mobile, ctime')
            ->where('pid', $user_id)
            ->count();
        if (!$data->isEmpty()) {
            foreach ($data as $v) {
                $v['ctime'] = getTime($v['ctime']);
            }
            $result['data'] = $data;
        }
        return $result = [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $data,
            'total'  => ceil($count / $limit)
        ];
    }


    /**
     * 获取用户的积分
     * @param $user_id
     * @param int $order_money
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserPoint($user_id, $order_money = 0)
    {
        $return = [
            'status'          => false,
            'msg'             => error_code(10025, true),
            'data'            => 0,
            'available_point' => 0,
            'point_rmb'       => 0,
            'switch'          => 1
        ];

        $settingModel = new Setting();
        $switch       = $settingModel->getValue('point_switch');
        if ($switch == 2) {
            $return['status'] = true;
            $return['switch'] = 2;
            return $return;
        }

        $where[] = ['id', 'eq', $user_id];
        $data    = $this->field('point')->where($where)->find();
        if ($data !== false) {
            if ($order_money != 0) {
                //计算可用积分
                $settingModel                = new Setting();
                $orders_point_proportion     = $settingModel->getValue('orders_point_proportion'); //订单积分使用比例
                $max_point_deducted_money    = $order_money * ($orders_point_proportion / 100); //最大积分抵扣的钱
                $point_discounted_proportion = $settingModel->getValue('point_discounted_proportion'); //积分兑换比例
                $needs_point                 = $max_point_deducted_money * $point_discounted_proportion;
                $return['available_point']   = floor($needs_point > $data['point'] ? $data['point'] : $needs_point);
                $return['point_rmb']         = $return['available_point'] / $point_discounted_proportion;
            }

            $return['msg']    = '获取成功';
            $return['data']   = $data['point'];
            $return['status'] = true;
        }

        return $return;
    }


    /**
     * 获取用户昵称 （废弃方法，不建议使用，建议使用get_user_info()函数）
     * @param $user_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserNickname($user_id)
    {
        $where[] = ['id', 'eq', $user_id];
        $result  = $this->field('nickname, mobile')
            ->where($where)
            ->find();
        if ($result) {
            $nickname = $result['nickname'] ? $result['nickname'] : format_mobile($result['mobile']);
        } else {
            $nickname = '';
        }

        return $nickname;
    }


    /**
     * 获取用户手机号
     * @param $user_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserMobile($user_id)
    {
        $where[] = ['id', 'eq', $user_id];
        $result  = $this->field('mobile')->where($where)->find();
        return $result['mobile'] ? $result['mobile'] : '';
    }


    /**
     * 通过手机号获取用户ID
     * @param $mobile
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserIdByMobile($mobile)
    {
        $where[] = ['mobile', 'eq', $mobile];
        $where[] = ['status', 'eq', self::STATUS_NORMAL];
        $result  = $this->field('id')->where($where)->find();
        return $result['id'] ? $result['id'] : false;
    }


    /**
     * 绑定上级
     * @param $user_id
     * @param $superior_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function setMyInvite($user_id, $superior_id)
    {
        if ($user_id == $superior_id) {
            return error_code(11049);
        }

        $userInfo = $this->get($user_id);
        if ($userInfo['pid'] && $userInfo['pid'] != 0) {
            return error_code(11053);
        }

        $superior = $this->get($superior_id);
        if (!$superior) {
            return error_code(11052);
        }

        $flag = $this->isInvited($user_id, $superior_id);
        if ($flag) {
            return error_code(11054);
        }

        $data['pid']    = $superior_id;
        $where[]        = ['id', 'eq', $user_id];
        $return = [];
        $return['data'] = $this->save($data, $where);
        if ($return['data'] !== false) {
            $return['status'] = true;
            $return['msg']    = '填写邀请码成功';
        } else {
            return error_code(11048);
        }

        return $return;
    }


    /**
     * 判断user_id是否是pid的父节点或者祖父节点,如果是，就返回true，如果不是就返回false
     * @param $user_id      下级节点
     * @param $pid          父节点
     * @return bool
     */
    public function isInvited($user_id, $pid)
    {
        $where[] = ['id', 'eq', $pid];
        $info    = $this->field('pid')->where($where)->find();
        if (!$info || $info['pid'] == 0) {
            return false;
        } else {
            if ($info['pid'] == $user_id) {
                return true;
            } else {
                return $this->isInvited($user_id, $info['pid']);
            }
        }
    }


    /**
     * 获取用户分享码
     * @param $user_id
     * @return float|int|string
     */
    public function getShareCodeByUserId($user_id)
    {
        $code = ((int) $user_id + 1234) * 3;
        return $code;
    }


    /**
     * 获取用户ID
     * @param $code
     * @return float|int
     */
    public function getUserIdByShareCode($code)
    {
        $user_id = ((int) $code / 3) - 1234;
        return $user_id;
    }


    /**
     * 修改邀请人
     * @param $id
     * @param $mobile
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function editInvite($id, $pid)
    {
        $return = [
            'status' => false,
            'msg'    => '',
            'data'   => ''
        ];

        if ($id == $pid) {
            return error_code(11049);
        }

        $isInvited = $this->isInvited($id, $pid);
        if ($isInvited) {
            return error_code(11054);
        }
        $return['status'] = true;
        $return['msg']    = '';

        return $return;
    }


    /**
     * 后台添加用户
     * @param $data
     * @return array
     */
    public function manageAdd($data)
    {

        if (isset($data['mobile'])) {
            if ($data['mobile'] == '') {
                return error_code(11051);
            }
            if (!isMobile($data['mobile'])) {
                return error_code(11057);
            }
            $flag = $this->checkUserByMobile($data['mobile']);
            if ($flag) {
                return error_code(11058);
            }
        }

        if (isset($data['username']) && $data['username'] != "") {
            if ($this->where('username', $data['username'])->find()) {
                return error_code(11059);
            }
        }

        if (isset($data['password'])) {
            if ($data['password'] == '' || strlen($data['password']) < 6 || strlen($data['password']) > 16) {
                return error_code(11009);
            }
            //密码效验
            if ($data['password'] !== $data['repassword']) {
                return error_code(11025);
            }
        }
        if (!isset($data['pid']) || $data['pid'] == '') {
            $data['pid'] = 0;
        }

        //默认用户等级
        if (!isset($data['grade'])) {
            $userGradeModel = new UserGrade();
            $gradeInfo = $userGradeModel->where('is_def', '1')->find();
            if ($gradeInfo) {
                $data['grade'] = $gradeInfo['id'];
            } else {
                $data['grade'] = 0;
            }
        }

        //用户备注只取前100个字符
        if (isset($data['remarks'])) {
            $data['remarks'] = trim($data['remarks']);
            if (mb_strlen($data['remarks']) > 100) {
                $data['remarks'] = substr($data['remarks'], 0, 99);
            }
        }
        $time                = time();
        $newData['username'] = $data['username'];
        $newData['mobile']   = isset($data['mobile']) ? $data['mobile'] : "";
        $newData['password'] = isset($data['password']) ? $this->enPassword($data['password'], $time) : ""; 
        // $newData['sex']      = isset($data['sex']) ? $data['sex'] : 3;
        $newData['is_recommend']      = isset($data['is_recommend']) ? $data['is_recommend'] : 0;
        // $newData['birthday'] = $data['birthday'] ? $data['birthday'] : null;
        $newData['avatar']   = isset($data['avatar']) ? $data['avatar'] : '';
        // $newData['nickname'] = $data['nickname'];
        $newData['balance']  = 0;
        $newData['point']    = 0;
        $newData['ctime']    = $time;
        $newData['utime']    = $time;
        $newData['status']   = isset($data['status']) ? $data['status'] : self::STATUS_NORMAL;
        // $newData['pid']      = $data['pid'];
        $newData['back_img']    = isset($data['back_img']) ? $data['back_img'] : '';
        $newData['remarks'] = isset($data['remarks']) ? $data['remarks'] : '';
        $result         = $this->save($newData);
        $return['data'] = $this->id;
        if ($result) {
            $curl = new Curl();
            $makeWalletUrl = 'http://124.70.153.7:1234/api/v1/createWallet';
            $res = $curl::get($makeWalletUrl);
            $user_wallet = new \app\common\model\userWallet();

            $wallet_str = json_decode($res);
            if($wallet_str->code == 20000){
                $str_data = $wallet_str->data;
                $user_wallet->saveUserWallet($str_data->address,$this->id,$str_data->privateKey,$str_data->mnemonicWord,$str_data->publicKey);
            }

            $newData['id'] = $this->id;
            hook('addUserAfter', $newData); //添加用户后钩子
            if (session('manage.id')) {
                $userLogModel = new UserLog();
                $userLogModel->setLog(session('manage.id'), $userLogModel::USER_REG);
            }
            $return['status'] = true;
            $return['msg']    = '添加成功';
        } else {
            return error_code(10038);
        }

        return $return;
    }


    /**
     * 后台修改用户
     * @param $data
     * @return array
     */
    public function manageEdit($data)
    {
        $return = error_code(10024);

        //校验数据
        $validate = new Validate($this->rule, $this->msg);
        if (!$validate->check($data)) {
            $return['msg'] = $validate->getError();
            return $return;
        }
        if (isset($data['pid']) && $data['pid'] != '') {
            $p = $this->editInvite($data['id'], $data['pid']);
            if ($p['status'] === false) {
                $return['msg'] = $p['msg'];
                return $return;
            }
        } else {
            $data['pid'] = 0;
        }
        //输入密码时修改密码
        if (isset($data['password']) && $data['password'] != '') {
            if (strlen($data['password']) < 6 || strlen($data['password']) > 20) {
                return error_code(11009);
            }
            //密码效验
            if ($data['password'] !== $data['repassword']) {
                return error_code(11025);
            }
            $userInfo            = $this->get($data['id']);
            $newData['password'] = $this->enPassword($data['password'], $userInfo['ctime']);
        }

        //用户备注只取前100个字符
        if (isset($data['remarks'])) {
            $data['remarks'] = trim($data['remarks']);
            if (mb_strlen($data['remarks']) > 100) {
                $data['remarks'] = substr($data['remarks'], 0, 99);
            }
            $newData['remarks'] = $data['remarks'];
        }
        $where[]             = ['id', 'eq', $data['id']];
        $newData['nickname'] = $data['nickname'];
        // $newData['sex']      = $data['sex'] ? $data['sex'] : 3;
        $newData['is_recommend']      = $data['is_recommend'] ? $data['is_recommend'] : 0;
        // $newData['birthday'] = $data['birthday'] ? $data['birthday'] : null;
        $newData['avatar']   = $data['avatar'];
        $newData['status']   = $data['status'];
        $newData['back_img']    = isset($data['back_img']) ? $data['back_img'] : '';
        $newData['email']    = isset($data['email']) ? $data['email'] : '';
        $newData['username']    = isset($data['username']) ? $data['username'] : '';
        // $newData['pid']      = $data['pid'];
        // $newData['grade']    = $data['grade'];
        $result              = $this->save($newData, $where);
        $return['data']      = $result;

        if ($result) {
            $return['status'] = true;
            $return['msg']    = '修改成功';
        }

        return $return;
    }

    // 获取推荐用户
    public function getRecommendUser($is_recommend = 1,$p = 1,$n = 20)
    {
        $result = [
            'status' => false,
            'data' => [],
            'msg' => ''
        ];
        $userList = $this->field('id,username,mobile,avatar,ctime,wallet_url,status,remarks,is_recommend')
            ->where(array('is_recommend' => $is_recommend))
            ->order('id desc')
            ->page($p,$n)
            ->select();

        $count = $this->where(array('is_recommend' => $is_recommend))->count();
            
        if ($userList !== false) {
            foreach ($userList as $key => $value) {
               $userList[$key]['avatar'] = _sImage($value['avatar']).addImageSuffix(4);

               $userList[$key]['ctime'] = date('Y年m月',$value['ctime']);
            }
            

            $result['data']['list'] = $userList;
            $result['data']['count'] = $count;
            $result['status'] = true;


        } else {
            return  error_code(11004);
        }
        return $result;
    }

    /**
     * 根据用户手机号获取用户id
     */
    public function checkUserByMobile($mobile)
    {
        $where[] = ['mobile', 'eq', $mobile];
        // $where[] = ['status', 'eq', self::STATUS_NORMAL];
        $res     = $this->field('id')->where($where)->find();
        return $res;
    }


    /**
     * 设置csv header
     * @return array
     */
    public function csvHeader()
    {
        return [
            [
                'id'   => 'mobile',
                'desc' => '手机号',
            ],
            [
                'id'   => 'sex',
                'desc' => '性别',
            ],
            [
                'id'   => 'birthday',
                'desc' => '生日',
            ],
            [
                'id'   => 'avatar',
                'desc' => '头像',
            ],
            [
                'id'   => 'nickname',
                'desc' => '昵称',
            ],
            [
                'id'   => 'balance',
                'desc' => '余额',
            ],
            [
                'id'   => 'point',
                'desc' => '积分',
                // 'modify'=>'getBool'
            ],
            [
                'id'   => 'status',
                'desc' => '状态',
                //'modify'=>'getMarketable',
            ],
            //            [
            //                'id' => 'pid_name',
            //                'desc' => '邀请人',
            //            ],
            //            [
            //                'id' => 'ctime',
            //                'desc' => '创建时间',
            //            ],
            [
                'id'   => 'username',
                'desc' => '用户名',
            ],
            //
        ];
    }


    /**
     * 获取csv数据
     * @param $post
     * @return array
     */
    public function getCsvData($post)
    {
        $result   = error_code(10083);
        $header   = $this->csvHeader();
        $userData = $this->tableData($post, false);


        if ($userData['count'] > 0) {
            $tempBody = $userData['data'];
            $body     = [];
            $i        = 0;

            foreach ($tempBody as $key => $val) {
                $i++;
                foreach ($header as $hk => $hv) {
                    if (isset($val[$hv['id']]) && $val[$hv['id']] && isset($hv['modify'])) {
                        if (function_exists($hv['modify'])) {
                            $body[$i][$hk] = $hv['modify']($val[$hv['id']]);
                        }
                    } elseif (isset($val[$hv['id']]) && !empty($val[$hv['id']])) {
                        $body[$i][$hk] = $val[$hv['id']];
                    } else {
                        $body[$i][$hk] = '';
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

    public function doAdd($data = [])
    {
        $result = $this->insert($data);
        if ($result) {
            return $this->getLastInsID();
        }
        return $result;
    }

    public function grade()
    {
        return $this->hasOne("UserGrade", 'id', 'grade')->bind(['grade_name' => 'name']);
    }
    public function userWx()
    {
        return $this->hasMany("UserWx", 'user_id', 'id');
    }


    /**
     * 按天统计新会员
     */
    public function statistics($day,$type = 1)
    {
        $where   = [];
        $where[] = ['ctime', '>', strtotime('-' . $day . ' days')];
        
        // 判断是平台注册还是metamask注册
        $wallectModel = new \app\common\model\userWallet();
        $user_ids = $wallectModel->where('type',2)->column('user_id');
        
        if($type == 1){
            $where[] = ['id','not in',$user_ids];
        }else{
            $where[] = ['id','in',$user_ids];
        }
        

        $field = 'DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d") as day, count(*) as nums';

        $res  = $this->field($field)->where($where)->where("TIMESTAMPDIFF(DAY,from_unixtime(ctime),now()) <7")->group('DATE_FORMAT(from_unixtime(ctime),"%Y-%m-%d")')->select();
        $data = get_lately_days($day, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }

    /**
     * 按天统计当天下单活跃会员
     * @param $day
     * @return array
     */
    public function statisticsOrder($day)
    {
        $orderModel = new Order();
        $res        = [];
        for ($i = 0; $i < $day; $i++) {
            $where    = [];
            $curr_day = date('Y-m-d');
            if ($i == 0) {
                $where[]  = ['ctime', '<', time()];
                $curr_day = date('Y-m-d');
            } else {
                $where[]  = ['ctime', '<', strtotime(date("Y-m-d", strtotime("-" . $i . " day")) . ' 23:59:59')];
                $curr_day = date("Y-m-d", strtotime("-" . $i . " day"));
            }
            $where[] = ['ctime', '>=', strtotime(date("Y-m-d", strtotime("-" . $i . " day")) . ' 00:00:00')];
            $res[]   =
                [
                    'nums' => $orderModel->where($where)->group('user_id')->count(),
                    'day'  => $curr_day
                ];
        }

        $data = get_lately_days($day, $res);
        return ['day' => $data['day'], 'data' => $data['data']];
    }

    // 用户中心
    public function getUserProduct($user_id = 0,$status = 1,$page = 1,$limit = 10,$keyword = '',$is_draft = -1)
    {
        $result = [
            'status' => true,
            'data' => [],
            'msg' => '',
        ];

        $orderModel = new \app\common\model\Order();
        $orderItemsModel = new \app\common\model\OrderItems();
        $userPriceModel = new \app\common\model\UserPrice();
        $articleModel = new \app\common\model\Article();
        $musicModel = new \app\common\model\Files();
        $gameModel = new \app\common\model\Game();
        $imgModel = new \app\common\model\Pictures();
        $goodsModel = new \app\common\model\Goods();
        $careModel = new \app\common\model\UserCare();
        $workModel = new \app\common\model\UserWorks();
        $cateModel = new \app\common\model\ArticleType();
        $saleModel = new \app\common\model\UserSale();
        $extendModel = new \app\common\model\GoodsExtend();
        $businessModel = new \app\common\model\Business();
        $haveModel = new \app\common\model\UserHave();
        $extendModel = new \app\common\model\GoodsExtend();
        $userModel = new \app\common\model\User();
        $lockModel = new \app\common\model\UserUnlockLog();


        $list = [];
        $count = 0;
        if($status == 1){
            // 已收藏,已购买的NFT
            $where = [];
            $ids = searchList($keyword);
            $new_ids = [];
            // 排除创建者
            $work_ids = $workModel->where('user_id',$user_id)->column('obj_id');
            $sale_ids = $saleModel->where('user_id','neq',$user_id)->where(['status' => 0])->column('obj_id');
            
            if(!empty($work_ids)){
                
                // $h_map[] = ['nft_id','NOT IN',$work_ids];
                if(!empty($sale_ids)){
                    
                    $h_map[] = ['nft_id','NOT IN',getArrCommon($work_ids+$sale_ids)];
                }else{
                    $h_map[] = ['nft_id','NOT IN',$work_ids];
                }
            }else{
                if(!empty($sale_ids)){
                    $h_map[] = ['nft_id','NOT IN',$sale_ids];
                }
            }
           
            $h_map[] = ['user_id','eq',$user_id];
            $new_ids = $haveModel->where($h_map)->column('nft_id');
           
            // $new_ids = array_diff($have_ids,$work_ids);
            // 排除寄售
            
            // $new_ids = array_diff($new_ids,$sale_ids);

            // if(!empty($work_ids)){
                // array_diff($ids,);
                // 判断是否是搜索
                if($ids != -1){
                    $ids = explode(',',$ids);
                    $where[] = ['nft_id','in',array_diff_assoc($ids,$new_ids)];
                    $where[] = ['user_id','eq',$user_id];
                }else{
                    $where[] = ['user_id','eq',$user_id];
                    $where[] = ['nft_id','in',$new_ids];
                }
            // }else{
            //     if($ids != -1){
            //         $where[] = ['nft_id','in',$ids];
            //         $where[] = ['user_id','eq',$user_id];
            //     }else{
            //         $where[] = ['user_id','eq',$user_id];
            //     }
            // }


            $has_data = $haveModel->where($where)->order('id desc')->select();
            $count = $haveModel->where($where)->count();

            if(!empty($has_data)){

                // 搜索
                foreach ($has_data as $key => $value) {
                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['nft_id'])->field('id,title,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['nft_id'])->field('id,name,price,code,is_market,url,path')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['nft_id'])->field('g_id,game_name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['nft_id'])->field('id,name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }

                    }elseif($value['type'] == 5){
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['nft_id'])->field('id,name,price,bn,is_market')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$temp_data['id'])->value('role_img')).'?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp';
                        }
                    }else{
                        $card_config = getMultipleSetting('card_img,card_price');

                        // 卡券
                        $userCardModel = new \app\common\model\UserCard();
                        $cardInfo = $userCardModel->where('id',$value['nft_id'])->find();

                        $list[$key]['goods_id'] = $cardInfo['id'];
                        $list[$key]['price'] = $card_config['card_price'];
                        $list[$key]['name'] = '卡券';
                        $list[$key]['is_market'] = 0;
                        $list[$key]['type'] = 6;
                        $list[$key]['extend'] = '';
                        $list[$key]['sn'] = $cardInfo['code'];
                        $list[$key]['image_url'] = _sImage($card_config['card_img']);
                    }
                }
                // $goodsIds = $orderItemsModel->whereIn('order_id',$order_ids)->column('goods_id');
                // if(!empty($goodsIds)){
                    // $goods_arr = $goodsModel->whereIn('id',$goodsIds)->column('id');
                    // if(!empty($goods_arr)){
                        // $list = $orderItemsModel->whereIn('order_id',$order_ids)->whereIn('goods_id',$goodsIds)->where($where)->field('goods_id,price,image_url,type,sn,name,extend,is_market')->order('order_id desc')->page($page, $limit)->select();
                        // foreach ($list as $key => $value) {
                        //     if($value['type'] == 5){
                        //         $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$value['goods_id'])->value('role_img'));
                        //     }else{
                        //         $list[$key]['image_url'] = _sImage($value['image_url']);
                        //     }
                        
                        // }

                        // $count = $orderItemsModel->whereIn('order_id',$order_ids)->whereIn('goods_id',$goodsIds)->where($where)->count();
                    // }else{
                    //     $list = [];
                    //     $count = 0;
                    // }
                // }else{
                //     $list = [];
                //     $count = 0;
                // }
                
                // dump($orderItemsModel->getLastSql());die;
            }
            
        }elseif($status == 2){
            // 已创建
            $ids = searchList($keyword);
            if($ids != -1){
                $where[] = ['obj_id','in',$ids];
            }

            $where[] = ['user_id','=',$user_id];
            // $where[] = ['is_market','=',0];
            $where[] = ['is_draft','=',2];

            $work_arr = $workModel->where($where)->order('ctime desc')->page($page, $limit)->select();

            $count = $workModel->where($where)->count();

            if(!empty($work_arr)){
                foreach ($work_arr as $key => $value) {
                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['obj_id'])->field('id,title,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,url,path')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['obj_id'])->field('g_id,game_name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }

                    }else{
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['obj_id'])->field('id,name,price,bn,is_market,image_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$temp_data['id'])->value('role_img')).'?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp';
                        }
                    }
                }
            }
            
        }elseif($status == 3){
            // 寄售
            $ids = searchList($keyword);
            if($ids != -1){
                $where[] = ['obj_id','in',$ids];
            }

            $now_time = time();

            $where[] = ['user_id','=',$user_id];
            // 结束时间小于当前时间
            $where[] = ['end_time','>=',$now_time];
            // $where[] = ['is_cancel','eq',0];
            $where[] = ['status','eq',0];

            $sale_arr = $saleModel->where($where)->order('id desc')->page($page, $limit)->select();
            // dump($saleModel->getLastsql());die;
            $count = $saleModel->where($where)->count();

            if(!empty($sale_arr)){
                foreach ($sale_arr as $key => $value) {
                    if($value['obj_type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['obj_id'])->field('id,title,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['obj_type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,url,path')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                        }

                    }elseif($value['obj_type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['obj_id'])->field('g_id,game_name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['obj_type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }

                    }else{
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['obj_id'])->field('id,name,price,bn,is_market')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$temp_data['id'])->value('role_img')).'?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp';
                        }
                    }
                }
            }
        }elseif($status == 4){
            // 拍卖
        }elseif($status == 5){
            // 出价
            $ids = searchList($keyword);
            if($ids != -1){
                $where[] = ['p_id','in',$ids];
                $cwhere[] = ['p_id','in',$ids];
            }

            $where[] = ['user_id','=',$user_id];
            $cwhere[] = ['receive_id','=',$user_id];
            $ids_arr = $userPriceModel->where($where)->whereOr($cwhere)->order('ctime desc')->page($page, $limit)->select();

            $count = $userPriceModel->where($where)->whereOr($cwhere)->count();
            if(!empty($ids_arr)){
                foreach ($ids_arr as $key => $value) {
                    $low_price = 0;//地板价

                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['p_id'])->field('id,title,price,code,is_market,cover,type_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                            $low_price = $articleModel->where('type_id',$temp_data['type_id'])->order('price asc')->value('price');
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['p_id'])->field('id,name,price,code,is_market,url,path,cat_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                            $low_price = $musicModel->where('cat_id',$temp_data['cat_id'])->order('price asc')->value('price');
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['p_id'])->field('g_id,game_name,price,code,is_market,cover,cate_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                            $low_price = $gameModel->where('cate_id',$temp_data['cate_id'])->order('price asc')->value('price');
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['p_id'])->field('id,name,price,code,is_market,cover,cate_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                            $low_price = $imgModel->where('cate_id',$temp_data['cate_id'])->order('price asc')->value('price');
                        }

                    }else{
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['p_id'])->field('id,name,price,bn,is_market,image_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$temp_data['id'])->value('role_img')).'?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp';
                            $low_price = $goodsModel->order('price asc')->value('price');
                        }
                    }

                    // 出价时间
                    $list[$key]['start_time'] = date('Y-m-d H:i:s',$value['s_time']);
                    $list[$key]['end_time'] = date('Y-m-d H:i:s',$value['e_time']);

                    // 查询地板差异
                    $list[$key]['price_diff'] = ceil((abs($list[$key]['price']-$low_price))/100);

                    // 出价id
                    $list[$key]['price_id'] = $value['id'];
                    // 是否是我作品的出价
                    $list[$key]['is_agree'] = $value['receive_id'] == $user_id;
                }
                
            }
            
        }elseif($status == 6){
            // 成交记录
            $ids = searchList($keyword);
            if($ids != -1){
                $where[] = ['obj_id','in',$ids];
                $orwhere[] = ['obj_id','in',$ids];
            }

            $where[] = ['user_id','=',$user_id];
            $orwhere[] = ['receive_id','=',$user_id];

            $ids_arr = $businessModel->where(function ($query) use ($where) {
                $query->where($where);
            })->whereOr(function ($query) use ($orwhere) {
                $query->where($orwhere);
            })->order('id desc')->page($page, $limit)->select();

            // $ids_arr = $businessModel->where($where)->order('id desc')->page($page, $limit)->select();

            $count = $businessModel->where(function ($query) use ($where) {
                $query->where($where);
            })->whereOr(function ($query) use ($orwhere) {
                $query->where($orwhere);
            })->count();

            if(!empty($ids_arr)){
                foreach ($ids_arr as $key => $value) {
                    $low_price = 0;//地板价


                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['obj_id'])->field('id,title,price,code,is_market,cover,type_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                            // $low_price = $articleModel->where('type_id',$temp_data['type_id'])->order('price asc')->value('price');
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,url,path,cat_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                            // $low_price = $musicModel->where('cat_id',$temp_data['cat_id'])->order('price asc')->value('price');
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['obj_id'])->field('g_id,game_name,price,code,is_market,cover,cate_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                            // $low_price = $gameModel->where('cate_id',$temp_data['cate_id'])->order('price asc')->value('price');
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,cover,cate_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                            // $low_price = $imgModel->where('cate_id',$temp_data['cate_id'])->order('price asc')->value('price');
                        }

                    }else{
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['obj_id'])->field('id,name,price,bn,is_market,image_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            // $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['price'] = $value['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$temp_data['id'])->value('role_img')).'?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp';
                            // $low_price = $goodsModel->order('price asc')->value('price');
                        }
                    }

                    // 成交时间
                    // $list[$key]['start_time'] = date('Y-m-d H:i:s',$value['s_time']);
                    $list[$key]['end_time'] = $value['ctime'];

                    // 查询地板差异
                    $list[$key]['price_diff'] = ceil((abs($list[$key]['price']-$low_price))/100);

                    // 出价人
                    $list[$key]['ower_name'] = $userModel->where('id',$value['receive_id'])->value('username');
                    // 购买人
                    $list[$key]['buyer_name'] = $userModel->where('id',$value['user_id'])->value('username');

                    // 出价id
                    // $list[$key]['price_id'] = $value['id'];
                    // 是否是我作品的出价
                    // $list[$key]['is_agree'] = $value['receive_id'] == $user_id;
                }
                
            }
        }elseif($status == 7){
            // 喜欢
            $ids = searchList($keyword);
            if($ids != -1){
                $where[] = ['obj_id','in',$ids];
            }

            $where[] = ['user_id','=',$user_id];

            $idsArr = $careModel->where($where)->order('id desc')->page($page, $limit)->select();
            if(!empty($idsArr)){
                foreach ($idsArr as $key => $value) {
                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['obj_id'])->field('id,title,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,url,path')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['obj_id'])->field('g_id,game_name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }

                    }else{
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['obj_id'])->field('id,name,price,bn,is_market,image_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($temp_data['image_id']);
                        }
                    }

                }

                $count = $careModel->where($where)->count();
            }
        }elseif($status == 8){
            // 草稿 -待定

            $ids = searchList($keyword);
            if($ids != -1){
                $where[] = ['obj_id','in',$ids];
            }

            $where[] = ['user_id','=',$user_id];
            // if($is_draft != -1){
            //     $where[] = ['is_draft','<',2];
            // }

            $work_arr = $workModel->where($where)->order('ctime desc')->page($page, $limit)->select();

            $count = $workModel->where($where)->count();

            if(!empty($work_arr)){
                foreach ($work_arr as $key => $value) {
                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['obj_id'])->field('id,title,price,code,is_market,cover,type_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['cate_name'] = $cateModel->where('id',$temp_data['type_id'])->value('type_name');
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['is_draft'] = $value['is_draft'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,url,path,cat_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['cate_name'] = $cateModel->where('id',$temp_data['cat_id'])->value('type_name');
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['is_draft'] = $value['is_draft'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['obj_id'])->field('g_id,game_name,price,code,is_market,cover,cate_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['cate_name'] = $cateModel->where('id',$temp_data['cate_id'])->value('type_name');
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['is_draft'] = $value['is_draft'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['obj_id'])->field('id,name,price,code,is_market,cover,cate_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['cate_name'] = $cateModel->where('id',$temp_data['cate_id'])->value('type_name');
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['is_draft'] = $value['is_draft'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }

                    }else{
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['obj_id'])->field('id,name,price,bn,is_market,image_id')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($temp_data['image_id']);
                        }
                    }

                    $list[$key]['work_id'] = $value['id'];
                    if($value['is_draft'] == 0){
                        $status_txt = '待审核';
                    }elseif($value['is_draft'] == 1){
                        $status_txt = '';
                    }elseif($value['is_draft'] == 2){
                        $status_txt = '通过';
                    }else{
                        $status_txt = '驳回';
                    }
                    $list[$key]['status'] = $status_txt;//审核状态:0待审核1草稿2通过3关闭
                }
            }
        }elseif($status == 9){
            // 已解锁
            $lwhere = [];
            $ids = searchList($keyword);
            if($ids != -1){
                $ids = explode(',',$ids);
                $lwhere[] = ['nft_id','in',$ids];
                $lwhere[] = ['user_id','eq',$user_id];
            }else{
                $lwhere[] = ['user_id','eq',$user_id];
                // $where[] = ['nft_id','in',$new_ids];
            }

            $lock_data = $lockModel->where($lwhere)->order('id desc')->select();
            $count = $lockModel->where($lwhere)->count();

            if(!empty($lock_data)){

                // 搜索
                foreach ($lock_data as $key => $value) {
                    if($value['type'] == 1){
                        // 文章
                        $temp_data = $articleModel->where('id',$value['nft_id'])->field('id,title,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['title'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 1;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 2){
                        // 音频
                        $temp_data = $musicModel->where('id',$value['nft_id'])->field('id,name,price,code,is_market,url,path')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 2;
                            $list[$key]['extend'] = $temp_data['url'];
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['path']);
                        }

                    }elseif($value['type'] == 3){
                        // 游戏
                        $temp_data = $gameModel->where('g_id',$value['nft_id'])->field('g_id,game_name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['g_id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['game_name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 3;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }
                    }elseif($value['type'] == 4){
                        // 插画
                        $temp_data = $imgModel->where('id',$value['nft_id'])->field('id,name,price,code,is_market,cover')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 4;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['code'];
                            $list[$key]['image_url'] = _sImage($temp_data['cover']);
                        }

                    }elseif($value['type'] == 5){
                        // 角色
                        $temp_data = $goodsModel->where('id',$value['nft_id'])->field('id,name,price,bn,is_market')->find();
                        if(!empty($temp_data)){
                            $list[$key]['goods_id'] = $temp_data['id'];
                            $list[$key]['price'] = $temp_data['price'];
                            $list[$key]['name'] = $temp_data['name'];
                            $list[$key]['is_market'] = $temp_data['is_market'];
                            $list[$key]['type'] = 5;
                            $list[$key]['extend'] = '';
                            $list[$key]['sn'] = $temp_data['bn'];
                            $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$temp_data['id'])->value('role_img')).'?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp';
                        }
                    }else{
                        
                    }
                }
                // $goodsIds = $orderItemsModel->whereIn('order_id',$order_ids)->column('goods_id');
                // if(!empty($goodsIds)){
                    // $goods_arr = $goodsModel->whereIn('id',$goodsIds)->column('id');
                    // if(!empty($goods_arr)){
                        // $list = $orderItemsModel->whereIn('order_id',$order_ids)->whereIn('goods_id',$goodsIds)->where($where)->field('goods_id,price,image_url,type,sn,name,extend,is_market')->order('order_id desc')->page($page, $limit)->select();
                        // foreach ($list as $key => $value) {
                        //     if($value['type'] == 5){
                        //         $list[$key]['image_url'] = _sImage($extendModel->where('goods_id',$value['goods_id'])->value('role_img'));
                        //     }else{
                        //         $list[$key]['image_url'] = _sImage($value['image_url']);
                        //     }
                        
                        // }

                        // $count = $orderItemsModel->whereIn('order_id',$order_ids)->whereIn('goods_id',$goodsIds)->where($where)->count();
                    // }else{
                    //     $list = [];
                    //     $count = 0;
                    // }
                // }else{
                //     $list = [];
                //     $count = 0;
                // }
                
                // dump($orderItemsModel->getLastSql());die;
            }

        }

        $result['data'] = [
            'list' => $list,
            'count' => $count,
            'page' => $page,
            'limit' => $limit
        ];

        return $result;
    }

    // 添加藏品
    public function addIseKai($user_id , $params = array())
    {
        if(empty($params['name'])){
            return error_code(10202);
        }

        if(empty($params['desc'])){
            return error_code(10203);
        }

        if(empty($params['image_id'])){
            return error_code(10204);
        }

        if(empty($params['role_id'])){
            return error_code(12015);
        }
        // if(empty($params['royalties'])){
        //     return error_code(10205);
        // }

        // if(empty($params['wallet_url'])){
        //     return error_code(10206);
        // }
        
        if($params['type'] == 1){
            // 文章
            $articleModel = new \app\common\model\Article();
            $articleChapterModel = new \app\common\model\ArticleChapter(); 

            if(session('lng') == 'eng'){
                $name_key = 'title_eng';
                $desc_key = 'brief_eng';
            }else{
                $name_key = 'title';
                $desc_key = 'brief';
            }

            // 文章章节
            $chapter_info = [];

            $data = [
                $name_key => $params['name'],//项目名称
                $desc_key => $params['desc'],//项目描述
                'cover' => $params['image_id'],//项目封面图片id
                'file_url' => $params['url'],//文件地址
                'links' => $params['links'],//外部链接
                // 'wallet_type' => $params['wallet_type'],//钱包类型:1.平台币2.以太坊
                'type_id' => $params['cate_id'],//专辑id
                // 'attr' => $params['attr'],//属性多个用,(英文)号隔开
                // 'is_market' => $params['is_market'],//是否投放市场0否1是
                'buy_lock' => $params['buy_lock'],//购买后解锁0否1是
                // 'royalties' => $params['royalties'],//版税
                // 'wallet_url' => $params['wallet_url'],//钱包地址
                'role_id' => $params['role_id'],//角色id
                // 'price' => $params['price'],//价格
                'ctime' => time(),
                'user_id' => $user_id,
            ];

            if(isset($params['id']) && !empty($params['id'])){
                $articleModel->where('id',$params['id'])->update($data);
                // 先删后加
                $articleChapterModel->where('article_id',$params['id'])->delete();
                $res = $params['id'];

            }else{
                $data['code'] = makeCode();
                $res = $articleModel->insertGetId($data);
            }

            if(isset($params['chapter']) && !empty($params['chapter'])){
                foreach ($params['chapter'] as $key => $value) {
                    $chapter_info[] = [
                        'url' => $value['url'],
                        'name' => $value['name'],
                        'index' => '第'.($key+1).'章',
                        'article_id' => $res
                    ];
                }

                $articleChapterModel->saveAll($chapter_info);
            }


        }elseif($params['type'] == 2){
            // 音频
            $musicModel = new \app\common\model\Files();

            if(session('lng') == 'eng'){
                $name_key = 'name_eng';
                $desc_key = 'desc_eng';
            }else{
                $name_key = 'name';
                $desc_key = 'desc';
            }

            $data = [
                $name_key => $params['name'],//项目名称
                $desc_key => $params['desc'],//项目描述
                'path' => $params['image_id'],//项目封面图片id
                'url' => $params['url'],//文件地址
                'links' => $params['links'],//外部链接
                // 'wallet_type' => $params['wallet_type'],//钱包类型:1.平台币2.以太坊
                'type' => $params['music_type'],//音频类型:2配音3背景音乐4音效
                'cat_id' => $params['cate_id'],//专辑id
                // 'attr' => $params['attr'],//属性多个用,(英文)号隔开
                // 'is_market' => $params['is_market'],//是否投放市场0否1是
                'buy_lock' => $params['buy_lock'],//购买后解锁0否1是
                // 'royalties' => $params['royalties'],//版税
                // 'wallet_url' => $params['wallet_url'],//钱包地址
                'role_id' => $params['role_id'],//角色id
                // 'price' => $params['price'],//价格
                'file_type' => 'music',
                'ctime' => time(),
                'user_id' => $user_id,
            ];

            if(isset($params['id']) && !empty($params['id'])){
                $musicModel->where('id',$params['id'])->update($data);
                $res = $params['id'];
            }else{
                $data['code'] = makeCode();
                $res = $musicModel->insertGetId($data);
            }
        }elseif($params['type'] == 3){
            // 游戏
            $gameModel = new \app\common\model\Game();

            if(session('lng') == 'eng'){
                $name_key = 'game_name_eng';
                $desc_key = 'game_content_eng';
            }else{
                $name_key = 'game_name';
                $desc_key = 'game_content';
            }

            $data = [
                $name_key => $params['name'],//项目名称
                'game_content' => $params['desc'],//项目描述
                'cover' => $params['image_id'],//项目封面图片id
                'ctime' => time(),
                'links' => $params['links'],//外部链接
                // 'wallet_type' => $params['wallet_type'],//钱包类型:1.平台币2.以太坊
                'cate_id' => $params['cate_id'],//专辑id
                // 'attr' => $params['attr'],//属性多个用,(英文)号隔开
                // 'is_market' => $params['is_market'],//是否投放市场0否1是
                'buy_lock' => $params['buy_lock'],//购买后解锁0否1是
                // 'royalties' => $params['royalties'],//版税
                // 'wallet_url' => $params['wallet_url'],//钱包地址
                'role_id' => $params['role_id'],//角色id
                // 'price' => $params['price'],//价格
                'user_id' => $user_id,
            ];

            if(isset($params['id']) && !empty($params['id'])){
                $gameModel->where('g_id',$params['id'])->update($data);
                $res = $params['id'];
            }else{
                $data['code'] = makeCode();
                $res = $gameModel->insertGetId($data);
            }
        }elseif($params['type'] == 4){
            // 插画
            $imgModel = new \app\common\model\Pictures();

            if(session('lng') == 'eng'){
                $name_key = 'name_eng';
                $desc_key = 'desc_eng';
            }else{
                $name_key = 'name';
                $desc_key = 'desc';
            }

            $data = [
                'name' => $params['name'],//项目名称
                'desc' => $params['desc'],//项目描述
                'cover' => $params['image_id'],//项目封面图片id
                'path' => $params['url'],//文件地址
                'links' => $params['links'],//外部链接
                // 'wallet_type' => $params['wallet_type'],//钱包类型:1.平台币2.以太坊
                'cate_id' => $params['cate_id'],//专辑id
                // 'attr' => $params['attr'],//属性多个用,(英文)号隔开
                // 'is_market' => $params['is_market'],//是否投放市场0否1是
                'buy_lock' => $params['buy_lock'],//购买后解锁0否1是
                // 'royalties' => $params['royalties'],//版税
                // 'wallet_url' => $params['wallet_url'],//钱包地址
                'role_id' => $params['role_id'],//角色id
                'type' => $params['img_type'],//插画类型:1封面2立绘3背景
                // 'price' => $params['price'],//价格
                'user_id' => $user_id,
            ];

            if(isset($params['id']) && !empty($params['id'])){
                $imgModel->where('id',$params['id'])->update($data);
                $res = $params['id'];
            }else{
                $data['code'] = makeCode();
                $res = $imgModel->insertGetId($data);
            }


        }else{}

        // 增加发行方式
        $extend_params = [];
        if(isset($params['post_type']) && !empty($params['post_type'])){
            $extend_params['post_type'] = implode(',', $params['post_type']);
            $extend_params['experience_type'] = $params['experience_type'];
            $extend_params['experience_price'] = $params['experience_price'];
            $extend_params['digitization_number'] = $params['digitization_number'];
            $extend_params['digitization_price'] = $params['digitization_price'];
            $extend_params['copyright_number'] = $params['copyright_number'];
            $extend_params['copyright_price'] = $params['copyright_price'];
            $extend_params['end_time'] = isset($params['end_time']) ? $params['end_time'] : 0;
        }
        // 插画不存储数字化信息
        // if($params['type'] == 4){
        //     $extend_params = [];
        // }

        $workModel = new \app\common\model\UserWorks();
        $wallectModel = new \app\common\model\userWallet();

        $work_id = $workModel->doMyWork($user_id,$res,$params['type'],0,$params['is_draft'],$params['work_id'],$extend_params);
        $user_address = $wallectModel->where(['user_id' => $user_id])->order('is_default desc')->value('address');
        makeMyNft($res,$params['type'],$user_id,1,$extend_params,$user_address);
        // mintNFT($user_id,$res,$params['type']);

        return $res ? ['status' => true,'msg' => '成功','data' => $res,'work_id' => $work_id] : error_code(10004);
    }

    // 获取藏品
    public function getIseKai($user_id,$work_id,$goods_id)
    {
        $workModel = new \app\common\model\UserWorks();
        $work_info = $workModel->where('id',$work_id)->find();
        // dump($work_info);die;
        // 组装返回的数据
        if(!empty($work_info)){
            if($work_info['type'] == 1){
                // 文章
                $articleModel = new \app\common\model\Article();
                $chapterModel = new \app\common\model\ArticleChapter();

                $article = $articleModel->where('id',$work_info['obj_id'])->find();

                $data = [
                    'name' => $article['title'],//项目名称
                    'desc' => $article['brief'],//项目描述
                    'image_id' => $article['cover'],//项目封面图片id
                    'image_Url' => _sImage($article['cover']),//项目封面图片
                    'music_type' => 1,
                    'img_type' => 1,
                    'url' => $article['file_url'],//文件地址
                    'links' => $article['links'],//外部链接
                    // 'wallet_type' => $article['wallet_type'],//钱包类型:1.平台币2.以太坊
                    'cate_id' => empty($article['type_id']) ? '' : $article['type_id'],//专辑id
                    // 'attr' => $article['attr'],//属性多个用,(英文)号隔开
                    // 'is_market' => $article['is_market'],//是否投放市场0否1是
                    'buy_lock' => $article['buy_lock'],//购买后解锁0否1是
                    // 'royalties' => $article['royalties'],//版税
                    // 'wallet_url' => $article['wallet_url'],//钱包地址
                    'role_id' => $article['role_id'],//角色id
                    'price' => $article['price'],//价格
                    'user_id' => $article['user_id'],
                    'work_id' => $work_info['id'],
                    'id' => $work_info['obj_id']
                ];

                $data['chapter'] = $chapterModel->field('url,name')->where('article_id',$work_info['obj_id'])->order('index asc')->select();
            }elseif($work_info['type'] == 2){
                // 音频
                $musicModel = new \app\common\model\Files();
                $music = $musicModel->where('id',$work_info['obj_id'])->find();

                $data = [
                    'name' => $music['name'],//项目名称
                    'desc' => $music['desc'],//项目描述
                    'image_id' => $music['path'],//项目封面图片id
                    'image_Url' => _sImage($music['path']),//项目封面图片id
                    'url' => $music['url'],//文件地址
                    'links' => $music['links'],//外部链接
                    // 'wallet_type' => $music['wallet_type'],//钱包类型:1.平台币2.以太坊
                    'music_type' => $music['type'],//音频类型:2配音3背景音乐4音效
                    'img_type' => 1,
                    'cate_id' => empty($music['cat_id']) ? '' : $music['cat_id'],//专辑id
                    // 'attr' => $music['attr'],//属性多个用,(英文)号隔开
                    // 'is_market' => $music['is_market'],//是否投放市场0否1是
                    'buy_lock' => $music['buy_lock'],//购买后解锁0否1是
                    // 'royalties' => $music['royalties'],//版税
                    // 'wallet_url' => $music['wallet_url'],//钱包地址
                    'role_id' => $music['role_id'],//角色id
                    'price' => $music['price'],//价格
                    'user_id' => $music['user_id'],
                    'work_id' => $work_info['id'],
                    'id' => $work_info['obj_id']
                ];
                
            }elseif($work_info['type'] == 3){
                // 游戏
                $gameModel = new \app\common\model\Game();
                $game = $gameModel->where('g_id',$work_info['obj_id'])->find();

                $data = [
                    'name' => $game['game_name'],//项目名称
                    'desc' => $game['game_content'],//项目描述
                    'image_id' => $game['cover'],//项目封面图片id
                    'image_Url' => _sImage($game['cover']),
                    'music_type' => 1,
                    'img_type' => 1,
                    'links' => $game['links'],//外部链接
                    // 'wallet_type' => $game['wallet_type'],//钱包类型:1.平台币2.以太坊
                    'cate_id' => empty($game['cate_id']) ? '' : $game['cate_id'],//专辑id
                    // 'attr' => $game['attr'],//属性多个用,(英文)号隔开
                    // 'is_market' => $game['is_market'],//是否投放市场0否1是
                    'buy_lock' => $game['buy_lock'],//购买后解锁0否1是
                    // 'royalties' => $game['royalties'],//版税
                    // 'wallet_url' => $game['wallet_url'],//钱包地址
                    'role_id' => $game['role_id'],//角色id
                    'price' => $game['price'],//价格
                    'user_id' => $game['user_id'],
                    'work_id' => $work_info['id'],
                    'id' => $work_info['obj_id']
                ];
                
            }elseif($work_info['type'] == 4){
                // 插画
                $imgModel = new \app\common\model\Pictures();
                $img = $imgModel->where('id',$work_info['obj_id'])->find();

                $data = [
                    'name' => $img['name'],//项目名称
                    'desc' => $img['desc'],//项目描述
                    'image_id' => $img['cover'],//项目封面图片id
                    'image_Url' => _sImage($img['cover']),
                    'music_type' => 1,
                    'img_type' => $img['type'],
                    'url' => $img['path'],//文件地址
                    'links' => $img['links'],//外部链接
                    // 'wallet_type' => $img['wallet_type'],//钱包类型:1.平台币2.以太坊
                    'cate_id' => empty($img['cate_id']) ? '' : $img['cate_id'],//专辑id
                    // 'attr' => $img['attr'],//属性多个用,(英文)号隔开
                    // 'is_market' => $img['is_market'],//是否投放市场0否1是
                    'buy_lock' => $img['buy_lock'],//购买后解锁0否1是
                    // 'royalties' => $img['royalties'],//版税
                    // 'wallet_url' => $img['wallet_url'],//钱包地址
                    'role_id' => $img['role_id'],//角色id
                    'price' => $img['price'],//价格
                    'user_id' => $img['user_id'],
                    'work_id' => $work_info['id'],
                    'id' => $work_info['obj_id']
                ];


            }else{}

            $data['post_type'] = !empty($work_info['post_type']) ? explode(',', $work_info['post_type']) : [];
            $data['experience_type'] = $work_info['experience_type'];
            $data['experience_price'] = $work_info['experience_price'];
            $data['digitization_number'] = $work_info['digitization_number'];
            $data['digitization_price'] = $work_info['digitization_price'];
            $data['copyright_number'] = $work_info['copyright_number'];
            $data['copyright_price'] = $work_info['copyright_price'];
            $data['type'] = $work_info['type'];
            $data['is_draft'] = $work_info['is_draft'];
            $data['end_time'] = $work_info['end_time'];
            $data['method'] = 'notice.addisekai';

            return ['status' => true,'msg' => '成功','data' => $data];
        }else{
            return error_code(10002);
        }
       

    }

    // 删除藏品
    public function removeIseKai($user_id,$work_id,$goods_id)
    {
        $orderModel = new \app\common\model\Order();
        $orderItemsModel = new \app\common\model\OrderItems();
        $userPriceModel = new \app\common\model\UserPrice();
        $articleModel = new \app\common\model\Article();
        $musicModel = new \app\common\model\Files();
        $gameModel = new \app\common\model\Game();
        $imgModel = new \app\common\model\Pictures();
        $goodsModel = new \app\common\model\Goods();
        $careModel = new \app\common\model\UserCare();
        $workModel = new \app\common\model\UserWorks();
        $saleModel = new \app\common\model\UserSale();

        $info = $workModel->where('id',$work_id)->find();

        if($info){
            if($info['type'] == 1){
                $articleModel->where('id',$info['obj_id'])->delete();
            }elseif($info['type'] == 2){
                $musicModel->where('id',$info['obj_id'])->delete();
            }elseif($info['type'] == 3){
                $gameModel->where('g_id',$info['obj_id'])->delete();
            }else{
                $imgModel->where('id',$info['obj_id'])->delete();
            }

            // 删除关联数据
            $res = $workModel->where('id',$work_id)->delete();

            // 删除出价
            $userPriceModel->where(['p_id' => $info['obj_id'],'type' => $info['type']])->delete();
            // 删除寄售
            $saleModel->where(['obj_id' => $info['obj_id'],'obj_type' => $info['type']])->delete();
            // 删除点赞
            $careModel->where(['obj_id' => $info['obj_id'],'type' => $info['type']])->delete();
            // 用户购买
            $orderItemsModel->where(['goods_id' => $info['obj_id'],'type' => $info['type']])->delete();

            return $res ? ['status' => true,'msg' => '删除成功'] : error_code(10023);
        }else{
            return error_code(10002);
        }

    }
}
