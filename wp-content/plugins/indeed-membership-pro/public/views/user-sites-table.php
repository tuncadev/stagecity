<?php global $current_user;?>
<?php if (!empty($data['uid_levels'])):?>
<table class="ihc-account-subscr-list ihc-user-sites-table">
	<thead>
		<tr>
			<td class="ihc-col1"><?php esc_html_e('Level', 'ihc');?></td>
			<td class="ihc-col2"><?php esc_html_e('Site', 'ihc');?></td>
			<td class="ihc-col3"><?php esc_html_e('Status', 'ihc');?></td>
		</tr>
	</thead>
	<?php foreach ($data['uid_levels'] as $lid=>$array):?>
		<?php $level_active = \Indeed\Ihc\UserSubscriptions::isActive( $current_user->ID, $lid );?>
		<tr>
			<td class="ihc-level-name-wrapp"><?php echo Ihc_Db::get_level_name_by_lid($lid);
				if (!$level_active){
					esc_html_e(' - Expired', 'ihc');
				}
			?></td>
			<td><?php
				if ($site_id = Ihc_Db::get_user_site_for_uid_lid($current_user->ID, $lid)):
					$site_details = get_blog_details( $site_id );
					$site_address = untrailingslashit( $site_details->domain . $site_details->path );
					if (strpos($site_address, 'http')===FALSE){
						$site_address = 'http://' . $site_address;
					}
					?>
					<a href="<?php echo $site_address;?>" target="_blank"><?php echo $site_details->blogname;?></a>
					- <span class="ihc-user-sites-delete-bttn" onClick="ihcDoUsersiteModuleDelete(<?php echo $lid;?>);"
							><?php esc_html_e('Delete', 'ihc');?></span>
					<?php
				else :?>
				<?php if ($level_active):?>
					<a href="<?php echo add_query_arg('lid', $lid, $data['add_new']);?>"><?php esc_html_e('Add New', 'ihc');?></a>
			<?php
					else :
						echo '-';
					endif;
				endif;
			?></td>
			<td><?php
				if (empty($site_id)){
					echo '-';
				} else {
					$status = Ihc_Db::is_blog_available($site_id);
					if ($status){
						if ($level_active){
							esc_html_e('Active', 'ihc');
						} else {
							update_blog_status($site_id, 'deleted', 1); /// cron does not update yet, so we can manually update
							esc_html_e('Site Inactive', 'ihc');
						}
					} else {
						if ($level_active){
							esc_html_e('Site Inactive', 'ihc');
						} else {
							esc_html_e('Site Inactive - Level Expired', 'ihc');
						}
					}
				}
			?></td>
		</tr>
	<?php endforeach;?>
</table>
<?php else:
	$level_can_do = array();
	foreach ($data['levels_can_do'] as $lid=>$active){
		if ($active){
			$level_can_do[] = Ihc_Db::get_level_name_by_lid($lid);
		}
	}
	if (empty($level_can_do)){
		echo '<div class="ihc-additional-message">' .esc_html__('This service is not yet available. Please stay in touch.').'</div>';
	} else {
		echo esc_html__('You have no level for creating a site. In order to do that please get one of the following levels: ', 'ihc') . implode( ',', $level_can_do );
	}

endif;?>
<span class="ihc-js-user-sites-table-data"
		data-current_url="<?php echo isset( $data['current_url'] ) ? $data['current_url'] : '';?>"
		data-current_question="<?php esc_html_e('Are you sure you want to delete selected Site?', 'ihc');?>"
></span>
