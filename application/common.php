<?php
// 应用公共文件
use think\Cache;
use think\Config;
use think\Cookie;
use think\Db;
use think\Debug;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Lang;
use think\Loader;
use think\Log;
use think\Model;
use think\Request;
use think\Response;
use think\Session;
use think\Url;
use think\View;
use think\Container;
use app\common\model\Operation;
use app\common\model\Area;
use app\common\model\Payments;
use app\common\model\Logistics;
use org\Wx;
use org\Curl;

error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**
 * 返回当前的毫秒时间戳
 * @return string
 */
function msectime()
{
    list($tmp1, $tmp2) = explode(' ', microtime());
    return sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 1000);
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 * 判断前端浏览器类型
 * @return bool|string
 */
function get_client_broswer()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];

    //微信内置浏览器
    if (stripos($ua, 'MicroMessenger')) {
        //preg_match('/MicroMessenger\/([\d\.]+)/i', $ua, $match);
        return "weixin";
    }
    //支付宝内置浏览器
    if (stripos($ua, 'AlipayClient')) {
        //preg_match('/AlipayClient\/([\d\.]+)/i', $ua, $match);
        return "alipay";
    }
    return false;
}


/**
 * 生成编号
 * @param $type
 * @return string
 */
function get_sn($type)
{
    switch ($type) {
        case 1:         //订单编号
            $str = date('mdHis').mt_rand(10000, 99999);
            break;
        case 2:         //支付单编号
            $str = $type . substr(msectime() . rand(0, 9), 1);
            break;
        case 3:         //商品编号
            $str = 'G' . substr(msectime() . rand(0, 5), 1);
            break;
        case 4:         //货品编号
            $str = 'P' . substr(msectime() . rand(0, 5), 1);
            break;
        case 5:         //售后单编号
            $str = $type . substr(msectime() . rand(0, 9), 1);
            break;
        case 6:         //退款单编号
            $str = $type . substr(msectime() . rand(0, 9), 1);
            break;
        case 7:         //退货单编号
            $str = $type . substr(msectime() . rand(0, 9), 1);
            break;
        case 8:         //发货单编号
            $str = $type . substr(msectime() . rand(0, 9), 1);
            break;
        case 9:         //提货单号
            //$str = 'T'.$type.substr(msectime().rand(0,5), 1);
            $chars    = ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '2', '3', '4', '5', '6', '7', '8', '9'];
            $charsLen = count($chars) - 1;
            shuffle($chars);
            $str = '';
            for ($i = 0; $i < 6; $i++) {
                $str .= $chars[mt_rand(0, $charsLen)];
            }
            break;
        default:
            $str = substr(msectime() . rand(0, 9), 1);
    }
    return $str;
}


/**
 * 获取hash值
 * @return string
 */
function get_hash()
{
    $chars   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
    $random  = $chars[mt_rand(0, 73)] . $chars[mt_rand(0, 73)] . $chars[mt_rand(0, 73)] . $chars[mt_rand(0, 73)] . $chars[mt_rand(0, 73)];
    $content = uniqid() . $random;
    return sha1($content);
}


/**
 * @param $filename
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 11:32
 */
function get_file_extension($filename)
{
    $pathinfo = pathinfo($filename);
    return strtolower($pathinfo['extension']);
}


/***
 * 获取HASH目录
 * @param $name
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 15:26
 */
function get_hash_dir($name = 'default')
{
    $ident = sha1(uniqid('', true) . $name . microtime());
    $dir   = '/' . $ident{0} . $ident{1} . '/' . $ident{2} . $ident{3} . '/' . $ident{4} . $ident{5} . '/';
    return $dir;
}


/**
 *
 * +--------------------------------------------------------------------
 * Description 递归创建目录
 * +--------------------------------------------------------------------
 * @param  string $dir 需要创新的目录
 * +--------------------------------------------------------------------
 * @return 若目录存在,或创建成功则返回为TRUE
 * +--------------------------------------------------------------------
 * @author gongwen
 * +--------------------------------------------------------------------
 */
function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || mkdir($dir, $mode, true)) return true;
    if (!mkdirs(dirname($dir), $mode)) return false;
    return mkdir($dir, $mode, true);
}


/**
 * 返回图片地址
 * TODO 水印，裁剪，等操作
 * @param string $image_id
 * @param string $type
 * @return array|mixed|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-09 18:34
 */
function _sImage($image_id = '', $type = 's')
{
    if (!$image_id) {
        $image_id = getSetting('shop_default_image');//系统默认图片
        if (!$image_id) {
            return config('jshop.default_image');//默认图片
        }
    }

    if (stripos($image_id, 'http') !== false || stripos($image_id, 'https') !== false) {
        return $image_id;
    }

    $image_obj = new \app\common\model\Images();
    $image     = $image_obj->where([
        'id' => $image_id
    ])->field('url')->cache(true)->find();
    if ($image) {
        if (stripos($image['url'], 'http') !== false || stripos($image['url'], 'https') !== false) {
            return str_replace("\\", "/", $image['url']);
        } else {
            return request()->domain() . str_replace("\\", "/", $image['url']);
        }
    } else {
        return config('jshop.default_image');//默认图片
    }
}


/**
 * 相对地址转换为绝对地址
 * @param string $url
 * @return string
 */
function getRealUrl($url = '')
{
    if (stripos($url, 'http') !== false || stripos($url, 'https') !== false) {
        return $url;
    } else {
        $storage_params = getSetting('image_storage_params');
        if (isset($storage_params['domain']) && $storage_params['domain']) {
            return $storage_params['domain'] . $url;
        }
        if (config('jshop.image_storage.domain')) {
            return config('jshop.image_storage.domain') . $url;
        }
        return request()->domain() . $url;
    }
}


/**
 * 格式化数据化手机号码
 * @param $mobile
 * @return string
 */
function format_mobile($mobile)
{
    return substr($mobile, 0, 5) . "****" . substr($mobile, 9, 2);
}


/**
 * 如果没有登陆的情况下，记录来源url，并跳转到登陆页面
 * @return mixed|string
 */
function redirect_url($url = "")
{
    if (cookie('?redirect_url')) {
        $str = cookie('redirect_url');
        cookie('redirect_url', null);
    } else {
        if ($url) {
            $str = $url;
        } else {
            $str = '/';
        }

    }
    return $str;
}


/**
 * 返回用户信息
 * @param $user_id
 * @param string $field
 * @return string
 */
function get_user_info($user_id, $field = 'mobile')
{
    if (!$user_id) {
        return "";
    }
    $field_type = $field;
    if($field == 'showname'){
        $field = '*';
    }
    //用户增加缓存
    $user = \app\common\model\User::where('id','=',$user_id)->field($field)->find();
    if ($user) {
        if ($field_type == 'nickname') {
            $nickname = $user['nickname'];
            if ($nickname == '' && isset($user['mobile'])) {
                $nickname = format_mobile($user['mobile']);
            }
            return $nickname;
        }elseif($field_type == 'showname'){
            $str = $user['nickname'];
            $str .= "(".$user['id'].")";
            return $str;
        }elseif($field_type == '*'){
            $user['avatar'] = _sImage($user['avatar']);
            $user->hidden(['password']);
            return $user;
        }else if(stripos($field,',') != false){
            if(isset($user['avatar']))$user['avatar'] = _sImage($user['avatar']);
            $user->hidden(['password']);
            return $user;
        } else {
            return $user->$field;
        }
    } else {
        return "";
    }
}


/**
 * 返回商品信息
 * @param $goods_id
 * @param string $field
 * @return array|mixed|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_goods_info($goods_id, $field = 'name')
{
    $goodsModel = new \app\common\model\Goods();
    $info       = $goodsModel->where(['id' => $goods_id])->cache(86400)->find();
    if ($info) {
        if ($field == 'image_id') {
            return _sImage($info[$field]);
        } else {
            return $info[$field];
        }
    } else {
        return '';
    }
}


/**
 * 返回用户信息
 * @param $mobile
 * @return bool|mixed
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_user_id($mobile)
{
    $userModel = new app\common\model\User();
    $user      = $userModel->where(array('mobile' => $mobile))->find();
    if ($user) {
        return $user->id;
    } else {
        return false;
    }
}


/**
 * 获取转换后金额
 * @param int $money
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-02-01 15:32
 */
function getMoney($money = 0)
{
    return sprintf("%.2f", $money);
}


/**
 * 根据支付方式编码取支付方式名称等
 * @param $payment_code
 * @param string $field
 * @return mixed
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_payment_info($payment_code, $field = 'name')
{
    $paymentModel = new Payments();
    $paymentInfo  = $paymentModel->where(['code' => $payment_code])->find();
    if ($paymentInfo) {
        return $paymentInfo[$field];
    } else {
        return $payment_code;
    }
}


/**
 * 根据物流编码取物流名称等信息
 * @param $logi_code
 * @param string $field
 * @return mixed
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_logi_info($logi_code, $field = 'logi_name')
{
    $logisticsModel = new Logistics();
    $logiInfo       = $logisticsModel->where(['logi_code' => $logi_code])->find();
    if ($logiInfo) {
        return $logiInfo[$field];
    } else {
        return $logi_code;
    }
}


/**
 * 根据地区id取省市区的信息
 * @param $area_id
 * @return string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_area($area_id)
{
    $areaModel = new Area();
    $data      = $areaModel->getArea($area_id);
    $parse     = "";
    foreach ($data as $v) {
        if (isset($v['info'])) {
            $parse .= $v['info']['name'] . " ";
        }
    }
    return $parse;
}


/**
 * @param $code
 * @param bool $mini
 * @return array|mixed
 */
function error_code($code, $mini = false)
{
    $result = [
        'status' => false,
        'data'   => $code,
        'msg'    => config('error.10000')
    ];
    //有语言包就应用语言包
    $data = func_get_args();
    array_splice($data,1,1);
    $msg = jshop_l(...$data);
    if($msg == ""){
        if (config('?error.' . $code)) {
            $msg = config('error.' . $code);
            $count = count($data);
            $count--;
            for($i=1;$i<=$count;$i++){
                $msg = str_replace("{JSHOP".$i."}",$data[$i],$msg);
            }
        }
    }
    $result['msg'] = $msg;
    if ($mini) {
        return $result['msg'];
    } else {
        return $result;
    }
}
//语言包，前端和数据
function jshop_l($code){
    $data = func_get_args();
    $str = "";
    if (!config('?language_'.getLng().'.' . $code)) {
        return $str;
    }
    $str = config('language_'.getLng().'.' . $code);
    $count = count($data);
    $count--;
    for($i=1;$i<=$count;$i++){
        $str = str_replace("{JSHOP".$i."}",$data[$i],$str);
    }
    return $str;
}
//后端语言包,所有的后端的显示放到这里
function jshop_m_l($code){
    $data = func_get_args();
    $str = "";
    if (!config('?language_'.config('jshop.language').'.' . $code)) {
        return $str;
    }
    $str = config('language_'.config('jshop.language').'.' . $code);
    $count = count($data);
    $count--;
    for($i=1;$i<=$count;$i++){
        $str = str_replace("{JSHOP".$i."}",$data[$i],$str);
    }
    return $str;
}


/**
 * 删除数组中指定值
 * @param $arr
 * @param $value
 * @return mixed
 */
function unsetByValue($arr, $value)
{
    $keys = array_keys($arr, $value);
    if (!empty($keys)) {
        foreach ($keys as $key) {
            unset($arr[$key]);
        }
    }
    return $arr;
}


/**
 * 删除图片
 * @param $image_id
 * @return bool
 * @throws \think\Exception
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 * @throws \think\exception\PDOException
 */
function delImage($image_id)
{
    $image_obj = new \app\common\model\Images();
    $image     = $image_obj->where(['id' => $image_id])->find();
    if ($image) {
        //删除图片数据
        $res = $image_obj->where(['id' => $image_id])->delete();
        if ($image['type'] == 'local') {
            @unlink($image['path']);
        }
        //todo 其它存储引擎不调整
        if ($res) {
            return true;
        }
        //默认本地存储，返回本地域名图片地址
    } else {
        return false;
    }
}


/**
 * 查询标签
 * @param $ids
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getLabel($ids)
{
    if (!$ids) {
        return [];
    }
    $label_obj = new \app\common\model\Label();
    $labels    = $label_obj->field('name,style')->where('id', 'in', $ids)->select();
    if (!$labels->isEmpty()) {
        return $labels->toArray();
    }
    return [];
}


/**
 * @param $style
 * @return string
 */
function getLabelStyle($style)
{
    $label_style = '';
    switch ($style) {
        case 'red':
            $label_style = "";
            break;
        case 'green':
            $label_style = "layui-bg-green";
            break;
        case 'orange':
            $label_style = "layui-bg-orange";
            break;
        case 'blue':
            $label_style = "layui-bg-blue";
            break;
        default :
            $label_style = '';
    }
    return $label_style;
}


