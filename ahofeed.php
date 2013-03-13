<?php 
/*
Plugin Name: AHO Delivery Feed
Plugin URI: http://www.firestitch.com
Description: Plugin for displaying deliveries from the AHO application
Author: Raymond Gigliotti
Version: 1.0
Author URI: http://www.firestitch.com
*/

function p($s) {
	echo "<pre>";
	print_r($s);
	echo "</pre>";
}

function ahofeed_admin() {
	include('ahofeed_admin.php');
}

function ahofeed_admin_actions() {
    add_options_page("AHOFeed", "AHOFeed", 1, "AHOFeed", "ahofeed_admin");
}

add_action('admin_menu', 'ahofeed_admin_actions');
add_shortcode("ahofeed-delivery-sites", "ahofeed_delivery_sites");
add_shortcode("ahofeed-delivery-site", "ahofeed_delivery_site");
add_shortcode("ahofeed-case-contents", "ahofeed_case_contents");
add_filter('the_title',"ahofeed_title");


function the_delivery_site()
{
  $site = stripslashes(@$_GET['delivery_site']);
  $delivery = null;
  foreach(ahofeed_deliveries() as $tmp_delivery)
  {
		if(trim($tmp_delivery->name)==trim($site)) 
		{
			$delivery = $tmp_delivery;
			break;
		}
	}
	
	return $delivery;
}

function ahofeed_title($title) 
{
	$site = the_delivery_site();

	if($site)
	{
    if($site->home_delivery)
    {
      return $site->name . " <img src='" . plugins_url("assets/house.jpg", __FILE__) . "'/>";
    }
    else
    {
      return $site->name;
    }
	}

	return $title;
}

function ahofeed_feed($url,$name) {

	$data = get_option('ahofeed_cache_'.$name);

	$timestamp = get_option('ahofeed_cache_timestamp_'.$name);

	if($timestamp < time() - (60 * 60 * get_option('ahofeed_cache_hours')))
	{

		$handle = @fopen($url, "r");

		$result = @fread($handler,8192);

		$data = "";
		if($handle)
	    while (($buffer = fgets($handle, 4096))!==false)
	        $data .= $buffer;

	    
	    @fclose($handle);

	    update_option('ahofeed_cache_'.$name,$data);
	    update_option('ahofeed_cache_timestamp_'.$name,time());
	}

	return json_decode($data);
}

function ahofeed_deliveries() {
	return (array)ahofeed_feed(get_option('ahofeed_delivery_sites_url'),"delivery_sites");
}


function ahofeed_case_contents() {
	$case_contents = ahofeed_feed(get_option('ahofeed_case_contents_url'),"case_contents");

?>

	<div class="case-contents">

		<div class="current">
			You are currently viewing the box contents for the week starting <?=$case_contents->start_date->formatted?> and ending <?=$case_contents->end_date->formatted?> View Previous Weeks</a>
		</div>

		<? if (!$case_contents) { ?>
			<p>
			    <strong>The case contents have not been published for this week yet.</strong>
			</p>
		<? } else { ?>

			<table>
			    <thead>
		    	<tr>
			    	<th>Produce</th>
			    	<th>In Small Box</th>
			    	<th>In Large Box</th>
			    </tr>
				</thead>

			    <tbody>
			    	<? foreach ($case_contents->case_contents as $case_content) { ?>
			    	<tr>
				    	<td><div class="name"><?=$case_content->name?></div></td>
				    	<td>
				    		<div class="in-box <?=($case_content->small ? "yes" : "no")?>"></div>
				    	</td>
				    	<td>
				    		<div class="in-box <?=($case_content->large ? "yes" : "no")?>"></div>
				    	</td>
				    </tr>
				    <? } ?>
				</tbody>
			</table>
		<? } ?>
	</div>
<?

}

