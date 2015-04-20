<?php
ini_set("max_execution_time", 0);
ini_set("memory_limit", -1);
ini_set('display_errors', 1);

error_reporting(E_ALL | E_STRICT);
ob_implicit_flush(true);

global $base_url_magento;
$base_url_magento	= '/home/retail/public_html/';
/////*****\\\\\
echo gmdate('Y-m-d H:i:s')."----> Magmi started \n";
/////*****\\\\\
$flag_start		= 'true';
while($flag_start == 'true')
{
	$fh = fopen($base_url_magento.'amazon_import_products/csv_file/magmi_flag.txt','r');
	while ($line = fgets($fh))
	{
		if($line == 'True')
		{
			add_magmi_products('True');
		}
		elseif($line == 'Last')
		{
			add_magmi_products('Last');
			$flag_start = 'false';
		}
		else{}
	}
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magmi Flag = ".$line." \n";
	/////*****\\\\\	
	fclose($fh);
	ob_flush();
	sleep(30);
}

function add_magmi_products($flag_magmi)
{
	global $base_url_magento;
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magmi product insert started with flag ".$flag_magmi." \n";
	/////*****\\\\\
//	shell_exec('sudo php '.$base_url_magento.'magmi/cli/magmi.cli.php -mode=create -profile=default');
shell_exec('/usr/bin/php '.$base_url_magento.'magmi/cli/magmi.cli.php -mode=create -profile=default');
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Magmi product completed \n";
	/////*****\\\\\
	unlink($base_url_magento.'amazon_import_products/csv_file/magmi_flag.txt');
	unlink($base_url_magento.'var/import/main_file_import.csv');
	if($flag_magmi == 'Last')
	{
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Deleting temporary images \n";
		/////*****\\\\\
		$files_del = glob($base_url_magento.'amazon_import_products/liki_img/cache/*'); // get all file names
		foreach($files_del as $file){ // iterate files
		  if(is_file($file))
			unlink($file); // delete file
		}
		/////*****\\\\\
		echo gmdate('Y-m-d H:i:s')."----> Temporary images deleted \n";
		/////*****\\\\\
		
		$tempfiles_del = glob($base_url_magento.'amazon_import_products/csv_file/*'); // get all file names
		foreach($tempfiles_del as $file){ // iterate files
		  if(is_file($file))
			unlink($file); // delete file
		}
	}
}

/////*****\\\\\
echo gmdate('Y-m-d H:i:s')."----> Mail Trigger \n";
/////*****\\\\\

include($base_url_magento.'app/Mage.php');
$app = Mage::app('default');

$category 				= Mage::getModel('catalog/category');
$tree 					= $category->getTreeModel();
$tree->load();
$ids 					= $tree->getCollection()->getAllIds();
$cat_array_namings		= array("Appliances","Automotive","Baby Products","Computers","Electronics","Health & Personal Care","Home & Garden","Pet Supplies","Sporting & Outdoors","Toys & Games","Jewelry");
$content = '<table><tr><th>S.No.</th><th>Category</th><th>No. of products</th></tr>';
$sno					= 1;
$abc					= 0;
if($ids)
{
	foreach($ids as $id)
	{
		$cat 			= Mage::getModel('catalog/category');
		$cat->load($id);
		$data 			= $cat->getData();
		$description	= $data['description'];
		$desc_arr		= explode("==",$description);
		$amazon_id		= $desc_arr[1];
		$cat_active		= $data['is_active'];
		if($cat_active == '1')												// Only active categories
		{
			//if($amazon_id != '1' && $amazon_id != '')
			//{
			if($data['level'] >= 2)										// Only sub-categories upto level 3
				{
					$parent_name_arr	= explode("/",$data['url_path']);						
					$parent_name_count	= count($parent_name_arr);
					$parent_name		= $parent_name_arr[$parent_name_count -2];  // For get oarent catgory edit by liki on 25-Nov-014
				 if($parent_name != 'and-more')
					{
					if($parent_name == 'appliances')
					{
						$cat_name		= $cat_array_namings[0];
					}
					/*if($parent_name == 'automotive')
					{
						$cat_name 		= $cat_array_namings[1];
					}*/
					if($parent_name == 'baby-products')
					{
						$cat_name 		= $cat_array_namings[1];
					}
					if($parent_name == 'camera-electronics')
					{
						$cat_name 		= $cat_array_namings[2];
					}
					if($parent_name == 'computers')
					{
						$cat_name 		= $cat_array_namings[3];
					}
					if($parent_name == 'furniture')
					{
						$cat_name 		= $cat_array_namings[4];
					}
					if($parent_name == 'mattresses')
					{
						$cat_name 		= $cat_array_namings[5];
					}
					/*if($parent_name == 'pet-supplies')
					{
						$cat_name 		= $cat_array_namings[7];
					}*/
					if($parent_name == 'television-home-theater')
					{
						$cat_name 		= $cat_array_namings[6];
					}
					if($parent_name == 'video-games')
					{
						$cat_name 		= $cat_array_namings[7];
					}
					/*if($parent_name == 'jewelry')
					{
						$cat_name 		= $cat_array_namings[10];
					  }*/
					}
					$cat_info 	= $data['name']."||".$data['entity_id']."||".$data['parent_id']."||".$data['path']."||".$data['level']."||".$data['children_count']."||".$amazon_id."||".$data['is_active'];
					$collection=$cat->getProductCollection();
					$collection->setVisibility(array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG));     
					$collection->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
					$content .= '<tr><td>'.$sno.'</td><td>'.$cat_name.'/'.$data['name'].'</td><td>'.$collection->count().'<td>';
					$abc += $collection->count();
					$sno++;
				}
			//}
		}
	}
}
$content .= '<tr><td colspan="2">Total</td><td>'.$abc.'<td></table>';