/**
 * 单位自动转换函数
 * @param $size
 * @return string
 */
function getRealSize($size)
{
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if ($size < $kb) {
        return $size . 'B';
    } else if ($size < $mb) {
        return round($size / $kb, 2) . 'KB';
    } else if ($size < $gb) {
        return round($size / $mb, 2) . 'MB';
    } else if ($size < $tb) {
        return round($size / $gb, 2) . 'GB';
    } else {
        return round($size / $tb, 2) . 'TB';
    }
}


/**
 * url参数转换为数组
 * @param $query
 * @return array
 */
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);
    $params     = array();
    foreach ($queryParts as $param) {
        $item             = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}


/**
 * bool型转义
 * @param string $value
 * @return mixed
 */
function getBool($value = '1')
{
    $bool = ['1' => '是', '2' => '否'];
    return $bool[$value];
}


/**
 * 时间格式化
 * @param int $time
 * @param bool|true $year
 * @return bool|string
 */
function getTime($time = 0,$year=true)
{
    if($year){
        return date('Y-m-d H:i:s', $time);
    }else{
        return date('m-d H:i:s', $time);
    }
}


/**
 * 标签转换
 * @param array $labels
 * @return string
 */
function getExportLabel($labels = [])
{
    $labelString = '';
    foreach ((array)$labels as $v) {
        $labelString = $v['name'] . ',';
    }
    return substr($labelString, 0, -1);
}


/**
 * 上下架状态转换
 * @param string $status
 * @return string
 */
function getMarketable($marketable = '1')
{
    $status = ['1' => '上架', '2' => '下架'];
    return $status[$marketable];
}


/**
 * 数组转xml
 * @param $arr
 * @param string $root
 * @return string
 */
function arrayToXml($arr, $root = "root")
{
    $xml = "<" . $root . ">";
    foreach ($arr as $key => $val) {
        if (is_array($val)) {

            $xml .= "<" . $key . ">" . arrayToXml($val, $root) . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        }
    }
    $xml .= "</" . $root . ">";
    return $xml;
}

/**
 * 数组转xml
 * @param $arr
 * @param string $root
 * @return string
 */
function arrayToXml2($arr, $root = 'root')
{
    $xml = "<" . $root . ">";
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            if (isset($val[0])) {
                foreach ($val as $skey => $sval) {
                    $xml .= arrayToXml2($sval, $key);
                }
            } else {
                $xml .= arrayToXml2($val, $key);
            }
        } else {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        }
    }
    $xml .= "</" . $root . ">";
    return $xml;
}

/**
 * 在模板中，有时候，新增的时候，要设置默认值
 * @param $val
 * @param $default
 * @return mixed
 */
function setDefault($val, $default)
{
    return $val ? $val : $default;
}


/**
 * xml转数组
 * @param $xml
 * @return mixed
 */
function xmlToArray($xml)
{
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}


/**
 * 判断url是否内网ip
 * @param string $url
 * @return bool
 */
function isIntranet($url = '')
{
    $params = parse_url($url);
    $host   = gethostbynamel($params['host']);
    if (is_array($host)) {
        foreach ($host as $key => $val) {
            if (!filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return true;
            }
        }
    }
    return false;
}


/**
 * 获取微信操作对象（单例模式）
 * @staticvar array $wechat 静态对象缓存对象
 * @param type $type 接口名称 ( Card|Custom|Device|Extend|Media|Oauth|Pay|Receive|Script|User )
 * @return \Wehcat\WechatReceive 返回接口对接
 */
function & load_wechat($type = '')
{

    static $wechat = array();
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        // 从数据库获取配置信息
        $options = array(
            'token'          => getSetting('wx_official_token'), // 填写你设定的key
            'appid'          => getSetting('wx_official_appid'), // 填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'      => getSetting('wx_official_app_secret'), // 填写高级调用功能的密钥
            'encodingaeskey' => getSetting('wx_official_encodeaeskey'), // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
            'mch_id'         => '', // 微信支付，商户ID（可选）
            'partnerkey'     => '', // 微信支付，密钥（可选）
            'ssl_cer'        => '', // 微信支付，双向证书（可选，操作退款或打款时必需）
            'ssl_key'        => '', // 微信支付，双向证书（可选，操作退款或打款时必需）
            'cachepath'      => '', // 设置SDK缓存目录（可选，默认位置在Wechat/Cache下，请保证写权限）
        );
        \Wechat\Loader::config($options);
        $wechat[$index] = \Wechat\Loader::get($type);
    }
    return $wechat[$index];
}


/**
 * 获取最近天数的日期和数据
 * @param $day
 * @param $data
 * @return array
 */
function get_lately_days($day, $data)
{
    $day  = $day - 1;
    $days = [];
    $d    = [];
    for ($i = $day; $i >= 0; $i--) {
        $d[]                                               = date('d', strtotime('-' . $i . ' day')) . '日';
        $days[date('Y-m-d', strtotime('-' . $i . ' day'))] = 0;
    }
    foreach ($data as $v) {
        $days[$v['day']] = $v['nums'];
    }
    $new = [];
    foreach ($days as $v) {
        $new[] = $v;
    }
    return ['day' => $d, 'data' => $new];
}


/**
 * 商家发送信息助手
 * @param $user_id
 * @param $code
 * @param $params
 * @return array
 */
function sendMessage($user_id, $code, $params)
{
    $messageCenter = new \app\common\model\MessageCenter();
    hook("adminmessage",array('user_id'=>$user_id,"code"=>$code,"params"=>$params));
    return $messageCenter->sendMessage($user_id, $code, $params);
}


/**
 * 根据商户id和用户id获取openid (废弃方法)
 * @param $user_id
 * @return array|bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getUserWxInfo($user_id)
{
    $wxModel  = new \app\common\model\UserWx();
    $filter[] = ['user_id', 'eq', $user_id];
    $wxInfo   = $wxModel->field('id,user_id,openid,unionid,avatar,nickname')->where($filter)->find();
    if ($wxInfo) {
        return $wxInfo->toArray();
    } else {
        return false;
    }
}


/**
 * 判断用户是否有新消息，用于前端显示小红点
 * @param $user_id
 * @return bool
 */
function hasNewMessage($user_id)
{
    $messageModel = new \app\common\model\Message();
    $re           = $messageModel->hasNew($user_id);
    return $re;
}


/**
 * 格式化银行卡号，前四位和最后显示原样的，其他隐藏
 * @param $cardNo
 * @return string
 */
function bankCardNoFormat($cardNo)
{
    $n = strlen($cardNo);
    //判断尾部几位显示原型
    if ($n % 4 == 0) {
        $j = 4;
    } else {
        $j = $n % 4;
    }
    $str = "";
    for ($i = 0; $i < $n; $i++) {
        if ($i < 4 || $i > $n - $j - 1) {
            $str .= $cardNo[$i];
        } else {
            $str .= "*";
        }
        if ($i % 4 == 3) {
            $str .= " ";
        }
    }
    return $str;
}


/**
 * 获取系统设置
 * @param string $key
 * @return mixed|string
 */
function getSetting($key = '')
{
    $systemSettingModel = new \app\common\model\Setting();
    return $systemSettingModel->getValue($key);
}


/**
 * 获取多个系统设置
 * @param string $key //多个英文逗号分隔
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function getMultipleSetting($key = '')
{
    $systemSettingModel = new \app\common\model\Setting();
    return $systemSettingModel->getMultipleValue($key);
}


/**
 * 获取插件配置信息
 * @param string $name
 * @return array|mixed
 */
function getAddonsConfig($name)
{
    if (!$name) {
        return [];
    }
    $addonModel = new \app\common\model\Addons();
    return $addonModel->getSetting($name);
}

/**
 * 获取插件的某一个配置信息
 * @param $name     插件名称
 * @param $val      插件的配置文件
 * @return string   插件的值
 */
function getAddonsConfigVal($name, $val)
{
    $conf = getAddonsConfig($name);
    if (isset($conf[$val])) {
        return $conf[$val];
    } else {
        return "";
    }

}


/**
 * 货品上的多规格信息，自动拆分成二维数组
 * @param $str_spes_desc
 * @return array
 */
function getProductSpesDesc($str_spes_desc)
{
    if ($str_spes_desc == "") {
        return [];
    }
    $spes = explode(',', $str_spes_desc);
    if (is_array($spes)) {
        $re = [];
        foreach ($spes as $v) {
            $val = explode(':', $v);
            if (count($val) >= 2) {
                $re[$val[0]] = $val[1];
            }
        }
    }
    return $re;
}


/**
 * 返回管理员信息
 * @param $manage_id
 * @param string $field
 * @return string
 */
function get_manage_info($manage_id, $field = 'username')
{
    $user = app\common\model\Manage::get($manage_id);
    if ($user) {
        if ($field == 'nickname') {
            $nickname = $user['nickname'];
            if ($nickname == '') {
                $nickname = format_mobile($user['mobile']);
            }
            return $nickname;
        } else {
            return $user->$field;
        }
    } else {
        return "";
    }
}


/**
 * 数组倒排序，取新的键
 * @param array $array
 * @return array
 */
function _krsort($array = [])
{
    krsort($array);
    if (is_array($array)) {
        $i          = 0;
        $temp_array = [];
        foreach ($array as $val) {
            $temp_array[$i] = $val;
            $i++;
        }
        return $temp_array;
    } else {
        return $array;
    }
}


/**
 * 判断钩子是否有插件
 * @param string $hookname
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function checkAddons($hookname = '')
{
    $hooksModel = new \app\common\model\Hooks();
    $addons     = $hooksModel->where(['name' => $hookname])->field('addons')->find();
    if (isset($addons['addons']) && !empty($addons['addons'])) {
        return true;
    } else {
        return false;
    }
}


/**
 * 判断商品是否参加团购
 * @param int $gid
 * @param int $promotion_id
 * @return bool
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function isInGroup($gid = 0, &$promotion_id = 0,&$condition = [])
{
    if (!$gid) {
        return false;
    }

    $promotion = new app\common\model\Promotion();

    $where[]   = ['p.status', 'eq', $promotion::STATUS_OPEN];

    if($promotion_id){
        $where[]   = ['p.id', 'eq', $promotion_id];//团购秒杀id
    }
    /*
    $where[]   = ['p.stime', 'lt', time()];
    $where[]   = ['p.etime', 'gt', time()];*/
    $where[]   = ['gg.goods_id', '=',  $gid];
    $where[]   = ['p.type', 'in', [$promotion::TYPE_GROUP, $promotion::TYPE_SKILL]];

    $condition = $promotion->field('p.id as id,p.type,p.status,p.params as params,p.stime as stime,p.etime as etime')
        ->alias('p')
        ->join('promotion_condition pc', 'pc.promotion_id = p.id')
        ->join('group_goods gg', 'gg.rule_id = p.id')
        ->where($where)
        ->find();

    if ($condition) {
        return true;
    }
    return false;
}


/***
 * 判断是否json
 * @param $str
 * @return bool
 */
function isjson($str)
{
    return is_null(json_decode($str)) ? false : true;
}


/**
 * 判断是否手机号
 * @param $mobile
 * @return bool
 */
function isMobile($mobile = '')
{
    if (preg_match("/^1[123456789]{1}\d{9}$/", $mobile)) {
        return true;
    } else {
        return false;
    }
}


/**
 * 秒转换为天，小时，分钟
 * @param int $second
 * @return string
 */
function secondConversion($second = 0)
{
    $newtime = '';
    $d       = floor($second / (3600 * 24));
    $h       = floor(($second % (3600 * 24)) / 3600);
    $m       = floor((($second % (3600 * 24)) % 3600) / 60);
    if ($d > '0') {
        if ($h == '0' && $m == '0') {
            $newtime = $d . '天';
        } else {
            $newtime = $d . '天' . $h . '小时' . $m . '分';
        }
    } else {
        if ($h != '0') {
            if ($m == '0') {
                $newtime = $h . '小时';
            } else {
                $newtime = $h . '小时' . $m . '分';
            }
        } else {
            $newtime = $m . '分';
        }
    }
    return $newtime;
}


/**
 * 返回文件地址
 * @param $file_id
 * @param $type
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-15
 */
function _sFile($file_id, $type = 's')
{
    if (!$file_id) {
        return false;
    }
    if (stripos($file_id, 'http') !== false || stripos($file_id, 'https') !== false) {
        return $file_id;
    }
    $file_obj = new \app\common\model\Files();
    $file     = $file_obj->where([
        'id' => $file_id
    ])->field('url')->find();
    if ($file) {
        if (stripos($file['url'], 'http') !== false || stripos($file['url'], 'https') !== false) {
            return str_replace("\\", "/", $file['url']);
        } else {
            return request()->domain() . str_replace("\\", "/", $file['url']);
        }
    } else {
        return false;
    }
}


