<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);	
	error_reporting(E_ALL | E_STRICT);     
	ob_implicit_flush(true);

	$dbhost = 'localhost';
	$dbuser = 'bn_magento';
	$dbpass = 'fbeee979d3';
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	if(! $conn )
	{
	  die('Could not connect: ' . mysql_error());
	}

	global $base_url_magento;
	$base_url_magento = '/home/retail/public_html/';

    include("amazon_api_class.php");
	include($base_url_magento."amazon_import_products/Top_seller_api/amazon_parse_xml.php");
    $obj = new AmazonProductAPI();

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
		mysql_select_db('rd_qmt'); 
		$sql = "select distinct nodeid,categoryid from query"; 
		$retval = mysql_query($sql);
		if(! $retval )
		{
			 die('Could not get data: ' . mysql_error());
		}
	
		$browsenodes_array = array();
		$cat_array		   = array(); 
		while($row = mysql_fetch_assoc($retval))
		{	
		 	  array_push($browsenodes_array,$row["nodeid"]);
		 	  array_push($cat_array,$row["categoryid"]);
						  
		}
		for($n=0;$n< count($cat_array);$n++)
		{
			try 
			{
			$result = $obj->topProducts($browsenodes_array[$n]);
			print_r($result); 
			
			ob_flush();
			 sleep(2);
			}	
			
			catch(Exception $e)
			{
				$chk_exception = $e->getMessage();			
				echo gmdate('Y-m-d H:i:s')."----> ".$chk_exception ." for: \n";
				$count_error++;					
				if($chk_exception == 'Invalid xml response.')
				{
				if($count_error < 1)
				$result = $obj->topProducts($browsenodes_array[$n]);
				$count_error++;
				}
					$count_error =0;
					ob_flush();
					sleep(2);
					continue; 
			}
			echo "brows node---------->".$browsenodes_array[$n].'<br/>';
			echo gmdate('Y-m-d H:i:s')."----> Amazon Import Completed \n"; 	
			echo gmdate('Y-m-d H:i:s')."----> XML to CSV Started \n";		
			$csv_filename				= $browsenodes_array[$n]."@".$cat_array[$n]."@Top_seller.csv"; // xml to csv file name
			
			echo gmdate('Y-m-d H:i:s')."----> CSV File: ".$csv_filename." \n"; 
			$csv_file					= $base_url_magento."amazon_import_products/Top_seller_api/csv_file/".$csv_filename;	// xml to csv file full path
			$fileconversion_response	= xml_to_csv_conversion_browsenode_lookup($result,$csv_file);
			
			
			echo gmdate('Y-m-d H:i:s')."----> XML to CSV Completed \n";
			$count_error = 0;
		}
	 }
	?>