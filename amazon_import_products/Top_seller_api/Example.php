<?php
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 

    $obj = new AmazonProductAPI();
    $base_url_magento = '/home/retail/public_html/';
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
	echo gmdate('Y-m-d H:i:s')."----> Top Seller Product started \n";
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
		$count_error=0;
		$files_process	= glob($base_url_magento.'amazon_import_products/Top_seller_api/csv_file/*'); // get all file names
		$filecount_flag	= 'false';
		foreach($files_process as $file)
		{	
			if(is_file($file))
			{	
					$cat_array	= explode("@",$file);
					$cat_id		= $cat_array[1];
					echo "<br>".$cat_id."<br/>";
					$a=1;
					$filehandler = fopen($file, 'r');	
				while(($content = fgets($filehandler)) !== FALSE)									// Reading file line by line
				{
				echo "file name == --->".$file;
				
					if($a != '1')															// Leaving header line from csv file
					{
						
						try 
						{
								
							echo "<br>".$content;
							$result = $obj->searchProducts(trim($content));
							//print_r($result);
							$csv_filename				=trim($content)."@".trim($cat_id)."@"."Top_seller.csv"; // xml to csv file name		
							$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
							$feturedNo					= 1; 
							$topseller					= 1; 
							$fileconversion_response	= xml_to_csv_conversion($result,$csv_file,$feturedNo,$topseller);
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
					
					}$a++;
				}	
			}		
		}
	}
?>