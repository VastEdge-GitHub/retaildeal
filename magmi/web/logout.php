<?php
session_start();
if(isset($_SESSION['magmi_admin']) || isset($_SESSION['magmi_status']))
{	
	unset($_SESSION['magmi_admin']);
	unset($_SESSION['magmi_status']);
<<<<<<< HEAD
	header("Location: admin_login.php");
exit(0);
}
 ?>
  <head>
  <META http-equiv="refresh" content="1;URL=http://www.retaildeal.biz/magmi/web/admin_login.php">
=======
	//ob_end_flush();
	//header("Location: admin_login.php");
//exit(0);
}
 ?>
  <head>
 <!-- Changes by LIKI Ext Start-->
<!-- Reason of Change: After logout, It was not redirecting to http://54.68.111.240/magmi/web/admin_login.php -->
  <META http-equiv="refresh" content="1;URL=http://54.68.111.240/magmi/web/admin_login.php">
  <!--Changes by LIKI Ext End-->
>>>>>>> master

</head>
<body>Redirecting...</body>