<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<div>
	<div class="iump-page-title">
		Ultimate Membership Pro -
		<span class="second-text">
			<?php esc_html_e('Subscriptions Plan', 'ihc');?>
		</span>
	</div>
	<div class="ihc-stuffbox">
				<div class="impu-shortcode-display">
					[ihc-select-level]
				</div>
			</div>
<div class="metabox-holder indeed">
<?php
		if ( isset($_POST['ihc_save'] ) && !empty($_POST['ihc_admin_subscription_plan_nonce']) && wp_verify_nonce( $_POST['ihc_admin_subscription_plan_nonce'], 'ihc_admin_subscription_plan_nonce' ) ){
				ihc_save_update_metas('general-subscription');//save update metas
		}
		$meta_arr = ihc_return_meta_arr('general-subscription');//getting metas

		?>
					<form  method="post">

						<input type="hidden" name="ihc_admin_subscription_plan_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_subscription_plan_nonce' );?>" />

						<div class="ihc-stuffbox">
							<h3> <?php esc_html_e("Subscriptions Plan Settings", 'ihc');?></h3>
							<div class="inside">
							 <div class="iump-register-select-template">
								<div class="iump-form-line iump-no-border">
									<h2><?php esc_html_e('Subscriptions Plan Template', 'ihc');?></h2>
								</div>
								<div class="iump-form-line iump-no-border">
								<?php esc_html_e('Select Subscriptions Plan Template:', 'ihc');?> <select name="ihc_level_template" id="ihc_level_template" onChange="ihcPreviewSelectLevels();">
									<?php
										$templates = array(
															'ihc_level_template_9'=>'(#9) '.esc_html__('Modern Theme', 'ihc'),
															'ihc_level_template_8'=>'(#8) '.esc_html__('Gray Theme', 'ihc'),
															'ihc_level_template_7'=>'(#7) '.esc_html__('Green Premium Theme', 'ihc'),
															'ihc_level_template_6'=>'(#6) '.esc_html__('Effect Premium Theme', 'ihc'),
															'ihc_level_template_5'=>'(#5) '.esc_html__('Blue Premium Theme', 'ihc'),
															'ihc_level_template_4'=>'(#4) '.esc_html__('Serious Theme', 'ihc'),
															'ihc_level_template_3'=>'(#3) '.esc_html__('Sample Theme', 'ihc'),
														    'ihc_level_template_2'=>'(#2) '.esc_html__('Business Theme', 'ihc'),
															'ihc_level_template_1'=>'(#1) '.esc_html__('Block Box Theme', 'ihc')
															);
										foreach($templates as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if ($k==$meta_arr['ihc_level_template']){
													 echo 'selected';
												}
												?>
												><?php echo $v;?></option>
											<?php
										}
									?>
								</select>
								</div>
							  </div>
								<div>
									<div id="ihc_preview_levels"></div>
								</div>

								<div class="ihc-wrapp-submit-bttn">
									<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn" />
								</div>
							</div>
						</div>
						<div class="ihc-stuffbox">
							<h3><?php esc_html_e('Additional Custom CSS', 'ihc');?></h3>
							<div class="inside">
								<textarea id="ihc_select_level_custom_css" onBlur="ihcPreviewSelectLevels();" name="ihc_select_level_custom_css" class="ihc-dashboard-textarea-full"><?php echo (isset($meta_arr['ihc_select_level_custom_css'])) ? $meta_arr['ihc_select_level_custom_css'] : '';?></textarea>
								<div class="ihc-wrapp-submit-bttn">
									<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn">
								</div>
							</div>
						</div>
					</form>
</div>
</div>
