<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

if (!defined('DIR_IMAGE')) {
    define('DIR_IMAGE', dirname(__DIR__) . '/upload/image/');
}

class Controller
{
    public $config;
}

class Mail
{
    public $engine;
    public $parameter;
    public $smtp_hostname;
    public $smtp_username;
    public $smtp_password;
    public $smtp_port;
    public $smtp_timeout;
    public $to;
    public $bcc;
    public $from;
    public $sender;
    public $subject;
    public $text;
    public $html;
    public $single_part_html = false;
    public $attachments = array();

    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    public function setTo($value)
    {
        $this->to = $value;
    }

    public function setBcc($value)
    {
        $this->bcc = $value;
    }

    public function setFrom($value)
    {
        $this->from = $value;
    }

    public function setSender($value)
    {
        $this->sender = $value;
    }

    public function setSubject($value)
    {
        $this->subject = $value;
    }

    public function setText($value)
    {
        $this->text = $value;
    }

    public function setHtml($value)
    {
        $this->html = $value;
    }

    public function setSinglePartHtml($value)
    {
        $this->single_part_html = (bool)$value;
    }

    public function addAttachment($filename)
    {
        $this->attachments[] = $filename;
    }
}

class DryzenOrderMailTestConfig
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}

class DryzenOrderMailTestLanguage
{
    private $values = array(
        'text_boxnow_locker' => 'BOX NOW paketomat',
        'text_boxnow_address' => 'Adresa',
        'text_boxnow_locker_id' => 'ID lockera',
		'text_gls_parcel_shop' => 'GLS paket shop',
		'text_gls_parcel_locker' => 'GLS paketomat',
		'text_gls_pickup_location' => 'Mjesto preuzimanja',
		'text_gls_pickup_id' => 'ID lokacije',
    );

    public function get($key)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $key;
    }
}

function dryzenOrderMailAssertSame($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            "FAIL: {$message}\nExpected: " . var_export($expected, true)
            . "\nActual: " . var_export($actual, true) . "\n"
        );
        exit(1);
    }
}

require_once dirname(__DIR__) . '/upload/catalog/controller/mail/order.php';

$reflection = new ReflectionClass('ControllerMailOrder');
$controller = $reflection->newInstanceWithoutConstructor();
$controller->config = new DryzenOrderMailTestConfig(array(
    'config_email' => 'shop@milla.hr',
    'config_mail_alert' => array('order', 'review'),
    'config_mail_alert_email' => "nabava@milla.hr, order@milla.hr\nORDER@milla.hr;invalid-address",
    'config_mail_engine' => 'smtp',
    'config_mail_parameter' => '',
    'config_mail_smtp_hostname' => 'tls://smtp.example.com',
    'config_mail_smtp_username' => 'shop@milla.hr',
    'config_mail_smtp_password' => 'secret',
    'config_mail_smtp_port' => 587,
    'config_mail_smtp_timeout' => 5,
));

$parseBoxNowLocation = $reflection->getMethod('parseBoxNowLocation');
$parseBoxNowLocation->setAccessible(true);

dryzenOrderMailAssertSame(
    array(
        'address' => '10000, Ilica 1, Zagreb',
        'locker_id' => 'HR100',
    ),
    $parseBoxNowLocation->invoke($controller, '10000, Ilica 1, Zagreb;HR100'),
    'BOX NOW address and locker ID are parsed from the checkout value.'
);

dryzenOrderMailAssertSame(
    array(
        'address' => '',
        'locker_id' => 'HR200',
    ),
    $parseBoxNowLocation->invoke($controller, 'HR200'),
    'Legacy values containing only a locker ID remain supported.'
);

$formatShippingMethod = $reflection->getMethod('formatShippingMethodForEmail');
$formatShippingMethod->setAccessible(true);

dryzenOrderMailAssertSame(
    'BOX NOW<br /><br /><strong>BOX NOW paketomat</strong><br /><strong>Adresa:</strong> 10000, Ilica 1 &amp; 3, Zagreb<br /><strong>ID lockera:</strong> HR100',
    $formatShippingMethod->invoke(
        $controller,
        array(
            'shipping_method' => 'BOX NOW',
            'shipping_code' => 'boxnow.boxnow',
            'boxnow' => '10000, Ilica 1 & 3, Zagreb;HR100',
        ),
        new DryzenOrderMailTestLanguage()
    ),
    'The order email shows escaped BOX NOW address and locker ID as separate fields.'
);

