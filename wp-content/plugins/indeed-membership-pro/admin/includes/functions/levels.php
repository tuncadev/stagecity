<?php
//////////////////////////////LEVELS
function ihc_save_level($post_data=array(), $install=FALSE){
	/*
	 * @param array
	 * @return none
	 */
	if (isset($post_data['name']) && $post_data['name']!=''){

		$option_name = 'ihc_levels';
		$data = get_option($option_name);
		$oldLogs = new \Indeed\Ihc\OldLogs();
		if ( !empty($data) && is_array( $data ) && count($data)>=3 && $oldLogs->FGCS() === '1' ){
			if (!$install){
				echo '<div class="ihc-admin-err-level">' . esc_html__("You cannot add more than one level on Trial Version!", 'ihc') . '</div>';
			}
			return;
		}
		$arr = array(
							'name'=>'',
							'payment_type'=>'',
							'price'=>'',
						    'label'=>'',
								'short_description' => '',
							'description'=>'',
							'price_text' => '',
							'button_label' => '',
							'order' => '',
							'access_type' => 'unlimited',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'D',
							'access_regular_time_value' => '',
							'billing_type' => '',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_action' => 0,
							'afterexpire_level' => -1,
							'aftercancel_action' => 0,
							'aftercancel_level' => -1,
							'grace_period' => '',
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',
							'access_trial_type' => 1,
							///magic feat
							'badge_image_url' => '',
		);

		$arr = apply_filters('ihc_save_level_meta_names_filter', $arr);
		// @description filter fired when save level. @param array with level data

		foreach ($arr as $k=>$v){
			$arr[$k] = (isset($post_data[$k])) ? $post_data[$k] : '';
		}

		//if it's not regular period type of level ... force billing_type to be bl_onetime
		if (isset($arr['access_type']) && $arr['access_type']!='regular_period'){
			$arr['billing_type'] = 'bl_onetime';
		}

		$arr = apply_filters('ihc_save_level_filter', $arr);

		if ($data!==FALSE){
			if (isset($post_data['level_id']) && $post_data['level_id']!=''){
				//update level
				$id = $post_data['level_id'];
			} else {

				$id = ihc_get_biggest_key_from_array($data);
				$id++;
				$arr['name'] = ihc_make_string_simple($arr['name']);
			}
			$check = ihc_array_value_exists($data, $post_data['name'], 'name');
			if ($check!==FALSE && $check!=$id){
				if (!$install){
					echo '<div class="ihc-admin-err-level">' . esc_html__("A Level with this name ", 'ihc') . $post_data['name'] . esc_html__(" already exists! Please choose another name!", 'ihc') . '</div>';
				}
				return 0;
			}
			$data[$id] = $arr;
			update_option($option_name, $data);
			return $id;
		} else {
			//create the first level
			$data[1] = $arr;
			update_option($option_name, $data);
			return 1;
		}
	}
}
