<?php
/*
 * This file is part of the Mouf core package.
 *
 * (c) 2012 David Negrier <david@mouf-php.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */
namespace Mouf\Reflection;

use Exception;

/**
 * Class specialized in forwarding a reflexion request to another script that will perform it.
 * It is useful to perform reflexion in a separate script because in another script, the 
 * context of the Mouf management is not loaded.
 *
 */
class MoufReflectionProxy {

	/**
	 * Returns a MoufXmlReflectionClass representing the class we are going to analyze.
	 *
	 * @param string $className
	 * @param boolean $selfEdit
	 * @return MoufXmlReflectionClass
	 */
	public static function getClass($className, $selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_class.php?class=".$className."&selfedit=".(($selfEdit)?"true":"false");

		$response = self::performRequest($url);
		
		return new MoufXmlReflectionClass($response);
	}
	
	/**
	 * Returns the complete list of all classes in a PHP array.
	 *
	 * @param boolean $selfEdit
	 * @return array
	 */
	public static function getAllClasses($selfEdit, $exportMode = MoufReflectionClass::EXPORT_ALL) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_all_classes.php?selfedit=".(($selfEdit)?"true":"false")."&export_mode=".$exportMode;
	
		$response = self::performRequest($url);
	
		$obj = @unserialize($response);
		
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".plainstring_to_htmlprotected($url, ENT_QUOTES)."'>".plainstring_to_htmlprotected($url, ENT_QUOTES)."</a>");
		}
		
		return $obj;
	}
	
	/**
	 * Returns a list of all the components that are of a class that extends or implements $baseClass
	 *
	 * @param string $baseClass The class or interface name.
	 * @return array<string>
	 */
	public static function getInstances($baseClass, $selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_instances.php?class=".$baseClass."&selfedit=".(($selfEdit)?"true":"false");
		
		$response = self::performRequest($url);

		$obj = @unserialize($response);
		
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".htmlspecialchars($url, ENT_QUOTES)."'>".htmlspecialchars($url, ENT_QUOTES)."</a>");
		}
		
		return $obj;
	}
	
	public static function getComponentsList($selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_components_list.php?selfedit=".(($selfEdit)?"true":"false");

		$response = self::performRequest($url);

		$obj = @unserialize($response);
		
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".htmlspecialchars($url, ENT_QUOTES)."'>".htmlspecialchars($url, ENT_QUOTES)."</a>");
		}
		
		return $obj;
		
	}
	
	public static function getEnhancedComponentsList($selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_enhanced_components_list.php?selfedit=".(($selfEdit)?"true":"false");

		$response = self::performRequest($url);

		$obj = @unserialize($response);
		
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".htmlspecialchars($url)."'>".htmlspecialchars($url)."</a>");
		}
		
		return $obj;
		
	}
	

	/**
	 * Returns the array of all constants defined in the config.php file at the root of the project. 
	 * 
	 * @return array
	 */
	public static function getConfigConstants($selfEdit) {

		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_defined_constants.php?selfedit=".(($selfEdit)?"true":"false");
		
		$response = self::performRequest($url);

		$obj = @unserialize($response);
		
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".plainstring_to_htmlprotected($url)."'>".plainstring_to_htmlprotected($url)."</a>");
		}
		
		return $obj;
	}
	
	/**
	 * Analyzes the include files.
	 * Returns an empty array if everything is fine, or an array like this if there is an error:
	 * 	array("errorType"=>"crash", "errorMsg"=>"txt");
	 * 
	 * errorTypes can be "crash" or "outputStarted" or "filedoesnotexist"
	 * 
	 * @param string $selfEdit
	 * @throws Exception
	 */
	public static function analyzeIncludes($selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/analyze_includes.php?selfedit=".(($selfEdit)?"true":"false");
		
		$response = self::performRequest($url);

		// Let's strip the invalid parts:
		$arr = explode("\nX4EVDX4SEVX548DSVDXCDSF489\n", $response);
		if (count($arr) < 2) {
			// No delimiter: there has been a crash.
			return array("errorType"=>"crash", "errorMsg"=>$response);
		}
		$msg = $arr[count($arr)-1]; 
		
		$obj = @unserialize($msg);
		
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".plainstring_to_htmlprotected($url)."'>".plainstring_to_htmlprotected($url)."</a>");
		}
		
		return $obj;
	}
	
	/**
	 * Analyzes the include files.
	 *
	 * @param string $selfEdit
	 * @throws Exception
	 */
	public static function analyzeIncludes2($selfEdit, $forbiddenClasses) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/analyze_includes_2.php";
		/*foreach ($forbiddenClasses as $forbiddenClass) {
			$url .= "&forbiddenClasses[]=".$forbiddenClass;
		}*/	
		
		$response = self::performRequest($url, array("selfedit"=>json_encode($selfEdit), "classMap"=>json_encode($forbiddenClasses)));
	
		// Let's strip the invalid parts:
		/*$arr = explode("\nX4EVDX4SEVX548DSVDXCDSF489\n", $response);
		if (count($arr) < 2) {
			// No delimiter: there has been a crash.
			return array("errorType"=>"crash", "errorMsg"=>$response);
		}
		$msg = $arr[count($arr)-1];
	
		$obj = unserialize($msg);
	
		if ($obj === false) {
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".plainstring_to_htmlprotected($url)."'>".plainstring_to_htmlprotected($url)."</a>");
		}
	
		return $obj;*/
		return $response;
	}
	
	/**
	 * Analyzes the include files.
	 *
	 * @param bool $selfEdit
	 * @throws Exception
	 */
	public static function getClassMap($selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/get_class_map.php?selfedit=".(($selfEdit)?"true":"false");
	
		$response = self::performRequest($url);
		
		$obj = @unserialize($response);
		
		if ($obj === false) {
			//echo $response;
			throw new Exception("Unable to unserialize message:\n".$response."\n<br/>URL in error: <a href='".\htmlspecialchars($url)."'>".\htmlspecialchars($url)."</a>");
		}
		
		return $obj;
	}
	
	/**
	 * Returns the "return" fully qualified class name from the code passed in parameter.
	 * 
	 * @param string $code
	 * @param bool $selfEdit
	 * @throws Exception
	 * @return string
	 */
	public static function getReturnTypeFromCode($code, $selfEdit) {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/return_type_from_code.php";

	
		$response = self::performRequest($url,
		[
			"selfedit"=>($selfEdit)?"true":"false",
			"code" => $code
		]);
	
		$obj = @unserialize($response);
	
		if ($obj === false) {
			throw new Exception($response);
		}
	
		return $obj["data"]["class"];
	}
	
	/**
	 * Checks if the CURL connection does work or not.
	 * In the case we are trying to access a remote server, if the server does not know its own hostname,
	 * the request will fail. This can happen if the DNS has been configured in the /etc/hosts file
	 * of the client and not on the server. 
	 * 
	 * @return bool
	 */
	public static function checkConnection() {
		$url = MoufReflectionProxy::getLocalUrlToProject()."src/direct/test_connection.php";
	
		$response = self::performRequest($url);
		
		if ($response == 'ok') {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Performs a request using CURL using GET or POST methods and returns the result.
	 *
	 * @param string $url
	 * @param array<string, string> $post POST parameters
	 * @throws \Exception
	 */
	public static function performRequest($url, $post = array()) {
		// preparation de l'envoi
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post, '', '&'));
		} else {
			curl_setopt($ch, CURLOPT_POST, false);
		}
			
		if (isset($_SERVER['HTTPS'])) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		}
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //Fixes the HTTP/1.1 417 Expectation Failed Bug
	
		// Let's forward all cookies so the session in preserved.
		// Problem: because the session file is locked, we cannot do that without closing the session first
		session_write_close();
	
		$cookieArr = array();
		foreach ($_COOKIE as $key=>$value) {
			$cookieArr[] = $key."=".urlencode($value);
		}
		$cookieStr = implode("; ", $cookieArr);
		curl_setopt($ch, CURLOPT_COOKIE, $cookieStr);
	
		$response = curl_exec($ch );
	
		// And let's reopen the session...
		session_start();
	
		if( curl_error($ch) ) {
			throw new \Exception("An error occured: ".curl_error($ch));
		}
		curl_close( $ch );
	
		return $response;
	}
	
	/**
	 * Performs a curl request, same as performRequest, but  without using the session nore the cookies... 
	 * @param string $url
	 * @param array<string, mixed> $post
	 * @throws \Exception
	 * @return mixed
	 */
	public static function performRequestWithoutSession($url, $post = array()) {
		// preparation de l'envoi
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		} else {
			curl_setopt($ch, CURLOPT_POST, false);
		}
			
		if (isset($_SERVER['HTTPS'])) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		}
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //Fixes the HTTP/1.1 417 Expectation Failed Bug
	
	
		$response = curl_exec($ch );
	
		if( curl_error($ch) ) {
			throw new \Exception("An error occured: ".curl_error($ch));
		}
		curl_close( $ch );
	
		return $response;
	}
	
	public static function getLocalUrlToProject(){
		if (defined('MOUF_URL')) {
			$moufUrl = MOUF_URL;
		} else {
			$moufUrl = ROOT_URL;
		}

		// Let's try to detect the HTTPS protocol.
		// It can be tricky because $_SERVER['HTTPS'] is not always set.
		// (see: http://stackoverflow.com/questions/452375/detecting-https-requests-in-php )
		if (isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
 				$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || $_SERVER['SERVER_PORT'] == 443) {
			$url = "https://".$_SERVER['SERVER_NAME'].$moufUrl;
		} else {
			$url = "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$moufUrl;
		}
		return $url;
	}
}
?>