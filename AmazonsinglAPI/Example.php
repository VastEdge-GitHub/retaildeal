<?php
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 

    $obj = new AmazonProductAPI();

	$minPrice = '3500';				//$35
	$maxPrice = '250000';			//$2500	
	$base_url_magento	= '/home/retail/public_html/';
	
		$sku_all = array("B00E0GRTB8","B00KVHEL8E","B00CMHWTRQ","B00O65HZKS","B002WWH5PM","B00J0825H4","B002UKHEO8","B00JBJ3JBI");  
		$count_error=0;
		$sql = "select * from featured_products";
		$retval = mysql_query($sql);
		if(! $retval )
		{
	 		 die('Could not get data: ' . mysql_error());
		}
	$fh1	= fopen($base_url_magento.'amazon_import_products/sub_categories_query_count1.csv', 'w');
	$rows = array();
	while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
	{	
		$rows[$row['id']] = $row;
	}
	/*foreach($rows as $row)
	{	
		echo $row['id']."|".$row['cname']."|".$row['cat_id']."|".$row['p_id']."|".$row['asin'];
	}
	die;*/
		foreach($rows as $row)
		{	$chk_exception='';
			try 
			{
			 	/*if($sku_all[$sku]=='B00E0GRTB8'){$cat_id='3565,3565';} 
				if($sku_all[$sku]=='B00KVHEL8E'){$cat_id='3562';}
				if($sku_all[$sku]=='B00CMHWTRQ'){$cat_id='3569';}
				if($sku_all[$sku]=='B00O65HZKS'){$cat_id='3629';}
				if($sku_all[$sku]=='B00J0825H4'){$cat_id='3564';}
				if($sku_all[$sku]=='B002WWH5PM'){$cat_id='3569';}
				if($sku_all[$sku]=='B002UKHEO8'){$cat_id='3568';}
				if($sku_all[$sku]=='B00JBJ3JBI'){$cat_id='3564';}*/ 

				//$row['p_id'] = '4211'; 
				echo $row['id']."|".$row['cname']."|".$row['cat_id']."|".$row['p_id']."|".$row['asin']." <br>";
				$result = $obj->searchProducts($row['asin']);
				$csv_filename				=$row['asin']."@".$row['cat_id']."@".$row['id']."@TOP_SELLER.csv"; // xml to csv file name		
				$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
				//$csv_file					= "csv_file/".$csv_filename;		// xml to csv file full path
				//echo file_put_contents($csv_file,print_r($result,true));echo " \n";
				$feturedNo					= 1;
				$fileconversion_response	= xml_to_csv_conversion($result,$csv_file,$feturedNo);
				//if($result->Items->Item->ASIN=='B00JBJ3JBI')break;
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