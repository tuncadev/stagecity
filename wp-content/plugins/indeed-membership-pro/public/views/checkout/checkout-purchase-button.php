

<input type="hidden" name="lid" value="<?php echo $data['lid'];?>" />
<input type="hidden" name="checkout-form" value="1" />
<input type="hidden" name="subscription_access_type" value="<?php echo $data['levelData']['access_type'];?>" />

<input type="hidden" name="payment_selected" value="<?php echo (isset($data['paymentMethodData']['selected'])) ? $data['paymentMethodData']['selected'] : '';?>" />

<input type="hidden" name="coupon_used" value="<?php echo (isset($data['couponData']['details']['code'])) ? $data['couponData']['details']['code'] : '';?>" />

<input type="hidden" name="dynamic_price_set" value="<?php echo (isset($data['dynamicData']['used'])) ? $data['dynamicData']['used'] : '';?>" />

<?php if ( isset($data['country']) && isset($data['country']) ):?>
<input type="hidden" name="country" value="<?php echo $data['country'];?>" />
<?php endif;?>

<?php if ( isset($data['state']) && isset($data['state']) ):?>
<input type="hidden" name="state" value="<?php echo $data['state'];?>" />
<?php endif;?>

<?php if ( isset($data['buttonData']['show']) && isset($data['buttonData']['label']) ):?>
<div class="ihc-checkout-page-box-wrapper ihc-purchase-wrapper">
  <input type="submit"  name="ihc-complete-purchase" value="<?php echo $data['buttonData']['label'];?>" class="ihc-complete-purchase-button" />
  <!-- Loading Spinner -->
  <div class="ihc-loading-purchase-button ihc-display-none"><i class="fa-ihc ihc-icon-spinner ihc-icon-spin"></i> <?php esc_html_e('Please wait...','ihc'); ?></div>
</div>
<?php endif;?>
