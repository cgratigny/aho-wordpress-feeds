<?php 

	$ahofeed_delivery_sites_url 	= get_option('ahofeed_delivery_sites_url');
	$ahofeed_case_contents_url 		= get_option('ahofeed_case_contents_url');
	$ahofeed_delivery_path 			= get_option('ahofeed_delivery_path');
	$ahofeed_cache_hours 			= get_option('ahofeed_cache_hours');

	if($_POST['ahofeed_hidden'] == 'Y') {
		
		$ahofeed_delivery_sites_url = $_POST['ahofeed_delivery_sites_url'];
		update_option('ahofeed_delivery_sites_url', $ahofeed_delivery_sites_url);
		
		$ahofeed_case_contents_url = $_POST['ahofeed_case_contents_url'];
		update_option('ahofeed_case_contents_url', $ahofeed_case_contents_url);		

		$ahofeed_delivery_path = $_POST['ahofeed_delivery_path'];
		update_option('ahofeed_delivery_path', $ahofeed_delivery_path);		


		$ahofeed_cache_hours = $_POST['ahofeed_cache_hours'];
		update_option('ahofeed_cache_hours', $ahofeed_cache_hours);				

		?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		<?php
	}
	
?>

<div class="wrap">
<?php    echo "<h2>" . __( 'AHO Feed Options', 'ahofeed_trdom' ) . "</h2>"; ?>

<form name="ahofeed_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	
	<input type="hidden" name="ahofeed_hidden" value="Y">
	
	<p><?php _e("Devliery Sites URL: " ); ?><input type="text" name="ahofeed_delivery_sites_url" value="<?php echo $ahofeed_delivery_sites_url; ?>" size="100"><?php _e(" eg: http://my.abundantharvestorganics.com/json/delivery_sites.php" ); ?></p>
	
	<p><?php _e("Case Contentets URL: " ); ?><input type="text" name="ahofeed_case_contents_url" value="<?php echo $ahofeed_case_contents_url; ?>" size="100"><?php _e(" eg: http://my.abundantharvestorganics.com/json/case_contents.php" ); ?></p>
	
	<p><?php _e("Delivery Site Page: " ); ?><input type="text" name="ahofeed_delivery_path" value="<?php echo $ahofeed_delivery_path; ?>" size="100"><?php _e(" eg: /delivery-site" ); ?></p>
	
	<p><?php _e("Feed Cache Hours: " ); ?><input type="text" name="ahofeed_cache_hours" value="<?php echo $ahofeed_cache_hours; ?>" size="4"><?php _e(" eg: 24" ); ?></p>
	
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Update Options', 'ahofeed_trdom' ) ?>" />
	</p>


	<p>
		<div>Cached Delivery Sites</div>
		<textarea style="width:100%;height:400px"><? print_r(json_decode(get_option("ahofeed_cache_delivery_sites")))?></textarea>
	</p>

	<p>
		<div>Cached Case Contents</div>
		<textarea style="width:100%;height:400px"><? print_r(json_decode(get_option("ahofeed_cache_case_contents")))?></textarea>
	</p>

</form>
</div>