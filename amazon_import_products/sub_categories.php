<?php
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", -1);
	ini_set('display_errors', 1);	
    error_reporting(E_ALL | E_STRICT);   
	ob_implicit_flush(true);

	global $base_url_magento;
	$base_url_magento = '/opt/bitnami/apps/magento/htdocs/';
	echo gmdate('Y-m-d H:i:s')."----> Sub Category Product Import Started \n";
    include($base_url_magento."amazon_import_products/amazon_api_class.php");
    include($base_url_magento."amazon_import_products/amazon_parse_xml.php"); 
	include($base_url_magento."app/Mage.php");
	global $chk_exception;	

    $obj				= new AmazonProductAPI();
	
	echo gmdate('Y-m-d H:i:s')."----> Loop for sub categories started \n";
												
	$fh				= fopen($base_url_magento.'amazon_import_products/csv_file/sub_categories.txt', 'w');
	fwrite($fh,'True');
	fclose($fh);
	
	$cat_array_namings		= array("Appliances","Automotive","Baby","PCHardware","Electronics","HealthPersonalCare","HomeGarden","PetSupplies","SportingGoods","Toys","Jewelry","Beauty","Kitchen","OutdoorLiving","Photo","Tools","Watches","Wireless","WirelessAccessories");		// Amazon Categories
	$cat_id_arr_ids			= array("3559","3560","3561","3562","3564","3565","3566","3567","3568","3569","3570","3565","3566","3566","3564","3559","3564","3564","3564","3697");																		
	
	
	// Magento category IDs
	$cat_array				= array();
	$browsenodes_array		= array();
	$cat_id_arr				= array();
	$sub_cat_name			= array();
	$file_count				= 0;
	
	echo gmdate('Y-m-d H:i:s')."----> Getting magento sub categories \n";
												
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
	$sql = "select * from query where parent_id!=2 and state='active'";
	
	mysql_select_db('rd_qmt');
	$retval = mysql_query( $sql, $conn );
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
		fwrite($fh1,$row['nodeid']."|".$row['categoryid']."|".$row['categoryname']."|".$row['manufacturer']."|".$row['brands']."|".$row['keywords']."|".$row['nodename']."|".$row['id'].PHP_EOL);
		echo $row['nodeid']."|".$row['categoryid']."|".$row['categoryname']."|".$row['manufacturer']."|".$row['brands']."|".$row['keywords']."|".$row['nodename']." <br> ";	
		$nodeid 		= $row['nodeid'];
		$categoryid		= $row['categoryid'];
		$parentid		= $row['parent_id'];
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
			echo gmdate('Y-m-d H:i:s')."----> CSV processing started \n";
			$count_error	= '0';
			$pageNum		= 1;
			$lastPage 		= 10;
			while($pageNum <= $lastPage)
			{
				$chk_exception		= '';

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Amazon Import Started for Title = ".$categoryname." || Page No. = ".$pageNum." || Min Price = ".$minPrice." \n";
	/////*****\\\\\
												
				try
				{
					$result = $obj->searchProducts($nodeid,$nodename,"BrowseNode",$pageNum,$manufacturer,$brands,$keyowrds,$minPrice,$maxPrice,$nodename);
					 if (isset($result->Items->Item->ItemAttributes->Title)){$query_state='active'; echo " \n ".$query_state." activated \n ";}
				}
				catch(Exception $e)
				{
					$chk_exception = $e->getMessage();
					
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> ".$chk_exception ." for: \n";
	echo "Error Count - ".$count_error." || BN ID - ".$nodeid." || Cat Name - ".$nodename." || Page No. - ".$pageNum." || Min Price - ".$minPrice." || Max Price - ".$maxPrice."++++++++++++ \n";
	/////*****\\\\\
										
					if($chk_exception == 'Invalid xml response.')
					{
						$count_error++;
						if($count_error > 1)
						{
							//deleterow($file_name,$a);
							//$fh_zero_result = fopen($base_url_magento.'amazon_import_products/zero_result.csv', 'a');
							//fwrite($fh_zero_result,$browsenodes_array[$n]."|".$cat_array[$n]."|".$pageNum."|".$manufacturer."|".$keywords."|".$minPrice."|".$maxPrice."|".$sub_cat_name[$n]."|".$csv_filename."|".$content."".PHP_EOL);
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
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> Amazon Import Completed \n";
	/////*****\\\\\
				
				if($chk_exception != ''){}
				else
				{
	
	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> XML to CSV Started \n";
	/////*****\\\\\
	
					$csv_filename				= $nodename."@".$parentid.",".$categoryid."@MainLoop".$row['id']."@SubLoop".$l."@manufacturer".trim($manufacturer)."@brand".trim($brands)."@keywords".trim($keyowrds)."@PageNum".$pageNum.".csv"; // xml to csv file name
	$csv_filename	= preg_replace('/\s+/', '', $csv_filename);

	/////*****\\\\\
	echo gmdate('Y-m-d H:i:s')."----> CSV File: ".$csv_filename." \n";
	/////*****\\\\\
	
					$csv_file					= $base_url_magento."amazon_import_products/csv_file/".$csv_filename;		// xml to csv file full path
					$fileconversion_response	= xml_to_csv_conversion($result,$csv_file);
					echo gmdate('Y-m-d H:i:s')."----> NO of Result in a query: ".$fileconversion_response." \n";
					/*if($fileconversion_response>10 || $fileconversion_response!=10){
					$fh_morethan_ten = fopen($base_url_magento.'amazon_import_products/greater_than_ten_result.csv', 'a');
					fwrite($fh_morethan_ten,$browsenodes_array[$n]."|".$cat_array[$n]."|".$pageNum."|".$manufacturer."|".$keywords."|".$minPrice."|".$maxPrice."|".$sub_cat_name[$n]."|".$fileconversion_response."|".$csv_filename."".PHP_EOL);}*/
					$file_count++;
					$count++;
	
	/////*****\\\\\	
	echo gmdate('Y-m-d H:i:s')."----> XML to CSV Completed \n";
	/////*****\\\\\
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
	$fh	= fopen($base_url_magento.'amazon_import_products/csv_file/sub_categories.txt', 'w');
	fwrite($fh,'False');
	fclose($fh);
	
	echo gmdate('Y-m-d H:i:s')."----> Total csv files: ".$file_count." \n";
	echo gmdate('Y-m-d H:i:s')."----> Sub Category Product Import Completed \n";
?>