$fh			= fopen($base_url_magento.'amazon_import_products/detail_products.php', 'w');
fwrite($fh,'<?php echo \''.$content.'\'; ?>');
fclose($fh);

$url 		= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
$mail_data	= 'Total number of products within sub-categories is: '.$abc.'. Visit following link for more details: '.$url.'amazon_import_products/detail_products.php';
shell_exec('echo "'.$mail_data.'" | mail -s \'Magemojo Products Update\' upinder@essitco.com,vivek@essitco.com,vishal@essitco.com,manoj@essitco.com,kailash@vastedge.com'); 
/////*****\\\\\
echo gmdate('Y-m-d H:i:s')."----> Magmi completed \n";
/////*****\\\\\
//include($base_url_magento."app/Mage.php");
$mageFilename		= $base_url_magento.'app/Mage.php';
require_once $mageFilename;
$app				= Mage::app('default');

$custom_hostname="localhost"; 
$custom_username="bn_magento";
$custom_password="fbeee979d3";
$custom_dbName="rd_qmt";
if($custom_conn=mysql_connect($custom_hostname,$custom_username,$custom_password)){}
else{echo "Something went wrong. Unable to establish MySQL conection";}
mysql_select_db($custom_dbName, $custom_conn);
$sql = "select asin from featured_products";
$retval = mysql_query($sql);
if(! $retval )
{
	 die('Could not get data: ' . mysql_error());
}
//QUERY MANAGEMENT TOOLDATABASE CONNECTION CREATED

$_asin = array();
//print_r($_asin);
while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
{	
	array_push($_asin,$row['asin']);
}
//Converted into an array
//print_r($_asin);
$custom_hostname="localhost";
$custom_username="bn_magento";
$custom_password="fbeee979d3";
$custom_dbName="bitnami_magento";
if($custom_conn=mysql_connect($custom_hostname,$custom_username,$custom_password)){echo "MySQL conection has been created successfully ";}
else{echo "Something went wrong. Unable to establish MySQL conection";}
mysql_select_db($custom_dbName, $custom_conn);

$sql = "select entity_id from `catalog_product_flat_1` where sku in ('".implode("','",$_asin)."')";
//echo $sql; 
$retval = mysql_query($sql);
if(! $retval )
{
	 die('Could not get data: ' . mysql_error());
}
//QUERY MANAGEMENT TOOLDATABASE CONNECTION CREATED

$_entity = array();
//print_r($_asin);
while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
{	
	array_push($_entity,$row['entity_id']);
}
$sql = "update catalog_product_entity_int set value=1 where attribute_id=154 and entity_id in ('".implode("','",$_entity)."')";
$retval = mysql_query($sql);
$sql = "update catalog_product_entity_int set value=1 where attribute_id=192 and entity_id in ('".implode("','",$_entity)."')";
$retval = mysql_query($sql);
?>
