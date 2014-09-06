<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <title>Retaildeal - Admin Panel</title>
  <style>

body{
	margin: 0;
	padding: 0;
	background: #fff;

	color: #fff;
	font-family: Arial;
	font-size: 12px;
}

.body{
	position: absolute;
	top: -20px;
	left: -20px;
	right: -40px;
	bottom: -40px;
	width: auto;
	height: auto;
	/*background-image: url(file:///C|/xampp/htdocs/images/bg.jpg);*/
	background-size: cover;
	z-index: 0;
}

/*.grad{
	position: absolute;
	top: -20px;
	left: -20px;
	right: -40px;
	bottom: -40px;
	width: auto;
	height: auto;
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,0,0,0)), color-stop(100%,rgba(0,0,0,0.65))); /* Chrome,Safari4+ */
/*	z-index: 1;
	opacity: 0.7;
}
*/
.header{
	position: absolute;
	top: calc(50% - 165px);
	left: calc(50% - 145px);
	z-index: 2;
}

.header div span{
	color: #5379fa !important;
}

.login{
	position: absolute;
	top: calc(50% - 75px);
	left: calc(50% - 50px);
	height: 260px;
	width: 350px;
	padding: 10px;
	z-index: 2;
}

.login input[type=text]{
	width: 250px;
	height: 30px;
	background: transparent;
	border: 1px solid rgba(1, 50, 175, 0.6);
	border-radius: 2px;
	font-size: 16px;
	font-weight: 400;
	padding: 4px;
}

.login input[type=password]{
	width: 250px;
	height: 30px;
	background: transparent;
	border: 1px solid rgba(1, 50, 175, 0.6);
	border-radius: 2px;
	font-size: 16px;
	font-weight: 400;
	padding: 4px;
	margin-top: 10px;
}

.login input[type=button]{
	width: 90px;
	height: 35px;
	background: #fff;
	border: 1px solid #fff;
	cursor: pointer;
	border-radius: 2px;
	color: #a18d6c;
	font-family: 'Exo', sans-serif;
	font-size: 16px;
	font-weight: 400;
	padding: 6px;
	margin-top: 8px;
	mafgin-left: 79px;
}

.login input[type=button]:hover{
	opacity: 0.8;
}

.login input[type=button]:active{
	opacity: 0.6;
}

.login input[type=text]:focus{
	outline: none;
	border: 1px solid rgba(1, 50, 175, 0.9);
}

.login input[type=password]:focus{
	outline: none;
	border: 1px solid rgba(1, 50, 175, 0.9);
}

.login input[type=button]:focus{
	outline: none;
}

::-webkit-input-placeholder{
   color: rgba(1, 50, 175, 0.6);
}

::-moz-input-placeholder{
   color: rgba(1, 50, 175, 0.6);
}
</style>
<script src="js/jquery.min.js" type="text/javascript"></script>
</head>
<body>
	<div class="body"></div>
	<div class="login" style="background: #fff;opacity: 0.7;border: 1px solid rgba(34, 34, 34, 0.6);border-radius: 4%;margin-left: -120px; margin-top: -100px;">
		<div class="header" style="margin-top: 20px;">
			<div class="header_logo" >
				<a href=""><img src="images/retail-deal_Logo.png" alt="RetailDeal: Rent-to-Own the Largest selection of products"></a>
			</div>
		</div>
		<script type="text/javascript">
		function login_check()
		{
			var un = document.getElementById("user").value;
			un = un.replace(/^\s+|\s+$/g,'');
			var pw = document.getElementById("password").value;
			pw = pw.replace(/^\s+|\s+$/g,'');
			if(un == "" || pw == "")
			{
				alert('Username or Password can not be left blank.');
				return false;
			}
			dataString = '&un=' + un  + '&pw=' + pw ;
			//alert(un+'|||'+pw);
			$.ajax({
				type: 'POST',
				url: 'login_admin.php',
				data: dataString,
				dataType: 'html',
				success: function(data)
				{
					//alert(data);
					data = data.replace(/^\s+|\s+$/g,'');
					//alert('abc');
					//alert(data);
					if(data == '0')
					{
						alert('Either Username or Password is incorrect.');
						return false;
					}
					else if(data == '1')
					{
						<?php //$status=1; ?> 
						<?php //header("Location:magmi.php?status=".$status);  
						//  header("Location:magmi.php?status=1"); ?>
						window.location="http://www.retaildeal.biz/magmi/web/magmi.php";				
					}
					else if(data == '2')
					{
						
					}
				}
			})
		}
		
		</script>
		<div style="margin-top: 90px; margin-left: 44px;">
			<form name="admin_login" class="" action=""  method="post">
				<p class="">
					<input type="text" placeholder="username" name="user" id="user" onFocus=""><br>
				</p>
				<p class="">
					<input type="password" placeholder="password" name="password" id="password" onFocus=""><br>
				</p>
					<input type="button" onClick="return login_check();" value="Login" style="float: left; margin-right: 46px; margin-top: 8px;margin-left:79px;background-color:#FF8E1B;color:white;">
			</form>
		</div>
		<div><span style="font-weight:lighter;color:#ff0000;margin-left: 59px; position: absolute;" id="err_msg"></span></div>
	</div>
</body>
</html>