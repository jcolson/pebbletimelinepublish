<?php

function ptp_free_network_destroy($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				ptp_free_destroy();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	ptp_free_destroy();
}

function ptp_free_destroy()
{
	global $wpdb;

	if(get_option('xyz_credit_link')=="ptp")
	{
		update_option("xyz_credit_link", '0');
	}

	delete_option('xyz_ptp_application_id');
	delete_option('xyz_ptp_application_secret');
	delete_option('xyz_ptp_fb_id');
	delete_option('xyz_ptp_message');
	delete_option('xyz_ptp_po_method');
	delete_option('xyz_ptp_post_permission');
	delete_option('xyz_ptp_current_appln_token');
	delete_option('xyz_ptp_af');
	delete_option('xyz_ptp_pebble_apikey');
	delete_option('xyz_ptp_pebble_topic');
	delete_option('xyz_ptp_pebble_time');
	delete_option('xyz_ptp_pages_ids');
	delete_option('xyz_ptp_future_to_publish');
	delete_option('xyz_ptp_apply_filters');

	delete_option('xyz_ptp_free_version');

	delete_option('xyz_ptp_include_pages');
	delete_option('xyz_ptp_include_posts');
	delete_option('xyz_ptp_include_categories');
	delete_option('xyz_ptp_include_customposttypes');
	delete_option('xyz_ptp_peer_verification');
	delete_option('xyz_ptp_post_logs');
	delete_option('xyz_twap_premium_version_ads');
	delete_option('xyz_ptp_default_selection_edit');
}

register_uninstall_hook(XYZ_ptp_PLUGIN_FILE,'ptp_free_network_destroy');


?>
