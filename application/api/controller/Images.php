<?php
namespace app\api\controller;
use app\common\model\Images as imageModel;
use app\common\model\Pictures as PicturesModel;
use app\common\controller\Api;
use think\facade\Cache;
use Request;

/**
 * 图片
 * Class Images
 * @package app\api\controller
 */
class Images extends Api
{
    public function upload()
    {
        $imageModel = new imageModel();
        // dump($_REQUEST);die;
        $result     = $imageModel->saveImage();
        if ($result['status']) {
            $data = [
                'status' => true,
                'msg'    => '上传成功',
                'data'   => [
                    'image_id' => $result['data']['id'],
                    'url'      => $result['data']['url'],
                    'type'     => '',
                ]
            ];
            return $data;
        } else {
            return error_code(10035);
        }
    }

    // 插画列表
    public function getPaintings()
    {
        $picturesModel = new PicturesModel();

        $token = Request::param('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        $page = Request::param('page', 1);
        $type = Request::param('type', -1);//1人气2最新3关注
        $limit = Request::param('limit', 10);
        $is_my = Request::param('is_my', 0);//是否是个人作品
        $is_market = Request::param('is_market', -1);//是否是市场:0否1是-1全部
        $min_price = Request::param('min_price', 0);//最低价格
        $max_price = Request::param('max_price', 0);//最高价格
        $status = Request::param('status', -1);//1.立即解锁2.新品3.免费-1全部
        $sort = Request::param('sort', -1);//-1默认1.最近上市2最近创建3.价格:从低到高4.价格:从高到底5.最久远的6.最近出售7.拍卖即将结束8.最高成交价
        // if (!Cache::has("jshop_pictures_getpictureslist" .'_'. $limit . "_" . $page . "_" . $type."_".$user_id."_".$is_my)) {
            // Cache::set("jshop_pictures_getpictureslist" .'_'. $limit . "_" . $page . "_" . $type."_".$user_id."_".$is_my, $picturesModel->picturesList($user_id, $page, $limit,$type,$is_my), 3600 * 1);
        // }
        return $picturesModel->picturesList($user_id, $page, $limit,$type,$is_my,$cate_id,$is_market,$min_price,$max_price,$status,$sort);
        // return Cache::get("jshop_pictures_getpictureslist" .'_'. $limit . "_" . $page . "_" . $type."_".$user_id."_".$is_my);
    }

    // 插画详情
    public function getPaintingInfo()
    {
        $picturesModel = new PicturesModel();

        $token = Request::param('token');
        if(isset($token) && !empty($token)){
            $user_id = getUserIdByToken($token);
        }else{
            $user_id = 0;
        }

        $picture_id = Request::param('picture_id', 0);
        if (!$picture_id) return error_code(10051);
        // if (!Cache::has("jshop_picture_getpaintinginfo" . "_" . $picture_id)) {
        //     Cache::set("jshop_picture_getpaintinginfo". "_" . $picture_id, $picturesModel->pictureDetail($picture_id,$user_id), 3600 * 1);
        // }
        // return Cache::get("jshop_picture_getpaintinginfo". "_" . $picture_id);
        return $picturesModel->pictureDetail($picture_id,$user_id);
    }

    // 上传音频
    public function uploadAudio()
    {
        $result = error_code(10035);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');


        // 移动到框架应用根目录/uploads/ 目录下
        $maxSize  = config('jshop.file_size');
        $savepath = '/static/uploads/music';

        $filePath = ROOT_PATH . 'public' . $savepath;
        if (!is_dir($filePath)) {
            @mkdir($filePath);
        }
        // $info = $file->validate(['size' => $maxSize, 'ext' => 'mp3,wma,aac,ogg,mpc,flac,ape,wv'])->move($filePath);
        $imageModel = new \app\common\model\Images();

        //上传处理类
        $config        = [
            'rootPath' => ROOT_PATH . 'public',
            'savePath' => $savepath,
            'subName'  => ['get_date_dir', ''],
            'maxSize'  => $maxSize,
            'exts'     => 'mp3,ogg,mp4,wav,webm',
            'saveName' => ['uniqid', time()],
        ];

        $image_storage = config('jshop.image_storage');

        if (!$image_storage) {
            $image_storage = [
                'type' => 'Local',
            ];
        }

        //增加后台设置，如果设置则用后台设置的
        if (getSetting('image_storage_params')) {
            $image_storage = array_merge(['type' => getSetting('image_storage_type')], getSetting('image_storage_params'));
        }

        $upload = new \org\Upload($config, $image_storage['type'], $image_storage);
        $info   = $upload->upload();

        if ($info) {
            $first             = array_shift($info);
            $url               = getRealUrl($first['savepath'] . $first['savename']);
            // $iData['id']       = md5(get_hash($first['name']));
            // $iData['group_id'] = input('param.group_id', $group_id);
            // $iData['type']     = $image_storage['type'];
            // $iData['name']     = $first['name'];
            // $iData['url']      = $url;
            // $iData['ctime']    = time();
            // $iData['path']     = ROOT_PATH . 'public' . $first['savepath'] . $first['savename'];
            // if (!$imageModel->save($iData)) {
            //     $return_data['msg'] = error_code(10004, true);
            //     return $return_data;
            // }
            $result['status'] = true;
            $result['data']   = ['url' => $url];
            $result['msg']    = '保存成功';
        } else {
            $result['msg'] = $upload->getError();
        }

        return $result;

    }

    // 上传文本
    public function uploadArticle()
    {
        $result = error_code(10035);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');


        // 移动到框架应用根目录/uploads/ 目录下
        $maxSize  = config('jshop.file_size');
        $savepath = '/static/uploads/book';

        $filePath = ROOT_PATH . 'public' . $savepath;
        if (!is_dir($filePath)) {
            @mkdir($filePath);
        }
        // $info = $file->validate(['size' => $maxSize, 'ext' => 'mp3,wma,aac,ogg,mpc,flac,ape,wv'])->move($filePath);
        $imageModel = new \app\common\model\Images();

        //上传处理类
        $config        = [
            'rootPath' => ROOT_PATH . 'public',
            'savePath' => $savepath,
            'subName'  => ['get_date_dir', ''],
            'maxSize'  => $maxSize,
            'exts'     => 'txt,md,docx,doc',
            'saveName' => ['uniqid', time()],
        ];

        $image_storage = config('jshop.image_storage');

        if (!$image_storage) {
            $image_storage = [
                'type' => 'Local',
            ];
        }

        //增加后台设置，如果设置则用后台设置的
        if (getSetting('image_storage_params')) {
            $image_storage = array_merge(['type' => getSetting('image_storage_type')], getSetting('image_storage_params'));
        }

        $upload = new \org\Upload($config, $image_storage['type'], $image_storage);
        $info   = $upload->upload();
        if ($info) {
            $first             = array_shift($info);
            $url               = getRealUrl($first['savepath'] . $first['savename']);
            // $iData['id']       = md5(get_hash($first['name']));
            // $iData['group_id'] = input('param.group_id', $group_id);
            // $iData['type']     = $image_storage['type'];
            // $iData['name']     = $first['name'];
            // $iData['url']      = $url;
            // $iData['ctime']    = time();
            // $iData['path']     = ROOT_PATH . 'public' . $first['savepath'] . $first['savename'];
            // if (!$imageModel->save($iData)) {
            //     $return_data['msg'] = error_code(10004, true);
            //     return $return_data;
            // }
            $result['status'] = true;
            $result['data']   = ['url' => $url,'name' => $first['name']];
            $result['msg']    = '保存成功';
        } else {
            $result['msg'] = $upload->getError();
        }

        return $result;

    }
}