<?php
function ptp_free_network_install($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				ptp_install_free();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	ptp_install_free();
}

function ptp_install_free()
{
	/*$pluginName = 'xyz-wp-smap/xyz-wp-smap.php';
	if (is_plugin_active($pluginName)) {
		wp_die( "The plugin Pebble Timeline Publish cannot be activated unless the premium version of this plugin is deactivated. Back to <a href='".admin_url()."plugins.php'>Plugin Installation</a>." );
	}*/

	global $current_user;
	get_currentuserinfo();
	if(get_option('xyz_credit_link')=="")
	{
		add_option("xyz_credit_link", '0');
	}

	$ptp_installed_date = get_option('ptp_installed_date');
	if ($ptp_installed_date=="") {
		$ptp_installed_date = time();
		update_option('ptp_installed_date', $ptp_installed_date);
	}

	add_option('xyz_ptp_application_id','');
	add_option('xyz_ptp_application_secret', '');
	add_option('xyz_ptp_fb_id', '');
	add_option('xyz_ptp_message', 'New post added at {BLOG_TITLE} - {POST_TITLE}');
 	add_option('xyz_ptp_po_method', '2');
	add_option('xyz_ptp_post_permission', '1');
	add_option('xyz_ptp_current_appln_token', '');
	add_option('xyz_ptp_af', '1'); //authorization flag
	add_option('xyz_ptp_pebble_apikey', ''); //pebble api key
	add_option('xyz_ptp_pebble_topic', 'WOD'); //pebble topic to post to
	add_option('xyz_ptp_pebble_time', '11'); // time for event to show up on timeline the next day
	add_option('xyz_ptp_pages_ids','-1');
	add_option('xyz_ptp_future_to_publish', '1');
	add_option('xyz_ptp_apply_filters', '');


	$version=get_option('xyz_ptp_free_version');
	$currentversion=xyz_ptp_plugin_get_version();
	update_option('xyz_ptp_free_version', $currentversion);

	add_option('xyz_ptp_include_pages', '0');
	add_option('xyz_ptp_include_posts', '1');
	add_option('xyz_ptp_include_categories', 'All');
	add_option('xyz_ptp_include_customposttypes', '');

	add_option('xyz_ptp_peer_verification', '1');
	add_option('xyz_ptp_post_logs', '');
	add_option('xyz_ptp_premium_version_ads', '1');
	add_option('xyz_ptp_default_selection_edit', '0');

}


register_activation_hook(XYZ_ptp_PLUGIN_FILE,'ptp_free_network_install');
?>
