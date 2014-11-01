<?php
ini_set("max_execution_time", 0);
ini_set("memory_limit", -1);
ini_set('display_errors', 1);

error_reporting(E_ALL | E_STRICT);
ob_implicit_flush(true);

global $base_url_magento;
$base_url_magento	= '/opt/bitnami/apps/magento/htdocs/';
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
	shell_exec('sudo php '.$base_url_magento.'magmi/cli/magmi.cli.php -mode=create -profile=default');
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
			if($amazon_id != '1' && $amazon_id != '')
			{
				if($data['level'] == 3)										// Only sub-categories upto level 3
				{
					$parent_name_arr	= explode("/",$data['url_path']);
					$parent_name		= $parent_name_arr[0];
					if($parent_name == 'appliances')
					{
						$cat_name		= $cat_array_namings[0];
					}
					if($parent_name == 'automotive')
					{
						$cat_name 		= $cat_array_namings[1];
					}
					if($parent_name == 'baby-products')
					{
						$cat_name 		= $cat_array_namings[2];
					}
					if($parent_name == 'computers')
					{
						$cat_name 		= $cat_array_namings[3];
					}
					if($parent_name == 'electronics')
					{
						$cat_name 		= $cat_array_namings[4];
					}
					if($parent_name == 'health-personal-care')
					{
						$cat_name 		= $cat_array_namings[5];
					}
					if($parent_name == 'home-garden')
					{
						$cat_name 		= $cat_array_namings[6];
					}
					if($parent_name == 'pet-supplies')
					{
						$cat_name 		= $cat_array_namings[7];
					}
					if($parent_name == 'sports-outdoors')
					{
						$cat_name 		= $cat_array_namings[8];
					}
					if($parent_name == 'toys-games')
					{
						$cat_name 		= $cat_array_namings[9];
					}
					if($parent_name == 'jewelry')
					{
						$cat_name 		= $cat_array_namings[10];
					}
					$cat_info 	= $data['name']."||".$data['entity_id']."||".$data['parent_id']."||".$data['path']."||".$data['level']."||".$data['children_count']."||".$amazon_id."||".$data['is_active'];
					$collection=$cat->getProductCollection();
					$collection->setVisibility(array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG));     
					$collection->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
					$content .= '<tr><td>'.$sno.'</td><td>'.$cat_name.'/'.$data['name'].'</td><td>'.$collection->count().'<td>';
					$abc += $collection->count();
					$sno++;
				}
			}
		}
	}
}
$content .= '<tr><td colspan="2">Total</td><td>'.$abc.'<td></table>';

$fh			= fopen($base_url_magento.'amazon_import_products/detail_products.php', 'w');
fwrite($fh,'<?php echo \''.$content.'\'; ?>');
fclose($fh);

$url 		= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
$mail_data	= 'Total number of products within sub-categories is: '.$abc.'. Visit following link for more details: '.$url.'amazon_import_products/detail_products.php';
shell_exec('echo "'.$mail_data.'" | mail -s \'Products Update\' upinder@essitco.com,vivek@essitco.com,vishal@essitco.com');
/////*****\\\\\
echo gmdate('Y-m-d H:i:s')."----> Magmi completed \n";
/////*****\\\\\
?>