/**
 * 验证是否邮箱
 * @param $email
 * @return bool
 */
function isEmail($email)
{
    $pattern = '/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i';
    if (preg_match($pattern, $email)) {
        return true;
    } else {
        return false;
    }
}


/***
 * 导出时字符串太长不显示时，处理
 */
function convertString($value = '')
{
    return  '="'.$value.'"';
}


/***
 * 导出时字符串太长不显示时，处理
 */
function mobile_string($user_id = 0)
{
    $mobile = get_user_info($user_id);
    return '="'.$mobile.'"';
}


/***
 * 导出时字符串太长不显示时，处理
 */
function nickname_string($user_id = 0)
{
    $nickname = get_user_info($user_id, 'nickname');
    return '="'.$nickname.'"';
}


/**
 * 根据token获取userid
 * @param string $token
 * @return int
 */
function getUserIdByToken($token = '')
{
    if (!$token) {
        return 0;
    }
    $userTokenModel = new \app\common\model\UserToken();
    $return_token   = $userTokenModel->checkToken($token);
    if ($return_token['status'] == false) {
        return 0;
    }
    return $return_token['data']['user_id'];
}


/**
 * 清除HTML中指定样式
 * @param $content
 * @return mixed
 */
function clearHtml($content, $rule = [])
{
    if (!$rule) {
        return $content;
    }
    foreach ($rule as $v) {
        $content = preg_replace('/' . $v . '\s*=\s*\d+\s*/i', '', $content);
        $content = preg_replace('/' . $v . '\s*=\s*.+?["\']/i', '', $content);
        $content = preg_replace('/' . $v . '\s*:\s*\d+\s*px\s*;?/i', '', $content);
    }
    return $content;
}


/**
 * 获取秒数对应的时间
 * @param int $second
 * @return array
 */
function secondConversionArray($second = 0)
{
    $d       = floor($second / (3600 * 24));
    $h       = floor(($second % (3600 * 24)) / 3600);
    $m       = floor((($second % (3600 * 24)) % 3600) / 60);
    $s       = floor((($second % (3600 * 24)) % 3600) % 60);
    $newtime = [
        'day'    => $d,
        'hour'   => $h,
        'minute' => $m,
        'second' => $s
    ];
    return $newtime;
}

/***
 * 令牌校检
 * @return array
 */
function validateJshopToken()
{
    $_token      = input('__Jshop_Token__/s', '');
    $form        = input('validate_form/s', '');

    $cache_token = \think\facade\Cache::get($form . '_token');

    if (!$_token || $_token != $cache_token) {
        if (\think\facade\Request::isAjax()) {

            $new_token = \think\facade\Request::token('__Jshop_Token__', 'sha1');
            \think\facade\Cache::set($form . '_token', $new_token, 86400);   //1天过期
            $return = [
                'data'   => '',
                'msg'    => error_code(10082,true),
                'status' => false,
                'token'  => $new_token
            ];
            //\think\facade\Cache::rm($form . '_token');//删除旧缓存
            header('Content-type:text/json');
            echo json_encode($return);
            exit;
        } else {
            die("CSRF is die");
        }
    }
}

/**
 * 生成令牌
 * @param string $form
 * @return string
 */
function jshopToken($form = 'form')
{
    $data = \think\facade\Request::token('__Jshop_Token__', 'sha1');
    \think\facade\Cache::set($form . '_token', $data, 86400);   //1天过期
    return '<input type="hidden" name="validate_form" value="' . $form . '"><input type="hidden" name="__Jshop_Token__" value="' . $data . '" class="Jshop_Token">';
}


/***
 * 获取年月日目录
 * @param $name
 * @return string
 * User: wjima
 * Email:1457529125@qq.com
 */
function get_date_dir()
{
    $dir = '/' . date('Y') . '/' . date('m') . '/' . date('d');
    return $dir;
}


/*
* @param $posttime 时间戳，例如：1558315633
*/
function time_ago($posttime)
{
    //当前时间的时间戳
    $nowtimes = time();
    //相差时间戳
    $counttime = $nowtimes - $posttime;

    //进行时间转换
    if ($counttime <= 60) {
        return '刚刚';
    } else if ($counttime > 60 && $counttime <= 120) {
        return '1分钟前';
    } else if ($counttime > 120 && $counttime <= 180) {
        return '2分钟前';
    } else if ($counttime > 180 && $counttime < 3600) {
        return intval(($counttime / 60)) . '分钟前';
    } else if ($counttime >= 3600 && $counttime < 3600 * 24) {
        return intval(($counttime / 3600)) . '小时前';
    } else if ($counttime >= 3600 * 24 && $counttime < 3600 * 24 * 2) {
        return '昨天';
    } else if ($counttime >= 3600 * 24 * 2 && $counttime < 3600 * 24 * 3) {
        return '前天';
    } else if ($counttime >= 3600 * 24 * 3 && $counttime <= 3600 * 24 * 7) {
        return intval(($counttime / (3600 * 24))) . '天前';
    } else if ($counttime >= 3600 * 24 * 7 && $counttime <= 3600 * 24 * 30) {
        return intval(($counttime / (3600 * 24 * 7))) . '周前';
    } else if ($counttime >= 3600 * 24 * 30 && $counttime <= 3600 * 24 * 365) {
        return intval(($counttime / (3600 * 24 * 30))) . '个月前';
    } else if ($counttime >= 3600 * 24 * 365) {
        return intval(($counttime / (3600 * 24 * 365))) . '年前';
    }
}


/**
 * 获取插件状态 todo 缓存插件
 * @param $name
 * @return 1：已安装，false：未安装
 */
function get_addons_status($name)
{
    if (!$name) {
        return [];
    }
    $addonModel = new \app\common\model\Addons();
    $info       = $addonModel->where(['name' => $name])->field('status')->find();
    return (isset($info['status']) && ($info['status'] == $addonModel::INSTALL_STATUS)) ? $info['status'] : false;
}


/**
 * 加密函数
 * @param string $txt 需要加密的字符串
 * @return string 返回加密结果
 */
function encrypt($txt)
{
    if (!file_exists(ROOT_PATH . '/config/install.lock')) {
        @touch(ROOT_PATH . '/config/install.lock');
    }
    $key = filectime(ROOT_PATH . '/config/install.lock');

    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(MD5_KEY);
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey  = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $nh1   = rand(0, 64);
    $nh2   = rand(0, 64);
    $nh3   = rand(0, 64);
    $ch1   = $chars{$nh1};
    $ch2   = $chars{$nh2};
    $ch3   = $chars{$nh3};
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum  = 0;
    $i     = 0;
    while (isset($key{$i})) $knum += ord($key{$i++});
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $txt   = base64_encode(time() . '_' . $txt);
    $txt   = str_replace(array('+', '/', '='), array('-', '_', '.'), $txt);
    $tmp   = '';
    $j     = 0;
    $k     = 0;
    $tlen  = strlen($txt);
    $klen  = strlen($mdKey);
    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k++})) % 64;
        $tmp .= $chars{$j};
    }
    $tmplen = strlen($tmp);
    $tmp    = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
    $tmp    = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
    $tmp    = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);
    return $tmp;
}

/**
 * 解密函数
 * @param string $txt 需要解密的字符串
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $ttl = 0)
{
    if (empty($txt)) return $txt;
    $key = filectime(ROOT_PATH . '/config/install.lock');
    if (empty($key)) $key = md5(MD5_KEY);
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey  = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $knum  = 0;
    $i     = 0;
    $tlen  = @strlen($txt);
    while (isset($key{$i})) $knum += ord($key{$i++});
    $ch1   = @$txt{$knum % $tlen};
    $nh1   = strpos($chars, $ch1);
    $txt   = @substr_replace($txt, '', $knum % $tlen--, 1);
    $ch2   = @$txt{$nh1 % $tlen};
    $nh2   = @strpos($chars, $ch2);
    $txt   = @substr_replace($txt, '', $nh1 % $tlen--, 1);
    $ch3   = @$txt{$nh2 % $tlen};
    $nh3   = @strpos($chars, $ch3);
    $txt   = @substr_replace($txt, '', $nh2 % $tlen--, 1);
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $tmp   = '';
    $j     = 0;
    $k     = 0;
    $tlen  = @strlen($txt);
    $klen  = @strlen($mdKey);
    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k++});
        while ($j < 0) $j += 64;
        $tmp .= $chars{$j};
    }
    $tmp = str_replace(array('-', '_', '.'), array('+', '/', '='), $tmp);
    $tmp = trim(base64_decode($tmp));
    if (preg_match("/\d{10}_/s", substr($tmp, 0, 11))) {
        if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl)) {
            $tmp = null;
        } else {
            $tmp = substr($tmp, 11);
        }
    }
    return $tmp;
}


/**
 * 分享URL压缩
 * @param $url
 * @return string // type-invite-id-team_id  type=场景类型  invite=邀请码  id=场景ID值  team_id=拼团ID
 */
function share_parameter_encode($url)
{
    $urlArray     = explode('&', $url);
    $allParameter = [
        'type'    => '',
        'invite'  => '',
        'id'      => '',
        'team_id' => ''
    ];
    foreach ($urlArray as $v) {
        $parameter                   = explode('=', $v);
        $allParameter[$parameter[0]] = $parameter[1];
    }
    $newUrl = $allParameter['type'] . '-' . $allParameter['invite'] . '-' . $allParameter['id'] . '-' . $allParameter['team_id'];
    return $newUrl;
}


/**
 * 分享URL解压缩
 * @param $url
 * @return string
 */
function share_parameter_decode($url)
{
    $urlArray = explode('-', $url);
    $newUrl   = 'type=' . $urlArray[0] . '&invite=' . $urlArray[1] . '&id=' . $urlArray[2] . '&team_id=' . $urlArray[3];
    return $newUrl;
}


/**
 * Translates a number to a short alhanumeric version
 *
 * Translated any number up to 9007199254740992
 * to a shorter version in letters e.g.:
 * 9007199254740989 --> PpQXn7COf
 *
 * specifiying the second argument true, it will
 * translate back e.g.:
 * PpQXn7COf --> 9007199254740989
 *
 * this function is based on any2dec && dec2any by
 * fragmer[at]mail[dot]ru
 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
 *
 * If you want the alphaID to be at least 3 letter long, use the
 * $pad_up = 3 argument
 *
 * In most cases this is better than totally random ID generators
 * because this can easily avoid duplicate ID's.
 * For example if you correlate the alpha ID to an auto incrementing ID
 * in your database, you're done.
 *
 * The reverse is done because it makes it slightly more cryptic,
 * but it also makes it easier to spread lots of IDs in different
 * directories on your filesystem. Example:
 * $part1 = substr($alpha_id,0,1);
 * $part2 = substr($alpha_id,1,1);
 * $part3 = substr($alpha_id,2,strlen($alpha_id));
 * $destindir = "/".$part1."/".$part2."/".$part3;
 * // by reversing, directories are more evenly spread out. The
 * // first 26 directories already occupy 26 main levels
 *
 * more info on limitation:
 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
 *
 * if you really need this for bigger numbers you probably have to look
 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
 * but I haven't really dugg into this. If you have more info on those
 * matters feel free to leave a comment.
 *
 * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
 * @author  Simon Franz
 * @author  Deadfish
 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
 * @link    http://kevin.vanzonneveld.net/
 *
 * @param mixed $in String or long input to translate
 * @param boolean $to_num Reverses translation when true
 * @param mixed $pad_up Number or boolean padds the result up to a specified length
 * @param string $passKey Supplying a password makes it harder to calculate the original ID
 *
 * @return mixed string or long
 */
function alphaID($in, $to_num = false, $pad_up = false)
{
    if (!file_exists(ROOT_PATH . '/config/install.lock')) {
        @touch(ROOT_PATH . '/config/install.lock');
    }
    $passKey = filectime(ROOT_PATH . '/config/install.lock');

    $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($passKey !== null) {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // with this patch by Simon Franz (http://blog.snaky.org/)
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID

        for ($n = 0; $n < strlen($index); $n++) {
            $i[] = substr($index, $n, 1);
        }

        $passhash = hash('sha256', $passKey);
        $passhash = (strlen($passhash) < strlen($index))
            ? hash('sha512', $passKey)
            : $passhash;

        for ($n = 0; $n < strlen($index); $n++) {
            $p[] = substr($passhash, $n, 1);
        }

        array_multisort($p, SORT_DESC, $i);
        $index = implode($i);
    }

    $base = strlen($index);

    if ($to_num) {
        // Digital number  <<--  alphabet letter code
        $in  = strrev($in);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $bcpow = bcpow($base, $len - $t);
            $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }

        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $out -= pow($base, $pad_up);
            }
        }
        $out = sprintf('%F', $out);
        $out = substr($out, 0, strpos($out, '.'));
    } else {
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $in += pow($base, $pad_up);
            }
        }

        $out = "";
        for ($t = floor(log($in, $base)); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a   = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in  = $in - ($a * $bcp);
        }
        $out = strrev($out); // reverse
    }

    return $out;
}


