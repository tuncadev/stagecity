<?php
/*
// deprecated since version 11.3 , data['form'] is in ChangePassword class.
global $ihc_error_register;
if (empty($ihc_error_register)){
		$ihc_error_register = array();
}

if (!class_exists('UserAddEdit')){
  require_once IHC_PATH . 'classes/UserAddEdit.class.php';
}
$obj_form = new \UserAddEdit();
$args = array(
        'user_id'              => $data['uid'],
        'type'                 => 'edit',
        'tos'                  => false,
        'captcha'              => false,
        'select_level'         => false,
        'action'               => '',
        'is_public'            => true,
        'register_template'    => $data['template'],
        'print_errors'         => $ihc_error_register,
				'is_change_password'	 => true,
      );
$obj_form->setVariable($args);
$form = $obj_form->form();
$form = apply_filters('ihc_update_password_form_html', $form );
*/
?>

<div class="iump-user-page-wrapper ihc_userpage_template_1">
  <div class="iump-user-page-box">
    <div class="iump-user-page-box-title"><?php esc_html_e('Password change', 'ihc');?></div>
    <div class="iump-register-form <?php echo $data['template'];?>"><?php echo $data['form'];?></div>
  </div>
</div>
