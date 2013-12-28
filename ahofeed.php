<?php 
/*
Plugin Name: AHO Delivery Feed
Description: Plugin for displaying deliveries from the AHO application
*/

function p($s) 
{
	echo "<pre>";
	print_r($s);
	echo "</pre>";
}

function ahofeed_admin() 
{
	include('ahofeed_admin.php');
}

function ahofeed_admin_actions() 
{
    add_options_page("AHOFeed", "AHOFeed", 1, "AHOFeed", "ahofeed_admin");
}

add_action('admin_menu', 'ahofeed_admin_actions');
add_shortcode("ahofeed-delivery-sites", "ahofeed_delivery_sites");
add_shortcode("ahofeed-delivery-site", "ahofeed_delivery_site");

// the monday charge day
add_shortcode("ahofeed-case-contents-mon", "ahofeed_case_contents_mon");
// the thursday 9:00 charge day
add_shortcode("ahofeed-case-contents-thurs-9", "ahofeed_case_contents_thurs_9");
// the thursday 11:00 charge day
add_shortcode("ahofeed-case-contents-thurs-11", "ahofeed_case_contents_thurs_11");

//add_filter('the_title',"ahofeed_title");


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

function ahofeed_feed($url,$name) 
{

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

function ahofeed_deliveries() 
{
	return (array)ahofeed_feed(get_option('ahofeed_delivery_sites_url'),"delivery_sites");
}

function ahofeed_case_contents_mon()
{
	return ahofeed_case_contents('1');
}

function ahofeed_case_contents_thurs_9()
{
	return ahofeed_case_contents('2');
}

function ahofeed_case_contents_thurs_11()
{
	return ahofeed_case_contents('3');
}

function ahofeed_case_contents($id=1) 
{
	$case_contents = ahofeed_feed(get_option('ahofeed_case_contents_url').'?charge_time_id='.$id,"case_contents_".$id);

?>

	<div class="case-contents">

		<div style="float:right; width:400px;">
			<img src="https://my.abundantharvestorganics.com/images/Weeklyboxrecipes-logo-link-bevel.jpg" />
		</div>

		<div style="float:left; width:400px;">
			<div class="current">
				You are currently viewing the box contents for the week starting <?=$case_contents->start_date->formatted?> and ending <?=$case_contents->end_date->formatted?>.
			</div><br />
	
			<p>
				<em>
					<strong>
						Subscribers: 
					</strong>
					Case contents will vary depending on your delivery day. Log into your account and select "View Case Contents" on your Subscription Dashboard to see what's in your case this week.
				</em>
			</p>

		<a href="#">View Previous Week</a>

		<a href="#">View Next Week</a>

		</div>

		<br style="clear:both" />
		<? if (!$case_contents) { ?>
			<p>
			    <strong>The case contents have not been published for this week yet.</strong>
			</p>
		<? } else { ?>

			<table>
			    <thead>
		    	<tr>
			    	<th>Produce</th>
			    	<th class="in-small-box">In Small Box</th>
			    	<th class="in-large-box">In Large Box</th>
			    </tr>
				</thead>

			    <tbody>
			    	<? foreach ($case_contents->case_contents as $case_content) { ?>
			    	<tr>
				    	<td><div class="name"><?=$case_content->name?></div></td>
				    	<td>
				    		<div class="in-box <?=($case_content->small ? "yes" : "no")?>"><?=($case_content->small ? "yes" : "no")?></div>
				    	</td>
				    	<td>
				    		<div class="in-box <?=($case_content->large ? "yes" : "no")?>"><?=($case_content->large ? "yes" : "no")?></div>
				    	</td>
				    </tr>
				    <? } ?>
				</tbody>
			</table>
		<? } ?>
	</div>
<?

}

function ahofeed_delivery_sites() 
{
	
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
		$start_time = new DateTime(date("Y-m-d H:i:s",strtotime(str_replace(array("T","Z"),"",$delivery->delivery_start_time))));
		$end_time 	= new DateTime(date("Y-m-d H:i:s",strtotime(str_replace(array("T","Z"),"",$delivery->delivery_end_time))));
	?>

	<tr>	
		<td class="<?=($delivery->home_delivery ? "home-delivery" : "")?>">
			<div class="icon"></div>
		</td>
		<td class="sites-title">
			<a href="/<?=trim(get_option('ahofeed_delivery_path'),"/")?>/?delivery_site=<?=urlencode($delivery->name)?>" title="<?=$delivery->name?>" rel="bookmark" class="name">
			  <?=$delivery->name?>
			  <?php if($delivery->status == "pending"): ?>
			    <br /><span class="coming-soon">Deliveries Coming Soon...get more info</span>
			  <?php endif; ?>
			  
			  <?php if($delivery->home_delivery && $delivery->authenticate): ?>
			    <br /><span class="passcode-required">Passcode required for this site</span>
			  <?php endif; ?>
			</a>
			<div class="address">
				<?=$delivery->address->address1?> <?=$delivery->address->address2?>
				<?=$delivery->address->city?> <?=$delivery->address->state?> <?=$delivery->address->postal_code?>
			</div>
		</td>
		<td class="sites-delivery-time">
			<div class="day"><?=$delivery->delivery_day?></div>
			<div class="time">
				<?=date("g:ia",strtotime(str_replace(array("T","Z"),"",$delivery->start_time)))?>-<?=date("g:ia",strtotime(str_replace(array("T","Z"),"",$delivery->end_time)))?>
			</div>
		</td>
		<td class="sites-box-price">
			<div class="price-small">SM $<?=number_format($delivery->box_prices->small,2)?></div>
			<div class="price-large">LG $<?=number_format($delivery->box_prices->large,2)?></div>
		</td>
		<td class="sites-sign-up"><a href="https://my.abundantharvestorganics.com/users/signup?site=<?=$delivery->slug?>" class="button">Sign Up NOW</a></td>
	</tr>

	<? } ?>
</tbody>
</table>

<?

}

