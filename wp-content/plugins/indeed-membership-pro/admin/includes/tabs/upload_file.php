
                                  <div class="iump-form-line-register iump-form-file">
                                  <label class="iump-labels-register"><?php echo $field['label'];?></label>
                                    <?php
                            				wp_enqueue_script( 'ihc-jquery_form_module' );
                            				wp_enqueue_script( 'ihc-jquery_upload_file' );
                            				$upload_settings = ihc_return_meta_arr('extra_settings');
                            				$max_size = $upload_settings['ihc_upload_max_size'] * 1000000;
                            				$rand = rand(1,10000);
                                    $attachment_name = '';
                                    $url = '';

                                    // $ajaxURL = IHC_URL . 'public/ajax-upload.php?ihcpublicn='. wp_create_nonce( 'ihcpublicn' );
                                    $ajaxURL = get_site_url() . '/wp-admin/admin-ajax.php?action=ihc_ajax_public_upload_file&ihcpublicn=' . wp_create_nonce( 'ihcpublicn' );
                                    ?>
                            				<div id="<?php echo 'ihc_fileuploader_wrapp_' . $rand;?>" class="ihc-wrapp-file-upload ihc-wrapp-file-field ihc-vertical-align-top">
                            				<div class="ihc-file-upload ihc-file-upload-button"><?php esc_html_e("Upload", 'ihc');?></div>
                                    <span class="ihc-js-upload-file-data"
                                          data-alert_text="<?php echo esc_html__('"To add a new file please remove the previous one!"', 'ihc');?>"
                                          data-rand="<?php echo $rand;?>"
                                          data-url="<?php echo $ajaxURL;?>"
                                          data-max_size="<?php echo $max_size;?>"
                                          data-allowed_types="<?php echo $upload_settings['ihc_upload_extensions'];?>"
                                          data-field_name="<?php echo  $fieldName;?>"
                                        ></span>

                                <?php
                                if ( $fieldValue ){
                                  if ( strpos ( $fieldValue, 'http' ) !== false ){
                                    $fileExtension = explode( '.', $fieldValue );
                                    end( $fileExtension );
                                    $attachment_type = current( $fileExtension );
                                    $url = $fieldValue;
                                  } else {
                                    $attachment_type = ihc_get_attachment_details($fieldValue, 'extension');
                                    $url = wp_get_attachment_url($fieldValue);
                                  }
                                  $imgClass = isset( $field['img_class'] ) ? $field['img_class'] : 'ihc-member-photo';
                                  switch ($attachment_type){
                                    case 'jpg':
                                    case 'jpeg':
                                    case 'png':
                                    case 'gif':
                                      //print the picture
                                      ?>
                                      <img src="<?php echo $url;?>" class="<?php echo $imgClass;?>" /><div class="ihc-clear"></div>
                                      <?php
                                      break;
                                    default:
                                      //default file type
                                      ?>
                                      <div class="ihc-icon-file-type"></div>

                                      <?php
                                      break;
                                  }
                                  ?>
                                  <?php
                                  $attachment_name = ihc_get_attachment_details($fieldValue);
                                }
                                ?>
  <?php if ( $fieldValue != '' ):?>
      <div class="ihc-file-name-uploaded"><a href="<?php echo $url;?>" target="_blank"><?php echo $attachment_name;?></a></div>
      <div onClick='ihcDeleteFileViaAjax( "<?php echo $fieldValue;?>", <?php echo $data['uid'];?>, "#ihc_fileuploader_wrapp_<?php echo $rand;?>", "<?php echo $fieldName;?>", "#ihc_upload_hidden_<?php echo $rand;?>");' class="ihc-delete-attachment-bttn"><?php esc_html_e( 'Remove', 'ihc' );?></div>
  <?php endif;?>
  <input type="hidden" value="<?php echo $fieldValue;?>" name="<?php echo $fieldName;?>" id="ihc_upload_hidden_<?php echo $rand;?>" />

  </div>
</div>
