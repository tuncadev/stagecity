<div class="ihc-checkout-page-box-wrapper ihc-checkout-page-payment-selection-wrapper">
<?php if ( isset($data['paymentMethodData']['show']) && isset($data['paymentMethodData']['services']) && is_array($data['paymentMethodData']['services']) && count($data['paymentMethodData']['services']) > 0 ): ?>

      <div class="ihc-checkout-page-box-title">
        <?php echo $data['messages']['ihc_checkout_payment_title'];?>
      </div>
      <!-- List of available Payment methods for current Membership -->
      <div class="ihc-checkout-page-payment-selection">

        <?php if ( $data['paymentMethodData']['theme'] === 'ihc-select-payment-theme-1' ):?>
            <div class="ihc-select-payment-theme-1">
                    <?php foreach ( $data['paymentMethodData']['services'] as $paymentSlug => $paymentLabel ):
                      $label = get_option('ihc_' . $paymentSlug . '_label');
                  		if (empty($label)){
                  			$label = $paymentLabel;
                  		}
                      ?>

                      <div class="iump-form-paybox" >
                        <?php $checked = $data['paymentMethodData']['selected'] === $paymentSlug ? 'checked' : '';?>
                        <input type="radio" class="ihc-js-select-payment-service-radio" <?php echo $checked;?> value="<?php echo $paymentSlug;?>" /> <?php echo $label;?>
                      </div>

                    <?php endforeach;?>
            </div>
        <?php elseif ( $data['paymentMethodData']['theme'] === 'ihc-select-payment-theme-2' ): ?>

            <div class="ihc-select-payment-theme-2 ">

              <?php foreach ( $data['paymentMethodData']['services'] as $paymentSlug => $paymentLabel ):?>
                <div class="iump-form-paybox" >
                    <?php
                    $paymentLogo = IHC_URL . 'assets/images/' . $paymentSlug . '.png';
              			$paymentLogo = apply_filters( 'ihc_filter_payment_logo', $paymentLogo, $paymentSlug );
                    ?>
                  <?php $extraClass = $data['paymentMethodData']['selected'] === $paymentSlug ? 'ihc-payment-select-img-selected' : '';?>
                  <img src="<?php echo $paymentLogo;?>" data-type="<?php echo $paymentSlug;?>" class="ihc-payment-icon <?php echo $extraClass;?> ihc-js-select-payment" id="ihc_payment_icon_<?php echo $paymentSlug;?>" />
                </div>
              <?php endforeach;?>

            </div>

        <?php elseif ( $data['paymentMethodData']['theme'] === 'ihc-select-payment-theme-3' ):?>
            <div class="ihc-select-payment-theme-3">
                <select name="" class="ihc-js-select-payment-service-select">
                  <?php foreach ( $data['paymentMethodData']['services'] as $paymentSlug => $paymentLabel ):
                    $label = get_option('ihc_' . $paymentSlug . '_label');
                    if (empty($label)){
                      $label = $paymentLabel;
                    }
                    ?>?>
                      <?php $selected = $data['paymentMethodData']['selected'] === $paymentSlug ? 'selected' : '';?>
                      <option value="<?php echo $paymentSlug;?>" <?php echo $selected;?> ><?php echo $label;?></option>
                  <?php endforeach;?>
                </select>
            </div>
        <?php endif;?>

      </div>


<?php endif;?>
<?php if (isset($data['paymentMethodData']['services']) && is_array($data['paymentMethodData']['services']) && count($data['paymentMethodData']['services']) > 0 ): ?>
<!-- Inline fields from Braintree, Authorize, etc -->
<div class="ihc-checkout-page-payment-onsite-fields">

  <!-- Loading Spinner -->
  <div class="ihc-loading-inline-payment-fields ihc-display-none"><i class="fa-ihc ihc-icon-spinner ihc-icon-spin"></i> <?php esc_html_e('Please wait...','ihc'); ?></div>

    <!-- Authorize.Net Recurring Subscriptions -->
    <?php if ( isset( $data['paymentMethodData']['services']['authorize'] ) && $data['levelData']['access_type'] === 'regular_period' ):?>
      <?php
      if (!class_exists('ihcAuthorizeNet')){
        require_once IHC_PATH . 'classes/PaymentGateways/ihcAuthorizeNet.class.php';
      }
      $authorizeObject = new \ihcAuthorizeNet();
      ?>
        <div id="ihc_authorize_payment_fields"  <?php echo ($data['paymentMethodData']['selected'] != 'authorize') ? 'class="ihc-display-none"' : ''; ?>>
            <?php echo $authorizeObject->getCheckoutform(); ?>
        </div>
    <?php endif;?>

    <!-- Braintree -->
    <?php if ( isset( $data['paymentMethodData']['services']['braintree'] ) ):?>
        <?php
            require_once IHC_PATH . 'classes/PaymentGateways/Ihc_Braintree.class.php';
            $braintree = new \Ihc_Braintree();
        ?>
        <div id="ihc_braintree_payment_fields" <?php echo ($data['paymentMethodData']['selected'] != 'braintree') ? 'class="ihc-display-none"' : ''; ?>>
            <?php echo $braintree->getCheckoutform();?>
        </div>
    <?php endif;?>

    <!-- Stripe Connect -->
    <?php if ( isset( $data['paymentMethodData']['services']['stripe_connect'] ) ):?>
        <?php
            $stripeConnect = new \Indeed\Ihc\Gateways\StripeConnect();
        ?>
        <div id="ihc_stripe_connect_payment_fields" <?php echo ($data['paymentMethodData']['selected'] != 'stripe_connect') ? 'class="ihc-display-none"' : ''; ?>>
            <?php echo $stripeConnect->getCheckoutform( $data );?>
        </div>
    <?php endif;?>

    <!-- Bank Transfer Message -->
    <?php if ( isset( $data['paymentMethodData']['selected'] ) && $data['paymentMethodData']['selected'] === 'bank_transfer' ):?>
        <div id="" >
            <?php echo esc_html__('Bank Transfer details will be shown after payment complete', 'ihc');?>
        </div>
    <?php endif;?>

    <?php if ( isset( $data['paymentMethodData']['selected'] ) && $data['uid'] !== 0 ):?>
        <input type="hidden" name="ihc_payment_gateway" value="<?php echo $data['paymentMethodData']['selected'];?>" />
    <?php endif;?>
</div>
<?php endif;?>
 </div>