/**
 * 去除XSS（跨站脚本攻击）的函数(高效率)
 * @param $val 需要过滤值
 * @param null $opt 操作动作
 * @return string
 * @Auth leyangjun
 */
function remove_xss($val)
{
    if(!$val){
        return $val;
    }
    if(is_array($val)){
        foreach((array)$val as $key=>$value){
            $val[$key] = remove_xss($value);
        }
    }else{
        $val = strip_tags($val);
        $val = htmlspecialchars($val, ENT_QUOTES);
    }
    return $val;
}


/**
 * 对内容进行安全处理
 * @param string|array $string 要处理的字符串或者数组
 * @param $string $flags 指定标记
 */
function safe_filter($string, $flags = null) {
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = safe_filter($val, $flags);
        }
    } else {
        if($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&', '"', '<', '>'), $string);
            if(strpos($string, '&#') !== false) {
                $string = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if(PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if(strtolower(CHARSET) == 'utf-8') {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
    return $string;
}


/**
 * 检查文字
 * @param $content
 * @param int $type //1微信免费 2珊瑚收费
 * @return bool
 */
function msgSecCheck($content, $type = 1)
{
    $wx = new Wx();
    if ($type == 1) {
        $res = $wx->msgSecCheck($content);
    } else {
        $res = $wx->msgSecCheckPay($content);
    }
    return $res;
}

/**
 * 检查图片
 * @param $img
 * @param int $type //1微信免费 2珊瑚收费
 * @return bool
 */
function imgSecCheck($img, $type = 1)
{
    $wx = new Wx();
    if ($type == 1) {
        $res = $wx->imgSecCheck($img);
    } else {
        $res = $wx->imgSecCheckPay($img);
    }
    return $res;
}

// 生成编码
function makeCode()
{
    // return hash('fnv164', uniqid().microtime(true));
    return rand(10000,99999);
}

// 获取专辑
function getAlbum()
{
    $cate_obj = new \app\common\model\ArticleType();

    return $cate_obj->select();
} 

// 判断是否是已点赞的
function isUserCare($user_id = '',$id = '',$type = 1)
{
    $care_obj = new \app\common\model\UserCare();

    $is_care = $care_obj->where(['user_id' => $user_id,'obj_id' => $id,'type' => $type])->count();

    return $is_care > 0 ? true : false;
}

// 判断是否是已出价
function isUserPostPrice($user_id = '',$type = 1,$id = '')
{
    $price_obj = new \app\common\model\UserPrice();
    $user_obj = new \app\common\model\User();

    $where['e_time'] = ['>',time()];
    $where['user_id'] = $user_id;
    $where['type']  = $type;
    $where['p_id']  = $id;

    $is_price = $price_obj->where($where)->find();

    // 获取出价历史
    $price_list = $price_obj->field('price,e_time,user_id')->where(['type' => $type,'p_id' => $id])->select();
    if($price_list){
        foreach ($price_list as $key => $value) {
            $user_info = $user_obj->where('id',$value['user_id'])->field('username,avatar')->find();
            $price_list[$key]['e_time'] = date('Y-m-d H:i',$value['e_time']);
            $price_list[$key]['buyer_name'] = $user_info['username'];
            $price_list[$key]['buyer_img'] = _sImage($user_info['avatar']);
        }
    }

    if($is_price){
        $return_data = [
            'state' => true,
            'price' => $is_price['price'],
            'e_time' => date('Y-m-d',$is_price['e_time']),
            'history' => $price_list
        ];
    }else{
        $return_data = [
            'state' => false,
            'price' => 0,
            'e_time' => 0,
            'history' => $price_list
        ];
    }
    return $return_data;
}

// 计算概率
function randomSelect(&$array)
{
    $datas = $array;
    if(!is_array($datas) || count($datas) == 0){
        return false;
    }

    asort($datas);//按照大小排序

    $random = rand(1,100);
    $sum = 0;
    $flag = '';
    foreach ($datas as $key => $data) {
        $sum += $data;
        // 看取出来的随机数属于哪个区间
        if($random <= $sum){
            $flag = $key;
            break;
        }
    }

    if($flag == ''){//如果传递进去的值的和小于100，则取概率最大的
        $keys = array_keys($datas);
        $flag = $keys[count($keys) - 1];
    }

    return $flag;
}

/**
 * 递归删除文件
 * @param $dirName
 * @param bool|true $subdir
 * @return bool
 */
function del_dir_and_file($dirName,$subdir = true)
{
    if(!is_dir($dirName)) return true;
    if ($handle = opendir("$dirName")) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir("$dirName/$item")){
                    del_dir_and_file("$dirName/$item", false);
                }
                else
                    @unlink("$dirName/$item");
            }
        }
        closedir($handle);
        if (!$subdir) @rmdir($dirName);
    }
}

function gtranslate($text,$lng = 'zh-CN')
{
    $entext = urlencode($text);
    $url = 'http://translate.google.cn/translate_a/single?client=gtx&dt=t&ie=UTF-8&oe=UTF-8&sl=auto&tl='.$lng.'&q=' . $entext;
    set_time_limit(0);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result);
    if (!empty($result)) {
        foreach ($result[0] as $k) {
            $v[] = $k[0];
        }
        return implode(" ", $v);
    }
}

// 百度翻译
function language($value,$from="en",$to="zh")
{
     $value_code = $value;
     $appid = "20220801001289544"; //您注册的API Key
     $key = "BIHdMLDLV2m5X0xeYtVA"; //密钥
     $salt = rand(1000000000,9999999999); //随机数
     $sign = md5($appid.$value_code.$salt.$key); //签名
     $value_code = urlencode($value_code);
     //生成翻译API的URL 
     $languageurl = "http://api.fanyi.baidu.com/api/trans/vip/translate?q=$value_code&appid=$appid&salt=$salt&from=$from&to=$to&sign=$sign";
     $text = json_decode(language_text($languageurl));
     $lan = $text->trans_result;
     $result = '';
     foreach ($lan as $k => $v)
      {
           $result .= ucwords($v->dst);
      }
      return $result;
}

function language_text($reqURL)
{
    $ch = curl_init($reqURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    if($result){
        curl_close($ch);
        return $result;
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        return ("curl出错，错误码:$error");
    }
}
    
// 是否是我的作品
function isMyProduct($id,$type,$user_id)
{
    if(empty($user_id)){
        return false;
    }

    // 审核通过或者已售的不可编辑
    $user_works = new \app\common\model\UserWorks();
    $map['type'] = $type;
    $map['obj_id'] = $id;
    $map['user_id'] = $user_id;

    $work_info = $user_works->where($map)->find();
    // if($work_info['is_draft'] == 2){
    //     return false;
    // }

    $have_mod = new \app\common\model\UserHave();
    $have_info = $have_mod->where(['nft_id' => $id,'type' => $type])->find();
    if($work_info['user_id'] != $have_info['user_id']){
        return false;
    }

    return !empty($work_info) ? true : false;
}

// 搜索,返回id数组
function searchList($name = '')
{
    if(empty($name)){
        return -1;
    }

    $last_return = [] ;
    $user_obj = new \app\common\model\User();
    // $where = [];

    // if(!empty($name)){
    //     $user_ids = $user_obj->where('username','like','%'.$name.'%')->column('id');
    //     if(!empty($user_ids)){
    //         $where[] = ['user_id','in',$user_ids];
    //     }
        
    // }

    $art_ids = [];//文章id数组
    $music_ids = [];//音频id数组
    $game_ids = [];//游戏id数组
    $img_ids = [];//插画id数组

    // if($type == 1){
        // 文章
        $article_obj = new \app\common\model\Article();

        $where1[] = ['title','like','%'.$name.'%'];
        $art_ids = $article_obj->where($where1)->column('id');
        
    // }elseif($type == 2){
        // 音频
        $musicModel = new \app\common\model\Files();
        $where2[] = ['name','like','%'.$name.'%'];
        $music_ids = $musicModel->where($where2)->column('id');
    
    // }elseif($type == 3){
        // 游戏
        $gameModel = new \app\common\model\Game();
        $where3[] = ['game_name','like','%'.$name.'%'];
        $game_ids = $gameModel->where($where3)->column('g_id');
    
    // }elseif($type == 4){
        // 插画
        $imgModel = new \app\common\model\Pictures();

        $where4[] = ['name','like','%'.$name.'%'];
        $img_ids = $imgModel->where($where4)->column('id');
    // }

        $last_return = implode(',', $art_ids).','.implode(',', $music_ids).','.implode(',', $game_ids).','.implode(',', $img_ids);

    return $last_return;
}

function sendMsg($mobile='',$msg=''){
    $url = 'http://47.97.85.29:7862/sms?action=send';
    $data['account'] = '922138';
    $data['password'] = 'zRY5A3';
    $data['mobile'] = $mobile;
    $data['content'] = '【萌鼠世界】'.$msg;
    $data['extno'] = '10690138';
    $data['rt'] = 'json';
    $ret = json_decode(send_post($url,$data));
    if($ret->status == 0){
        return true;
    }
    return false;

}

/**
 * 模拟post
 * @param $url
 * @param $post_data
 * @return false|string
 */
function send_post($url, $post_data) {
    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded;charset=UTF-8',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;

}
    
// 增加阿里云图片配置参数
function addImageSuffix($type = 1)
{
    switch ($type) {
        case 1:
            // 轮播
            $suffix = '?x-oss-process=image/resize,w_2920,m_lfit/format,webp';
            break;
        case 2:
            // 专辑封面
            $suffix = '?x-oss-process=image/resize,w_600,m_lfit/format,webp';
            break;
        case 3:
            // 专辑头像
            $suffix = '?x-oss-process=image/resize,w_112,m_lfit/format,webp';
            break;
        case 4:
            // 推荐作者
            $suffix = '?x-oss-process=image/resize,w_160,m_lfit/format,webp';
            break;
        case 5:
            // 小banner
            $suffix = '?x-oss-process=image/resize,w_300,m_lfit/format,webp';
            break;
        case 6:
            // 音频
            $suffix = '?x-oss-process=image/resize,w_200,m_lfit/format,webp';
            break;
        case 7:
            // 文章
            $suffix = '?x-oss-process=image/resize,w_800,m_lfit/format,webp';
            break;
        case 8:
            // 游戏
            $suffix = '?x-oss-process=image/resize,w_530,m_lfit/format,webp';
            break;
        default:
            //插画  
            $suffix = '?x-oss-process=image/format,webp';
            break;
    }

    return $suffix;
}
    
// 通过图片原始名称查找图片
function findImgByRealName($name = '')
{
    $source_obj = new \app\common\model\Images();
    if(!empty($name)){
       $return = $source_obj->where('name',$name)->value('id');
       return !empty($return) ? $return : _sImage();
    }else{
        return _sImage();
    }
}

// 计算稀有度
function computeRate($id = '')
{
    $part_obj = new \app\common\model\GoodsPart();

    $total = $part_obj->where('part_id',$id)->count();

    $rate = $total/100;

    return $rate;
}

//获取随机加密串
function getRandStr()
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";
    return str_shuffle($chars);
}

// 加密秘钥
function eccodeStr($str = '',$key = '')
{
    return base64_encode($str).$key;
}

// 解密秘钥
function decodeStr($str = '',$key = '')
{
    $res = str_replace($key,'',$str);
    $result = base64_decode($res);
    return $result;
}

// 添加平台草稿
function addPlatformDraft($id,$type,$is_draft=2)
{
    $workModel = new \app\common\model\UserWorks();
    $work_data = [
        'obj_id' => $id,
        'type' => $type,
        'ctime' => time(),
        'user_id' => 0,
        'is_draft' => $is_draft
    ];

    $workModel->insert($work_data);
} 

// 记录用户页面访问记录
function saveUserActions($user_id = 0,$page_url = '',$action_id = '9005001',$action_name = '进入网站')
{
    $historyModel = new \app\common\model\UserHistory();
    $page_data = config('front.');

    $url_data = [];

    foreach ($page_data as $key => $value) {

        if($value['url'] == $page_url){
            $url_data = $value;
            break;
        }
    }
    if(!empty($url_data)){
        $act_data = [
            'user_id' => $user_id,
            'page_id' => $url_data['page_id'],
            'action_id' => $action_id,
            'page_name' => $url_data['page_name'],
            'position' => $url_data['position'],
            'action_name' => $action_name,
            'ctime' => time(),
            'http_cookie' => $_SERVER['HTTP_COOKIE']
        ];

    }else{
        $act_data = [
            'user_id' => $user_id,
            'page_id' => '10002013',
            'action_id' => '9005001',
            'page_name' => '推荐界面',
            'position' => 'button_explore',
            'action_name' => '曝光',
            'ctime' => time(),
            'http_cookie' => $_SERVER['HTTP_COOKIE']
        ];
    }
    
    return $historyModel->insert($act_data);
    
}

