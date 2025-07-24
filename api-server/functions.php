<?php
function db_connect($db)
{
	$username=getenv('DB_USER');
	$password=getenv('DB_PASS');
	$host=getenv('DB_HOST');

	$dblink=new mysqli($host,$username,$password,$db);
	return $dblink;
}

function redirect ($uri)
{ ?>
		<script type="text/javascript">
			document.location.href="<?php echo $uri; ?>";
		</script>
<?php die;
}
?>