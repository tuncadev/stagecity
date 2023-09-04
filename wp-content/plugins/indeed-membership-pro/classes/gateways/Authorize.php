<?php
namespace Indeed\Ihc\Gateways;

class Authorize extends \Indeed\Ihc\Gateways\PaymentAbstract
{
    protected $paymentType                    = 'authorize'; // slug. cannot be empty.

    protected $paymentRules                   = [
                'canDoRecurring'						                  => true, // does current payment gateway supports recurring payments.
                'canDoTrial'							                    => true, // does current payment gateway supports trial subscription
                'canDoTrialFree'						                  => true, // does current payment gateway supports free trial subscription
                'canApplyCouponOnRecurringForFirstPayment'		=> true, // if current payment gateway support coupons on recurring payments only for the first transaction
                'canApplyCouponOnRecurringForFirstFreePayment'=> true, // if current payment gateway support coupons with 100% discount on recurring payments only for the first transaction.
                'canApplyCouponOnRecurringForEveryPayment'	  => true, // if current payment gateway support coupons on recurring payments for every transaction
                'paymentMetaSlug'                             => 'payment_authorize', // payment gateway slug. exenple: paypal, stripe, etc.
                'returnUrlAfterPaymentOptionName'             => '', // option name ( in wp_option table ) where it's stored the return URL after a payment is done.
                'returnUrlOnCancelPaymentOptionName'          => '', // option name ( in wp_option table ) where it's stored the return URL after a payment is canceled.
                'paymentGatewayLanguageCodeOptionName'        => '', // option name ( in wp_option table ) where it's stored the language code.
    ]; // some payment does not support all our features
    protected $intervalSubscriptionRules      = [
                'daysSymbol'               => 'days',
                'weeksSymbol'              => '',
                'monthsSymbol'             => 'months',
                'yearsSymbol'              => '',
                'daysSupport'              => true,
                'daysMinLimit'             => 7,
                'daysMaxLimit'             => 365,
                'weeksSupport'             => false,
                'weeksMinLimit'            => 1,
                'weeksMaxLimit'            => 52,
                'monthsSupport'            => true,
                'monthsMinLimit'           => 1,
                'monthsMaxLimit'           => 12,
                'yearsSupport'             => false,
                'yearsMinLimit'            => 1,
                'yearsMaxLimit'            => 1,
                'maximumRecurrenceLimit'   => 9999, // leave this empty for unlimited
                'minimumRecurrenceLimit'   => 1,
                'forceMaximumRecurrenceLimit'   => true,
    ];
    protected $intervalTrialRules             = [
                              'daysSymbol'               => 'days',
                              'weeksSymbol'              => '',
                              'monthsSymbol'             => 'months',
                              'yearsSymbol'              => '',
                              'supportCertainPeriod'     => false,
                              'supportCycles'            => true,
                              'cyclesMinLimit'           => 1,
                              'cyclesMaxLimit'           => '',
                              'daysSupport'              => true,
                              'daysMinLimit'             => 7,
                              'daysMaxLimit'             => 365,
                              'weeksSupport'             => false,
                              'weeksMinLimit'            => false,
                              'weeksMaxLimit'            => false,
                              'monthsSupport'            => true,
                              'monthsMinLimit'           => 1,
                              'monthsMaxLimit'           => 12,
                              'yearsSupport'             => false,
                              'yearsMinLimit'            => false,
                              'yearsMaxLimit'            => false,
    ];

    protected $stopProcess                    = false;
    protected $inputData                      = []; // input data from user
    protected $paymentOutputData              = [];
    protected $paymentSettings                = []; // api key, some credentials used in different payment types

    protected $paymentTypeLabel               = 'Authorize Payment Gateway'; // label of payment
    protected $redirectUrl                    = ''; // redirect to payment gateway or next page
    protected $defaultRedirect                = ''; // redirect home
    protected $errors                         = [];

    /**
     * @param none
     * @return object
     */
    public function check()
    {
        // default checks from abstract class
        parent::check();

        // check the payment details
        if ( isset( $_POST['ihcpay_cardholderName'] ) ){
            $checkFields = $this->checkFields( $_POST );
            if ( $checkFields['status'] === 0 ){
                $this->stopProcess = true;
                $this->errors[] =  implode( ',', $checkFields['errors'] );
            }
        }

        return $this;
    }

