<?php

if (!class_exists('UserAddEdit')):
	include_once IHC_PATH . 'classes/UserAddEdit.class.php';
endif;



if (!class_exists('LiteRegister')):

class LiteRegister extends UserAddEdit{
	private $lite_register_metas = array();
	protected $shortcodes_attr = array();

	public function __construct(){}

	public function setVariable($arr=array()){
		/*
		 * set the input variables
		 * @param array
		 * @return none
		 */
		if(count($arr)){
			foreach($arr as $k=>$v){
				$this->$k = $v;
			}
		}
	}

	private function set_register_fields(){
		/*
		 * @param none
		 * @return none
		 */
		$this->register_fields = ihc_get_user_reg_fields();//register fields
	}

	public function form(){
		/*
		 * @param none
		 * @return string
		 */
		$this->set_register_fields();
		$key = ihc_array_value_exists($this->register_fields, 'user_email', 'name');
		$this->register_template = $this->lite_register_metas['ihc_register_lite_template'];
		if (isset($this->shortcodes_attr['template']) && $this->shortcodes_attr['template']!==FALSE){
			$this->register_template = $this->shortcodes_attr['template'];
		}
		$data['email_fields'] = $this->print_fields($this->register_fields[$key]);
		$data['submit_button'] = indeed_create_form_element(array('type'=>'submit', 'name'=>'Submit', 'value' => esc_html__('Register', 'ihc'), 'class' => 'button button-primary button-large',));
		$data['hidden_fields'][] = indeed_create_form_element(array('type'=>'hidden', 'name'=>'ihcaction', 'value' => 'register_lite' ));
		$data['hidden_fields'][] = $this->printNonce();
		$data['css'] = stripslashes($this->lite_register_metas['ihc_register_lite_custom_css']);
		$data['js'] = '';
		$data['template'] = $this->lite_register_metas['ihc_register_lite_template'];

		ob_start(); // Start output buffer capture.
		include(IHC_PATH . 'public/views/register_lite_form.php'); // Include your template.
		$output = ob_get_contents(); // This contains the output of yourtemplate.php
		// Manipulate $output...
		ob_end_clean(); // Clear the buffer.
		return $output;
		//require IHC_PATH . 'public/views/register_lite_form.php';
	}



	public function save_update_user(){
		/*
		 * @param none
		 * @return none
		 */
		 $this->register_metas = array_merge(ihc_return_meta_arr('register'), ihc_return_meta_arr('register-msg'), ihc_return_meta_arr('register-custom-fields'));
		 $this->set_register_fields();

		 $errors = apply_filters( 'ihc_filter_register_lite_process_check_errors', [], $_POST, $this->register_fields, $this->user_id );
		 if ( $errors ){
				 $this->errors = $errors;
		 }

		 $this->errors = apply_filters('ump_before_printing_errors', $this->errors);

		 if ($this->errors){
			 //print the error and exit
			 $this->return_errors();
			 return FALSE;
		 }

		 $this->fields['user_login'] = (isset($_POST['user_email'])) ? $_POST['user_email'] : '';
		 $this->fields['user_login'] = sanitize_text_field( $this->fields['user_login'] );
		 $this->fields['user_email'] = (isset($_POST['user_email'])) ? $_POST['user_email'] : '';
		 $this->fields['user_email'] = sanitize_email( $this->fields['user_email'] );
		 $this->fields['user_pass'] = wp_generate_password(10);

		 if ( empty( $this->user_id ) ){
		 		// we set the role via filter. It's called in classses/RegistrationEvents.php
			 	$this->fields['role'] = apply_filters( 'ihc_filter_register_role', '', $_POST, $this->shortcodes_attr, 'register_lite' );
		 }

		 $this->user_id = wp_insert_user($this->fields);
		 do_action('ump_on_register_action', $this->user_id);
		 // @description Run on register user. @param user id (integer)

		 do_action('ump_on_register_lite_action', $this->user_id);
		 // @description Run on register user with lite register form. @param user id (integer)

		 //Ihc_Db::increment_dashboard_notification('users');
		 if ( $this->type == 'create' ){
				 do_action( 'ihc_register_action_after_insert', $this->user_id, $_POST, $this->register_metas, $this->shortcode_atts, 'register_lite' );
		 }

		 $this->notify_user_send_password();
		 $this->do_autologin();

				if (!empty($this->register_metas['ihc_register_new_user_role']) && $this->register_metas['ihc_register_new_user_role']=='pending_user'){
					//PENDING
					do_action( 'ihc_action_create_user_review_request', $this->user_id, (isset($_POST['lid'])) ? $_POST['lid'] : 0 );
				} else {
					do_action( 'ihc_action_create_user_register', $this->user_id, (isset($_POST['lid'])) ? $_POST['lid'] : 0  );
				}

		 if ($this->is_public){
			$this->succes_message();//this will redirect
		 }
	}

