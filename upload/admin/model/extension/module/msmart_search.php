<?php 

/**
 * MSS
 * 
 * @author info@ocdemo.eu <info@ocdemo.eu> 
 */
class ModelExtensionModuleMsmartSearch extends Model {
	
	private $_stores_list;
	
	public function install() {
		$this->saveSettings('msmart_search', array(
			'fields' => array(
				'name' => array(
					'sort_order' => 0
				)
			)
		));
		
		$this->saveSettings('msmart_search_s', array(
			'enabled' => '1',
			'required_number_of_results' => '1'
		));
		
		$this->saveSettings('msmart_search_enabled', '1');
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "msmart_search_history` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`keyphrase` VARCHAR(255) NOT NULL,
				`customer_id` INT(11) NULL,
				`customer_ip` VARCHAR(64) NULL,
				`number_of_results` INT(11) NULL,
				`date` DATE NOT NULL,
				`time` TIME NOT NULL,
				PRIMARY KEY(`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "msmart_search_replaced_phrase` (
				`phrase_id` INT(11) NOT NULL AUTO_INCREMENT,
				`search` VARCHAR(255) NOT NULL,
				`replaced` VARCHAR(255) NOT NULL,
				`regex` TINYINT(1) NOT NULL DEFAULT '0',
				PRIMARY KEY(`phrase_id`),
				UNIQUE KEY `search` (search)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
		
		$this->update( true );
	}
	
	public function update( $install, $version = null ) {		
		if( $install || version_compare( $version, '3.0.3', '<' ) ) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "msmart_search_extra_field` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`type` VARCHAR(20) NOT NULL,
					`config` TEXT NOT NULL,
					PRIMARY KEY(`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
			");
		}
	}
	
	public function getExtraFields( $data ) {
		/* @var $sql string */
		$sql = "SELECT * FROM `" . DB_PREFIX . "msmart_search_extra_field`" . $this->sqlLimit( $data );
		
		/* @var $extra_fields array */
		$extra_fields = array();
		
		/* @var $row array */
		foreach( $this->db->query( $sql )->rows as $row ) {
			$row['config'] = json_decode( $row['config'], true );
			
			$extra_fields[] = $row;
		}
		
		return $extra_fields;
	}
	
	public function getExtraField( $id ) {
		/* @var $query \stdClass */
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "msmart_search_extra_field` WHERE `id` = " . (int) $id);
		
		if( ! $query->num_rows ) {
			return null;
		}
		
		/* @var $record array */
		$record = $query->row;
		
		/* @var $record array */
		$record['config'] = json_decode( $record['config'], true );
		
		return $record;
	}
	
	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteExtraField( $id ) {
		$this->db->query("
			DELETE FROM
				`" . DB_PREFIX . "msmart_search_extra_field`
			WHERE
				`id` = '" . (int) $id . "'"
		);
	}
	
	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteSearchHistory( $id ) {
		$this->db->query("
			DELETE FROM
				`" . DB_PREFIX . "msmart_search_history`
			WHERE
				`id` = '" . (int) $id . "'"
		);
	}
	
	/**
	 * @param int $limit
	 * @return array
	 */
	public function getTopSearchHistory( $limit ) {
		return $this->db->query("
			SELECT
				`keyphrase`, `customer_id`, `id`, COUNT(`keyphrase`) AS `total_searches`
			FROM 
				`" . DB_PREFIX . "msmart_search_history`
			GROUP BY 
				MD5(`keyphrase`)
			ORDER BY 
				`total_searches` DESC 
			LIMIT 
				0," . (int) $limit
		)->rows;
	}
	
	/**
	 * @param array $data
	 * @return array
	 */
	public function getSearchHistory( $data = array() ) {
		/* @var $conditions array */
		$conditions = $this->prepareSearchHistoryConditions( $data );
		
		/* @var $sql string */
		$sql = $this->createSearchHistoryQuery( '`id`, `keyphrase`, `firstname`, `lastname`, `customer_ip`, `number_of_results`, `date`, `time`', $conditions );
		
		$sql .= ' ORDER BY `h`.`date` DESC, `h`.`time` DESC' . $this->sqlLimit( $data );
		
		return $this->db->query( $sql )->rows;
	}
	
	private function sqlLimit( $data ) {
		if( isset( $data['start'] ) && isset( $data['limit'] ) ) {
			return ' LIMIT ' . (int) $data['start'] . ',' . (int) $data['limit'];
		}
		
		return '';
	}
	
	/**
	 * @param array $data
	 * @return int
	 */
	public function getTotalSearchHistory( $data = array() ) {
		/* @var $conditions array */
		$conditions = $this->prepareSearchHistoryConditions( $data );
		
		/* @var $sql string */
		$sql = $this->createSearchHistoryQuery( 'COUNT(*) AS `t`', $conditions );
		
		return $this->db->query( $sql )->row['t'];
	}
	
	/**
	 * @param string $columns
	 * @param array $conditions
	 * @return string
	 */
	private function createSearchHistoryQuery( $columns, $conditions ) {
		$sql = "
			SELECT 
				" . $columns . "
			FROM 
				`" . DB_PREFIX . "msmart_search_history` AS `h` 
			LEFT JOIN 
				`" . DB_PREFIX . "customer` AS `c` 
			ON 
				`h`.`customer_id` = `c`.`customer_id`";
		
		if( $conditions ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conditions );
		}
		
		return $sql;
	}
	
	private function prepareSearchHistoryConditions( $data ) {
		/* @var $conditions array */
		$conditions = array();
			
		if( ! empty( $data['phrase'] ) ) {
			if( strtolower( $data['phrase'] ) != 'guest' ) {
				/* @var $words array */
				$words = array_unique( explode( ' ', $data['phrase'] ) );
				
				/* @var $sub_conditions array */
				$sub_conditions = array();
				
				/* @var $word string */
				foreach( $words as $word ) {
					$sub_conditions[] = "(`c`.`firstname` LIKE '%" . $this->db->escape( $word ) . "%' OR `c`.`lastname` LIKE '%" . $this->db->escape( $word )."%' )";
				}
				
				if( $sub_conditions ) {
					$conditions[] = '( ' . implode( ' OR ', $sub_conditions ) . ' )';
				}
			} else {
				$conditions[] = "(`c`.`customer_id` IS NULL ) ";
			}
		}

		if( ! empty( $data['email'] ) ) {
			$conditions[] = "`c`.`email` LIKE '%" . $this->db->escape( $data['email'] ) . "%'";
		}

		if( ! empty( $data['date_start'] ) && ! empty( $data['date_end'] ) ) {
			$conditions[] = "(`h`.`date` BETWEEN '" . $this->db->escape( $data['date_start'] ) . "' AND '" . $this->db->escape( $data['date_end'] ) . "')";
		}
		
		return $conditions;
	}
	
	/**
	 * @return array
	 */
	public function getReplacedPhrases( $data = array() ) {
		/* @var $sql string */
		$sql = "SELECT * FROM `" . DB_PREFIX . "msmart_search_replaced_phrase`";
		
		if( isset( $data['start'] ) && isset( $data['limit'] ) ) {
			$sql .= " LIMIT " . (int) $data['start'] . ",". (int) $data['limit'];
		}
		
		return $this->db->query( $sql )->rows;
	}
	
	/**
	 * @return int
	 */
	public function getTotalReplacedPhrases( $data = array() ) {
		/* @var $sql string */
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "msmart_search_replaced_phrase`";
		
		return $this->db->query( $sql )->row['total'];
	}
	
	/**
	 * @param array $data
	 * @return array
	 */
	public function getReplacedPhrase( $data = array() ) {
		/* @var $sql string */
		$sql = "SELECT * FROM `" . DB_PREFIX . "msmart_search_replaced_phrase`";
		
		if( isset( $data['search'] ) ) {
			$conditions[] = "`search` = '" . $this->db->escape( $data['search'] ) . "'";
		}
		
		if( $conditions ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conditions );
		}
		
		return $this->db->query($sql)->row;
	}
	
	/**
	 * @param array $data
	 * @return int
	 */
	public function addReplacedPhrase( $data ) {
		$this->db->query("
			INSERT INTO
				`" . DB_PREFIX . "msmart_search_replaced_phrase`
			SET
				search='" . $this->db->escape( $data['search'] ) . "', 
				replaced='". $this->db->escape( $data['replaced'] ) ."',
				regex='". $this->db->escape( $data['regex'] ) . "'
		");
		
		return $this->db->getLastId();
	}
	
	/**
	 * @return void
	 */
	public function deleteReplacedPhrase( $id ) {
		$this->db->query("
			DELETE FROM
				`" . DB_PREFIX . "msmart_search_replaced_phrase`
			WHERE
				phrase_id = '".(int) $id."'
		");
	}
	
	/**
	 * @return array
	 */
	public function getDbTables() {
		return $this->db->query('SHOW tables')->rows;
	}
	
	/**
	 * @return array
	 */
	public function getDbTableColumns( $table ) {
		return $this->db->query('SHOW COLUMNS FROM `' . DB_PREFIX . $table . '`')->rows;
	}
	
	/**
	 * @param string $config
	 * @return int
	 */
	public function addExtraField( $type, $config ) {
		$this->db->query(
			"INSERT INTO 
				`" . DB_PREFIX . "msmart_search_extra_field`
			SET
				`type` = '" . $this->db->escape( $type ) . "',
				`config` = '" . $this->db->escape( json_encode( $config ) ) . "'
			"
		);
		
		return $this->db->getLastId();
	}
	
	/**
	 * @param int $id
	 * @param array $config
	 */
	public function updateExtraField( $id, $type, $config ) {
		$this->db->query(
			"UPDATE 
				`" . DB_PREFIX . "msmart_search_extra_field`
			SET
				`type` = '" . $this->db->escape( $type ) . "',
				`config` = '" . $this->db->escape( json_encode( $config ) ) . "'
			WHERE 
				`id` = " . (int) $id
		);
	}
	
	public function saveSettings( $group, $data ) {
		if( $this->_stores_list === NULL ) {
			$this->load->model('setting/store');

			$this->_stores_list = array(0);

			foreach( $this->model_setting_store->getStores() as $row ) {
				$this->_stores_list[] = $row['store_id'];
			}
		}
		
		$this->load->model('setting/setting');
		
		foreach( $this->_stores_list as $store_id ) {
			$this->model_setting_setting->editSetting($group, array( $group => $data ), $store_id);
		}
	}
	
	public function uninstall() {		
		$this->db->query("
			DELETE FROM
				`" . DB_PREFIX . "setting`
			WHERE
				`key` IN('msmart_search_version', 'msmart_search_license', 'msmart_search', 'msmart_search_s', 'msmart_search_enabled', 'msmart_search_latest_ver','msmart_search_lf','msmart_search_lf_enabled','msmart_search_recommended')
		");	
		
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "msmart_search_history`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "msmart_search_replaced_phrase`");
		
		// @since 3.0.2
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "msmart_search_extra_field`");
	}
	
	public function addColumn( $table, $column, $type ) {		
		$query = $this->db->query('SHOW COLUMNS FROM `' . DB_PREFIX . $table . '` LIKE "' . $column . '"');
		
		if( ! $query->num_rows ) {
			$this->db->query( 'ALTER TABLE `' . DB_PREFIX . $table . '` ADD `' . $column . '` ' . $type );
			
			return true;
		}
		
		return false;
	}
	
	public function removeColumn( $table, $column ) {		
		$query = $this->db->query('SHOW COLUMNS FROM `' . DB_PREFIX . $table . '` LIKE "' . $column . '"');
		
		if( $query->num_rows ) {
			$this->db->query('ALTER TABLE `' . DB_PREFIX . $table . '` DROP `' . $column . '`');
			
			return true;
		}
		
		return false;
	}

}
?>