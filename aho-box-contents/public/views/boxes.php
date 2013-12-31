<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   Aho_Box_Contents
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright Abundant Harvest Organics
 */
?>

<h1><?php echo $delivery_days ?> Deliveries</h1>

<!-- This file is used to markup the public facing aspect of the plugin. -->
<div class="pull-left">
  <p>
    You are currently viewing the box contents for the week starting 
    <strong><?php echo date("F d, Y", strtotime($week->start_date)) ?></strong> and ending
    <strong><?php echo date("F d, Y", strtotime($week->end_date)) ?></strong>.
  </p>

  <p>
    <strong>Subscribers:</strong> case contents will vary depending on your delivery day.  Log in to your
    account and select "View Case Contents" on your Subscription Dashboard to see what's in your case this week.
  </p>
</div>
<div class="pull-right">
  <a href="http://www.abundantharvestkitchen.com/category/recipes-inspired-by-the-box/">
    <img src="<?php echo plugins_url("aho-box-contents/public/assets/kitchen.jpg") ?>" />
  </a>
</div>

<a href="?week_id=<?php echo $previous_week ?>&charge_time_id=<?php echo $charge_time_id ?>">Previous Week</a>
<?php if(empty($boxes)): ?>
  <p><strong>AHO is taking the week off.  No deliveries scheduled this week.</strong></p>
<?php else: ?>
  <div id="box-contents">
    <?php foreach($boxes as $box): ?>
      <table class="table">
        <thead>
          <tr>
            <th><?php echo $box->box_type->name ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($box->box_items as $item): ?>
            <tr>
              <td><?php echo $item->name ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endforeach; ?>
  </div>
<?php endif; ?>