$parseGlsPickupPoint = $reflection->getMethod('parseGlsPickupPoint');
$parseGlsPickupPoint->setAccessible(true);

dryzenOrderMailAssertSame(
	array(
		'location' => 'GLS Centar, Ilica 1 & 3, Zagreb',
		'point_id' => 'HR-GLS-100',
	),
	$parseGlsPickupPoint->invoke($controller, 'GLS Centar, Ilica 1 &amp; 3, Zagreb;HR-GLS-100'),
	'GLS pickup location and identifier are parsed from the checkout value.'
);

dryzenOrderMailAssertSame(
	'GLS Paketomat<br /><br /><strong>GLS paketomat</strong><br /><strong>Mjesto preuzimanja:</strong> GLS Centar, Ilica 1 &amp; 3, Zagreb<br /><strong>ID lokacije:</strong> HR-GLS-100',
	$formatShippingMethod->invoke(
		$controller,
		array(
			'shipping_method' => 'GLS Paketomat',
			'shipping_code' => 'glspaketomat.glspaketomat',
			'gls_ps' => 'GLS Centar, Ilica 1 & 3, Zagreb;HR-GLS-100',
		),
		new DryzenOrderMailTestLanguage()
	),
	'The order email safely shows the selected GLS parcel locker and its identifier.'
);

$getRecipients = $reflection->getMethod('getOrderMailRecipients');
$getRecipients->setAccessible(true);

dryzenOrderMailAssertSame(
    array(
        array('email' => 'customer@example.com', 'context' => 'customer confirmation'),
        array('email' => 'shop@milla.hr', 'context' => 'store confirmation'),
        array('email' => 'nabava@milla.hr', 'context' => 'additional store confirmation'),
        array('email' => 'order@milla.hr', 'context' => 'additional store confirmation'),
    ),
    $getRecipients->invoke($controller, 'customer@example.com'),
    'The full order email includes configured additional recipients without duplicates.'
);

$controller->config = new DryzenOrderMailTestConfig(array(
    'config_email' => 'shop@milla.hr',
    'config_mail_alert' => array('review'),
    'config_mail_alert_email' => 'nabava@milla.hr,order@milla.hr',
));

dryzenOrderMailAssertSame(
    array(
        array('email' => 'customer@example.com', 'context' => 'customer confirmation'),
        array('email' => 'shop@milla.hr', 'context' => 'store confirmation'),
    ),
    $getRecipients->invoke($controller, 'customer@example.com'),
    'Additional order recipients respect the Orders alert setting.'
);

$controller->config = new DryzenOrderMailTestConfig(array(
    'config_mail_engine' => 'smtp',
    'config_mail_parameter' => '',
    'config_mail_smtp_hostname' => 'tls://smtp.example.com',
    'config_mail_smtp_username' => 'shop@milla.hr',
    'config_mail_smtp_password' => 'secret',
    'config_mail_smtp_port' => 587,
    'config_mail_smtp_timeout' => 5,
));

$createMail = $reflection->getMethod('createOrderConfirmationMail');
$createMail->setAccessible(true);
$fullHtml = '<html><body><h1>Narudžba 15</h1><p>Proizvodi i ukupni iznos.</p></body></html>';
$customerMail = $createMail->invoke(
    $controller,
    'customer@example.com',
    'shop@milla.hr',
    'DryZen',
    'DryZen - Narudžba 15',
    $fullHtml,
    false
);
$adminMail = $createMail->invoke(
    $controller,
    'admin@milla.hr',
    'shop@milla.hr',
    'DryZen',
    'DryZen - Narudžba 15',
    $fullHtml,
    true
);
$additionalMail = $createMail->invoke(
    $controller,
    'nabava@milla.hr',
    'shop@milla.hr',
    'DryZen',
    'DryZen - Narudžba 15',
    $fullHtml,
    true
);

