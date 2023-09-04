<?php
function ihc_test_if_must_block($block_or_show, $user_levels, $target_levels, $post_id=-1, $usedLocation='' ){
	/*
	 * return if user must be block of not
	 * @param:
	 * block_or_show = string 'block' or 'show'
	 * $user_levels = string (all current user levels seppareted by comma)
	 * target levels = array (show/hide content for users with these levels)
	 * @return :
	 * 0 - show
	 * 1 - block
	 * 2 - expired
	 */
	global $current_user;
	$block = 0; //SHOW

	if (!$target_levels || (isset($target_levels[0]) && $target_levels[0]=='')){
		$block = apply_filters('filter_on_ihc_test_if_must_block', $block, $block_or_show, $user_levels, $target_levels, $post_id, $usedLocation );
		return $block;
	}
	$registered_user = ($user_levels=='unreg') ? 'other' : 'reg';

	$arr = explode(',', $user_levels);

	if ($target_levels && is_array($target_levels)){
		switch($block_or_show){
			case 'block': {
				$block = 0;
				break;
			}
			case 'show': {
				$block = 1;
				break;
			}
		}

		foreach ($target_levels as $current_user_level){
		  switch($block_or_show){
			case 'block': {
				if(in_array($current_user_level, $arr)){

					//===LEVEL VALIDATION
					$is_expired = ihc_is_user_level_expired($current_user->ID, $current_user_level);
					// :0 - level not expired
					// :1 - level expired

					$is_ontime = ihc_is_user_level_ontime($current_user_level);
					// :0 - level OUT time
					// :1 - level ON time

					if ($is_expired == 0 && $is_ontime == 1){
						//level VALIDATED for BLOCK
						$block = 1;
					//===END LEVEL VALIDATION
					}

				}elseif( in_array('all', $target_levels) || in_array($registered_user, $target_levels) ){
						$block = 1;
				}
				 break;
			}
			case 'show': {
				if(in_array($current_user_level, $arr)){

					//===LEVEL VALIDATION
					$is_expired = ihc_is_user_level_expired($current_user->ID, $current_user_level);
					// :0 - level not expired
					// :1 - level expired

					$is_ontime = ihc_is_user_level_ontime($current_user_level);
					// :0 - level OUT time
					// :1 - level ON time

					if ($is_expired == 0 && $is_ontime == 1){
						//level VALIDATED for SHOW
						$block = 0;
					//===END LEVEL VALIDATION
				  }

				}elseif( in_array('all', $target_levels) || in_array($registered_user, $target_levels)){
						$block = 0;
				}
				if ($block == 0){
					$block = ihc_check_drip_content($current_user->ID, $current_user_level, $post_id);
					$block = apply_filters('filter_on_ihc_test_if_must_block', $block, $block_or_show, $user_levels, $target_levels, $post_id, $usedLocation );
					return $block;
				}
				break;
			}
		  }
		}
	}
	$block = apply_filters('filter_on_ihc_test_if_must_block', $block, $block_or_show, $user_levels, $target_levels, $post_id, $usedLocation );
	return $block;
}

function ihc_is_user_level_expired($u_id, $l_id, $not_started_check=TRUE, $expire_check=TRUE){
	/*
	 * test if user level is expired
	 * @param: user id, level id
	 * @return:
	 * 1 - expired/not available yet
	 * 0 - ok
	 */

	global $wpdb;

	$u_id = esc_sql($u_id);
	$l_id = esc_sql($l_id);

	$grace_period = \Indeed\Ihc\UserSubscriptions::getGracePeriod( $u_id, $l_id );

	$data = \Indeed\Ihc\UserSubscriptions::getStartAndExpireForSubscription( $u_id, $l_id );
	$current_time = indeed_get_unixtimestamp_with_timezone();
	if (!empty($data['start_time']) && $not_started_check){
		$start_time = strtotime($data['start_time']);
		if ($current_time<$start_time){
			//it's not available yet
			return 1;
		}
	}
	if (!empty($data['expire_time']) && $expire_check){
		$expire_time = strtotime($data['expire_time']) + ((int)$grace_period * 24 * 60 *60);
		if ($current_time>$expire_time){
			//it's expired
			return 1;
		}
	}
	return 0;
}

function ihc_is_user_level_ontime($l_id){
	/*
	 * test if user level is on time
	 * @param: level_id
	 * @return:
	 * 1 - ON time - ok
	 * 0 - OFF time
	 */
	$on_time = 1;
	$level_data = ihc_get_level_by_id($l_id);
	if (isset($level_data['special_weekdays'])){
		$day = date( "w", indeed_get_unixtimestamp_with_timezone() );
		if ($level_data['special_weekdays']=='weekdays' && ($day==6 || $day==0) ){
			//WEEK DAYS
			$on_time = 0;
		} else if ($level_data['special_weekdays']=='weekend'  && ($day!=6 && $day!=0) ){
			// WEEK-END
			$on_time = 0;
		}
	}
	return $on_time;
}