// 记录用户页面停留时长
function saveUserTimes($user_id = 0,$page_url = '',$enter_time = 0,$leave_time = 0)
{
    $historyModel = new \app\common\model\UserViewTime();
    $page_data = config('front.');

    $url_data = [];

    foreach ($page_data as $key => $value) {

        if($value['url'] == $page_url){
            $url_data = $value;
            break;
        }
    }

    $use_time = $leave_time - $enter_time;

    if(!empty($url_data)){
        $act_data = [
            'user_id' => $user_id,
            'page_id' => $url_data['page_id'],
            'page_name' => $url_data['page_name'],
            'enter_time' => $enter_time,
            'leave_time' => $leave_time,
            'used_time' => $use_time,
            'http_cookie' => $_SERVER['HTTP_COOKIE']
        ];

        return $historyModel->insert($act_data);

    }
    
    
}

// 计算角色立场
function calRolePosition($role_id = '')
{
    // 仁善
    $benevolent = 'https://ec.wexiang.vip/character/benevolent.png';
    // 济世
    $compassionate = 'https://ec.wexiang.vip/character/compassionate.png';
    // 刚正
    $justice = 'https://ec.wexiang.vip/character/justice.png';
    // 中庸
    $moderate = 'https://ec.wexiang.vip/character/moderate.png';
    // 叛逆
    $rebelious = 'https://ec.wexiang.vip/character/rebelious.png';
    // 唯我
    $egoistical = 'https://ec.wexiang.vip/character/egoistical.png';
    // 极恶
    $evil = 'https://ec.wexiang.vip/character/evil.png';
    // 混沌
    $chaotic = 'https://ec.wexiang.vip/character/chaotic.png';

    $extend_mod = new \app\common\model\GoodsExtend();

    $character = $extend_mod->where('goods_id',$role_id)->value('character_info');
    
    if(empty($character)){
        return;
    }

    $character = unserialize($character);

    $position_val = '混沌';
    $position = 0;
    $position_img = $chaotic;

    foreach ($character as $key => $value) {
        $position += intval($value['value']);
    }

    if($position >= 2 && $position <= 4){
        //仁善
        $position_val = '仁善';
        $position_img = $benevolent;

    }elseif($position >= 8 && $position <= 10){
        // 济世
        $position_val = '济世';
        $position_img = $compassionate;
    }elseif($position >= 5 && $position <= 7){
        //刚正
        $position_val = '刚正';
        $position_img = $justice;
    }elseif($position >= -1 && $position <= 1){
        // 中庸
        $position_val = '中庸';
        $position_img = $moderate;
    }elseif($position >= -2 && $position <= -4){
        // 叛逆
        $position_val = '叛逆';
        $position_img = $rebelious;
    }elseif($position >= -5 && $position <= -7){
        //唯我 
        $position_val = '唯我';
        $position_img = $egoistical;
    }elseif($position >= -8 && $position <= -10){
        // 极恶
        $position_val = '极恶';
        $position_img = $evil;
    // }elseif($position > 10 && $position < -10){
        // 混沌
    }else{
        // 混沌
        // $position = '混沌';
        // $position_img = $benevolent;
    }

    return ['position' => $position_val,'position_img' => $position_img];
}

// 钱包地址格式转换
function formatWallet($address = '')
{
    $replace = '...';
    return substr_replace($address,$replace,6,32);
}


/*链端*/

// nft铸造
function mintNFT($address = '',$id = '',$type = 1,$level = 2)
{
    // 获取NFT创作者信息
    $work_mod = new \app\common\model\UserWorks();
    $work_info = $work_mod->where(['type' => $type,'obj_id' => $id])->find();

    // 创作者钱包
    $wallet_mod = new \app\common\model\userWallet();
    $user_mod = new \app\common\model\User();

    $wallet_info = $wallet_mod->where('address',$address)->find();
    // $buyer_wallet_info = $wallet_mod->where('user_id',$user_id)->where('type',1)->find();

    if(empty($wallet_info)){
        return error_code(10215);
    }

    $curl = new Curl();

    // 组装元数据
    $articleModel = new \app\common\model\Article();
    $musicModel = new \app\common\model\Files();
    $gameModel = new \app\common\model\Game();
    $imgModel = new \app\common\model\Pictures();
    $roleModel = new \app\common\model\Goods();

    if($type == 1){
        $info = $articleModel->where('id',$id)->field('title,cover,code')->find();
        $info['image'] = _sImage($info['cover']);
        $info['path'] = '';
        $info['name'] = $info['title'];
        unset($info['cover']);
        unset($info['title']);

    }elseif($type == 2){
        $info = $musicModel->where('id',$id)->field('name,path,code,url')->find();
        $info['image'] = _sImage($info['path']);
        unset($info['path']);
    
    }elseif($type == 4){
        $info = $imgModel->where('id',$id)->field('name,cover,code')->find();
        $info['image'] = _sImage($info['cover']);
        $info['path'] = _sImage($info['cover']);
        unset($info['path']);
    }else{
        $info = $roleModel->where('id',$id)->field('name,bn as code,image_id as cover')->find();
        $info['image'] = _sImage($info['cover']);
        $level = 1;
        unset($info['cover']);
    }

    $info['author'] = $user_mod->where('id',$work_info['user_id'])->value('username');

    // $uniqueId = hash('fnv164', uniqid().microtime(true));
    $mintNFT = 'http://124.71.137.54:1234/api/v1/mintNFT';

    // if($wallet_info){
        $address = $wallet_info['address'];
        $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        $key = $key_1.$key_2;
        // if(!$key){
        //     return error_code(10209);
        // }

        $data = [
            'contractLevel' => $level,
            'address' => $address,
            'uri' => $info,
            'privateKey' => $key
        ];
        // dump($data);die;
        $res = $curl::post($mintNFT, $data);
        // dump($res);die;
        addLog($mintNFT,$data,$res);

        $result = json_decode($res);

        // 添加铸造历史
        $nftModel = new \app\common\model\MakeNftHistory();
        $nft_data = [
            'address' => $address,
            'uri' => json_encode($info),
            'nft_id' => $id,
            'type' => $type,
            'ctime' => time(),
            // 'buy_id' => $user_id,
            'user_id' => $ower_id,
            'level' => $level,
            'status' => $result
        ];

        $nftModel->insert($nft_data);

        return ['status' => true,'msg' => $result->msg];
        // 铸造成功nft交易
        // if($json_str->data && !empty($buyer_wallet_info)){
        //     // 设置版税
        //     $royalties = $user_mod->where('id',$ower_id)->value('royalties');
        //     if(!empty($royalties)){
        //         setTokenRoyalty(2,$json_str->data->hash,$address,$royalties,$key);
        //     }

        //     $nftsaleModel = new \app\common\model\NftSaleHistory();
            
        //     $sale_res = safeTransfer($level,$address,$json_str->data->hash,$key,$buyer_wallet_info['address'],$price);
        //     // dump($sale_res);die;
        //     $sale_data = [
        //         'nft_id' => $id,
        //         'type' => $type,
        //         'user_id' => $ower_id,
        //         'buyer_id' => $user_id,
        //         'fromAddress' => $address,
        //         'toAddress' => $buyer_wallet_info['address'],
        //         'tokenId' => $json_str->data->hash,
        //         'values' => $price,
        //         'contractLevel' => $level,
        //         'ctime' => time(),
        //         'status' => $sale_res
        //     ];

        //     $nftsaleModel->insert($sale_data);
        // }
        // return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
    // }else{
    //     return error_code(10210);
    // }
    
}

// nft交易
function safeTransfer($contractLevel = 1,$fromAddress = '',$id = '',$privateKey = '',$toAddress = '',$values = '')
{
    $curl = new Curl();

    $safeUrl = 'http://124.71.137.54:1234/api/v1/safeTransfer';
    $safeData = [
        'contractLevel' => $contractLevel,
        'fromAddress' => $fromAddress,
        'id' => $id,
        'privateKey' => $privateKey,
        'toAddress' => $toAddress,
        'values' => $values
    ];

    // dump($safeData);die;
    return $curl::post($safeUrl, $safeData);
}

// 设置默认版税
function setDefaultRoyalty($address = '',$price = 0,$level = 1)
{
    if(empty($address)){
        return error_code(10206);
    }

    $curl = new Curl();

    $mintNFT = 'http://124.71.137.54:1234/api/v1/setDefaultRoyalty';
        
    $data = [
        'contractLevel' => $level,
        'receiver' => $address,
        'feeNumerator' => $price,
    ];

    $res = $curl::post($mintNFT, $data);
    addLog($mintNFT,$data,$res);
    $json_str = json_decode($res);

    return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
}

// 设置token版税 
function setTokenRoyalty($contractLevel = 1,$tokenId = '',$receiver = '',$feeNumerator = 0,$privateKey = '')
{
    $curl = new Curl();

    $mintNFT = 'http://124.71.137.54:1234/api/v1/setTokenRoyalty';

    $data = [
        'contractLevel' => $contractLevel,
        'tokenId' => $tokenId,
        'receiver' => $receiver,
        'feeNumerator' => $feeNumerator,
        'privateKey' => $privateKey,
    ];

    return $curl::post($mintNFT, $data);
}

// 出售nft
function startSale($address = '',$level = 1,$tokenId = '',$price = 0)
{
    if(empty($address)){
        return error_code(10206);
    }

    $curl = new Curl();

    $startSale = 'http://124.71.137.54:1234/api/v1/startSale';

    $wallet_mod = new \app\common\model\userWallet();

    $wallet_info = $wallet_mod->where('address',$address)->find();

    if($wallet_info){
        
        $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        $key = $key_1.$key_2;
        if(!$key){
            return error_code(10209);
        }

        $data = [
            'contractLevel' => $level,
            'id' => $tokenId,
            'num' => $price,
            'privateKey' => $key
        ];

        $res = $curl::post($startSale, $data);
        
        $json_str = json_decode($res);

        return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
    }else{
        return error_code(10210);
    }
}

// 查询余额 
function balanceOf($address = '',$level = 1)
{
    $curl = new Curl();

    $wallet_mod = new \app\common\model\userWallet();
    $wallet_info = $wallet_mod->where('address',$address)->find();

    if($wallet_info){
        $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        $privateKey = $key_1.$key_2;
        if(!$privateKey){
            return 0;
        }
    }else{
        return 0;
    }

    $balance = 'http://124.71.137.54:1234/api/v1/balanceOf?address='.$address.'&contractLevel='.$level.'&privateKey='.$privateKey;

    $res = $curl::get($balance);
    
    addLog($balance,['contractLevel' => $level,'privateKey' => $privateKey],$res);
    $json_str = json_decode($res);

    return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
}

// 获取语言
function getLng()
{
    return session('lng_'.$_SERVER['HTTP_COOKIE']);
}

// 提取版税
function withdraw($address = '',$level = 1)
{
    if(empty($address)){
        return error_code(10206);
    }

    $curl = new Curl();

    $withdraw = 'http://124.71.137.54:1234/api/v1/withdraw';

    $wallet_mod = new \app\common\model\userWallet();

    $wallet_info = $wallet_mod->where('address',$address)->find();

    if($wallet_info){
        
        $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        $key = $key_1.$key_2;
        if(!$key){
            return error_code(10209);
        }

        $data = [
            'contractLevel' => $level,
            'privateKey' => $key
        ];

        $res = $curl::post($withdraw, $data);
        
        $json_str = json_decode($res);

        return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
    }else{
        return error_code(10210);
    }
}

// 授权寄售
function setApprovalForAll($address = '',$level = 1)
{
    $curl = new Curl();

    $setApprovalForAll = 'http://124.71.137.54:1234/api/v1/setApprovalForAll';
    $wallet_mod = new \app\common\model\userWallet();

    $wallet_info = $wallet_mod->where('address',$address)->find();

    if($wallet_info){
        
        $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        $key = $key_1.$key_2;
        if(!$key){
            return error_code(10209);
        }

        $data = [
            'address' => $address,
            'contractLevel' => $level,
            'privateKey' => $key
        ];

        $res = $curl::post($setApprovalForAll, $data);
        addLog($setApprovalForAll,$data,$res);
        $json_str = json_decode($res);

        return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
    }else{
        return error_code(10210);
    }
}

