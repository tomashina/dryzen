<?php
namespace modulepoints;
/**
 * https://cookieconsent.insites.com/documentation/about-cookie-consent/
 * https://github.com/insites/cookieconsent/
 */

class MpGdpr extends \ModulePoints {

	public $coderequestdeleteme = 'REQUEST_DELETEME';
	public $coderequestpersonaldata = 'REQUEST_PERSONAL_DATA';
	/*13 sep 2019 gdpr session starts*/
	public $coderequestresstrictdataprocessing = 'REQUEST_RESTRICT_DATA_PROCESSING';
	/*13 sep 2019 gdpr session ends*/
	public $codedownloadpersonalinfo = 'DOWNLOAD_PERSONAL_INFO';
	public $codedownloadorder = 'DOWNLOAD_ORDER';
	/*13 sep 2019 gdpr session starts*/
	/* No use of order history, order product, order vouchers. We include them in download order*/
	// public $codedownloadorderhistory = 'DOWNLOAD_ORDER_HISTORY';
	// public $codedownloadorderproduct = 'DOWNLOAD_ORDER_PRODUCT';
	// public $codedownloadordervoucher = 'DOWNLOAD_ORDER_VOUCHER';
	/*13 sep 2019 gdpr session ends*/
	public $codedownloadaddress = 'DOWNLOAD_ADDRESS';
	public $codedownloadgdpr = 'DOWNLOAD_GDPR';
	public $codedownloadwishlist = 'DOWNLOAD_WISHLIST';
	public $codedownloadhistorytransaction = 'DOWNLOAD_HISTORY_TRANSACTION';
	public $codedownloadhistorycustomer = 'DOWNLOAD_HISTORY_CUSTOMER';
	public $codedownloadhistorysearch = 'DOWNLOAD_HISTORY_SEARCH';
	public $codedownloadhistoryreward = 'DOWNLOAD_HISTORY_REWARD';
	public $codedownloadhistoryactivity = 'DOWNLOAD_HISTORY_ACTIVITY';

	// policy acceptance
	public $codepolicyacceptcontactus = 'POLICY_ACCEPT_CONTACTUS';
	public $codepolicyacceptregister = 'POLICY_ACCEPT_REGISTER';
	public $codepolicyacceptcheckout = 'POLICY_ACCEPT_CHECKOUT';
	public $codepolicyacceptcookieconsent = 'POLICY_ACCEPT_COOKIECONSENT';

	// Access Request Status IDS
	public $requestaccess_expire = '0'; // on verification fail
	public $requestaccess_confirmed = '1'; // on verification success
	public $requestaccess_awating = '2'; // when create new request
	public $requestaccess_reportsend = '3'; // update by admin
	public $requestaccess_deny = '4'; // update by admin

	// Anonymouse/Deletion Request Status IDS
	public $requestanonymouse_expire = '0'; // on verification fail
	public $requestanonymouse_confirmed = '1'; // on verification success
	public $requestanonymouse_awating = '2'; // when create new request
	public $requestanonymouse_complete = '3'; // update by admin
	public $requestanonymouse_deny = '4'; // update by admin

	private $logger;
	public function log($message, $write=1) {
		if ($write) {
			$this->logger->write($message);
		}
	}