function ihc_check_drip_content($uid, $lid, $post_id){
	/*
	 * /////// DRIP CONTENT \\\\\\\\
	 * @param int, int, int
	 * @return int ( 1 : block, 0 : unblock)
	 */
	global $wpdb;
	$uid = esc_sql($uid);
	$lid = esc_sql($lid);
	$post_id = esc_sql($post_id);
	$block = 0;
	if ($post_id>-1){
		$post_meta = ihc_post_metas($post_id);
		if (!empty($post_meta['ihc_drip_content'])){
			 /// DRIP CONTENT ACTIVE
			$current_time = indeed_get_unixtimestamp_with_timezone();

			if ($lid=='unreg'){
				/// drip content for unreg users
				if ($post_meta['ihc_drip_end_type']==3){
					$start_time = strtotime($post_meta['ihc_drip_start_certain_date']);
					$end_time = strtotime($post_meta['ihc_drip_end_certain_date']);
					if ($current_time<$start_time){//to early
						$block = 1;
						$block = apply_filters('filter_on_ihc_check_drip_content', $block, $uid, $lid, $post_id);
						return $block;
					}
					if ($current_time>$end_time){//to late
						$block = 1;
						$block = apply_filters('filter_on_ihc_check_drip_content', $block, $uid, $lid, $post_id);
						return $block;
					}
				}
			} else {
				/// registered users with level or not
				if ($lid=='reg'){
					$query = $wpdb->prepare( "SELECT user_registered FROM {$wpdb->base_prefix}users WHERE ID=%d ", $uid );
					$data = $wpdb->get_row( $query );
					if (!empty($data->user_registered)){
						$subscription_start = strtotime($data->user_registered);
					}
				} else {
					$data = \Indeed\Ihc\UserSubscriptions::getStartAndExpireForSubscription( $uid, $lid );
					if (!empty($data['start_time'])){
						$subscription_start = strtotime($data['start_time']);
					}
				}

				if (!empty($subscription_start)){
					//SET START TIME
					if ($post_meta['ihc_drip_start_type']==1){
						//initial
						$start_time = $subscription_start;
					} else if ($post_meta['ihc_drip_start_type']==2){
						//after
						if ($post_meta['ihc_drip_start_numeric_type']=='days'){
							$start_time = $subscription_start + $post_meta['ihc_drip_start_numeric_value'] * 24 * 60 * 60;
						} else if ($post_meta['ihc_drip_start_numeric_type']=='weeks'){
							$start_time = $subscription_start + $post_meta['ihc_drip_start_numeric_value'] * 7 * 24 * 60 * 60;
						} else {
							$start_time = $subscription_start + $post_meta['ihc_drip_start_numeric_value'] * 30 * 24 * 60 * 60;
						}
					} else {
						//certain date
						$start_time = strtotime($post_meta['ihc_drip_start_certain_date']);
					}
					if (empty($start_time)){
						$start_time = $subscription_start;
					}

					//SET END TIME
					if ($post_meta['ihc_drip_end_type']==1){
						//infinite
						$end_time = $start_time + 3600 * 24 * 60 * 60;// 10years should be enough
					} else if ($post_meta['ihc_drip_end_type']==2){
						//after
						if ($post_meta['ihc_drip_end_numeric_type']=='days'){
							$end_time = $start_time + $post_meta['ihc_drip_end_numeric_value'] * 24 * 60 * 60;
						} else if ($post_meta['ihc_drip_end_numeric_type']=='weeks'){
							$end_time = $start_time + $post_meta['ihc_drip_end_numeric_value'] * 7 * 24 * 60 * 60;
						} else {
							$end_time = $start_time + $post_meta['ihc_drip_end_numeric_value'] * 30 * 24 * 60 * 60;
						}
					} else {
						//certain date
						$end_time = strtotime($post_meta['ihc_drip_end_certain_date']);
					}
					if (empty($end_time)){
						$end_time = $start_time + 3600 * 24 * 60 * 60;
					}

					if ($current_time<$start_time){//to early
						$block = 1;
						$block = apply_filters('filter_on_ihc_check_drip_content', $block, $uid, $lid, $post_id);
						return $block;
					}
					if ($current_time>$end_time){//to late
						$block = 1;
						$block = apply_filters('filter_on_ihc_check_drip_content', $block, $uid, $lid, $post_id);
						return $block;
					}
				}
			}
		}
	}
	$block = apply_filters('filter_on_ihc_check_drip_content', $block, $uid, $lid, $post_id);
	return $block;
}

function ihc_block_url($url, $current_user, $post_id){
	/*
	 * @param string, string, int
	 * @return none
	 */
	if (!$current_user){
		$current_user = ihc_get_user_type();
	}
	if ($current_user=='admin'){
		//admin can view anything
		return;
	}

	if (strpos($url, 'indeed-membership-pro')!==FALSE){
		/// links inside plugin must work everytime!
		return;
	}

	$redirect_link = false;
	$data = get_option('ihc_block_url_entire');
	if ($data){
		//////////////////////// BLOCK URL
		$key = ihc_array_value_exists($data, $url, 'url');
		if ($key!==FALSE){
			if ($data[$key]['target_users']!='' && $data[$key]['target_users']!=-1){
				$target_users = explode(',', $data[$key]['target_users']);
			} else {
				$target_users = FALSE;
			}

			$block_or_show = (isset($data[$key]['block_or_show'])) ? $data[$key]['block_or_show'] : 'block';

			/// used to $block = ihc_test_if_must_block('block', $current_user, $target_users, $post_id); older version
			$block = ihc_test_if_must_block($block_or_show, $current_user, $target_users, $post_id);	//test if user must be block

			if ($block){
				if ($data[$key]['redirect'] && $data[$key]['redirect']!=-1){
					$redirect_link = get_permalink($data[$key]['redirect']);
					if (!$redirect_link){
						$redirect_link = ihc_get_redirect_link_by_label($data[$key]['redirect']);
					}
				} else {
					//if not exists go to homepage
					$redirect_link = get_home_url();
				}
			}
		}
	}
	$data = get_option('ihc_block_url_word');
	if ($data){
		///////////////// BLOCK IF URL CONTAINS A SPECIFIED WORD
		foreach($data as $k=>$arr){
			if (strpos($url, $arr['url'])!==FALSE) {
				if ($arr['target_users']!='' && $arr['target_users']!=-1){
					$target_users = explode(',', $arr['target_users']);
				} else {
					$target_users = FALSE;
				}

				$block_or_show = (isset($arr['block_or_show'])) ? $arr['block_or_show'] : 'block';

				$block = ihc_test_if_must_block($block_or_show, $current_user, $target_users, $post_id);

				if ($block){
					if ($arr['redirect'] && $arr['redirect']!=-1){
						$redirect_link = get_permalink($arr['redirect']);
						if (!$redirect_link){
							$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
						}
					} else {
						//if not exists go to homepage
						$redirect_link = get_home_url();
					}
					break;
				}
			}
		}
	}
	if ($redirect_link){
		$redirect_link = apply_filters( 'ump_filter_block_url_redirect_link', $redirect_link, $post_id );
		wp_redirect($redirect_link);
		exit();
	}
}

