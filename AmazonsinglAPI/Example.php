<?php
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	include("config.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 
	
	$file_count			= 0;
	
    $obj = new AmazonProductAPI();
    $base_url_magento = '/home/retail/public_html/';
	$count_error=0;
	
	$fh1	= fopen($base_url_magento.'amazon_import_products/sub_categories_query_count1.csv', 'w');
	
	$file_main_check	= "false";
	$file_sub_check		= "false";
	if(file_exists($base_url_magento.'amazon_import_products/csv_file/main_categories.txt'))
	{
		$file_main_check		= 'true';
	}
	if(file_exists($base_url_magento.'amazon_import_products/csv_file/main_categories.txt'))
	{
		$file_sub_check			= 'true';
	}
	echo gmdate('Y-m-d H:i:s')."----> Featured Product started \n";
	while($file_main_check == 'true' || $file_sub_check == 'true')
	{
		$main_file = $base_url_magento.'amazon_import_products/csv_file/main_categories.txt';
		$fh = fopen($main_file,'r');
		while ($line = fgets($fh))
		{
			//echo $line;
			if($line == 'True')
			{
				$file_main_check = 'true';
				echo gmdate('Y-m-d H:i:s')."----> Wating...! Main Categories(".$line.") Products are pushing \n";
				sleep(10);
				continue; 
			}
			else
			{
				$file_main_check = 'false';
			}
		}
		fclose($fh);
		$sub_file = $base_url_magento.'amazon_import_products/csv_file/sub_categories.txt';
		$fh = fopen($sub_file,'r');
		while ($line = fgets($fh))
		{
			//echo $line;
			if($line == 'True')
			{
				$file_sub_check = 'true';
				echo gmdate('Y-m-d H:i:s')."----> Wating...! Sub Categories(".$line.") Products are pushing \n";
				sleep(10);
				continue;
			}
			else
			{
				$file_sub_check = 'false';
			}
		}
		fclose($fh);	
	}
	if($file_sub_check == 'false' && $file_main_check== 'false')
	{
		$sql = "select * from featured_products";
		$retval = mysql_query($sql);
		if(! $retval )
		{
			 die('Could not get data: ' . mysql_error());
		}
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
					$file_count++;
					
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
	}
	echo gmdate('Y-m-d H:i:s')."----> Total csv files: ".$file_count." \n";
	echo gmdate('Y-m-d H:i:s')."----> Featured Product Import Completed \n";
?>