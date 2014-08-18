<?php
namespace cyclonephp\http;

class Request {
	
	const METHOD_GET = 'GET';

    const METHOD_POST = 'POST';

    const METHOD_PUT = 'PUT';

    const METHOD_DELETE = 'DELETE';

    const METHOD_HEAD = 'HEAD';

    const METHOD_TRACE = 'TRACE';

    const METHOD_CONNECT = 'CONNECT';

    const METHOD_OPTIONS = 'OPTIONS';
    
    private static function createHeadersFromSuperglobals() {
		$rval = [];
		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'HTTP_') === 0) {
				$uppercaseHeader = substr($key, 5);
				$header = strtolower(str_replace('_', ' ', $uppercaseHeader));
				$header = ucwords($header);
				$header = str_replace(' ', '-', $header);
				$rval[$header] = $value;
			}
		}
		return $rval;
	}

    public static function builder() {
        return new RequestBuilder;
    }
    
    public static function initial() {
		if ( ! empty($_SERVER['PATH_INFO'])) {
            $uri = $_SERVER['PATH_INFO'];
        } else {
			if (isset($_SERVER['REQUEST_URI'])) {
                $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $uri = rawurldecode($uri);
            } elseif (isset($_SERVER['PHP_SELF'])) {
                $uri = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['REDIRECT_URL'])) {
                $uri = $_SERVER['REDIRECT_URL'];
            } else
                throw new \Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, or PHP_SELF');
		}
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
		$queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
		$clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
		return new Request($uri, $method, self::createHeadersFromSuperglobals(), $queryString, file_get_contents('php://input'), $clientIp);
	}
    
    private $uri;
    
    private $method;
    
    private $headers;
    
    private $rawBody;
    
    private $bodyParams;
    
    private $queryParams;
    
    private $queryString;
    
    private $clientIp;
    
    public function __construct($uri, $method, array $headers, $queryString, $rawBody = '', $clientIp = null) {
		$this->uri = $uri;
		$this->method = $method;
		$this->headers = $headers;
		$this->rawBody = $rawBody;
		$this->queryString = $queryString;
		$this->clientIp = $clientIp;
	}
	
	public function rawBody() {
		return $this->rawBody;
	}
	
	public function bodyParams() {
		if ($this->bodyParams === null) {
			parse_str($this->rawBody, $this->bodyParams);
		}
		return $this->bodyParams;
	}
	
	public function queryString() {
		return $this->queryString;
	}
	
	public function queryParams() {
		if ($this->queryParams === null) {
			parse_str($this->queryString, $this->queryParams);
		}
		return $this->queryParams;
	}
	
	public function uri() {
		return $this->uri;
	}
	
	public function method() {
		return $this->method;
	}
	
	public function headers() {
		return $this->headers;
	}
	
	public function clientIp() {
		if ($this->clientIp === null) {
			$headers = $this->headers();
			foreach (['X-Forwarded-For', 'Client-Ip'] as $candidateKey) {
				if (isset($headers[$candidateKey])) {
					return $this->clientIp = $headers[$candidateKey];
				}
			}
		}
		return $this->clientIp;
	}

}
