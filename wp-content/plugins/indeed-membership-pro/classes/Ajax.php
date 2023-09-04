<?php
namespace Indeed\Ihc;

class Ajax
{
    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        add_action('wp_ajax_ihc_admin_send_email_popup', array($this, 'ihc_admin_send_email_popup') );
        add_action('wp_ajax_ihc_admin_do_send_email', array($this, 'ihc_admin_do_send_email') );
        add_action('wp_ajax_ihc_generate_direct_link', array($this, 'ihc_generate_direct_link') );
        add_action('wp_ajax_ihc_generate_direct_link_by_uid', array($this, 'ihc_generate_direct_link_by_uid') );
        add_action('wp_ajax_ihc_direct_login_delete_item', array($this, 'ihc_direct_login_delete_item') );
        add_action('wp_ajax_ihc_save_reason_for_cancel_delete_level', array($this, 'ihc_save_reason_for_cancel_delete_level') );
        add_action('wp_ajax_nopriv_ihc_save_reason_for_cancel_delete_level', array($this, 'ihc_save_reason_for_cancel_delete_level') );
        add_action( 'wp_ajax_ihc_close_admin_notice', array( $this, 'ihc_close_admin_notice' ) );
        add_action( 'wp_ajax_ihc_remove_media_post', array( $this, 'ihc_remove_media_post' ) );
        add_action( 'wp_ajax_nopriv_ihc_remove_media_post', array( $this, 'ihc_remove_media_post' ) );
        add_action( 'wp_ajax_nopriv_ihc_update_list_notification_constants', array( $this, 'ihc_update_list_notification_constants' ) );
        add_action( 'wp_ajax_ihc_update_list_notification_constants', array( $this, 'ihc_update_list_notification_constants' ) );
        add_action( 'wp_ajax_ihc_admin_list_users_total_spent_values', array( $this, 'usersTotalSpentValues') );
        add_action( 'wp_ajax_ihc_admin_make_order_completed', array( $this, 'adminMakeOrderCompleted') );

        add_action( 'wp_ajax_ihc_get_membership_details', [ $this, 'adminGetMembershipDetails' ] );
        add_action( 'wp_ajax_ihc_user_level_get_next_expire_time', [ $this, 'adminGetNextExpireTimeOnUserLevel' ] );
        add_action( 'wp_ajax_ihc_user_level_pause', [ $this, 'adminUserSubscriptionPause' ] );
        add_action( 'wp_ajax_ihc_user_level_reactivate', [ $this, 'adminUserSubscriptionReactivate'] );

        add_action( 'wp_ajax_ihc_user_put_subscrition_on_pause', [ $this, 'userPutSubscriptionOnPause' ] );
        add_action( 'wp_ajax_nopriv_ihc_user_put_subscrition_on_pause', [ $this, 'userPutSubscriptionOnPause' ] );

        add_action( 'wp_ajax_ihc_user_put_subscrition_resume', [ $this, 'userPutSubscriptionResume' ] );
        add_action( 'wp_ajax_nopriv_ihc_user_put_subscrition_resume', [ $this, 'userPutSubscriptionResume' ] );

        add_action( 'wp_ajax_ihc_stripe_connect_form_fields', [ $this, 'ihc_stripe_connect_form_fields'] );
        add_action( 'wp_ajax_nopriv_ihc_stripe_connect_form_fields', [ $this, 'ihc_stripe_connect_form_fields'] );

        add_action( 'wp_ajax_ihc_stripe_connect_generate_payment_intent', [ $this, 'ihc_stripe_connect_generate_payment_intent'] );
        add_action( 'wp_ajax_nopriv_ihc_stripe_connect_generate_payment_intent', [ $this, 'ihc_stripe_connect_generate_payment_intent'] );

