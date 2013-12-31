<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Aho_Addons
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright Abundant Harvest Organics
 */
?>

<div id="aho-addons">
  <h1>This Week's Add-Ons</h1>

  <div class="row">
    <div class="pull-left col-lg-8">
      <!-- This file is used to markup the public facing aspect of the plugin. -->
      <p>
        You are currently viewing the available add-ons for the week starting
        <strong><?php echo date("F d, Y", strtotime($week->start_date)) ?></strong> and ending
        <strong><?php echo date("F d, Y", strtotime($week->end_date)) ?></strong>.
      </p>

      <p>
        <strong>Subscribers:</strong> case contents will vary depending on your delivery day.  Log in to your
        account and select "View Case Contents" on your Subscription Dashboard to see what's in your case this week.
      </p>
      <p><strong>All Add-Ons are organic unless otherwise noted.</strong></p>

      <div class="navigate-weeks row">
        <a href="?week=<?php echo $previous_week ?>">Previous Week</a>
        <a href="?week=<?php echo $next_week ?>">Next Week</a>
      </div>
      <?php if(empty($add_ons)): ?>
        <p><strong>AHO is taking the week off.  No deliveries scheduled this week.</strong></p>
      <?php else: ?>
        <table class="table">
          <?php foreach($add_ons as $add_on): ?>
            <tr>
              <td>
                <h4><?php echo $add_on->adminized_title ?></h4>
                <p>
                  Grower(s): <a href="mailto:<?php echo $add_on->vendor->email ?>"><?php echo $add_on->vendor->company ?></a><br />
                  Price Each: $<?php echo $add_on->sale_price ?>
                </p>
              </td>
              <td>
                <?php if(isset($add_on->image->image->thumb->url)): ?>
                  <img src="<?php echo $add_on->image->image->thumb->url ?>" />
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>

    <div class="pull-right col-lg-4">
      <h3>Did you know?</h3>
      <p>
        We offer a wide variety of add-ons to our subscribers every week. 
        You can order all kinds of items like extra veggies and fruit, 
        bread, coffee, grains, herbs, milk, meat and cheese.
      </p>

      <p>
        Add-ons are charged at the same time as your box and delivered 
        with your box. They are a super convenient way to round out your 
        grocery shopping each week.
      </p>

      <p>
        Many subscribers tell us they get almost all of their shopping 
        done with their weekly delivery from Abundant Harvest Organics.
      </p>

      <p>
        If you are already a subscriber and you want to order add-ons with your delivery, 
        log into your account and click on "Edit this Week/Add-Ons" on the right side of your profile page.
      </p>

      <p>
        If you're not already a subscriber and you would like to order add-ons, 
        <a href="https://my.abundantharvestorganics.com/users/sign_up">sign up now!</a>
      </p>
    </div>
  </div>
</div>