    /**
     * @param array
     * @return array
     */
    public function checkFields( $data=[] )
    {
        $data['status'] = 1;
        $fullName = isset( $data['ihcpay_cardholderName'] ) ? esc_sql( $data['ihcpay_cardholderName'] ) : '';
        $number = isset( $data['ihcpay_card_number'] ) ? esc_sql( $data['ihcpay_card_number'] ) : '';
        $expire = isset( $data['ihcpay_card_expire'] ) ? esc_sql( $data['ihcpay_card_expire'] ) : '';
        if ( $fullName === '' ){
            $data['status'] = 0;
            $data['errors']['ihcpay_cardholderName'] = esc_html__('Name field can only contain letters', 'ihc');
        }
        if ( $number === '' ){
            $data['status'] = 0;
            $data['errors']['ihcpay_card_number'] = esc_html__('Invalid card number', 'ihc');
        }
        if ( $expire === '' ){
            $data['status'] = 0;
            $data['errors']['ihcpay_card_expire'] = esc_html__('Please enter the expiration date in the MMYY format', 'ihc');
        }
        return $data;
    }

    /**
     * @param none
     * @return object
     */
    public function charge()
    {
        if ( $this->paymentOutputData['is_recurring'] ){
            // recurring
            $this->recurringPayment();
        } else {
            // single payment
            $this->singlePayment();
        }

        return $this;
    }

    /**
     * @param none
     * @return none
     */
    public function singlePayment()
    {
        $siteUrl = trailingslashit( site_url() );
        $relayUrl = add_query_arg( 'ihc_action', 'authorize', $siteUrl );

        // an invoice is generated using the date and time
    		$invoice	= date('YmdHis');
    		// a sequence number is randomly generated
    		$sequence	= rand(1, 1000);

    		// a timestamp is generated
        $date = new \DateTime();
        $date->setTimestamp( time() );
        $date->setTimezone( new \DateTimeZone('UTC') );
        $time = $date->format('Y-m-d H:i:s');
        $timeStamp = strtotime( $time );

        $testMode		= "false";

        if( phpversion() >= '5.1.2' ){
            $fingerprint = hash_hmac("md5", $this->paymentSettings['ihc_authorize_login_id'] . "^" . $sequence . "^" . $timeStamp . "^" . $this->paymentOutputData['amount'] . "^" . $this->paymentOutputData['currency'], $this->paymentSettings['ihc_authorize_transaction_key'] );
        } else {
            $fingerprint = bin2hex(mhash(MHASH_MD5, $this->paymentSettings['ihc_authorize_login_id'] . "^" . $sequence . "^" . $timeStamp . "^" . $this->paymentOutputData['amount'] . "^". $this->paymentOutputData['currency'], $this->paymentSettings['ihc_authorize_transaction_key'] ));
        }

        if ( $this->paymentSettings['ihc_authorize_sandbox'] ){
            // sandbox
            $url = 'https://test.authorize.net/gateway/transact.dll';
            \Ihc_User_Logs::write_log( esc_html__('Authorize Payment: set Sandbox mode', 'ihc'), 'payments');
        } else{
            // live
            $url = 'https://secure.authorize.net/gateway/transact.dll';
            \Ihc_User_Logs::write_log( esc_html__('Authorize Payment: set Live mode', 'ihc'), 'payments');
        }

        $args = [
                  'x_login'               => $this->paymentSettings['ihc_authorize_login_id'],
                  'x_amount'              => $this->paymentOutputData['amount'],
                  'x_currency_code'       => $this->paymentOutputData['currency'],
                  'x_type'                => 'AUTH_CAPTURE',
                  'x_description'         => $this->paymentOutputData['level_description'],
                  'x_invoice_num'         => $invoice,
                  'x_fp_sequence'         => $sequence,
                  'x_fp_timestamp'        => $timeStamp,
                  'x_fp_hash'             => $fingerprint,
                  'x_relay_response'      => 'FALSE',
                  'x_relay_url'           => $relayUrl,
                  'x_cust_id'             => $this->paymentOutputData['uid'],
                  'x_po_num'              => $this->paymentOutputData['order_identificator'], // x_po_num will hold order identificator, in older implementation this field keep level id
                  'x_test_request'        => $testMode,
                  'x_show_form'           => 'PAYMENT_FORM',
                  'target_url'            => $url,
        ];
        include IHC_PATH . 'classes/gateways/libraries/authorize/form.php';
        exit;
    }

