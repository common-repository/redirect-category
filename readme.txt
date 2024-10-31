=== Redirect Category ===
Contributors: budhiman 
Tags: redirect, redirection, post, category, categories, posts
Requires at least: 3.1
Tested up to: 3.1.1
Stable tag: 0.2
	
Very straightforward and intuitive way to redirect your posts based on category to another website.

== Description ==

Redirect Category will perform HTTP 301 redirections on requests to posts belonging to categories of your choice.

Most important requirements.

1. Permalinks structure of both the sites should match.
2. The corresponding posts should be present on the destination domain.

Before you select existing categories for redirection, it is recommended that you create a test category and study the functionality of the plugin to make sure it meets your specific requirement.

Once turned on, it is recommended to keep the plugin turned on till all relevant search engines have updated their indexes for your redirected posts. 

== Installation ==

1. Upload the redirect-category folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Settings page and set up the redirection rules

== Changelog ==

= 0.2 =
* Plugin was failing due to Wordpress 3.1.1 updates

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 0.2 =
This version fixes a critical issue caused by Wordpress 3.1.1 update which made the plugin to fail. Update immediately.