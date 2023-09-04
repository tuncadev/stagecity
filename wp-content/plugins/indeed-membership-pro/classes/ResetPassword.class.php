<?php
namespace IHC{
	class ResetPassword{
		private $expire_interval = 3600;//one hour

		public function __construct(){}

		public function send_mail_with_link($username_or_email=''){
			/*
			 * @param string
			 * @return none
			 */
			global $ihc_reset_pass;
			$ihc_reset_pass = -1;
			$user = get_user_by('email', $username_or_email);
			if ($user){
				$uid = $user->data->ID;
				$email_addr = $username_or_email;
			} else {
				//get user by user_login
				global $wpdb;
				$username_or_email = esc_sql($username_or_email);
				$query = $wpdb->prepare( "SELECT ID, user_email FROM {$wpdb->base_prefix}users WHERE `user_login`=%s;", $username_or_email );
				$data = $wpdb->get_row( $query );
				if (isset($data->ID) && isset($data->user_email)){
					$uid = $data->ID;
					$email_addr = $data->user_email;
				}
			}

			if (!empty($email_addr) && !empty($uid)){
				$hash = ihc_random_str(10);
				$time = indeed_get_unixtimestamp_with_timezone();
				update_user_meta($uid, 'ihc_reset_password_temp_data', array('code' => $hash, 'time' => $time ));
				$link = site_url();
				$link = add_query_arg('ihc_action', 'arrive', $link);
				$link = add_query_arg('do_reset_pass', 'true', $link);
				$link = add_query_arg('c', $hash, $link);
				$link = add_query_arg('uid', $uid, $link);
				$link = apply_filters( 'ump_public_filter_reset_password_link', $link, $uid, $hash );

				$sent = apply_filters( 'ihc_filter_reset_password_process', false, $uid, array('{password_reset_link}' => $link) );
				if (!$sent){
					$subject = esc_html__('Password reset on ', 'ihc') . get_option('blogname');
					$msg = '<p>' . esc_html__('You or someone else has requested to change password for your account', 'ihc'). '</p><br><p>' . esc_html__('To change Your Password click on this URL:', 'ihc') . ' </p>' . $link;
					wp_mail($email_addr, $subject, $msg);
				}
				$ihc_reset_pass = 1;
			}
		}

		public function proceed($uid=0, $code=''){
			/*
			 * @param int, string
			 * @return none
			 */
			 if ($uid && $code){
			 	$time = indeed_get_unixtimestamp_with_timezone();
				$data = get_user_meta($uid, 'ihc_reset_password_temp_data', TRUE);
				if ($data){
					if ($data['code']==$code && $data['time']+$this->expire_interval>$time){
						$sucess = $this->do_reset_password($uid);
						if ($sucess){
							delete_user_meta($uid, 'ihc_reset_password_temp_data');
						}
					}
				}
			 }
		}

		private function do_reset_password($uid=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 if ($uid){
			 	add_filter( 'send_password_change_email', '__return_false', 1);
			 	$fields['ID'] = $uid;
				$fields['user_pass'] = wp_generate_password(10, TRUE);
				$user_id = wp_update_user($fields);
				if ($user_id==$fields['ID']){

					$sent = apply_filters( 'ihc_filter_reset_password', false, $user_id, [ '{NEW_PASSWORD}' => $fields['user_pass'] ] );
					if (!$sent){
						$email_addr = $this->get_mail_by_uid($user_id);
						if ($email_addr){
							$subject = esc_html__('Password reset on ', 'ihc') . get_option('blogname');
							$msg = esc_html__('Your new password it\'s: ', 'ihc') . $fields['user_pass'];
							$sent = wp_mail( $email_addr, $subject, $msg );
						}
					}
					return $sent;
				}
			 }
		}

		private function get_mail_by_uid($uid=0){
			/*
			 * @param int
			 * @return string
			 */
			 if ($uid){
			 	$data = get_userdata($uid);
				return (!empty($data) && !empty($data->user_email)) ? $data->user_email : '';
			 }
			 return '';
		}

	}
}
