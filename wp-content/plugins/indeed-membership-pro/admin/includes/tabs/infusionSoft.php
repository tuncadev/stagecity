<?php

ihc_save_update_metas('infusionSoft');//save update metas
$data['metas'] = ihc_return_meta_arr('infusionSoft');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

$levels = \Indeed\Ihc\Db\Memberships::getAll();
$object = new \Indeed\Ihc\Services\InfusionSoft();
$tags = $object->getContactGroups();


?>
<form  method="post">

	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Infusion Soft', 'ihc');?></h3>
		<div class="inside">
		<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold InfusionSoft', 'ihc');?></h2>
                <p><?php esc_html_e('Synchronize your InfusionSoft contacts based on Tags. For each user status or Membership a Tag is associated. ', 'ihc');?></p>
				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_infusionSoft_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_infusionSoft_enabled');" <?php echo $checked;?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_infusionSoft_enabled" value="<?php echo $data['metas']['ihc_infusionSoft_enabled'];?>" id="ihc_infusionSoft_enabled" />
			</div>
      <div class="iump-form-line">
      <h5><?php esc_html_e('Step 1: Set InfusionSoft credentials', 'ihc');?></h5>
      </div>
      <div class="iump-form-line">
      	<div class="input-group">
       	<span class="input-group-addon" ><?php esc_html_e('Account ID', 'ihc');?></span>
        <input type="text" name="ihc_infusionSoft_id" class="form-control"  value="<?php echo $data['metas']['ihc_infusionSoft_id'];?>" id="ihc_infusionSoft_id" />
        </div>
      </div>

      <div class="iump-form-line">
      	<div class="input-group">
        <span class="input-group-addon" ><?php esc_html_e('Api Key', 'ihc');?></span>
        <input type="text" name="ihc_infusionSoft_api_key" class="form-control" value="<?php echo $data['metas']['ihc_infusionSoft_api_key'];?>" id="ihc_infusionSoft_api_key" />
        </div>
      </div>
      <div class="iump-form-line">
      <h5><?php esc_html_e('Step 2: Create Tags for users into your InfusionSoft account', 'ihc');?></h5>

      </div>
			<div class="iump-form-line">
				<h5><?php esc_html_e('Step 3: Submit credentials with "Save Changes" button in order to syncronize Ultimate Membership Pro with Infusionsoft settings.', 'ihc');?></h5>
			<div class="ihc-wrapp-submit-bttn ihc-submit-form">
          <input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				</div>
			</div>
		</div>
	</div>


	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Step 4: Assign UMP Memberships to InfusionSoft Tags', 'ihc');?></h3>
		<div class="inside">
				<?php if ( $tags ):?>
						<?php foreach ( $levels as $lid => $levelData ):?>
								<div class="iump-form-line">
									<label ><?php echo $levelData['name'];?></label>
									<select name="ihc_infusionSoft_levels_groups[<?php echo $lid;?>]">
											<?php foreach ( $tags as $id => $label ):?>
													<?php $selected = (isset($data['metas']['ihc_infusionSoft_levels_groups'][$lid]) && $data['metas']['ihc_infusionSoft_levels_groups'][$lid]==$id) ? 'selected' : '';?>
													<option value="<?php echo $id;?>" <?php echo $selected;?> ><?php echo $label;?></option>
											<?php endforeach;?>
									</select>
								</div>
						<?php endforeach;?>
					<div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
				<?php else :?>
						<?php esc_html_e( 'No tags, of you are not connected to your InfusionSoft account.', 'ihc' );?>
				<?php endif;?>
		</div>
	</div>
</form>

<?php
