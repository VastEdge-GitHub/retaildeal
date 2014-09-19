<?php
session_start();
ob_start();
if(isset($_SESSION['magmi_admin']) || isset($_SESSION['magmi_status']))
{	
	unset($_SESSION['magmi_admin']);
	unset($_SESSION['magmi_status']);
	header("Location: /magmi/web/admin_login.php");
}
?>
