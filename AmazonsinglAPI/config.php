<?php
	$custom_hostname="localhost";
	$custom_username="bn_magento";
	$custom_password="fbeee979d3";
	$custom_dbName="rd_qmt";
	if($custom_conn=mysql_connect($custom_hostname,$custom_username,$custom_password)){}
	else{echo "Something went wrong. Unable to establish MySQL conection";}
	mysql_select_db($custom_dbName, $custom_conn);
?>