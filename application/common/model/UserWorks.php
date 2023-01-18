<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 我的作品模型
 * Class Area
 * @package app\common\model
 * @author keinx
 */
class UserWorks extends Common
{   
    // 添加我的作品
    public function doMyWork($user_id,$id,$type=1,$is_market=0,$is_draft=1,$work_id = '',$post_data = array(),$end_time = '')
    {

        $workData = [
            'obj_id' => $id,
            'type' => $type,
            'is_market' => $is_market,
            'is_draft' => $is_draft,
            'user_id' => $user_id,
            'end_time' => $end_time
        ];

        if(!empty($post_data)){
            $workData['post_type'] = $post_data['post_type'];
            $workData['experience_type'] = $post_data['experience_type'];
            $workData['experience_price'] = $post_data['experience_price'];
            $workData['digitization_number'] = $post_data['digitization_number'];
            $workData['digitization_price'] = $post_data['digitization_price'];
            $workData['copyright_number'] = $post_data['copyright_number'];
            $workData['copyright_price'] = $post_data['copyright_price'];
            // $workData['end_time'] = $post_data['end_time'];

            // 数字化发行-格式化价格
            $post_type = explode(',', $post_data['post_type']);
            
            if(in_array(2, $post_type)){
                $articleModel = new \app\common\model\Article();
                $musicModel = new \app\common\model\Files();
                $gameModel = new \app\common\model\Game();
                $imgModel = new \app\common\model\Pictures();

                if($type == 1){
                    // 小说
                    $articleModel->where('id',$id)->update(['price' => $post_data['digitization_price']]);
                
                }elseif($type == 2){
                    // 音频
                    $musicModel->where('id',$id)->update(['price' => $post_data['digitization_price']]);
                
                }elseif($type == 3){
                    // 游戏
                    $gameModel->where('g_id',$id)->update(['price' => $post_data['digitization_price']]);
                }else{
                    // 插画
                    $imgModel->where('id',$id)->update(['price' => $post_data['digitization_price']]);
                }

            }
        }

        if(isset($work_id) && !empty($work_id)){
            $this->save($workData,['id' => $work_id]);
            return $work_id;
        }else{
            $workData['ctime'] = time();
            return $this->insertGetId($workData);
        }


    }
}