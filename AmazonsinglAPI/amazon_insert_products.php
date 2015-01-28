<?php
define('THUMBNAIL_IMAGE_MAX_WIDTH', 200);
define('THUMBNAIL_IMAGE_MAX_HEIGHT', 155);
error_reporting(E_ALL | E_STRICT);
session_start();

global $base_url_magento;

$mageFilename = '../app/Mage.php';
require_once $mageFilename;
$app = Mage::app('default');


function insert_products($file_name,$cat_id)
{
	global $base_url_magento;
	global $prod_count;
	ini_set('display_errors', 1);												// Enabling errors visibilty
	$file = fopen($file_name, 'r');												// Reading CSV file
	 $a=1;																		// Variable = 1 to omit 1st line(header) of CSV file
	echo gmdate('Y-m-d H:i:s')."----> CSV processing started \n";
	while(($content = fgets($file)) !== FALSE)									// Reading file line by line
	{
		if($a != '1')															// Leaving header line from csv file
		{
			$stopword_check 				= 0;
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
				if(trim($content_itemweight) != '' || trim($content_itemweight) <= 0)
					{
						$prod_qty				= preg_replace('/\s+/', ' ', trim($content_arr[34]));
						if($prod_qty > 0)
						{
							$prod_name 			= preg_replace('/\s+/', ' ', trim($content_arr[30]));			// Title
					//	if($stopword_check != 1)															// If Stopword does not exists
					//		{
								$prod_weight 		= preg_replace('/\s+/', ' ', trim($content_itemweight));	// Weight from PackageDimensions
								$prod_status		= '1';														// 1 = Enabled, 2 = Disabled
								$prod_desc			= preg_replace('/\s+/', ' ', trim($content_arr[43]));		// Product Description from EditorialReviews
								if($content_arr[9] != ''){
									$prod_shortdesc = 'By '.preg_replace('/\s+/', ' ', trim($content_arr[9]));						// Brand
									$prod_brand		= ucfirst(strtolower(preg_replace('/\s+/', ' ', trim($content_arr[9]))));		// Brand
								}
								else if($content_arr[18] != ''){
									$prod_shortdesc = 'By '.preg_replace('/\s+/', ' ', trim($content_arr[18]));						// Manufacturer
									$prod_brand		= ucfirst(strtolower(preg_replace('/\s+/', ' ', trim($content_arr[18]))));		// Manufacturer
								}
								else if($content_arr[29] != ''){
									$prod_shortdesc = 'By '.preg_replace('/\s+/', ' ', trim($content_arr[29]));						// Studio
									$prod_brand		= ucfirst(strtolower(preg_replace('/\s+/', ' ', trim($content_arr[29]))));		// Studio
								}
								else{
									$prod_shortdesc = '';
									$prod_brand		= '';
								}
								$prod_tax_class		= '0';							// 0 = None, 2 = Taxable Goods, 4 = Shipping
								$prod_visibility	= '4';							// 1=Not Visible Individually, 2=Catalog, 3=Search, 4=Catalog and Search
								$prod_typeid		= 'simple';
								if($prod_qty > 0){$prod_instock	= '1';}				// 0 = Out Of Stock, 1 = In Stock
								else{$prod_instock	= '0';}
								/*$prod_img_url		= preg_replace('/\s+/', ' ', trim($content_arr[7]));	// LargeImage
								$prod_thumbimg_url	= preg_replace('/\s+/', ' ', trim($content_arr[5]));	// SmallImage
								$prod_smlimg_url	= preg_replace('/\s+/', ' ', trim($content_arr[6]));	// MediumImage
								$prod_salesrank		= trim($content_arr[4]);								// SalesRank
								$prod_color_arr		= explode(",",ucfirst(strtolower(trim($content_arr[11]))));			// Color
								$prod_color_arr1	= explode("/",$prod_color_arr[0]);
								$prod_color			= $prod_color_arr1[0];
								//if(trim($prod_brand) != ''){$prod_brand_id	= addAttributeOption('manufacturer', $prod_brand);}
								//else{$prod_brand_id = '';}
								//if(trim($prod_color) != ''){$prod_color_id 	= addAttributeOption('color', $prod_color);}
								//else{$prod_color_id = '';}
								if(!getimagesize($prod_img_url)){return $prod_count;}
								if(!getimagesize($prod_thumbimg_url)){return $prod_count;}
								if(!getimagesize($prod_smlimg_url)){return $prod_count;}*/
								//if(preg_replace('/\s+/', ' ', trim($content_arr[41])) == '1'){$prod_amazonprime = 'Amazon Prime';}//																									IsEligibleForSuperSaverShipping
								//else{$prod_amazonprime = 'Not Amazon Prime';}
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
								
								$meta_tag_robot				= '';							// 3 = INDEX,FOLLOW, 4 = INDEX,NOFOLLOW, 5 = NOINDEX,FOLLOW, 6 = NOINDEX,NOFOLLOW
								//// Calcualting Standard Shipping \\\\
						/*		$mag_catid_arr = array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570");
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
								*/	//break;
								}
								//// Standard Shipping Calculation Ends \\\\
								$standard_shipping = 0.99;
								$prod_price 		= $prod_price+$standard_shipping;							// Final Price = MSRP+Shipping Cost	
								$liki_disc			= '15';														// 15%
								$liki_price			= ($liki_disc/100)*$prod_price;								// LIKI price calculation
								$liki_price			= number_format($liki_price, 2, '.', ',');					// Value upto 2 decimals
								$liki_price			= "$".$liki_price;
								$prod_likidesc		= "LeaseItKeepIt Price : ".$liki_price." per month";							
								$new_sku			.= $prod_sku.",";			// Pushing all new SKUs in global variable to disable/delete old magento products
											
								echo gmdate('Y-m-d H:i:s')."----> New Product \n";
								echo gmdate('Y-m-d H:i:s')."----> Getting new images \n";
								
							/*	// Getting name of images
								$prod_img_name 		= pathinfo($prod_img_url, PATHINFO_FILENAME);
								$prod_thumbimg_name	= pathinfo($prod_thumbimg_url, PATHINFO_FILENAME);
								$prod_smlimg_name 	= pathinfo($prod_smlimg_url, PATHINFO_FILENAME);
								// Getting extension of images
								$prod_img_ext 		= pathinfo($prod_img_url, PATHINFO_EXTENSION);
								$prod_thumbimg_ext	= pathinfo($prod_thumbimg_url, PATHINFO_EXTENSION);
								$prod_smlimg_ext 	= pathinfo($prod_smlimg_url, PATHINFO_EXTENSION);
								// Defining path where images are to be temporarily stored
								$img				= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_img_name.'_'.$prod_sku.'.'.$prod_img_ext;
								$thumbimg			= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_thumbimg_name.'_'.$prod_sku.'.'.$prod_thumbimg_ext;
								$smlimg				= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_smlimg_name.'_'.$prod_sku.'.'.$prod_smlimg_ext;
								// Temporarily storing images
								file_put_contents($img, file_get_contents($prod_img_url));
								file_put_contents($thumbimg, file_get_contents($prod_thumbimg_url));
								file_put_contents($smlimg, file_get_contents($prod_smlimg_url));
								echo gmdate('Y-m-d H:i:s')."----> Converting new images \n";
						
								$main_image			= $img;									// Image to be used for LIKI text
								$small_image		= $smlimg;								// Image to be used for LIKI text
								$bottom_image		= $base_url_magento.'amazon_import_products/liki_img/cache/imagefilledrectangle.jpg';	// Path of the rectangle image created
								main($main_image, $bottom_image, $liki_price, 'main');
								main($small_image, $bottom_image, $liki_price, 'small');
								$new_main_img		= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_img_name.'_'.$prod_sku.'_liki.'.$prod_img_ext;	// Path of new LIKI image
								$new_smail_img		= $base_url_magento.'amazon_import_products/liki_img/cache/'.$prod_smlimg_name.'_'.$prod_sku.'_liki.'.$prod_smlimg_ext;	// Path of new LIKI image
								echo gmdate('Y-m-d H:i:s')."----> New images converted \n";
								
								echo gmdate('Y-m-d H:i:s')."----> Checking SKU in custom URL rewrite table \n";*/
								$prod_count++;
								
								$search_spcl_chars	= array("PHP_EOL","|","^","\r\n","\r","\n");
								$replace_spcl_chars	= array(" ",","," "," "," "," ");
								
								$mainfile_fh		= fopen('../var/import/main_file_import_new.csv', 'a');	
								
								fwrite($mainfile_fh,'^admin^|^base^|^Default^|^simple^|^'.$prod_categoryid.'^|^'.$prod_sku.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,utf8_encode(ltrim(rtrim($prod_name,'"'),'"'))).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($new_main_img,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($new_smail_img,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($new_smail_img,'"'),'"')).'^|^'.$prod_amazonprime.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($prod_likidesc,'"'),'"')).'^|^'.$meta_tag_robot.'^|^'.$prod_price.'^|^'.$prod_weight.'^|^'.$standard_shipping.'^|^'.$prod_status.'^|^'.$prod_visibility.'^|^'.$prod_tax_class.'^|^'.$amazon_sync.'^|^'.$amazon_use_categories.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,utf8_encode(ltrim(rtrim($prod_desc,'"'),'"'))).'^|^'.utf8_encode($prod_shortdesc).'^|^'.$amazon_local.'^|^'.$amazon_asin.'^|^'.$amazon_ean.'^|^'.$amazon_offer_condition.'^|^'.$amazon_offer_price_type.'^|^'.$amazon_offer_price.'^|^'.$amazon_offer_currency.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($amazon_offers_list_url,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($amazon_product_url,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($amazon_reviews_url,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($liki_price,'"'),'"')).'^|^'.$prod_qty.'^|^0^|^1^|^0^|^0^|^0^|^1^|^10000^|^1^|^'.$prod_instock.'^|^1^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,utf8_encode(ltrim(rtrim($prod_name,'"'),'"'))).'^|^0^|^simple^|^'.$prod_salesrank.'^|^'.$prod_brand.'^'.PHP_EOL);
								fclose($mainfile_fh);
						//	}	// End of Stopword list check
						}		// End of Product Qunatity check
					}			// End of Weight loop
			}					// End of Shiping Detail products loop
		}						// End of leaving header line loop
		$a++;
	}							// End of file reading
	return $prod_count;
//}

?>