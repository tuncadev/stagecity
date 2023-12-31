<?php
function ihc_filter_content($content){
	/*
	 * @param string
	 * @return string
	 */
	//GETTING POST META
	global $post;
	if($post==FALSE || !isset($post->ID)){
		 return do_shortcode($content);
	}
	$meta_arr = ihc_post_metas($post->ID);
	if($meta_arr['ihc_mb_block_type']=='redirect'){
		 return do_shortcode($content);///this extra check it's for ihc_list_posts_filter(),
	}

	///GETTING USER TYPE
	$current_user = ihc_get_user_type();
	if($current_user=='admin'){
		 return do_shortcode($content);//show always for admin
	}

	// who can access the content
	if (isset($meta_arr['ihc_mb_who'])){
		if ($meta_arr['ihc_mb_who']!=-1 && $meta_arr['ihc_mb_who']!=''){
			$target_users = explode(',', $meta_arr['ihc_mb_who']);
		} else {
			$target_users = FALSE;
		}
	}else{
		return do_shortcode($content);
	}

	////TESTING USER
	$block = ihc_test_if_must_block($meta_arr['ihc_mb_type'], $current_user, $target_users, (isset($post->ID)) ? $post->ID : -1);

	//IF NOT BLOCKING, RETURN THE CONTENT
	if(!$block){
		return do_shortcode($content);
	}
	// REPLACE CONTENT
	if (isset($meta_arr['ihc_replace_content'] )){
		$meta_arr['ihc_replace_content'] = stripslashes($meta_arr['ihc_replace_content']);
		$meta_arr['ihc_replace_content'] = htmlspecialchars_decode($meta_arr['ihc_replace_content']);
		$meta_arr['ihc_replace_content'] = ihc_format_str_like_wp($meta_arr['ihc_replace_content']);
		$meta_arr['ihc_replace_content'] = apply_filters('filter_on_ihc_replace_content', $meta_arr['ihc_replace_content'], (isset($post->ID)) ? $post->ID : -1);

		global $wp_embed;
		$meta_arr['ihc_replace_content'] = $wp_embed->run_shortcode($meta_arr['ihc_replace_content']);

		return do_shortcode($meta_arr['ihc_replace_content']);
	}

	//IF SOMEHOW IT CAME UP HERE, RETURN CONTENT
	return do_shortcode($content);
}

add_filter('ihc_update_profile_form_html', 'ihc_message_update_profile_form_html', 1, 1);
function ihc_message_update_profile_form_html($content='')
{
		if (!empty($_REQUEST['ihc_register']) && $_REQUEST['ihc_register']=='update_message'){
				return '<div class="ihc-reg-update-msg">' . ihc_correct_text(get_option('ihc_general_update_msg')) . '</div>' . $content;
		}
		return $content;
}

//Change Password
add_filter('ihc_update_password_form_html', 'ihc_message_update_password_form_html', 1, 1);
function ihc_message_update_password_form_html($content='')
{
		if (!empty($_REQUEST['ihc_register']) && $_REQUEST['ihc_register']=='update_password'){
				return '<div class="ihc-reg-update-msg">' . ihc_correct_text(get_option('ihc_general_update_msg')) . '</div>' . $content;
		}
		return $content;
}

add_filter('the_content', 'ihc_print_message', 99);
function ihc_print_message($content){
	/*
	 * print success message after register
	 * print update message on edit user page
	 * print the step 2. of registration (Subscription Plan)
	 * print the bank transfer message
	 */
	$str = '';
	 if (isset($_REQUEST['ihc_register'])){
		 switch ( $_REQUEST['ihc_register'] ){
			case 'create_message':
				$str .= '<div class="ihc-reg-success-msg">' . ihc_correct_text(get_option('ihc_register_success_meg')) . '</div>';
			break;
			case 'step2':
				$str .= ihc_user_select_level();
			break;
		 }
	 }
	 global $stop_printing_bt_msg;
	 if (isset($_REQUEST['ihcbt']) && isset($_REQUEST['ihc_lid']) && isset($_REQUEST['ihc_uid']) && empty( $stop_printing_bt_msg ) ){
	 	$str .= ihc_print_bank_transfer_order($_REQUEST['ihc_uid'], $_REQUEST['ihc_lid']);
		$stop_printing_bt_msg = true;
	 }
	 $content .= $str;
	 return do_shortcode($content);
}

