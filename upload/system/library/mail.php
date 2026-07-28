<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Mail class
*/
class Mail {
	protected $adaptor;
	protected $to;
	protected $bcc = array();
	protected $from;
	protected $sender;
	protected $reply_to;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments = array();
	public $protocol;
	public $parameter;
	public $smtp_hostname;
	public $smtp_username;
	public $smtp_password;
	public $smtp_port = 25;
	public $smtp_timeout = 5;

	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 *
 	*/
	public function __construct($adaptor = 'mail') {
		$class = 'Mail\\' . $adaptor;
		
		if (class_exists($class)) {
			$this->adaptor = new $class();
		} else {
			trigger_error('Error: Could not load mail adaptor ' . $adaptor . '!');
			exit();
		}	
	}
	
	/**
     * 
     *
     * @param	mixed	$to
     */
	public function setTo($to) {
		$this->to = $to;
	}

	/**
     *
     *
     * @param	mixed	$bcc
     */
	public function setBcc($bcc) {
		$this->bcc = $bcc;
	}
	
	/**
     * 
     *
     * @param	string	$from
     */
	public function setFrom($from) {
		$this->from = $from;
	}
	
	/**
     * 
     *
     * @param	string	$sender
     */
	public function setSender($sender) {
		$this->sender = $sender;
	}
	
	/**
     * 
     *
     * @param	string	$reply_to
     */
	public function setReplyTo($reply_to) {
		$this->reply_to = $reply_to;
	}
	
	/**
     * 
     *
     * @param	string	$subject
     */
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	/**
     * 
     *
     * @param	string	$text
     */
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
     * 
     *
     * @param	string	$html
     */
	public function setHtml($html) {
		$this->html = $html;
	}
	
	/**
     * 
     *
     * @param	string	$filename
     */
	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}
	
	/**
     * 
     *
     */
	public function send() {
		// Authenticated SMTP servers commonly require the envelope sender to
		// match the authenticated mailbox. Keep the public store address as the
		// reply-to address while sending from the SMTP account.
		if ($this->adaptor instanceof \Mail\Smtp && isset($this->smtp_username) && filter_var($this->smtp_username, FILTER_VALIDATE_EMAIL) && strcasecmp($this->from, $this->smtp_username) !== 0) {
			if (!$this->reply_to) {
				$this->reply_to = $this->from;
			}

			$this->from = $this->smtp_username;
		}

		if (!$this->to) {
			throw new \Exception('Error: E-Mail to required!');
		}

		if (!$this->from) {
			throw new \Exception('Error: E-Mail from required!');
		}

		if (!$this->sender) {
			throw new \Exception('Error: E-Mail sender required!');
		}

		if (!$this->subject) {
			throw new \Exception('Error: E-Mail subject required!');
		}

		if ((!$this->text) && (!$this->html)) {
			throw new \Exception('Error: E-Mail message required!');
		}
		
		foreach (get_object_vars($this) as $key => $value) {
			if ($key != 'adaptor') {
				$this->adaptor->$key = $value;
			}
		}
		
		$this->adaptor->send();
	}
}