dryzenOrderMailAssertSame(false, $customerMail === $adminMail, 'The customer and admin receive standalone messages.');
dryzenOrderMailAssertSame(false, $adminMail === $additionalMail, 'Every additional recipient receives a standalone message.');
dryzenOrderMailAssertSame($fullHtml, $customerMail->html, 'The customer mail contains the full rendered HTML.');
dryzenOrderMailAssertSame($fullHtml, $adminMail->html, 'The admin mail contains the same full rendered HTML.');
dryzenOrderMailAssertSame($fullHtml, $additionalMail->html, 'Additional recipients contain the same full rendered HTML.');
dryzenOrderMailAssertSame(
    "Narudžba 15\nProizvodi i ukupni iznos.",
    $customerMail->text,
    'The customer order has a complete plain-text fallback for strict mail clients.'
);
dryzenOrderMailAssertSame(
    $customerMail->text,
    $adminMail->text,
    'The admin receives the identical complete plain-text fallback.'
);
dryzenOrderMailAssertSame($customerMail->text, $additionalMail->text, 'Additional recipients receive the identical text fallback.');
dryzenOrderMailAssertSame('customer@example.com', $customerMail->to, 'The customer copy has the correct recipient.');
dryzenOrderMailAssertSame('admin@milla.hr', $adminMail->to, 'The admin copy has the correct recipient.');
dryzenOrderMailAssertSame('nabava@milla.hr', $additionalMail->to, 'The additional copy has the correct recipient.');
dryzenOrderMailAssertSame(false, $customerMail->single_part_html, 'The customer keeps the existing multipart mail.');
dryzenOrderMailAssertSame(true, $adminMail->single_part_html, 'The admin receives Roundcube-compatible single-part HTML.');
dryzenOrderMailAssertSame(true, $additionalMail->single_part_html, 'Additional recipients receive single-part HTML.');
dryzenOrderMailAssertSame(
    array(DIR_IMAGE . 'catalog/legal/eu-legal-guarantee-hr.png'),
    $customerMail->attachments,
    'The official unmodified legal-guarantee notice is attached to the customer confirmation.'
);
dryzenOrderMailAssertSame($customerMail->attachments, $adminMail->attachments, 'The store copy includes the same official notice attachment.');

$route = '';
$args = array();
$controller->alert($route, $args);

require_once dirname(__DIR__) . '/upload/system/library/mail/smtp.php';

$smtp = new \Mail\Smtp();
$smtp->to = 'customer@example.com';
$smtp->bcc = array('admin@milla.hr', 'nabava@milla.hr', 'ADMIN@milla.hr');
$smtpReflection = new ReflectionClass('Mail\\Smtp');
$getSmtpRecipients = $smtpReflection->getMethod('getRecipients');
$getSmtpRecipients->setAccessible(true);
$encodeSmtpBase64 = $smtpReflection->getMethod('encodeBase64');
$encodeSmtpBase64->setAccessible(true);

dryzenOrderMailAssertSame(
    array('customer@example.com', 'ADMIN@milla.hr', 'nabava@milla.hr'),
    $getSmtpRecipients->invoke($smtp),
    'SMTP sends one payload to the primary and unique BCC envelope recipients.'
);

dryzenOrderMailAssertSame(
    false,
    strpos(file_get_contents(dirname(__DIR__) . '/upload/system/library/mail/smtp.php'), 'Bcc:') !== false,
    'BCC addresses are not exposed in the SMTP message headers.'
);

$longHtml = '<html><body>' . str_repeat('Puni sadržaj narudžbe čćžšđ. ', 100) . '</body></html>';
$encodedHtml = $encodeSmtpBase64->invoke($smtp, $longHtml);
$encodedLines = preg_split('/\R/', trim($encodedHtml));

foreach ($encodedLines as $line) {
    dryzenOrderMailAssertSame(true, strlen($line) <= 76, 'MIME Base64 lines never exceed 76 characters.');
    dryzenOrderMailAssertSame(0, strlen($line) % 4, 'Every MIME Base64 line ends on a complete encoding quantum.');
}

