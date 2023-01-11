<?php

namespace app\common\model;
// 用户点赞

class UserCare extends Common
{
    const CARE_ARTICLE = 1;//文章
    const CARE_AUDIO = 2;//音频
    const CARE_GAME = 3;//游戏
    const CARE_IMG = 4;//插画
    const CARE_ROLE = 5;//角色

    // 用户点赞
    public function addUserCare($user_id = '',$id = '',$type = 1,$act = 1)
    {
        if(empty($id)){
            return error_code(12601);
        }

        $article = new \app\common\model\Article();
        $files = new \app\common\model\Files();
        $pictures = new \app\common\model\Pictures();
        $game = new \app\common\model\Game();
        $role = new \app\common\model\Goods();

        if($act == 1){
            // 点赞
            $is_care = $this->where(['user_id' => $user_id,'obj_id' => $id,'type' => $type])->count();
            if($is_care > 0){
                return error_code(12602);
            }

            switch ($type) {
                case self::CARE_ARTICLE:
                    $article->where('id',$id)->setInc('likes',1);
                    break;
                case self::CARE_AUDIO:
                    $files->where('id',$id)->setInc('likes',1);
                    break;
                case self::CARE_IMG:
                    $pictures->where('id',$id)->setInc('likes',1);
                    break;
                case self::CARE_GAME:
                    $game->where('g_id',$id)->setInc('likes',1);
                    break;
                case self::CARE_ROLE:
                    $role->where('id',$id)->setInc('likes',1);
                    break;
                default:
                    $article->where('id',$id)->setInc('likes',1);
                    break;
            }

            $care_data = [
                'user_id' => $user_id,
                'obj_id' => $id,
                'type' => $type
            ];
            $res = $this->insert($care_data);

            return $res ? ['status' => true,'msg' => '点赞成功','data' => ''] : error_code(10004);

        }else{
            // 取消点赞
            $care_res = $this->where(['user_id' => $user_id,'obj_id' => $id,'type' => $type])->delete();

            if($care_res){
                switch ($type) {
                    case self::CARE_ARTICLE:
                        $article->where('id',$id)->setDec('likes',1);
                        break;
                    case self::CARE_AUDIO:
                        $files->where('id',$id)->setDec('likes',1);
                        break;
                    case self::CARE_IMG:
                        $pictures->where('id',$id)->setDec('likes',1);
                        break;
                    case self::CARE_GAME:
                        $game->where('g_id',$id)->setDec('likes',1);
                        break;
                    case self::CARE_ROLE:
                        $role->where('id',$id)->setDec('likes',1);
                        break;
                    default:
                        $article->where('id',$id)->setDec('likes',1);
                        break;
                }
            }
            
            return $care_res ? ['status' => true,'msg' => '取消点赞成功','data' => ''] : error_code(10018);
        }

    }
}