<?php

if( ! class_exists( 'Msmart_Search' ) ) {
	if( class_exists( '\VQMod' ) ) {
		require_once \VQMod::modCheck(modification(DIR_SYSTEM . 'library/msmart_search.php'));
	} else {
		require_once modification(DIR_SYSTEM . 'library/msmart_search.php');
	}
}

class ModelExtensionModuleMsmartSearch extends Model {
	
    public function addToDatabase($phrase) {		
		if( $phrase !== null && mb_strlen($phrase,'utf8') > 1 ){
			/* @var $last_phrase string */
			$last_phrase = isset( $this->session->data['msmart_search_phrase'] ) ? $this->session->data['msmart_search_phrase'] : null;

			if( $phrase != $last_phrase ) {				
				$this->session->data['msmart_search_phrase'] = $phrase;
			
				$user_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
				$number_of_results = Msmart_Search::make( $this )->filterData( array('filter_name' => $phrase, 'not_save_in_search_history' => true) )->getTotalProducts();

				$sql = "
					INSERT INTO
						`" . DB_PREFIX . "msmart_search_history`
					SET
						`keyphrase` = '" . $this->db->escape( $phrase ) . "', 
						`customer_id` = '". $this->db->escape( $user_id ) ."', 
						`customer_ip` = '". $this->db->escape( $_SERVER['REMOTE_ADDR'] ) ."', 
						`number_of_results` = '". $this->db->escape( $number_of_results ) ."', 
						`date` = NOW(), 
						`time` = NOW()
				";
				
				$this->db->query($sql);
			}
		}
    }
	
	public function checkPhrase($phrase){
		if( null != ( $row = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "msmart_search_replaced_phrase` WHERE `search` = '" . $this->db->escape( $phrase ) . "' AND `regex` = '0'")->row ) ) {
			$phrase = $row['replaced'];
		}
		
		$regex_phrases = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "msmart_search_replaced_phrase` WHERE `regex` = '1'");
		
		if( ! $regex_phrases->num_rows ) {
			return $phrase;
		}
		
		foreach( $regex_phrases->rows as $row ) {
			try {
				$phrase = preg_replace( $row['search'], $row['replaced'], $phrase );
			} catch (Exception $ex) {}
		}
		
		return $phrase;
	}
}