function ihc_check_block_rules($url='', $current_user='', $post_id=0){
	/*
	 * @param string, string, int
	 * @return none
	 */
	if (!$current_user){
		$current_user = ihc_get_user_type();
	}
	if ($current_user=='admin'){
		//admin can view anything
		return;
	}
	if (strpos($url, 'indeed-membership-pro')!==FALSE){
		/// links inside plugin must work everytime!
		return;
	}

	/// CHECK BLOCK ALL POST TYPES
	$block_posts = get_option('ihc_block_posts_by_type');

	if (!empty($block_posts)){
		$post_type = get_post_type($post_id);

		foreach ($block_posts as $key=>$array){
			if ($post_type==$array['post_type']){
				$except_arr = array();
				if (!empty($array['except'])){
					$except_arr = explode(',', $array['except']);
				} else {
					$except_arr = array();
				}
				if (!empty($except_arr) && in_array($post_id, $except_arr)){
					continue; /// SKIP THIS RULE
				}
				/// TARGET USERS
				$target_users = FALSE;
				if (!empty($array['target_users']) && $array['target_users']!=-1){
					$target_users = explode(',', $array['target_users']);
				}
				$block_or_show = (isset($array['block_or_show'])) ? $array['block_or_show'] : 'block';
				$block = ihc_test_if_must_block($block_or_show, $current_user, $target_users, $post_id);//test if user must be block

				if ($block){
					if (!empty($array['redirect'])){
						$redirect = $array['redirect'];
					}
					if (!empty($redirect)){
						$redirect_link = get_permalink($redirect);
					}
					if (empty($redirect_link)){
						$redirect_link = get_home_url();
					}
					break;
				}
			}
		}
	}

	/// BLOCK CATS
	$block_terms_data = get_option('ihc_block_cats_by_name');
	if (!empty($block_terms_data)){
		$post_terms = get_terms_for_post_id($post_id);
		if (!empty($post_terms)){
			foreach ($block_terms_data as $key=>$array){
				if (in_array($array['cat_id'], $post_terms)){
					$except_arr = array();
					if (!empty($array['except'])){
						$except_arr = explode(',', $array['except']);
						if ( in_array( $post_id, $except_arr ) ){
								continue;
						}
					}

					/// TARGET USERS
					$target_users = FALSE;
					if (!empty($array['target_users']) && $array['target_users']!=-1){
						$target_users = explode(',', $array['target_users']);
					}

					$block_or_show = (isset($array['block_or_show'])) ? $array['block_or_show'] : 'block';
					$block = ihc_test_if_must_block($block_or_show, $current_user, $target_users, $post_id);//test if user must be block

					if ($block){
						if (!empty($array['redirect'])){
							$redirect = $array['redirect'];
						}
						if (!empty($redirect)){
							$redirect_link = get_permalink($redirect);
						}
						if (empty($redirect_link)){
							$redirect_link = get_home_url();
						}
						break;
					}
				}
			}
		}
	}

	if (empty($redirect_link)){
		$redirect_link = '';
	}
	$redirect_link = apply_filters('filter_on_ihc_block_url', $redirect_link, $url, $current_user, $post_id);

	/// REDIRECT IF IT's CASE
	if (!empty($redirect_link)){
		wp_redirect($redirect_link);
		exit();
	}

}

function ihc_if_register_url($url){
	/*
	 * test if current page is register page
	 * if is register page and lid(level id) is not set redirect to subscription plan (if its set and available one)
	 */

	$reg_page = get_option('ihc_general_register_default_page');

	if ($reg_page && $reg_page!=-1){

		$reg_page_url = get_permalink($reg_page);

		if (strpos($url,$reg_page_url) !== FALSE){
			//current page is register page

			$subscription_type = get_option('ihc_subscription_type');
			if ( $subscription_type=='predifined_level' && !isset( $_GET['lid'] ) ){
				 	return;
			}

			// special condition for elementor - edit page
			if ( current_user_can( 'administrator' ) && isset( $_GET['elementor-preview'] ) && $_GET['elementor-preview'] !== '' ){
					if (!function_exists('is_plugin_active')){
						include_once ABSPATH . 'wp-admin/includes/plugin.php';
					}
					if ( is_plugin_active( 'elementor/elementor.php' ) ){
							return;
					}
			}
			// end of special condition for elementor - edit page

			$lid = isset( $_GET['lid'] ) ? $_GET['lid'] : -1;
			$levels = \Indeed\Ihc\Db\Memberships::getAll();
			//if ( $lid > -1 && isset( $levels[$lid] ) ){
			if ( $lid > -1 && \Indeed\Ihc\Db\Memberships::getOne( $lid ) !== false ){
					$checkResult = ihc_check_level_restricted_conditions( [ $lid => [] ] );
					if ( isset( $checkResult[ $lid ] ) ){
							// user can ask for this level
							return;
					}
			}

			$subscription_pid = get_option('ihc_subscription_plan_page');
			if ($subscription_pid && $subscription_pid!=-1){
				$subscription_link = get_permalink($subscription_pid);
				if ($subscription_link){
					wp_redirect($subscription_link);
					exit();
				}
			}
		}
	}
}

