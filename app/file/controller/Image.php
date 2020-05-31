<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2020/1/9
 * Time: 21:29
 */

namespace app\file\controller;


use app\file\common\FileBase;
use app\file\util\File;
use app\file\model\SysFile;
use Jasmine\helper\Config;
use Jasmine\library\http\Request;

class Image extends FileBase
{
    /**
     * 读取
     * @param Request $request
     * @return array|false|string
     * itwri 2020/1/19 14:30
     */
    function index(Request $request)
    {
        try {

            $id = $request->get('id', 0);

            $SysFileM = new SysFile();
            $file = $SysFileM->find($id);

            if ($file == false) {
                return $this->error('File not exists.');
            }
            //
            $rootPath = realpath(Config::get('PATH_ROOT', '') . '/../') . '/';

            //
            $img = $rootPath . $file['path'];

            $info = getimagesize($img);
            //获取文件后缀
            $imgExt = image_type_to_extension($info[2], false);

            $fun = "imagecreatefrom{$imgExt}";
            //1.由文件或 URL 创建一个新图象。如:imagecreatefrompng ( string $filename )
            $imgInfo = $fun($img);

            $mime = image_type_to_mime_type(exif_imagetype($img)); //获取图片的 MIME 类型

            header('Content-Type:' . $mime);

            $quality = 100;
            if ($imgExt == 'png') $quality = 9;        //输出质量,JPEG格式(0-100),PNG格式(0-9)
            $getImgInfo = "image{$imgExt}";
            $getImgInfo($imgInfo, null, $quality);    //2.将图像输出到浏览器或文件。如: imagepng ( resource $image )
            imagedestroy($imgInfo);
            die();
        } catch (\Exception $exception) {
            die($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|false|string
     * itwri 2020/1/19 14:30
     */
    function upload(Request $request){
        try{
            $file = $this->getUploadService()->file('file');

            if(!($file instanceof File)){
                throw new \Exception('no files has been uploaded.');
            }

            $rootPath = realpath(Config::get('PATH_ROOT','').'/../').'/';
            $subDir = 'data/uploads/';
            // 移动到框架应用根目录/uploads/ 目录下
            $f = $file->validate(['size'=>1024*1024*2,'ext'=>'jpg,jpeg,png,gif'])->move($rootPath.$subDir);

            if(!$f){
                // 上传失败获取错误信息
                return $this->error($f->getError());
            }

            $data = [];
            $data['name'] = $f->getInfo('name');
            $data['path'] = $subDir.str_replace('\\','/',$f->getSaveName());
            $data['size'] = $f->getSize();
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['modify_time'] = date('Y-m-d H:i:s');
            $SysFileM = new SysFile();
            $id = $SysFileM->insert($data);

            if(!$id){
                throw new \Exception('add failed.');
            }

            // 成功上传后 获取上传信息
            return $this->success('success ',['url'=>url('file/image/index',['id'=>$id])]);

        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }
}