dryzenOrderMailAssertSame(
    $longHtml,
    base64_decode(implode('', $encodedLines), true),
    'Strict mail clients can decode the complete HTML body.'
);

if (function_exists('pcntl_fork') && function_exists('stream_socket_pair')) {
    $server = stream_socket_server('tcp://127.0.0.1:0', $serverErrorNumber, $serverError);

    if (!$server) {
        throw new RuntimeException('Unable to start the test SMTP server: ' . $serverError);
    }

    $serverAddress = stream_socket_get_name($server, false);
    $serverPort = (int)substr(strrchr($serverAddress, ':'), 1);
    $transport = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, 0);
    $processId = pcntl_fork();

    if ($processId === 0) {
        fclose($transport[0]);
        $connection = stream_socket_accept($server, 10);
        $capturedMessage = '';

        if ($connection) {
            fwrite($connection, "220 localhost test SMTP\r\n");

            while (($line = fgets($connection)) !== false) {
                $command = rtrim($line, "\r\n");

                if (stripos($command, 'EHLO ') === 0 || stripos($command, 'HELO ') === 0) {
                    fwrite($connection, "250 localhost\r\n");
                } elseif (stripos($command, 'MAIL FROM:') === 0 || stripos($command, 'RCPT TO:') === 0) {
                    fwrite($connection, "250 accepted\r\n");
                } elseif ($command === 'DATA') {
                    fwrite($connection, "354 send message\r\n");

                    while (($dataLine = fgets($connection)) !== false) {
                        if (rtrim($dataLine, "\r\n") === '.') {
                            break;
                        }

                        $capturedMessage .= $dataLine;
                    }

                    fwrite($connection, "250 queued\r\n");
                } elseif ($command === 'QUIT') {
                    fwrite($connection, "221 bye\r\n");
                    break;
                }
            }

            fclose($connection);
        }

        fwrite($transport[1], $capturedMessage);
        fclose($transport[1]);
        fclose($server);
        exit(0);
    }

    if ($processId < 0) {
        throw new RuntimeException('Unable to fork the test SMTP server.');
    }

    fclose($transport[1]);
    $singlePartSmtp = new \Mail\Smtp();
    $singlePartSmtp->to = 'admin@milla.hr';
    $singlePartSmtp->from = 'shop@milla.hr';
    $singlePartSmtp->sender = 'DryZen';
    $singlePartSmtp->subject = 'DryZen - Narudžba 15';
    $singlePartSmtp->text = 'Puni sadržaj narudžbe.';
    $singlePartSmtp->html = $fullHtml;
    $singlePartSmtp->single_part_html = true;
    $singlePartSmtp->smtp_hostname = '127.0.0.1';
    $singlePartSmtp->smtp_port = $serverPort;
    $singlePartSmtp->smtp_timeout = 5;
    $singlePartSmtp->send();
    $capturedMessage = stream_get_contents($transport[0]);
    fclose($transport[0]);
    fclose($server);
    pcntl_waitpid($processId, $processStatus);

    dryzenOrderMailAssertSame(
        true,
        strpos($capturedMessage, 'Content-Type: text/html; charset="utf-8"') !== false,
        'The internal SMTP copy is a single-part HTML message.'
    );
    dryzenOrderMailAssertSame(
        true,
        strpos($capturedMessage, 'Content-Transfer-Encoding: quoted-printable') !== false,
        'The internal SMTP copy uses Roundcube-compatible quoted-printable encoding.'
    );
    dryzenOrderMailAssertSame(
        false,
        strpos($capturedMessage, 'multipart/') !== false,
        'The internal SMTP copy has no multipart structure for Roundcube to misparse.'
    );

    $capturedParts = preg_split("/\r?\n\r?\n/", $capturedMessage, 2);
    $capturedBody = isset($capturedParts[1]) ? $capturedParts[1] : '';
    dryzenOrderMailAssertSame(
        $fullHtml,
        rtrim(quoted_printable_decode($capturedBody), "\r\n"),
        'The full customer HTML survives the actual SMTP transport for the admin.'
    );
}

echo "Order mail tests passed.\n";
