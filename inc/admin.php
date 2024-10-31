<?php

/*

Admin component of the Redirect Category plugin

*/

if ( is_admin() ): ?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div>
<h2>Redirect Category</h2>
<form method="post" action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=redirect-category">
<?php

	$redirectcategory_enable = get_option('redirectcategory_enable');	
	if (!$redirectcategory_enable) echo '<div class="warn"><p>	
<input type="checkbox" name="redirectcategory_enable" /> <label>Turn on Redirect Category. I understand the consequences of doing a <a href="http://en.wikipedia.org/wiki/HTTP_301" target="_blank">HTTP 301 redirect</a> to another domain.</label>	
	</p></div>';
	else echo '<p><input type="checkbox" name="redirectcategory_enable" checked/> <label>Turn on Redirect Category. I understand the consequences of doing a <a href="http://en.wikipedia.org/wiki/HTTP_301" target="_blank">HTTP 301 redirect</a> to another domain.</label></p>';
	
?>

<div id="redirect-category-settings">
<div class="fl">
<h3>Destination Settings</h3>
<table id="redirect-category-settings-table">
	<tr><td colspan="2">
	Configure the protocol and domain name details of the destination website where you want to redirect the posts to.
	<div class="lsep" />
	</td></tr>
	<tr>
		<td>
			<label for="redirectcategory_protocol"><?php echo _e("Protocol", 'redirect-category'); ?></label>
		</td>
		<td>
			<select name="redirectcategory_protocol" id="redirectcategory_protocol">
				<?php
					$redirectcategory_protocol = get_option('redirectcategory_protocol');
				?>
				<option value="http"<?php if ($redirectcategory_protocol == 'http') echo ' selected'; ?>>HTTP</option>
				<option value="https"<?php if ($redirectcategory_protocol == 'https') echo ' selected'; ?>>HTTPS</option>
			</select>			
		</td>
	</tr>
	<tr>
		<td>
			<label for="redirectcategory_destination_domain"><?php echo _e("Domain", 'redirect-category'); ?></label>
		</td>
		<td>
			<input name="redirectcategory_destination_domain" id="redirectcategory_destination_domain" type="text" value="<?php				
				$redirectcategory_destination_domain = get_option('redirectcategory_destination_domain');
				echo $redirectcategory_destination_domain;
			?>" style="width:290px" />
		</td>
	</tr>
	<tr>
		<td>			
		</td>
		<td>
			<input type="checkbox" id="redirectcategory_no_domain_validation" name="redirectcategory_no_domain_validation" <?php
				
				$no_validation = get_option('redirectcategory_no_domain_validation');
				if ($no_validation)	echo 'checked="checked"';
			
			?>/> <label for="redirectcategory_no_domain_validation">Don't validate domain</label>
		</td>
	</tr>	
	<tr>
		<td colspan="2">		
		<h3>Which categories would you like to redirect?</h3>			
<?php
	
	
	$redirectcategory_categories = get_option('redirectcategory_categories');
	if (!is_array($redirectcategory_categories)) $redirectcategory_categories = unserialize($redirectcategory_categories);
		
	$args = array('orderby' => 'name', 'order' => 'ASC');	
	$categories = get_categories($args);
	
	foreach($categories as $category) {
		
		echo '<input name="redirectcategory_categories[]" type= "checkbox" value="'. $category->term_id .'"';
		if (count($redirectcategory_categories) > 0) if (in_array($category->term_id , $redirectcategory_categories)) { echo 'checked="checked"'; }
		echo '/> ';
		echo '<a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a>';
		echo ' ('.$category->count.' posts)<br/>';		
	} 

?>			
		</td>
	</tr>
</table>
</div>
<div class="fl" id="redirect-category-guide">
<h3>Guide</h3>
Redirect Category will perform HTTP 301 redirections on requests to posts belonging to categories of your choice.
<p>
Most important requirements.
<ol>
<li>Permalinks structure of both the sites should match.</li>
<li>The corresponding posts should be present on the destination domain.</li>
</ol>
</p>
<p>
Before you select existing categories for redirection, it is recommended that you create a test category and study the functionality of the plugin to make sure it meets your specific requirement.
</p>
<p>
Once turned on, it is recommended to keep the plugin turned on till all relevant search engines have updated their indexes for your redirected posts.
</p>
<p>
A <a href="http://www.budhiman.com/">Budhiman</a> Wordpress plugin.
</p>
</div>
<div class="cb"></div>

<?php wp_nonce_field('redirect-category', 'redirect-category-action'); ?>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>

</div>
<?php endif; ?>
