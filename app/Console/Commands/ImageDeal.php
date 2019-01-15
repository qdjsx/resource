<?php

namespace App\Console\Commands;

use App\Models\ImageCompress;
use Illuminate\Console\Command;
use Image;

class ImageDeal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:image_zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'banner图片压缩服务';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $items = ImageCompress::where('is_deal',0)->get();
//        $compressPath = config('image.compress_path');
//        foreach ($items as $item) {
//            $image = $compressPath.$item->image_path;
//            $img = Image::make($image);
//            $width = $img->width();
//            $height = $img->height();
//            $widthZipArr = array(320,500);
//            foreach ($widthZipArr as $v) {
//                $heightZip = ceil($height * $v / $width);
//                $img->resize($v, $heightZip)->save(substr($image,0,strrpos($image,'.')) . '_'.$v.substr($image,strrpos($image,'.'), 100));
//            }
//            $item->is_deal = 1;
//            $item->save();
//        }
    }
}