function ihc_block_page_content($postid, $url){
	/*
	 * test if current post, page content must to blocked
	 */
	$meta_arr = ihc_post_metas($postid);
	if(isset($meta_arr['ihc_mb_block_type']) && $meta_arr['ihc_mb_block_type']){
		if($meta_arr['ihc_mb_block_type']=='redirect'){
			/////////////////////// REDIRECT
			if(isset($meta_arr['ihc_mb_who'])){

				//getting current user type and target user types
				$current_user = ihc_get_user_type();
				if($meta_arr['ihc_mb_who']!=-1 && $meta_arr['ihc_mb_who']!=''){
					$target_users = explode(',', $meta_arr['ihc_mb_who']);
				} else {
					$target_users = FALSE;
				}
				//test if current user must be redirect
				if($current_user=='admin'){
					 return;//show always for admin
				}

				$redirect = ihc_test_if_must_block($meta_arr['ihc_mb_type'], $current_user, $target_users, $postid);


				if($redirect){
					//getting default redirect id
					$default_redirect_id = get_option('ihc_general_redirect_default_page');

					//PREVENT INFINITE REDIRECT LOOP - if current page is default redirect page return
					if($default_redirect_id==$postid){
						 return;
					}

					if (isset($meta_arr['ihc_mb_redirect_to']) && $meta_arr['ihc_mb_redirect_to']!=-1){
						$redirect_id = $meta_arr['ihc_mb_redirect_to'];//redirect to value that was selected in meta box
						//test if redirect page exists

						if(get_post_status($redirect_id)){
							$redirect_link = get_permalink($redirect_id);
						} else {
							//custom redirect link
							if (!empty($redirect_id)){
								$redirect_link = ihc_get_redirect_link_by_label($redirect_id);
							}

							if (empty($redirect_link)){
								//if not exists go to homepage
								$redirect_link = home_url();
							}
						}
					} else {
						if ($default_redirect_id && $default_redirect_id!=-1){
							if (get_post_status($default_redirect_id)){
								$redirect_link = get_permalink($default_redirect_id); //default redirect page, selected in general settings
							} else {
								//custom redirect link
								if (!empty($redirect_id)){
									$redirect_link = ihc_get_redirect_link_by_label($redirect_id);
								}
								if (empty($redirect_link)){
									//if not exists go to homepage
									$redirect_link = home_url();
								}
							}
						} else {
							$redirect_link = home_url();//if default redirect page is not set, redirect to home
						}
					}

					if ($url==$redirect_link){
						//PREVENT INFINITE REDIRECT LOOP
						return;
					}

					$redirect_link = apply_filters('filter_on_ihc_link_to_redirect', $redirect_link, $current_user, $target_users, $postid);
					wp_redirect($redirect_link);
					exit();
				}
			}
		}else{
			////////////////////// REPLACE CONTENT, adding filter to block, show only the content
			add_filter('the_content', 'ihc_filter_content');
		}
	}
}