function ahofeed_delivery_site() 
{

	$site = stripslashes(@$_GET['delivery_site']);

	$delivery = the_delivery_site();
	

	if($delivery) {
		$address = array_filter(array($delivery->address->city,$delivery->address->state,$delivery->address->postal_code),"strlen");

	?>

	<div class="delivery-site <?=$delivery->status?>">

		<div class="col2">
		    <div class="media-player" style="display:block">
		      '<iframe width="418" height="235" src="http://www.youtube.com/embed/x3pAK4g_lO8?list=UUTj4QC8sMTGOptbM6lDCXSw" frameborder="0" allowfullscreen></iframe>
		    </div>

			<div class="sign-up-links" style="display:block">
			  <? if($delivery->status=="active") { ?>
  				<a href="https://my.abundantharvestorganics.com/users/signup?site=<?=$delivery->slug?>" class="sign-up">Sign Up Now for Service</a>
  			<? } else { ?>
  				<a href="https://my.abundantharvestorganics.com/users/signup?site=<?=$delivery->slug?>" class="sign-up">Join the Waiting List</a>
  			<? } ?>
			</div>
		</div>
		<div class="col1">
			<h2 class="welcome">Welcome to this Delivery Site!</h2>	
			<p><?=$delivery->host_message?></p>

			<?php if($delivery->box_prices && $delivery->box_prices->small): ?>
			  <h4>Box Prices for this Delivery Site:</h4>	
  			<div class="box-small">SM $<?=number_format($delivery->box_prices->small, 2)?></div>
  			<div class="box-large">LG $<?=number_format($delivery->box_prices->large, 2)?></div>
			<?php endif; ?>

			<?php if($delivery->authenticate): ?>
				<div class="passcode-required">
					<? if ($delivery->home_site_message): ?>
					<?=$delivery->home_site_message?>
					<? else: ?>
					***IMPORTANT***<br>
					PASSCODE REQUIRED to sign up for this site. CONTACT the HOST to get the passcode before signing up.
					<? endif; ?>
				</div>
			<?php endif; ?>

      <?php if($delivery->host_name): ?>
        <h4>Host(s)</h4>	
  			<p><?=$delivery->host_name?></p>
      <?php endif; ?>
			
			<?php if($delivery->delivery_day): ?>
			  <h4>Day and Time</h4>	
  			<p class="day-time">
  				<?=$delivery->delivery_day?><br />
  				<?=date("g:ia",strtotime(str_replace(array("T","Z"),"",$delivery->start_time)))?>-<?=date("g:ia",strtotime(str_replace(array("T","Z"),"",$delivery->end_time)))?>
  			</p>
			<?php endif; ?>
			
			
			<?php if($delivery->address && trim($delivery->address->address1)): ?>
			  <h4>
			    Delivery Site Address
			    <?php if(stristr($delivery->address->address1, "@") === false): ?>
			      <a style="color:#c5453e" href="https://maps.google.com/maps?q=<?=trim($delivery->address->address1)?>+<?=trim($delivery->address->line_2)?>+<?=trim(implode("+",$address))?>&hl=en" target="_blank">View Map</a>
			    <?$delivery->address->address1; endif; ?>
			  </h4>	

  			<p>
  				<?=trim($delivery->address->address1)?><br />

  				<? if($delivery->address->address2) { ?>
  					<?=trim($delivery->address->line_2)?><br />
  				<? } ?>
  				
  				<?=trim(implode(", ",$address))?><br />
  			</p>
			<?php endif; ?>

			<? if($delivery->home_phone) { ?>
				<p class="phone"><label>Phone:</label><?=$delivery->home_phone?></p>
			<? } ?>

			<? if($delivery->cell_phone) { ?>
				<p class="phone-alt"><label>Alternate Phone:</label><?=$delivery->cell_phone?></p>
			<? } ?>

			<? if($delivery->host_email) { ?>
				<p class="email"><label>Email: </label> <a href="mailto:<?=$delivery->host_email?>"><?=$delivery->host_email?></a></p>
			<? } ?>

			<? if($delivery->additional_information) { ?>
				<p class="additional"><label>Additional Information:</label><?=$delivery->additional_information?></p>
			<? } ?>

			<? if($delivery->host_message) { ?>
				<br /><p class="host-message"><label>Host Message: </label><?=$delivery->host_message?></p>
			<? } ?>
		</div>

	</div>

	<?

	}
}