<?php
namespace Indeed\Ihc;

class Checkout
{
  /**
   * @var int
   */
    private $lid = 0;
    /**
     * @var int
     */
    private $uid = 0;

    private $currency = 'USD';

    private $country =  '';

    private $state = '';

    private $coupon = '';

    private $dynamic_price = '';

    private $selectedPayment = '';

    private $metaData = [];

    private $levelaData = [];

    private $isRegistered = FALSE;

    private $OnRegistration = '';

    private $OnRegistrationLevel = '';


    /**
     * @param none
     * @return none
     */
    public function __construct()
    {
        // the shortcode
        add_shortcode( 'ihc-checkout-page', [ $this, 'output' ] );

        // Ajax - update the checkout page
        add_action( 'wp_ajax_ihc_checkout_subscription_details', [ $this, 'subscriptionDetails' ] );
        add_action( 'wp_ajax_nopriv_ihc_checkout_subscription_details', [ $this, 'subscriptionDetails' ] );

        // processing after submit
        add_action( 'init', [ $this, 'processingSubmit' ], 999 );

        // CheckPrettyLinks
        //add_action( 'init', [ $this, 'getMembershipBasedSlug' ], 999 );

        /// CHECKOUT OnRegisterPage
        add_filter('ump_before_submit_form', [ $this, 'CheckoutRegistration' ], 100, 4 );
    }

    /**
     * @param int
     * @return object
     */
    public function setLid( $lid=0 )
    {
        $this->lid = $lid;
        return $this;
    }

    /**
     * @param int
     * @return object
     */
    public function setUid( $uid=0 )
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @param int
     * @return object
     */
    public function setArgs( $args=[] )
    {
      // set the user id

      global $current_user;
      if ( $this->uid === 0 && isset( $current_user->ID ) ){
          $this->uid = $current_user->ID;
      }

      // set the level id
      if ( $this->lid === 0 && isset( $_GET['lid'] ) ){
          $this->lid = $_GET['lid'];
      }
      if ( $this->lid === 0 && isset( $_POST['lid'] ) ){
          $this->lid = $_POST['lid'];
      }
      if ( $this->lid === 0 && isset( $_REQUEST['lid'] ) ){
          $this->lid = $_REQUEST['lid'];
      }
      if ( $this->lid === 0 && isset( $args['lid'] ) ){
          $this->lid = $args['lid'];
      }
      if ( $this->lid === 0 && isset( $this->OnRegistrationLevel ) ){
          $this->lid = $this->OnRegistrationLevel;
      }

      // set Dynamic Price
      if ( isset( $_GET['dynamic_price_set'] ) ){
          $this->dynamic_price = esc_sql($_GET['dynamic_price_set']);
      }
      if ( isset( $_POST['dynamic_price_set'] ) ){
          $this->dynamic_price = esc_sql($_POST['dynamic_price_set']);
      }
      if ( isset( $args['dynamic_price'] ) ){
          $this->dynamic_price = $args['dynamic_price'];
      }

      // set Coupon
      if ( isset( $_GET['coupon_used'] ) ){
          $this->coupon = esc_sql($_GET['coupon_used']);
      }
      if ( isset( $_POST['coupon_used'] ) ){
          $this->coupon = esc_sql($_POST['coupon_used']);
      }
      if ( isset( $args['coupon'] ) ){
          $this->coupon = $args['coupon'];
      }

      // set Default payment
      $this->selectedPayment = get_option('ihc_payment_selected');
      $this->selectedPayment = \Ihc_Db::get_default_payment_gateway_for_level( $this->lid, $this->selectedPayment );
      if ( isset( $_GET['payment_selected'] ) ){
          $this->selectedPayment = esc_sql($_GET['payment_selected']);
      }
      if ( isset( $_POST['payment_selected'] ) ){
          $this->selectedPayment = esc_sql($_POST['payment_selected']);
      }
      if ( isset( $args['payment'] ) ){
          $this->selectedPayment = $args['payment'];
      }
      if ( isset( $_GET['py'] ) ){
         $this->selectedPayment = $_GET['py'];
      }

      $this->country = get_user_meta( $this->uid, 'ihc_country', true );
      if ( isset( $_GET['country'] ) ){
          $this->country = esc_sql($_GET['country']);
      }
      if ( isset( $_POST['country'] ) ){
          $this->country = esc_sql($_POST['country']);
      }
      if ( isset( $args['country'] ) ){
          $this->country = $args['country'];
      }

      $this->state = get_user_meta( $this->uid, 'ihc_state', true );
      if ( isset( $_GET['state'] ) ){
          $this->state = esc_sql($_GET['state']);
      }
      if ( isset( $_POST['state'] ) ){
          $this->state = esc_sql($_POST['state']);
      }
      if ( isset( $args['state'] ) ){
          $this->state = $args['state'];
      }

      // set Cuurency
      $this->currency = get_option('ihc_currency');
      if ( isset( $_GET['currency'] ) ){
          $this->currency = esc_sql($_GET['currency']);
      }
      if ( isset( $_POST['currency'] ) ){
          $this->currency = esc_sql($_POST['currency']);
      }
      if ( isset( $args['currency'] ) ){
          $this->currency = $args['currency'];
      }
      if ($this->currency == FALSE){
          $this->currency = 'USD';
      }

    }

