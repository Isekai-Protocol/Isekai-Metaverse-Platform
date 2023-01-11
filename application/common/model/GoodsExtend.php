<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: tianyu <tianyu@jihainet.com>
// +----------------------------------------------------------------------

namespace app\common\model;

class GoodsExtend extends Common
{
    //执行导入任务
    public function exec($params)
    {
        $message = [];
        try{
        	$part_mod = new \app\common\model\Assembly();
            $goods_part_mod = new \app\common\model\GoodsPart();
        	$goods_prop_mod = new \app\common\model\Prop();
            $goods_mod = new \app\common\model\Goods();
        	$card_mod = new \app\common\model\CardRole();

            $file = json_decode($params, true);
            $csv = new \org\Csv();
            $resCsv = $csv->import($file['file_path']);
            if($resCsv['status']){
                $header = $resCsv['data'][0];
                // dump($header);die;
                unset($resCsv['data'][0]);
                // $title=$userModel->csvHeader();
                // 定义键
                $role_id_key = 0;//角色id
                $role_name_key = 1;//角色名称
                $role_eng_name_key = 2;//角色英文名称
                $role_img_key = 3;//角色立绘
                $role_sex_key = 4;//性别
                $role_type_key = 5;//角色类型
                $role_size_key = 6;//头身
                $role_work_type_key = 7;//职业类目
                $role_work_key = 8;//职业
                $role_work_life_key = 9;//转生前职业
                $role_title_key = 10;//称号
                $role_title_val_key = 11;//称号赋值
                $role_history_key = 12;//历程
                $role_character_key = 13;//特性
                $role_character_val_key = 14;//特性赋值
                $role_position_key = 15;//立场
                $role_talent_1_key = 16;//天赋1
                $role_talent_2_key = 17;//天赋2
                $role_talent_3_key = 18;//天赋3
                $role_talent_4_key = 19;//天赋4
                $role_talent_img_key = 20;//天赋图标
                $role_skill_1_key = 21;//潜能1
                $role_skill_2_key = 22;//潜能2
                $role_skill_3_key = 23;//潜能3
                $role_skill_4_key = 24;//潜能4
                $role_skill_img_key = 25;//潜能图标
                $role_ears_key = 26;//耳朵
                $role_breastplate_key = 27;//胸甲
                $role_arms_key = 28;//武器
                $role_behind_key = 29;//披风后
                $role_right_key = 30;//翅膀右
                $role_lining_key = 31;//内衬
                $role_underwear_key = 32;//内衣
                $role_stock_key = 33;//丝袜
               	$role_special_key = 34;//特效

                $iData = [];
                // dump($resCsv['data']);die;
                // if($fields){
                    // $i = 0;
                    foreach ($resCsv['data'] as $key=>$val)
                    {
                    	if(!empty($val[$role_id_key])){
                			// 称号、称号赋值
                			$role_title_info = [];
                			if(isset($val[$role_title_key]) && !empty($val[$role_title_key])){
                				$role_title_temp = explode(',', $val[$role_title_key]);
                				$role_sort_temp = explode(',', $val[$role_title_val_key]);
                				$role_temp = [];

                				for ($i=0; $i < count($role_title_temp); $i++) { 
                					$role_temp = [
                						'title' => $role_title_temp[$i],
                						'sort' => isset($role_sort_temp[$i]) ? $role_sort_temp[$i] : $i+1 
                					];

                					$role_title_info[] = $role_temp;
                				}
                			}

                			// 特性、特性赋值
                			$character_info = [];
                			if(isset($val[$role_character_key]) && !empty($val[$role_character_key])){
                				$role_character_temp = explode(',', $val[$role_character_key]);
                				$role_val_temp = explode(',', $val[$role_character_val_key]);
                				$character_temp = [];

                				for ($j=0; $j < count($role_character_temp); $j++) { 
                					$character_temp = [
                						'name' => $role_character_temp[$j],
                						'value' => isset($role_val_temp[$j]) ? $role_val_temp[$j] : 0 
                					];

                					$character_info[] = $character_temp;
                				}
                			}

                			// 天赋、天赋图标
                			$talent_info = [];
                			// if(isset($val[13]) && !empty($val[13])){
                				$role_talent_temp = [$val[$role_talent_1_key],$val[$role_talent_2_key],$val[$role_talent_3_key],$val[$role_talent_4_key]];

                				$role_talent_img_temp = explode(',', $val[$role_talent_img_key]);
                				$talent_temp = [];

                				for ($k=0; $k < count($role_talent_temp); $k++) { 
                					if(isset($role_talent_temp[$k]) && !empty($role_talent_temp[$k])){
                						$talent_temp = [
                							'title' => $role_talent_temp[$k],
                							'img' => isset($role_talent_img_temp[$k]) ? findImgByRealName($role_talent_img_temp[$k]) : _sImage()
                						];
                					}

                					$talent_info[] = $talent_temp;
                				}
                			// }


                			// 潜能、潜能图标
                			$potential_info = [];
                			// if(isset($val[13]) && !empty($val[13])){
                				$role_potential_temp = [$val[$role_skill_1_key],$val[$role_skill_2_key],$val[$role_skill_3_key],$val[$role_skill_4_key]];

                				$role_potential_img_temp = explode(',', $val[$role_skill_img_key]);
                				$potential_temp = [];

                				for ($m=0; $m < count($role_potential_temp); $m++) { 
                					if(isset($role_potential_temp[$m]) && !empty($role_potential_temp[$m])){
                						$potential_temp = [
                							'title' => $role_potential_temp[$m],
                							'img' => isset($role_potential_img_temp[$m]) ? findImgByRealName($role_potential_img_temp[$m]) : _sImage()
                						];
                					}

                					$potential_info[] = $potential_temp;
                				}
                			// }

                			// 角色信息
                			$goods_temp = [
                                'name' => $val[$role_name_key],//角色名称
                				'name_eng' => $val[$role_eng_name_key],//角色英文名称
                				'sex' => $val[$role_sex_key],//性别:0无性1男2女
                				'work' => $val[$role_work_key],//职业
                				'bn' => get_sn(3),
                				'ctime' => time()
                			];

                			// goods_id
                			$goods_id = $goods_mod->insertGetId($goods_temp);

                			if($goods_id){
                				// 角色扩展信息
                				$extend_temp = [
                					'goods_id' => $goods_id,//角色id
                					'role_id' => $val[$role_id_key],//角色id
                					'role_img' => findImgByRealName($val[$role_img_key]),//角色立绘
                					'work_type' => $val[$role_work_type_key],//职业类目1士2农3工4商
                					'work_life' => $val[$role_work_life_key],//转生前职业
                					'role_title_info' => !empty($role_title_info) ? serialize($role_title_info) : '',//称号信息
                					'history' => $val[$role_history_key],//历程
                					'character_info' => !empty($character_info) ? serialize($character_info) : '',//特性信息
                					'position' => $val[$role_position_key],//立场
                					'talent_info' => !empty($talent_info) ? serialize($talent_info) : '',//天赋信息
                					'potential_info' => !empty($potential_info) ? serialize($potential_info) : '',//潜能信息
                                    'role_type' => $val[$role_type_key],//角色类别
                                    'figure' => $val[$role_size_key],//身材
                				];

                				$this->insert($extend_temp);

                				// 组件信息
                				$part_temp = [];
                				$key_number = $role_ears_key;
                				// dump($header);die;
                				for ($n=0; $n < 9; $n++) {
                					$part_id = $part_mod->where(['part_type' => $header[$key_number],'part_id' => $val[$key_number]])->value('id');
                					if(!empty($part_id)){
                						$part_temp = [
                							'goods_id' => $goods_id,
                							'part_id' => $part_id
                						];
                					}
                					
                					// dump($part_mod->getLastSql());
                					$key_number++;
                					// dump($key_number);
                					$goods_part_mod->insert($part_temp);

                				}

                                if($val[$role_type_key] == '哥布林' || $val[$role_type_key] == '老头'){
                                    $prop_role_img = _sImage(findImgByRealName($val[$role_img_key])).'?x-oss-process=image/crop,x_2082,y_2332,w_842,h_248/format,webp';
                                }else{
                                    $prop_role_img = _sImage(findImgByRealName($val[$role_img_key])).'?x-oss-process=image/crop,x_2086,y_2408,w_842,h_248/format,webp';
                                }

                                // 道具信息
                                $prop_data = [
                                    'goods_id' => $goods_id,
                                    'name' => '表情',
                                    'cover' => findImgByRealName($val[$role_img_key]),
                                    'role_img' => findImgByRealName($val[$role_img_key]),
                                    'prop_img' => $prop_role_img
                                ];

                                $goods_prop_mod->insert($prop_data);

                                // 抽卡机会
                                $chance_data = [
                                    'draw_id' => 1,
                                    'goods_id' => $goods_id,
                                    'chance' => 1
                                ];
                                
                                $card_mod->insert($chance_data);
                			}
                			
                			// $iData[] = [
                			// 	'goods' => $goods_temp,
                			// 	'extend_temp' => $extend_temp,
                			// 	'part_temp' => $part_temp
                			// ];
                			// if(!empty($val[1])){
                			// 	$temp_data = [
                			// 		'part_id' => $val[0],//ID
                			// 		'ears' => $val[1],//耳朵
                			// 		'breastplate' => $val[2],//胸甲
                			// 		'arms' => $val[3],//武器
                			// 		'cloak_after' => $val[4],//披风后
                			// 		'wing_right' => $val[5],//翅膀右
                			// 		'lining' => $val[6],//内衬
                			// 		'underwear' => $val[7],//内衣
                			// 		'silk_stockings' => $val[8],//丝袜
                			// 		'special_effect' => $val[9],//特效
                			// 	];
                			// }
                			
                		    // foreach($fields as $fkey=>$fval){
                		    //     $iData[$i][$fval['value']]=$val[$fval['index']];
                		    // }
                		    // $i++;
                    	}
                    	
                    }

                    // $this->insertAll($iData);
                    // dump($iData);
                    // die;
                // }
               
                // $uData['status'] = $ietaskModle::IMPORT_SUCCESS_STATUS;
                // $uData['message'] = '导入成功';
                // if($message){
                //     $uData['message'] .= json_encode($message);
                // }
                // $uData['utime'] = time();
                // $ietaskModle->update($uData, ['id' => $params['task_id']]);
            	return ['status' => true,'msg' => '导入成功'];
            }else{
                return error_code(10041);
            }
        }catch (Exception $e){
            return ['status' => false,'msg' => $e->getMessage()];
        }
        
    }

}
