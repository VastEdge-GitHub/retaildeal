<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);
	
	error_reporting(E_ALL | E_STRICT);
	ob_implicit_flush(true);

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magento script started \n";
	/////*****\\\\\
	
	$base_url_magento	= '/opt/bitnami/apps/magento/htdocs/';
    include($base_url_magento."amazon_import_products/amazon_api_class.php");
    include($base_url_magento."amazon_import_products/amazon_parse_xml.php");
	include($base_url_magento."app/Mage.php");
	include($base_url_magento."amazon_import_products/config.php");
   	include($base_url_magento."amazon_import_products/amazon_insert_products.php");

	global $base_url_magento;
	global $prod_count;
	global $new_sku;
	global $blocklist_arr;
	global $new_mainfile;
	$new_sku			= '';

	$mageFilename		= $base_url_magento.'app/Mage.php';
	require_once $mageFilename;
	$app				= Mage::app('default');

	$pCollection 		= Mage::getSingleton('index/indexer')->getProcessesCollection(); 
	foreach ($pCollection as $process) {
	  $process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
	}
	Mage::register("isSecureArea", 1);
	
	$q_create_table	= "CREATE TABLE IF NOT EXISTS `custom_rewrite_check` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Auto increment id',`url_rewrite_id` int(11) NOT NULL COMMENT 'Value from table: core_url_rewrite',`prod_sku` varchar(64) DEFAULT NULL COMMENT 'SKU of product',`deleted_on` date DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
	$r_create_table	= mysql_query($q_create_table);

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Getting all old products started \n";
	/////*****\\\\\

	// Getting SKU of all products already stored in magento
	// If these products are not found in current amazon feed then these products will be disabled
	$products_sku	= Mage::getModel("catalog/product")->getCollection();	// Getting all products from magento
	$all_sku		= '';
	foreach ($products_sku as $product_sku)									// For each product
	{
		$sku		= $product_sku->getSku();								// Getting SKU of each product
		$all_sku	.= $sku.",";											// Pushing all SKUs in variable
	}
	rtrim($all_sku,",");
	$fh				= fopen($base_url_magento.'amazon_import_products/prod_sku/old_products_sku.txt', 'w');
	fwrite($fh,$all_sku);
	fclose($fh);
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Getting all old products completed \n";
	/////*****\\\\\
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Creating stopword list \n";
	/////*****\\\\\
	$file_blocklist	= fopen($base_url_magento.'amazon_import_products/stopword_list.txt', 'r');
	$blocklist_arr	= array();
	while(($content_blocklist = fgets($file_blocklist)) !== FALSE)
	{
		$content_blocklist_arr	= explode(',',$content_blocklist);
		for($block=0;$block<count($content_blocklist_arr);$block++)
		{
			array_push($blocklist_arr,$content_blocklist_arr[$block]);
		}
	}
	fclose($file_blocklist);
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Stopword list created \n";
	/////*****\\\\\

	$files_process	= glob($base_url_magento.'amazon_import_products/csv_file/*'); // get all file names
	$flag			= 'true';
	$filecount_flag	= 'true';
	$file_main		= 0;
	$file_sub		= 0;
	$prod_count		= 0;
	$new_mainfile	= 1;
	$file_count		= count($files_process);
	while($filecount_flag == 'true' || $flag == 'true')
	{
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Reading Folder:  \n";
		/////*****\\\\\
		$file_count	= 0;
		$files_process	= glob($base_url_magento.'amazon_import_products/csv_file/*'); // get all file names
		$filecount_flag	= 'false';
		foreach($files_process as $file)
		{
			 // iterate files
			if(is_file($file))
			{
				if($new_mainfile == 1)
				{
					$main_fh		= fopen($base_url_magento.'var/import/main_file_import_new.csv', 'w');
					fwrite($main_fh,'^store^|^websites^|^attribute_set^|^type^|^category_ids^|^sku^|^name^|^image^|^small_image^|^thumbnail^|^amazon_prime^|^liki_desription^|^vastedge_meta_robots^|^price^|^weight^|^standard_shipping^|^status^|^visibility^|^tax_class_id^|^wp_amazon_sync^|^wp_amazon_use_categories^|^description^|^short_description^|^wp_amazon_local^|^wp_amazon_asin^|^wp_amazon_ean^|^wp_amazon_offer_condition^|^wp_amazon_offer_price_type^|^wp_amazon_offer_price^|^wp_amazon_offer_currency^|^wp_amazon_offers_list_url^|^wp_amazon_product_url^|^wp_amazon_reviews_url^|^liki_price^|^qty^|^min_qty^|^use_config_min_qty^|^is_qty_decimal^|^backorders^|^min_sale_qty^|^use_config_min_sale_qty^|^max_sale_qty^|^use_config_max_sale_qty^|^is_in_stock^|^use_config_manage_stock^|^product_name^|^store_id^|^product_type_id^|^sales_rank^|^manufacturer^'.PHP_EOL);
					fclose($main_fh);
				}
				$new_mainfile = 0;
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$file_count++;
				if($file == $base_url_magento.'amazon_import_products/csv_file/main_categories.txt')
				{
					$file_main = 1;
					$fh = fopen($file,'r');
					while ($line = fgets($fh))
					{
						if($line == 'True')
						{
							$flag_main = 'true';
						}
						else
						{
							$flag_main = 'false';
						}
					}
					fclose($fh);
				}
				if($file == $base_url_magento.'amazon_import_products/csv_file/sub_categories.txt')
				{
					$file_sub = 1;
					$fh = fopen($file,'r');
					while ($line = fgets($fh))
					{
						if($line == 'True')
						{
							$flag_sub = 'true';
						}
						else
						{
							$flag_sub = 'false';
						}
					}
					fclose($fh);
				}
				if($ext == 'csv')
				{
					$filecount_flag	= 'true';
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Reading File: ".$file." \n";
					/////*****\\\\\
					$cat_array	= explode("@",$file);
					$cat_id		= $cat_array[1];
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Magento products add started \n";
					/////*****\\\\\
					$insert_products	= insert_products($file,$cat_id);
					$prod_count			= $insert_products;
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Magento products add Completed \n";
					/////*****\\\\\
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Total products: ".$prod_count." \n";
					/////*****\\\\\
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Deleting File: ".$file." \n";
					/////*****\\\\\
					unlink($file);
				}
				/////*****\\\\\
				echo gmdate('Y-m-d H:i:s')."----> File Count: ".$file_count." \n";
				/////*****\\\\\

				if($prod_count >= 5000)
				{
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Magmi flag \n";
					/////*****\\\\\
					$magmi_add_prod	= start_magmi_products('True');
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Magmi flag setted \n";
					/////*****\\\\\
				}
				
			}
		}
		if($file_main == 0)
		{
			$flag_main = 'false';
		}
		if($file_sub == 0)
		{
			$flag_sub = 'false';
		}
		if($flag_main == 'false' && $flag_sub == 'false')
		{
			$flag = 'false';
		}
		
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Main Flag: ".$flag_main.", Sub Flag: ".$flag_sub." and Flag: ".$flag." \n";
	echo gmdate('Y-m-d H:i:s')."----> File Count: ".$file_count." and Flag: ".$flag." \n";
	/////*****\\\\\				
		if($file_count <= 3 && $flag == 'true')
		{
			ob_flush();
			sleep(5);
		}
	}
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magmi flag \n";
	/////*****\\\\\
	$magmi_add_prod	= start_magmi_products('Last');
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magmi flag set \n";
	/////*****\\\\\
	
	function start_magmi_products($magmiflag_value)
	{
		global $prod_count;
		global $new_mainfile;
		global $base_url_magento;
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Copying file 'main_file_import_new' to 'main_file_import' \n";
		/////*****\\\\\		
		shell_exec("cp ".$base_url_magento."var/import/main_file_import_new.csv ".$base_url_magento."var/import/main_file_import.csv");
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> File copied \n";
		echo gmdate('Y-m-d H:i:s')."----> Setting magmi flag to ".$magmiflag_value." for inserting products (".$prod_count.") \n";
		/////*****\\\\\
		$fh_magmi	= fopen($base_url_magento.'amazon_import_products/csv_file/magmi_flag.txt', 'w');
		fwrite($fh_magmi,$magmiflag_value);
		fclose($fh_magmi);
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Magmi flag set to ".$magmiflag_value." \n";
		/////*****\\\\\
		$prod_count 	= 0;
		$new_mainfile	= 1;
	}
	
	rtrim($new_sku,",");
	$filehandler	= fopen($base_url_magento.'amazon_import_products/prod_sku/new_products_sku.txt', 'w');
	fwrite($filehandler,$new_sku);
	fclose($filehandler);

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Deleting old products \n";
	/////*****\\\\\
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Getting old skus \n";
	/////*****\\\\\
	$file_oldsku 	= fopen($base_url_magento.'amazon_import_products/prod_sku/old_products_sku.txt', 'r');
	$old_sku_arr	= array();
	while(($content_oldsku = fgets($file_oldsku)) !== FALSE)
	{
		$content_oldsku_arr	= explode(',',$content_oldsku);
		for($b=0;$b<count($content_oldsku_arr);$b++)
		{
			array_push($old_sku_arr,$content_oldsku_arr[$b]);
		}
	}
	fclose($file_oldsku);
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Getting new skus \n";
	/////*****\\\\\
	$file_newsku 	= fopen($base_url_magento.'amazon_import_products/prod_sku/new_products_sku.txt', 'r');
	$new_sku_arr	= array();
	while(($content_newsku = fgets($file_newsku)) !== FALSE)
	{
		$content_newsku_arr	= explode(',',$content_newsku);
		for($a=0;$a<count($content_newsku_arr);$a++)
		{
			array_push($new_sku_arr,$content_newsku_arr[$a]);
		}
	}
	fclose($file_newsku);
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Comparing old ( ".count($content_oldsku_arr)." ) and new ( ".count($content_newsku_arr)." ) skus \n";
	/////*****\\\\\
	$disbaled_skus	= '';
	for($c=0;$c<count($old_sku_arr);$c++)
	{
		if(!in_array($old_sku_arr[$c], $new_sku_arr))
		{
			try
			{
				$sku			= $old_sku_arr[$c];
				$disbaled_skus	.= $sku.",";
				/////*****\\\\\
				echo gmdate('Y-m-d H:i:s')."----> Product SKU = ".$sku."\n";
				/////*****\\\\\
				try
				{
					$product_id		= Mage::getModel('catalog/product')->loadByAttribute('sku',$sku)->getId();
				}
				catch(Exception $e)
				{
					echo gmdate('Y-m-d H:i:s')."----> Unable to load product  \n";
					echo $e->getMessage()."\n";
				}
				$my_product		= Mage::getModel('catalog/product')->load($product_id); 
				$my_product_url	= $my_product->getProductUrl();
				$cats 			= $my_product->getCategoryIds();
				$d				= 0;
				foreach ($cats as $category_id) {
					$d++;
					if($d == 1)
					{
						$cat_url = Mage::getModel('catalog/category')->load($category_id)->getUrl();
					}
				}
				echo gmdate('Y-m-d H:i:s')."----> Product ID (".$product_id.") and SKU (".$sku.") \n";
				echo gmdate('Y-m-d H:i:s')."----> Product (".$product_id.") URL (".$my_product_url.") and Category URL (".$cat_url.") \n";
				$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
				$items = $mediaApi->items($product_id);
				$attributes = $my_product->getTypeInstance()->getSetAttributes();
				$gallery = $attributes['media_gallery'];
				echo gmdate('Y-m-d H:i:s')."----> Getting old product images \n";
				$img_arr = array();
				foreach($items as $item)
				{
					echo gmdate('Y-m-d H:i:s')."----> Image : ".$item['file']." \n";
					array_push($img_arr,$item['file']);
				}
				echo gmdate('Y-m-d H:i:s')."----> Deleting old product (".$product_id.") \n";
				try
				{
					Mage::getModel("catalog/product")->load($product_id)->delete();
					echo gmdate('Y-m-d H:i:s')."----> Old product deleted \n";
					for($imgCount=0;$imgCount<count($img_arr);$imgCount++){unlink($base_url_magento."media/catalog/product".$img_arr[$imgCount]);}
				}
				catch(Exception $e)
				{
					echo gmdate('Y-m-d H:i:s')."----> Unable to delete old product (".$product_id.") because:  \n";
					echo $e->getMessage()."\n";
				}
			}
			catch(Exception $e)
			{
				echo gmdate('Y-m-d H:i:s')."----> Delete product(".$product_id.") failed because:  \n";
				echo $e->getMessage()."\n";
			}
			try
			{
				$prod_url_arr 	= explode("/",$my_product_url);
				$prod_url_count	= count($prod_url_arr);
				$prod_url		= $prod_url_arr[$prod_url_count-1];
				$cat_url_arr 	= explode("/",$cat_url);
				$cat_url_count	= count($cat_url_arr);
				$category_url	= $cat_url_arr[$cat_url_count-2]."/".$cat_url_arr[$cat_url_count-1];
				echo gmdate('Y-m-d H:i:s')."---->  Creating redirect url from (".$prod_url.") to (".$category_url."). \n";
				Mage::getModel('core/url_rewrite')
					->setIsSystem(1)
					->setStoreId(1)   
					->setOptions('RP')  //301 redirect perm
					->setTargetPath($category_url)
					->setRequestPath($prod_url)
					->save();
				echo gmdate('Y-m-d H:i:s')."----> Redirect url created. \n";
			}
			catch(Exception $e)
			{
				echo gmdate('Y-m-d H:i:s')."----> Unable to create redirect url because:  \n";
				echo $e->getMessage()."\n";
			}
			$qget_rewriteid		= "select `url_rewrite_id` from `core_url_rewrite` where `request_path` = '".$prod_url."' && `target_path` = '".$category_url."' ";
			$rget_rewriteid		= mysql_query($qget_rewriteid);
			$rowget_rewriteid	= mysql_fetch_array($rget_rewriteid);
			$rewrite_id			= $rowget_rewriteid['url_rewrite_id'];
			
			$qadd_rewriteid		= "insert into `custom_rewrite_check` ( `url_rewrite_id`, `prod_sku`, `deleted_on` ) values ( '".$rewrite_id."', '".$sku."', '".gmdate('Y-m-d')."' )";
			$radd_rewriteid		= mysql_query($qadd_rewriteid);
		}
	}
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Deleting old rewrites from database \n";
	/////*****\\\\\
	$q = "select * from `custom_rewrite_check`";
	$r = mysql_query($q);
	while($row = mysql_fetch_array($r))
	{
		$deleted_on = date_create($row['deleted_on']);
		$today		= date_create(gmdate('Y-m-d'));
		$diff		= date_diff($deleted_on,$today);
		if($diff->format("%a") > 30)
		{
			$old_rewrite_id		= $rowget_rewriteid['url_rewrite_id'];
			echo gmdate('Y-m-d H:i:s')."----> OLD REWRITE (".$old_rewrite_id.") \n";
			$custom_id			= $rowget_rewriteid['id'];
			$qdel_rewriteid		= "delete from `core_url_rewrite` where `url_rewrite_id` = '".$old_rewrite_id."' ";
			$rdel_rewriteid		= mysql_query($qdel_rewriteid);
			$qdel_customid		= "delete from `custom_rewrite_check` where `id` = '".$custom_id."' ";
			$rdel_customid		= mysql_query($qdel_customid);
		}
	}
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Old rewrites deleted \n";
	/////*****\\\\\
	
	$tempfiles_del = glob($base_url_magento.'amazon_import_products/prod_sku/*'); // get all file names
	foreach($tempfiles_del as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Old products deleted \n";
	/////*****\\\\\

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Clearing Cache \n";
	/////*****\\\\\
	try
	{
		echo gmdate('Y-m-d H:i:s')."----> Clearing image cache \n";
		Mage :: getModel('catalog/product_image')->clearCache();
		echo gmdate('Y-m-d H:i:s')."----> Image cache cleared \n";
	}
	catch(Exception $e)
	{
		echo gmdate('Y-m-d H:i:s')."----> Image cache clearance failed because: \n";
		echo $e->getMessage()."\n";
	}
	try
	{
		echo gmdate('Y-m-d H:i:s')."----> Clearing all caches \n";
		$cacheTypes = Mage::app()->useCache();
		foreach ($cacheTypes as $type => $option){Mage::app()->getCacheInstance()->cleanType($type);}
		echo gmdate('Y-m-d H:i:s')."----> All caches cleared \n";
	}
	catch(Exception $e)
	{
		echo gmdate('Y-m-d H:i:s')."----> All caches clearance failed because: \n";
		echo $e->getMessage()."\n";
	}
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Cache cleared \n";
	/////*****\\\\\
	try
	{
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Reindex URLs \n";
		/////*****\\\\\
		$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_url');
		$process->reindexAll();
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Reindex completed \n";
		/////*****\\\\\		
	}
	catch(Exception $e)
	{
		
		echo gmdate('Y-m-d H:i:s')."----> Reindex failed because: \n";
		echo $e->getMessage()."\n";
	}
	
	$pCollection 		= Mage::getSingleton('index/indexer')->getProcessesCollection(); 
	foreach ($pCollection as $process) {
	  $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
	}	
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magento script completed \n";
	/////*****\\\\\
	
?>