function ihc_init_form_action($url){
	/*
	 * form actions :
	 * REGISTER
	 * LOGIN
	 * UPDATE
	 * RESET PASS
	 * DELETE LEVEL FROM ACCOUNT PAGE
	 * CANCEL LEVEL FROM ACCOUNT PAGE
	 * RENEW LEVEL
	 */
	switch ($_POST['ihcaction']){
		case 'suspend':
			global $current_user;
			if (!empty($current_user->ID)){
				if (ihc_suspend_account($current_user->ID)){
					////// do logout
					///write log
					Ihc_User_Logs::set_user_id($current_user->ID);
					$username = Ihc_Db::get_username_by_wpuid($current_user->ID);
					Ihc_User_Logs::write_log(__('User ', 'ihc') . $username .esc_html__(' suspend his profile.', 'ihc'), 'user_logs');
					require_once IHC_PATH . 'public/logout.php';
					ihc_do_logout($url);
				}
			}
			break;
		case 'login':
			//login
			include_once IHC_PATH . 'public/login.php';
			ihc_login($url);
		break;
		// deprecated, moved to RegisterForm class
		/*
		case 'register':
			///////////////////////////////register
			if (!class_exists('UserAddEdit')){
				include_once IHC_PATH . 'classes/UserAddEdit.class.php';
			}
			$args = array(
					'user_id' => false,
					'type' => 'create',
					'tos' => true,
					'captcha' => true,
					'action' => '',
					'is_public' => true,
					'url' => $url,
			);
			$obj = new UserAddEdit();
			$obj->setVariable($args);//setting the object variables
			$obj->save_update_user();
			break;

		// deprecated, moved to RegisterForm class
		*/
		/*
		case 'register_lite':
		 	if (!class_exists('LiteRegister')){
		 		include_once IHC_PATH . 'classes/LiteRegister.class.php';
		 	}
			$data['metas'] = ihc_return_meta_arr('register_lite');
			$args = array(
					'user_id' => false,
					'type' => 'create',
					'is_public' => true,
					'url' => $url,
					'lite_register_metas' => $data['metas'],
			);
			$object = new LiteRegister();
			$object->setVariable($args);//setting the object variables
			$object->save_update_user();
			break;
		*/

		case 'update':
			/////////////////////// UPDATE
			if (is_user_logged_in()){

				// Profile Form - new implementation starting with 11.0
				$profileEdit = new \Indeed\Ihc\ProfileForm();
				$result = $profileEdit->setUid()
				                      ->setFields()
				                      ->doUpdate( $_POST );
				// end of Profile Form

				/*
        /// Deprecated since version 11.0

				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
				if ($user_id){
					Ihc_User_Logs::set_user_id($current_user->ID);
					$username = Ihc_Db::get_username_by_wpuid($current_user->ID);
					Ihc_User_Logs::write_log(esc_html__('User ', 'ihc') . $username .esc_html__(' update his profile.', 'ihc'), 'user_logs');
					if (!class_exists('UserAddEdit')){
						include_once IHC_PATH . 'classes/UserAddEdit.class.php';
					}
					$args = array(
							'user_id' => $user_id,
							'type' => 'edit',
							'tos' => false,
							'captcha' => false,
							'action' => '',
							'is_public' => true,
					);
					$obj = new UserAddEdit();
					$obj->setVariable($args);
					$obj->save_update_user();
				}
        // end of deprecated !

				*/

			}
		break;
		case 'reset_pass':
			//check nonce
			if ( empty( $_POST['ihc_lost_password_nonce'] ) || !wp_verify_nonce( $_POST['ihc_lost_password_nonce'], 'ihc_lost_password_nonce' ) ){
					return;
			}
			require_once IHC_PATH . 'classes/ResetPassword.class.php';
			$reset_password = new IHC\ResetPassword();
			$reset_password->send_mail_with_link($_REQUEST['email_or_userlogin']);
		break;
		case 'renew_cancel_delete_level_ap':
			global $current_user;

			// ----------------------------- DELETE USER - LEVEL RELATION ---------------------
			if (isset($_POST['ihc_delete_level']) && $_POST['ihc_delete_level']!=''){
				//delete level
				if (isset($current_user->ID)){
					/// user logs
					Ihc_User_Logs::set_user_id($current_user->ID);
					Ihc_User_Logs::set_level_id($_POST['ihc_delete_level']);
					$username = Ihc_Db::get_username_by_wpuid($current_user->ID);
					$level_name = Ihc_Db::get_level_name_by_lid($_POST['ihc_delete_level']);
					Ihc_User_Logs::write_log(__('User ', 'ihc') . $username .esc_html__(' delete Level ', 'ihc') . $level_name, 'user_logs', $_POST['ihc_delete_level']);
					\Indeed\Ihc\UserSubscriptions::deleteOne( $current_user->ID, $_POST['ihc_delete_level'] );
				}
			}
			// ----------------------------- END OF DELETE USER - LEVEL RELATION ---------------------

			// ------------------------ CANCEL LEVEL --------------------------------
			if (isset($_POST['ihc_cancel_level']) && $_POST['ihc_cancel_level']!=''){
				//////////////cancel level
				/// user logs
				Ihc_User_Logs::set_user_id($current_user->ID);
				Ihc_User_Logs::set_level_id($_POST['ihc_cancel_level']);
				$username = Ihc_Db::get_username_by_wpuid($current_user->ID);
				$level_name = Ihc_Db::get_level_name_by_lid($_POST['ihc_cancel_level']);
				Ihc_User_Logs::write_log(__('User ', 'ihc') . $username .esc_html__(' cancel Level ', 'ihc') . $level_name, 'user_logs', $_POST['ihc_cancel_level']);

				$cancel = new \Indeed\Ihc\Payments\CancelSubscription();
				$cancel->setUid( $current_user->ID )
							 ->setLid( esc_sql( $_POST['ihc_cancel_level'] ) )
							 ->proceed();


			}
			// ----------------------------- END OF CANCEL LEVEL ----------------------------

			// ----------------------- FINISH PAYMENT ------------------------------
			if ( isset( $_POST['ihc_finish_payment_level'] ) && $_POST['ihc_finish_payment_level'] ){
					// getting payment type
					$orderId = isset( $_POST['order_id'] ) ? esc_sql( $_POST['order_id'] ) : false;
					$orderMeta = new \Indeed\Ihc\Db\OrderMeta();
					$paymentType = $orderMeta->get( $orderId, 'ihc_payment_type' );
					if ( $paymentType == false ){
							$paymentType = isset( $_POST['ihc_payment_gateway'] ) ? esc_sql( $_POST['ihc_payment_gateway'] ) : '';
					}

					$newWay = [
											'stripe_checkout_v2',
											'pagseguro',
											'paypal_express_checkout',
											'mollie',
											'paypal',
											'bank_transfer',
											'twocheckout',
					];
					if ( in_array( $paymentType, $newWay )){
							// new way
							$finishPayment = new \Indeed\Ihc\Payments\FinishUnpaidPayments();
							$finishPayment->setInput([
																					'payment_type'			=> $paymentType,
																					'uid'								=> $current_user->ID,
																					'lid'								=> esc_sql( $_POST['ihc_finish_payment_level'] ),
																					'order_id'					=> $orderId,
														])
														->doIt();
					} else {
							// old way
							if ( ihc_check_payment_available( $paymentType ) ){
									ihc_renew_level( $current_user->ID, $_POST['ihc_finish_payment_level'], $paymentType );
							}
					}
			}
			// ---------------------- END OF FINISH PAYMENT ------------------------------

			// ----------------------- RENEW LEVEL
			// Deprecated since vesio 10.5.1
			if ( isset( $_POST['ihc_renew_level'] ) && $_POST['ihc_renew_level'] ){
					$paymentType = isset( $_POST['ihc_payment_gateway'] ) ? esc_sql( $_POST['ihc_payment_gateway'] ) : '';
					if ( ihc_check_payment_available( $paymentType ) ){
							ihc_renew_level( $current_user->ID, $_POST['ihc_renew_level'], $paymentType );
					}
			}
			// ---------------------- END OF RENEW LEVEL

		break;

		default:
			do_action( 'ihc_action_public_post', $_POST['ihcaction'], $_POST );
			break;

	}
}//end of ihc_init_form_action()

function ihc_do_pay_new_level(){
	/*
	 * @param none
	 * @return none
	 */
	if (isset($_REQUEST['lid'])){
		global $current_user;
		if (isset($current_user->ID)){	//only if we have a user id to proceed
			$uid = $current_user->ID;
			$return_url = (isset($_GET['urlr'])) ? urldecode($_GET['urlr']) : '';
			$level_data = ihc_get_level_by_id($_GET['lid']);
			if (!class_exists('UserAddEdit')){
				require_once IHC_PATH . 'classes/UserAddEdit.class.php';
			}
			$coupon = isset( $_REQUEST['ihc_coupon'] ) ? esc_attr( $_REQUEST['ihc_coupon'] ) : '';
			$args = array(
					'user_id' => $uid,
					'type' => 'edit',
					'tos' => false,
					'captcha' => false,
					'action' => '',
					'is_public' => true,
			);
			$obj = new UserAddEdit();
			$obj->setVariable($args);//setting the object variables
			$obj->set_coupon( $coupon );
			$obj->update_level($return_url);
			$obj->save_coupon();
		}
	}
}