function ahofeed_delivery_sites() {
	
	$deliveries = ahofeed_deliveries();

?>

<table id="delivery-sites">
<tbody>
	<tr>	
		<th></th>
		<th class="sites">DELIVERY SITES AND LOCATIONS</th>
		<th class="day-and-time">DAY AND TIME</th>
		<th class="box-prices">BOX PRICES</th>
		<th class="signup">&nbsp;</th>
	</tr>

	<? foreach($deliveries as $delivery) { ?>

	<?
		$start_time = new DateTime($delivery->delivery_start_time);
		$end_time 	= new DateTime($delivery->delivery_end_time);
	?>

	<tr>	
		<td class="<?=($delivery->home_delivery ? "home-delivery" : "")?>">
			<div class="icon"></div>
		</td>
		<td class="sites-title">
			<a href="/<?=trim(get_option('ahofeed_delivery_path'),"/")?>/?delivery_site=<?=urlencode($delivery->name)?>" title="<?=$delivery->name?>" rel="bookmark" class="name">
			  <?=$delivery->name?>
			  <?php if($delivery->status == "inactive"): ?>
			    <br /><span class="coming-soon">Coming Soon...get more info</span>
			  <?php endif; ?>
			  
			  <?php if($delivery->home_delivery && $delivery->passcode_required): ?>
			    <br /><span class="passcode-required">Passcode required for this site</span>
			  <?php endif; ?>
			</a>
			<div class="address">
				<?=$delivery->address->line_1?> <?=$delivery->address->line_2?>
				<?=$delivery->address->city?> <?=$delivery->address->state?> <?=$delivery->address->postal_code?>
			</div>
		</td>
		<td>
			<div class="day"><?=@date("l",$delivery->delivery_day)?></div>
			<div class="time">
				<?=$start_time->format('g:ia')?>-<?=$end_time->format('g:ia')?>
			</div>
		</td>
		<td>
			<div class="price-small">SM $<?=number_format($delivery->box_prices->small,2)?></div>
			<div class="price-large">LG $<?=number_format($delivery->box_prices->large,2)?></div>
		</td>
		<td><a href="https://my.abundantharvestorganics.com/signup-delivery-step1" class="signup">Signup Up now</a></td>
	</tr>

	<? } ?>
</tbody>
</table>

<?

}

function ahofeed_delivery_site() {

	$site = stripslashes(@$_GET['delivery_site']);

	$delivery = the_delivery_site();
	

	if($delivery) {
		$address = array_filter(array($delivery->address->city,$delivery->address->state,$delivery->address->postal_code),"strlen");

		$start_time = new DateTime($delivery->delivery_start_time);
		$end_time 	= new DateTime($delivery->delivery_end_time);
	?>

	<div class="delivery-site <?=$delivery->status?>">

		<div class="col2">
		  <?php if($delivery->media): ?>
		    <div class="media-player" style="display:block">
		      <?=$delivery->media?>
		    </div>
		  <?php endif; ?>

			<div class="sign-up-links" style="display:block">
			  <? if($delivery->status=="active") { ?>
  				<a href="https://my.abundantharvestorganics.com/signup-delivery-step1" class="sign-up">Sign Up Now for Service</a>
  			<? } else { ?>
  				<a href="https://my.abundantharvestorganics.com/signup-delivery-step1" class="join-list">Join the Waiting List</a>
  			<? } ?>
			</div>
		</div>
		<div class="col1">
			<h2 class="welcome">Welcome to this Delivery Site!</h2>	
			<p><?=$delivery->site_information?></p>

			<h4>Box Prices for this Delivery Site:</h4>	

			<div class="box-small">SM $<?=number_format($delivery->box_prices->small,2)?></div>
			<div class="box-large">LG $<?=number_format($delivery->box_prices->large,2)?></div>

			<? if($delivery->passcode_required) { ?>

				<div class="passcode-required">
					***IMPORTANT***<br>
					PASSCODE REQUIRED to sign up for this site. CONTACT the HOST to get the passcode before signing up.
				</div>
			<? } ?>

			<h4>Host(s)</h4>	
			<p><?=$delivery->host_name?></p>
			
			<h4>Day and Time</h4>	
			<p class="day-time">
				<div class="day"><?=@date("l",$delivery->delivery_day)?></div>
				<div class="time"><?=$start_time->format('g:ia')?>-<?=$end_time->format('g:ia')?></div>
			</p>
			
			<h4>Delivery Site Address</h4>	
			
			<p class="address">
				<div class="address1">
					<?=$delivery->address->line_1?>
				</div>

				<div class="city-state-zip">
					<?=implode(", ",$address)?>
				</div>

				<? if($delivery->line_2) { ?>
					<div class="address2">
						<?=$delivery->address->line_2?>
					</div>
				<? } ?>
			</p>

			<? if($delivery->status=="active") { ?>
				
				<? if($delivery->phone) { ?>
					<p class="phone"><label>Phone:</label><?=$delivery->phone?></p>
				<? } ?>

				<? if($delivery->phone1) { ?>
					<p class="phone-alt"><label>Alternate Phone:</label><?=$delivery->phone1?></p>
				<? } ?>

				<? if($delivery->host_email) { ?>
					<p class="email"><label>Email: </label> <a href="mailto:<?=$delivery->host_email?>"><?=$delivery->host_email?></a></p>
				<? } ?>
			<? } ?>

			<? if($delivery->additional_information) { ?>
				<p class="additional"><label>Additional Information:</label><?=$delivery->additional_information?></p>
			<? } ?>

			<? if($delivery->host_message) { ?>
				<p class="host-message"><label>Host Message: </label><?=$delivery->host_message?></p>
			<? } ?>
		</div>

	</div>

	<?

	}

}