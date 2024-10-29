<?php
/**
 * Plugin Name:       Banana Newsletters
 * Description:       A minimalistic solution to send newsletters from your dashboard, with a subscribers manager, HTML templates and a shortcode to display a subscription opt-in form.
 * Version:           2.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Alvaro Franz
 * Author URI:        https://alvarofranz.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       banana-newsletters
 * Domain Path:       /languages
 */

namespace banana\newsletters;

include( plugin_dir_path( __FILE__ ) . 'includes/cpt-newsletter.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/cpt-subscriber.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/custom-cron-schedule.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/send-pending-newsletters.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/plugin-activation.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/plugin-deactivation.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/page-send-newsletter.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/enqueue-custom-admin-style.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/handle-unsubscribe-user.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/handle-subscribe-user.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/admin-settings-page.php' );
include( plugin_dir_path( __FILE__ ) . 'includes/shortcode-subscribe-form.php' );
