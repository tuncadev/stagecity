<?php
require_once IHC_PATH . 'classes/gateways/libraries/braintree_v1/lib/Braintree.php';

Ihc_User_Logs::write_log( esc_html__('Braintree Payment Webhook: Start process', 'ihc'), 'payments');

/// AUTH
$meta = ihc_return_meta_arr('payment_braintree');
if ($meta['ihc_braintree_sandbox']){
	Braintree_Configuration::environment('sandbox');
} else {
	Braintree_Configuration::environment('production');
}
Braintree_Configuration::merchantId($meta['ihc_braintree_merchant_id']);
Braintree_Configuration::publicKey($meta['ihc_braintree_public_key']);
Braintree_Configuration::privateKey($meta['ihc_braintree_private_key']);

if (!empty($_REQUEST["bt_signature"]) && !empty($_REQUEST["bt_payload"])){
	$webhookNotification = Braintree_WebhookNotification::parse($_REQUEST["bt_signature"], $_REQUEST["bt_payload"]);
	if (!empty($webhookNotification) && !empty($webhookNotification->subscription) && !empty($webhookNotification->subscription->id)){
		$transaction_id = $webhookNotification->subscription->id;


		$data = ihc_get_lid_uid_by_txn_id($transaction_id);

		Ihc_User_Logs::set_user_id((isset($data['uid'])) ? $data['uid'] : '');
		Ihc_User_Logs::set_level_id((isset($data['lid'])) ? $data['lid'] : '');

		switch ($webhookNotification->kind){
			case 'subscription_charged_successfully':
				if (isset($data['lid']) && isset($data['uid'])){
					///success
					$data['message'] = 'success';
					$level_data = ihc_get_level_by_id($data['lid']);//getting details about current level
					\Indeed\Ihc\UserSubscriptions::makeComplete( $data['uid'], $data['lid'], false, [ 'payment_gateway' => 'braintree' ] );
					ihc_insert_update_transaction($data['uid'], $transaction_id, $data);

					do_action( 'ihc_payment_completed', $data['uid'], $data['lid'] );
					Ihc_User_Logs::write_log( esc_html__("Braintree Payment Webhook: Update user level expire time.", 'ihc'), 'payments');
				}
				break;
			case 'subscription_canceled':
			case 'subscription_charged_unsuccessfully':
			case 'subscription_expired':
				///FAIL
				if (!function_exists('ihc_is_user_level_expired')){
					require_once IHC_PATH . 'public/functions.php';
				}
				$expired = ihc_is_user_level_expired($data['uid'], $data['lid'], FALSE, TRUE);
				if ($expired){
					//it's expired and we must delete user - level relationship
					\Indeed\Ihc\UserSubscriptions::deleteOne( $data['uid'], $data['lid'] );
					Ihc_User_Logs::write_log( esc_html__("Braintree Payment Webhook: Delete user level.", 'ihc'), 'payments');
				}
				break;
		}
	}
} else if (!empty($_REQUEST['transaction_id'])){
	/// TESTING WEBHOOK
	$sampleNotification = Braintree_WebhookTesting::sampleNotification(
	    Braintree_WebhookNotification::SUBSCRIPTION_WENT_ACTIVE,
	    $_REQUEST['transaction_id']
	);

	$webhookNotification = Braintree_WebhookNotification::parse(
	    $sampleNotification['bt_signature'],
	    $sampleNotification['bt_payload']
	);

	echo $webhookNotification->subscription->id;

}