        add_action( 'wp_ajax_ihc_ajax_check_braintree_form_fields', [ $this, 'ihc_ajax_check_braintree_form_fields'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_check_braintree_form_fields', [ $this, 'ihc_ajax_check_braintree_form_fields'] );

        add_action( 'wp_ajax_ihc_ajax_check_authorize_form_fields', [ $this, 'ihc_ajax_check_authorize_form_fields'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_check_authorize_form_fields', [ $this, 'ihc_ajax_check_authorize_form_fields'] );

        add_action( 'wp_ajax_ihc_ajax_deauth_from_stripe_connect', [ $this, 'ihc_ajax_deauth_from_stripe_connect'] );

        add_action( 'wp_ajax_ihc_ajax_get_stripe_connect_change_card_fields', [ $this, 'ihc_ajax_get_stripe_connect_change_card_fields'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_get_stripe_connect_change_card_fields', [ $this, 'ihc_ajax_get_stripe_connect_change_card_fields'] );

        add_action( 'wp_ajax_ihc_ajax_do_stripe_connect_change_card', [ $this, 'ihc_ajax_do_stripe_connect_change_card'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_do_stripe_connect_change_card', [ $this, 'ihc_ajax_do_stripe_connect_change_card'] );

        add_action( 'wp_ajax_ihc_ajax_stripe_connect_generate_payment_intent', [ $this, 'ihc_ajax_stripe_connect_generate_payment_intent'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_stripe_connect_generate_payment_intent', [ $this, 'ihc_ajax_stripe_connect_generate_payment_intent'] );

        add_action( 'wp_ajax_ihc_ajax_stripe_connect_generate_setup_intent', [ $this, 'ihc_ajax_stripe_connect_generate_setup_intent'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_stripe_connect_generate_setup_intent', [ $this, 'ihc_ajax_stripe_connect_generate_setup_intent'] );

        add_action( 'wp_ajax_ihc_ajax_stripe_connect_generate_setup_intent_no_payment', [ $this, 'ihc_ajax_stripe_connect_generate_setup_intent_no_payment'] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_stripe_connect_generate_setup_intent_no_payment', [ $this, 'ihc_ajax_stripe_connect_generate_setup_intent_no_payment'] );

        add_action( 'wp_ajax_ihc_ajax_prorate_delete_group', [ $this, 'ihc_ajax_prorate_delete_group'] );

        add_action( 'wp_ajax_ihc_ajax_send_double_email_verification', [ $this, 'ihc_ajax_send_double_email_verification' ] );

        add_action( 'wp_ajax_ihc_ajax_close_admin_mk_notice', [ $this, 'ihc_ajax_close_admin_mk_notice' ] );

        // custom ajax for admin
        add_action( 'wp_ajax_ihc_ajax_custom_admin_ajax_gate', [ $this, 'ihc_ajax_custom_admin_ajax_gate' ] );

        // upload file
        add_action( 'wp_ajax_ihc_ajax_public_upload_file', [ $this, 'ihc_ajax_public_upload_file' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_public_upload_file', [ $this, 'ihc_ajax_public_upload_file' ] );

        // ajax
        add_action( 'wp_ajax_ihc_ajax_public_custom_ajax_gate', [ $this, 'ihc_ajax_public_custom_ajax_gate' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_public_custom_ajax_gate', [ $this, 'ihc_ajax_public_custom_ajax_gate' ] );

        add_action( 'wp_ajax_ihc_admin_delete_account_page_menu_item', [ $this, 'ihc_admin_delete_account_page_menu_item' ] );

        add_action( 'wp_ajax_ihc_close_admin_registration_notice', [ $this, 'ihc_close_admin_registration_notice' ] );

        add_action( 'wp_ajax_ihc_ajax_forms_check_one_field', [ $this, 'ihc_forms_check_one_field' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_forms_check_one_field', [ $this, 'ihc_forms_check_one_field' ] );

        add_action( 'wp_ajax_ihc_ajax_forms_check_all_fields', [ $this, 'ihc_ajax_forms_check_all_fields' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_forms_check_all_fields', [ $this, 'ihc_ajax_forms_check_all_fields' ] );

        add_action( 'wp_ajax_ihc_ajax_profile_edit_check_one_conditional_logic', [ $this, 'ihc_ajax_profile_edit_check_one_conditional_logic' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_profile_edit_check_one_conditional_logic', [ $this, 'ihc_ajax_profile_edit_check_one_conditional_logic' ] );

        add_action( 'wp_ajax_ihc_ajax_edit_profile_check_unique_field', [ $this, 'ihc_ajax_edit_profile_check_unique_field' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_edit_profile_check_unique_field', [ $this, 'ihc_ajax_edit_profile_check_unique_field' ] );

        add_action( 'wp_ajax_ihc_ajax_edit_profile_check_conditional_text_field', [ $this, 'ihc_ajax_edit_profile_check_conditional_text_field' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_edit_profile_check_conditional_text_field', [ $this, 'ihc_ajax_edit_profile_check_conditional_text_field' ] );

        add_action( 'wp_ajax_ihc_ajax_get_state_field_as_html', [ $this, 'ihc_ajax_get_state_field_as_html' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_get_state_field_as_html', [ $this, 'ihc_ajax_get_state_field_as_html' ] );

        // ---- register ----
        // check one field:
        add_action( 'wp_ajax_ihc_ajax_register_forms_check_one_field', [ $this, 'ihc_register_forms_check_one_field' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_register_forms_check_one_field', [ $this, 'ihc_register_forms_check_one_field' ] );

        // check all fields:
        add_action( 'wp_ajax_ihc_ajax_register_form_check_all_fields', [ $this, 'ihc_ajax_register_form_check_all_fields' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_register_form_check_all_fields', [ $this, 'ihc_ajax_register_form_check_all_fields' ] );

        // conditional logic
        add_action( 'wp_ajax_ihc_ajax_register_form_check_one_conditional_logic', [ $this, 'ihc_ajax_register_form_check_one_conditional_logic' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_register_form_check_one_conditional_logic', [ $this, 'ihc_ajax_register_form_check_one_conditional_logic' ] );

        // unique value
        add_action( 'wp_ajax_ihc_ajax_register_form_check_unique_field', [ $this, 'ihc_ajax_register_form_check_unique_field' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_register_form_check_unique_field', [ $this, 'ihc_ajax_register_form_check_unique_field' ] );

        //
        add_action( 'wp_ajax_ihc_ajax_register_form_check_conditional_text_field', [ $this, 'ihc_ajax_register_form_check_conditional_text_field' ] );
        add_action( 'wp_ajax_nopriv_ihc_ajax_register_form_check_conditional_text_field', [ $this, 'ihc_ajax_register_form_check_conditional_text_field' ] );
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_admin_send_email_popup()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        $uid = empty($_POST['uid']) ? 0 : esc_sql($_POST['uid']);
        if (empty($uid)){
            die;
        }
        $toEmail = \Ihc_Db::get_user_col_value($uid, 'user_email');
        if (empty($toEmail)){
            die;
        }
        $fromEmail = '';
        $fromEmail = get_option('ihc_notifications_from_email_addr');
        if (empty($fromEmail)){
            $fromEmail = get_option('admin_email');
        }
        $view = new \Indeed\Ihc\IndeedView();
        $view->setTemplate(IHC_PATH . 'admin/includes/tabs/send_email_popup.php');
        $view->setContentData([
                                'toEmail' 		=> $toEmail,
                                'fromEmail' 	=> $fromEmail,
                                'fullName'		=> \Ihc_Db::getUserFulltName($uid),
                                'website'			=> get_option('blogname')
        ], true);
        echo $view->getOutput();
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_admin_do_send_email()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        $to = empty($_POST['to']) ? '' : esc_sql($_POST['to']);
        $from = empty($_POST['from']) ? '' : esc_sql($_POST['from']);
        $subject = empty($_POST['subject']) ? '' : esc_sql($_POST['subject']);
        $message = empty($_POST['message']) ? '' : stripslashes(htmlspecialchars_decode(ihc_format_str_like_wp($_POST['message'])));
        $headers = [];

        if (empty($to) || empty($from) || empty($subject) || empty($message)){
            die;
        }

        $from_name = get_option('ihc_notification_name');
        $from_name = stripslashes($from_name);
        if (!empty($from) && !empty($from_name)){
          $headers[] = "From: $from_name <$from>";
        } else if ( !empty( $from ) ){
          $headers[] = "From: <$from>";
        }
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $sent = wp_mail($to, $subject, $message, $headers);
        echo $sent;
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_generate_direct_link()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['username'] ) ){
            echo 'Error';
            die;
        }
        $uid = \Ihc_Db::get_wpuid_by_username( $_POST['username'] );
        if ( empty($uid) ){
            echo 'Error';
            die;
        }
        $expireTime = isset($_POST['expire_time']) ? $_POST['expire_time'] : 24;
        if ($expireTime<1){
            $expireTime = 24;
        }
        $expireTime = $expireTime * 60 * 60;
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        echo $directLogin->getDirectLoginLinkForUser( $uid, $expireTime );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_generate_direct_link_by_uid()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['uid'] ) ){
            echo 'Error';
            die;
        }
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        echo $directLogin->getDirectLoginLinkForUser( $_POST['uid'] );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_direct_login_delete_item()
    {
        if ( !ihcIsAdmin() ){
            echo 0;
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            echo 0;
            die;
        }
        if ( empty( $_POST['uid'] ) ){
            die;
        }
        $uid = esc_sql($_POST['uid']);
        $directLogin = new \Indeed\Ihc\Services\DirectLogin();
        $directLogin->resetTokenForUser( $uid );
        echo 1;
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_save_reason_for_cancel_delete_level()
    {
        if ( empty($_POST['lid']) || empty($_POST['reason']) || empty($_POST['action_type']) ){
           die;
        }
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $uid = ihc_get_current_user();
        if ( !$uid ){
            die;
        }
        $reasonDbObject = new \Indeed\Ihc\Db\ReasonsForCancelDeleteLevels();
        $made = $reasonDbObject->save(array(
            'uid'         => $uid,
            'lid'         => esc_sql($_POST['lid']),
            'reason'      => esc_sql($_POST['reason']),
            'action_type' => esc_sql($_POST['action_type']),
        ));
        if ( $made ){
            echo 1;
            die;
        }
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_close_admin_notice()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        update_option( 'ihc_hide_admin_license_notice', 1 );
        echo 1;
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_remove_media_post()
    {
        if ( empty( $_POST['postId'] ) ){
            return;
        }
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        wp_delete_attachment( esc_sql( $_POST['postId'] ), true );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_update_list_notification_constants()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['notificationType'] ) ){
            die;
        }
        $data = ihcNotificationConstants( esc_sql( $_POST['notificationType'] ) );
        if ( !$data ){
            die;
        }
        foreach ( $data as $constant => $value ){
            echo '<div>' . $constant . '</div>';
        }
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function usersTotalSpentValues()
    {
        global $wpdb;
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['users'] ) ){
            die;
        }
        $ids = esc_sql( $_POST['users'] );
        $queryString = "SELECT SUM(amount_value) AS sum, uid FROM {$wpdb->prefix}ihc_orders WHERE uid IN ($ids) GROUP BY uid";
        $data = $wpdb->get_results( $queryString );
        if ( !$data ){
            die;
        }
        $currency = get_option( 'ihc_currency' );
        foreach ( $data as $object ){
            $array[$object->uid] = ihc_format_price_and_currency( $currency, $object->sum );
        }
        echo json_encode( $array );
        die;
    }

    public function adminMakeOrderCompleted()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( empty( $_POST['id'] ) ){
            die;
        }
        $orderId = esc_sql( $_POST['id'] );
        $orderObject = new \Indeed\Ihc\Db\Orders();
        $orderObject->setId( $orderId )->update( 'status', 'Completed' );
        $orderData = $orderObject->fetch()->get();
        if ( !$orderData ){
            die;
        }
        $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
        $paymentGateway = $orderMeta->get( $orderId, 'ihc_payment_type' );
        $levelData = \Indeed\Ihc\Db\Memberships::getOne( $orderData->lid );
        if (isset($levelData['access_trial_time_value']) && $levelData['access_trial_time_value'] > 0 && \Indeed\Ihc\UserSubscriptions::isFirstTime( $orderData->uid, $orderData->lid )){
          /// CHECK FOR TRIAL
            \Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, true, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
        } else {
            \Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, false, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
        }
        if ( $paymentGateway === 'bank_transfer' ){
          // create a transaction_id for this entry
          $transactionId = $orderData->uid . '_' . $orderData->lid . '_' . time();
          $orderMeta->save( $orderId, 'transaction_id', $transactionId );
		  do_action( 'ihc_payment_completed', $orderData->uid, $orderData->lid );
        }
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminGetMembershipDetails()
    {
        global $wpdb;
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( !isset( $_POST['levelId'] ) || $_POST['levelId'] == -1 || !isset( $_POST['uid'] ) ){
            die;
        }
        $lid = esc_sql( $_POST['levelId'] );
        $uid = esc_sql( $_POST['uid'] );
        if ( \Indeed\Ihc\UserSubscriptions::userHasSubscription( $uid, $lid) ){
            die;
        }

        // level data
        $levelDetails = \Indeed\Ihc\Db\Memberships::getOne( $lid );

        // trial
        $isTrial = \Indeed\Ihc\Db\Memberships::isTrial( $lid );
        if ( $isTrial ){
            $trial = esc_html__( 'Yes - until ', 'ihc' ) . date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTimeForTrial( $lid, indeed_get_unixtimestamp_with_timezone() ) );
        } else {
           $trial = esc_html__( 'No', 'ihc' );
        }

        // grace period
        $gracePeriod = \Indeed\Ihc\Db\Memberships::getMembershipGracePeriod( $levelDetails['id'] );
        if ( $gracePeriod ){
            $gracePeriod = esc_html__( 'Yes - ', 'ihc') . $gracePeriod . ihcGetTimeTypeByCode( 'D', $gracePeriod ) .  esc_html__(' after expires', 'ihc' );
        } else {
            $gracePeriod = esc_html__( 'No', 'ihc' );
        }

        // start time & expire time
        $startTime = date( 'Y-m-d H:i:s', indeed_get_unixtimestamp_with_timezone() );
        $endTime = date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTime( $lid, indeed_get_unixtimestamp_with_timezone() ) ) ;
        if ( $isTrial ){
            $endTime = date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTimeForTrial( $lid, indeed_get_unixtimestamp_with_timezone() ) );
        }

        $str = "<tr class='ihc-js-user-level-row-" . $lid . "'>
                    <td class='ihc-levels-table-name'>"
                      . $levelDetails['label'] . "<input type='hidden' name='ihc_assign_user_levels[]' value='" . $lid . "' /></td>
                    <td>" . \Indeed\Ihc\Db\Memberships::getAccessTypeAsLabel( $lid ) . "</td>
                    <td>" . ihcPaymentPlanDetailsAdmin( $uid, $lid ) . "</td>
                    <td>-</td>
                    <td>" . $trial . "</td>
                    <td>" . $gracePeriod . "</td>
                    <td>-</td>
                    <td>
                      <div class='input-group'>
                      <input type='text' name='start_time_levels[" . $lid . "]' value='" . $startTime . "' placeholder='' class='start_input_text form-control' />
                      <div class='input-group-addon'><i class='fa-ihc ihc-icon-edit'></i></div>
                      </div>
                    </td>
                    <td>
                      <div class='input-group'>
                      <input type='text' name='expire_levels[" . $lid . "]' value='" . $endTime . "' placeholder='' class='expire_input_text form-control' />
                      <div class='input-group-addon'><i class='fa-ihc ihc-icon-edit'></i></div>
                      </div>
                    </td>
                    <td class='ihc-levels-table-status'>" . esc_html__( 'Active', 'ihc' ) . "</td>
                    <td>
                        <div class='ihc-js-delete-user-level ihc-pointer' data-lid='" . $lid . "' >" . esc_html__( 'Remove', 'ihc' ) . "</div>
                    </td>
        </tr>";
        echo $str;
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminGetNextExpireTimeOnUserLevel()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !isset( $_POST['levelId'] ) || !isset( $_POST['currentExpireTime'] ) ){
            die;
        }
        $endTime = $_POST['currentExpireTime'] == '0000-00-00 00:00:00' ? indeed_get_unixtimestamp_with_timezone() : strtotime( esc_sql( $_POST['currentExpireTime'] ) );
        $endTime = date( 'Y-m-d H:i:s', \Indeed\Ihc\Db\Memberships::getEndTime( esc_sql( $_POST['levelId'] ), $endTime ) );
        echo json_encode([
                'expire_time'   => $endTime,
                'new_status'    => esc_html__( 'Active', 'ihc' ),
        ]);
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminUserSubscriptionPause()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !isset( $_POST['levelId'] ) || !isset( $_POST['uid'] ) || !isset( $_POST['currentExpireTime'] )  || !isset( $_POST['subscriptionId'] ) ){
            die;
        }
        $expireTime = strtotime( $_POST['currentExpireTime'] );
        if ( indeed_get_unixtimestamp_with_timezone() > $expireTime ){
            die;
        }

        $pause = true;
        $uid = $_POST['uid'];
        $lid = $_POST['levelId'];
        $subscriptionId = $_POST['subscriptionId'];
        //Pause Recurring Subscription
        $subscriptionPayment = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( $subscriptionId, 'payment_gateway' );
        if( $subscriptionPayment ){
            switch ( $subscriptionPayment ){
                  case 'stripe_connect':
                    $object = new \Indeed\Ihc\Gateways\StripeConnect();
                    $pause = $object->pause( $uid, $lid,  $subscriptionId );
                    break;
                default:
                  $paymentObject = apply_filters( 'ihc_payment_gateway_create_payment_object', false, $subscriptionPayment );
                  if ( $paymentObject !== false && method_exists ( $paymentObject , 'pause' ) ){
                      $pause = $object->pause( $uid, $lid,  $subscriptionId );
                  }
                  break;
            }
        }

        if($pause){
          echo json_encode(
            [
                    'remain_time'       => $expireTime - indeed_get_unixtimestamp_with_timezone(),
                    'expire_time'       => date( 'Y-m-d H:i:s', indeed_get_unixtimestamp_with_timezone() ),
                    'new_status'        => esc_html__( 'Paused', 'ihc' ),
            ]
          );
        }

        die;
    }

    /**
     * @param none
     * @return string
     */
    public function adminUserSubscriptionReactivate()
    {
          if ( !ihcIsAdmin() ){
              die;
          }
          if ( !isset( $_POST['levelId'] ) || !isset( $_POST['uid'] ) || !isset( $_POST['subscriptionId'] ) ){
              die;
          }
          $currentTime = indeed_get_unixtimestamp_with_timezone();
          $remainTime = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( esc_sql($_POST['subscriptionId']), 'remain_time' );
          $expireTime = $currentTime + $remainTime;

          $resume = true;
          $uid = $_POST['uid'];
          $lid = $_POST['levelId'];
          $subscriptionId = $_POST['subscriptionId'];
          //Pause Recurring Subscription
          $subscriptionPayment = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( $subscriptionId, 'payment_gateway' );
          if( $subscriptionPayment ){
              switch ( $subscriptionPayment ){
                    case 'stripe_connect':
                      $object = new \Indeed\Ihc\Gateways\StripeConnect();
                      $pause = $object->resume( $uid, $lid,  $subscriptionId );
                      break;
                  default:
                    $paymentObject = apply_filters( 'ihc_payment_gateway_create_payment_object', false, $subscriptionPayment );
                    if ( $paymentObject !== false && method_exists ( $paymentObject , 'resume' ) ){
                        $pause = $object->resume( $uid, $lid,  $subscriptionId );
                    }
                    break;
              }
          }

          if($resume){
            echo json_encode(
              [
                      'start_time'        => date( 'Y-m-d H:i:s', $currentTime ),
                      'expire_time'       => date( 'Y-m-d H:i:s', $expireTime ),
                      'new_status'        => esc_html__( 'Active', 'ihc' ),
              ]
            );
          }

          die;
    }

    /**
     * @param none
     * @return string
     */
    public function userPutSubscriptionOnPause()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        if ( !isset( $current_user->ID ) ){
            die;
        }
        if ( !isset( $_POST['subscriptionId'] ) ){
            die;
        }
        if ( !isset( $_POST['lid'] ) ){
            die;
        }
        $uid = $current_user->ID;
        $subscriptionId = esc_sql($_POST['subscriptionId']);
        $lid = esc_sql($_POST['lid']);
        if ( \Indeed\Ihc\UserSubscriptions::userHasSubscription( $uid, $lid ) === false ){
            die;
        }
        $currentTime = indeed_get_unixtimestamp_with_timezone();
        $subscriptionData = \Indeed\Ihc\UserSubscriptions::getOne( $uid, $lid );
        if ( $subscriptionData === false || !isset( $subscriptionData['expire_time'] ) ){
            die;
        }
        $remainTime = strtotime( $subscriptionData['expire_time'] ) - $currentTime;
        if ( $remainTime < 0 ){
            die;
        }
        $pause = true;
        //Pause Recurring Subscription
        $subscriptionPayment = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( $subscriptionId, 'payment_gateway' );
        if( $subscriptionPayment ){
            switch ( $subscriptionPayment ){
                  case 'stripe_connect':
                    $object = new \Indeed\Ihc\Gateways\StripeConnect();
                    $pause = $object->pause( $uid, $lid,  $subscriptionId );
                    break;
                default:
                  $paymentObject = apply_filters( 'ihc_payment_gateway_create_payment_object', false, $subscriptionPayment );
                  if ( $paymentObject !== false && method_exists ( $paymentObject , 'pause' ) ){
                      $pause = $object->pause( $uid, $lid,  $subscriptionId );
                  }
                  break;
            }
        }
        if($pause){
            $currentTime = date( 'Y-m-d H:i:s', $currentTime );
            \Indeed\Ihc\UserSubscriptions::updateSubscriptionTime( $uid, $lid, '', $currentTime, [] );
            \Indeed\Ihc\UserSubscriptions::updateStatusBySubscriptionId( $subscriptionId, 4 );
            \Indeed\Ihc\Db\UserSubscriptionsMeta::save( $subscriptionId, 'remain_time', $remainTime );
        }
    }

    /**
     * @param none
     * @return string
     */
    public function userPutSubscriptionResume()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        if ( !isset( $current_user->ID ) ){
            die;
        }
        if ( !isset( $_POST['subscriptionId'] ) ){
            die;
        }
        if ( !isset( $_POST['lid'] ) ){
            die;
        }
        $uid = $current_user->ID;
        $subscriptionId = esc_sql($_POST['subscriptionId']);
        $lid = esc_sql($_POST['lid']);

        $currentTime = indeed_get_unixtimestamp_with_timezone();
        $remainTime = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( $subscriptionId, 'remain_time' );
        $expireTime = $currentTime + $remainTime;
        $expireTime = date( 'Y-m-d H:i:s', $expireTime );

        $resume = true;
        //Resume Recurring Subscription
        $subscriptionPayment = \Indeed\Ihc\Db\UserSubscriptionsMeta::getOne( $subscriptionId, 'payment_gateway' );
        if( $subscriptionPayment ){
            switch ( $subscriptionPayment ){
                  case 'stripe_connect':
                    $object = new \Indeed\Ihc\Gateways\StripeConnect();
                    $pause = $object->resume( $uid, $lid,  $subscriptionId );
                    break;
                default:
                  $paymentObject = apply_filters( 'ihc_payment_gateway_create_payment_object', false, $subscriptionPayment );
                  if ( $paymentObject !== false && method_exists ( $paymentObject , 'resume' ) ){
                      $pause = $object->resume( $uid, $lid,  $subscriptionId );
                  }
                  break;
            }
        }
        if($resume){
          \Indeed\Ihc\UserSubscriptions::updateSubscriptionTime( $uid, $lid, '', $expireTime, [] );
          \Indeed\Ihc\UserSubscriptions::updateStatusBySubscriptionId( $subscriptionId, 1 );
          \Indeed\Ihc\Db\UserSubscriptionsMeta::save( $subscriptionId, 'remain_time', '' );
        }
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_stripe_connect_form_fields()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $stripeConnect = new \Indeed\Ihc\Gateways\StripeConnect();
        $response = $stripeConnect->getFormFields();
        echo $response;
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_stripe_connect_generate_payment_intent()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $data = []; // amount, currency, subscription_type ( single payment or recurring )
        $stripeConnect = new \Indeed\Ihc\Gateways\StripeConnect();
        $response = $stripeConnect->generatePaymentSecret( $data );
        echo json_encode( $response );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_check_braintree_form_fields()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $braintree = new \Indeed\Ihc\Gateways\Braintree();
        $response = $braintree->checkFields( $_POST );
        echo json_encode( $response );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_check_authorize_form_fields()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $authorize = new \Indeed\Ihc\Gateways\Authorize();
        $response = $authorize->checkFields( $_POST );
        echo json_encode( $response );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_deauth_from_stripe_connect()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        $response = wp_remote_get( $_POST['url'] );
        $code = isset( $response['response']['code'] ) ? $response['response']['code'] : false;
        if ( $code === false || !isset( $response['body'] ) ){
        	echo json_encode([
        										'code'        => 400,
        										'message'     => 'Error',
        	]);
        	die;
        }
        $responseBody = json_decode( $response['body'], true );

        if ( !isset( $responseBody['message'] ) || !isset( $responseBody['env'] ) ){
        	echo json_encode([
        										'code'        => $code,
        										'message'     => isset( $response['body'] ) ? $response['body'] : 'Error',
        	]);
        }
        if ( $code === 200 && $responseBody['message'] === 'Deauthorize completed' ){
        		// remove stripe keys
        		if ( $responseBody['env'] === 'sandbox' ){
        				// sandbox
        				update_option( 'ihc_stripe_connect_test_publishable_key', '' );
        				update_option( 'ihc_stripe_connect_test_client_secret', '' );
        				update_option( 'ihc_stripe_connect_test_account_id', '' );
        		} else {
        				// live
        				update_option( 'ihc_stripe_connect_publishable_key', '' );
        				update_option( 'ihc_stripe_connect_client_secret', '' );
        				update_option( 'ihc_stripe_connect_account_id', '' );
        		}
        }
        echo json_encode([
        									'code'        => $code,
        									'message'     => isset( $responseBody['message'] ) ? $responseBody['message'] : 'Error',
        ]);
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_get_stripe_connect_change_card_fields()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        require_once IHC_PATH . 'public/views/stripe-connect-change-card.php';
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_ajax_do_stripe_connect_change_card()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }

        $paymentMethodId = isset( $_POST['payment_method_id'] ) ? esc_sql( $_POST['payment_method_id'] ) : false;
        $umpSubscriptionId = isset( $_POST['ump_subscription_id'] ) ? esc_sql( $_POST['ump_subscription_id'] ) : false;
        $uid = isset( $_POST['uid'] ) ? esc_sql( $_POST['uid'] ) : false;


        $stripe = new \Indeed\Ihc\Gateways\StripeConnect();
        $result = $stripe->changePaymentMethodForUser( $paymentMethodId, $umpSubscriptionId );

        echo json_encode( $result );
        die;
    }

    /**
      * @param none
      * @return none
      */
    public function ihc_ajax_stripe_connect_generate_payment_intent()
    {
        if ( !ihcPublicVerifyNonce() ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        if ( !isset( $_POST['session'] ) || $_POST['session'] === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $checkoutHash = base64_decode( $_POST['session'] );
        if ( $checkoutHash === false || $checkoutHash === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        try {
            $checkoutData = unserialize( $checkoutHash );
        } catch ( \Exception $e ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        if ( !isset( $checkoutData['amount'] ) || !isset( $checkoutData['lid'] ) ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $checkoutData['payment_method'] = isset( $_POST['payment_method'] ) ? $_POST['payment_method'] : '';
        if ( $checkoutData['payment_method'] === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $stripeConnect = new \Indeed\Ihc\Gateways\StripeConnect();
        $response = $stripeConnect->createPaymentIntent( $checkoutData );
        echo json_encode( $response );
        die;
    }

    /**
      * @param none
      * @return none
      */
    public function ihc_ajax_stripe_connect_generate_setup_intent()
    {
        if ( !ihcPublicVerifyNonce() ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        if ( !isset( $_POST['session'] ) || $_POST['session'] === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $checkoutHash = base64_decode( $_POST['session'] );
        if ( $checkoutHash === false || $checkoutHash === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        try {
            $checkoutData = unserialize( $checkoutHash );
        } catch ( \Exception $e ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        if ( !isset( $checkoutData['lid'] ) ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $checkoutData['payment_method'] = isset( $_POST['payment_method'] ) ? $_POST['payment_method'] : '';
        if ( $checkoutData['payment_method'] === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $stripeConnect = new \Indeed\Ihc\Gateways\StripeConnect();
        $response = $stripeConnect->createSetupIntent( $checkoutData );
        echo json_encode( $response );
        die;
    }

    public function ihc_ajax_stripe_connect_generate_setup_intent_no_payment()
    {
        if ( !ihcPublicVerifyNonce() ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $data['payment_method'] = isset( $_POST['payment_method'] ) ? $_POST['payment_method'] : '';
        if ( $data['payment_method'] === '' ){
            echo json_encode( [
                                'status'        => 0,
                                'message'       => esc_html__( 'Error. Please try again!', 'ihc' ),
            ] );
            die;
        }
        $stripeConnect = new \Indeed\Ihc\Gateways\StripeConnect();
        $response = $stripeConnect->createSetupIntent( $data );
        echo json_encode( $response );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_ajax_prorate_delete_group()
    {
        if ( !ihcIsAdmin() ){
            die;
        }

        if ( !isset( $_POST['group_id'] ) ){
            die;
        }
        $groupId = esc_sql( $_POST['group_id'] );

        \Indeed\Ihc\Db\ProrateMembershipGroups::deleteOne( $groupId );
        echo 1;
        die;

    }

    /**
     * @param none
     * @return none
     */
    public function ihc_ajax_send_double_email_verification()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !isset( $_POST['user_id'] ) || $_POST['user_id'] === '' ){
            echo json_encode([
                  'status'        => 0,
                  'title'         => esc_html__( 'Error', 'ihc' ),
                  'message'       => esc_html__( 'Something went wrong. Try again!', 'ihc' ),
            ]);
            die;
        }
        $uid = isset( $_POST['user_id'] ) ? esc_sql( $_POST['user_id'] ) : 0;
        if ( $uid === [] ){
            echo json_encode([
                  'status'        => 0,
                  'title'         => esc_html__( 'Error', 'ihc' ),
                  'message'       => esc_html__( 'Something went wrong. Try again!', 'ihc' )
            ]);
            die;
        }
        $lids = \Indeed\Ihc\UserSubscriptions::getAllForUser( $uid );
        /*
        if ( $lids === [] ){
            echo json_encode([
                  'status'        => 0,
                  'title'         => esc_html__( 'Error', 'ihc' ),
                  'message'       => esc_html__( 'Something went wrong. Try again!', 'ihc' )
            ]);
            die;
        }
        */
        $lid = isset( $lids[1]['level_id'] ) ? $lids[1]['level_id'] : 0;
        /*
        if ( $lid === 0 ){
            echo json_encode([
                  'status'        => 0,
                  'title'         => esc_html__( 'Error', 'ihc' ),
                  'message'       => esc_html__( 'Something went wrong. Try again!', 'ihc' )
            ]);
            die;
        }
        */
        $hash = ihc_random_str( 10 );
        //put the hash into user option
        update_user_meta( $uid, 'ihc_activation_code', $hash );
        //set ihc_verification_status @ -1
        update_user_meta( $uid, 'ihc_verification_status', -1 );

        $activationUrl = site_url();
        $activationUrl = add_query_arg( 'ihc_action', 'user_activation', $activationUrl );
        $activationUrl = add_query_arg( 'uid', $uid, $activationUrl );
        $activationUrl = add_query_arg( 'ihc_code', $hash, $activationUrl );

        $lid = isset( $postData['lid'] ) ? $postData['lid'] : '';
        do_action( 'ihc_action_double_email_verification', $uid, $lid, [ '{verify_email_address_link}' => $activationUrl ] );
        echo json_encode([
              'status'        => 1,
              'title'         => esc_html__( 'Success', 'ihc' ),
              'message'       => esc_html__( 'Email verification successfully sent.', 'ihc' )
        ]);
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_ajax_close_admin_mk_notice()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !isset( $_POST['target'] ) ){
            die;
        }
        $target = esc_sql( $_POST['target'] );
        switch ( $target ){
            case 'divi':
              update_option( 'ihc_disable_divi_mk_message', time() );
              break;
            case 'elementor':
              update_option( 'ihc_disable_elementor_mk_message', time() );
              break;
        }
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_ajax_custom_admin_ajax_gate()
    {
        $viaWpAjax = true;
        include IHC_PATH . 'admin/ajax-custom.php';
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function ihc_ajax_public_upload_file()
    {
        $viaWpAjax = true;
        include IHC_PATH . 'public/ajax-upload.php';
        die;
    }

    public function ihc_ajax_public_custom_ajax_gate()
    {
        $viaWpAjax = true;
        include IHC_PATH . 'public/ajax-custom.php';
        die;
    }

    public function ihc_admin_delete_account_page_menu_item()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        if ( !ihcAdminVerifyNonce() ){
            die;
        }
        if ( !isset( $_POST['slug'] ) || $_POST['slug'] === '' ){
            die;
        }
        \Ihc_Db::account_page_menu_delete_custom_item( $_POST['slug'] );
        die;
    }

    public function ihc_close_admin_registration_notice()
    {
        if ( !ihcIsAdmin() ){
            die;
        }
        update_option( 'ihc_hide_admin_license_registration_notice', 1 );
        echo 1;
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_forms_check_one_field()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }

        $uid = isset( $current_user->ID ) ? $current_user->ID : 0;
        if ( !isset( $_POST['name'] ) || $_POST['name'] === '' ){
            echo json_encode([
                                'status'          => -1,
                                'message'         => esc_html__( 'Field name not provided', 'ihc' ),
            ]);
            die;
        }
        if ( !isset( $_POST['value'] ) ){
            echo json_encode([
                                'status'          => -1,
                                'message'         => esc_html__( 'Field value not provided', 'ihc' ),
            ]);
            die;
        }

        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $ValidateForm->resetInputProperties()
                     ->setFieldName( esc_sql( $_POST['name'] ) )
                     ->setCurrentValue( esc_sql( $_POST['value'] ) )
                     ->setIsRequired() // true or false
                     ->setUid( $uid ); // int or null if the user is not registered yet
        if ( isset( $_POST['second_value'] ) ){
            // conditional logic here
            $ValidateForm->setCompareValue( esc_sql( $_POST['second_value'] ) );
        }

        $result = $ValidateForm->isValid();
        echo json_encode( $result );
        die;

    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_forms_check_all_fields()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $uid = isset( $current_user->ID ) ? $current_user->ID : 0;
        if ( !isset( $_POST['fields_obj'] ) || $_POST['fields_obj'] === '' ){
            echo json_encode([
                                'status'          => -1,
                                'message'         => esc_html__( 'Fields not provided', 'ihc' ),
            ]);
            die;
        }

        $ValidateForm = new \Indeed\Ihc\ValidateForm();

        if ( count( $_POST['fields_obj'] ) === 0 ){
            echo 1;
            die;
        }

        $returnArr = [];
        foreach ( $_POST['fields_obj'] as $field ){

            $ValidateForm->resetInputProperties()
                           ->setFieldName( esc_sql( $field['name'] ) )
                           ->setCurrentValue( esc_sql( $field['value'] ) )
                           ->setIsRequired() // true or false
                           ->setUid( $uid ); // int or null if the user is not registered yet

            if ( isset( $field['second_value'] ) ){
                // conditional logic here
                $ValidateForm->setCompareValue( esc_sql( $field['second_value'] ) );
            }

            $result = $ValidateForm->isValid();
            $returnArr[] = [
                                     'name'       => $field['name'],
                                     'value'      => $result['status'],
                                     'message'    => $result['message'],
            ];
        }
        echo json_encode( $returnArr );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_profile_edit_check_one_conditional_logic()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $uid = isset( $current_user->ID ) ? $current_user->ID : 0;
        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $result = $ValidateForm->resetInputProperties()
                     ->setFieldName( esc_sql( $_POST['field'] ) )
                     ->setUid( $uid )
                     ->checkConditionalLogic( esc_sql( $_POST['value'] ) );
        echo (int)$result;
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_edit_profile_check_unique_field()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $uid = isset( $current_user->ID ) ? $current_user->ID : 0;
        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $result = $ValidateForm->setFieldName( esc_sql( $_POST['meta_key'] ) )
                               ->setCurrentValue( esc_sql( $_POST['meta_value'] ) )
                               ->setUid( $uid )
                               ->isValid();
        echo json_encode( $result );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_edit_profile_check_conditional_text_field()
    {
        global $current_user;
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $uid = isset( $current_user->ID ) ? $current_user->ID : 0;
        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $result = $ValidateForm->setFieldName( esc_sql( $_POST['meta_key'] ) )
                               ->setCurrentValue( esc_sql( $_POST['meta_value'] ) )
                               ->setUid( $uid )
                               ->isValid();
        echo json_encode( $result );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_get_state_field_as_html()
    {
        if ( !ihcPublicVerifyNonce() ){
        		die;
        }
      	if (isset($_POST['country'])){
            $params = empty($_POST['is_edit'] ) ? [] : [ 'avoid_reload_cart'=> true ];
      		  echo ihc_get_state_field_str( esc_sql($_POST['country']), $params );
      	}
      	die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_register_forms_check_one_field()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }

        if ( !isset( $_POST['name'] ) || $_POST['name'] === '' ){
            echo json_encode([
                                'status'          => -1,
                                'message'         => esc_html__( 'Field name not provided', 'ihc' ),
            ]);
            die;
        }
        if ( !isset( $_POST['value'] ) ){
            echo json_encode([
                                'status'          => -1,
                                'message'         => esc_html__( 'Field value not provided', 'ihc' ),
            ]);
            die;
        }

        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $ValidateForm->resetInputProperties()
                     ->setFieldName( esc_sql( $_POST['name'] ) )
                     ->setCurrentValue( esc_sql( $_POST['value'] ) )
                     ->setIsRequired();
        if ( isset( $_POST['second_value'] ) ){
            // conditional logic here
            $ValidateForm->setCompareValue( esc_sql( $_POST['second_value'] ) );
        }

        $result = $ValidateForm->isValid();
        echo json_encode( $result );
        die;

    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_register_form_check_all_fields()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        if ( !isset( $_POST['fields_obj'] ) || $_POST['fields_obj'] === '' ){
            echo json_encode([
                                'status'          => -1,
                                'message'         => esc_html__( 'Fields not provided', 'ihc' ),
            ]);
            die;
        }

        $ValidateForm = new \Indeed\Ihc\ValidateForm();

        if ( count( $_POST['fields_obj'] ) === 0 ){
            echo 1;
            die;
        }

        $returnArr = [];
        foreach ( $_POST['fields_obj'] as $field ){

            $ValidateForm->resetInputProperties()
                           ->setFieldName( esc_sql( $field['name'] ) )
                           ->setCurrentValue( esc_sql( $field['value'] ) )
                           ->setIsRequired(); // true or false

            if ( isset( $field['second_value'] ) ){
                // conditional logic here
                $ValidateForm->setCompareValue( esc_sql( $field['second_value'] ) );
            }

            $result = $ValidateForm->isValid();
            $returnArr[] = [
                                     'name'       => $field['name'],
                                     'value'      => $result['status'],
                                     'message'    => $result['message'],
            ];
        }
        echo json_encode( $returnArr );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_register_form_check_one_conditional_logic()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $result = $ValidateForm->resetInputProperties()
                               ->setFieldName( esc_sql( $_POST['field'] ) )
                               ->checkConditionalLogic( esc_sql( $_POST['value'] ) );
        echo (int)$result;
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_register_form_check_unique_field()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $result = $ValidateForm->setFieldName( esc_sql( $_POST['meta_key'] ) )
                               ->setCurrentValue( esc_sql( $_POST['meta_value'] ) )
                               ->isValid();
        echo json_encode( $result );
        die;
    }

    /**
     * @param none
     * @return string
     */
    public function ihc_ajax_register_form_check_conditional_text_field()
    {
        if ( !ihcPublicVerifyNonce() ){
            die;
        }
        $ValidateForm = new \Indeed\Ihc\ValidateForm();
        $result = $ValidateForm->setFieldName( esc_sql( $_POST['meta_key'] ) )
                               ->setCurrentValue( esc_sql( $_POST['meta_value'] ) )
                               ->isValid();
        echo json_encode( $result );
        die;
    }


}
