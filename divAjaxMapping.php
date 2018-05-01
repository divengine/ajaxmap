<?php

/**
 * Div PHP Ajax Mapping
 *
 * Server instance
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program as the file LICENSE.txt; if not, please see
 * http://www.gnu.org/licenses/gpl.txt.
 *
 * @author  Rafa Rodriguez <rafageist@hotmail.com>
 * @version 1.2
 *
 */
define("DIV_AJAX_MAPPING_ACCESS_DENIED_HOST", "DIV_AJAX_MAPPING_ACCESS_DENIED_HOST");
define("DIV_AJAX_MAPPING_ACCESS_DENIED_USER", "DIV_AJAX_MAPPING_ACCESS_DENIED_USER");
define("DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL", "DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL");
define("DIV_AJAX_MAPPING_LOGIN_FAILED", "DIV_AJAX_MAPPING_LOGIN_FAILED");
define("DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL", "DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL");
define("DIV_AJAX_MAPPING_METHOD_EXECUTED", "DIV_AJAX_MAPPING_METHOD_EXECUTED");
define("DIV_AJAX_MAPPING_METHOD_NOT_EXISTS", "DIV_AJAX_MAPPING_METHOD_NOT_EXISTS");

/**
 * How to use?
 *
 * $server = new divAjaxMapping(); // Server instance
 * $server->addMethod("getEnterprise", "reu"); // Add public method
 * $server->addMethod("Company::getEmployees", true); // Add private method
 * $server->addData("Company", array("name" => "My Company", "phone" =>
 * (444)-485758")); // Add some data
 * $server->go();
 */
class divAjaxMapping
{

	private $methods = [];

	private $data = [];

	public $name = null;

	public function __construct($name)
	{
		$this->__name = $name;
	}

	/**
	 * Add data
	 *
	 * @param string $var
	 * @param mixed  $value
	 */
	public function addData($var, $value)
	{
		$var                = str_replace(" ", "_", $var);
		$this->data[ $var ] = $value;
	}

	/**
	 * Register method
	 *
	 * @param string  $name
	 * @param boolean $params_complex
	 * @param boolean $security
	 * @param array   $hosts
	 * @param mixed   $include
	 * @param string  $namespace
	 *
	 * @throws Exception
	 */
	public function addMethod($name, $params_complex = false, $security = false, $hosts = [], $include = null, $namespace = null)
	{
		$params = [];

		$class_name = '';

		if(strpos($name, "::") !== false)
		{
			$parts      = explode("::", $name);
			$class_name = $parts[0];
			$name       = $parts[1];
			$r          = new ReflectionClass($class_name);
			if(method_exists($class_name, $name))
			{
				$m = $r->getMethod($name);
				if($m->isStatic() && $m->isPublic())
				{
					$p = $m->getParameters();
					foreach($p as $param)
					{

						$params[] = $param->getName();
					}
					$class_name .= '::';
				}
				else
				{
					throw new Exception("-- $class_name::$name -- is not a static or public method");
				}
			}
		}
		else
		{
			if(is_callable($name))
			{
				$r = new ReflectionFunction($name);
				$p = $r->getParameters();
				foreach($p as $param)
				{
					$params[] = $param->getName();
				}
			}
			else
			{
				$r = new ReflectionObject($this);
				if(method_exists($this, $name))
				{
					$m = $r->getMethod($name);
					$p = $m->getParameters();
					foreach($p as $param)
					{
						$params[] = $param->getName();
					}
				}
			}
		}

		$m = [
			"security" => $security,
			"hosts" => $hosts,
			"name" => $class_name . $name,
			"params" => $params,
			"params_complex" => $params_complex,
			"include" => $include,
			"namespace" => $namespace
		];

		$this->methods[ $class_name . $name ] = $m;
		if($namespace != '' && ! is_null($namespace)) $this->methods[ $namespace ] = $m;
	}

	/**
	 * Add static methods of entire class
	 *
	 * @param string $class_name
	 */
	public function addClass($class_name)
	{
		if(class_exists($class_name))
		{
			$r = new ReflectionClass($class_name);
			$m = $r->getMethods();
			foreach($m as $method)
			{
				if($method->isStatic() && $method->isPublic())
				{
					$this->addMethod($class_name . "::" . $method->getName());
				}
			}
		}
	}

