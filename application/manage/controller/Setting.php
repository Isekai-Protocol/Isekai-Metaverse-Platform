<?php
namespace app\Manage\controller;
use app\common\controller\Manage;
use app\common\model\Setting as SettingModel;
use app\common\model\Videos as VideosModel;
use Request;
use think\Console;
use think\facade\Cache;


class Setting extends Manage
{

    public function index()
    {
        $settingModel = new SettingModel();
        if(Request::isAjax()){
            foreach(input('param.') as $k => $v){
                if($k == '__Jshop_Token__') break;
                if($k == 'validate_form') break;
                if($k == 'editorValue') break;

                $result = $settingModel->setValue($k, $v);
                //如果出错，就返回，如果是没有此参数，就默认跳过
                if(!$result['status'] && $result['data'] != 10008){
                    return $result;
                }
            }
            $result = array(
                'status' => true,
                'data' => [],
                'msg' => '保存成功'
            );
            //配置保存后，清理缓存
            Cache::clear();//TODO 如果开启其他缓存，记得这里要配置缓存配置信息
            Console::call('clear', ['--cache', '--dir']);//清除缓存文件
            Console::call('clear', ['--path', ROOT_PATH . '\\runtime\\temp\\']); //清除模板缓存
            return $result;
        }else{
            $data = $settingModel->getAll();
            $this->assign('data', $data);
            return $this->fetch();
        }
    }


    /*
    * 弹出层视频列表
    * */
    public function videos(){
        $videosModel = new VideosModel();

        if (\think\facade\Request::isAjax()) {
            $filter = input('request.');
            return $videosModel->tableData($filter);
        }
        return $this->fetch();
    }


    /*
     * 视频添加
     * */
    public function videoAdd(){
        $this->view->engine->layout(false);

        if (Request::isPost()) {
            $videosModel = new VideosModel();
            return $videosModel->addData(input('param.'));
        }
        return $this->fetch('setting/video_add');
    }


    /*
     * 视频删除
     * */
    public function videoDel(){
        $videosModel = new VideosModel();
        $result  = [
            'status' => true,
            'msg'    => '删除成功'
        ];
        $id = input('param.id/d');
        $url = input('param.path');
        $res = $videosModel->destroy($id);
        if (!unlink($url) && !$res) { // 删除视频文件
            return error_code(10023);
        }
        return $result;
    }

    /**
     * 前端菜单列表
     * @return mixed
     */
    public function frontMenu()
    {
        $menuModel = new \app\common\model\FrontMenu();
        if (Request::isAjax()) {
            $list = $menuModel->getMenuTree();
            foreach ($list as $key => $value) {
                $list[$key]['menu_img'] = _sImage($value['menu_img']);
                $list[$key]['menu_path'] = '/';
                foreach (getMenuList() as $k => $v) {
                    if($v['value'] == $value['path']){
                        $list[$key]['menu_path'] = $v['name'];
                    }
                }
                
            }
            return ['code' => 0,'msg' => '','data' => $list];
        }
        return $this->fetch('pages/menu');
    }

    // 添加前端菜单
    public function addMenu()
    {
        // 追加菜单地址
        $this->view->engine->layout(false);
        $menuModel = new \app\common\model\FrontMenu();
        if (Request::isPost()) {
            $menu_data = [
                'menu_name' => input('menu_name'),
                'pid' => input('pid'),
                'sort' => input('sort'),
                'menu_img' => input('menu_img'),
                'menu_eng_name' => input('menu_eng_name'),
                'status' => input('status'),
                'path' => input('path'),
            ];

            $res = $menuModel->insert($menu_data);
        
            return $res ? ['status' => true,'msg' => '保存成功'] : error_code(10004);
        }

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('pages/menu_add',['cate' =>  $menuModel->where('pid',0)->select(),'path' => getMenuList()])
        ];
    }

    /**
     * 编辑前端菜单
     * @return mixed
     */
    public function editMenu()
    {
        $this->view->engine->layout(false);
        $menuModel = new \app\common\model\FrontMenu();
        if (Request::isPost()) {
            $menu_data = [
                'menu_name' => input('menu_name'),
                'pid' => input('pid'),
                'sort' => input('sort'),
                'menu_img' => input('menu_img'),
                'menu_eng_name' => input('menu_eng_name'),
                'status' => input('status'),
                'path' => input('path'),
            ];

            $res = $menuModel->where('id',input('param.id/d'))->update($menu_data);
 
            return $res ? ['status' => true,'msg' => '保存成功'] : error_code(10004);
        }

        return [
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $this->fetch('pages/menu_edit',['info'=> $menuModel->where('id', input('param.id/d'))->find(),'cate' =>  $menuModel->where('pid',0)->select(),'path' => getMenuList()])
        ];
    }

    // 关于我们配置
    public function aboutUs()
    {
        // $this->view->engine->layout(false);
        $setModel = new \app\common\model\Setting();
        $about_info = $setModel->where('skey','aboutus')->value('value');
        if (Request::isPost()) {
            if(empty($about_info)){
                $res = $setModel->where('skey','aboutus')->insert(['skey' => 'aboutus','value' => input('content')]);
            }else{
                $res = $setModel->where('skey','aboutus')->update(['value' => $_POST['content']]);
            }

            return $res ? ['status' => true,'msg' => '保存成功'] : error_code(10004);
        }

        return $this->fetch('pages/aboutus',['info' => $about_info]);
    }

}