// lazy mint
function lazyMint($address = '',$price = 0,$signature = '')
{
    $curl = new Curl();

    $lazyMint = 'http://124.71.137.54:1234/api/v1/lazyMint';

    $uniqueId = hash('fnv164', uniqid().microtime(true));

    $wallet_mod = new \app\common\model\userWallet();

    $wallet_info = $wallet_mod->where('address',$address)->find();

    if($wallet_info){
        
        $key_1 = decodeStr($wallet_info['privateKey_1'],$wallet_info['salt']);
        $key_2 = decodeStr($wallet_info['privateKey_2'],$wallet_info['salt']);
        $key = $key_1.$key_2;
        if(!$key){
            return error_code(10209);
        }

        $data = [
            'from' => $address,
            'uri' => $uniqueId,
            'values' => $price,
            'signature' => $signature,
            'privateKey' => $key,
        ];

        $res = $curl::post($lazyMint, $data);
        
        $json_str = json_decode($res);

        return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
    }else{
        return error_code(10210);
    }

}
 
// 寄售交易 TODO
function nftTransfer()
{

}

// 设置默克尔树根
function setMerkleRoot($root = '',$level = 1)
{
    $curl = new Curl();

    $setMerkleRoot = 'http://124.71.137.54:1234/api/v1/setMerkleRoot';

    $data = [
        'root' => $root,
        'contractLevel' => $level
    ];

    $res = $curl::post($setMerkleRoot, $data);
    dump($res);die;
    addLog($setMerkleRoot,$data,$res);
    $json_str = json_decode($res);

    return ['code' => $json_str->code,'msg' => $json_str->msg,'data' => $json_str->data];
}

/*
* 设置nft默认排序方式
* $sort 1时间,2热度,3手动排序
* type 1文章2音频3游戏4插画5角色6专辑
*/
function setSortType($sort = '',$type = 1)
{
    $res = false;

    if(!empty($sort)){
        $set_mod = new \app\common\model\Setting();

        $info = $set_mod->where('skey','sort_type_'.$type)->find();

        if($info){
            $res = $set_mod->where('skey','sort_type_'.$type)->update(['value' => $sort]);
        }else{
            $res = $set_mod->insert(['skey' => 'sort_type_'.$type,'value' => $sort]);
        }

    }

    return $res;
}

// 判断nft状态
function getNftStatus($id = '',$type = 1,$user_id = '')
{
    $work_mod = new \app\common\model\UserWorks();
    $lock_mod = new \app\common\model\UserUnlockLog();
    // 判断发行类型
    $work_info = $work_mod->where(['type' => $type,'obj_id' => $id])->find();

    $is_need_buy = false;
    $lock_price = 0;
    $post_type = 1;
    $post_num = 0;
    $copy_num = 0;
    $post_price = 0;
    $copy_price = 0;

    if($type == 5){
        $role_mod = new \app\common\model\Goods();
        
        return ['is_lock' => $is_need_buy,'lock_price' => $lock_price,'post_type' => 2,'post_num' => 1,'post_price' => $role_mod->where('id',$id)->value('price'),'copy_price' => $copy_price,'copy_num' => $copy_num];
        
    }

    if($work_info){
        $post_type = explode(',',$work_info['post_type']);
        // 内容体验
        if(in_array(1, $post_type)){
            // 判断是免费还是付费
            // 判断是不是作者本人
            if($work_info['user_id'] == $user_id){
                // 免费
                $is_need_buy = false;
            }else{
                if($work_info['experience_type'] == 1){
                    // 免费
                    $is_need_buy = false;
                }else{
                    // 付费
                    // 判断该用户是否已经付费解锁
                    $lock_info = $lock_mod->where(['user_id' => $user_id,'nft_id' => $id,'type' => $type])->find();
                    if($lock_info){
                        $is_need_buy = false;
                    }else{
                        $is_need_buy = true;
                        $lock_price = $work_info['experience_price'];
                    }
                }
            }
        }

        if(in_array(2, $post_type) || in_array(3, $post_type)){
            if(in_array(2, $post_type)){
                // $lock_price = $work_info['digitization_price'];
                $post_num = $work_info['digitization_number'];
                $post_price = $work_info['digitization_price'];
                // $copy_num = $work_info['digitization_number'];
            // }else{
                $copy_price = $work_info['copyright_price'];
                // $post_num = $work_info['copyright_number'];
                $copy_num = $work_info['copyright_number'];
            }
            $is_need_buy = true;
            
        }

    }

    return ['is_lock' => $is_need_buy,'lock_price' => $lock_price,'post_type' => $post_type,'post_num' => $post_num,'post_price' => $post_price,'copy_price' => $copy_price,'copy_num' => $copy_num];
}

// nft公共筛选
function nftStatus($type = 1,$status = -1,$is_market = -1)
{
    $work_mod = new \app\common\model\UserWorks();
    $sale_mod = new \app\common\model\UserSale();

    $articleModel = new \app\common\model\Article();
    $musicModel = new \app\common\model\Files();
    $gameModel = new \app\common\model\Game();
    $imgModel = new \app\common\model\Pictures();

    $map['is_draft'] = 2;
    if($type != -1){
        $map['type'] = $type;
    }
    
    // if($is_market != -1){
    //     $map['is_market'] = $is_market;
    // }

    $no_ids = $work_mod->whereIn('is_draft',[0,1,3])->where('type',$type)->column('obj_id');

    $ids = '';
    
    // 探索
    if(!empty($status) && $status !=-1){
        // 1.立即解锁2.新品3.免费-1全部4.立即购买5.拍卖中
        for ($i=0; $i < count($status); $i++) { 
            if($status[$i] == 1){
                // 立即解锁
                $map['post_type'] = 1;
                $map['experience_type'] = 2;

            }elseif($status[$i] == 2){
                // 新品
                if($type == 1){
                    // 文章
                    $ids_obj = $articleModel->where('is_new',1)->whereNotIn('id',$no_ids)->column('id');
                }elseif($type == 2){
                    // 音频
                    $ids_obj = $musicModel->where('is_new',1)->whereNotIn('id',$no_ids)->column('id');
                }elseif($type == 3){
                    // 游戏
                    $ids_obj = $gameModel->where('is_new',1)->whereNotIn('g_id',$no_ids)->column('g_id');
                }elseif($type == 4){
                    // 插画
                    $ids_obj = $imgModel->where('is_new',1)->whereNotIn('id',$no_ids)->column('id');
                }else{
                    // 全部
                    // 文章
                    $ids_obj_1 = $articleModel->where('is_new',1)->whereNotIn('id',$no_ids)->column('id');
                    // 音频
                    $ids_obj_2 = $musicModel->where('is_new',1)->whereNotIn('id',$no_ids)->column('id');
                    // 游戏
                    $ids_obj_3 = $gameModel->where('is_new',1)->whereNotIn('g_id',$no_ids)->column('g_id');
                    // 插画
                    $ids_obj_4 = $imgModel->where('is_new',1)->whereNotIn('id',$no_ids)->column('id');

                    $ids_obj = $ids_obj_1+$ids_obj_2+$ids_obj_3+$ids_obj_4;
                }
                // return ['key' => 'is_new','val' => 1];
                // return;
            }elseif($status[$i] == 3){
                // 免费
                $map['post_type'] = 1;
                $map['experience_type'] = 1;
            }elseif($status[$i] == 4){
                // 立即购买-固定价格
                $where['type'] = 2;
                $where['obj_type'] = $type;
                $where['status'] = 0;
               
                if(!empty($no_ids)){
                    // $where[] = ['obj_id','NOT IN',$no_ids];
                    $ids_obj = $sale_mod->where($where)->whereNotIn('obj_id',$no_ids)->column('obj_id');
                }else{
                    $ids_obj = $sale_mod->where($where)->column('obj_id');
                }
                
                // dump($ids_obj);die;
                // if(empty($ids_obj)){
                //     return false;
                // }else{
                    // return $ids_obj;
                // }
            }elseif($status[$i] == 5){
                // 拍卖中
                $where['type'] = 3;
                $where['obj_type'] = $type;
                $where['status'] = 0;
                 if(!empty($no_ids)){
                    // $where[] = ['obj_id','NOT IN',$no_ids];
                    $ids_obj = $sale_mod->where($where)->whereNotIn('obj_id',$no_ids)->column('obj_id');
                }else{
                    $ids_obj = $sale_mod->where($where)->column('obj_id');
                }
                // if(empty($ids_obj)){
                //     return false;
                // }else{
                //     return $ids_obj;
                // }
            }
            
            if($status[$i] == 2){
                $ids_item = $ids_obj;
            }else{
                if($status[$i] == 4 || $status[$i] == 5){
                    $ids_item = $ids_obj;
                }else{
                    $ids_item = $work_mod->where($map)->column('obj_id');
                }
            }
            
            
            if(empty($ids_item)){
                return false;
            }else{
                $ids .= implode(',',$ids_item).',';
            }
        }
        
        if(empty($ids)){
            return false;
        }else{
            $ids = explode(',',$ids);
            // $unique_arr = array_unique($ids);
            // $repeat_arr = array_diff_assoc($ids,$unique_arr);
            // $ids = array_filter($ids);
            // dump(array_count_values($ids));die;
            if(count($status) > 1){
                
                return array_filter(getArrCommon($ids));
            }else{
                return array_filter($ids);
            }
            
        }

        // 市场
        // 1.立即购买2.拍卖中3.新品

    }else{
        $ids = $work_mod->where($map)->column('obj_id');
        
        if(empty($ids)){
            return false;
        }else{
            return $ids;
        }
    }

}

//判断是否可以出售
function isMyNft($nft_id = 0,$type = 1,$user_id = 0){
    $have_mod = new \app\common\model\UserHave();
    $works_mod = new \app\common\model\UserWorks();
    $sale_mod = new \app\common\model\UserSale();

    // 数字化发行需出售
    $work_info = $works_mod->where(['obj_id' => $nft_id,'type' => $type])->find();
    $post_type = explode(',',$work_info['post_type']);
    if(in_array(2, $post_type)){
        // return true;
        // 判断是否寄售
        // $sale_info = $sale_mod->where(['obj_id' => $nft_id,'obj_type' => $type,'status' => 0])->find();
        // if($sale_info){
            // 是否是我藏品
            $have_res = $have_mod->where(['user_id' => $user_id,'nft_id' => $nft_id,'type' => $type])->count();

            return $have_res > 0 || ($user_id == $work_info['user_id']);
        // }else{
        //     return false;
        // }
       
    }else{
        if($type == 5){
            return $have_mod->where(['user_id' => $user_id,'nft_id' => $nft_id,'type' => $type])->count() > 0;
        }else{
            return false;
        }
        
    }
}