function ihc_add_stripe_public_form($content='', $doPrint=false){
	/*
	 * @param string
	 * @return string
	 */
 	global $current_user;
	$publishable_key = get_option('ihc_stripe_publishable_key');
	$uid = (!empty($current_user) && !empty($current_user->ID)) ? $current_user->ID : 0;
	$email = empty($current_user->user_email) ? '' : $current_user->user_email;
	$top_logo = get_option('ihc_stripe_popup_image');
	$button_label = get_option('ihc_stripe_bttn_value');
	$locale_code = get_option('ihc_stripe_locale_code');
	if ($locale_code){
			$locale = $locale_code;
	} else {
			$locale = "auto";
	}
	if ($top_logo){
			$image = $top_logo;
	} else {
			$image = '';
	}
	if ($button_label){
			$bttn = $button_label;
	} else {
			$bttn = '';
	}

	$currency = get_option('ihc_currency');
	$multiply = ihcStripeMultiplyForCurrency( $currency );
	//$ajaxURL = IHC_URL . 'public/ajax-custom.php?ihcpublicn=' . wp_create_nonce( 'ihcpublicn' );
	$ajaxURL = get_site_url() . '/wp-admin/admin-ajax.php?action=ihc_ajax_public_custom_ajax_gate&ihcpublicn=' . wp_create_nonce( 'ihcpublicn' );

	$str = '';
	$str .= '<form method="post" class="ihc-stripe-form-payment" >';
	$str .= '<input type="hidden" name="uid" value="'.$current_user->ID.'" />';
	$str .= '<input type="hidden" name="lid" id="ihc_lid_stripe" value="" />';
	$str .= '</form>';
	$str .= '<span class="ihc-js-stripe-v1-data"
							data-key="' . $publishable_key . '"
							data-locale="' . $locale . '"
							data-image="' . $image . '"
							data-bttn="' . $bttn . '"
							data-email="' . $email . '"
							data-multiply="' . $multiply . '"
							data-target_url="' . $ajaxURL . '"
							data-uid="' . $uid . '"
							data-blogname="' . get_option( 'blogname' ) . '"
							data-currency="' . $currency . '"
							data-form=".ihc-stripe-form-payment"
	></span>';
	wp_enqueue_script( 'ihc-stripe-checkout', "https://checkout.stripe.com/checkout.js", [ 'jquery' ], 11.2 );
	wp_enqueue_script( 'ihc-stripe-custom', IHC_URL . 'assets/js/stripe.js', [ 'jquery' ], 11.2 );

	if ($doPrint){
			echo do_shortcode($content) . $str;
			return;
	}
	return do_shortcode($content) . $str;
}

function ihc_pay_new_lid_with_stripe($request=array()){
	/*
	 * @param array
	 * @return boolean
	 */
	 $ihc_dont_pay_after_discount = false;
	if (isset($request['stripeToken']) && isset($request['stripeEmail']) && isset($request['lid']) && isset($request['uid']) ) {

		if (!class_exists('ihcStripe')){
			require_once IHC_PATH . 'classes/PaymentGateways/ihcStripe.class.php';
		}

		$lid = $request['lid'];
		$uid = $request['uid'];
		$level_data = ihc_get_level_by_id($lid);
		$post_data = array(
				'lid' 					=> $lid,
				'uid' 					=> $uid,
				'stripeToken' 	=> $request['stripeToken'],
				'stripeEmail' 	=> $request['stripeEmail'],
		);
		$post_data['ihc_coupon'] = (!empty($request['ihc_coupon'])) ? $request['ihc_coupon'] : '';
		$taxes_settings = ihc_return_meta_arr('ihc_taxes_settings');
		if (!empty($taxes_settings['ihc_enable_taxes'])){
			$post_data['ihc_country'] = get_user_meta($uid, 'ihc_country', TRUE);
		}
		if (ihc_dont_pay_after_discount($lid, (isset($_REQUEST['ihc_coupon'])) ? $_REQUEST['ihc_coupon'] : '', $level_data, TRUE)){
			// 0 amount to pay
			$ihc_dont_pay_after_discount = true;

			$succees = \Indeed\Ihc\UserSubscriptions::assign( $uid, $lid );
			\Indeed\Ihc\UserSubscriptions::makeComplete( $uid, $lid, false, [ 'payment_gateway' => 'stripe' ] );
			//v.7.1 Do not insert Orders with 0 amount anymore.
		} else {
			//payment
			$payment_obj = new ihcStripe();
			$pay_result = $payment_obj->charge($post_data, TRUE);
			if ( isset( $pay_result['message'] ) && $pay_result['message'] ) { // == "success"

				$trans_id = $pay_result['trans_id'];
				$trans_info = $pay_result;
				$trans_info['ihc_payment_type'] = 'stripe';
				ihc_insert_update_transaction($uid, $trans_id, $trans_info, TRUE);
			}
		}

		if($ihc_dont_pay_after_discount == FALSE){
			$succees = \Indeed\Ihc\UserSubscriptions::assign( $uid, $lid );
		}

		if ($succees){
			return TRUE;
		}
	}
}

