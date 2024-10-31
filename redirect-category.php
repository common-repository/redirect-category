<?php

/*
Plugin Name: Redirect Category
Plugin URI: http://www.budhiman.com/wordpress-plugin/redirect-category.html
Description: Very straightforward and intuitive way to redirect your posts based on category to another website.
Version: 0.2
License: GPL
Author: Budhiman
Author URI: http://www.budhiman.com/


Copyright 2011 Budhiman (email: contact@budhiman.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//error_reporting(E_ALL);
define('REDIRCAT_PLUGIN_NAME', 'redirect-category');
define('REDIRCAT_PLUGIN_DIR', WP_PLUGIN_DIR . '/'.REDIRCAT_PLUGIN_NAME.'/');
define('REDIRCAT_PLUGIN_FILE', WP_PLUGIN_DIR . '/'.REDIRCAT_PLUGIN_NAME.'/'.REDIRCAT_PLUGIN_NAME.'.php');
define('REDIRCAT_PLUGIN_BASENAME', plugin_basename(REDIRCAT_PLUGIN_FILE));


if (isset($_POST['redirect-category-action'])) {

	$redirectcategory_updated = false;
	
	$redirectcategory_enable = $_POST['redirectcategory_enable'];
	$redirectcategory_protocol = $_POST['redirectcategory_protocol'];
	$redirectcategory_destination_domain = $_POST['redirectcategory_destination_domain'];
	
	$siteurl = get_option('siteurl');
	$siteurl = str_replace('http://', '', $siteurl);
	$siteurl = str_replace('https://', '', $siteurl);
	$a = explode('/', $siteurl);
	$current_domain = $a[0];
	$redirectcategory_loop_redirection = false;
	if ($current_domain == $redirectcategory_destination_domain) { $redirectcategory_loop_redirection = true; }
	
	// maybe needed for testing domains in local network
	if (isset($_POST['redirectcategory_no_domain_validation'])) {
		$valid_redirectcategory_destination_domain = true;
		$redirectcategory_no_domain_validation = 1;
	}
	else {
		$valid_redirectcategory_destination_domain = false;	
		$redirectcategory_no_domain_validation = 0;
		$re = '/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/';
		if (preg_match($re, $redirectcategory_destination_domain, $matches)) { $valid_redirectcategory_destination_domain = true; }
	}
	
	$redirectcategory_categories = array();
	if (isset($_POST['redirectcategory_categories'])) { $redirectcategory_categories = $_POST['redirectcategory_categories']; }	
	
	if ($valid_redirectcategory_destination_domain && !$redirectcategory_loop_redirection) {
		update_option('redirectcategory_protocol', $redirectcategory_protocol);
		update_option('redirectcategory_categories', serialize($redirectcategory_categories));
		update_option('redirectcategory_destination_domain', $redirectcategory_destination_domain);
		update_option('redirectcategory_enable', $redirectcategory_enable);
		update_option('redirectcategory_no_domain_validation', $redirectcategory_no_domain_validation);
		$redirectcategory_updated = true;
	}
	
	// hook the admin notices action
	add_action( 'admin_notices', array('RedirectCategory', 'notice_update'), 9 );	
}

class RedirectCategory {

	/* Plugin options page */

	function admin_init() { wp_register_style( 'redirect-category.style.admin', WP_PLUGIN_URL . '/redirect-category/style.admin.css' ); }
   
	function admin_menu() { 
   
		$page = add_options_page('Redirect Category', 'Redirect Category', 'administrator', REDIRCAT_PLUGIN_NAME, array('RedirectCategory', 'options_menu'));
	    
		// add admin css   
		add_action( 'admin_print_styles-' . $page, array('RedirectCategory', 'admin_styles' ));	
	}
   
	function admin_styles() { wp_enqueue_style( 'redirect-category.style.admin' ); }   

	function options_menu() { require_once(REDIRCAT_PLUGIN_DIR . 'inc/admin.php'); }

	function notice_update() {
		
		global $redirectcategory_updated, $valid_redirectcategory_destination_domain, $redirectcategory_loop_redirection;
		
		if ($redirectcategory_updated) {
			$redirectcategory_enable = get_option('redirectcategory_enable');
			if ($redirectcategory_enable) echo "<div class = 'updated'><p>" . __("Redirection options <b>updated</b>.", REDIRCAT_PLUGIN_NAME) ."</p></div>";
		}
		
		if (!$valid_redirectcategory_destination_domain) {
			echo "<div class = 'error'><p>" . __("Invalid destination domain name. Redirect Category won't be turned on without a valid domain name.", REDIRCAT_PLUGIN_NAME) ."</p></div>";
		}
		if ($redirectcategory_loop_redirection) {
			echo "<div class = 'error'><p>" . __("You can't redirect to the same domain. Redirect Category not turned on.", REDIRCAT_PLUGIN_NAME) ."</p></div>";
		}		
		
	} 
	
	function settings_link($links, $file) {
		
		if ($file == REDIRCAT_PLUGIN_BASENAME) {			
			$settings_link = '<a href="options-general.php?page='.REDIRCAT_PLUGIN_NAME.'">'.__('Settings').'</a>';
			array_unshift( $links, $settings_link ); // before other links			
		}
		
		return $links;	
	}
	
	// On plugin activation
	function on_activation() {
		
		add_option('redirectcategory_enable', 0, '', 'yes');
		add_option('redirectcategory_protocol', 'http', '', 'yes');
		add_option('redirectcategory_destination_domain', '', '', 'yes');
		add_option('redirectcategory_no_domain_validation', '', '', 'yes');
		add_option('redirectcategory_categories', serialize(array()), '', 'yes');
	}
	
	// On plugin deactivation
	function on_deactivation() {
		
		delete_option('redirectcategory_enable');
		delete_option('redirectcategory_protocol');
		delete_option('redirectcategory_destination_domain');
		delete_option('redirectcategory_no_domain_validation');
		delete_option('redirectcategory_categories');
	}
	
	function redirect() {
		
		global $wp;		
		$protocol = get_option('redirectcategory_protocol');
		$destination_domain = get_option('redirectcategory_destination_domain');		
		$path = $_SERVER['REQUEST_URI'];		
		$url = $protocol . '://' . $destination_domain . $path;
		
		//die($url);
		
		// use wp_redirect($url, 301);?
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $url);
		exit();
	}	
	
	function redirect_candidate() {		
		
		global $wp, $wpdb;
		
		// bother only if no error was encountered
		if (!isset($wp->query_vars['error'])) {
			
			$post_id = null;
			if (isset($wp->query_vars['p'])) $post_id = $wp->query_vars['p'];
			else if (isset($wp->query_vars['page_id'])) $post_id = $wp->query_vars['page_id'];
			else {
				
				// bother only if it is a post; pages will get past this condition
				if (isset($wp->query_vars['name'])) {
				
					$post_name = $wp->query_vars['name'];
								
					$query = 'SELECT id FROM ' . $wpdb->posts . ' WHERE post_name="' . $post_name . '"';					
					$myrows = $wpdb->get_results($query);					
					
					$post_id = $myrows[0]->id;
				}
			}
			
			// see if the post category is to be redirected			
			if ($post_id) {
				
				$redirectcategory_categories = unserialize(get_option('redirectcategory_categories'));				

				// if some category is set for redirection
				if ($redirectcategory_categories && count($redirectcategory_categories) > 0) {
					$query = "SELECT {$wpdb->term_taxonomy}.term_id as id 
								FROM {$wpdb->term_taxonomy}, {$wpdb->term_relationships} 
								WHERE {$wpdb->term_relationships}.object_id=$post_id AND 
								{$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id AND 
								{$wpdb->term_taxonomy}.taxonomy = 'category'";
					//echo ($query);
					$myrows = $wpdb->get_results($query);
					
					foreach ($myrows as $row => $cat) {
						
						if (in_array($cat->id, $redirectcategory_categories)) {
							RedirectCategory::redirect();
							break;
						}			
					}
				}
			}
		}
		
	}
	
	
}


// let's catch those posts that need to be redirected
if (get_option('redirectcategory_enable')) add_action('send_headers', array('RedirectCategory', 'redirect_candidate'));

	// plugin options
add_action('admin_init', array('RedirectCategory', 'admin_init'));
add_action('admin_menu', array('RedirectCategory', 'admin_menu'));
add_filter('plugin_action_links', array('RedirectCategory', 'settings_link'), 10, 2);

// Activation / deactivation
register_activation_hook(REDIRCAT_PLUGIN_FILE, array('RedirectCategory', 'on_activation'));
register_deactivation_hook(REDIRCAT_PLUGIN_FILE, array('RedirectCategory', 'on_deactivation'));
