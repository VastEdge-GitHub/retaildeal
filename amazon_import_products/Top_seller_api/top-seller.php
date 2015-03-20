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
	$base_url_magento = '/opt/bitnami/apps/magento/htdocs/';
	
    include("amazon_api_class.php");
	include($base_url_magento."amazon_import_products/Top_seller_api/amazon_parse_xml.php");
    $obj = new AmazonProductAPI();
	
	mysql_select_db('rd_qmt'); 
	
	$sql = "select * from category where nodeid >0"; 
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
 /*       $browsenodes_array	= array("166863011");	
    		$cat_array		   = array('3626');	 */ 
		     $count_error =0;
				
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
	?>