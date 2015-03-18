<?php
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 

    $obj = new AmazonProductAPI();
    $base_url_magento = '/opt/bitnami/apps/magento/htdocs/';
	
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
					$file = fopen($file, 'r');	
				while(($content = fgets($file)) !== FALSE)									// Reading file line by line
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
		
?>