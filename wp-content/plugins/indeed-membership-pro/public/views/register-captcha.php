<?php if ( $type !== false && $type == 'v3' ):?>
    <div class="js-ump-recaptcha-v3-item"></div>
    <span class="ihc-js-recaptcha-v3-key" data-value="<?php echo $key;?>"></span>
    <?php wp_enqueue_script( 'ihc-login-captcha-google', 'https://www.google.com/recaptcha/api.js?render=' . $key, ['jquery'], 10.1 );?>
    <?php wp_enqueue_script( 'ihc-login-captcha', IHC_URL . 'assets/js/captcha.js', ['jquery'], 10.1 );?>
<?php else :?>
    <div class="g-recaptcha-wrapper <?php if(!empty($class)){
      echo $class;
    }?>">
        <div class="g-recaptcha" data-sitekey="<?php echo $key;?>"></div>
        <?php wp_enqueue_script( 'ihc-login-captcha-google', 'https://www.google.com/recaptcha/api.js?hl=' . $langCode, ['jquery'], 10.1);?>
    </div>
<?php endif;?>
