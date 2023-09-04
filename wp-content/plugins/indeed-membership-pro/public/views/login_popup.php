<?php if ( !$isLoginPage && empty( $uid ) ):?>
    <?php
        wp_enqueue_style( 'ihc_iziModal' );
        wp_enqueue_script( 'ihc_iziModal_js' );
        wp_enqueue_script( 'ihc_login_modal', IHC_URL . 'assets/js/IhcLoginModal.js', ['jquery'], 10.1 );
    ?>

    <?php if ( $content ):?>
        <div class="ihc-login-modal-trigger">
            <?php echo $content;?>
        </div>
    <?php endif;?>

    <div class="ihc-display-none" id="ihc_login_modal" data-title="<?php esc_html_e('Login', 'ihc');?>">
        <?php echo do_shortcode( '[ihc-login-form]' );?>
    </div>

<?php endif;?>

<?php
$preventDefault = empty($trigger) ? 0 : 1;
$triggerSelector = empty($trigger) ? '.ihc-login-modal-trigger' : '.' . $trigger;
?>
<span class="ihc-js-login-popup-data"
      data-is_login_page="<?php if ( $isLoginPage ) {echo 1;} else {echo 0;} ?>"
      data-is_logged="<?php if ( !empty( $uid ) ) {echo 1;} else {echo 0;} ?>"
      data-trigger_selector="<?php echo $triggerSelector;?>"
      data-trigger_default="<?php echo $preventDefault;?>"
      data-autoStart="<?php
      if ( !empty ( $_GET['ihc_login_fail'] ) ){
          echo 'true';
      } else if ( !empty( $_GET['ihc_login_pending'] ) ){
          echo 'true';
      } else {
          echo 'false';
      }
      ?>"
></span>