function ihc_pay_new_lid_with_authorize($uid=0, $data=array()){
	/*
	 * Used only in buy new level (recurring) from account page and subscription plan page for registered users.
	 * @param uid - int, data - array
	 * @return boolean
	 */
	$level_data = ihc_get_level_by_id($data['lid']);

	if (!class_exists('ihcAuthorizeNet')){
		require_once IHC_PATH . 'classes/PaymentGateways/ihcAuthorizeNet.class.php';
	}
	$auth_pay = new ihcAuthorizeNet();
	$charge = $auth_pay->charge($data);

	if ($charge){
		$pay_result = $auth_pay->subscribe($data);
		if ($pay_result['code'] == 2){
			$trans_id = $pay_result['trans_id'];
			$trans_info = $pay_result;
			$trans_info['ihc_payment_type'] = 'authorize';

			$succees = \Indeed\Ihc\UserSubscriptions::assign( $uid, $data['lid'] );
			if ($succees){
				\Indeed\Ihc\UserSubscriptions::makeComplete( $uid, $data['lid'], false, [ 'payment_gateway' => 'authorize' ] );
				do_action( 'ihc_payment_completed', $uid, $data['lid'] );
				// @description run on payment complete. @param user id (integer), level id (integer)

				ihc_insert_update_transaction( $uid, $trans_id, $trans_info );
				return TRUE;
			}
		}
	}
	return FALSE;
}

/*
 * Deprecated since version 10.5.1
 */
function ihc_renew_level($u_id, $l_id, $payment_type=''){
	if (!$payment_type){
		$payment_type = get_option('ihc_payment_selected');
	}
	$taxes_settings = ihc_return_meta_arr('ihc_taxes_settings');
	if (!empty($taxes_settings['ihc_enable_taxes'])){
		$ihc_country = get_user_meta($u_id, 'ihc_country', TRUE);
	}

	switch ($payment_type){
		case 'stripe':
			if (!class_exists('ihcStripe')){
				require_once IHC_PATH . 'classes/PaymentGateways/ihcStripe.class.php';
			}
			$payment_obj = new ihcStripe();
			$post_data['stripeEmail'] = $_REQUEST['stripeEmail'];
			$post_data['stripeToken'] = $_REQUEST['stripeToken'];
			$post_data['lid'] = $l_id;
			$post_data['uid'] = $u_id;
			if (!empty($_REQUEST['ihc_coupon'])){
				$post_data['ihc_coupon'] = $_REQUEST['ihc_coupon'];
			}
			if (isset($ihc_country)){
				$post_data['ihc_country'] = $ihc_country;
			}
			$pay_result = $payment_obj->charge($post_data);
			$trans_id = $pay_result['trans_id'];
			$trans_info = $pay_result;
			$trans_info['ihc_payment_type'] = 'stripe';
			insert_order_from_renew_level($u_id, $l_id, (isset($_REQUEST['ihc_coupon'])) ? $_REQUEST['ihc_coupon'] : '', (isset($ihc_country)) ? $ihc_country : '', $payment_type, 'pending');
			ihc_insert_update_transaction($u_id, $trans_id, $trans_info);
			break;
		case 'twocheckout':
					$options = array(
							'uid'										=> $u_id,
							'lid'										=> $l_id,
							'ihc_coupon'	  				=> isset( $_POST['ihc_coupon']) ? esc_sql($_POST['ihc_coupon']) : '',
							'ihc_country'						=> isset( $ihc_country ) ? $ihc_country : '',
							'ihc_state'							=> get_user_meta( $u_id, 'ihc_state', true ),
							'ihc_dynamic_price'			=> false,
							'defaultRedirect'				=> '',
							'is_register'						=> false,
					);
					$paymentObject = new \Indeed\Ihc\DoPayment( $options, $payment_type );
					$paymentObject->processing();

			break;
		case 'authorize':
			$level_data = ihc_get_level_by_id($l_id);
			if(isset($level_data['access_type']) && $level_data['access_type']=='regular_period'){
				$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ///  $_SERVER['SERVER_NAME']
			}else{
				$url = IHC_URL . 'classes/PaymentGateways/authorize_payment.php';
			}
			$url = add_query_arg('ihc_authorize_fields', 1, $url);
			$url = add_query_arg('lid', $l_id, $url);
			$url = add_query_arg('uid', $u_id, $url);
			if (!empty($_REQUEST['ihc_coupon'])){
				$url = add_query_arg('ihc_coupon', $_REQUEST['ihc_coupon'], $url);
			}
			if (isset($ihc_country)){
				$url = add_query_arg('ihc_country', $ihc_country, $url);
			}
			insert_order_from_renew_level($u_id, $l_id, (isset($_REQUEST['ihc_coupon'])) ? $_REQUEST['ihc_coupon'] : '', (isset($ihc_country)) ? $ihc_country : '', $payment_type, 'pending');
			wp_redirect($url);
			exit();
			break;
		case 'bank_transfer':
					$options = array(
							'uid'										=> $u_id,
							'lid'										=> $l_id,
							'ihc_coupon'	  				=> isset( $_POST['ihc_coupon']) ? esc_sql($_POST['ihc_coupon']) : '',
							'ihc_country'						=> isset( $ihc_country ) ? $ihc_country : '',
							'ihc_state'							=> get_user_meta( $u_id, 'ihc_state', true ),
							'ihc_dynamic_price'			=> false,
							'defaultRedirect'				=> '',
							'is_register'						=> false,
					);
					$paymentObject = new \Indeed\Ihc\DoPayment( $options, $payment_type );
					$paymentObject->processing();


			break;
		case 'braintree':
			$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ///  $_SERVER['SERVER_NAME']
			$url = add_query_arg('lid', $l_id, $url);
			$url = add_query_arg('uid', $u_id, $url);
			if (!empty($_REQUEST['ihc_coupon'])){
				$url = add_query_arg('ihc_coupon', $_REQUEST['ihc_coupon'], $url);
			}
			if (isset($ihc_country)){
				$url = add_query_arg('ihc_country', $ihc_country, $url);
			}
			$url = add_query_arg('ihc_braintree_fields', 1, $url);
			insert_order_from_renew_level($u_id, $l_id, (isset($_REQUEST['ihc_coupon'])) ? $_REQUEST['ihc_coupon'] : '', (isset($ihc_country)) ? $ihc_country : '', $payment_type, 'pending');
			wp_redirect($url);
			exit();
			break;
		default:
			$options = array(
					'uid'										=> $u_id,
					'lid'										=> $l_id,
					'ihc_coupon'	  				=> isset( $_POST['ihc_coupon']) ? esc_sql($_POST['ihc_coupon']) : '',
					'ihc_country'						=> isset( $ihc_country ) ? $ihc_country : '',
					'ihc_state'							=> get_user_meta( $u_id, 'ihc_state', true ),
					'ihc_dynamic_price'			=> false,
					'defaultRedirect'				=> '',
					'is_register'						=> false,
			);
			$paymentObject = new \Indeed\Ihc\DoPayment( $options, $payment_type );
			$paymentObject->processing();
			break;
	}
}


