<?php
/**
  +------------------------------------------------------------------------------
 * 九宫格图像操作类库
  +------------------------------------------------------------------------------
 * @author adophper
 * @time 2013-02-02 21:50
 * @e-mail hello@adophper.com
 * @version   $Id: AppDiary.class.php $
  +------------------------------------------------------------------------------
 */
class AppDiary{
/**
      +----------------------------------------------------------
     * 取得图像信息
     *
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $image 图像文件名
      +----------------------------------------------------------
     * @return mixed
      +----------------------------------------------------------
     */

    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        } else {
            return false;
        }
    }
    
	/**
      +----------------------------------------------------------
     * 为图片添加水印
      +----------------------------------------------------------
     * @static public
      +----------------------------------------------------------
     * @param string $source 原文件名
     * @param string $water  水印图片
     * @param int    $posX  水印X开始位置
     * @param int    $posY  水印Y开始位置
     * @param string $savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static public function water($source, $water, $posX = 0, $posY = 0, $savename=null, $alpha=80) {
        //检查文件是否存在
        if (!file_exists($source) || !file_exists($water))
            return false;

        //图片信息
        $sInfo = self::getImageInfo($source);
        $wInfo = self::getImageInfo($water);
        //var_dump($wInfo);

        //如果图片小于水印图片，不生成图片
        if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
            return false;

        //建立图像
        $sCreateFun = "imagecreatefrom" . $sInfo['type'];
        $sImage = $sCreateFun($source);
        $wCreateFun = "imagecreatefrom" . $wInfo['type'];
        $wImage = $wCreateFun($water);

        //设定图像的混色模式
        imagealphablending($wImage, true);

        //图像位置,默认为右下角右对齐
        if (!$posX){
        	$posX = $sInfo["width"] - $wInfo["width"];
        }
        if (!$posY){
        	$posY = $sInfo["height"] - $wInfo["height"];
        }

        //生成混合图像
        //imagecopy($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);
        imagecopy($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height']);
        
        //输出图像
        $ImageFun = 'Image' . $sInfo['type'];
        //如果没有给出保存文件名，默认为原图像名
        if (!$savename) {
            $savename = $source;
            @unlink($source);
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
        imagedestroy($wImage);
        unset($wInfo);
        unset($sInfo);
        unset($savename);
    }
    /**
     * 产生随机数
     */
    static public function randomkeys($length) {
	    $returnStr='';
	    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	    for($i = 0; $i < $length; $i ++) {
	        $returnStr .= $pattern {mt_rand ( 0, 61 )}; //生成php随机数
	    }
	    return $returnStr;
	}
	
	/* 返回一个字符的数组 */
	static public function chararray($str,$charset="utf-8"){
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		return $match;
	}

	/* 返回一个字符串在图片中所占的宽度 */
	static public function charwidth($fontsize,$fontangle,$ttfpath,$char){
		$box = @imagettfbbox($fontsize,$fontangle,$ttfpath,$char);
        $width = max($box[2], $box[4]) - min($box[0], $box[6]);
		return $width;
	}

	static public function autowrap($fontsize, $angle, $fontface, $string, $width, $charset = 'utf-8'){
		$_string = "";
		$_width = 0;
		$temp = self::chararray($string, $charset);
		//var_dump($temp);
		foreach ($temp[0] as $k=>$v){
			$w = self::charwidth($fontsize, $angle, $fontface, $v);
			$_width += intval($w);
			if (($_width > $width) && ($v !== "")){
				$_string .= "\n";
				$_width = 0;
			}
			$_string .= $v;
			$w = 0;
			//echo $v;
		}
		if (function_exists("mb_convert_encoding")){
			$_string = mb_convert_encoding($_string, "html-entities","utf-8");
		}
		
		return $_string;
	}
    
	/**
	* 可以统计中文字符串长度的函数
	* @param $str 要计算长度的字符串
	* @param $type 计算长度类型，0(默认)表示一个中文算一个字符，1表示一个中文算两个字符
	*
	*/
	static public function abslength($str){
		header('Content-type:text/html;charset=utf-8');
		if (empty($str)){
			return 0;
		}
		if (function_exists('mb_strlen')){
			return mb_strlen($str,'utf-8');
		}else{
			preg_match_all("/./u", $str, $ar);
			return count($ar[0]);
		}
	}
    
	/**
      +----------------------------------------------------------
     * 为图片添加文字
      +----------------------------------------------------------
     * @static public
      +----------------------------------------------------------
     * @param string $source 原文件名
     * @param string $text  水印文字
     * @param int    $posX  水印X开始位置
     * @param int    $posY  水印Y开始位置
     * @param string $font  水印文字字体
     * @param int    $fontsize  字体大小
     * @param string $savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static public function fontwater($text = '', $w = 90, $h = 90, $cate = null, $type = 'png', $pos = array(0,0), $font = 'msyh.ttf', $fontcolor = null, $savename = null, $alpha = 127) {

		header("Content-type: image/".$type);

        //建立图像
		$sImage = imagecreatetruecolor($w, $h);
		//只针对png
        imagesavealpha($sImage,true);
		$trans_colour = imagecolorallocatealpha($sImage, 0, 0, 0, $alpha); //透明度0-127：0 完全不透明，127：完全透明
		imagefill($sImage, 0, 0, $trans_colour); 
		//字符数
		//$text = iconv("gb2312","UTF-8",$text);
		if (!$text){
			$text = '无';
		}
        $strlen = self::abslength($text);

		if ($cate == 'cnt'){
			$fontsize = 12;
			$fontcolor = array(70,45,25);
		}elseif ($cate == 'mid_title'){
			$fontsize = 16;
			$fontcolor = array(54,40,18);
		}elseif ($cate == 'cnt_titles'){
			//
			if ($strlen < 2){
				$fontsize = 30;
			}
			elseif ($strlen < 3){
				$fontsize = 24;
			}elseif ($strlen >= 3 && $strlen < 10){
				$fontsize = 16;
			}else{
				$fontsize = 12;
			}
		}else{
			$fontsize = 12;
		}
        		
        $path = dirname(__FILE__);
        //默认字体位置
        $font = $path.'/font/'.$font;
        if (!file_exists($font)){
        	return false;
        }

        //图像位置,默认为右下角右对齐
        if (!$pos[0]){
        	$pos[0] = 1;
        }
        if (!$pos[1]){
        	$pos[1] = $fontsize;
        }
        //文字颜色
        if (!$fontcolor){
        	$fontcolor = imagecolorallocate($sImage, 77, 77, 77);
        }else{
        	$fontcolor = imagecolorallocate($sImage, $fontcolor[0], $fontcolor[1], $fontcolor[2]);
        }
        
        //生成混合图像
        //imagettftext($sImage, $fontsize, 0, $posX, $posY, -$fontcolor, $font, $text);
        //imagestring($sImage, $fontsize, $posX, $posY, $text, $fontcolor);
        $__string = self::autowrap($fontsize, 0, $font, $text, $w);
		if (function_exists("mb_substr")){
			$one = mb_substr($__string,0,1);
		}else{
			$one = substr($__string,0,1);
		}
        $box = imagettfbbox($fontsize,0,$font,$one);
		$jiang = 3;//字间距
		$strlength = intval($strlen)*($fontsize+$jiang);//字的长度
		$x = intval(($w-$strlength)/2);//X坐标位置
		$y = intval(($h-$fontsize)/2);//Y坐标位置
		if ($strlen < 3){

			imagettftext($sImage,$fontsize,0,$pos[0]+$x-10,$pos[1]+$y,$fontcolor,$font,$__string);

		}else{

			imagettftext($sImage,$fontsize,0,$pos[0],$pos[1]+($box[3]-$box[7]),$fontcolor,$font,$__string);

		}

        //输出图像
        if ($type == 'jpg'){
        	$ImageFun = 'Imagejpeg';
        }else{
        	$ImageFun = 'Image' . $type;
        }
        
        //如果没有给出保存文件名，默认为原图像名
        $savePath = $path.'/upfile/';
        if (!$savename) {
            $savename = $savePath.'tmp_'.time().self::randomkeys(5).'.'.$type;
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
        return $savename;
    }
    
    /**
     * 将数组内容粘贴到背景图上
     */
    static public function topaste($source,$array){
    	if (!file_exists($source) || empty($array))
    		return false;
    	//water($source, $water, $posX = 0, $posY = 0, $savename=null, $alpha=80)
    	$root = dirname(__FILE__).'/upfile/';
    	$filename = self::randomkeys(8).'-'.self::randomkeys(12);
    	$savename = $root.$filename.'.jpg';
    	//var_dump($array);
    	foreach ($array as $k=>$v){
    		$string = $array[$k][0];
    		if (@self::getImageInfo($string)){
    			self::water($source, $string, $array[$k][1], $array[$k][2], $savename);
    		}else{
    			$tmp_file = self::fontwater($string,$array[$k][3],$array[$k][4],$array[$k][5]);
    			self::water($source, $tmp_file, $array[$k][1], $array[$k][2], $savename);
    			@unlink($tmp_file);
    		}
    		//第一次生成后就在些图片上进行操作
    		$source = $savename;
    	}
    	return $filename.'.jpg';
    }
    
}