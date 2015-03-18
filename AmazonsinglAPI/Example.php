<?php
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	include("config.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 
    $obj = new AmazonProductAPI();
    $base_url_magento = '/home/retail/public_html/';
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
	foreach($rows as $row)
	{
		$chk_exception='';
		$count_error = 0;
		$_getData  = true;
		while($_getData)
		{
			try 
			{ 
				echo "Getting data for - ". $row['id']."|".$row['cname']."|".$row['cat_id']."|".$row['p_id']."|".$row['asin']." \n";
				$result 					= $obj->searchProducts($row['asin']);
				$csv_filename				=$row['asin']."@".$row['cat_id']."@".$row['id']."@Featured_ASIN.csv"; // xml to csv file name		
				$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
				$feturedNo					= 1;
				$topseller					= 1;
				$fileconversion_response	= xml_to_csv_conversion($result,$csv_file,$feturedNo,$topseller); 
				
				echo "Data converted for - ". $row['id']."|".$row['cname']."|".$row['cat_id']."|".$row['p_id']."|".$row['asin']." \n\n";
				$_getData = false;
			}
			catch(Exception $e)
			{
				$chk_exception = $e->getMessage();
				echo $chk_exception . " for " . "" .$row['id']."|".$row['cname']."|".$row['cat_id']."|".$row['p_id']."|".$row['asin'] ."\n\n";
				//if($chk_exception == 'Invalid xml response.')
				//{
					$count_error++;
					if($count_error > 1)
					{
						$count_error = 0;
						$_getData = false; 
					}
				//}
				ob_flush();
				sleep(2);
				continue;
			}
		}
	}
?>