function ihc_authorize_reccuring_payment(){
	/*
	 * @param none
	 * @return string
	 */
		if (isset($_POST['ihc_submit_authorize'])){
			global $current_user;
			$paid = ihc_pay_new_lid_with_authorize($current_user->ID, $_REQUEST);
			if ($paid){
				return esc_html__("Payment Complete", 'ihc');
			} else {
				return esc_html__("An error have occured. Please try again later!", 'ihc');
			}
		} else {
			if (!class_exists('ihcAuthorizeNet')){
				require_once IHC_PATH . 'classes/PaymentGateways/ihcAuthorizeNet.class.php';
			}
			$auth_pay = new ihcAuthorizeNet();
			$str = '';
			$str .= '<form method="post">';
			$str .= '<div id="ihc_authorize_r_fields">';
			$str .= '<div class="ihc_payment_details">'.esc_html__('Complete Payment with Authorize', 'ihc').'</div>';
			$str .=  $auth_pay->payment_fields();
			$str .= '</div>';
			$str .= '<input type="hidden" value="' . $_GET['lid'] . '" name="lid" />';
			if (!empty($_GET['ihc_coupon'])){
				$str .= '<input type="hidden" value="' . $_GET['ihc_coupon'] . '" name="ihc_coupon" />';
			}
			$str .= '<div>';
			$str .= indeed_create_form_element(array('type'=>'submit', 'name'=>'ihc_submit_authorize', 'value' =>esc_html__('Submit', 'ihc'),
					'class' => 'button button-primary button-large', 'id'=>'ihc_submit_authorize' ));
			$str .= '</div>';
			$str .= '</form>';
			return $str;
		}
	return '';
}

function ihc_braintree_payment_for_reg_users(){
	/*
	 * @param none
	 * @return string
	 */
	if (isset($_POST['ihc_submit_braintree'])){
		global $current_user;
		$post_data = $_REQUEST;
		$post_data['uid'] = (isset($current_user->ID)) ? $current_user->ID : 0;
		\Indeed\Ihc\UserSubscriptions::assign( $post_data['uid'], $post_data['lid'] );
		if ( version_compare( phpversion(), '7.2', '>=' ) ){
				// braintree v2
				require_once IHC_PATH . 'classes/PaymentGateways/Ihc_Braintree_V2.class.php';
				$braintree = new Ihc_Braintree_V2();
		} else {
				// braintree v1
				require_once IHC_PATH . 'classes/PaymentGateways/Ihc_Braintree.class.php';
				$braintree = new Ihc_Braintree();
		}
		$paid = $braintree->do_charge($post_data);
		if ($paid){
			return esc_html__("Payment Complete", 'ihc');
		} else {
			return esc_html__("An error have occured. Please try again later!", 'ihc');
		}
	} else {
		require_once IHC_PATH . 'classes/PaymentGateways/Ihc_Braintree.class.php';
		$braintree = new Ihc_Braintree();
		$str = '';
		$str .= '<form method="post" >';
		$str .= '<div id="ihc_braintree_r_fields">';
		$str .= '<div class="ihc_payment_details">' .esc_html__('Complete Payment with Braintree', 'ihc') . '</div>';
		$str .=  $braintree->get_form();
		$str .= '</div>';
		$str .= '<input type="hidden" value="' . $_GET['lid'] . '" name="lid" />';
		if (!empty($_GET['ihc_coupon'])){
			$str .= '<input type="hidden" value="' . $_GET['ihc_coupon'] . '" name="ihc_coupon" />';
		}
		$str .= '<div>';
		$str .= indeed_create_form_element(array('type'=>'submit', 'name'=>'ihc_submit_braintree', 'value' =>esc_html__('Submit', 'ihc'),
				'class' => 'button button-primary button-large', 'id'=>'ihc_submit_braintree' ));
		$str .= '</div>';
		$str .= '</form>';
		return $str;
	}
	return '';
}

function ihc_check_individual_page_block($post_id=0){
	/*
	 * TRUE if must block
	 * @param int
	 * @return boolean
	 */
	 global $current_user;
	 $user_type = ihc_get_user_type();
	 if ($user_type!='admin'){
		 $uid = (isset($current_user->ID)) ? $current_user->ID : 0;
		 if ($post_id){
		 	 $individual_page = get_post_meta($post_id, 'ihc_individual_page', TRUE);
			 if ($individual_page && $individual_page!=$uid){
			 	return TRUE;
			 }
		 }
	 }
	 return FALSE;
}

function ihc_do_block_if_individual_page($post_id=0){
	/*
	 * Do REDIRECT IF IT's CASE
	 * @param int
	 * @return none
	 */
	if ($post_id && ihc_is_magic_feat_active('individual_page')){
		$is_individual_page = ihc_check_individual_page_block($post_id);
		if ($is_individual_page){
			$default_redirect_id = get_option('ihc_general_redirect_default_page');
			if ($default_redirect_id){
				$redirect_link = get_permalink($default_redirect_id);
			}
			if (empty($redirect_link)){
				$redirect_link = home_url();
			}
			wp_redirect($redirect_link);
			exit();
		}
	}
}