	private function do_autologin(){
		/*
		 * @param none
		 * @return none
		 */
		if (isset($this->shortcodes_attr['autologin']) && $this->shortcodes_attr['autologin']!==FALSE){
			$this->lite_register_metas['ihc_register_lite_auto_login'] = $this->shortcodes_attr['autologin'];
		}
		if (!empty($this->lite_register_metas['ihc_register_lite_auto_login']) && !empty($this->lite_register_metas['ihc_register_lite_user_role']) && $this->lite_register_metas['ihc_register_lite_user_role']!='pending_user'){
			wp_set_auth_cookie($this->user_id);
		}
	}

	protected function succes_message(){
		/*
		 * @param none
		 * @return none
		 */
		if ($this->type=='create'){
			$q_arg = 'create_message';
		} else {
			$q_arg = 'update_message';
		}
		$redirect = get_option('ihc_register_lite_redirect');
		$redirect = apply_filters( 'ump_public_filter_redirect_page_after_register', $redirect );
		if (empty($redirect) || $redirect==-1){
			$redirect = get_option('ihc_general_register_redirect');
			$redirect = apply_filters( 'ump_public_filter_redirect_page_after_register', $redirect );
		}
		if ($redirect && $redirect!=-1 && $this->type=='create'){
			//custom redirect
			$url = get_permalink($redirect);
			if (!$url){
				$url = ihc_get_redirect_link_by_label($redirect, $this->user_id);
				if (strpos($url, IHC_PROTOCOL . $_SERVER['HTTP_HOST'] )!==0){ 
					//if it's a external custom redirect we don't want to add extra params in url, so let's redirect from here
					wp_safe_redirect($url);
					exit();
				}
			}
		}

		if (empty($url)){
			$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
		}

		if ($this->bank_transfer_message){
			/// bt redirect only to same page
			$bt_params = array( 'ihc_register' => $q_arg,
								'ihcbt' => 'true',
								'ihc_lid' => $_POST['lid'],
								'ihc_uid' => $this->user_id,
			);

			if ($this->coupon){
				$coupon_data = ihc_check_coupon($this->coupon, $_POST['lid']);
				if ($coupon_data){
					if ($coupon_data['discount_type']=='percentage'){
						$bt_params['cp'] = $coupon_data['discount_value'];
					} else {
						$bt_params['cc'] = $coupon_data['discount_value'];
					}
					ihc_submit_coupon($this->coupon);
				}
			}

			//country
			if (!empty($_POST['ihc_country'])){
				$bt_params['ihc_country'] = $_POST['ihc_country'];
			}
			$url = add_query_arg($bt_params, $url);
		} else {
			$url = add_query_arg(array('ihc_register'=>$q_arg), $url);
		}
		wp_safe_redirect($url);
		exit();
	}

	protected function notify_user_send_password(){
		/*
		 * @param none
		 * @return none
		 */
		 do_action( 'ihc_register_lite_action', $this->user_id, [ '{NEW_PASSWORD}' => $this->fields['user_pass'] ] );

	}

}



endif;
