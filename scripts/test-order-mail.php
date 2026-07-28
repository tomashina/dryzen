<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

class Controller
{
    public $config;
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

echo "Order mail tests passed.\n";