//////////////// MENU FILTER
add_action('wp_nav_menu_objects', 'ihc_custom_menu_filter');
function ihc_custom_menu_filter($items){
	global $post;
	$current_user = ihc_get_user_type();
	if ($current_user=='admin'){
		return $items;//show all to admin
	}

	$arr = array();
	foreach ($items as $item){
		$for = (isset($item->ihc_mb_who_menu_type)) ? $item->ihc_mb_who_menu_type : '';
		$type = (isset($item->ihc_menu_mb_type)) ? $item->ihc_menu_mb_type : '';
		if ($for!=-1 && $for!=''){
			$for = explode(',', $for);
		} else {
			$for = FALSE;
		}
		$block = ihc_test_if_must_block($type, $current_user, $for, (isset($item->ID)) ? $item->ID : -1, 'wp_menu' );//test user
		if (!$block){
			/// individual page check
			$block = ihc_check_individual_page_block((isset($item->ID)) ? $item->ID : 0);
		}
		if (!$block){
			$arr[] = $item;
		}
	}
	return $arr;
}

////////LIST POSTS FILTER TO BLOCK THE CONTENT
add_filter('the_content', 'ihc_list_posts_filter');
function ihc_list_posts_filter($str){
	if( !is_single() && !is_page() ){
		return ihc_filter_content($str);
	}
	return $str;
}

//////////LIST POSTS - FILTER REMOVE POSTS THAT HAS A REDIRECT BLOCK
add_filter('pre_get_posts', 'ihc_filter_query_list_posts', 999);
function ihc_filter_query_list_posts($query) {
	/*
	 * @param object
	 * @return object
	 */
	 if (get_option('ihc_listing_show_hidden_post_pages')){
	  		return $query;
	  }
	  if (function_exists('is_bbpress') && get_post_type() == 'topic'){
	  		return $query;
	  }

	  if ($query->is_single || $query->is_page) {
			return $query;
	  } else {
	  		$current_user = ihc_get_user_type();
			if ($current_user=='admin'){
				return $query; /// ADMIN CAN VIEW ANYTHING
			}

	  		global $iump_posts_not_in;
			if (empty($iump_posts_not_in)){
				global $wpdb;
				$data = $wpdb->get_results('SELECT a.post_id,
												CASE a.meta_key WHEN  "ihc_mb_type"  THEN a.meta_value END AS type,
												CASE a.meta_key WHEN "ihc_mb_who" THEN a.meta_value END AS who,
												CASE a.meta_key WHEN "ihc_mb_block_type" THEN a.meta_value END as block_type
												FROM '.$wpdb->prefix.'postmeta a
												where (a.meta_key = "ihc_mb_type"  OR a.meta_key = "ihc_mb_who" OR a.meta_key="ihc_mb_block_type") AND a.meta_value <> ""
												AND a.meta_value!="replace"
												ORDER BY a.post_id'
				);



				if (!empty($data) && is_array($data)){
					$iump_posts_not_in = array();
					$posts = array();

					foreach ($data as $object){
						$post_id = $object->post_id;
						if (!empty($object->who)){
							$posts[$post_id]['who'] = $object->who;
						} else if (!empty($object->type)){
							$posts[$post_id]['type'] = $object->type;
						}


						//////////
						$block = 0;
						if (!empty($posts[$post_id]) && !empty($posts[$post_id]['who']) && !empty($posts[$post_id]['type'])){
							$for = explode(',', $posts[$post_id]['who']);
							$block = ihc_test_if_must_block($posts[$post_id]['type'], $current_user, $for, $post_id);
							if ($block){
								$iump_posts_not_in[] = $post_id;
							}

							unset($posts[$post_id]);
						}
					}

					unset($posts);
					unset($data);
				}
			}

			if (!empty($iump_posts_not_in)){
					$query->set( 'post__not_in', $iump_posts_not_in );
			}

			/// post types
			$LockRules = new Indeed\Ihc\LockRules();
			$disabledPostTypes = $LockRules->setPostId( 0 )->getBlockedPostTypes();
			if ( $disabledPostTypes ){
      		$searchablePostTypes = get_post_types( array( 'exclude_from_search' => false ) );
					foreach ( $disabledPostTypes as $disabledPostType ){
							if ( isset( $searchablePostTypes[$disabledPostType] ) ){
									unset( $searchablePostTypes[$disabledPostType] );
							}
					}
					$query->set( 'post_type', $searchablePostTypes );
			}

	 }
	 return $query;
}


