<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);	
	error_reporting(E_ALL | E_STRICT);     
	ob_implicit_flush(true);
	
	global $base_url_magento;
	$base_url_magento = '/home/retail/public_html/';
	
	echo gmdate('Y-m-d H:i:s')."----> Main Category Product Import Started \n";
	
	$fh				= fopen($base_url_magento.'amazon_import_products/csv_file/main_categories.txt', 'w');
	fwrite($fh,'True');
	fclose($fh);
	
	//// Delete previous cache files if available
	$files_del = glob($base_url_magento.'amazon_import_products/liki_img/cache/*'); // get all file names
	foreach($files_del as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	
    include($base_url_magento."amazon_import_products/amazon_api_class.php");
    include($base_url_magento."amazon_import_products/amazon_parse_xml.php");
	global $chk_exception;
	$file_count			= 0;

    $obj				= new AmazonProductAPI();
	
	echo gmdate('Y-m-d H:i:s')."----> Loop for main categories started \n";
												
           $cat_array = array("Appliances","Automotive","Baby","PCHardware","Electronics","HealthPersonalCare","HomeGarden","PetSupplies","SportingGoods","Toys","Jewelry","Beauty","Kitchen","OutdoorLiving","Photo","Tools","Watches","Wireless","WirelessAccessories"); // Amazon Categories
		   
         $browsenodes_array	= array("2619525011","15690151","165796011","541966","172282","3760931","285080","12923371","3375251","165793011","3880591","11055981","284507","286168","502394","468240","377110011","508494","13900851");	// Amazon BrowseNodes of Categories
 	
	$cat_id_arr 			= array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570","3565","3566","3566","3564","3559","3564","3564","3564");	
	// Magento category IDs
	
	
	
	$dbhost = 'localhost';
	$dbuser = 'bn_magento';
	$dbpass = 'fbeee979d3';
	//$dbuser = 'root'; 
	//$dbpass = 'abc.123';
	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	if(! $conn )
	{
	  die('Could not connect: ' . mysql_error());
	}
	$sql = "select * from query where state = 'active' and parent_id = 2";
	
	mysql_select_db('rd_qmt');
	$retval = mysql_query( $sql, $conn );
	if(! $retval )
	{
	  die('Could not get data: ' . mysql_error());
	}
	$fh1	= fopen($base_url_magento.'amazon_import_products/main_categories_query_count.txt', 'w');
	while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
	{
		fwrite($fh1,$row['nodeid']."|".$row['categoryid']."|".$row['categoryname']."|".$row['manufacturer']."|".$row['brands']."|".$row['keywords']."|".$row['nodename']."|".$row['id'].PHP_EOL);
		echo $row['nodeid']."|".$row['categoryid']."|".$row['categoryname']."|".$row['manufacturer']."|".$row['brands']."|".$row['keywords']."|".$row['nodename']." <br> ";	
		$nodeid 		= $row['nodeid'];
		$categoryid		= $row['categoryid'];
		$categoryname	= $row['categoryname'];
		$manufacturer	= $row['manufacturer'];
		$brands			= $row['brands'];
		$keyowrds		= $row['keywords'];
		$nodename		= $row['nodename'];
		$query_state	= 'inactive';
		for($l=1;$l<=3;$l++)
		{
			$max_pages		= 10;
			if($l==1)
			{
				$minPrice	= '5000';				// $50
				$maxPrice	= '85000';				// $1250
			}
			if($l==2)
			{
				$minPrice 	= '85100';				// $851
				$maxPrice 	= '160000';				// $1600
			}
			if($l==3)
			{
				$minPrice 	= '165100';				// $1651
				$maxPrice 	= '250000';				// $2500
			}
			//if($cat_array[$n] == 'Appliances'){$max_pages = 10;}
			//if($cat_array[$n] == 'Automotive'){$max_pages = 10;}
			$count_error	= '0';
			$pageNum		= 1;
			$lastPage 		= $max_pages;
			//$Manufacturer	= "";
			//$Keywords		= "";
			while($pageNum <= $lastPage)
			{
				$chk_exception		= '';

	echo gmdate('Y-m-d H:i:s')."----> Amazon Import Started for Title = ".$nodename." || Page No. = ".$pageNum." || Min Price = ".$minPrice." \n";
												
				try
				{
					echo gmdate('Y-m-d H:i:s')."---->prices:".$minPrice.$maxPrice." \n";
					$result = $obj->searchProducts($nodeid,$nodename,"BrowseNode",$pageNum,$brands,$keyowrds,$minPrice,$maxPrice,$nodename);
					if (isset($result->Items->Item->ItemAttributes->Title)){$query_state='active'; echo " \n ".$query_state." activated \n ";}
				}
				catch(Exception $e)
				{
					$chk_exception = $e->getMessage();
					
	echo gmdate('Y-m-d H:i:s')."----> ".$chk_exception ." for: \n";
	echo "Error Count - ".$count_error." || BN ID - ".$nodeid." || Cat Name - ".$nodename." || Page No. - ".$pageNum." || Min Price - ".$minPrice." || Max Price - ".$maxPrice."++++++++++++ \n";
										
					if($chk_exception == 'Invalid xml response.')
					{
						$count_error++;
						if($count_error > '1')
						{
							$pageNum++;
							$count_error = 0;
							
						}
					}
					ob_flush();
					sleep(2);
					continue; 
				}
				$TotalPages=$result->Items->TotalPages;
				if($TotalPages < 10){$lastPage = $TotalPages;}
	
	echo gmdate('Y-m-d H:i:s')."----> Amazon Import Completed \n";
				
				if($chk_exception != ''){}
				else
				{
	
	echo gmdate('Y-m-d H:i:s')."----> XML to CSV Started \n";
	
					$csv_filename				= $nodename."@".$categoryid."@MainLoop".$row['id']."@SubLoop".$l."@PageNum".$pageNum.".csv"; // xml to csv file name
	
	echo gmdate('Y-m-d H:i:s')."----> CSV File: ".$csv_filename." \n";
					
					$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
					//echo file_put_contents($csv_file,print_r($result,true));echo " \n";
				    $feturedNo					= 0;
					$topseller					= 0;
					$fileconversion_response	= xml_to_csv_conversion($result,$csv_file,$feturedNo,$topseller);
					/*if($fileconversion_response>10 || $fileconversion_response!=10){
					$fh_morethan_ten = fopen($base_url_magento.'amazon_import_products/greater_than_ten_result.csv', 'a');
					fwrite($fh_morethan_ten,$browsenodes_array[$n]."|".$cat_array[$n]."|".$pageNum."|".$manufacturer."|".$keywords."|".$minPrice."|".$maxPrice."|".$cat_array[$n]."|".$fileconversion_response."|".$csv_filename."".PHP_EOL);}*/ 
					$file_count++;
	
	echo gmdate('Y-m-d H:i:s')."----> XML to CSV Completed \n";
					$count_error = 0;
					$pageNum++;				
				}
			}
		}
		echo "resultant query summary : ".$query_state." \n ";
		if($query_state=='inactive')
		{
			$sql = "UPDATE query SET state='inactive' WHERE id='".$row['id']."'";
			$retval = mysql_query( $sql, $conn) or mysql_error();
			echo " \n set state : ".$retval." \n ";
		}
	}
	fclose($fh1);
	$fh				= fopen($base_url_magento.'amazon_import_products/csv_file/main_categories.txt', 'w');
	fwrite($fh,'False');
	fclose($fh);
	
	echo gmdate('Y-m-d H:i:s')."----> Total csv files: ".$file_count." \n";
	echo gmdate('Y-m-d H:i:s')."----> Main Category Product Import Completed \n";
	
?>