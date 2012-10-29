<?php
/**
 *图片补白合并
 * @promo $ImageUrl 图片的URL地址
 * @promo $Path   补白合并后的图片保存路径
 * @promo $ImageWidth  对图片限制的宽度
 * @promo $ImageHeight 对图片限制的高度
 */
class ImageCopy{
	public $ImageUrl;
	public $Path;
	public $ImageWidth;
	public $ImageHeight;
	function __construct($ImageUrl,$Path,$ImageWidth,$ImageHeight){
		$this->ImageUrl=$ImageUrl;
		$this->Path=$Path;
		$this->ImageWidth=$ImageWidth;
		$this->ImageHeight=$ImageHeight;
	}
	//获取图片信息，高宽的值
	public function getImageSize(){
		list($width,$height)=getimagesize($this->ImageUrl);
		$imageArr=array(
			"width"=>$width,
			"height"=>$height
		);
		return $imageArr;
	}
	public function SetImageCopy(){
		//创建一个新的图像
		$NewImage=imagecreate($this->ImageWidth,$this->ImageHeight);
		//设置图像的背景颜色，白色
		imagecolorallocate($NewImage, 255, 255, 255);
		// 读取图片
		$Image=imagecreatefromjpeg($this->ImageUrl);
		//获取图片信息
		$imageSize=$this->getImageSize();
		//计算高宽的值
		$x=($this->ImageWidth-$imageSize['width'])/2;
		$y=($this->ImageHeight-$imageSize['height'])/2;
		//进行图片的合并操作
		imagecopy($NewImage,$Image,$x,$y,0,0,$imageSize['width'],$imageSize['height']);
		$NewImageUrl=$this->Path."/".basename($this->ImageUrl,".jpg")."_".$this->ImageWidth."_".$this->ImageHeight.".jpg";
		// 输出新的图片
		imagejpeg($NewImage,$NewImageUrl,100);
		imagedestroy($NewImage);
		//最后返回图片保存路径
		return array(
			"imageUrl"=>$NewImageUrl
		);
	}
}
?>