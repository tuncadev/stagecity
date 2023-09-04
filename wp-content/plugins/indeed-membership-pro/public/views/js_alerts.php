<?php
wp_enqueue_style( 'indeed_sweetalert_css', IHC_URL . 'assets/css/sweetalert.css', [], 10.2 );
wp_enqueue_script( 'indeed_sweetalert_js', IHC_URL . 'assets/js/sweetalert.js', [ 'jquery' ], 10.2 );
wp_enqueue_script( 'ihc-alerts', IHC_URL . 'assets/js/alerts.js', [ 'jquery' ], 10.2 );
?>
<span class="ihc-js-public-alerts-data"
      data-error_title="<?php esc_html_e('Error', 'ihc');?>"
      data-error_text="<?php echo $error;?>"
      data-warning_title="<?php esc_html_e('Warning', 'ihc');?>"
      data-warning_text="<?php echo $warning;?>"
      data-info_title="<?php esc_html_e('Info', 'ihc');?>"
      data-info_text="<?php echo $info;?>"
      data-error="<?php echo empty($error) ? 0 : 1;?>"
      data-warning="<?php echo empty($warning) ? 0 : 1;?>"
      data-info="<?php echo empty($info) ? 0 : 1;?>"
      data-ok="<?php esc_html_e( 'Ok', 'ihc' );?>"
></span>
