<?php
namespace cyclonephp\http;

use cyclonephp\http\Request;

class RequestBuilder {

    private $uri;

    private $method = Request::METHOD_GET;

    private $headers = array();

    private $queryParams = array();

    private $bodyParams = array();

    private $clientIp;

    /**
     * @return Request
     */
    public function build() {
        return new Request($this->uri, $this->method, $this->headers, $this->queryString(), $this->rawBody(), $this->clientIp);
    }

    /**
     * @param $uri string
     * @return $this
     */
    public function uri($uri) {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param $method string
     * @return $this
     */
    public function method($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function headers(array $headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param $key string
     * @param $value string
     * @return $this
     */
    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * @param array $queryParams
     * @return $this
     */
    public function queryParams(array $queryParams) {
        $this->queryParams = $queryParams;
        return $this;
    }

    /**
     * @param $key string
     * @param $value string
     * @return $this
     */
    public function addQueryParam($key, $value) {
        $this->queryParams[$key] = $value;
        return $this;
    }

    /**
     * @param array $bodyParams
     * @return $this
     */
    public function bodyParams(array $bodyParams) {
        $this->bodyParams = $bodyParams;
        return $this;
    }

    /**
     * @param $key string
     * @param $value string
     * @return $this
     */
    public function addBodyParam($key, $value) {
        $this->bodyParams[$key] = $value;
        return $this;
    }

    /**
     * @param $clientIp string
     * @return $this
     */
    public function clientIp($clientIp) {
        $this->clientIp = $clientIp;
        return $this;
    }

    public function queryString() {
        return http_build_query($this->queryParams);
    }

    public function rawBody() {
        return http_build_query($this->bodyParams);
    }

}