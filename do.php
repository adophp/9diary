<?php
	/**
	 * 添加日志
	 * bgid 背景图片
	 * title 日志标题
	 * 表格标题与内容以'-|*dt*|-'分隔为1、2、3、4、6、7、8、9个表格中的信息
	 * 表格5相关：mid_title,emoticons
	 * 模板id: tempid,待开发
	 * open: 隐私
	 */
	
		//dump($_POST);
		$bgid = intval(htmlspecialchars($_REQUEST['bgid']));
		$title = htmlspecialchars($_POST['title']);
		$cnt = str_replace(PHP_EOL, '',$_POST['cnt']);
		//标题和内容不能为空
		if (!isset($title) || !isset($cnt)){
			echo '标题和内容不能为空!';
		}
		$imgid = intval($_POST['imgId']);
		$weatherId = intval($_POST['weatherId']);
		$mid_title = htmlspecialchars($_POST['mid_title']);
		$cnt_titles = $_POST['cnt_titles'];
		$tempId = intval($_POST['tempId']);
		$open = intval($_POST['open']);
		
		//写入图片
		require_once('AppDiary.class.php');
		$image = new AppDiary();
		$path = dirname(__FILE__);
		$backimage = $path.'/images/preview/'.$bgid.'.jpg';
		$weatherimg = $path.'/images/weather/'.$weatherId.'.gif';
		$emoticons_img = $path.'/images/emoticons/'.$imgid.'.gif';
		$contents = explode('-|*dt*|-',$cnt);
		$contents_title = explode('-|*dt*|-', $cnt_titles);
		$str = array(
			array($weatherimg,528,1,61,62),//天气
			array($contents_title[0],35,83,146,28,'cnt'),//表格1 标题
			array($contents[0],35,111,160,100,'cnt_titles'),//表格1 内容
			array($contents_title[1],222,83,146,28,'cnt'),//表格2 标题
			array($contents[1],222,111,160,100,'cnt_titles'),//表格2 内容
			array($contents_title[2],407,83,146,28,'cnt'),//表格3 标题
			array($contents[2],407,111,160,100,'cnt_titles'),//表格3 内容
			array($contents_title[3],35,244,146,28,'cnt'),//表格4 标题
			array($contents[3],35,273,160,100,'cnt_titles'),//表格4 内容
			array($mid_title,240,235,146,38,'mid_title'),//表格5 标题
			array($emoticons_img,261,283,80,80),//表格5 内容
			array($contents_title[4],407,244,146,28,'cnt'),//表格6 标题
			array($contents[4],407,273,160,100,'cnt_titles'),//表格6 内容
			array($contents_title[5],35,409,146,28,'cnt'),//表格7 标题
			array($contents[5],35,436,160,100,'cnt_titles'),//表格7 内容
			array($contents_title[6],221,409,146,28,'cnt'),//表格8 标题
			array($contents[6],221,436,160,100,'cnt_titles'),//表格8 内容
			array($contents_title[7],407,409,146,28,'cnt'),//表格9 标题
			array($contents[7],407,436,160,100,'cnt_titles'),//表格9 内容
		);
		//var_dump($str);
		$pic = $image->topaste($backimage,$str);
		$filename = $path.'/upfile/'.$pic;
		//加了一个time()主要为了防止相同的文件名
		$newname = $title.'_'.time().strrchr($pic, '.');
		$newfile = $path.'/upfile/'.$newname;
		//这里主要用于中文标题
		$newfile = iconv( 'UTF-8', 'GB18030', $newfile );
		if (file_exists($filename)){
			//更改文件名成功
			if (rename($filename,$newfile)){
				$pic = $newname;
			}
			echo json_encode(array('info'=>$pic,'status'=>1));
		}else{
			//生成文件失败
			echo json_encode(array('info'=>'Error'.$pic,'status'=>0));
		}
