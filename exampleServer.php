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

include "divAjaxMapping.php";

// A function
function getServerTime ()
{
    return date("y-m-d h:i:s");
}

// A class with static method
class Encryption
{

    public static function getMd5 ($v)
    {
        return md5($v);
    }

    public function getSha1 ($v)
    {
        return sha1($v);
    }
}

// 
class MyAjaxServer extends divAjaxMapping
{

    public function __construct ($name)
    {
        // Functions
        $this->addMethod("getServerTime", false, false, array(), "Return the date and time of the server");
        
        // Methods
        $this->addMethod("getClientIP");
        $this->addMethod("getPrivateData", false, true);
        $this->addMethod("getProducts", false, true);
        
        // Data
        $this->addData("Date", date("D M-d \of Y"));
        $this->addData("Server Description", "This is an example divAjaxMapping");
        
        parent::__construct($name);
    }

    public function getClientIP ()
    {
        return self::getClientIPAddress();
    }

    public function getPrivateData ()
    {
        return "The number of your strong box is 53323";
    }

    public function getProducts ()
    {
        return array(
                array(
                        "Name" => "Chai",
                        "QuantityPerUnit" => "10 boxes x 20 bags",
                        "UnitPrice" => 18
                ),
                array(
                        "Name" => "Chang",
                        "QuantityPerUnit" => "24 - 12 oz bottles",
                        "UnitPrice" => 19
                )
        );
    }
}

// Server instance

$server = new MyAjaxServer("This is an example of divAjaxMapping server");
$server->addClass('Encryption');
$server->go();
