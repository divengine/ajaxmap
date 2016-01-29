<?php
/**
 * PHP AJAX Mapping
 * 
 * Mapping PHP data, functions and methods in JavaScript
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
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
 *
 * @author Rafa Rodriguez <rafacuba2015@gmail.com>
 * @version 1.1
 *
 */

/**
 * divAjaxMapping
 *
 * This is a static class and contain some methods
 * using by divAjaxMapping
 *
 */

class divAjaxMapping{


	private static $__method_access = array();

	/**
	 * Begin a session on server
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	static function login($username, $password){
		if (function_exists("divAjaxMapping_auth")){
			$r = divAjaxMapping_auth($username, $password);
			if ($r === true){
				$_SESSION['divAjaxMapping_user'] = $username;
				return true;
			}
		}
		return false;
	}

	/**
	 * Close session
	 */
	static function logout(){
		unset($_SESSION['divAjaxMapping_user']);
	}

	/**
	 * Verify authentication
	 * @return boolean
	 */
	static function verifyAuth(){
		return isset($_SESSION['divAjaxMapping_user']);
	}

	/**
	 * Register method for the control of access
	 *
	 * @param string $callable
	 */
	static function registerMethodAccess($callable){
		self::$__method_access[] = $callable;
	}

	/**
	 * Check if user can access to specific method
	 *
	 * @param string $method
	 * @return boolean
	 */
	static function checkMethodAccess($method, $namespace = ''){

		$methods = self::$__method_access;
		if (array_search('divAjaxMapping_chkma', $methods)=== false) $methods[] = 'divAjaxMapping_chkma';
			
		$access = true;
		foreach($methods as $chkma){
			if (is_callable($chkma)){
				$v = self::verifyAuth();
				$r = false;

				if ($v === true) eval('$r = '.$chkma.'($_SESSION[\'divAjaxMapping_user\'], $method, $namespace);');
				else eval('$r = '.$chkma.'(null, $method, $namespace);');

				$access = $access && $r;
			}
		}

		return $access;
	}

	/**
	 * Get the IP address of client
	 *
	 * @return string
	 */
	static function getClientIPAddress() {
		if (!isset($_SERVER)){
			$_SERVER = $HTTP_SERVER_VARS;
		}
		$ip = '127.0.0.1';
		if (isset($_SERVER['REMOTE_ADDR'])){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])){
			$ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
		}
		return $ip;
	}

	/**
	 * Check ip in range ip
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $ip
	 * @return boolean
	 */
	static function checkRangeIP($from, $to, $ip){
		$from = ip2long($from);
		$to = ip2long($to);
		$ip = ip2long($ip);
		return $ip>=$from && $ip<=to;
	}
}
