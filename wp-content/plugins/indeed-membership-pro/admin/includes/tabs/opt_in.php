<?php
ihc_save_update_metas('opt_in');//save update metas
$meta_arr = ihc_return_meta_arr('opt_in');//getting metas
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>

			<form method="post">
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Opt-In Settings', 'ihc');?></h3>
					<div class="inside">
						<p><?php esc_html_e('If you wish to ask member permission for OptIn process you may find a special field ', 'ihc');?> <b>ihc_optin_accept</b> <?php esc_html_e(' in ', 'ihc'); ?> <a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=register&subtab=custom_fields'); ?>" target="_blank">  <?php esc_html_e('Registration form fields', 'ihc');?> </a></p>
						</div>
					</div>

				<div class="ihc-stuffbox">
					<h3>Active Campaign</h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
 		         <div class="col-xs-5 ihc-col-no-padding">

 		           <div class="iump-form-line">
 		             <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Api URL', 'ihc');?></span>
 		             <input type="text" value="<?php echo $meta_arr['ihc_active_campaign_apiurl'];?>" name="ihc_active_campaign_apiurl" class="form-control" />
 		             </div>
 		           </div>

 		           <div class="iump-form-line">
 		             <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Api Key:', 'ihc');?></span>
 		               <input type="text" value="<?php echo $meta_arr['ihc_active_campaign_apikey'];?>" name="ihc_active_campaign_apikey" class="form-control" />
 		           </div>
						 </div>

 		           <div class="iump-form-line">
 		             <div class="input-group"><span class="input-group-addon"><?php esc_html_e('List ID:', 'ihc');?></span>
 		                 <input type="text" value="<?php echo $meta_arr['ihc_active_campaign_listId'];?>" name="ihc_active_campaign_listId" class="form-control /">
 		           </div>
 		       </div>
				 </div>
			 </div>

						<div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3>Aweber</h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
						 <div class="col-xs-5 ihc-col-no-padding">

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Auth Code', 'ihc');?></span>
									 <textarea id="ihc_aweber_auth_code" name="ihc_aweber_auth_code" class="form-control" ><?php
										 echo $meta_arr['ihc_aweber_auth_code'];
									 ?></textarea>
								 </div>
								 <a href="https://auth.aweber.com/1.0/oauth/authorize_app/751d27ee" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Get Your Auth Code From Here', 'ihc');?>
								 </a>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Unique List ID', 'ihc');?></span>
									 <input type="text" value="<?php echo $meta_arr['ihc_aweber_list'];?>" name="ihc_aweber_list" class="form-control" />
							 </div>
							 <a href="https://www.aweber.com/users/settings/" target="_blank" class="ihc-info-link">
								 <?php esc_html_e('Get Unique List ID', 'ihc');?>
							 </a>

						 </div>
						 <div class="iump-form-line">
						 <div onclick="ihcConnectAweber( '#ihc_aweber_auth_code' );" class="button button-primary button-large">
							 <?php esc_html_e('Connect', 'ihc');?>
						 </div>
					 </div>


						</div>
						</div>

					    <div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3>Mailchimp</h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
						 <div class="col-xs-5 ihc-col-no-padding">

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('API Key', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_mailchimp_api'];?>" name="ihc_mailchimp_api" class="form-control">
								 </div>
								 <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I find my API Key?', 'ihc');?>
								 </a>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('ID List', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_mailchimp_id_list'];?>" name="ihc_mailchimp_id_list" class="form-control">
							 </div>
							 <a href="http://kb.mailchimp.com/article/how-can-i-find-my-list-id/" target="_blank" class="ihc-info-link">
								 <?php esc_html_e('Where can I find List ID?', 'ihc');?>
							 </a>
						 </div>
						</div>
						</div>
					    <div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3>Get Response</h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
						 <div class="col-xs-5 ihc-col-no-padding">

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">GetResponse <?php esc_html_e('API Key', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_getResponse_api_key'];?>" name="ihc_getResponse_api_key" class="form-control">
								 </div>
								 <a href="http://www.getresponse.com/learning-center/glossary/api-key.html" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I find my API Key?', 'ihc');?>
								 </a>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">GetResponse <?php esc_html_e('List token', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_getResponse_token'];?>" name="ihc_getResponse_token" class="form-control">
								 </div>
								 <a href="https://app.getresponse.com/campaign_list.html " target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I find List token?', 'ihc');?>
								 </a>
							 </div>
						 </div>
					 </div>
					    <div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3>Campaign Monitor</h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
						 <div class="col-xs-5 ihc-col-no-padding">
							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">CampaignMonitor <?php esc_html_e('API Key', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_cm_api_key'];?>" name="ihc_cm_api_key" class="form-control" />
								 </div>
								 <a href="https://www.campaignmonitor.com/api/getting-started/#apikey" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I find API Key ?', 'ihc');?>
								 </a>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">CampaignMonitor <?php esc_html_e('List ID', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_cm_list_id'];?>" name="ihc_cm_list_id" class="form-control" />
								 </div>
								 <a href="https://www.campaignmonitor.com/api/clients/#subscriber_lists" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I find List ID?', 'ihc');?>
								 </a>
							 </div>
						 </div>
						</div>
					    <div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3>IContact</h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
						 <div class="col-xs-5 ihc-col-no-padding">
							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">iContact <?php esc_html_e('Username', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_icontact_user'];?>" name="ihc_icontact_user" class="form-control" />
								 </div>
								 <a href="https://www.campaignmonitor.com/api/getting-started/#apikey" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I find API Key ?', 'ihc');?>
								 </a>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">iContact <?php esc_html_e('App ID', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_icontact_appid'];?>" name="ihc_icontact_appid" class="form-control" />
								 </div>
								 <a href="http://www.icontact.com/developerportal/documentation/register-your-app/" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Where can I get my App ID?', 'ihc');?>
								 </a>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">iContact <?php esc_html_e('App Password', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_icontact_pass'];?>" name="ihc_icontact_pass" class="form-control" />
								 </div>
							 </div>

							 <div class="iump-form-line">
								 <div class="input-group"><span class="input-group-addon">iContact <?php esc_html_e('List ID', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_icontact_list_id'];?>" name="ihc_icontact_list_id" class="form-control" />
								 </div>
								 <a href="https://app.icontact.com/icp/core/mycontacts/lists" target="_blank" class="ihc-info-link">
									 <?php esc_html_e('Click on the list name:', 'ihc');?>
								 </a>
								 <div>
									 <?php esc_html_e('Click on the list name and get the ID from the URL', 'ihc');?> ( ex:  https://app.icontact.com/icp/core/mycontacts/lists/edit/
									 <b>
										 ID_LIST
									 </b>
									 /?token=f155cba025333b071d49974c96ae0894 )
								 </div>
							 </div>

						 </div>
						</div>
					    <div class="ihc-wrapp-submit-bttn ihc-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

			<div class="ihc-stuffbox">
				<h3>Constant Contact</h3>
				<div class="inside">
					<div class="row ihc-row-no-margin">
					 <div class="col-xs-5 ihc-col-no-padding">
						 <div class="iump-form-line">
							 <div class="input-group"><span class="input-group-addon">Constant Contact <?php esc_html_e('Username', 'ihc');?></span>
								<input type="text" value="<?php echo $meta_arr['ihc_cc_user'];?>" id="ihc_cc_user" name="ihc_cc_user" class="form-control" />
							 </div>
						 </div>

						 <div class="iump-form-line">
							 <div class="input-group"><span class="input-group-addon">Constant Contact <?php esc_html_e('Password', 'ihc');?></span>
								<input type="password" value="<?php echo $meta_arr['ihc_cc_pass'];?>" id="ihc_cc_pass" name="ihc_cc_pass" class="form-control" />
							 </div>
						 </div>

						 <div class="iump-form-line">
						 <div onclick="ihcGetCcList( '#ihc_cc_user', '#ihc_cc_pass' );" class="button button-primary button-large">
							 <?php esc_html_e('Get Lists', 'ihc');?>
						 </div>
					 </div>
							<div class="iump-form-line">
								<div class="input-group"><span class="input-group-addon">Constant Contact <?php esc_html_e('List', 'ihc');?></span>
									<select id="ihc_cc_list" name="ihc_cc_list" class="form-control">
										<?php
											$list_name = '';
											if (isset($meta_arr['ihc_cc_list']) && $meta_arr['ihc_cc_list']){
												//getting list name by id
												include_once IHC_PATH . 'classes/services/email_services/constantcontact/class.cc.php';
												$cc = new cc($meta_arr['ihc_cc_user'], $meta_arr['ihc_cc_pass']);
												$list_arr= $cc->get_list($meta_arr['ihc_cc_list']);
												if(isset($list_arr['Name'])){
													 $list_name = $list_arr['Name'];
												}
											}
										?>
										<option value="<?php echo $meta_arr['ihc_cc_list'];?>"><?php echo $list_name;?></option>
									</select>
							</div>
					 </div>
					</div>
					</div>
					<div class="ihc-wrapp-submit-bttn ihc-submit-form">
						<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="ihc-stuffbox">
				<h3>Wysija Contact</h3>
				<div class="inside">
					<div class="row ihc-row-no-margin">
					 <div class="col-xs-5 ihc-col-no-padding">

						 <div class="iump-form-line">
							<div class="input-group"><span class="input-group-addon"><?php esc_html_e('Select Wysija List:', 'ihc');?></span>
								<?php
									if (!class_exists('IhcMailServices')){
										 require_once IHC_PATH . 'classes/IhcMailServices.class.php';
									}
									$obj = new IhcMailServices();
										$wysija_list = $obj->indeed_returnWysijaList();
										if ($wysija_list && count($wysija_list)>0){
											?>
												<select name="ihc_wysija_list_id" class="form-control">
													<?php
															foreach ($wysija_list as $k=>$v){
																$selected = '';
																if($meta_arr['ihc_wysija_list_id']==$k){
																	 $selected = 'selected="selected"';
																}
																		?>
																			<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
																		<?php
																}
														?>
												</select>
								 <?php
							 }else echo "<div class='iump-form-line'>" . esc_html__("No List available ", 'ihc') . "</div>" . "<input type='hidden' name='ihc_wysija_list_id' value=''/> ";
								 ?>
								 </div>
					 </div>
					</div>
					</div>
					<div class="ihc-wrapp-submit-bttn ihc-submit-form">
						<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="ihc-stuffbox">
				<h3>Mailster (MyMail)</h3>
				<div class="inside">
					<div class="row ihc-row-no-margin">
					 <div class="col-xs-5 ihc-col-no-padding">
						 <div class="iump-form-line">
							 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Select MyMail List:', 'ihc');?></span>
								 <?php
														 $mymailList = $obj->indeed_getMyMailLists();
															 if ($mymailList){
																 ?>
																	 <select name="ihc_mymail_list_id" class="form-control">
																		 <?php
																			 foreach ($mymailList as $k=>$v){
																				 $selected = '';
																				 if ($meta_arr['ihc_mymail_list_id']==$k){
																						$selected = 'selected="selected"';
																				 }
																					 ?>
																						 <option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
																			 <?php
																			 }
																			 ?>
																	 </select>
													 <?php
												 }else echo "<div class='iump-form-line'>" . esc_html__('No List available', 'ihc') . "</div>" . " <input type='hidden' name='ihc_mymail_list_id' value=''/> ";
											 ?>
							 </div>
						</div>
					</div>
					</div>
					<div class="ihc-wrapp-submit-bttn ihc-submit-form">
						<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="ihc-stuffbox">
				<h3>Mad Mimi</h3>
				<div class="inside">
					<div class="row ihc-row-no-margin">
					 <div class="col-xs-5 ihc-col-no-padding">

						 <div class="iump-form-line">
							 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Username Or Email:', 'ihc');?></span>
									<input type="text" value="<?php echo $meta_arr['ihc_madmimi_username'];?>" name="ihc_madmimi_username" class="form-control" />
							 </div>
						 </div>
						 <div class="iump-form-line">
							 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('Api Key:', 'ihc');?></span>
								 <input type="text" value="<?php echo $meta_arr['ihc_madmimi_apikey'];?>" name="ihc_madmimi_apikey" class="form-control" />
							 </div>
						 </div>
						 <div class="iump-form-line">
							 <div class="input-group"><span class="input-group-addon"><?php esc_html_e('List Name:', 'ihc');?> </span>
									<input type="text" value="<?php echo $meta_arr['ihc_madmimi_listname'];?>" name="ihc_madmimi_listname" class="form-control" />
							 </div>
						 </div>
					</div>
					</div>
					<div class="ihc-wrapp-submit-bttn ihc-submit-form">
						<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="ihc-stuffbox">
				<h3><?php esc_html_e('Saved E-mail List', 'ihc');?></h3>
				<div class="inside">
				  	<?php
				  		$email_list = get_option('ihc_email_list');
				  		if ($email_list==FALSE){
								 $email_list = '';
							}
				  	?>
				    <textarea disabled class="ihc-custom-css-box"><?php
				    	echo $email_list;
				    ?></textarea>
				</div>
			</div>
		</form>

<?php
