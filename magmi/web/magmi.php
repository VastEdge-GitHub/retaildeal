<head>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="-1" />
<?php
//LIKI Code Start
//Reason of Change: Authorized user can access with username and password
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Expires: Fri, 06 Jun 1975 15:10:00 GMT');
session_start();
if(!isset($_SESSION['magmi_admin']) || !isset($_SESSION['magmi_status']) || $_SESSION['magmi_admin']=="" || $_SESSION['magmi_status']=="")
{ 
	header("Location: /magmi/web/admin_login.php");
}
if(isset($_SESSION['magmi_admin']) && isset($_SESSION['magmi_status']))
{
echo '<div style="width:940px;margin:5px auto; height:30px; font-size:19px">
		<a href="logout.php"><div style="float:right;"><input type="button" value="Logout"  style="float: left; cursor:pointer; background-color:#8888cc;color:white;"></div></a>
		<div style="float:right;color:#000;margin:3px 10px 0 0">Welcome : '.$_SESSION['magmi_admin'].'</div>		
	</div>';
//LIKI Code End	
header('Pragma: public'); // required
//header('Expires: -1'); // no cache
//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
//header('Cache-Control: private', false);
require_once ("header.php");
require_once ("magmi_config.php");
require_once ("magmi_statemanager.php");
require_once ("fshelper.php");
require_once ("magmi_web_utils.php");
$badrights = array();
$postinst = "../inc/magmi_postinstall.php";
if (file_exists($postinst))
{
    require_once ("$postinst");
    if (function_exists("magmi_post_install"))
    {
        $result = magmi_post_install();
        
        if ($result["OK"] != "")
        {
            ?>
<div class="container_12">
	<div class="grid_12 subtitle">
		<span>Post install procedure</span>
	</div>
	<div class="grid_12 col">
		<h3>Post install output</h3>
		<div class="mgupload_info" style="margin-top: 5px">
	 <?php echo $result["OK"]?>
	 </div>
	</div>
</div>
<?php
        }
        rename($postinst, $postinst . "." . strval(time()));
    }
}
foreach (array("../state","../conf","../plugins") as $dirname)
{
    if (!FSHelper::isDirWritable($dirname))
    {
        $badrights[] = $dirname;
    }
}
if (count($badrights) == 0)
{
    $state = Magmi_StateManager::getState();
    
    if ($state == "running" || (isset($_REQUEST["run"]) && $_REQUEST["run"] == "import"))
    {
        require_once ("magmi_import_run.php");
    }
    else
    {
        Magmi_StateManager::setState("idle", true);
        require_once ("magmi_config_setup.php");
        require_once ("magmi_profile_config.php");
    }
}
else
{
    ?>

<div class="container_12">
	<div class="grid_12">
		<div class="magmi_error" style="margin-top: 5px">
			Directory permissions not compatible with Mass Importer operations
			<ul>
		<?php
    
    foreach ($badrights as $dirname)
    {
        $trname = str_replace("..", "magmi", $dirname);
        ?>
			<li><?php echo $trname?> not writable!</li>
		<?php }?>
		</ul>
		</div>
	</div>
</div>
<?php
}
?>
<!--Changes by LIKI Ext Start-->
<!--Reason of Change: In safari browser, when we did login then after logout, if we clicked on back button of safari browser then it was not redirected to login screen.-->
<script>
window.onpageshow = function(event) {
    if (event.persisted) {
      window.location.reload();
    }
};
</script>
<!--Changes by LIKI Ext End-->
<?php require_once("footer.php");?>
<div id="overlay" style="display: none">
	<div id="overlaycontent"></div>
</div> 
<?php 
//LIKI Code Start
}
//LIKI Code End
 ?>
