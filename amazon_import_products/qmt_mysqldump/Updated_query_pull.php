<?php
	$last_line = system('wget http://demo.retaildeal.biz/amazon_import_products/qmt_mysqldump/query.sql',$retval);
	if($retval==0)
	{
		$last_line = system('mysql -ubn_magento -pfbeee979d3 rd_qmt < /opt/bitnami/apps/magento/htdocs/amazon_import_products/qmt_mysqldump/query.sql',$retval);
		if($retval==0)
		{
			system('rm -rf /opt/bitnami/apps/magento/htdocs/amazon_import_products/qmt_mysqldump/query.sql',$retval);
		}
	}
?>
