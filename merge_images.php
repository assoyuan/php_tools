<?php

class MergeImages {
    /**
     * 合成图片
     *
     * @param string $prev 上面的图片
     * @param string $back 背景图片
     * @param array $options 位置参数 ['dst_x','dst_y','sf_width','sf_height']
     * @param string $savePath 合成之后保存的路径
     * @param string $saveName 合成之后保存的名称
     * @param bool $isZoom 是否把上面的图片进行缩放
     * @param bool $isTest 是否在浏览器中测试显示图片
     * @param string $mark 合成之后保存名称要添加的标识
     * @return bool
     */
    public function mergeImages($prev, $back, $options, $savePath, $saveName, $isZoom = true, $isTest = false, $mark = '_merge')
    {
        $dst_x = $options['dst_x'];
        $dst_y = $options['dst_y'];
        if ($isZoom === true) {
            $sf_width = $options['sf_width'];
            $sf_height = $options['sf_height'];
        } else {
            list($sf_width, $sf_height) = getimagesize($prev);
        }

        // 资源后缀名解析
        $prevImgSuffix = strtolower(pathinfo($prev, PATHINFO_EXTENSION));
        $backImgSuffix = strtolower(pathinfo($back, PATHINFO_EXTENSION));
        if ($prevImgSuffix == 'jpg') {
            $prevImgSuffix = 'jpeg';
        }
        if ($backImgSuffix == 'jpg') {
            $backImgSuffix = 'jpeg';
        }

        // 读取图片资源
        $imagecreatefromextensionprev = 'imagecreatefrom' . $prevImgSuffix;
        $imagecreatefromextensionback = 'imagecreatefrom' . $backImgSuffix;
        if ($isZoom === true) {
            // 把需要嵌入的图片进行缩放
            $src_im = $this->resizeImage($prev, $sf_width, $sf_height, $prevImgSuffix);
        } else {
            $src_im = @$imagecreatefromextensionprev($prev);
        }
        $dst_im = @$imagecreatefromextensionback($back);

        if (!is_resource($dst_im)) {
            return false;
        }

        // 定位图片
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, 0, 0, $sf_width, $sf_height);

        // 输出图片
        $imageextension = 'image' . $backImgSuffix;
        if ($isTest === true) {
            // 在浏览器中测试查看效果软件
            header("Content-type: image/jpeg");
            $imageextension($dst_im);
            exit();
        } else {
            $saveName = $savePath . $saveName . $mark . '.' . $backImgSuffix;
            $imageextension($dst_im, $saveName);
        }

        // 销毁无用图片，回收内存
        imagedestroy($dst_im);
        imagedestroy($src_im);

        return true;
    }

    /**
     * 按比例缩放图片
     *
     * @param string $filename 要缩放的图片的路径
     * @param string $max_width 缩放的宽
     * @param string $max_height 缩放的高
     * @param string $suffix 图片的后缀
     * @return false|resource
     */
    public function resizeImage($filename, $max_width, $max_height, $suffix)
    {
        list($orig_width, $orig_height) = getimagesize($filename);

        $image_p = imagecreatetruecolor($orig_width, $orig_height);
//        $white = imagecolorallocate($image_p, 255, 255, 255);
//        imagefill($image_p, 0, 0, $white);

        $imagecreatefromsuffix = 'imagecreatefrom' . $suffix;
        $image = $imagecreatefromsuffix($filename);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0,
            $max_width, $max_height, $orig_width, $orig_height);

        return $image_p;
    }
}
