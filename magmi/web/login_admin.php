<?php
session_start();
	$custom_hostname="localhost";
	$custom_username="bn_magento";
	$custom_password="fbeee979d3";
	$custom_dbName="bitnami_magento";
	if($custom_conn=mysql_connect($custom_hostname,$custom_username,$custom_password))
	{
		mysql_select_db($custom_dbName, $custom_conn);
		$uname=trim($_POST['un']);
		$pswd=trim($_POST['pw']);
		$sql = "SELECT * FROM `custom_magmi_user` WHERE `username`='rdadmin' and `password`='newinstance123$'";
		$result=mysql_query($sql);
		$data='0';
		while($row = mysql_fetch_array($result))
		{
			if($row['username']==$uname && $row['password']==$pswd)
			{
				$data='1';
				$_SESSION['magmi_admin']=$row['username'];
				$_SESSION['magmi_status']=$data;
			}
		}
			echo $data;
	}
	else
	{
		echo "could not connect to database";
	}
?>