<?php
session_start();
if(isset($_SESSION['magmi_admin']) || isset($_SESSION['magmi_status']))
{	
	unset($_SESSION['magmi_admin']);
	unset($_SESSION['magmi_status']);
	header("Location: admin_login.php");
exit(0);
}
 ?>
  <head>
  <META http-equiv="refresh" content="1;URL=http://www.retaildeal.biz/magmi/web/admin_login.php">

</head>
<body>Redirecting...</body>