<?php
namespace Indeed\Ihc\PaymentGateways;
/*
Created v.7.4
Modified  v.7.4
Deprecated starting with v.9.3
*/
abstract class PaymentAbstract
{

  protected $stopProcess      = false;
  protected $attributes       = [];
  protected $paymentSettings  = [];
  protected $currency         = '';
  protected $paymentType      = '';
  protected $redirectUrl      = '';
  protected $defaultRedirect  = '';
  protected $paymentTypeLabel = '';

  /**
   * set currency, set payment gateway settings ( credentials, sandbox, etc )
   * @param none
   * @return none
   */
  public function __construct()
  {
      $this->currency = get_option('ihc_currency');
      $this->paymentSettings = ihc_return_meta_arr( $this->paymentType );
  }

  /**
   * Set level settings based on $this->attribute['lid']
   * @param none
   * @return bool
   */
  protected function setLevelsData()
  {
      $levelsData = \Indeed\Ihc\Db\Memberships::getAll();
      if ( isset( $levelsData[ $this->attributes['lid'] ] ) ){
          return $levelsData[ $this->attributes['lid'] ];
      }
      return [];
  }

  /**
   * Check if current level is free.
   * @param none
   * @return bool
   */
  protected function isLevelFree()
  {
      $isFree = false;
      if ( !isset( $this->levelData ) ){
          $isFree = true;
      }
      if ( $this->levelData['payment_type']=='free' || $this->levelData['price'] == '' || !$this->levelData['price'] ){
          $isFree = true;
      }
      $temporaryAmount = $this->applyDynamicPrice($this->levelData['price']);
      if ( $temporaryAmount == 0 ){
          $isFree = true;
      }
      if ( $isFree ){
          $this->approveFreeLevel();
      }
      return $isFree;
  }

  /**
   * @param none
   * @return none
   */
  protected function isRecurringLevel()
  {
      if (isset($this->levelData['access_type']) && $this->levelData['access_type']=='regular_period'){
          return true;
      }
      return false;
  }

