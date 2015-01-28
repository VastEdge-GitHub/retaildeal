<?php	
	error_reporting(E_ALL | E_STRICT);
	ob_implicit_flush(true);
	ini_set('default_charset', 'UTF-8');
	header('Content-Type: text/html; charset=utf-8');
	

	/////*****\\\\\
	//echo gmdate('Y-m-d H:i:s')."----> Magento script started \n";
	/////*****\\\\\
	
	$base_url_magento	= '/opt/bitnami/apps/magento/htdocs/';
    include("amazon_api_class.php");
    include("amazon_parse_xml.php");
	include($base_url_magento."app/Mage.php");
	include("config.php");
  

	$mageFilename		= $base_url_magento.'app/Mage.php';
	require_once $mageFilename;
	$app				= Mage::app('default');
	//Mage::register("isSecureArea", 1);

	$files_process	= glob($base_url_magento.'AmazonsinglAPI/csv_file/*'); // get all file names
	$file_count		= count($files_process);
	echo "filecount--->".$file_count . "\n" ;
	/////*****\\\\\
		//echo gmdate('Y-m-d H:i:s')."----> Reading Folder:  \n";
		/////*****\\\\\
	$files_process	= glob($base_url_magento.'AmazonsinglAPI/csv_file/*'); // get all file names
	
		foreach($files_process as $file) 
		       {
			   
		       $main_fh		= fopen($base_url_magento.'var/import/main_file_import.csv', 'w');
			   
				fwrite($main_fh,'^store^|^websites^|^attribute_set^|^type^|^category_ids^|^sku^|^name^|^image^|^small_image^|^thumbnail^|^amazon_prime^|^liki_desription^|^vastedge_meta_robots^|^price^|^weight^|^standard_shipping^|^status^|^visibility^|^tax_class_id^|^wp_amazon_sync^|^wp_amazon_use_categories^|^description^|^short_description^|^wp_amazon_local^|^wp_amazon_asin^|^wp_amazon_ean^|^wp_amazon_offer_condition^|^wp_amazon_offer_price_type^|^wp_amazon_offer_price^|^wp_amazon_offer_currency^|^wp_amazon_offers_list_url^|^wp_amazon_product_url^|^wp_amazon_reviews_url^|^liki_price^|^qty^|^min_qty^|^use_config_min_qty^|^is_qty_decimal^|^backorders^|^min_sale_qty^|^use_config_min_sale_qty^|^max_sale_qty^|^use_config_max_sale_qty^|^is_in_stock^|^use_config_manage_stock^|^product_name^|^store_id^|^product_type_id^|^sales_rank^|^manufacturer^'.PHP_EOL);
			
	         $content1 = fopen($file,'r');
			 echo "filename in loop--->". $file;
			 
			 $a=1;													            // Reading file line by line
			 while(($content = fgets($content1)) !== FALSE)						    // Reading file line by line
			 {
				 
				$content_arr 					= explode('"|"',$content);			// Creating an array with values
		    	if(1)															// Leaving header line from csv file
	      		{
					echo "in\n";
					$content_arr 					= explode('"|"',$content);			// Creating an array with values
			
					$content_arr[0] 				= substr($content_arr[0], 1);		// Omiting first character(")
					$content_arr[43] 				= substr(trim($content_arr[43]) ,0,-2);	// Omiting last 2 characters ("|)
					for($n=0;$n<=count($content_arr);$n++)								    // Chekcing all values for BLANK
					{
						trim($content_arr[$n]);
						if($content_arr[$n] == 'BLANK'){$content_arr[$n] = '';}			// Replacing word BLANK with null
					}
					if($content_arr[41] == '1' || $content_arr[41] == '0')				// Only getting Shiping Detail products
					{
						echo "in1\n";
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
						$prod_price = preg_replace('/\s+/', ' ', trim($content_listprice));				
						$prod_qty				= preg_replace('/\s+/', ' ', trim($content_arr[34]));
						$prod_name 			= preg_replace('/\s+/', ' ', trim($content_arr[30]));			// Title
					
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
						
						$meta_tag_robot				= '';								
						$standard_shipping = 0.99;
						$prod_price 		= $prod_price+$standard_shipping;							// Final Price = MSRP+Shipping Cost	
						$liki_disc			= '15';														// 15%
						$liki_price			= ($liki_disc/100)*$prod_price;								// LIKI price calculation
						$liki_price			= number_format($liki_price, 2, '.', ',');					// Value upto 2 decimals
						$liki_price			= "$".$liki_price;
						$prod_likidesc		= "LeaseItKeepIt Price : ".$liki_price." per month";							
								
											
						echo gmdate('Y-m-d H:i:s')."----> New Product \n";
						echo gmdate('Y-m-d H:i:s')."----> Getting new images \n";
												
						$search_spcl_chars	= array("PHP_EOL","|","^","\r\n","\r","\n");
						$replace_spcl_chars	= array(" ",","," "," "," "," ");
						
						// $mainfile_fh		= fopen($base_url_magento.'var/import/main_file_import_new.csv', 'a');
						echo gmdate('Y-m-d H:i:s')."----> Value inserted \n";
							  									
						fwrite($main_fh,'^admin^|^base^|^Default^|^simple^|^4^|^'.$prod_sku.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($prod_name,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($new_main_img,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($new_smail_img,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($new_smail_img,'"'),'"')).'^|^'.$prod_amazonprime.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($prod_likidesc,'"'),'"')).'^|^'.$meta_tag_robot.'^|^'.$prod_price.'^|^'.$prod_weight.'^|^'.$standard_shipping.'^|^'.$prod_status.'^|^'.$prod_visibility.'^|^'.$prod_tax_class.'^|^'.$amazon_sync.'^|^'.$amazon_use_categories.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($prod_desc,'"'),'"')).'^|^'.$prod_shortdesc.'^|^'.$amazon_local.'^|^'.$amazon_asin.'^|^'.$amazon_ean.'^|^'.$amazon_offer_condition.'^|^'.$amazon_offer_price_type.'^|^'.$amazon_offer_price.'^|^'.$amazon_offer_currency.'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($amazon_offers_list_url,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($amazon_product_url,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($amazon_reviews_url,'"'),'"')).'^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($liki_price,'"'),'"')).'^|^'.$prod_qty.'^|^0^|^1^|^0^|^0^|^0^|^1^|^10000^|^1^|^'.$prod_instock.'^|^1^|^'.str_replace($search_spcl_chars,$replace_spcl_chars,ltrim(rtrim($prod_name,'"'),'"')).'^|^0^|^simple^|^'.$prod_salesrank.'^|^'.$prod_brand.'^'.PHP_EOL);
								
						fclose($mainfile_fh);							
				  	}	
				}
           	}
			$a++;
			////*********\\\\\\		
			/////*****\\\\\
			echo gmdate('Y-m-d H:i:s')."----> Magento products add Completed \n";
			/////*****\\\\\
			//unlink($file);
		    ////*****\\\\\
			echo gmdate('Y-m-d H:i:s')."----> File Count: ".$file_count." \n";
			/////*****\\\\\
		} 
?>