    /**
     * @param none
     * @return array
     */
    public function recurringPayment()
    {
      if ( $this->paymentSettings['ihc_authorize_sandbox'] ){
        $url = "https://apitest.authorize.net/xml/v1/request.api";
      } else {
        $url = 'https://api.authorize.net/xml/v1/request.api';
      }

      if ( !empty( $this->paymentOutputData['is_trial'] ) && $this->paymentOutputData['first_payment_interval_value'] !== '' ){
          $temporary = $this->paymentOutputData['subscription_cycles_limit'] + $this->paymentOutputData['first_payment_interval_value'];
          if ( $temporary > $this->intervalSubscriptionRules['maximumRecurrenceLimit'] ){
              $temporary = $this->intervalSubscriptionRules['maximumRecurrenceLimit'];
          }
          $this->paymentOutputData['subscription_cycles_limit'] = $temporary;
      }

      $content =
      "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
      "<ARBCreateSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
      "<merchantAuthentication>".
      "<name>" . $this->paymentSettings['ihc_authorize_login_id'] . "</name>".
      "<transactionKey>" . $this->paymentSettings['ihc_authorize_transaction_key'] . "</transactionKey>".
      "</merchantAuthentication>".
      "<refId>" . 'ihc_' . $this->paymentOutputData['uid'] . '_' . $this->paymentOutputData['lid'] . "</refId>".
      "<subscription>".
      "<name>" . $this->paymentOutputData['level_label'] . "</name>".
      "<paymentSchedule>".
      "<interval>".
      "<length>" . $this->paymentOutputData['interval_value'] . "</length>".
      "<unit>" . $this->paymentOutputData['interval_type'] . "</unit>".
      "</interval>".
      "<startDate>" . date("Y-m-d") . "</startDate>". //
      "<totalOccurrences>" . $this->paymentOutputData['subscription_cycles_limit'] . "</totalOccurrences>";

      //TRIAL
      if ( !empty( $this->paymentOutputData['is_trial'] ) && $this->paymentOutputData['first_payment_interval_value'] !== '' ){
          $content .= "<trialOccurrences>" . $this->paymentOutputData['first_payment_interval_value'] . "</trialOccurrences>";
      }

      $content .= "</paymentSchedule>".
      "<amount>" . urlencode( $this->paymentOutputData['amount'] ) . "</amount>";

      //TRIAL
      if ( !empty($this->paymentOutputData['is_trial']) ){
        $content .= "<trialAmount>" . urlencode( $this->paymentOutputData['initial_first_amount'] ) . "</trialAmount>";
      }

      // user details
      $country = isset($_POST['ihc_country']) ? esc_sql( $_POST['ihc_country'] ) : '';
      $state = (isset($_POST['ihc_state'])) ? esc_sql( $_POST['ihc_state'] ) : '';
      $zip = (isset($_POST['zip'])) ? esc_sql( $_POST['zip'] ) : '';
      $city = (isset($_POST['city'])) ? esc_sql( $_POST['city'] ) : '';
      $address = (isset($_POST['addr1'])) ? esc_sql( $_POST['addr1'] ) : '';
      if ( isset( $this->paymentOutputData['uid'] ) ){
          if ( $country === '' ){
              $country = get_user_meta( $this->paymentOutputData['uid'], 'ihc_country', true );
          }
          if ( $state === '' ){
              $state = get_user_meta( $this->paymentOutputData['uid'], 'ihc_state', true );
          }
          if ( $zip === '' ){
              $zip = get_user_meta( $this->paymentOutputData['uid'], 'zip', true );
          }
          if ( $city === '' ){
              $city = get_user_meta( $this->paymentOutputData['uid'], 'city', true );
          }
          if ( $address === '' ){
              $address = get_user_meta( $this->paymentOutputData['uid'], 'addr1', true );
          }
      }

      $fullName = isset( $_POST['ihcpay_cardholderName'] ) ? esc_sql( $_POST['ihcpay_cardholderName'] ) : '';
      $fullNameArray = explode( ' ', $fullName );
      $key = key( array_slice( $fullNameArray, -1, 1, true ) );
      $lastName = isset( $fullNameArray[$key] ) ? $fullNameArray[$key] : '';
      $firstName = str_replace( $lastName, '', $fullName );
      $number = isset( $_POST['ihcpay_card_number'] ) ? esc_sql( $_POST['ihcpay_card_number'] ) : '';
      $expire = esc_sql( $_POST['ihcpay_card_expire'] );

      $content .= "<payment>".
      "<creditCard>".
      "<cardNumber>$number</cardNumber>".
      "<expirationDate>$expire</expirationDate>".
      "</creditCard>".
      "</payment>".
      "<billTo>".
      "<firstName>$firstName</firstName>".
      "<lastName>$lastName</lastName>".
      "<address>$address</address>".
      "<city>$city</city>".
      "<state>$state</state>".
      "<zip>$zip</zip>".
      "<country>$country</country>".
      "</billTo>".
      "</subscription>".
      "</ARBCreateSubscriptionRequest>";

      //send the xml via curl
      $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);