  /**
   * set logs, check if level is set, check if user id is set, check if level is free.
   * @param none
   * @return object
   */
  public function initDoPayment()
  {
      \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__( ': Start process', 'ihc'), 'payments');
      if ( empty( $this->attributes['uid'] ) ){
          \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__( ': Error, user id not set.', 'ihc'), 'payments');
          $this->stopProcess = true;
      }
      if ( empty( $this->attributes['lid'] ) ){
          \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__( ': Error, level id not set.', 'ihc'), 'payments');
          $this->stopProcess = true;
      }
      $this->levelData = $this->setLevelsData();
      if ( empty( $this->levelData ) ){
          \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__('t: Error, level not available.', 'ihc'), 'payments');
          $this->stopProcess = true;
      }
      if ( $this->isLevelFree() ){
          \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__(': Level is free.', 'ihc'), 'payments');
          $this->stopProcess = true;
      }

      \Ihc_User_Logs::set_user_id( $this->attributes['uid'] );
      \Ihc_User_Logs::set_level_id( $this->attributes['lid'] );

      return $this;
  }

	abstract public function doPayment();

	abstract public function redirect();

	abstract public function webhook();

  abstract public function cancelSubscription();

  /**
   * @param array
   * @return object
   */
  public function setAttributes($params=array())
  {
      $this->attributes = $params;
      $this->setDefaultRedirect();
      return $this;
  }

  /**
  * @param int
  * @return int
  */
  protected function addTaxes($amount=0)
  {
      $levelData = \Indeed\Ihc\Db\Memberships::getOne( $this->attributes['lid'] );
      $country = (isset($this->attributes['ihc_country'])) ? $this->attributes['ihc_country'] : '';
      $state = (isset($this->attributes['ihc_state'])) ? $this->attributes['ihc_state'] : '';
      if ( $amount > 0 ){
          $taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $amount );
      } else {
          $taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $levelData['price']);
      }

      if ($taxes_price && !empty($taxes_price['total'])){
          $amount += $taxes_price['total'];
      }
      return $amount;
  }

  /**
  * @param int or float
  * @return int or float
  */
  protected function applyDynamicPrice( $amount=0 )
  {
      if ( !ihc_is_magic_feat_active('level_dynamic_price') || !isset( $this->attributes['ihc_dynamic_price'] ) ){
          return $amount;
      }
      $tempAmount = $this->attributes['ihc_dynamic_price'];
      if ( ihc_check_dynamic_price_from_user( $this->attributes['lid'], $tempAmount ) ){
        $amount = $tempAmount;
        \Ihc_User_Logs::write_log( esc_html__( $this->paymentTypeLabel . ': Dynamic price on - Amount is set by the user @ ', 'ihc') . $amount . $this->currency, 'payments' );
      }
      return $amount;
  }

  /**
  * @param none
  * @return none
  */
  protected function setDefaultRedirect()
  {
      if ( !empty( $this->attributes['defaultRedirect'] ) ){
         $this->defaultRedirect = $this->attributes['defaultRedirect'];
         return;
      }
      $this->defaultRedirect = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $redirect = get_option('ihc_general_register_redirect');
      if (!$redirect || $redirect < 0){
          return;
      }
      $url = get_permalink($redirect);
      if ($url){
          $this->defaultRedirect = $url;
      }
      if ( !empty( $this->attributes['is_register'] ) ){
          $this->defaultRedirect = apply_filters('ihc_register_redirect_filter', $this->defaultRedirect, $this->attributes['uid'], $this->attributes['lid']);
      }

      $url = ihc_get_redirect_link_by_label($redirect, $this->attributes['uid']);
      if (strpos($url, IHC_PROTOCOL . $_SERVER['HTTP_HOST'] )!==0){
          $this->defaultRedirect = $url;
      }
  }

  /**
   *
   Mandatory $paymentData should cointain [
                      'uid'                           => '',
                      'lid'                           => '',
                      'orderId'                       => '',
                      'transactionIdentificator'      => '',
                      'amount_value'                  => '',
                      'amount_type'                   => '',
  ];
   * @param array
   * @param bool
   * @return none
   */
  protected function completeLevelPayment( $paymentData=[], $isTrial=false )
  {
      // Level Expire
      $this->attributes['lid'] = $paymentData['lid'];
      $this->levelData = $this->setLevelsData();
      if ( $isTrial ){
          \Indeed\Ihc\UserSubscriptions::makeComplete( $paymentData['uid'], $paymentData['lid'], true, [ 'payment_gateway' => $this->paymentType ] );
      } else {
          \Indeed\Ihc\UserSubscriptions::makeComplete( $paymentData['uid'], $paymentData['lid'], false, [ 'payment_gateway' => $this->paymentType ] );
      }
      \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__(" Payment Webhook: Updated user (".$paymentData['uid'].") level (".$paymentData['lid'].") expire time.", 'ihc'), 'payments');

      /// Order
      if ( empty( $paymentData['orderId'] ) ){
          $paymentData['orderId'] = \Ihc_Db::getLastOrderByTxnId( $paymentData['transactionIdentificator'] );
          $order = new \Indeed\Ihc\Db\Orders();
      }
      $orderStatus = $order->setId( $paymentData['orderId'] )->fetch()->getStatus();
      if ( $orderStatus && $orderStatus != 'Completed' ){
          // update order status
          \Ihc_Db::updateOrderStatus( $orderId, 'Completed' );
      } else {
          // insert order
          $orderObject = new \Indeed\Ihc\Db\Orders();
          $currentOrderId = $orderObject->setData( [
                                              'amount_value'      => $paymentData['amount_paid'],
                                              'amount_type'       => $paymentData['currency'],
                                              'uid'               => $paymentData['uid'],
                                              'lid'               => $paymentData['lid'],
                                              'automated_payment' => $this->isRecurringLevel() ? 1 : 0,
                                              'status'            => 'Completed',
          ] )->save();
      }

      /// Transaction
      $IndeedMembersPayments = new \Indeed\Ihc\Db\IndeedMembersPayments();
      $IndeedMembersPayments->setTxnId( $paymentData['transactionIdentificator'] )
                            ->setUid( $paymentData['uid'] )
                            ->setPaymentData( $paymentData )
                            ->setHistory( $paymentData )
                            ->setOrders( $paymentData['orderId'] )
                            ->save();

      \Ihc_User_Logs::write_log( $this->paymentTypeLabel . esc_html__(' Payment Webhook: Payment - Completed.', 'ihc'), 'payments');

      /// Action on payment completed
      do_action( 'ihc_payment_completed', $paymentData['uid'], $paymentData['lid'] );
      // @description run on payment complete. @param user id (integer), level id (integer)
  }

  protected function approveFreeLevel()
  {
      // Level Expire
      $this->levelData = $this->setLevelsData();
      \Indeed\Ihc\UserSubscriptions::makeComplete( $this->attributes['uid'], $this->attributes['lid'], false, [ 'payment_gateway' => $this->paymentType ] );
      do_action( 'ihc_payment_completed', $this->attributes['uid'], $this->attributes['lid'] );
      // @description run on payment complete. @param user id (integer), level id (integer)
  }

}
