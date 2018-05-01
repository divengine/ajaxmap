<?php

/**
 * Div PHP Ajax Mapping
 * Mapping PHP data, functions and methods in JavaScript
 * 
 * Example PHP script
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program as the file LICENSE.txt; if not, please see
 * http://www.gnu.org/licenses/gpl.txt.
 * 
 * @author Rafa Rodriguez <rafacuba2015@gmail.com>
 * @link http://divengine.com/solutions/div-php-ajax-mapping
 * @version 1.0
 */
session_start();

include "../divAjaxMapping.php";
include "../divAjaxMappingServer.php";

// Methods
function getServerTime ()
{
    return date("y-m-d h:i:s");
}

function getClientIP ()
{
    return divAjaxMapping::getClientIPAddress();
}

class Encryption
{

    public function getMd5 ($v)
    {
        return md5($v);
    }

    public function getSha1 ($v)
    {
        return sha1($v);
    }
}

function getPrivateData ()
{
    return "The number of your strong box is 53323";
}

// Hook implements
function divAjaxMapping_auth ($user, $password)
{
    return $user == "iam" && $password == "free";
}

function divAjaxMapping_chkma ($user, $method)
{
    return $user == "iam";
}

// Server instance

$server = new divAjaxMappingServer("This is an example of divAjaxMapping server");

// Functions
$server->addMethod("getServerTime", array(), false, false, array(), 
        "Return the date and time of the server");
$server->addMethod("getClientIP");
$server->addMethod("getPrivateData", array(), false, true);

// Methods
$server->addMethod("Encryption::getMd5", "v");
$server->addMethod("Encryption::getSha1", "v");

// Data
$server->addData("Date", date("D M-d \of Y"));
$server->addData("Server Description", 
        "This is an example divAjaxMapping server. For more information, go to the <a href = \"http://salvipascual.com/phphotmap\">phpHotMap site</a>");
$server->addData("Products", 
        array(
                array(
                        "ProductName" => "Chai",
                        "QuantityPerUnit" => "10 boxes x 20 bags",
                        "UnitPrice" => 18
                ),
                array(
                        "ProductName" => "Chang",
                        "QuantityPerUnit" => "24 - 12 oz bottles",
                        "UnitPrice" => 19
                )
        ));

$person1 = new stdClass();
$person1->name = "Peter Boston";
$person1->age = 45;
$person1->notes = array(
        "I need some products" => array(
                array(
                        "ProductName" => "Chai",
                        "Prices" => array(
                                18,
                                20,
                                34
                        )
                ),
                array(
                        "ProductName" => "Chang",
                        "Prices" => array(
                                19,
                                40,
                                55
                        )
                )
        ),
        "I have much money!" => "$1,000,000"
);

$person2 = new stdClass();
$person2->name = "Michael Roller";
$person2->age = 24;
$person2->notes = array(
        "My shop is terrific!"
);

$server->addData("Persons", array(
        $person1,
        $person2
));

$server->go();