	public function __construct($registry) 	{
		parent:: __construct($registry);
		// do any startup work here
		$this->load->language('mpgdpr/requests');

		$this->logger = new \Log('mpgdpr.log');
	}
	/*13 sep 2019 gdpr session starts*/
	public function getRequestTypes($getRequests=array()) {
	/*13 sep 2019 gdpr session ends*/
		$data = array();
		$data[] = array(
			'code' => $this->coderequestdeleteme,
			'value' => $this->language->get('text_'. $this->coderequestdeleteme)
		);
		$data[] = array(
			'code' => $this->coderequestpersonaldata,
			'value' => $this->language->get('text_'. $this->coderequestpersonaldata)
		);
		/*13 sep 2019 gdpr session starts*/
		$data[] = array(
			'code' => $this->coderequestresstrictdataprocessing,
			'value' => $this->language->get('text_'. $this->coderequestresstrictdataprocessing)
		);
		/*13 sep 2019 gdpr session ends*/
		$data[] = array(
			'code' => $this->codedownloadpersonalinfo,
			'value' => $this->language->get('text_'. $this->codedownloadpersonalinfo)
		);
		$data[] = array(
			'code' => $this->codedownloadorder,
			'value' => $this->language->get('text_'. $this->codedownloadorder)
		);
		/*13 sep 2019 gdpr session starts*/
		/* No use of order history, order product, order vouchers. We include them in download order*/
		// $data[] = array(
		// 	'code' => $this->codedownloadorderhistory,
		// 	'value' => $this->language->get('text_'. $this->codedownloadorderhistory)
		// );
		// $data[] = array(
		// 	'code' => $this->codedownloadorderproduct,
		// 	'value' => $this->language->get('text_'. $this->codedownloadorderproduct)
		// );
		// $data[] = array(
		// 	'code' => $this->codedownloadordervoucher,
		// 	'value' => $this->language->get('text_'. $this->codedownloadordervoucher)
		// );
		/*13 sep 2019 gdpr session ends*/
		$data[] = array(
			'code' => $this->codedownloadaddress,
			'value' => $this->language->get('text_'. $this->codedownloadaddress)
		);
		$data[] = array(
			'code' => $this->codedownloadgdpr,
			'value' => $this->language->get('text_'. $this->codedownloadgdpr)
		);
		$data[] = array(
			'code' => $this->codedownloadwishlist,
			'value' => $this->language->get('text_'. $this->codedownloadwishlist)
		);
		$data[] = array(
			'code' => $this->codedownloadhistorytransaction,
			'value' => $this->language->get('text_'. $this->codedownloadhistorytransaction)
		);
		$data[] = array(
			'code' => $this->codedownloadhistorycustomer,
			'value' => $this->language->get('text_'. $this->codedownloadhistorycustomer)
		);
		$data[] = array(
			'code' => $this->codedownloadhistorysearch,
			'value' => $this->language->get('text_'. $this->codedownloadhistorysearch)
		);
		$data[] = array(
			'code' => $this->codedownloadhistoryreward,
			'value' => $this->language->get('text_'. $this->codedownloadhistoryreward)
		);
		$data[] = array(
			'code' => $this->codedownloadhistoryactivity,
			'value' => $this->language->get('text_'. $this->codedownloadhistoryactivity)
		);
		$data[] = array(
			'code' => $this->codepolicyacceptcontactus,
			'value' => $this->language->get('text_'. $this->codepolicyacceptcontactus)
		);
		$data[] = array(
			'code' => $this->codepolicyacceptregister,
			'value' => $this->language->get('text_'. $this->codepolicyacceptregister)
		);
		$data[] = array(
			'code' => $this->codepolicyacceptcheckout,
			'value' => $this->language->get('text_'. $this->codepolicyacceptcheckout)
		);
		$data[] = array(
			'code' => $this->codepolicyacceptcookieconsent,
			'value' => $this->language->get('text_'. $this->codepolicyacceptcookieconsent)
		);
		/*13 sep 2019 gdpr session starts*/
		if (!empty($getRequests) && is_array($getRequests)) {
			foreach ($data as $key => $value) {
				if (!in_array($value['code'], $getRequests)) {
					unset($data[$key]);
				}
			}
		}
		/*13 sep 2019 gdpr session ends*/
		return $data;
	}

