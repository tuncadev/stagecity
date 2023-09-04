<?php
if(!class_exists('ihcStripe')){
	class ihcStripe{
		private $publishable_key = FALSE;
		private $secret_key = FALSE;
		private $level_data = array();
		private $currency = 'USD';

		public function __construct(){
			//set keys
			$this->publishable_key = get_option('ihc_stripe_publishable_key');
			$this->secret_key = get_option('ihc_stripe_secret_key');
			$this->level_data = \Indeed\Ihc\Db\Memberships::getAll();
			$this->currency = get_option('ihc_currency');

			//load stripe libs
			require_once IHC_PATH . 'classes/PaymentGateways/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->secret_key);

			//multiply
			$this->multiply = ihcStripeMultiplyForCurrency( $this->currency );
		}

		public function payment_fields($level_id, $bind=TRUE){
			if (isset($this->level_data[$level_id])){

				/// popup stuff
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
				$currency = get_option( 'ihc_currency');
				$multiply =  ihcStripeMultiplyForCurrency( $currency );

				$amount = $this->level_data[$level_id]['price'] * $this->multiply;
				if ( $this->multiply == 100 && $amount > 0 && $amount < 50 ){
						$amount = 50;
				}

				if ( $bind ){
						$bind = 1;
				} else {
						$bind = 0;
				}
				//$ajaxURL = IHC_URL . 'public/ajax-custom.php?ihcpublicn=' . wp_create_nonce( 'ihcpublicn' );
				$ajaxURL = get_site_url() . '/wp-admin/admin-ajax.php?action=ihc_ajax_public_custom_ajax_gate&ihcpublicn=' . wp_create_nonce( 'ihcpublicn' );

				wp_enqueue_script( 'ihc-stripe-checkout', 'https://checkout.stripe.com/checkout.js', [ 'jquery' ], 10.1 );
				wp_enqueue_script( 'ihc-stripe-custom', IHC_URL . 'assets/js/stripe.js', [ 'jquery' ], 10.1 );
				$str = '<span class="ihc-js-stripe-v1-data"
										data-key="' . $this->publishable_key . '"
										data-locale="' . $locale . '"
										data-image="' . $image . '"
										data-bttn="' . $bttn . '"
										data-email=""
										data-multiply="' . $multiply . '"
										data-target_url="' . $ajaxURL . '"
										data-blogname="' . get_option( 'blogname' ) . '"
										data-currency="' . $currency . '"
										data-form=".ihc-form-create-edit"
										data-bind="' . $bind . '"
										data-access_type="' . $this->level_data[$level_id]['access_type']  . '"
				></span>';

				return $str;
			}
		}

		public function charge($post_data, $insert_order=FALSE){
			/*
			 * @param array
			 * @return array
			 */
			if (isset($this->level_data[$post_data['lid']])){

				///LOGS
				Ihc_User_Logs::set_user_id((isset($post_data['uid'])) ? $post_data['uid'] : '');
				Ihc_User_Logs::set_level_id((isset($post_data['lid'])) ? $post_data['lid'] : '');
				Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Start process', 'ihc'), 'payments');

				$order_extra_metas = array();
				$reccurrence = FALSE;
				if (isset($this->level_data[$post_data['lid']]['access_type']) && $this->level_data[$post_data['lid']]['access_type']=='regular_period'){
					$reccurrence = TRUE;
				}

				/*************************** DYNAMIC PRICE ***************************/
				if (ihc_is_magic_feat_active('level_dynamic_price') && isset($post_data['ihc_dynamic_price'])){
					$temp_amount = $post_data['ihc_dynamic_price'];
					if (ihc_check_dynamic_price_from_user($post_data['lid'], $temp_amount)){
						$this->level_data[$post_data['lid']]['price'] = $temp_amount;
						Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Dynamic price on - Amount is set by the user @ ', 'ihc') . $temp_amount . $this->currency, 'payments');
					}
				}
				/**************************** DYNAMIC PRICE ***************************/

				//DISCOUNT
				if (!empty($post_data['ihc_coupon'])){
					$coupon_data = ihc_check_coupon($post_data['ihc_coupon'], $post_data['lid']);
					if ($coupon_data && (!empty($coupon_data['reccuring']) || !$reccurrence)){
						//available only for single payment or discount on all recurring payments
						$order_extra_metas['discount_value'] = ihc_get_discount_value($this->level_data[$post_data['lid']]['price'], $coupon_data);
						$order_extra_metas['coupon_used'] = $post_data['ihc_coupon'];
						$this->level_data[$post_data['lid']]['price'] = ihc_coupon_return_price_after_decrease(
																		$this->level_data[$post_data['lid']]['price'],
																		$coupon_data,
																		TRUE,
																		$post_data['uid'],
																		$post_data['lid']);
						Ihc_User_Logs::write_log( esc_html__('Stripe Payment: the user used the following coupon: ', 'ihc') . $post_data['ihc_coupon'], 'payments');
					}

				}

				$amount = $this->level_data[$post_data['lid']]['price'];

				$amount = $amount * $this->multiply;
				if ( $this->multiply == 100 && $amount> 0 && $amount<50){
					$amount = 50;// 0.50 cents minimum amount for stripe transactions
				}

				/// TAXES
				$state = (isset($post_data['ihc_state'])) ? $post_data['ihc_state'] : '';
				$country = isset($post_data['ihc_country']) ? $post_data['ihc_country'] : '';
				$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
				if ($taxes_data && !empty($taxes_data['total'])){
					$amount += $taxes_data['total'];
					$order_extra_metas['tax_value'] = $taxes_data['total'];
				}

				$amount = round($amount);

				$customer_arr = array(
						'email' => $post_data['stripeEmail'],
						'card'  => $post_data['stripeToken'],
				);


				if ($reccurrence){
					Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Recurrence payment set.', 'ihc'), 'payments');
					$ihc_plan_code = 'ihc_plan_' . rand(1,10000);
					switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
						case 'D':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'day';
							break;
						case 'W':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'week';
							break;
						case 'M':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'month';
							break;
						case 'Y':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'year';
							break;
					}

					///trial
					$trial_period_days = 0;
					if (!empty($this->level_data[$post_data['lid']]['access_trial_type'])){
						if ($this->level_data[$post_data['lid']]['access_trial_type']==1 && isset($this->level_data[$post_data['lid']]['access_trial_time_value'])
								&& $this->level_data[$post_data['lid']]['access_trial_time_value'] !=''){
							switch ($this->level_data[$post_data['lid']]['access_trial_time_type']){
								case 'D':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'];
									break;
								case 'W':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 7;
									break;
								case 'M':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 31;
									break;
								case 'Y':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 365;
									break;
							}
						} else if ($this->level_data[$post_data['lid']]['access_trial_type']==2 && isset($this->level_data[$post_data['lid']]['access_trial_couple_cycles'])
									&& $this->level_data[$post_data['lid']]['access_trial_couple_cycles']!=''){
							switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
								case 'day':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'];
									break;
								case 'week':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'] * 7;
									break;
								case 'month':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'] * 31;
									break;
								case 'year':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'] * 365;
									break;
							}
						}
					}
					//end of trial

					//v.7.1 - Recurring Level with Coupon 100% => 1 free Trial cycle
					if($trial_period_days == 0){
						if(isset($coupon_data)){
							$discounted_value = ihc_get_discount_value($amount, $coupon_data);

							if($amount - $discounted_value == 0){
							  $order_extra_metas['discount_value'] = $discounted_value;
							  $order_extra_metas['coupon_used'] = $post_data['ihc_coupon'];
							  switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
								  case 'day':
									  $trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'];
									  break;
								  case 'week':
									  $trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value']  * 7;
									  break;
								  case 'month':
									  $trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * 31;
									  break;
								  case 'year':
									  $trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value']  * 365;
									  break;
							  }

							  Ihc_User_Logs::write_log( esc_html__('Stripe Payment: the user used the following coupon: ', 'ihc') . $post_data['ihc_coupon'], 'payments');				}
						}
					}


					$plan = array(
							"amount" => $amount,
							"interval_count" => $this->level_data[$post_data['lid']]['access_regular_time_value'],
							"interval" => $this->level_data[$post_data['lid']]['access_regular_time_type'],
							"product" => array(
    						"name" => "Recurring for " . $this->level_data[$post_data['lid']]['name'] ),
							"currency" => $this->currency,
							"id" => $ihc_plan_code,
							//"trial_period_days" => $trial_period_days,

					);
					$trial_end = "now";
					if (!empty($trial_period_days)){
						Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Trial time value set @ ', 'ihc') . $trial_period_days . esc_html__(' days.', 'ihc'), 'payments');

						$trial_end = strtotime("+ ".$trial_period_days." days");
					}

					$return_data_plan = \Stripe\Plan::create($plan);
					try {
							$return_data_plan = \Stripe\Plan::create($plan);
					} catch (Exception $e){
							//Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Plan Error:', 'ihc') . $e, 'payments');
					}




					try {
							$customer = \Stripe\Customer::create($customer_arr);
					} catch (Exception $e){
							return false;
					}


					$resource =  \Stripe\Subscription::create(array(
						"customer" 				=> $customer->id,
						'items' 					=> [['plan' => $return_data_plan->id]],
						"trial_end" 			=> $trial_end,
					));
					//end of reccurence
				}else{
					try {
							$customer = \Stripe\Customer::create($customer_arr);
					} catch (Exception $e){
							return false;
					}
				}

				Ihc_User_Logs::write_log( esc_html__('Stripe Payment: amount set @ ', 'ihc') . $amount . $this->currency, 'payments');

				$sub_id = '';
				if ($reccurrence){
					/// RECCURRING PAYMENT
					$plan = \Stripe\Plan::retrieve($ihc_plan_code);

					if ( isset( $resource->id ) ){
						$sub_id = $resource->id;
					}
				} else {
					/// SINGLE PAYMENT
					try {
    					$charge = \Stripe\Charge::create(array(
																		'customer' => $customer->id,
																		'amount'   => $amount,
																		'currency' => $this->currency,
							));
					    $success = 1;
					    $paymentProcessor="Credit card (www.stripe.com)";
					} catch (Exception $e) {
							$error1 = $e->getMessage();
							$cardError = true;
					}
				}

				$amount = $amount / $this->multiply;
				$response_return = array(
						'amount' => urlencode($amount),
						'currency' => $this->currency,
						'level' => $post_data['lid'],
						'item_name' => $this->level_data[$post_data['lid']]['name'],
						'customer' => $customer->id,
				);
				if ( !empty( $cardError ) ){
						$response_return[ 'cardErrors' ] = true;
				}
				if ($sub_id){
					$response_return['subscription'] = $sub_id;
				}

				if (!empty($insert_order)){
					$saved_amount = $amount;

					//v.7.1. - Payments from account page for Recurring Levels with Trial or Coupons 100%
					if(!empty($trial_period_days) && $trial_period_days > 0)
						$saved_amount = 0;
					ihc_insert_update_order($post_data['uid'], $post_data['lid'], $saved_amount, 'pending', 'stripe', $order_extra_metas);
				}

				if ($reccurrence && isset($customer->id)){

					Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Successfully Finish the payment.', 'ihc'), 'payments');
					$response_return['message'] = "pending";
					$response_return['trans_id'] = $customer->id;
				} else if (!empty($charge) && $charge->paid) {

					Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Successfully Finish the payment.', 'ihc'), 'payments');
					$response_return['message'] = "pending";
					$response_return['trans_id'] = $charge->customer;
				} else {
					Ihc_User_Logs::write_log( esc_html__('Stripe Payment: Error.', 'ihc'), 'payments');
					$response_return['message'] = "error";
				}

				return $response_return;
			}
		}


		/**
		 * @param string
		 * @return none
		 */
		public function cancel_subscription( $transactionId='' )
		{
				global $wpdb;
				if ( $transactionId === '' ){
						return false;
				}
				$query = $wpdb->prepare( "SELECT payment_data FROM {$wpdb->prefix}indeed_members_payments WHERE txn_id=%s;", $transactionId );
				$data = $wpdb->get_row( $query );
				if ( !isset( $data->payment_data ) || $data->payment_data === '' ){
						return false;
				}
				$subscriptionData = json_decode( $data->payment_data, true );
				if ( !isset( $subscriptionData ) || !isset( $subscriptionData['subscription'] ) || $subscriptionData['subscription'] === '' ){
						return false;
				}
				$stripeSubscription = \Stripe\Subscription::retrieve( $subscriptionData['subscription'] );
				if ( !$stripeSubscription ){
						return false;
				}
				if ( $stripeSubscription['status'] !== 'active' && $stripeSubscription['status'] !== 'trialing'  ){
						return false;
				}
				try {
						$response = $stripeSubscription->cancel();
				} catch ( Stripe\Error\InvalidRequest $e ){
						return false;
				}
				return $response;
		}

		/**
		 * @param string
		 * @return boolean
		 */
		public function canDoCancel( $transactionId='' )
		{
				global $wpdb;
				if ( $transactionId === '' ){
						return false;
				}
				$query = $wpdb->prepare( "SELECT payment_data FROM {$wpdb->prefix}indeed_members_payments WHERE txn_id=%s;", $transactionId );
				$data = $wpdb->get_row( $query );
				if ( !isset( $data->payment_data ) || $data->payment_data === '' ){
						return false;
				}
				$subscriptionData = json_decode( $data->payment_data, true );
				if ( !isset( $subscriptionData ) || !isset( $subscriptionData['subscription'] ) || $subscriptionData['subscription'] === '' ){
						return false;
				}
				$stripeSubscription = \Stripe\Subscription::retrieve( $subscriptionData['subscription'] );
				if ( !$stripeSubscription ){
						return false;
				}
				if ( $stripeSubscription['status'] === 'active' || $stripeSubscription['status'] === 'trialing' ){
						return true;
				}
				return false;
		}

	}//end of class ihcStripe

}
