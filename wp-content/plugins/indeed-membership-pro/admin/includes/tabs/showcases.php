<?php
echo ihc_inside_dashboard_error_license();
?>
<div>
	<div class="ihc-dashboard-title">
		Ultimate Membership Pro -
		<span class="second-text">
			<?php esc_html_e('Front-End Showcases', 'ihc');?>
		</span>
	</div>
<div class="metabox-holder indeed">
<?php $url = get_admin_url() . 'admin.php?page=ihc_manage';?>
	<div class="ihc-popup-content showcases-wrapp ihc-text-aling-center">
        	<div class="ihc-display-inline ihc-showcase-section-wrapper">
	            <a href="<?php echo $url.'&tab=register';?>"><div class="ihc-popup-shortcodevalue"><i class="fa-ihc fa-user-plus-ihc"></i><?php esc_html_e('Register Form', 'ihc');?><span><?php esc_html_e('Templates, Custom Fields, Special Settings, Custom Messages', 'ihc');?></span></div></a>
	            <a href="<?php echo $url.'&tab=login';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-sign-in-ihc"></i><?php esc_html_e('Login Form', 'ihc');?><span><?php esc_html_e('Templates, Display Options, Custom Messages', 'ihc');?></span></div></a>
	            <a href="<?php echo $url.'&tab=subscription_plan';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-levels-ihc"></i><?php esc_html_e('Subscriptions Plan', 'ihc');?><span><?php esc_html_e('Templates, Custom Style', 'ihc');?></span></div></a>
							<a href="<?php echo $url.'&tab=checkout';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-checkout-ihc"></i><?php esc_html_e('Checkout Page', 'ihc');?><span><?php esc_html_e('Checkout Settings and Options available for Buyers', 'ihc');?> </span></div> </a>
	            <a href="<?php echo $url.'&tab=account_page';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-user-ihc"></i><?php esc_html_e('My Account Page', 'ihc');?><span><?php esc_html_e('Templates, ShowUp fields, ShowUp Tabs, Predefined Overview', 'ihc');?></span></div>  </a>
							<a href="<?php echo $url.'&tab=profile-form';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-profile-form-ihc"></i><?php esc_html_e('Profile Form', 'ihc');?><span><?php esc_html_e('Profile Form extra customization', 'ihc');?> </span></div> </a>
							<a href="<?php echo $url.'&tab=manage_subscription_table';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-subscription-table-ihc"></i><?php esc_html_e('Subscriptions Table', 'ihc');?><span><?php esc_html_e('Customization for Members Subscriptions table', 'ihc');?></span></div>  </a>
							<a href="<?php echo $url.'&tab=manage_order_table';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-calculator-ihc"></i><?php esc_html_e('Orders Table', 'ihc');?><span><?php esc_html_e('Customization for Members Orders table', 'ihc');?></span></div>  </a>
	            <a href="<?php echo $url.'&tab=thank-you-page';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-thank-you-ihc"></i><?php esc_html_e('Thank You Page', 'ihc');?><span><?php esc_html_e('After completing a purchase, details can be displayed', 'ihc');?> </span></div>  </a>
	            <a href="<?php echo $url.'&tab=listing_users';?>"><div class="ihc-popup-shortcodevalue"> <i class="fa-ihc fa-listing_users-ihc"></i><?php esc_html_e('Members Directory', 'ihc');?><span><?php esc_html_e('ShortCode Generator for listing current Members', 'ihc');?> </span></div>  </a>

				<div class="ihc-clear"></div>
        	</div>
    	</div>
</div>
</div>