// 生成我的藏品
function makeMyNft($nft_id = 0,$type = 1,$user_id = 0,$opt = 1,$post_data = array(),$address = '',$buy_type = 0)
{
    $have_mod = new \app\common\model\UserHave();
    $digit_mod = new \app\common\model\DigitNft();
    $work_mod = new \app\common\model\UserWorks();

    if($opt == 1){
        // 发行
        if(!empty($post_data)){
            // 判断是否是数字化发行
            $post_type = explode(',', $post_data['post_type']);
            if(in_array(2, $post_type)){
                for ($i=0; $i < $post_data['digitization_number']; $i++) { 
                    $digit_data = [
                        'nft_id' => $nft_id,
                        'type' => $type,
                        'user_id' => $user_id,
                        'nft_code' => time()
                    ];
                    $digit_mod->insert($digit_data);
                }
                
                // 第一次生成拥有者
                $have_data = [
                    'user_id' => $user_id,
                    'nft_id' => $nft_id,
                    'type' => $type,
                    'address' => $address
                ];

                $have_mod->insert($have_data);
            }else{
                // 非数字化发行-没有拥有者
               
            }

        }else{

            // 第一次生成拥有者
            $have_data = [
                'user_id' => $user_id,
                'nft_id' => $nft_id,
                'type' => $type,
                'address' => $address
            ];

            $have_mod->insert($have_data);
            
        }
    }else{
        // 购买

        // if($buy_type == 1){
        //     // 购买藏品
            
        // }elseif($buy_type == 2){
        //     // 购买版权
        // }else{

        // }
        // 数字化发行
        if(!empty($post_data)){
            $post_type = explode(',', $post_data['post_type']);
            if(in_array(2, $post_type)){
                $d_id = $digit_mod->where(['nft_id' => $nft_id,'type' => $type,'status' => 0])->value('id');
                $d_res = $digit_mod->where('id',$d_id)->update(['buyer_id' => $user_id,'status' => 1]);
                // 判断剩余库存
                if($d_res){
                   // 最后一件则改变所有者否则增加拥有者
                   $last_num = $digit_mod->where('status',0)->count();
                   if($last_num == 0){
                       // 改变拥有者
                       $have_info = $have_mod->where(['nft_id' => $nft_id,'type' => $type])->find();
                       // 改变拥有者
                       if($have_info['user_id'] != $user_id){
                           $have_mod->where('id',$have_info['id'])->update(['user_id' => $user_id]);
                       }
                   }else{
                       // 增加拥有者
                       $have_data = [
                           'user_id' => $user_id,
                           'nft_id' => $nft_id,
                           'type' => $type,
                           'address' => $address
                       ];

                       $have_mod->insert($have_data);
                   } 
                }
                
                // 数字化版权
                if(in_array(3, $post_type)){
                    $copy_mod = new \app\common\model\UserCopyright();
                    $work_info = $work_mod->where(['obj_id' => $nft_id,'type' => $type])->find();
                    if($work_info['copyright_number'] > 0){
                        // 判断剩余库存
                        $copy_data = [
                            'user_id' => $user_id,
                            'obj_id' => $nft_id,
                            'type' => $type,
                            'number' => 1,
                            'price' => $work_info['copyright_price'],
                            'ctime' => time()
                        ];

                        $copy_mod->insert($copy_data);
                        
                        $userWalletModel = new \app\common\model\userWallet();
                        // 平台版权抽成
                        $platform_fee = $work_info['copyright_price']*getSetting('platform_copy');
                        // 获取版权分润
                        $copy_fee = ($work_info['copyright_price']-$platform_fee)/$work_info['copyright_number'];
                          
                        
                        // 扣除作者版税
                        // $userWalletModel->where(['user_id' => $user_id,'type' => 1])->setDec('balance',($copy_fee+$platform_fee));



                        // 扣除库存
                        $work_mod->where(['obj_id' => $nft_id,'type' => $type])->setDec('copyright_number',1);
                    }
                }

            }else{
                // 数字化版权
                if(in_array(3, $post_type)){
                    $copy_mod = new \app\common\model\UserCopyright();
                    $work_info = $work_mod->where(['obj_id' => $nft_id,'type' => $type])->find();
                    if($work_info['copyright_number'] > 0){
                        // 判断剩余库存
                        $copy_data = [
                            'user_id' => $user_id,
                            'obj_id' => $nft_id,
                            'type' => $type,
                            'number' => 1,
                            'price' => $work_info['copyright_price'],
                            'ctime' => time()
                        ];

                        $copy_mod->insert($copy_data);
                        
                        $userWalletModel = new \app\common\model\userWallet();
                        // 平台版权抽成
                        $platform_fee = $work_info['copyright_price']*getSetting('platform_copy');
                        // 获取版权分润
                        $copy_fee = ($work_info['copyright_price']-$platform_fee)/$work_info['copyright_number'];
                        
                        
                        // 扣除作者版税
                        // $userWalletModel->where(['user_id' => $user_id,'type' => 1])->setDec('balance',($copy_fee+$platform_fee));



                        // 扣除库存
                        $work_mod->where(['obj_id' => $nft_id,'type' => $type])->setDec('copyright_number',1);
                    }
                }

                $have_info = $have_mod->where(['nft_id' => $nft_id,'type' => $type])->find();

                if($have_info){
                    // 改变拥有者
                    if($have_info['user_id'] != $user_id){
                        $have_mod->where('id',$have_info['id'])->update(['user_id' => $user_id]);
                    }

                }
            }
        }else{
            $have_info = $have_mod->where(['nft_id' => $nft_id,'type' => $type])->find();

            if($have_info){
                // 改变拥有者
                if($have_info['user_id'] != $user_id){
                    $have_mod->where('id',$have_info['id'])->update(['user_id' => $user_id]);
                }

            }else{
                // 增加拥有者
                $have_data = [
                    'user_id' => $user_id,
                    'nft_id' => $nft_id,
                    'type' => $type,
                    'address' => $address
                ];

                $have_mod->insert($have_data);
            }
        }

    }
   
    
}

// 判断是否显示出价按钮
function isPrice($nft_id = 0,$type = 1,$user_id = '')
{
    // 判断是否已经出售
    $price_status = true;

    // $sale_mod = new \app\common\model\UserSale();
    $works_mod = new \app\common\model\UserWorks();

    // $sale_info = $sale_mod->where(['obj_id' => $nft_id,'obj_type' => $type,'status' => 0])->find();

    // if($sale_info['type'] == 2){
    //     return false;
    // }else{
    // 内容体验与自己的没有出价
        $work_info = $works_mod->where(['obj_id' => $nft_id,'type' => $type])->find();
        if($work_info['user_id'] == $user_id){
            return false;
        }

        if(in_array(1,explode(',', $work_info['post_type']))){
            $price_status = false;
        }else{
            if(!empty($user_id) && $work_info['user_id'] == $user_id){
                return false;
            }else{
                $have_mod = new \app\common\model\UserHave();
                $have_user = $have_mod->where(['nft_id' => $nft_id,'type' => $type,'user_id' =>$user_id])->count();

                if($have_user > 0){
                    return false;
                }
                $price_status = true;
            }
            
        }
        // if($sale_info['status'] == 2){
        //     return true;
        // }else{
            
        // }
    // }

    return $price_status;
}

// 判断是否是市场
function isMarket($nft_id = 0,$type = 1)
{
    $sale_mod = new \app\common\model\UserSale();
    $sale_info = $sale_mod->where(['obj_id' => $nft_id,'obj_type' => $type,'status' => 0])->find();

    return !empty($sale_info) ? 1 : 0;

}

//获取市场数据
function getMarketData($is_market = -1,$type = -1)
{
    $sale_mod = new \app\common\model\UserSale();
    $works_mod = new \app\common\model\UserWorks();

    if($type != -1){
        $map[] = ['obj_type','eq',$type];
        $where['type'] = $type;
    }

    $where['is_draft'] = 2;
    $allow_ids = $works_mod->where($where)->column('obj_id');
    if(!empty($allow_ids)){
        $map[] = ['obj_id','IN',$allow_ids];
    }

    $map[] = ['status','eq',0];
    $sale_ids = $sale_mod->where($map)->column('obj_id');
    // if($is_market != -1){
        if($is_market == 1){
            // 市场-获取寄售中,排除未审核通过的
            
            return $sale_ids;
        }else{
            // 探索-排除寄售中-内容体验除外
            $need_ids = [];
            $work_data = $works_mod->where('type',$type)->where('is_draft',2)->select();
            if(!empty($work_data)){
                foreach ($work_data as $key => $value) {
                    if(!empty($value['post_type'])){
                        $post_type = explode(',',$value['post_type']);
                        if(in_array(1, $post_type)){
                            $need_ids[] = $value['obj_id'];
                        }
                    }
                    
                }

            }


            // $map[] = ['status','neq',0];
            $last_ids = array_diff($allow_ids, $sale_ids);
            return array_merge($need_ids,$last_ids);
        }
        
        
    // }
} 

// 求数组并集
function getArrCommon($arr = array())
{
    if(!empty($arr)){
        $n_arr = array_count_values($arr);
        $new_arr = [];
        foreach ($n_arr as $key => $value) {
            if($value > 1){
                $new_arr[] = $key;
            }
        }

        return empty($new_arr) ? $arr : $new_arr;
    }
}

// 判断是否可以购买
function canBuyNft($nft_id = 0,$type = 1,$user_id = 0)
{

    $works_mod = new \app\common\model\UserWorks();
    $digit_mod = new \app\common\model\DigitNft();
    $role_mod = new \app\common\model\Goods();
    $sale_mod = new \app\common\model\UserSale();

    // $price_mod = new \app\common\model\UserPrice();

    // 非数字化发行，不判断限购数量
    // if($type == 5){
    //     return true;
    // }
    $map['type'] = $type;
    $map['obj_id'] = $nft_id;
    // $map['user_id'] = $user_id;

    $work_info = $works_mod->where($map)->find();
    
    if(empty($work_info) && $type != 5){
        return false;
    }

    $have_mod = new \app\common\model\UserHave();
    $have_info = $have_mod->where(['nft_id' => $nft_id,'type' => $type])->column('user_id');
     // dump($have_info);die;
    if($work_info['user_id'] == $user_id){
        return false;//我的作品不需要购买
    }
   
    if(in_array($user_id,$have_info)){
        return false;//已拥有不需要购买
    }
    
    // if(!empty($have_info)){
    //     if(in_array($user_id,$have_info)){
    //         return false;//我的作品不需要购买
    //     }
    // }
    // if($work_info['user_id'] != $have_info['user_id']){
    //     return false;
    // }

    // return !empty($work_info) ? true : false;

    // $is_my = isMyProduct($nft_id,$type,$user_id);
    // if($is_my){
    //     return false;//我的作品不需要购买
    // }

    $post_info = $works_mod->where(['obj_id' => $nft_id,'type' => $type])->find();
    
    if($type == 5){
        // 角色
        $role_market = $role_mod->where('id',$nft_id)->value('is_market');
        return $role_market == 1;
    }else{
        // 其他
        $post_type = explode(',', $post_info['post_type']);
        
        if(empty($post_type)){
            return false;
        }
        if(in_array(2, $post_type) || in_array(3, $post_type)){
            // 判断是否寄售
            $sale_info = $sale_mod->where(['obj_id' => $nft_id,'obj_type' => $type,'status' => 0])->find();
            if($sale_info){
                // 已购买的不可以再购买
                $buy_count = $digit_mod->where('buyer_id',$user_id)->where('nft_id',$nft_id)->where('type',$type)->count();
                $can_buy = $digit_mod->where(['nft_id' => $nft_id,'type' => $type,'status' => 0])->count();
                if($buy_count > 0){
                    return false;
                }else{
                    return $can_buy > 0;
                }

                // if($buy_count <= 0){
                    // 数量不足时不能购买
                    
                // }
            }else{
                return false;
            }
            
            
        }else{
            if(in_array(1, $post_type)){
                return false;
            }
            
            $other_market = $works_mod->where(['obj_id' => $nft_id,'type' => $type])->value('is_market');
            return $other_market == 1;
        }
        
    }

    
    // if($post_info['post_type'] == 2){
    //     // return true;
    //     $can_buy = $digit_mod->where(['nft_id' => $nft_id,'type' => $type,'status' => 0])->count();
    //     // 判断有没有人出价
    //     $price_can = $price_mod->where('p_id',$nft_id)->where('type',$type)->where('e_time','egt',time())->count();
    //     if($can_buy == 0 && $price_can == 0){
    //         return false;
    //     }else{
    //         return true;
    //     }
    // }else{
    //     // if($post_info['post_type'] == 1){
    //         return false;
    //     // }
    // }

    
}

// 获取NFT细节
function getOtherDetail($nft_id = 0,$type = 1)
{
    // 判断是否是角色类型
    $history_mod = new \app\common\model\MakeNftHistory();
    $nft_info = $history_mod->where(['nft_id' => $nft_id,'type' => $type])->find();
    $rate_fee = getSetting('rate_fee');

    if($type == 5){
        $role_mod = new \app\common\model\Goods();
        $royalties = $role_mod->where('id',$nft_id)->value('royalties');
        $return_data = [
            'address' => '0xFfa4b223919063574E54b6FA46Dc6e28F95f0058',
            'token_id' => $nft_info['uri'],
            'type' => 'ERC-721',
            'ethereum' => 'Ethereum',
            'royalties' => empty($royalties) ? 0 : $royalties,
            'rate_fee' => $rate_fee,
        ];

    }else{
        $user_mod = new \app\common\model\User();
        $royalties = $user_mod->where('id',$nft_info['user_id'])->value('royalties');

        $return_data = [
            'address' => '0xE81cdfEEcFf981c535E2fE0Cdfa93489B86399bF',
            'token_id' => $nft_info['uri'],
            'type' => 'ERC-721',
            'ethereum' => 'Ethereum',
            'royalties' => empty($royalties) ? 0 : $royalties,
            'rate_fee' => $rate_fee,
        ];
    }

    return $return_data;
}

// 获取拥有者信息
function getNftMaster($id = '',$type = 1)
{
    $digit_mod = new \app\common\model\DigitNft();
    $user_mod = new \app\common\model\User();
    $wallect_mod = new \app\common\model\userWallet();
    $sale_mod = new \app\common\model\UserSale();

    $user_data = $digit_mod->where(['nft_id' => $id,'type' => $type,'status' => 1])->column('buyer_id');
    $user_data_2 = $digit_mod->where(['nft_id' => $id,'type' => $type,'status' => 1])->value('user_id');
    
    $user_data[] = $user_data_2;
    // $user_data = array_push($user_data_2,$user_data_1);
   
    $return_data = [];

    $sale_map[]= ['obj_id','eq',$id];
    $sale_map[]= ['obj_type','eq',$type];
    $sale_map[]= ['user_id','in',$user_data];

    $min_user = $sale_mod->where($sale_map)->order('price asc')->value('user_id');
    if(!empty($min_user)){
        $user_data_name = $user_mod->where('id',$min_user)->find();
        $return_data['user_name'] = $user_data_name['username'];
    }else{
        $return_data['user_name'] = '-';
    }

    $return_data['list'] = $user_mod->whereIn('id',$user_data)->field('id,username,avatar')->select();
    foreach ($return_data['list'] as $key => $value) {
        $return_data['list'][$key]['avatar'] = _sImage($value['avatar']);
        $return_data['list'][$key]['address'] = $wallect_mod->where('user_id',$value['id'])->order('is_default desc')->value('address');
    }

    return $return_data;
}