    /**
     * @param string
     * @return string
     */
    public function CheckoutRegistration( $output='', $is_public=FALSE, $typeOfForm='', $register_level=0  )
    {
      global $current_user;
      if ( $typeOfForm == 'edit' || (isset( $current_user->ID ) && $current_user->ID > 0 )){
         return $output;
      }

      //if(ihcCheckCheckoutSetup()){

          $this->OnRegistration = TRUE;
          if(isset($register_level) && $register_level > 0){
            $this->OnRegistrationLevel = $register_level;
          }

          return $output.$this->output();
        //}

        //return $output;
    }

    /**
     * @param array
     * @return string
     */
    public function output( $args=[] , $ajax = FALSE )
    {
        global $wp_version;

        $this->setArgs($args);

        // if we don't have level id out
        if ( $this->lid === 0){
            return '';
        }

        // level details and settings
        $this->levelData = \Indeed\Ihc\Db\Memberships::getOne( $this->lid );

        if(!$this->levelData){
          return '';
        }
        $this->metaData = array(
          'settings'      => ihc_return_meta_arr('checkout-settings'),
          'messages'      => ihc_return_meta_arr('checkout-messages'),
        );

        // the initial settings was not imported to the new workflow
        if ( empty( $this->metaData['settings']['ihc_checkout_inital'] ) ){
          $registerFieldsData = get_option('ihc_user_fields');

      		if ($registerFieldsData){
      			$payment_select_key = ihc_array_value_exists($registerFieldsData, 'payment_select', 'name');
      			$dynamic_price_key = ihc_array_value_exists($registerFieldsData, 'ihc_dynamic_price', 'name');
      			$coupon_key = ihc_array_value_exists($registerFieldsData, 'ihc_coupon', 'name');

      			if (!empty($registerFieldsData[$payment_select_key]['display_public_reg'])){
      					$this->metaData['settings']['ihc_checkout_payment_section'] = $registerFieldsData[$payment_select_key]['display_public_reg'];
      		  }
      			if (!empty($registerFieldsData[$payment_select_key]['theme'])){
      				$this->metaData['settings']['ihc_checkout_payment_theme'] = $registerFieldsData[$payment_select_key]['theme'];
      			}
      			if(!empty($registerFieldsData[$dynamic_price_key]['display_public_reg'])){
      				$this->metaData['settings']['ihc_checkout_dynamic_price'] = $registerFieldsData[$dynamic_price_key]['display_public_reg'];
      			}
      			if(!empty($registerFieldsData[$coupon_key]['display_public_reg'])){
      				$this->metaData['settings']['ihc_checkout_coupon'] = $registerFieldsData[$coupon_key]['display_public_reg'];
      			}
      		}
        }
        // end of the initial settings was not imported to the new workflow

        if(isset($this->OnRegistration) && $this->OnRegistration === TRUE && $this->metaData['settings']['ihc_checkout_avoid_free_membership'] == 1 && $this->levelData['payment_type'] == 'free' ){
          return '';
        }

        if ( $ajax == '' ){
            wp_register_script( 'ihc-checkout-js', IHC_URL . 'assets/js/checkout.js', ['jquery'], 1.4 );
            if ( version_compare ( $wp_version , '5.7', '>=' ) ){
                wp_add_inline_script( 'ihc-checkout-js', "window.ihcCurrentLid='" . $this->lid . "';" );
                wp_add_inline_script( 'ihc-checkout-js', "window.ihcPaymentType='" . $this->selectedPayment . "';" );
            } else {
                wp_localize_script( 'ihc-checkout-js', 'window.ihcCurrentLid', $this->lid );
                wp_localize_script( 'ihc-checkout-js', 'window.ihcPaymentType', $this->selectedPayment );
            }
            wp_enqueue_script( 'ihc-checkout-js' );
        }
        wp_enqueue_style( 'ihc-checkout-css', IHC_URL . 'assets/css/checkout.css', [] );


        //Calculate Product Details and SubTotal values
        $preparePaymentData = $this->preparePaymentData();

        //Payment Method Section
        $paymentMethodData = $this->paymentMethodData( $preparePaymentData );

         // Dynamic Price Section
         $dynamicData = $this->dynamicData( $preparePaymentData );

         //Coupon Section
         $couponData = $this->couponData($preparePaymentData);

         //Taxes Section
         $taxesData = $this->taxesData($preparePaymentData);

         //Privacy Policy Section
         $privacyData = $this->privacyData();

         //Purchase Button Section
         $buttonData = $this->buttonData($preparePaymentData);

         //isRegistered
         $this->isRegistered = $this->isRegistered();


        // params
        $params = [
                            'lid'                       => $this->lid,
                            'uid'                       => $this->uid,

                            'levelData'                 => $this->levelData,
                            'currency'                  => $this->currency,

                            'preparePaymentData'        => $preparePaymentData,

                            'paymentMethodData'         => $paymentMethodData,
                            'dynamicData'               => $dynamicData,
                            'couponData'                => $couponData,
                            'taxesData'                 => $taxesData,
                            'privacyData'               => $privacyData,
                            'buttonData'                => $buttonData,

                            'showUserDetails'           => 0,
                            'fields'                    => array(),

                            'country'                   => $this->country,
                            'state'                     => $this->state,

                            'settings'                  => $this->metaData['settings'],
                            'messages'                  => $this->metaData['messages'],
                            'isRegistered'              => $this->isRegistered,
                            'custom_css'                => $this->metaData['settings']['ihc_checkout_custom_css'],
        ];

        // returing the output
        $view = new \Indeed\Ihc\IndeedView();

        if( $ajax == TRUE){
            //Do something via AJAX
            $returnData['subtotal'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-subtotal.php' )
                                           ->setContentData( $params )
                                           ->getOutput();
            $returnData['taxes'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-taxes.php' )
                                        ->setContentData( $params )
                                        ->getOutput();
            $returnData['subscription_details'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-subscription-details.php' )
                                        ->setContentData( $params )
                                        ->getOutput();
            $returnData['bttn'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-purchase-button.php' )
                                        ->setContentData( $params )
                                        ->getOutput();
            $returnData['coupon_success'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-coupon-used.php' )
                                                 ->setContentData( $params )
                                                 ->getOutput();
            $returnData['dynamic_price_success'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-dynamic-price-set.php' )
                                                        ->setContentData( $params )
                                                        ->getOutput();
            $returnData['payment_method_section'] = $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-payment-method.php' )
                                                        ->setContentData( $params )
                                                        ->getOutput();
            $returnData['coupon_used'] = empty( $preparePaymentData['couponApplied'] ) ? 0 : 1;
            $returnData['dynamic_price_used'] = empty( $preparePaymentData['dynamic_price_used'] ) ? 0 : 1;
            return $returnData;
        }
        return $view->setTemplate( IHC_PATH . 'public/views/checkout/checkout-main-page.php' )
                    ->setContentData( $params )
                    ->getOutput();
    }

    /**
     * @param none
     * @return bool
     */
    public function isRegistered( )
    {
      if(isset($this->uid) && $this->uid > 0) {
        return true;
      }
      return false;
    }

    /**
     * @param none
     * @return int
     */
    public function getMembershipBasedSlug( )
    {
      $meta_arr = ihc_return_meta_arr('public_workflow');

      if(isset($meta_arr['ihc_pretty_links']) && $meta_arr['ihc_pretty_links'] == 1 && !isset($_GET['lid']) && !isset($this->lid)){

        $current_url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $levelsAll = \Indeed\Ihc\Db\Memberships::getAll();

        if(isset($levelsAll) && is_array($levelsAll) && count($levelsAll) > 0){
          foreach($levelsAll as $key=>$value){
            //Convert
            $levelSlug = str_replace('_','-', $value['name'] );
            if (strpos($current_url, '/' .  $levelSlug . '/')!==FALSE){
              $levelReturnData = \Indeed\Ihc\Db\Memberships::getOneByName($value['name']);

              $this->lid = $levelReturnData['id'];
          }
        }
      }
    }
      return '';
    }

    /**
     * @param string
     * @return string
     */
    public static function getForPeriod( $string ='', $interval = '', $type = '', $multiply = FALSE  )
    {
      $type = strtolower(substr($type, 0, 1 ));
      if($interval == 1){
        switch($type){
          case 'd':
            $type = esc_html__("day", 'ihc');
            break;
          case 'w':
            $type = esc_html__("week", 'ihc');
            break;
          case 'm':
            $type = esc_html__("month", 'ihc');
            break;
          case 'y':
            $type  = esc_html__("year", 'ihc');
            break;
          default:
            $type  = esc_html__("month", 'ihc');
            break;
        }
        if($multiply === TRUE){
          return $string.' '.$type;
        }
        return $string.' '.$interval.' '.$type;
      }else{
        switch($type){
          case 'd':
            $type = esc_html__("days", 'ihc');
            break;
          case 'w':
            $type = esc_html__("weeks", 'ihc');
            break;
          case 'm':
            $type = esc_html__("months", 'ihc');
            break;
          case 'y':
            $type  = esc_html__("years", 'ihc');
            break;
          default:
            $type  = esc_html__("months", 'ihc');
            break;
        }
        return $string.' '.$interval.' '.$type;
      }
    }

    /**
     * @param none
     * @return array
     */
    public function getPaymentServices()
    {
        $allServices = ihc_get_active_payments_services();
        $excludePayments = \Ihc_Db::get_excluded_payment_types_for_level_id( $this->lid );
        if ( empty( $excludePayments ) ){
            return $allServices;
        }
        $excludeArray = explode( ',', $excludePayments );
        foreach ($excludeArray as $ek=>$ev){
            if (isset($allServices[$ev])){
                unset($allServices[$ev]);
            }
        }
        return $allServices;
    }

    /**
     * @param none
     * @return array
     */
    public function preparePaymentData()
    {
      $this->attributes = array(
          'uid'										=> $this->uid,
          'lid'										=> $this->lid,
          'ihc_coupon'	  				=> $this->coupon,
          'ihc_country'						=> $this->country,
          'ihc_state'							=> $this->state,
          'ihc_dynamic_price'			=> $this->dynamic_price,
          'is_register'						=> false,
      );
      switch ($this->selectedPayment){
        case "paypal":
              if (ihc_check_payment_available('paypal')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\PayPalStandard();
                  $preparePayment = $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                                          ->check()
                                                          ->preparePayment();
              }
              break;
        case 'paypal_express_checkout':
              if (ihc_check_payment_available('paypal_express_checkout')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\PayPalExpressCheckout();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                                          ->check()
                                                          ->preparePayment();
              }
              break;
        case 'stripe_checkout_v2':
              if (ihc_check_payment_available('stripe_checkout_v2')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\StripeCheckout();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                                          ->check()
                                                          ->preparePayment();
              }
              break;
        case 'mollie':
              if (ihc_check_payment_available('mollie')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\Mollie();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                                          ->check()
                                                          ->preparePayment();
              }
              break;
        case 'twocheckout':
              if (ihc_check_payment_available('twocheckout')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\TwoCheckout();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                                          ->check()
                                                          ->preparePayment();
              }
              break;
        case 'pagseguro':
              if (ihc_check_payment_available('pagseguro')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\Pagseguro();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                                          ->check()
                                                          ->preparePayment();
              }
              break;
        case 'bank_transfer':
              if (ihc_check_payment_available('bank_transfer')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\BankTransfer();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                       ->check()
                                       ->preparePayment();
              }
              break;
        case 'braintree':
              if (ihc_check_payment_available('braintree')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\Braintree();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                       ->check()
                                       ->preparePayment();
              }
              break;
        case 'authorize':
              if (ihc_check_payment_available('authorize')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\Authorize();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                       ->check()
                                       ->preparePayment();
              }
              break;
        case 'stripe_connect':
              if (ihc_check_payment_available('stripe_connect')){
                  $paymentGatewayObject = new \Indeed\Ihc\Gateways\StripeConnect();
                  $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                                       ->check()
                                       ->preparePayment();
              }
              break;
      }// end of switch payment type

      if( !isset($paymentGatewayObject)){
          $paymentGatewayObject = new \Indeed\Ihc\Gateways\VirtualPayment();
          $paymentGatewayObject->setInputData($this->attributes) /// attributes for payment ( lid, uid, coupon, etc)
                               ->check()
                               ->preparePayment();
      }

      if ( isset( $paymentGatewayObject ) ){
          return $paymentGatewayObject->getPaymentOutputData();
      }
      return [];

    }

    /**
     * @param array
     * @return array
     */
    public function paymentMethodData($preparePaymentData=[] )
    {
      $paymentMethodData = array();

      if ( !empty( $this->metaData['settings']['ihc_checkout_payment_section'] )  && ( isset( $preparePaymentData['amount'] ) && $preparePaymentData['amount'] > 0 ) ){
         $paymentMethodData['show'] = 1;
      }
      // payment services
      $paymentMethodData['services'] = $this->getPaymentServices();

       // payment select settings
       $paymentMethodData['theme'] = $this->metaData['settings']['ihc_checkout_payment_theme'];


      $paymentMethodData['selected'] = $this->selectedPayment;

      return $paymentMethodData;
    }

    /**
     * @param array
     * @return array
     */
    public function dynamicData( $preparePaymentData=[] )
    {
        $dynamicData  = array();
        if(!empty($this->metaData['settings']['ihc_checkout_dynamic_price']) && ihc_is_magic_feat_active('level_dynamic_price')){
          $temp_dynamic_settings = ihc_return_meta_arr('level_dynamic_price');//getting metas
          if (!empty($temp_dynamic_settings['ihc_level_dynamic_price_levels_on'][$this->lid])){
              $dynamicData['show']  = 1;
          }
          $dynamicData['min'] = isset($temp_dynamic_settings['ihc_level_dynamic_price_levels_min'][$this->lid]) && $temp_dynamic_settings['ihc_level_dynamic_price_levels_min'][$this->lid]!='' ? $temp_dynamic_settings['ihc_level_dynamic_price_levels_min'][$this->lid] : 0;
          $dynamicData['max']  = isset($temp_dynamic_settings['ihc_level_dynamic_price_levels_max'][$this->lid]) && $temp_dynamic_settings['ihc_level_dynamic_price_levels_max'][$this->lid]!='' ? $temp_dynamic_settings['ihc_level_dynamic_price_levels_max'][$this->lid] : $this->levelData['price'];
          $dynamicData['step'] = isset($temp_dynamic_settings['ihc_level_dynamic_price_step']) && $temp_dynamic_settings['ihc_level_dynamic_price_step']!='' ? $temp_dynamic_settings['ihc_level_dynamic_price_step'] : 0.1;
          $dynamicData['used'] = isset($preparePaymentData['dynamic_price_used'] ) ? $preparePaymentData['dynamic_price_used'] : false;
        }
        return $dynamicData;
    }

    /**
     * @param array
     * @return array
     */
    public function couponData($preparePaymentData = [])
    {
      $couponData  = array();
      if(!empty($this->metaData['settings']['ihc_checkout_coupon']) && $preparePaymentData['amount'] > 0){
        $couponData['show']  = 1;
       }

       //Get Additional Details about Used Coupon but only after preparePayment process
       if(isset($preparePaymentData['coupon_used'])&& $preparePaymentData['couponApplied'] == TRUE){
         $couponObject = new \Indeed\Ihc\Payments\Coupons();
          $couponData['details'] = $couponObject->setCode( $preparePaymentData['coupon_used'] )
                                  ->setLid( $this->lid )
                                  ->getData();

         if ( $couponObject->isValid() &&  $couponData['details']  ){
           if( $couponData['details']['discount_type'] === 'price'){
              $couponData['details']['discount_display'] = ihc_format_price_and_currency( $this->currency,  $couponData['details']['discount_value']);
           }else{
              $couponData['details']['discount_display'] =  $couponData['details']['discount_value'].'%';
           }
         }
       }

      return $couponData;
    }

    /**
     * @param array
     * @return array
     */
    public function taxesData($preparePaymentData = [])
    {
      $taxesData = array();
      if(isset($this->metaData['settings']['ihc_checkout_taxes_display_section']) && $this->metaData['settings']['ihc_checkout_taxes_display_section'] == 1 && ihc_is_magic_feat_active('taxes')){
        $taxesData['show'] = 1;
      }
      if(isset($preparePaymentData['taxes_details'])  && is_array($preparePaymentData['taxes_details']) && count($preparePaymentData['taxes_details']) > 0){
        $taxesData['details'] = $preparePaymentData['taxes_details'];
      }
      return $taxesData;
    }

    /**
     * @param none
     * @return string
     */
    public function privacyData()
    {
      $privacyData = '';
      if(!empty($this->metaData['settings']['ihc_checkout_privacy_policy_option']) && !empty($this->metaData['settings']['ihc_checkout_privacy_policy_message'])){
        $privacyData = $this->metaData['settings']['ihc_checkout_privacy_policy_message'];
      }
      return $privacyData;
    }

    /**
     * @param array
     * @return array
     */
    public function buttonData($preparePaymentData = [])
    {
      $buttonData = array();

      if(isset($this->uid) && $this->uid > 0) {
        $buttonData['show'] = 1;
      }


      if($preparePaymentData['amount'] > 0){
        $buttonData['label'] = $this->metaData['messages']['ihc_checkout_purchase_button'];
      }else{
        $buttonData['label'] = $this->metaData['messages']['ihc_checkout_free_button'];
      }
      return $buttonData;
    }

    /**
     * Ajax call
     * @param none
     * @return string
     */
    public function subscriptionDetails()
    {
        global $current_user;

        $args = [
          'uid'										=> esc_sql(isset( $_POST['uid'] ) ? $_POST['uid'] : 0),
          'lid'										=> esc_sql(isset( $_POST['lid'] ) ? $_POST['lid'] : 0),
          'coupon'    	  				=> esc_sql(isset( $_POST['coupon'] ) ? $_POST['coupon'] : ''),
          'dynamic_price'	        => esc_sql(isset( $_POST['dynamicPrice'] ) ? $_POST['dynamicPrice'] : ''),
          'country'	    					=> esc_sql(isset( $_POST['country'] ) ? $_POST['country'] : ''),
          'state'   							=> esc_sql(isset( $_POST['state'] ) ? $_POST['state'] : ''),
          'payment'               => esc_sql(isset( $_POST['paymentType'] ) ? $_POST['paymentType'] : ''),
        ];

        $callViaAjax = true;
        $response = $this->output( $args, $callViaAjax );
        if ( $response === '' || !is_array( $response ) ){
            // error
            echo json_encode( [ 'status' => 0 ] );
            die;
        }

        if ( $_POST['typeOfRequest'] === 'coupon' && !$response['coupon_used'] ){
            $response['status'] = 0;
            echo json_encode( $response );
            die;
        }
        if ( $_POST['typeOfRequest'] === 'dynamic_price' && !$response['dynamic_price_used']  ){
            $response['status'] = 0;
            echo json_encode( $response );
            die;
        }

        $response['status'] = 1;
        echo json_encode( $response );
        die;
    }

    /**
     * @param none
     * @return none
     */
    public function processingSubmit()
    {
        if ( ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && !empty( $_POST['lid'] ) && !empty( $_POST['uid'] ) && !empty( $_POST['checkout-form'] ) ){
            // save the subscription
            $this->saveSubscription();

            // do payment
            $this->doPaymentFromCheckout();
        }
    }

    /**
     * @param none
     * @return none
     */
    protected function saveSubscription()
    {
        $uid = isset( $_POST['uid'] ) ? $_POST['uid'] : 0;
        $lid = isset( $_POST['lid'] ) ? $_POST['lid'] : '';
        \Indeed\Ihc\UserSubscriptions::assign( $uid, $lid );
    }

    /**
     * @param none
     * @return none
     */
    protected function doPaymentFromCheckout()
    {
        $args = array(
            'uid'										=> esc_sql(isset( $_POST['uid'] ) ? $_POST['uid'] : 0),
            'lid'										=> esc_sql(isset( $_POST['lid'] ) ? $_POST['lid'] : 0),
            'ihc_coupon'	  				=> esc_sql(isset( $_POST['coupon_used'] ) ? $_POST['coupon_used'] : ''),
            'ihc_country'						=> esc_sql(isset( $_POST['country'] ) ? $_POST['country'] : ''),
            'ihc_state'							=> esc_sql(isset( $_POST['state'] ) ? $_POST['state'] : ''),
            'ihc_dynamic_price'			=> esc_sql(isset( $_POST['dynamic_price_set'] ) ? $_POST['dynamic_price_set'] : ''),
            'defaultRedirect'				=> '',
            'is_register'						=> false,
        );
        $paymentObject = new \Indeed\Ihc\DoPayment( $args, esc_sql( $_POST['payment_selected'] ) );
        $paymentObject->processing();
    }


}
