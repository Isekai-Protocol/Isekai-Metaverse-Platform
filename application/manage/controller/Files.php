<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use Request;
use app\common\controller\Manage;
use app\common\model\Images as imageModel;

/**
 * 文件上传
 * Class File
 *
 * @package app\Manage\controller
 */
class Files extends Manage
{
    public function uploadVideo()
    {
        $result = error_code(10035);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $maxSize  = config('jshop.file_size');
        $savepath = '/static/uploads/video';

        $filePath = ROOT_PATH . 'public' . $savepath;
        if (!is_dir($filePath)) {
            @mkdir($filePath);
        }
        $info = $file->validate(['size' => $maxSize, 'ext' => 'mp4'])->move($filePath);
        if ($info) {
            $fileName           = $info->getFilename();
            $url                = getRealUrl($savepath . '/' . $info->getSaveName());
            $iData['id']        = md5(get_hash($fileName));
            $iData['type']      = 'local';
            $iData['file_type'] = 'video';
            $iData['name']      = $fileName;
            $iData['url']       = $url;
            $iData['ctime']     = time();
            $iData['path']      = $filePath . '/' . $info->getSaveName();
            $files              = new \app\common\model\Files();
            if ($files->save($iData)) {
                $result['data']   = $iData;
                $result['status'] = true;
                $result['msg']    = '上传成功';
                return $result;
            }
        } else {
            // 上传失败获取错误信息
            $result['msg'] = $file->getError();
            return $result;
        }
    }

    // 上传音频
    public function uploadMusic()
    {
        $result = error_code(10035);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');


        // 移动到框架应用根目录/uploads/ 目录下
        $maxSize  = config('jshop.file_size');
        $savepath = '/static/uploads/music';

        // $filePath = ROOT_PATH . 'public' . $savepath;
        // if (!is_dir($filePath)) {
        //     @mkdir($filePath);
        // }
        // $info = $file->validate(['size' => $maxSize, 'ext' => 'mp3,wma,aac,ogg,mpc,flac,ape,wv'])->move($filePath);
        $imageModel = new \app\common\model\Images();

        //上传处理类
        $config        = [
            'rootPath' => ROOT_PATH . 'public',
            'savePath' => $savepath,
            'subName'  => ['get_date_dir', ''],
            'maxSize'  => $maxSize,
            'exts'     => 'mp3,wav,ogg,mp4,webm',
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
            $result['data']   = $url;
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
            'exts'     => 'txt,md',
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
