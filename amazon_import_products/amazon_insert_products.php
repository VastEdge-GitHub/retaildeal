<?php
define('THUMBNAIL_IMAGE_MAX_WIDTH', 200);
define('THUMBNAIL_IMAGE_MAX_HEIGHT', 155);
error_reporting(E_ALL | E_STRICT);

global $base_url_magento;

$mageFilename = $base_url_magento.'app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');

///// CREATING LIKI IMAGE \\\\\
function imageCreateFromAny($filepath)
{
	global $base_url_magento;
    $type			= exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize() 
    $allowedTypes 	= array( 
						1,  // [] gif
						2,  // [] jpg
						3,  // [] png
						6   // [] bmp
					);
    if (!in_array($type, $allowedTypes)){ return false; }
    switch ($type)
	{
        case 1 : $im = imageCreateFromGif($filepath);
        break;
        case 2 : $im = imageCreateFromJpeg($filepath);
        break;
        case 3 : $im = imageCreateFromPng($filepath);
        break;
        case 6 : $im = imageCreateFromBmp($filepath);
        break;
    }
    return $im;
} 
function main($top_image, $bottom_image, $price, $imgtype)
{
	global $base_url_magento;
	$liki			= '_liki.'; /* Text to be appended with the name of the final image to be created eg Test_liki.png */
	$logo			= $base_url_magento.'amazon_import_products/liki_img/leaseit_keepit.png'; /* Path of liki logo */
	$mainimg_width 	= get_mainimg_size($top_image);
	$mainimg_height = get_mainimg_height($top_image);
	$txt_width		= get_likitxt_width($price);
	$topimg_name 	= pathinfo($top_image, PATHINFO_FILENAME);
	$topimg_ext 	= pathinfo($top_image, PATHINFO_EXTENSION);
	$resize			= 'false';
	$width_resize   = 'false';
	if($imgtype == 'small' && $mainimg_height <= '157')
	{
		trans_smain();
		$small_bg = $base_url_magento.'amazon_import_products/liki_img/cache/trans_smallbg.jpg';
		$thumbnail_image_path = $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
		if($mainimg_width > 200)
		{
			generate_thumbnail($top_image, $thumbnail_image_path);
			$width_resize = 'true';
			$top_image = $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
			merge_small($small_bg, $top_image, $liki, $width_resize);
		}
		else
		{
			merge_small($small_bg, $top_image, $liki, $width_resize);
		}
		$top_image	= $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
		$resize = 'true';
	}
	if($imgtype == 'small' && $mainimg_height > '157')
	{
		trans_smain();
		$thumbnail_image_path = $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
		generate_thumbnail($top_image, $thumbnail_image_path);
		list($width, $height, $type, $attr) = getimagesize($thumbnail_image_path);
		if($width > 200)
		{
			$top_image = $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
			generate_thumbnail($top_image, $thumbnail_image_path);
		}
		$small_bg = $base_url_magento.'amazon_import_products/liki_img/cache/trans_smallbg.jpg';
		$top_image	= $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
		merge_small($small_bg, $top_image, $liki, 'true');
		$resize = 'true';
	}
	if($imgtype == 'main')
	{
		if($mainimg_width < $txt_width)
		{
			create_trans_rec($top_image, $txt_width, $imgtype);
			$main_rectangle		= $base_url_magento.'amazon_import_products/liki_img/cache/imagetransrectangle.jpg';
			merge_trans_main($main_rectangle, $top_image, $liki);
			$top_image	= $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
			$resize = 'true';
		}
		create_mainimg_rec($top_image, $imgtype);/* Create main white rectangle */
		mergetop_wrec($top_image, $resize,$imgtype, $liki); /* Merge main image over the white rectangle */
		$top_image	= $base_url_magento.'amazon_import_products/liki_img/cache/'.$topimg_name.$liki.$topimg_ext;
	}
	create_rectangle($top_image, $imgtype); /* Create rectangle image on main image */
	merge($top_image, $bottom_image, $liki, $resize); /* Merge the rectangle image over main image */
	create_txt_image($top_image, $bottom_image, $price, $liki, $logo, $imgtype, $resize);/* Merge the text over the main image */
	merge_logo($top_image, $logo, $liki, $imgtype, $resize); /* Merge liki logo over main image */
}
function trans_smain()
{
	global $base_url_magento;
	$im		 	= imagecreatetruecolor(200, 200);
	$white 		= imagecolorallocate($im, 255, 255, 255);
	/* Draw a grey rectangle */
	
	imagefilledrectangle($im, 0, 0, 200, 200, $white);
	// Save the image
	imagejpeg($im, $base_url_magento.'amazon_import_products/liki_img/cache/trans_smallbg.jpg');
	imagedestroy($im);
}
function merge_small($small_bg, $top_image, $liki, $width_resize)
{
	global $base_url_magento;
	$capture 								= imageCreateFromAny($small_bg);
	$bottom									= imageCreateFromAny($top_image);
	$capt_img_name 							= pathinfo($top_image, PATHINFO_FILENAME);
	$ext 									= pathinfo($top_image, PATHINFO_EXTENSION);
	if($width_resize == 'true')
	{
		$img_name 							= $base_url_magento.'amazon_import_products/liki_img/cache/'.$capt_img_name.'.'.$ext;	
	}
	else
	{
		$img_name 							= $base_url_magento.'amazon_import_products/liki_img/cache/'.$capt_img_name.$liki.$ext;
	}
	imagesavealpha($capture,true);
	$logo_x 									= imagesx($bottom); 
	$logo_y 									= imagesy($bottom); 
	list($widthmr, $heightmr, $typemr, $attrmr) = getimagesize($small_bg);
	list($width, $height, $type, $attr) 		= getimagesize($top_image);	
	$height_left								= $heightmr - $height;
	$h_left										= 157 - $height;
	$y  										= 43 + $h_left/2;
	$width_left									= $widthmr - $width;
	$x											= $width_left/2;
	imagecopymerge($capture, $bottom,$x,$y, 0, 0,$logo_x, $logo_y,100); 
	if($type == 1){$capture 				= imagegif($capture,$img_name);}
	if($type == 2){$capture 				= imagejpeg($capture,$img_name,100);}
	if($type == 3){$capture 				= imagepng($capture,$img_name,0);}
	imagedestroy($bottom);
	imagedestroy($capture);
}
function generate_thumbnail($source_image_path, $thumbnail_image_path)
{
	global $base_url_magento;
	list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = THUMBNAIL_IMAGE_MAX_WIDTH / THUMBNAIL_IMAGE_MAX_HEIGHT;
    if ($source_image_width <= THUMBNAIL_IMAGE_MAX_WIDTH && $source_image_height <= THUMBNAIL_IMAGE_MAX_HEIGHT) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) (THUMBNAIL_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
        $thumbnail_image_height = THUMBNAIL_IMAGE_MAX_HEIGHT;
    } else {
        $thumbnail_image_width = THUMBNAIL_IMAGE_MAX_WIDTH;
        $thumbnail_image_height = (int) (THUMBNAIL_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
    imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
	imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
}
function mergetop_wrec($top_image, $resize, $imgtype, $liki)
{
	global $base_url_magento;
	$main_img 				= $base_url_magento.'amazon_import_products/liki_img/cache/imagemainrectangle.jpg';
	$capture 				= imageCreateFromAny($main_img);
	$bottom					= imageCreateFromAny($top_image);
	$top_img_name 			= pathinfo($top_image, PATHINFO_FILENAME);
	$ext 					= pathinfo($top_image, PATHINFO_EXTENSION);
	if($resize == 'true')
	{
		$img_name 			= $base_url_magento.'amazon_import_products/liki_img/cache/'.$top_img_name.'.'.$ext; // Name of the new image created after merge rectangle 
	}
	else
	{
		$img_name 			= $base_url_magento.'amazon_import_products/liki_img/cache/'.$top_img_name.$liki.$ext;	
		
	}
	list($width, $height, $type, $attr) 	= getimagesize($top_image);
	$logo_x 								= imagesx($bottom); 
	$logo_y 								= imagesy($bottom);
	if($imgtype == 'main')
	{
		imagecopymerge($capture, $bottom,0, 76, 0, 0, $logo_x, $logo_y, 100);
	}
	else
	{
		imagecopymerge($capture, $bottom,0, 43, 0, 0, $logo_x, $logo_y, 100);
	}
	if($type == 1){$capture 	= imagegif($capture,$img_name);}
	if($type == 2){$capture 	= imagejpeg($capture,$img_name,100);}
	if($type == 3){$capture 	= imagepng($capture,$img_name,0);}
	imagedestroy($bottom);
	imagedestroy($capture);
}
function create_mainimg_rec($top_image, $imgtype)
{
	global $base_url_magento;
	list($width, $height, $type, $attr) = getimagesize($top_image);
	if($imgtype == 'main')
	{
		$total_height = $height + 76;
	}
	else
	{
		$total_height = $height + 43;
	}
	$im		 	= imagecreatetruecolor($width, $total_height);
	$white 		= imagecolorallocate($im, 255, 255, 255);
	/* Draw a grey rectangle */
	
	imagefilledrectangle($im, 0, 0, $width, $total_height, $white);
	// Save the image
	imagejpeg($im, $base_url_magento.'amazon_import_products/liki_img/cache/imagemainrectangle.jpg');
	imagedestroy($im);
}
function create_trans_rec($top_image, $txt_width, $imgtype)
{
	global $base_url_magento;
	list($width, $height, $type, $attr) = getimagesize($top_image);
	if($imgtype == 'main')
	{
		$img_resized = ImageCreateTrueColor( $txt_width, $height ); /* To create trans rectangle if image size is large */
	}
	else
	{
		$img_resized = ImageCreateTrueColor( 200, $height ); /* To create trans rectangle if image size is small */
	}
    	
		$white = ImageColorAllocate( $img_resized, 255, 255, 255); /* White rectangle */
    	$transparent_new_index = ImageColorTransparent( $img_resized, $white); 
    	ImageFill( $img_resized, 0,0, $transparent_new_index ); /* don't forget to fill the new image with the transparent color */
		imagejpeg($img_resized, $base_url_magento.'amazon_import_products/liki_img/cache/imagetransrectangle.jpg');
		imagedestroy($img_resized);
}
function merge_trans_main($main_rectangle, $top_image, $liki)
{
	global $base_url_magento;
	$capture 				= imageCreateFromAny($main_rectangle);
	$bottom					= imageCreateFromAny($top_image);
	$capt_img_name 			= pathinfo($top_image, PATHINFO_FILENAME);
	$ext 					= pathinfo($top_image, PATHINFO_EXTENSION);
	$img_name 				= $base_url_magento.'amazon_import_products/liki_img/cache/'.$capt_img_name.$liki.$ext;	
	imagesavealpha($capture,true);
	$logo_x 				= imagesx($bottom); 
	$logo_y 				= imagesy($bottom); 
	list($widthmr, $heightmr, $typemr, $attrmr) = getimagesize($main_rectangle);
	list($width, $height, $type, $attr) 		= getimagesize($top_image);	
	$width_left				= $widthmr - $width;
	$x						= $width_left / 2;
	imagecopymerge($capture, $bottom,$x, 0, 0, 0,$logo_x, $logo_y,100); 
	if($type == 1){$capture = imagegif($capture,$img_name);}
	if($type == 2){$capture = imagejpeg($capture,$img_name,100);}
	if($type == 3){$capture = imagepng($capture,$img_name,0);}
	imagedestroy($bottom);
	imagedestroy($capture);
}
function get_mainimg_size($top_image)
{
	global $base_url_magento;
	list($widthm, $heightm, $typem, $attrm) = getimagesize($top_image);
	return $widthm; 
}
function get_mainimg_height($top_image)
{
	global $base_url_magento;
	list($widthm, $heightm, $typem, $attrm) = getimagesize($top_image);
	return $heightm; 
}

function get_likitxt_width($price)
{
	global $base_url_magento;
	$fontSize_price		= 32;
	$fontSize_mo		= 27;
	$font_price			= $base_url_magento.'amazon_import_products/liki_img/fonts/SourceSansPro-Bold.ttf';
	$month_txt			= '/mo';
	$bbox 				= imagettfbbox($fontSize_price, 0, $font_price, $price); 
	$priceWidth 		= abs($bbox[2] - $bbox[0]); /* Calculate the width of price text */
	$bbox1 				= imagettfbbox($fontSize_mo, 0, $font_price, $month_txt); 
	$monthWidth 		= abs($bbox1[2] - $bbox1[0]); /* Calculate the width of price text */
	$total_text_width	= 220 + $priceWidth + $monthWidth;
	return $total_text_width;
}
function create_rectangle($top_image, $imgtype)
{
	global $base_url_magento;
	list($width, $height, $type, $attr) = getimagesize($top_image);
	/* Create an image of width and height set */
	if($imgtype == 'main')
	{
		$im		 	= imagecreatetruecolor($width, 76);
	}
	else
	{
		$im		 	= imagecreatetruecolor($width, 43);
	}
	$bg_color 		= imagecolorallocate($im, 255, 255, 255);
	/* Draw a grey rectangle */
	if($imgtype == 'main')
	{
		imagefilledrectangle($im, 0, 0, $width, 76,  $bg_color);
	}
	else
	{
		imagefilledrectangle($im, 0, 0, $width, 43,  $bg_color);
	}
	// Save the image
	imagejpeg($im, $base_url_magento.'amazon_import_products/liki_img/cache/imagefilledrectangle.jpg');
	imagedestroy($im);
}
function create_txt_image($top_image, $bottom_image, $price, $liki, $logo, $imgtype, $resize)
{
	global $base_url_magento;
	$capt_img_name 			= pathinfo($top_image, PATHINFO_FILENAME);
	$ext 					= pathinfo($top_image, PATHINFO_EXTENSION);
	
	$img_name 			= $capt_img_name.'.'.$ext; /* Name of the new image created after merge text */
	$imgPath			= $base_url_magento.'amazon_import_products/liki_img/cache/'.$capt_img_name.'.'.$ext; /* Path of the new image created after merge text */
	
	
	list($width, $height, $type, $attr) = getimagesize($imgPath);
	list($widthl, $heightl, $typel, $attrl) = getimagesize($logo);
	if($type == 1){$image	= imagecreatefromgif($imgPath);}
	if($type == 2){$image	= imagecreatefromjpeg($imgPath);}
	if($type == 3){$image	= imagecreatefrompng($imgPath);}
	$image_x 				= imagesx($image); 
	$image_y 				= imagesy($image);
	$font 					= $base_url_magento.'amazon_import_products/liki_img/fonts/SourceSansPro-BoldItalic.ttf';
	if($imgtype == 'small')
	{
		$fontSize 							= 11;	
		$fontSize_price						= 15;
		$fontSize_slash						= 13;
	}
	else
	{
		$fontSize 							= 27;	
		$fontSize_price						= 32;
		$fontSize_slash						= 30;
	}
	$fontRotation 						= 0;
	$fontSize_dollar					= 10;
	$font_price							= $base_url_magento.'amazon_import_products/liki_img/fonts/Lato-Bold.ttf';
	$bbox 								= imagettfbbox($fontSize_price, 0, $font_price, $price); 
	$iWidth 							= abs($bbox[2] - $bbox[0]); /* Calculate the width of price text */
	if($imgtype == 'small')
	{
		$x_price 							= $width -35 - $iWidth;
		$x_slash							= $width -35;
		$x									= $width -29;
		$y 									= '30';
		$y_slash							= '29';
	}
	else
	{
		//$font_price							= $base_url_magento.'amazon_import_products/liki_img/fonts/Lato-Bold.ttf';
		$x_price 							= $width -77 - $iWidth;
		$x_slash							= $width -78;
		$x									= $width -65;
		$y 									= '52';
		$y_slash							= '51';
	}
	$slash								= '/';
	$mo									= 'mo';
	//$font_mo				= $base_url_magento.'amazon_import_products/liki_img/fonts/SourceSansPro-Regular.ttf';
	$penColour 				= imagecolorallocate($image, 0, 0, 0);
	$penColour_shadow 		= imagecolorallocate($image, 0, 0, 0);
	imagettftext($image, $fontSize_price, $fontRotation, $x_price, $y, $penColour, $font_price, $price);
	imagettftext($image, $fontSize_slash, $fontRotation, $x_slash, $y_slash, $penColour_shadow, $font_price, $slash);
	imagettftext($image, $fontSize, $fontRotation, $x, $y, $penColour_shadow, $font_price, $mo);
	imagesavealpha($image,true);
	if($type == 1){$image	= imagegif($image,$imgPath);}
	if($type == 2){$image	= imagejpeg($image,$imgPath,100);}
	if($type == 3){$image	= imagepng($image,$imgPath,0);}
}
function merge($top_file, $bottom_file, $liki, $resize)
{
	global $base_url_magento;	
	$capture				= imageCreateFromAny($top_file);
	$bottom					= imageCreateFromAny($bottom_file);
	$capt_img_name 			= pathinfo($top_file, PATHINFO_FILENAME);
	$ext 					= pathinfo($top_file, PATHINFO_EXTENSION);
	
	$img_name 			= $base_url_magento.'amazon_import_products/liki_img/cache/'.$capt_img_name.'.'.$ext; /* Name of the new image created after merge rectangle */

	imagesavealpha($capture,true);
	list($width, $height, $type, $attr) 	= getimagesize($top_file);
	$logo_x 				= imagesx($bottom); 
	$logo_y 								= imagesy($bottom); 	
	imagecopymerge($capture, $bottom,0, 0, 0, 0, $logo_x, $logo_y, 20); 
	if($type == 1){$capture = imagegif($capture,$img_name);}
	if($type == 2){$capture = imagejpeg($capture,$img_name,100);}
	if($type == 3){$capture = imagepng($capture,$img_name,0);}
	imagedestroy($bottom);
	imagedestroy($capture);
}
function merge_logo($top_image, $logo, $liki, $imgtype, $resize)
{
	global $base_url_magento;
	$capture_image_name 	= pathinfo($top_image, PATHINFO_FILENAME);
	$extension 				= pathinfo($top_image, PATHINFO_EXTENSION);	
	
	$capture_top_img	= $base_url_magento.'amazon_import_products/liki_img/cache/'.$capture_image_name.'.'.$extension; /* Path & Name of the new image created after merge logo */
	
	$capture_img			= imageCreateFromAny($capture_top_img);
	$bottom_img				= imageCreateFromAny($logo);
	list($width, $height, $type, $attr) = getimagesize($top_image);
	$logo_x 				= imagesx($bottom_img); 
	$logo_y 				= imagesy($bottom_img); 
	if($imgtype == 'small')
	{	
		$y								= '1';
		imagecopyresampled($capture_img, $bottom_img, 10, $y, 0, 0, 60, 40, $logo_x, $logo_y); /* Resize logo and merge over main image */
	}
	else
	{
		$y								= '1';
		imagecopyresampled($capture_img, $bottom_img,10, $y, 0, 0, 120, 73, $logo_x, $logo_y); 
	}
	
	if($type == 1){$capture = imagegif($capture_img,$capture_top_img);}
	if($type == 2){$capture = imagejpeg($capture_img,$capture_top_img,100);}
	if($type == 3){$capture = imagepng($capture_img,$capture_top_img,0);}
}
///// CREATING LIKI IMAGE ENDS \\\\\	

function insert_products($file_name,$cat_id)
{
	global $base_url_magento;
	global $prod_count;
	global $new_sku;
	
	$mainfile_fh		= fopen($base_url_magento.'var/import/main_file_import.csv', 'a');

	ini_set('display_errors', 1);												// Enabling errors visibilty
	$file = fopen($file_name, 'r');												// Reading CSV file
	$a=1;
	echo gmdate('Y-m-d H:i:s')."----> CSV processing started \n";																	// Variable = 1 to omit 1st line(header) of CSV file
	while(($content = fgets($file)) !== FALSE)									// Reading file line by line
	{
		if($a != '1')															// Leaving header line from csv file
		{
			$content_arr 					= explode('"|"',$content);			// Creating an array with values
			$content_arr[0] 				= substr($content_arr[0], 1);		// Omiting first character(")
			$content_arr[43] 				= substr(trim($content_arr[43]) ,0,-2);	// Omiting last 2 characters ("|)
			for($n=0;$n<=count($content_arr);$n++)								// Chekcing all values for BLANK
			{
				trim($content_arr[$n]);
				if($content_arr[$n] == 'BLANK'){$content_arr[$n] = '';}			// Replacing word BLANK with null
			}
			if($content_arr[41] == '1' || $content_arr[41] == '0')				// Only getting Shiping Detail products
			{
				$content_arr[22]			= explode(";",$content_arr[22]);		// PackageDimensions
				$content_weight_arr			= explode(":",$content_arr[22][2]);		// Getting weight from PackageDimensions
				$content_weight				= ($content_weight_arr[1])/100;
				
				$content_arr[15]			= explode(";",$content_arr[15]);		// ItemDimensions
				$content_itemweight_arr		= explode(":",$content_arr[15][2]);		// Getting weight from ItemDimensions
				$content_itemweight			= ($content_itemweight_arr[1])/100;
				
				$content_arr[17]			= explode(";",$content_arr[17]);		// ListPrice
				$content_listprice_arr		= explode(":",$content_arr[17][2]);		// Getting $ value from ListPrice
				$content_listprice			= str_replace("$","",$content_listprice_arr[1]);
				$content_listprice			= str_replace(",","",$content_listprice);
				
				$content_arr[33]			= explode(";",$content_arr[33]);		// LowestNewPrice
				$content_lowestnewprice_arr	= explode(":",$content_arr[33][2]);		// Getting $ value from LowestNewPrice
				$content_lowestnewprice		= str_replace("$","",$content_lowestnewprice_arr[1]);
				$content_lowestnewprice		= str_replace(",","",$content_lowestnewprice);
			
				$prod_sku 					= preg_replace('/\s+/', ' ', trim($content_arr[0]));						// ASIN
				if($content_listprice == ''){$prod_price = preg_replace('/\s+/', ' ', trim($content_lowestnewprice));} // If "ListPrice" else "LowestNewPrice"
				else{$prod_price = preg_replace('/\s+/', ' ', trim($content_listprice));}
				if($prod_price >= '50' && $prod_price <= '2500')
				{
					if(trim($content_itemweight) != '')
					{
						$prod_qty					= preg_replace('/\s+/', ' ', trim($content_arr[34]));
						if($prod_qty > 0)
						{
							$prod_weight 		= preg_replace('/\s+/', ' ', trim($content_itemweight));	//Weight from PackageDimensions
							$prod_categoryid	= preg_replace('/\s+/', ' ', trim($cat_id));				// Magento Category ID
							$prod_root_cat_id	= explode(",",trim($cat_id));								// Magento Category ID
							$prod_status		= '1';														// 1 = Enabled, 2 = Disabled
							$prod_name 			= preg_replace('/\s+/', ' ', trim($content_arr[30]));		// Title
							$prod_desc			= "<div>".preg_replace('/\s+/', ' ', trim($content_arr[43]))."</div>";	// Product Description from EditorialReviews
							if($content_arr[9] != ''){$prod_shortdesc = 'By '.preg_replace('/\s+/', ' ', trim($content_arr[9]));}			// Brand
							else if($content_arr[18] != ''){$prod_shortdesc = 'By '.preg_replace('/\s+/', ' ', trim($content_arr[18]));}	// Manufacturer
							else if($content_arr[29] != ''){$prod_shortdesc = 'By '.preg_replace('/\s+/', ' ', trim($content_arr[29]));}	// Studio
							else{$prod_shortdesc = '';}
							$prod_tax_class		= '0';							// 0 = None, 2 = Taxable Goods, 4 = Shipping
							$prod_visibility	= '4';							// 1=Not Visible Individually, 2=Catalog, 3=Search, 4=Catalog and Search
							$prod_typeid		= 'simple';
							if($prod_qty > 0){$prod_instock	= '1';}				// 0 = Out Of Stock, 1 = In Stock
							else{$prod_instock	= '0';}
							$prod_img_url		= preg_replace('/\s+/', ' ', trim($content_arr[7]));	// LargeImage
							$prod_thumbimg_url	= preg_replace('/\s+/', ' ', trim($content_arr[5]));	// SmallImage
							$prod_smlimg_url	= preg_replace('/\s+/', ' ', trim($content_arr[6]));	// MediumImage
							if(!getimagesize($prod_img_url)){return $prod_count;}
							if(!getimagesize($prod_thumbimg_url)){return $prod_count;}
							if(!getimagesize($prod_smlimg_url)){return $prod_count;}
							if(preg_replace('/\s+/', ' ', trim($content_arr[41])) == '1'){$prod_amazonprime = 'Yes';}		// IsEligibleForSuperSaverShipping
							else{$prod_amazonprime = 'No';}
							$amazon_asin				= preg_replace('/\s+/', ' ', trim($content_arr[0]));				// ASIN
							$amazon_local 				= 'us';
							$amazon_ean 				= preg_replace('/\s+/', ' ', trim($content_arr[12]));				// EAN
							$amazon_offer_condition		= 'new';
							$amazon_offer_price_type	= 'low';
							if($content_listprice == ''){$amazon_offer_price = $content_lowestnewprice;}
							else{$amazon_offer_price = $content_listprice;}				// If "ListPrice" else "LowestNewPrice"
							$amazon_offer_currency 		= 'USD';
							$amazon_offers_list_url 	= preg_replace('/\s+/', ' ', trim($content_arr[3]));				// AllOffersURL
							$amazon_product_url 		= preg_replace('/\s+/', ' ', trim($content_arr[1]));				// DetailPageURL
							$amazon_reviews_url 		= preg_replace('/\s+/', ' ', trim($content_arr[42]));				// CustomerReviews (Iframe URL)
							$amazon_sync 				= '0';							// 1 = True, 0 = False
							$amazon_use_categories 		= '0';							// 1 = True, 0 = False
							
							$meta_tag_robot				= '5';							// 3 = INDEX,FOLLOW, 4 = INDEX,NOFOLLOW, 5 = NOINDEX,FOLLOW, 6 = NOINDEX,NOFOLLOW
							//// Calcualting Standard Shipping \\\\
							$mag_catid_arr = array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570");
							switch ($prod_root_cat_id[0])
							{
							  case $mag_catid_arr[0]:
								$standard_shipping=$prod_weight*0.75;
								break;
							  case $mag_catid_arr[1]:
								$standard_shipping=$prod_weight*0.59;
								break;
							  case $mag_catid_arr[2]:
								$standard_shipping=$prod_weight*0.75;
								break;
							  case $mag_catid_arr[3]:
								$standard_shipping=$prod_weight*0.65;
								break;
							  case $mag_catid_arr[4]:
								$standard_shipping=$prod_weight*0.75;
								break;
							  case $mag_catid_arr[5]:
								$standard_shipping=$prod_weight*0.75;
								break;
							  case $mag_catid_arr[6]:
								$standard_shipping=$prod_weight*0.75;
								break;
							  case $mag_catid_arr[7]:
								$standard_shipping=$prod_weight*0.59;
								break;
							  case $mag_catid_arr[8]:
								$standard_shipping=$prod_weight*0.75;
								break;
							  case $mag_catid_arr[9]:
								$standard_shipping=$prod_weight*0.85;
								break;
							  case $mag_catid_arr[10]:
								$standard_shipping=0.99;
								break;
							}
							//// Standard Shipping Calculation Ends \\\\
							
							$prod_count++;
							$prod_price 		= $prod_price+$standard_shipping;							// Final Price = MSRP+Shipping Cost	
							$liki_disc			= '15';														// 15%
							$liki_price			= ($liki_disc/100)*$prod_price;								// LIKI price calculation
							$liki_price			= number_format($liki_price, 2, '.', ',');					// Value upto 2 decimals
							$liki_price_text	= "$".$liki_price;
							$prod_likidesc		= "LeaseItKeepIt Price : $".$liki_price." per month";							
							$new_sku			.= $prod_sku.",";			// Pushing all new SKUs in global variable to disable/delete old magento products
										
							echo gmdate('Y-m-d H:i:s')."----> New Product \n";
							echo gmdate('Y-m-d H:i:s')."----> Getting new images \n";
							
							// Getting name of images
							$prod_img_name 		= pathinfo($prod_img_url, PATHINFO_FILENAME);
							$prod_thumbimg_name	= pathinfo($prod_thumbimg_url, PATHINFO_FILENAME);
							$prod_smlimg_name 	= pathinfo($prod_smlimg_url, PATHINFO_FILENAME);
							// Getting extension of images
							$prod_img_ext 		= pathinfo($prod_img_url, PATHINFO_EXTENSION);
							$prod_thumbimg_ext	= pathinfo($prod_thumbimg_url, PATHINFO_EXTENSION);
							$prod_smlimg_ext 	= pathinfo($prod_smlimg_url, PATHINFO_EXTENSION);
							// Defining path where images are to be temporarily stored
							$img				= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_img_name.'.'.$prod_img_ext;
							$thumbimg			= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_thumbimg_name.'.'.$prod_thumbimg_ext;
							$smlimg				= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_smlimg_name.'.'.$prod_smlimg_ext;
							// Temporarily storing images
							file_put_contents($img, file_get_contents($prod_img_url));
							file_put_contents($thumbimg, file_get_contents($prod_thumbimg_url));
							file_put_contents($smlimg, file_get_contents($prod_smlimg_url));
							echo gmdate('Y-m-d H:i:s')."----> Converting new images \n";
					
							$main_image			= $img;									// Image to be used for LIKI text
							$small_image		= $smlimg;								// Image to be used for LIKI text
							$bottom_image		= $base_url_magento.'amazon_import_products/liki_img/cache/imagefilledrectangle.jpg';	// Path of the rectangle image created
							$liki_price_img		= "$".$liki_price;						// LIKI price to be watermarked on image
							main($main_image, $bottom_image, $liki_price_img, 'main');
							main($small_image, $bottom_image, $liki_price_img, 'small');
							$new_main_img		= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_img_name.'_liki.'.$prod_img_ext;	// Path of new LIKI image
							$new_smail_img		= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_smlimg_name.'_liki.'.$prod_smlimg_ext;	// Path of new LIKI image
							echo gmdate('Y-m-d H:i:s')."----> New images converted \n";
							//fwrite($main_file,"store,websites,attribute_set,type,category_ids,sku,name,image,small_image,thumbnail,amazon_prime,liki_desription,vastedge_meta_robots,price,weight,standard_shipping,status,visibility,tax_class_id,wp_amazon_sync,wp_amazon_use_categories,description,short_description,wp_amazon_local,wp_amazon_asin,wp_amazon_ean,wp_amazon_offer_condition,wp_amazon_offer_price_type,wp_amazon_offer_price,wp_amazon_offer_currency,wp_amazon_offers_list_url,wp_amazon_product_url,wp_amazon_reviews_url,liki_price,qty,min_qty,use_config_min_qty,is_qty_decimal,backorders,min_sale_qty,use_config_min_sale_qty,max_sale_qty,use_config_max_sale_qty,is_in_stock,use_config_manage_stock,product_name,store_id,product_type_id".PHP_EOL);
							fwrite($mainfile_fh,'"admin"|"base"|"Default"|"simple"|"'.$prod_categoryid.'"|"'.$prod_sku.'"|"'.utf8_encode($prod_name).'"|"'.$new_main_img.'"|"'.$new_smail_img.'"|"'.$new_smail_img.'"|"'.$prod_amazonprime.'"|"'.$prod_likidesc.'"|"'.$meta_tag_robot.'"|"'.$prod_price.'"|"'.$prod_weight.'"|"'.$standard_shipping.'"|"'.$prod_status.'"|"'.$prod_visibility.'"|"'.$prod_tax_class.'"|"'.$amazon_sync.'"|"'.$amazon_use_categories.'"|"'.utf8_encode($prod_desc).'"|"'.utf8_encode($prod_shortdesc).'"|"'.$amazon_local.'"|"'.$amazon_asin.'"|"'.$amazon_ean.'"|"'.$amazon_offer_condition.'"|"'.$amazon_offer_price_type.'"|"'.$amazon_offer_price.'"|"'.$amazon_offer_currency.'"|"'.$amazon_offers_list_url.'"|"'.$amazon_product_url.'"|"'.$amazon_reviews_url.'"|"'.$liki_price_text.'"|"'.$prod_qty.'"|"0"|"1"|"0"|"0"|"0"|"1"|"10000"|"1"|"'.$prod_instock.'"|"1"|"'.utf8_encode($prod_name).'"|"0"|"simple"'.PHP_EOL);
						}	// End of Product Qunatity check
					}		// End of Weight loop
				}			// End of Price Range loop
			}				// End of Shiping Detail products loop
		}					// End of leaving header line loop
		$a++;
	}						// End of file reading
	fclose($mainfile_fh);
	return $prod_count;
}

?>