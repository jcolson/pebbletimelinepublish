<?php
add_action('admin_menu', 'xyz_ptp_menu');

function xyz_ptp_add_admin_scripts()
{
	wp_enqueue_script('jquery');

	wp_register_script( 'xyz_notice_script', plugins_url('pebble-timeline-publish/js/notice.js') );
	wp_enqueue_script( 'xyz_notice_script' );

	wp_register_style('xyz_ptp_style', plugins_url('pebble-timeline-publish/admin/style.css'));
	wp_enqueue_style('xyz_ptp_style');
}

add_action("admin_enqueue_scripts","xyz_ptp_add_admin_scripts");



function xyz_ptp_menu()
{
	add_menu_page('Pebble Timeline Publish - Manage settings', 'Pebble Timeline Publish', 'manage_options', 'pebble-timeline-publish-settings', 'xyz_ptp_settings');
	$page=add_submenu_page('pebble-timeline-publish-settings', 'Pebble Timeline Publish - Manage settings', ' Settings', 'manage_options', 'pebble-timeline-publish-settings' ,'xyz_ptp_settings'); // 8 for admin
	add_submenu_page('pebble-timeline-publish-settings', 'Pebble Timeline Publish - Logs', 'Logs', 'manage_options', 'pebble-timeline-publish-log' ,'xyz_ptp_logs');
	add_submenu_page('pebble-timeline-publish-settings', 'Pebble Timeline Publish - About', 'About', 'manage_options', 'pebble-timeline-publish-about' ,'xyz_ptp_about'); // 8 for admin
}


function xyz_ptp_settings()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$_POST = xyz_trim_deep($_POST);
	$_GET = xyz_trim_deep($_GET);

	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/settings.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}



function xyz_ptp_about()
{
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/about.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}


function xyz_ptp_logs()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$_POST = xyz_trim_deep($_POST);
	$_GET = xyz_trim_deep($_GET);

	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/logs.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}

?>