	/**
	 * Publish methods: send a json to browser
	 */
	private function publish()
	{
		echo "{\n";
		$j      = 0;
		$clases = [];
		foreach($this->methods as $key => $m)
		{
			$namespace = false;
			if(isset($m['namespace'])) if($m['namespace'] != '') if( ! is_null($m['namespace'])) $namespace = $m['namespace'];

			if(strpos($key, "::") !== false && $namespace === false)
			{
				$arr = explode("::", $key);

				if( ! isset($clases[ $arr[0] ]))
				{
					$clases[ $arr[0] ] = $arr[0] . ": {\n";
				}
				else
				{
					$clases[ $arr[0] ] .= ",";
				}

				if($m['params_complex'] == true)
				{
					$clases[ $arr[0] ] .= ($namespace !== false ? "'$namespace'" : "{$arr[1]}") . ": function(params){\n";
				}
				else
				{
					$clases[ $arr[0] ] .= ($namespace !== false ? "'$namespace'" : "{$arr[1]}") . ": function(";

					$i = 0;
					foreach($m['params'] as $p)
					{
						if($i ++ > 0) $clases[ $arr[0] ] .= ",";
						$clases[ $arr[0] ] .= "$p";
					}

					$clases[ $arr[0] ] .= "){\n   var params = {};";
					foreach($m['params'] as $p)
					{
						$clases[ $arr[0] ] .= "params.$p = $p;";
					}
				}
				$clases[ $arr[0] ] .= "\n   return (new divAjaxMapping()).call(this.__server, '{$arr[0]}::{$arr[1]}',params);\n}\n";
				$j ++;
			}
			else
			{

				echo $j ++ > 0 ? ", " : "";

				if($m['params_complex'] == true)
				{
					echo ($namespace !== false ? "'$namespace'" : "$key") . ": function(params){\n";
				}
				else
				{
					echo ($namespace !== false ? "'$namespace'" : "$key") . ": function(";

					$i = 0;
					foreach($m['params'] as $p)
					{
						echo $i ++ > 0 ? "," : "";
						echo "$p";
					}

					echo ($i > 0 ? ", " : "") . "async){\n   var params = {};";
					foreach($m['params'] as $p)
					{
						echo "params.$p = $p;";
					}
				}
				echo "\n    return (new divAjaxMapping()).call(this.__server, '$key',params);\n }\n";
			}
		}
		$i    = 0;
		$data = $this->data;
		$total_clases = count($clases);
		foreach($clases as $key => $c)
		{
			echo $i == 0 && $j > $total_clases ? "," : "";
			echo $i ++ > 0 ? "," : "";
			echo "$c";
			if(isset($data[ $key ]))
			{
				$js = json_encode($data[ $key ]);
				if(substr($js, 0, 1) == "{")
				{
					$js = substr($js, 1, strlen($js) - 2);
				}
				echo ",$js}";
				unset($data[ $key ]);
			}
			else
				echo "}";
		}

		$k = 0;
		foreach($data as $var => $value)
		{
			echo $k == 0 && $i > 0 ? ",\n" : "";
			echo $k ++ > 0 ? ",\n" : "";
			echo "\"" . $var . "\": " . json_encode($value) . "";
		}

		echo "\n}";
	}

	/**
	 * Execute a method
	 *
	 * @param string $method
	 *
	 * @return mixed
	 */
	private function execute($method)
	{
		if( ! isset($this->methods[ $method ])) return DIV_AJAX_MAPPING_METHOD_NOT_EXISTS;

		// Execute hook before
		$this->_before($method);

		// Execute method
		$result = null;

		if(isset($this->methods[ $method ]))
		{
			$method = $this->methods[ $method ];

			ob_start();
			if(isset($method['include']))
			{
				$include = $method['include'];
				if( ! is_null($include))
				{
					if(is_string($include))
					{
						include_once($include);
					}
					elseif(is_array($include)) foreach($include as $inc) include_once $inc;
				}
			}
			ob_end_clean();

			$instruction = "{$method['name']}(";

			if( ! is_callable($method['name']))
			{
				if(method_exists($this, $method['name']))
				{
					$instruction = "\$this->{$method['name']}(";
				}
			}

			$i = 0;
			foreach($method['params'] as $p)
			{
				$instruction .= $i ++ > 0 ? ", " : "";
				if(isset($_POST[ $p ]))
				{
					$instruction .= 'unserialize($_POST["' . $p . '"])';
				}
				else
				{
					$instruction .= "null";
				}
			}
			$instruction .= ");";
			eval('$result = ' . $instruction);
		}

		// Execute hook after
		$this->_after($method);

		echo self::jsonEncode($result);
	}

