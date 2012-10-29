<?php
/*
 *图片缩放
 * @promo ImageUrl 图片的URL地址
 * @promo  MaxWidth  缩放设置的最大宽度
 * @promo MaxHeight  缩放设置的最大高度,默认值为"-1" 不限制
 * @promo sourcePath  原图保存的路径 一般为临时文件夹为主
 * @promo Path  图片保存的路径
 * Author:By Demon 2012.10.22
 */
class ImagesScale{
	public $ImageUrl;
	public $MaxWidth;
	public $MaxHeight;
	public $sourcePath;
	public $Path;
	function __construct($ImageUrl,$MaxWidth,$MaxHeight="-1",$Path,$sourcePath){
		$this->ImageUrl=$ImageUrl;
		$this->MaxWidth=$MaxWidth;
		$this->MaxHeight=$MaxHeight;
		$this->sourcePath=$sourcePath;
		$this->Path=$Path;
	}
	/*
	 *获取数据源图片的一些信息
	 *包括图片的宽度，高度，和图片的类型
	 */
	public function getImageSize(){
		$imageUrl=$this->grabImage($this->ImageUrl,$this->sourcePath);
		list($width,$height)=getimagesize($imageUrl["filename"]);
		$imageArr=array(
			"width"=>$width,
			"height"=>$height,
			"type"=>$imageUrl["type"],
			"url"=>$imageUrl["filename"]
		);
		return $imageArr;
	}
	/*
	 *降图片保存到服务器本地
	 *@promo $url 来源图片的路径
	 *@promo $filename 图片名，参数为图片保存的路径地址
	 */
	function grabImage($url, $filename = '') {
		if($url == '') {
			return false; //如果 $url 为空则返回 false;
		}
		$ext_name = strrchr($url, '.'); //获取图片的扩展名
		if($ext_name != '.gif' && $ext_name != '.jpg' && $ext_name != '.bmp' && $ext_name != '.png') {
			return false; //格式不在允许的范围
		}
		if($filename == '') {
			$filename = time().$ext_name; //以时间戳另起名
		}else{
			$filename=$filename."/".time().$ext_name;
		}
		//开始捕获
		ob_start();
		readfile($url);
		$img_data = ob_get_contents();
		ob_end_clean();
		$local_file = fopen($filename , 'a');
		fwrite($local_file, $img_data);
		fclose($local_file);
		return array(
			"filename"=>$filename,
			"type"=>$ext_name
		);
	}
	/*
	 *图片缩放
	 */
	public function setScale(){
		$imageSize=$this->getImageSize();
		if($imageSize==-1){
			return "上传的格式不正确";
		}
		$type=$imageSize["type"];
		$width=$imageSize["width"];
		$height=$imageSize["height"];
		//如果高度不限制
		if($this->MaxHeight==-1){
			//当宽度大于最大宽度
			if($width>$this->MaxWidth){
				$NewWidth=$this->MaxWidth;
				$NewHeight=$this->MaxWidth*$height/$width;
			}
		}else{
			//当高度也限制的时候
			//当宽高都小于最大值的时候
			if($width <= $this->MaxWidth AND $height <= $this->MaxHeight){
				$NewWidth=$width;
				$NewHeight= $height;
			}else if($width > $this->MaxWidth AND $height > $this->MaxHeight){
				//当宽高都大于最大值的时候
				if($width>=$height){
					$NewWidth=$this->MaxWidth;
					$NewHeight=$this->MaxWidth*$height/$width;
				}else{
					$NewHeight=$this->MaxHeight;
					$NewWidth=$width*$this->MaxHeight/$height;
				}
			}else if($width>=$this->MaxWidth){
//				只有宽度大于最大值的时候
				$NewWidth=$this->MaxWidth;
				$NewHeight=$this->MaxWidth*$height/$width;
			}else if($height>=$this->MaxHeight){
//				只有高度大于最大值的时候
				$NewHeight=$this->MaxHeight;
				$NewWidth=$width*$this->MaxHeight/$height;
			}
		}
		//化整
		$NewHeight=intval($NewHeight);
		$NewWidth=intval($NewWidth);
		if($NewWidth==0){
			$NewWidth=1; //判断图片宽度，如果小于0，则赋值为1
		}
		if($NewHeight==0){
			$NewHeight=1;//判断图片高度，如果小于0，则赋值为1
		}
		switch($type){
			case ".jpg":
				$image=imagecreatefromjpeg($imageSize["url"]);
				break;
			case ".gif":
				$image=imagecreatefromgif($imageSize["url"]);
				break;
			case ".png":
				$image=imagecreatefrompng($imageSize["url"]);
				break;
			case ".bmp":
				$image=imagecreatefromwbmp($imageSize["url"]);
				break;
		}

			//创建一副图像
		$image_p = imagecreatetruecolor($NewWidth, $NewHeight);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $NewWidth, $NewHeight, $width, $height);

		//输出
		$NewImageUrl=$this->Path."/".basename($imageSize["url"]);
		imagejpeg($image_p,$NewImageUrl,100);
		imagedestroy($image_p);
		return array(
			"imageUrl"=>$NewImageUrl
		);
	}
}
?>