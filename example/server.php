<?php

declare(strict_types=1);

session_start();

use divengine\ajaxmap;

require_once __DIR__ . '/../src/ajaxmap.php';

// Function to get the current server time
function getServerTime(): string
{
    return date('Y-m-d H:i:s');
}

// Encryption class with static and instance methods
class Encryption
{
    public static function getMd5(string $value): string
    {
        return md5($value);
    }

    public function getSha1(string $value): string
    {
        return sha1($value);
    }
}

// MyAjaxServer class extending ajaxmap
class MyAjaxServer extends ajaxmap
{
    public function __construct(string $name)
    {
        // Functions
        $this->addMethod('getServerTime', false, false, [], 'Returns the current server date and time');

        // Methods
        $this->addMethod('getClientIP');
        $this->addMethod('getPrivateData', false, true);
        $this->addMethod('getProducts', false, true);

        // Data
        $this->addData('Date', date('D M-d \of Y'));
        $this->addData('Server Description', 'This is an example of ajaxmap');

        parent::__construct($name);
    }

    public function getClientIP(): string
    {
        return self::getClientIPAddress();
    }

    public function getPrivateData(): string
    {
        return 'The number of your strong box is 53323';
    }

    public function getProducts(): array
    {
        return [
            [
                'Name' => 'Chai',
                'QuantityPerUnit' => '10 boxes x 20 bags',
                'UnitPrice' => 18,
            ],
            [
                'Name' => 'Chang',
                'QuantityPerUnit' => '24 - 12 oz bottles',
                'UnitPrice' => 19,
            ],
        ];
    }
}

// Server instance
$server = new MyAjaxServer('This is an example of ajaxmap server');
$server->addClass('Encryption');
$server->go();
