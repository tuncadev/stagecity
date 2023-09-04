<?php
ihc_save_update_metas('badges');//save update metas
$data['metas'] = ihc_return_meta_arr('badges');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

if (!empty($_POST['badge_image_url'])){
	foreach ($_POST['badge_image_url'] as $id=>$value){
		\Indeed\Ihc\Db\Memberships::saveMeta( $id, 'badge_image_url', $value );
	}
}
$levels = \Indeed\Ihc\Db\Memberships::getAll();
?>
<form  method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Membership Badges', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold Memberhsip Badges', 'ihc');?></h2>
				<p><?php esc_html_e('Add a custom badge for each membership for a better approach. Be sure that you add images with a proper size and ratio for each membership.', 'ihc');?></p>
				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_badges_on']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_badges_on');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_badges_on" value="<?php echo $data['metas']['ihc_badges_on'];?>" id="ihc_badges_on" />
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Custom CSS', 'ihc');?></h2>
				<textarea name="ihc_badge_custom_css" class="ihc-custom-css-box"><?php echo stripslashes($data['metas']['ihc_badge_custom_css']);?></textarea>
			</div>

			<div class="ihc-wrapp-submit-bttn ihc-submit-form">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>



<?php if ($levels):?>
	<div class="inside">
		<div class="iump-form-line">
			<div class="iump-rsp-table ihc-dashboard-form-wrap ihc-admin-user-data-list">
		<table class="wp-list-table widefat fixed tags ihc-admin-tables" id="ihc-levels-table">

			<thead>
				<tr>
					<th><?php esc_html_e('Membership', 'ihc');?></th>
					<th><?php esc_html_e('Image', 'ihc');?></th>
					<th><?php esc_html_e('Image URL', 'ihc');?></th>
				</tr>
			</thead>
			<tbody class="ihc-alternate">
		<?php foreach ($levels as $id => $level):?>
			<tr>
				<td><b><span class="ihc-list-affiliates-name-label"><?php echo $level['label'];?></span></b></td>
				<?php
				if (empty($level['badge_image_url'])){
					 $level['badge_image_url'] = '';
				}
				?>
				<td>
					<?php $display = (empty($level['badge_image_url'])) ? 'ihc-display-none' : 'ihc-display-block';?>
					<img src="<?php echo $level['badge_image_url'];?>" class="ihc-badge-img <?php echo $display;?>" id="<?php echo 'img_level' . $id;?>"/>
				</td>

				<td>
					<div class="form-group row">
					<div class="col-xs-8">
						<input type="text" class="form-control" onclick="openMediaUp(this, '<?php echo '#img_level' . $id;?>');" value="<?php echo $level['badge_image_url'];?>" name="<?php echo "badge_image_url[$id]";?>" id="<?php echo 'badge_image_url'.$id;?>">
						</div>
						<i class="fa-ihc ihc-icon-remove-e ihc-js-admin-badge-image-remove ihc-aff-search-wrapper" data-id="<?php echo $id;?>" title="Remove Badge"></i>
				</div>
				</td>
			</tr>
		<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td>
						<div class="ihc-wrapp-submit-bttn ihc-submit-form">
						<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
					</td>
					<td></td>
					<td></td>
				</tr>
			</tfoot>
		</table>


</div>
</div>
</div>
<?php endif;?>
</div>
</form>