function ihc_filter_print_bank_transfer_message($content = ''){
	/*
	 * @param string
	 * @return string
	 */
	global $stop_printing_bt_msg;
	$str = '';
	if (isset($_GET['ihc_lid']) ){ // && empty($stop_printing_bt_msg)
		global $current_user;
		$str = ihc_print_bank_transfer_order($current_user->ID, esc_sql($_GET['ihc_lid']) );
		$stop_printing_bt_msg = true;
	}
	return do_shortcode ($content) . $str;
}

function ihc_filter_reccuring_authorize_payment($content=''){
	/*
	 * @param string
	 * @return string
	 */
	return $content . ihc_authorize_reccuring_payment();
}

function ihc_filter_reccuring_braintree_payment($content=''){
	/*
	 * @param string
	 * @return string
	 */
	return $content . ihc_braintree_payment_for_reg_users();
}

add_filter('dlm_can_download', 'ihc_download_monitor_filter', 1, 999);
function ihc_download_monitor_filter($do_it){
	/*
	 * @param boolean
	 * @return boolean
	 */
	if ($do_it){
		if (get_option('ihc_download_monitor_enabled')){
			$type = get_option('ihc_download_monitor_limit_type');
			$values_per_level = get_option('ihc_download_monitor_values');
			$current_user_type = ihc_get_user_type();
			if ($current_user_type=='admin'){
				return $do_it;
			}
			global $current_user;
			if ($current_user_type!='' && $current_user_type!='unreg' && !empty($current_user->ID)){
				$user_count_value = Ihc_Db::download_monitor_get_count_for_user($current_user->ID, $type);
				if ($current_user_type=='reg'){
					/// registered users with no active level
					if (isset($values_per_level['level_reg']) && $values_per_level['level_reg']!=''){
						$return_value = FALSE;
						if ($user_count_value<$values_per_level['level_reg']){
							$return_value = $do_it;
						}
					}
				} else {
					/// users with levels
					$user_levels = explode(',', $current_user_type);
					if ($user_levels){
						$return_value = FALSE;
						foreach ($user_levels as $level){
							$is_expired = ihc_is_user_level_expired($current_user->ID, $level);
							$is_ontime = ihc_is_user_level_ontime($level);

							$dynamic_limit = Ihc_Db::ihc_download_monitor_get_user_limit($current_user->ID, $level);
							if ($dynamic_limit==FALSE){
								$value_to_test = (isset($values_per_level['level_' . $level])) ? $values_per_level['level_' . $level] : '';
							} else {
								$value_to_test = $dynamic_limit;
							}

							if (!$is_expired && $is_ontime && $value_to_test!=''){
								if ($user_count_value<$value_to_test){
									$return_value = $do_it;
									break; /// if one level can get file, get out from loop
								}
							}
						}
					}
				}
			}
			if (isset($return_value)){
				return $return_value;
			}
		}
	}
	return $do_it;
}
