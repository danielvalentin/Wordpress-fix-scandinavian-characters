<?php
/**
 * Plugin name: Fix Danish Characters
 * Description: Fixes danish characters in slugs, filenames and usernames
 * Version: 1.0
 * Plugin URI: http://github.com/tosak/
 */
 
/**
 * Fixes filenames
 */
function fix_danish_filenames($vals)
{
	$name = array_reverse(explode('/', $vals['file']));
	$name = urldecode($name[0]);
	$url = str_replace($name, '', urldecode($vals['file']));
	$wurl = str_replace($name, '', urldecode($vals['url']));
	$name = str_replace('æ','ae',$name);
	$name = str_replace('ø','oe',$name);
	$name = str_replace('å','aa',$name);
	$name = str_replace('Æ','AE',$name);
	$name = str_replace('Ø','OE',$name);
	$name = str_replace('Å','AA',$name);
	$name = str_replace(' ','-',$name);
	
	if(@rename($vals['file'], $url . $name))
	{
		return array(
			'file' => $url . $name,
			'url' => $wurl . $name,
			'type' => $vals['type']
		);
	}
	return $vals;
}
add_action('wp_handle_upload', 'fix_danish_filenames');

/**
 * Fixes slugs
 */
function improved_sanitize_title($title)
{
	$title = str_replace('æ', 'ae', $title);
	$title = str_replace('ø', 'oe', $title);
	$title = str_replace('å', 'aa', $title);
	$title = str_replace('Æ', 'AE', $title);
	$title = str_replace('Ø', 'OE', $title);
	$title = str_replace('Å', 'AA', $title);
	return $title;
}
add_filter('sanitize_title', 'improved_sanitize_title', 0);

/**
 * Allow æøå in usernames
 * [From oneconsult.dk and bbPress]
 */
function wk_sanitize_user_mbstring( $raw_username, $username, $strict = false )
{
	mb_internal_encoding("UTF-8");
	mb_regex_encoding("UTF-8");

	$raw_username = $username;
	$username = strip_tags($username);

	$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
	$username = preg_replace('/&.+?;/', '', $username);
	if ( $strict )
		$username = mb_ereg_replace('|[^a-zíúáóðæøå0-9 _.\-@]|i', '', $username);

	return apply_filters('sanitize_user_mbstring', $username, $raw_username, $strict);
};
add_action('sanitize_user', 'wk_sanitize_user_mbstring', 0, 3);