	/**
	 * Secure is_string
	 *
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	final static function isString($value)
	{
		if(is_string($value)) return true;
		if(is_object($value)) if(method_exists($value, "__toString")) return true;

		return false;
	}

	/**
	 * JSON Encode
	 *
	 * @param mixed $data
	 *
	 * @return string
	 */
	final static function jsonEncode($data)
	{
		if(is_array($data) || is_object($data))
		{
			$islist = is_array($data) && (empty($data) || array_keys($data) === range(0, count($data) - 1));

			if($islist) $json = '[' . implode(',', array_map('self::jsonEncode', $data)) . ']';
			else
			{
				$items = [];
				foreach($data as $key => $value)
				{
					$items[] = self::jsonEncode("$key") . ':' . self::jsonEncode($value);
				}
				$json = '{' . implode(',', $items) . '}';
			}
		}
		elseif(self::isString($data))
		{
			$string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
			$json   = '';
			$len    = strlen($string);
			for($i = 0; $i < $len; $i ++)
			{
				$char = $string[ $i ];
				$c1   = ord($char);
				if($c1 < 128)
				{
					$json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
					continue;
				}
				$c2 = ord($string[ ++ $i ]);
				if(($c1 & 32) === 0)
				{
					$json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
					continue;
				}
				$c3 = ord($string[ ++ $i ]);
				if(($c1 & 16) === 0)
				{
					$json .= sprintf("\\u%04x", (($c1 - 224) << 12) + (($c2 - 128) << 6) + ($c3 - 128));
					continue;
				}
				$c4 = ord($string[ ++ $i ]);
				if(($c1 & 8) === 0)
				{
					$u = (($c1 & 15) << 2) + (($c2 >> 4) & 3) - 1;

					$w1   = (54 << 10) + ($u << 6) + (($c2 & 15) << 2) + (($c3 >> 4) & 3);
					$w2   = (55 << 10) + (($c3 & 15) << 6) + ($c4 - 128);
					$json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
				}
			}
		}
		else
			$json = strtolower(var_export($data, true));

		return $json;
	}

	/**
	 * Login, logout, publish methods or execute a method
	 *
	 * @return boolean
	 */
	public function go()
	{

		// Client need login?
		if(isset($_GET['login']))
		{
			if(isset($_GET['password']))
			{
				$r = self::login($_GET['login'], $_GET['password']);
				$r = $r === true ? DIV_AJAX_MAPPING_LOGIN_SUCCESSFUL : DIV_AJAX_MAPPING_LOGIN_FAILED;
				echo $r;

				return $r;
			}
		}

		// Client need logout?
		if(isset($_GET['logout']))
		{
			self::logout();

			return DIV_AJAX_MAPPING_LOGOUT_SUCCESSFUL;
		}

		// Client need execute a specific method?
		if(isset($_GET['execute']))
		{
			$method = $_GET['execute'];

			if( ! isset($this->methods[ $method ]))
			{
				return DIV_AJAX_MAPPING_METHOD_NOT_EXISTS;
			}

			// Check host
			$ip    = self::getClientIPAddress();
			$hosts = $this->methods[ $method ]['hosts'];

			foreach($hosts as $host)
			{
				$from = $host['from'];
				$to   = $hots['to'];
				$v    = self::checkRangeIP($from, $to, $ip);
				if($v === false)
				{
					echo "DIV_AJAX_MAPPING_ACCESS_DENIED_HOST";

					return DIV_AJAX_MAPPING_ACCESS_DENIED_HOST;
				}
			}

			$namespace = '';
			if(isset($this->methods[ $method ]['namespace'])) $namespace = $this->methods[ $method ]['namespace'];

			$r = self::checkMethodAccess($method, $namespace);

			if( ! $this->methods[ $method ]['security'] || $r)
			{
				$this->execute($method);

				return DIV_AJAX_MAPPING_METHOD_EXECUTED;
			}
			echo "DIV_AJAX_MAPPING_ACCESS_DENIED_USER";

			return DIV_AJAX_MAPPING_ACCESS_DENIED_USER;
		}

		// Then client need publish!

		$this->publish();

		return DIV_AJAX_MAPPING_METHOD_EXECUTED;
	}

	/**
	 * Begin a session on server
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @return boolean
	 */
	public function login($username, $password)
	{
		return true;
	}

	/**
	 * Close session
	 */
	public function logout()
	{
		return true;
	}

	/**
	 * Verify authentication
	 *
	 * @return boolean
	 */
	public function verifyAuth()
	{
		return true;
	}

	/**
	 * Check if user can access to specific method
	 *
	 * @param string $method
	 *
	 * @return boolean
	 */
	public function checkMethodAccess($method, $namespace = '')
	{
		return true;
	}

	public function register()
	{
		return true;
	}

	/**
	 * Before hook
	 *
	 * @param string $method
	 *
	 * @return boolean
	 */
	public function _before($method)
	{
		return true;
	}

	/**
	 * After hook
	 *
	 * @param string $method
	 *
	 * @return boolean
	 */
	public function _after($method)
	{
		return true;
	}

	/**
	 * Get the IP address of client
	 *
	 * @return string
	 */
	final static function getClientIPAddress()
	{
		if( ! isset($_SERVER))
		{
			$_SERVER = $HTTP_SERVER_VARS;
		}
		$ip = '127.0.0.1';
		if(isset($_SERVER['REMOTE_ADDR']))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if(isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']))
		{
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
	 *
	 * @return boolean
	 */
	final static function checkRangeIP($from, $to, $ip)
	{
		$from = ip2long($from);
		$to   = ip2long($to);
		$ip   = ip2long($ip);

		return $ip >= $from && $ip <= to;
	}
}