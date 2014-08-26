<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);
	
	error_reporting(E_ALL | E_STRICT);
	ob_implicit_flush(true);

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magento script started \n";
	/////*****\\\\\
	
	global $base_url_magento;
	global $prod_count;
	global $new_sku;
	$new_sku			= '';
	$base_url_magento	= '/opt/bitnami/apps/magento/htdocs/';
    include($base_url_magento."amazon_import_products/amazon_api_class.php");
    include($base_url_magento."amazon_import_products/amazon_parse_xml.php");
	include($base_url_magento."app/Mage.php");
   	include($base_url_magento."amazon_import_products/amazon_insert_products.php");
	
	$mageFilename		= $base_url_magento.'app/Mage.php';
	require_once $mageFilename;
	$app				= Mage::app('default');
	
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
	
	$flag			= 'true';
	$file_main		= 0;
	$file_sub		= 0;
	$prod_count		= 0;
	$new_mainfile	= 1;
	$file_count		= count($files_process);
	while($file_count > 2 || $flag == 'true')
	{
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Reading Folder:  \n";
		/////*****\\\\\
		$file_count	= 0;
		$files_process	= glob($base_url_magento.'amazon_import_products/csv_file/*'); // get all file names
		foreach($files_process as $file)
		{
			 // iterate files
			if(is_file($file))
			{
				if($new_mainfile == 1)
				{
					$main_fh		= fopen($base_url_magento.'var/import/main_file_import.csv', 'w');
					fwrite($main_fh,'store|websites|attribute_set|type|category_ids|sku|name|image|small_image|thumbnail|amazon_prime|liki_desription|vastedge_meta_robots|price|weight|standard_shipping|status|visibility|tax_class_id|wp_amazon_sync|wp_amazon_use_categories|description|short_description|wp_amazon_local|wp_amazon_asin|wp_amazon_ean|wp_amazon_offer_condition|wp_amazon_offer_price_type|wp_amazon_offer_price|wp_amazon_offer_currency|wp_amazon_offers_list_url|wp_amazon_product_url|wp_amazon_reviews_url|liki_price|qty|min_qty|use_config_min_qty|is_qty_decimal|backorders|min_sale_qty|use_config_min_sale_qty|max_sale_qty|use_config_max_sale_qty|is_in_stock|use_config_manage_stock|product_name|store_id|product_type_id'.PHP_EOL);
					fclose($main_fh);
				}
				$new_mainfile = 0;
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$file_count++;
				if($file == '/opt/bitnami/apps/magento/htdocs/amazon_import_products/csv_file/main_categories.txt')
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
				if($file == '/opt/bitnami/apps/magento/htdocs/amazon_import_products/csv_file/sub_categories.txt')
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
					echo gmdate('Y-m-d H:i:s')."----> Inserting Products: ".$insert_products." \n";
					/////*****\\\\\
					$magmi_add_prod	= add_magmi_products();
					/////*****\\\\\
					echo gmdate('Y-m-d H:i:s')."----> Products Inserted \n";
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

	}
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Inserting Products: ".$insert_products." \n";
	/////*****\\\\\
	$magmi_add_prod	= add_magmi_products();
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Products Inserted \n";
	/////*****\\\\\
	$new_mainfile	= 1;
	
	function add_magmi_products()
	{
		global $prod_count;
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Magmi products (".$prod_count.") add start \n";
		/////*****\\\\\
		global $base_url_magento;
		shell_exec('sudo php '.$base_url_magento.'magmi/cli/magmi.cli.php -mode=create -profile=default');
		$prod_count = 0;
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Magmi product add completed \n";
		/////*****\\\\\
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Deleting temporary images \n";
		/////*****\\\\\
		$files_del = glob($base_url_magento.'amazon_import_products/liki_img/cache/*'); // get all file names
		foreach($files_del as $file){ // iterate files
		  if(is_file($file))
			unlink($file); // delete file
		}
		$new_mainfile	= 1;
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Temporary images deleted \n";
		/////*****\\\\\
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
	echo gmdate('Y-m-d H:i:s')."----> Comparing old and new skus \n";
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
				$product_id		= Mage::getModel('catalog/product')->loadByAttribute('sku',$sku)->getId();
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
				
				$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
				$items = $mediaApi->items($product_id);
				$attributes = $my_product->getTypeInstance()->getSetAttributes();
				$gallery = $attributes['media_gallery'];
				echo gmdate('Y-m-d H:i:s')."----> Deleting old images \n";
				foreach($items as $item)
				{
					if ($gallery->getBackend()->getImage($my_product, $item['file']))
					{
						$gallery->getBackend()->removeImage($my_product, $item['file']);
					}
				}
				echo gmdate('Y-m-d H:i:s')."----> Old images deleted \n";
				Mage::getModel("catalog/product")->load($product_id)->delete();
				echo gmdate('Y-m-d H:i:s')."----> Old product deleted \n";				
			}
			catch(Exception $e)
			{
				echo gmdate('Y-m-d H:i:s')."----> Unable to delete old product. \n";
				Mage::log($e->getMessage());
			}
		}
	}
	try
	{
		echo gmdate('Y-m-d H:i:s')."----> Creating redirect url. \n";
		$results = Mage::getModel('enterprise_urlrewrite/url_rewrite')
			->setStoreId($store_id)
			->setOptions('RP')
			->setIdentifier($my_product_url)
			->setTargetPath($cat_url)
			->setDescription('')
			->save();
		echo gmdate('Y-m-d H:i:s')."----> Redirect url created. \n";
	}
	catch(Exception $e)
	{
		echo gmdate('Y-m-d H:i:s')."----> Unable to create redirect url. \n";
		Mage::log($e->getMessage());
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
		Mage::log($e->getMessage());
		echo gmdate('Y-m-d H:i:s')."----> Image cache clearance failed because: \n";
		echo $e->getMessage();
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
		Mage::log($e->getMessage());
		echo gmdate('Y-m-d H:i:s')."----> All caches clearance failed because: \n";
		echo $e->getMessage();
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
		
		Mage::log($e->getMessage());
		echo gmdate('Y-m-d H:i:s')."----> Reindex failed because: \n";
		echo $e->getMessage();
	}
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magento script completed \n";
	/////*****\\\\\
	
?>