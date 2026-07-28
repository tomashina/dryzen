<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
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
$orderMail = $createMail->invoke(
    $controller,
    'customer@example.com',
    array('admin@milla.hr', 'nabava@milla.hr', 'order@milla.hr'),
    'shop@milla.hr',
    'DryZen',
    'DryZen - Narudžba 15',
    $fullHtml
);

dryzenOrderMailAssertSame($fullHtml, $orderMail->html, 'The order mail contains the full rendered customer HTML.');
dryzenOrderMailAssertSame(
    "Narudžba 15\nProizvodi i ukupni iznos.",
    $orderMail->text,
    'The same order also has a complete plain-text fallback for strict mail clients.'
);
dryzenOrderMailAssertSame('customer@example.com', $orderMail->to, 'The customer remains the visible primary recipient.');
dryzenOrderMailAssertSame(
    array('admin@milla.hr', 'nabava@milla.hr', 'order@milla.hr'),
    $orderMail->bcc,
    'The admin and additional addresses receive the exact same message through BCC.'
);

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

echo "Order mail tests passed.\n";