// 获取版权拥有者信息
function getNftMasterCopy($id = '',$type = 1)
{
    $copy_mod = new \app\common\model\UserCopyright();
    $user_mod = new \app\common\model\User();
    $wallect_mod = new \app\common\model\userWallet();
    $sale_mod = new \app\common\model\UserSale();

    $user_data = $copy_mod->where(['obj_id' => $id,'type' => $type])->order('price asc')->column('user_id');
    // $user_data_2 = $digit_mod->where(['nft_id' => $id,'type' => $type,'status' => 1])->value('user_id');
    
    // $user_data[] = $user_data_2;
    // $user_data = array_push($user_data_2,$user_data_1);
   
    $return_data = [];

    // $sale_map[]= ['obj_id','eq',$id];
    // $sale_map[]= ['obj_type','eq',$type];
    // $sale_map[]= ['user_id','in',$user_data];

    // $min_user = $sale_mod->where($sale_map)->order('price asc')->value('user_id');
    if(!empty($min_user)){
        $user_data_id = $copy_mod->where(['obj_id' => $id,'type' => $type])->order('price asc')->value('user_id');

        $return_data['user_name'] = $user_mod->whereIn('id',$user_data_id)->value('username');
    }else{
        $return_data['user_name'] = '-';
    }

    $return_data['list'] = $user_mod->whereIn('id',$user_data)->field('id,username,avatar')->select();
    foreach ($return_data['list'] as $key => $value) {
        $return_data['list'][$key]['avatar'] = _sImage($value['avatar']);
        $return_data['list'][$key]['address'] = $wallect_mod->where('user_id',$value['id'])->order('is_default desc')->value('address');
    }

    return $return_data;
}

// 公有排序
function commonSort($type = 1,$sort = -1)
{
    // $works_mod = new \app\common\model\UserWorks();
    // $sale_mod = new \app\common\model\UserSale();

    if($type == 3){
        $sort_key = 'g_id';
    }else{
        $sort_key = 'id';
    }

    if($sort != -1){
        // 最近上市// 最近创建
        if($sort == 1 || $sort == 2){
            return $sort_key.' desc';
        }elseif($sort == 3){
            // 价格:从低到高
            return 'price asc';
        }elseif($sort == 4){
            // 价格:从高到底
            return 'price desc';
        }elseif($sort == 5){
            // 最久远的
            return $sort_key.' asc';
        }elseif($sort == 6){
            // 最近出售
            if($type == 5){
                return $sort_key.' desc';
            }else{
                return 'is_new desc';
            }
            
        }elseif($sort == 7){
            // 拍卖即将结束
            return $sort_key.' asc';
        }elseif($sort == 8){
            // 最高成交价
            return 'price desc';
        }
        
    }else{
        $sort_type = getSetting('sort_type_'.$type);
        if(!empty($sort_type)){
            if($sort_type == 1){
                // 时间
                $order = $sort_key.' desc';
            }elseif($sort_type == 2){
                // 热度
                $order = 'likes desc';
            }elseif($sort_type == 3){
                // 手动排序
                $order = 'sort asc';
            }else{
                $order = $sort_key.' desc';
            }
        }else{
            $order = $sort_key.' desc';
        }

        return $order;
    }
}

// 判断是否可以取消拍卖和降低价格
function isCancel($id = '',$type = 1,$user_id = '')
{   
    $sale_mod = new \app\common\model\UserSale();

    return $sale_mod->where(['obj_id' => $id,'obj_type' => $type,'status' => 0,'user_id' => $user_id])->count() > 0;

}

// 获取藏品拍卖剩余时间-秒
function saleLastTime($id = '',$type = 1)
{
    $sale_mod = new \app\common\model\UserSale();
    $sale_info = $sale_mod->where(['obj_id' => $id,'obj_type' => $type,'status' => 0])->find();
    // if(empty($end_sale_time)){
        $end_at = date('Y-m-d H:i:s',$sale_info['end_time']);
    // }

    $start_at = date('Y-m-d H:i:s',time());
    $return_time = strtotime($end_at) - strtotime($start_at);
    if($sale_info['type'] == 2){
        return 0;
    }
    return $return_time > 0 ? $return_time : 0;

}

// 判断是否可以购买
function canBuy($id = '',$type = 1,$user_id = '')
{
    $have_mod = new \app\common\model\UserHave();

    if($type != 5){
        return $have_mod->where(['nft_id' => $id,'type' => $type,'user_id' => $user_id])->count() > 0;
    }
    
}

// 是否显示购买选项
function showBuy($id = '',$type = 1)
{
    $works_mod = new \app\common\model\UserWorks();

    $obj_work = $works_mod->where(['obj_id' => $id,'type' => $type])->find();

    $post_type = explode(',',$obj_work);

    if(in_array(2,$post_type) && in_array(3,$post_type)){
        return true;
    }

    return false;
}

// 添加购买记录
function addBuyLog($id = '',$type = 1,$buy_id = '',$buy_type = 1,$price = 0,$address = '')
{
    // 查询创作者信息
    $works_mod = new \app\common\model\UserWorks();
    $user_mod = new \app\common\model\User();
    $digt_mod = new \app\common\model\DigitNft();
    $buy_mod = new \app\common\model\BuyLog();

    $map['obj_id'] = $id;
    $map['type'] = $type;

    $work_info = $works_mod->where($map)->find();
    
    $work_count = $digt_mod->where(['nft_id' => $id,'type' => $type,'status' => 0,'user_id' => $buy_id])->count();//作者持有版权份数

    $my_work_count = $digt_mod->where(['nft_id' => $id,'type' => $type,'status' => 1,'buyer_id' => $buy_id])->count();//个人持有版权份数
    
    // 手续费
    $rate_fee = getSetting('rate_fee');//手续费比例
    $serice_fee = $price * ($rate_fee/100);//手续费
    // 版权费
    $user_royalties = $user_mod->where('id',$work_info['user_id'])->value('royalties');
    // 交易编号
    // $tran_sn = $work_info[];
    // 收入
    if($buy_type == 1){
        // 内容体验
        $worker_incomne = $price-$serice_fee;//创作者收入
        $incomne = $worker_incomne;//作品收入
    }elseif($buy_type == 2){
        // 数字藏品
        $worker_incomne = $price*$user_royalties;//创作者收入
        $incomne = $price-$rate_fee;//作品收入
    }else{
        // 数字版权
        $worker_incomne = ($price-$serice_fee-($price*$user_royalties)/$work_info['copyright_number'])*$work_count;//创作者收入
        $incomne = ($price-$serice_fee-($price*$user_royalties)/$work_info['copyright_number'])*$my_work_count;//作品收入
    }

    $saveData = [
        'obj_id' => $id,
        'obj_type' => $type,
        'price' => $price,
        'serice_fee' => $serice_fee,
        'worker_id' => $work_info['user_id'],
        'copy_fee' => ($price*$user_royalties),
        'user_id' => $buy_id,
        'income' => $worker_incomne,
        'type' => $buy_type,
        'ctime' => time(),
        'address' => $address,
        'works_fee' => $incomne
    ];

    $buy_mod->insert($saveData);
}

// 获取nft价格
function getNftPrice($nft_id = 0,$type = 1)
{
    $works_mod = new \app\common\model\UserWorks();
    $goods_mod = new \app\common\model\Goods();

    if($type == 5){
        $goods_mod->where('id',$nft_id)->value('price');
    }else{
        $work_info = $works_mod->where(['obj_id' => $nft_id,'type' => $type])->find();
        $post_type = explode(',', $work_info['post_type']);

        if($work_info['is_market'] == 0){
            return empty($work_info['experience_price']) ? 0 : $work_info['experience_price'];
        }else{
            if(in_array(2,$post_type)){
                return $work_info['digitization_price'];
            }else{
                if(in_array(3, $post_type)){
                    return $work_info['copyright_price'];
                }
            }

            return 0;
        }
    }
    
}

/* 链端接口返回日志
* $url 请求地址
* $params 请求参数
* $result 请求结果
*/ 
function addLog($url = '',$params = '',$result = '')
{
    $time = date('Y-m-d H:i:s',time());
    $json_params = json_encode($params);

    $str = '当前时间:'.$time.';请求地址:'.$url.';请求参数:'.$json_params.';请求结果:'.$result.PHP_EOL;

    @file_put_contents('chain_log.txt',$str,FILE_APPEND);
}
    
// 审核NFT
function aduitNft($nft_id = 0,$type = 1,$status = 0)
{
    $workModel = new \app\common\model\UserWorks();
    $work_info = $workModel->where(['obj_id' => $nft_id,'type' => $type])->find();

    if(!$work_info){
        return false;
    }

    if($status == 2){

        $saleModel = new \app\common\model\UserSale();
        $userModel = new \app\common\model\User();
        // 获取版税
        $royalties = $userModel->where('id',$work_info['user_id'])->value('royalties');

        $post_type = explode(',', $work_info['post_type']);

        if(in_array(2, $post_type) || in_array(3, $post_type)){

            if(in_array(3, $post_type)){
                $price = $work_info['copyright_price'];
            }

            if(in_array(2, $post_type)){
                $price = $work_info['digitization_price'];
            }
            
            $rate_fee = getSetting('rate_fee');

            $sale_data = [
                'sale_type' => 2,
                'price' => $price,
                'start_time' => 0,
                'end_time' => $work_info['end_time'],
                'id' => $nft_id,
                'price_type' => 1,
                'type' => $type,
                'gas_fee' => $rate_fee,
                'fee_total' => $price*$rate_fee/100+$price*$royalties/100
            ];
            
            $saleModel->saleMyWorks($work_info['user_id'],$sale_data);
        }
        
    }

    return $workModel->where('obj_id',$nft_id)->where('type',$type)->update(['is_draft' => $status]);
}

// 获取前端菜单
function getMenuList()
{
    $menu_path = [
        [
            'name' => '页面: 首页',
            'value' => '/home'
        ],
        [
            'name' => '页面: 品牌直售',
            'value' => '/brandDirectSelling'
        ],
        [
            'name' => '页面: 探索',
            'value' => '/explore/recommend'
        ],
        [
            'name' => '页面: 探索 推荐',
            'value' => '/explore/recommend'
        ],
        [
            'name' => '页面: 探索 所有专辑',
            'value' => '/explore/album'
        ],
        [
            'name' => '页面: 探索 品牌角色',
            'value' => '/explore?type=brandRole'
        ],
        [
            'name' => '页面: 探索 插画',
            'value' => '/explore?type=ikon'
        ],
        [
            'name' => '页面: 探索 音频',
            'value' => '/explore?type=audio'
        ],
        [
            'name' => '页面: 探索 同人文章',
            'value' => '/explore?type=colleagues'
        ],
        [
            'name' => '页面: 探索 游戏',
            'value' => '/explore?type=game'
        ],
        [
            'name' => '页面: 市场',
            'value' => '/bazaar/album'
        ],
        [
            'name' => '页面: 市场 所有专辑',
            'value' => '/bazaar/album'
        ],
        [
            'name' => '页面: 市场 品牌角色',
            'value' => '/bazaar?type=brandRole'
        ],
        [
            'name' => '页面: 市场 插画',
            'value' => '/bazaar?type=ikon'
        ],
        [
            'name' => '页面: 市场 音频',
            'value' => '/bazaar?type=audio'
        ],
        [
            'name' => '页面: 市场 同人文章',
            'value' => '/bazaar?type=colleagues'
        ],
        [
            'name' => '页面: 市场 游戏',
            'value' => '/bazaar?type=game'
        ],
        [
            'name' => '页面: 次元',
            'value' => '/dimension/helpCenter'
        ],
        [
            'name' => '页面: 帮助中心',
            'value' => '/dimension/helpCenter'
        ],
        [
            'name' => '页面: 伙伴',
            'value' => '/dimension/partner'
        ],
        [
            'name' => '页面: 博客',
            'value' => '/dimension/blogs'
        ],
        [
            'name' => '页面: 文档',
            'value' => '/dimension/document'
        ],
        // [
        //     'name' => '关于我们',
        //     'value' => '/dimension/about'
        // ],
        [
            'name' => '页面: 作品管理',
            'value' => '/framer/timetable'
        ],
        [
            'name' => '页面: 专辑管理',
            'value' => '/framer/albumMmanagement'
        ],
    ];

    $docModel = new \app\common\model\Document();

    $list = $docModel->select();

    foreach ($list as $key => $value) {
        array_push($menu_path, ['name' => '文章: '.$value['title'],'value' => '/dimension/about'.'?id='.$value['id']]);
    }
   
    return $menu_path;
}