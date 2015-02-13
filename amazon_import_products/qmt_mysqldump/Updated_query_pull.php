<?php
		$last_line = system('wget http://demo.retaildeal.biz/amazon_import_products/qmt_mysqldump/query.sql',$retval);
		if($retval==0)
		{
			$input='pwd';
			$output = shell_exec($input);
			echo "<pre>$output</pre>";
			$target = trim($output).'/query.sql';
			$input = 'mysql -ubn_magento -pfbeee979d3 rd_qmt < '.$target;
			echo $input;
			$output = shell_exec($input);
			$input = 'rm -rf '.$target;
			echo $input;
			if($output==''){ shell_exec($input);}
		}
?>
