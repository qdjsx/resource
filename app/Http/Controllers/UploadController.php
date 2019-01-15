<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public static $creativeArr = array(
        '640x*' =>'info_img',
        '640x400' => 'index_img',
        '319x180' => 'index_img',
        '640x640' => 'main_image',
        '70x70' => 'image_path',
        '212x250' => 'activity_image',
    );

    public static $creativeSizeArr = array(
        'offline_img' => '620x400',
        'logo_img' => '80x80',
        'coupon_index_img' => '620x400',
        'index_img' =>'590x220',
        'main_img' => '640x640',
        'banner_img'=>'750x280',
        'recommend_img'=>'118x118',
        'navigation_img' =>'70x70',
        'image_select' =>'360x225',
        'image_not_select' =>'360x225',
        'small_image_path' =>'150x150',
        'image_path' =>'212x250',
        'icon_select' =>'150x150',
        'icon_idle' =>'150x150',
        'icon' =>'150x150',
        'sign_pop_img' =>'150x150',
        'sign_pop_banner' =>'300x30',
    );
    //限制宽度的
    public static $richText = array(
        'rich_img' => '640',
        'info_img' => '640',
        'slot_img' => '630',

    );
    //不限制的
    public static $nolimit = array(
        //活动模板
        'common_img' =>'1',
        'patch_img' =>'1',
        'display_img' =>'1',
        'display_img_A' =>'1',
        'display_img_B' =>'1',
        'display_img_C' =>'1',
        'display_img_D' =>'1',
        //代金券主图，暂时不限制
//        'coupon_index_img' =>'1',
    );

    public function upload(Request $request)
    {
        $file = $_FILES['file'];
        $fileFlag = $request->get('flag','');
        if ($file['error'] == 0) {
            $filearr = explode('.',$file['name']);
            $newFileName = md5(time().rand(0,10000).$filearr[0]) .'.' .$filearr[1];
            $childPath = date('Y-m-d');
            $savePath = 'materials/'.$childPath;
            if($this->moveUploadFile($file['tmp_name'],$savePath.'/'.$newFileName)) {
                $imagePath = $savePath . '/' . $newFileName;
                list($width,$height) = getimagesize($imagePath);
                $index  = $width.'x'.$height;
                if (empty($fileFlag)) {
                    if (!isset(self::$creativeArr[$index])) {
                        $index = $width . 'x*';
                        if (!isset(self::$creativeArr[$index])) {
                            unlink($imagePath);
                            $this->data = array('code' => 0, 'msg' => '图片的尺寸不符合');
                        } else {
                            $this->data = array(
                                'code' => 200,
                                'msg' => '上传成功',
                                'path' => $savePath . '/' . $newFileName,
                                'index' => self::$creativeArr[$index]
                            );
                        }
                    } else {
                        $this->data = array(
                            'code' => 200,
                            'msg' => '上传成功',
                            'path' => $savePath . '/' . $newFileName,
                            'index' => self::$creativeArr[$index]
                        );
                    }
                }else{
                    //针对于info_img 640x*
                    if (isset(self::$richText[$fileFlag]) && $width == self::$richText[$fileFlag]) {
                        $this->data = array(
                            'code' => 200,
                            'msg' => '上传成功',
                            'path' => $savePath . '/' . $newFileName,
                            'index' => $fileFlag
                        );
                        $this->showJsonSuccessMsg();
                    }
                    //不限制宽高的
                    if (isset(self::$nolimit[$fileFlag])) {
                        $this->data = array(
                            'code' => 200,
                            'msg' => '上传成功',
                            'path' => $savePath . '/' . $newFileName,
                            'index' => $fileFlag
                        );
                        $this->showJsonSuccessMsg();
                    }
                    if (isset(self::$creativeSizeArr[$fileFlag]) && $index == self::$creativeSizeArr[$fileFlag]) {
                        $this->data = array(
                            'code' => 200,
                            'msg' => '上传成功',
                            'path' => $savePath . '/' . $newFileName,
                            'index' => $fileFlag
                        );
                    }else{
                        unlink($imagePath);
                        $this->data = array('code' => 0, 'msg' => '图片的尺寸不符合');
                    }
                }
                if (isset($this->data['index']) && $this->data['index'] == 'activity_image') {
                    $this->data['index'] = 'image_path';
                }

                $this->showJsonSuccessMsg();
            }
        }

        $this->showJsonFailMsg();
    }
}
