<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2020/1/7
 * Time: 14:20
 */

namespace app\image\controller;


use app\file\model\SysFile;
use app\image\common\UploadBase;
use app\image\helper\File;
use Jasmine\helper\Config;
use Jasmine\library\http\Request;

class Upload extends UploadBase
{

    function index(Request $request){
        try{
            $file = $this->file('file');

            if($file instanceof File){
                $rootPath = realpath(Config::get('PATH_ROOT','').'/../').'/';
                $subDir = 'data/uploads/';
                // 移动到框架应用根目录/uploads/ 目录下
                $f = $file->validate(['size'=>1024*1024*2,'ext'=>'jpg,jpeg,png,gif'])->move($rootPath.$subDir);
                if($f){
                    $data = [];
                    $data['name'] = $f->getInfo('name');
                    $data['path'] = $subDir.str_replace('\\','/',$f->getSaveName());
                    $data['size'] = $f->getSize();
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $data['modify_time'] = date('Y-m-d H:i:s');
                    $SysFileM = new SysFile();
                    $id = $SysFileM->insert($data);
                    // 成功上传后 获取上传信息
                    return $this->success('',['url'=>url('file/image/index',['id'=>$id])]);
                }

                // 上传失败获取错误信息
                return $this->error($f->getError());
            }else{
                return $this->error('no files be uploaded.');
            }

        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }
}