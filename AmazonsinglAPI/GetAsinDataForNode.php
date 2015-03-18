<?php
	$sku = explode(",",$_GET['asin']);
    /* Example usage of the Amazon Product Advertising API */
    include("amazon_api_class.php");
	include("amazon_parse_xml.php");
	include("config.php");
	ini_set('default_charset', 'utf-8');
 	header('Content-Type: text/html; charset=utf-8'); 
    $obj = new AmazonProductAPI();
    $base_url_magento = '/home/retail/public_html/';
	$count_error=0;
	for($i=0; $i< count($sku)-1; $i++){
		echo "Result for : ". $sku[$i]. "<br />";
		echo "<hr /><br />";
		$chk_exception='';
		
		try 
		{
			$result 					= $obj->searchProducts($sku[$i]);
			print_r (htmlspecialchars(json_encode($result)));	
		}
		catch(Exception $e)
		{
			$chk_exception = $e->getMessage();
			echo "There is an error while getting data from Amazon for this ASIN (".$sku[$i].").<br >";
			echo "Description for exception is : " . $chk_exception;
		}
		echo "<br><hr ><br /><br />";
	}
?>