      if (!empty($response)) {
        list ($refId, $resultCode, $code, $text, $subscriptionId) = $this->parse_return($response);
        if($resultCode == "Ok"){
            // success
            $returnData = [
                                  'message' => 'Success',
                                  'status'  => 1,
            ];
            // store subscriptionId
            $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
            $orderMeta->save( $this->paymentOutputData['order_id'], 'subscription_id', $subscriptionId );
            $orderMeta->save( $this->paymentOutputData['order_id'], 'order_identificator', $subscriptionId );

            // make completed
            $this->completeLevelPayment( $this->paymentOutputData );
        } else {
            $returnData = [
                                  'message' => 'Something went wrong',
                                  'status'  => 0,
            ];
        }
      } else {
          $returnData = [
                                'message' => 'Could not connect to Authorize.net',
                                'status'  => 0,
          ];
      }
      return $returnData;
    }

    /**
     * @param none
     * @return none
     */
    public function webhook()
    {
      if ( !isset($_POST) ){
          echo '============= Ultimate Membership Pro - Authorize Webhook ============= ';
          echo '<br/><br/>No Payments details sent. Come later';
          exit;
      }

      
      if ( isset($_POST['x_MD5_Hash']) && isset($_POST['x_response_code'])  && !empty($_POST['x_cust_id']) && !empty($_POST['x_po_num']) ){
          // Single Payment
          $orderIdentificator = esc_sql( $_POST['x_po_num'] );
          $transactionId = isset( $_POST['x_trans_id'] ) ? $_POST['x_trans_id'] : '';
          $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
          $orderId = $orderMeta->getIdFromMetaNameMetaValue( 'order_identificator', $orderIdentificator );
          $orderObject = new \Indeed\Ihc\Db\Orders();
          $orderData = $orderObject->setId( $orderId )
                                   ->fetch()
                                   ->get();

         
          if ( isset( $orderData->uid ) && isset( $orderData->lid ) ){
              $uid = $orderData->uid;
              $lid = $orderData->lid;
          } else {
              // old version
              $uid = $_POST['x_cust_id'];
              $lid = $_POST['x_po_num'];
          }
          switch ($_POST['x_response_code']){
        		case '1':
              // success
              $this->webhookData = [
                  'transaction_id'      => $transactionId,
                  'order_identificator' => $orderIdentificator,
                  'txn_id'              => $transactionId,
                  'uid'                 => $uid,
                  'lid'                 => $lid,
                  'amount'              => '',
                  'currency'            => '',
                  'payment_details'     => '',
                  'subscription_id'     => '',
                  'payment_status'      => 'completed', // values can be : completed, cancel, pending, failed
              ];
        			break;
        		case '2':
        		case '3':
        			// fail
              $this->webhookData = [
                  'transaction_id'      => $transactionId,
                  'order_identificator' => $orderIdentificator,
                  'uid'                 => $uid,
                  'lid'                 => $lid,
                  'amount'              => '',
                  'currency'            => '',
                  'payment_details'     => '',
                  'subscription_id'     => '',
                  'payment_status'      => 'failed', // values can be : completed, cancel, pending, failed
              ];
        			break;
        		case '4':
              // pending
              $this->webhookData = [
                  'transaction_id'      => $transactionId,
                  'order_identificator' => $orderIdentificator,
                  'uid'                 => $uid,
                  'lid'                 => $lid,
                  'amount'              => '',
                  'currency'            => '',
                  'payment_details'     => '',
                  'subscription_id'     => '',
                  'payment_status'      => 'pending', // values can be : completed, cancel, pending, failed
              ];
        			break;
        	}
      } else if ( isset($_POST['x_MD5_Hash']) && isset($_POST['x_subscription_id']) && isset($_POST['x_response_code']) ){
          // Recurring Payment

          $subscriptionId = isset( $_POST['x_subscription_id'] ) ? esc_sql( $_POST['x_subscription_id'] ) : '';
          $transactionId = isset( $_POST['x_trans_id'] ) ? esc_sql( $_POST['x_trans_id'] ) : '';
          $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
          $orderId = $orderMeta->getIdFromMetaNameMetaValue( 'subscription_id', $subscriptionId );
          $orderObject = new \Indeed\Ihc\Db\Orders();
          $orderData = $orderObject->setId( $orderId )
                                   ->fetch()
                                   ->get();
          if ( isset( $orderData->uid ) && isset( $orderData->lid ) ){
               $uid = $orderData->uid;
               $lid = $orderData->lid;
          } else {
              // old implementation
              global $wpdb;
              $query = $wpdb->prepare("SELECT id,txn_id,u_id,payment_data,history,orders,paydate FROM {$wpdb->prefix}indeed_members_payments WHERE txn_id=%s ORDER BY paydate DESC LIMIT 1", $subscriptionId );
              $dbData = $wpdb->get_row( $query );
              $uid = isset( $dbData->u_id ) ? $dbData->u_id : 0;
              if ( isset($dbData->payment_data) ){
                $paymentDataArray = json_decode($dbData->payment_data, TRUE);
                $lid = $paymentDataArray['level'];
            }
          }

          switch ($_POST['x_response_code']){
    			  case '1':
              // completed
              $this->webhookData = [
                  'transaction_id'      => $transactionId,
                  'txn_id'              => $transactionId,
                  'order_identificator' => '',
                  'uid'                 => $uid,
                  'lid'                 => $lid,
                  'amount'              => '',
                  'currency'            => '',
                  'payment_details'     => '',
                  'subscription_id'     => $subscriptionId,
                  'payment_status'      => 'completed', // values can be : completed, cancel, pending, failed
              ];
    				  break;
    			  case '2':
    			  case '3':
              // failed
              $this->webhookData = [
                  'transaction_id'      => $transactionId,
                  'txn_id'              => $transactionId,
                  'order_identificator' => '',
                  'uid'                 => $uid,
                  'lid'                 => $lid,
                  'amount'              => '',
                  'currency'            => '',
                  'payment_details'     => '',
                  'subscription_id'     => $subscriptionId,
                  'payment_status'      => 'failed', // values can be : completed, cancel, pending, failed
              ];
    				  break;
    			  case '4':
              // pending
              $this->webhookData = [
                  'transaction_id'      => $transactionId,
                  'txn_id'              => $transactionId,
                  'order_identificator' => '',
                  'uid'                 => $uid,
                  'lid'                 => $lid,
                  'amount'              => '',
                  'currency'            => '',
                  'payment_details'     => '',
                  'subscription_id'     => $subscriptionId,
                  'payment_status'      => 'pending', // values can be : completed, cancel, pending, failed
              ];
    				  break;
    		  }
      }


    }

    /**
     * @param string
     * @return array
     */
    private function parse_return( $content='' )
    {
      $refId = '00';
      $resultCode = $this->substring_between($content,'<resultCode>','</resultCode>');
      $code = $this->substring_between($content,'<code>','</code>');
      $text = $this->substring_between($content,'<text>','</text>');
      $subscriptionId = $this->substring_between($content,'<subscriptionId>','</subscriptionId>');
      return array ($refId, $resultCode, $code, $text, $subscriptionId);
    }

    /**
     * @param string
     * @param string
     * @param string
     * @return array
     */
    private function substring_between( $haystack='', $start='', $end='' )
    {
      if (strpos($haystack,$start) === false || strpos($haystack,$end) === false){
        return false;
      } else {
        $start_position = strpos($haystack,$start)+strlen($start);
        $end_position = strpos($haystack,$end);
        return substr($haystack,$start_position,$end_position-$start_position);
      }
    }

    /**
     * @param int
     * @param int
     * @return none
     */
    public function afterRefund( $uid=0, $lid=0 )
    {

    }

    /**
     * @param int
     * @param int
     * @param string
     * @return none
     */
    public function cancel( $uid=0, $lid=0, $transactionId='' )
    {

    }


}
