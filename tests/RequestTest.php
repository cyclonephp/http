<?php
namespace cyclonephp\http;

class RequestTest extends \PHPUnit_Framework_TestCase {
	
	public function uriKeyProvider() {
		return [
			['PATH_INFO'],
			['REQUEST_URI'],
			['PHP_SELF']
		];
	}
	
	
	/**
	 * @dataProvider uriKeyProvider
	 */
	public function testURIDetection($key) {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER[$key] = '/mypath';
		$actual = Request::initial();
		$this->assertEquals('/mypath', $actual->uri());
		unset($_SERVER[$key]);
	}
	
	public function testMethodDetection() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$actual = Request::initial();
		$this->assertEquals(Request::METHOD_GET, $actual->method());
	}
	
	public function testHeaders() {
		$_SERVER['HTTP_HOST'] = 'myhost';
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US';
		$_SERVER['HTTP_USER_AGENT'] = 'My Browser';
		$actual = Request::initial();
		$expectedHeaders = array(
			'Host' => 'myhost',
			'Accept-Language' => 'en-US',
			'User-Agent' => 'My Browser'
		);
		$this->assertEquals($expectedHeaders, $actual->headers());
	}
	
	public function testQueryString() {
		$_SERVER['QUERY_STRING'] = 'a=b%20c&d=e';
		$actual = Request::initial();
		$this->assertEquals('a=b%20c&d=e', $actual->queryString());
		$expectedQueryParams = array(
			'a' => 'b c',
			'd' => 'e'
		);
		$this->assertEquals($expectedQueryParams, $actual->queryParams());
	}
	
	public function clientIpKeyProvider() {
		return [
			['HTTP_X_FORWARDED_FOR'],
			['HTTP_CLIENT_IP'],
			['REMOTE_ADDR']
		];
	}
	
	
	/**
	 * @dataProvider clientIpKeyProvider
	 */
	public function testClientIp($key) {
		$ip = '1.2.3.4';
		$_SERVER[$key] = $ip;
		$actual = Request::initial();
		$this->assertEquals($ip, $actual->clientIp());
		unset($_SERVER[$key]);
	}
	
}
