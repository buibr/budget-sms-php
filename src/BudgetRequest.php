<?php

namespace buibr\Budget;

use buibr\Budget\Exceptions\EnvironmentException;
use buibr\Budget\Exceptions\InvalidRequestException;


/**
 * Author:      Burhan Ibraimi <mail@buib.me>
 *
 * Class:       BudgetRequest
 * Created:     Thu Apr 04 2019 7:39:45 PM
 *
 **/
class BudgetRequest
{
    /**
     * For how long the request will be waited.
     */
    const DEFAULT_TIMEOUT = 60;
    
    /**
     * private
     */
    private $curl;
    
    /**
     *
     */
    private $curlOpt = [];
    
    /**
     *
     */
    private $schema;
    
    /**
     *
     */
    private $headers;
    
    /**
     *
     */
    private $params;
    
    /**
     *
     */
    private $method;
    
    /**
     *
     */
    private $body;
    
    /**
     * Endpoing url
     */
    private $url;
    
    /**
     * @var
     */
    private $timeout;
    
    /**
     * BudgetRequest constructor.
     *
     * @param string|null $url     - endpoint of the request.
     * @param string|null $method  - post, get, put, head
     * @param array       $headers - request headers.
     * @param array       $params  - query params.
     * @param null        $body
     * @param int         $timeout
     */
    public function __construct(
        string $url = NULL,
        string $method = NULL,
        array $headers = [],
        array $params = [],
        $body = NULL,
        int $timeout = self::DEFAULT_TIMEOUT
    )
    {
        $this->url = $url;
        $this->setHeaders($headers);
        $this->setParams($params);  // query params
        $this->setMethod($method);
        $this->setBody($body);
        $this->timeout = $timeout;
        
        return $this;
    }
    
    /**
     * @param array $headers
     */
    public function setHeaders(array $headers = [])
    {
        if (!isset($this->curlOpt[CURLOPT_HTTPHEADER])) {
            $this->curlOpt[CURLOPT_HTTPHEADER] = [];
        }
        
        foreach ($headers as $key => $value) {
            $this->curlOpt[CURLOPT_HTTPHEADER][] = "$key: $value";
        }
    }
    
    /**
     * @param array $p
     *
     * @return string
     */
    public function setParams(array $p = [])
    {
        $this->params = [];
        
        foreach ($p as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $this->params[] = urlencode((string) $key) . '=' . urlencode((string) $item);
                }
            } else {
                $this->params[] = urlencode((string) $key) . '=' . urlencode((string) $value);
            }
        }
        
        if (!empty($this->params)) {
            $this->url .= "?";
        }
        
        return $this->params = implode('&', $this->params);
    }
    
    /**
     * @param null $method
     */
    public function setMethod($method = NULL)
    {
        if (is_null($method)) {
            $this->method = 'get';
        }
        
        $this->method = strtolower(trim($method));
    }
    
    /**
     * @param $data
     *
     * @return string
     */
    public function setBody($data)
    {
        $this->body = [];
        
        $data = $data ?: [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $this->body[] = urlencode((string) $key) . '=' . urlencode((string) $item);
                }
            } else {
                $this->body[] = urlencode((string) $key) . '=' . urlencode((string) $value);
            }
        }
        
        return $this->body = implode('&', $this->body);
    }
    
    /**
     * @param int $timeout
     *
     * @return array
     * @throws \buibr\Budget\Exceptions\EnvironmentException
     * @throws \buibr\Budget\Exceptions\InvalidRequestException
     */
    public function request($timeout = self::DEFAULT_TIMEOUT)
    {
        $this->validate();
        
        //  prepare options for curl
        $this->setOptions();
        
        try {
            
            if (!$this->curl = curl_init()) {
                throw new EnvironmentException('Unable to initialize cURL');
            }
            
            if (!curl_setopt_array($this->curl, $this->curlOpt)) {
                throw new EnvironmentException(curl_error($this->curl));
            }
            
            if (!$response = curl_exec($this->curl)) {
                throw new EnvironmentException(curl_error($this->curl));
            }
            
            return ['content' => $response, 'curl' => $this->curl];
            
        } catch (\ErrorException $e) {
            
            if (isset($this->curl) && is_resource($this->curl)) {
                curl_close($this->curl);
            }
            
            throw $e;
        }
        
    }
    
    /**
     * Validate request
     * @throws InvalidRequestException
     */
    protected function validate()
    {
        if (!\filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new InvalidRequestException("Not valid url");
        }
        
        if (empty($this->method)) {
            throw new InvalidRequestException("Method is not set.");
        }
    }
    
    /**
     * @return array
     * @throws \buibr\Budget\Exceptions\EnvironmentException
     */
    private function setOptions()
    {
        // static binding
        $this->curlOpt[CURLOPT_URL] = $this->url . $this->params;
        $this->curlOpt[CURLOPT_HEADER] = TRUE;
        $this->curlOpt[CURLOPT_RETURNTRANSFER] = TRUE;
        $this->curlOpt[CURLOPT_INFILESIZE] = NULL;
        $this->curlOpt[CURLOPT_TIMEOUT] = $this->timeout;
        
        switch (strtolower(trim($this->method))) {
            case 'get':
                $this->curlOpt[CURLOPT_HTTPGET] = TRUE;
                break;
            case 'post':
                $this->curlOpt[CURLOPT_POST] = TRUE;
                $this->curlOpt[CURLOPT_POSTFIELDS] = $this->body;
                break;
            case 'put':
                $this->curlOpt[CURLOPT_PUT] = TRUE;
                $this->putBlob();
                break;
            case 'head':
                $this->curlOpt[CURLOPT_NOBODY] = TRUE;
                break;
            default:
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = strtoupper($this->method);
        }
        
        return $this->curlOpt;
    }
    
    /**
     * @throws \buibr\Budget\Exceptions\EnvironmentException
     */
    private function putBlob()
    {
        $buffer = fopen('php://memory', 'w+');
        
        if (!$buffer) {
            throw new EnvironmentException('Unable to open a temporary file');
        }
        
        fwrite($buffer, $this->body);
        fseek($buffer, 0);
        $this->curlOpt[CURLOPT_INFILE] = $buffer;
        $this->curlOpt[CURLOPT_INFILESIZE] = strlen($this->body);
    }
    
    /**
     * Define the endpoint url;
     *
     * @param null $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}