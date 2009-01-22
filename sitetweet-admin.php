<div class="wrap">
<h2>sitetweet Admin</h2>

<?php
$buffer = "error";
$input_css = "";
$error_msg = "";
$tweet_country = "";

if ($_GET['is_api_code_update'] != "")
{
	if ($_GET['use_country'] != "")
		$tweet_country = ", show_country = 1";
	else 
		$tweet_country = ", show_country = 0";
		
	$wpdb->query("UPDATE ".$wpdb->prefix."sitetweet SET api_code = '".$_GET['api_code']."'".$tweet_country);
}

$stcode = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sitetweet");
foreach($stcode as $result)
{
    $api_code = $result->api_code;
    $tweet_country = $result->show_country;
}
if ($tweet_country == 1)
	$tweet_country = " checked";
else
	$tweet_country = "";

$url = "http://sitetweet.net/api_checkkey/?api=".$api_code;
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "$url");
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$buffer = TRIM(curl_exec($curl_handle));
curl_close($curl_handle);

if ($_GET['is_api_code_update'] != "")
{
	if ($buffer == "error")
		echo '<div id="message" class="updated fade"><strong>Incorrect API key</strong> - Please try again</div>';
	else 
		echo '<div id="message" class="updated fade">siteweet configuration <strong>updated</strong>.</div>';
}

if ($buffer == "error")
{ 
	if (TRIM($api_code) != "")
	{
		$input_css = ' style="border-style:solid;border-width:2;border-color:red;"';
		$error_msg = '<span style="color:red;">Incorrect API KEY</span>';	
	}
?>
<br>
To get your API Key, visit your <a href="http://sitetweet.net/profile" target="_blank">sitetweet's user account page</a>.  
<br><br>
If you do not have a sitetweet account, <a href="http://sitetweet.net/newuser" target="_blank">sign-up now</a>.
<br><br>
<hr>

<?php
} else {

echo "<br><br>".$buffer."<br><br>";

}
?>
<form name="sitetweet" action="<?php echo SUBSTR($_SERVER['REQUEST_URI'],0,STRPOS($_SERVER['REQUEST_URI'], "?")); ?>" method="GET">
<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
<input type="hidden" name="is_api_code_update" value="1">


Your API key: <input type="text" name="api_code" size="50" value="<?php echo $api_code; ?>" <?php echo $input_css ?>> <?php echo $error_msg ?>
<br><br><input type="checkbox" name="use_country" <?php echo $tweet_country ?>> Tweet GEO-IP country information (if available)
<br><br><input type="submit" value="UPDATE">


</form>
</div>