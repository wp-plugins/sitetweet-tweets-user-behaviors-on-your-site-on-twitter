<?PHP
/*
Plugin Name: sitetweet
Version: 0.2
Plugin URI: http://sitetweet.net/wordpress
Description: Wordpress plugin for <a href=http://sitetweet.net>sitetweet</a>.
Author: Phillip K
Author URI: http://siteweet.net/
*/

function sitetweet_install()
{
    global $wpdb;
    $table = $wpdb->prefix."sitetweet";
    $structure = "CREATE TABLE $table (
        api_code VARCHAR(250) NOT NULL,
        show_country INT NOT NULL DEFAULT 0
    );";
    $wpdb->query($structure);
    $structure = "INSERT INTO $table (api_code) VALUES ('');";
    $wpdb->query($structure);
}

function sitetweet_uninstall()
{
    global $wpdb;
    $table = $wpdb->prefix."sitetweet";
    $structure = "DROP TABLE $table;";
    $wpdb->query($structure);
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function sitetweet($thecode) {
	global $post;
	global $wpdb;


	if (get_post_meta($post->ID, 'sitetweet', true) != '') {

		$newcode = '<script type="text/javascript">'.chr(13);
		$newcode = $newcode.'//<![CDATA[['.chr(13);

		$st_api_id = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sitetweet");
		foreach($st_api_id as $result)
		{
		    $api_code = $result->api_code;
			if ($result->show_country == 1)
			    $tweet_country = "TRUE";
		}

		$newcode = $newcode.'var __sitetweet_id="'.urlencode($api_code).'";'.chr(13);

		$newcode = $newcode.'var __sitetweet_country="'.$tweet_country.'";'.chr(13);

		if (strtoupper(get_post_meta($post->ID, 'sitetweet', true)) == 'DIRECTORY') {
			$tweetmsg = "User just visited ".$_SERVER['REQUEST_URI']. " on ".$_SERVER["SERVER_NAME"];
		} else if (strtoupper(get_post_meta($post->ID, 'sitetweet', true)) == 'URL') {
			$tweetmsg = "User just visited ".curPageURL();
		} else 
		{
			$tweetmsg = get_post_meta($post->ID, 'sitetweet', true);
		}

		$newcode = $newcode.'var __sitetweet_message="'.$tweetmsg.'";'.chr(13);
		$newcode = $newcode.'document.write(unescape("%3Cimg src=\'http://bot.sitetweet.net/tweet/?api_id=" + __sitetweet_id + "&country=" + __sitetweet_country + "&msg=" + __sitetweet_message +"\' width=\'1\' height=\'1\' border=\'0\'%3E"));'.chr(13);
		$newcode = $newcode.'//]]>'.chr(13);
		$newcode = $newcode.'</script>';
		return $thecode.$newcode;
	} else {
		return $thecode;
	}
}

function sitetweet_menu()
{
    global $wpdb;
    include 'sitetweet-admin.php';
}
 
function sitetweet_admin_actions()
{
    add_options_page("sitetweet", "sitetweet", 1,"sitetweet", "sitetweet_menu");
}
 
add_action('admin_menu', 'sitetweet_admin_actions');


add_filter('the_content', 'sitetweet');

add_action('activate_sitetweet-tweets-user-behaviors-on-your-site-on-twitter/sitetweet.php', 'sitetweet_install');

register_deactivation_hook('sitetweet-tweets-user-behaviors-on-your-site-on-twitter/sitetweet.php', 'sitetweet_uninstall' );


?>