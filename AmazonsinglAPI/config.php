<?php
	$custom_hostname="localhost";
	$custom_username="bn_magento";
	$custom_password="fbeee979d3";
	$custom_dbName="bitnami_magento";
	if($custom_conn=mysql_connect($custom_hostname,$custom_username,$custom_password)){
		echo gmdate('Y-m-d H:i:s')."----> MySQL connection successful \n";
	}
	else{
		echo gmdate('Y-m-d H:i:s')."----> Unable to establish MySQL conection \n";
	}
	mysql_select_db($custom_dbName, $custom_conn);
?>