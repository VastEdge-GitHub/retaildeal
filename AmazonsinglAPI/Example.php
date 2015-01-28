<?php
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 

    $obj = new AmazonProductAPI();

	$minPrice = '3500';				//$35
	$maxPrice = '250000';			//$2500	
    $base_url_magento = '/opt/bitnami/apps/magento/htdocs/';
		$sku_all = array("B00E0GRTB8","B00KVHEL8E","B00CMHWTRQ","B00O65HZKS","B002WWH5PM","B00J0825H4","B002UKHEO8");
		$count_error=0;
		for($sku=0;$sku<=count($sku_all);$sku++)
		{	$chk_exception='';
			try 
			{
				if($sku_all[$sku]=='B00E0GRTB8'){$cat_id='3565';}
				if($sku_all[$sku]=='B00KVHEL8E'){$cat_id='3564,3651';}
				if($sku_all[$sku]=='B00CMHWTRQ'){$cat_id='3569';}
				if($sku_all[$sku]=='B00O65HZKS'){$cat_id='3562,3629';}
				if($sku_all[$sku]=='B002WWH5PM'){$cat_id='3569';}
				if($sku_all[$sku]=='B00J0825H4'){$cat_id='3564';}
				if($sku_all[$sku]=='B002UKHEO8'){$cat_id='3568';}


				$result = $obj->searchProducts($sku_all[$sku]);
				$csv_filename				=$sku_all[$sku]."@".$cat_id.".csv"; // xml to csv file name		
				$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
				//$csv_file					= "csv_file/".$csv_filename;		// xml to csv file full path
				//echo file_put_contents($csv_file,print_r($result,true));echo " \n";
				$fileconversion_response	= xml_to_csv_conversion($result,$csv_file);
			}
		
			catch(Exception $e)
			{
				$chk_exception = $e->getMessage();
				
				if($chk_exception == 'Invalid xml response.')
				{
					$count_error++;
					$sku--;
					if($count_error > '1')
					{
						$count_error = 0; 
					}
				}
				
					ob_flush();
					sleep(2);
					continue;
			}
		}
?>