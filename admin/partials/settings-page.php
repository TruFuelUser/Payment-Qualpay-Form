<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://trufuel.net
 * @since      1.0.0
 *
 * @package    Contact_Form_Trucore
 * @subpackage Contact_Form_Trucore/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php if (!defined('ABSPATH')) exit; ?>
<style>
  .wrap {
    margin: 0;
    padding: 10px;
    font-family: Inter;
  }

  .wrap h1{
    text-align: center;
  }

  .wrap input {
    border-radius: 10px;
  }

  .wrap input[type='submit'] {
    background-color: #137DC5;
  }
</style>
<div class="wrap">
  <h1><?php esc_html_e('Settings TruFuel Payment', 'trufuel-payment'); ?></h1>
  <form method="post" action="options.php">
    <?php
      settings_fields('trf_payment');
      do_settings_sections('trf_payment');
      submit_button(__('Save Changes', 'trufuel-payment'));
    ?>
  </form>
</div>
