<?php
if (class_exists('IndeedImport') && !class_exists('Ihc_Indeed_Import')):

class Ihc_Indeed_Import extends IndeedImport{

	/*
	 * @param string ($entity_name)
	 * @param string ($entity_opt)
	 * @param object ($xml_object)
	 * @return none
	 */
	protected function do_import_custom_table($entity_name, $entity_opt, &$xml_object){
		global $wpdb;
		$table = $wpdb->prefix . $entity_name;

		if (!$xml_object->$entity_name->Count()){
			return;
		}

		switch ($entity_name){
			case 'ihc_notifications':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->notification_type ) ) . "',
												'" . $this->esc_sql( (string)( $object->level_id ) ) . "',
												'" . $this->esc_sql( (string)( $object->subject ) ) . "',
												'" . $this->esc_sql( (string)( $object->message ) ) . "',
												'" . $this->esc_sql( (string)( $object->pushover_message ) ) . "',
												'" . $this->esc_sql( (string)( $object->pushover_status ) ) . "',
												'" . $this->esc_sql( (string)( $object->status ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_user_levels':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->user_id ) ) . "',
												'" . $this->esc_sql( (string)( $object->level_id ) ) . "',
												'" . $this->esc_sql( (string)( $object->start_time ) ) . "',
												'" . $this->esc_sql( (string)( $object->update_time ) ) . "',
												'" . $this->esc_sql( (string)( $object->expire_time ) ) . "',
												'" . $this->esc_sql( (string)( $object->notification ) ) . "',
												'" . $this->esc_sql( (string)( $object->status ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_debug_payments':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->source ) ) . "',
												'" . $this->esc_sql( (string)( $object->message ) ) . "',
												'" . $this->esc_sql( (string)( $object->insert_time ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'indeed_members_payments':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->txn_id ) ) . "',
												'" . $this->esc_sql( (string)( $object->u_id ) ) . "',
												'" . $this->esc_sql( (string)( $object->payment_data ) ) . "',
												'" . $this->esc_sql( (string)( $object->history ) ) . "',
												'" . $this->esc_sql( (string)( $object->orders ) ) . "',
												'" . $this->esc_sql( (string)( $object->paydate ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_coupons':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->code ) ) . "',
												'" . $this->esc_sql( (string)( $object->settings ) ) . "',
												'" . $this->esc_sql( (string)( $object->submited_coupons_count ) ) . "',
												'" . $this->esc_sql( (string)( $object->status ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_orders':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null,
											'" . $this->esc_sql( (string)( $object->uid ) ) . "',
											'" . $this->esc_sql( (string)( $object->lid ) ) . "',
											'" . $this->esc_sql( (string)( $object->amount_type ) ) . "',
											'" . $this->esc_sql( (string)( $object->amount_value ) ) . "',
											'" . $this->esc_sql( (string)( $object->automated_payment ) ) . "',
											'" . $this->esc_sql( (string)( $object->status ) ) . "',
											'" . $this->esc_sql( (string)( $object->create_date ) ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_orders_meta':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->order_id ) ) . "',
												'" . $this->esc_sql( (string)( $object->meta_key ) ) . "',
												'" . $this->esc_sql( (string)( $object->meta_value ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_taxes':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null,
											'" . $this->esc_sql( (string)( $object->country_code ) ) . "',
											'" . $this->esc_sql( (string)( $object->state_code ) ) . "',
											'" . $this->esc_sql( (string)( $object->amount_value ) ) . "',
											'" . $this->esc_sql( (string)( $object->label ) ) . "',
											'" . $this->esc_sql( (string)( $object->description ) ) . "',
											'" . $this->esc_sql( (string)( $object->status ) ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_dashboard_notifications':
				///
				break;
			case 'ihc_cheat_off':
				///
				break;
			case 'ihc_invitation_codes':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null,
											'" . $this->esc_sql( (string)( $object->code ) ) . "',
											'" . $this->esc_sql( (string)( $object->settings ) ) . "',
											'" . $this->esc_sql( (string)( $object->submited ) ) . "',
											'" . $this->esc_sql( (string)( $object->repeat_limit ) ) . "',
											'" . $this->esc_sql( (string)( $object->status ) ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_gift_templates':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
						$insert_string = "VALUES(null,
												'" . $this->esc_sql( (string)( $object->lid ) ) . "',
												'" . $this->esc_sql( (string)( $object->settings ) ) . "',
												'" . $this->esc_sql( (string)( $object->status ) ) . "'
						)";
						$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_security_login':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null,
											'" . $this->esc_sql( (string)( $object->username ) ) . "',
											'" . $this->esc_sql( (string)( $object->ip ) ) . "',
											'" . $this->esc_sql( (string)( $object->log_time ) ) . "',
											'" . $this->esc_sql( (string)( $object->attempts_count ) ) . "',
											'" . $this->esc_sql( (string)( $object->locked ) ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'ihc_user_logs':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null,
											'" . $this->esc_sql( (string)( $object->uid ) ) . "',
											'" . $this->esc_sql( (string)( $object->lid ) ) . "',
											'" . $this->esc_sql( (string)( $object->log_time ) ) . "',
											'" . $this->esc_sql( (string)( $object->log_content ) ) . "',
											'" . $this->esc_sql( (string)( $object->create_date ) ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
		}

	}

	/*
	 * @param string (table name)
	 * @param string (insert values)
	 * @return none
	 */
	private function do_basic_insert($table='', $insert_values=''){
		global $wpdb;
		$query = "INSERT INTO $table $insert_values;";
		$wpdb->query( $query );
	}

	private function esc_sql( $value='' )
	{
			global $wpdb;
			return esc_sql( $value );
	}

}

endif;