    public function anonymouseCustomerGDPRData($customer_id) {

    }
    public function install() {
    	// create all tables here
		/*--
		-- Table structure for table `oc_mpgdpr_datarequest`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpgdpr_datarequest` (
		  `mpgdpr_datarequest_id` int(11) NOT NULL AUTO_INCREMENT,
		  `customer_id` int(11) NOT NULL,
		  `store_id` int(11) NOT NULL,
		  `server_ip` varchar(100) NOT NULL,
		  `client_ip` varchar(100) NOT NULL,
		  `user_agent` varchar(500) NOT NULL,
		  `accept_language` varchar(255) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `code` varchar(255) NOT NULL,
		  `attachment` varchar(500) NOT NULL,
		  `denyreason` text NOT NULL,
		  `date_send` date NOT NULL,
		  `session_id` varchar(255) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `expire_on` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpgdpr_datarequest_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");

		/*--
		-- Table structure for table `oc_mpgdpr_deleteme`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpgdpr_deleteme` (
		  `mpgdpr_deleteme_id` int(11) NOT NULL AUTO_INCREMENT,
		  `customer_id` int(11) NOT NULL,
		  `store_id` int(11) NOT NULL,
		  `server_ip` varchar(100) NOT NULL,
		  `client_ip` varchar(100) NOT NULL,
		  `user_agent` varchar(500) NOT NULL,
		  `accept_language` varchar(255) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `code` varchar(255) NOT NULL,
		  `date_deletion` date NOT NULL,
		  `denyreason` text NOT NULL,
		  `session_id` varchar(255) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `expire_on` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpgdpr_deleteme_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");

		/*--
		-- Table structure for table `oc_mpgdpr_policyacceptance`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpgdpr_policyacceptance` (
		  `mpgdpr_policyacceptance_id` int(11) NOT NULL AUTO_INCREMENT,
		  `requessttype` varchar(100) NOT NULL,
		  `customer_id` int(11) NOT NULL,
		  `email` varchar(96) NOT NULL,
		  `store_id` int(11) NOT NULL,
		  `policy_id` int(11) NOT NULL,
		  `policy_title` varchar(255) NOT NULL,
		  `policy_description` mediumtext NOT NULL,
		  `server_ip` varchar(100) NOT NULL,
		  `client_ip` varchar(100) NOT NULL,
		  `user_agent` varchar(500) NOT NULL,
		  `accept_language` varchar(255) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpgdpr_policyacceptance_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");

		/*--
		-- Table structure for table `oc_mpgdpr_requestlist`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpgdpr_requestlist` (
		  `mpgdpr_requestlist_id` int(11) NOT NULL AUTO_INCREMENT,
		  `customer_id` int(11) NOT NULL,
		  `email` varchar(96) NOT NULL,
		  `store_id` int(11) NOT NULL,
		  `requessttype` varchar(100) NOT NULL,
		  `server_ip` varchar(100) NOT NULL,
		  `client_ip` varchar(100) NOT NULL,
		  `user_agent` varchar(500) NOT NULL,
		  `accept_language` varchar(255) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpgdpr_requestlist_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");

		/*--
		-- Table structure for table `oc_mpgdpr_restrict_processing`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpgdpr_restrict_processing` (
		  `mpgdpr_restrict_processing_id` int(11) NOT NULL AUTO_INCREMENT,
		  `customer_id` int(11) NOT NULL,
		  `store_id` int(11) NOT NULL,
		  `server_ip` varchar(100) NOT NULL,
		  `client_ip` varchar(100) NOT NULL,
		  `user_agent` varchar(500) NOT NULL,
		  `accept_language` varchar(255) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `session_id` varchar(255) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_modified` datetime NOT NULL,
		  PRIMARY KEY (`mpgdpr_restrict_processing_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");

		/*--
		-- Table structure for table `oc_mpgdpr_upload`
		--*/

		$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpgdpr_upload` (
		  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL,
		  `filename` varchar(255) NOT NULL,
		  `code` varchar(255) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `in_use` tinyint(1) NOT NULL,
		  PRIMARY KEY (`upload_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
		");

		/*13 sep 2019 gdpr session starts*/
		/*add new columns in mpgdpr_requestlist table. to pass any custom string as identified for request.*/
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "mpgdpr_requestlist` WHERE Field='custom_string'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "mpgdpr_requestlist` ADD `custom_string` text NOT NULL AFTER `requessttype`");
		}
		/*13 sep 2019 gdpr session ends*/
    }

	public function anonymouse($text) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $anonymouse = '';
	    $text_len = strlen($text);
	    $string_len = strlen($characters);
	    for ($i = 0; $i < $text_len; $i++) {
	    	/*13 sep 2019 gdpr session starts*/
	        $anonymouse .= $characters[rand(0, $string_len-1)];
	        /*13 sep 2019 gdpr session ends*/
	    }
	    return $anonymouse;
	}

	public function getRequestName($request_type) {
		return $this->language->get('text_'.$request_type);
	}

	// remove this function
	// public function getRequestType($request_type) {
	// 	$request_code = 0;
	// 	switch ($request_type) {
	// 		case $this->coderequestdeleteme:
	// 			$request_code = 1;
	// 			break;
	// 		case $this->coderequestpersonaldata:
	// 			$request_code = 2;
	// 			break;
	// 		case $this->codedownloadpersonalinfo:
	// 			$request_code = 3;
	// 			break;
	// 		case $this->codedownloadorder:
	// 			$request_code = 4;
	// 			break;
	// 		case $this->codedownloadaddress:
	// 			$request_code = 5;
	// 			break;
	// 		case $this->codedownloadgdpr:
	// 			$request_code = 6;
	// 			break;
	// 		default:
	// 			$request_code = 0;
	// 			break;
	// 	}
	// 	return $request_code;
	// }
}