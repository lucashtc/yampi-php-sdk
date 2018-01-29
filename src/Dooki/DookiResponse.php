<?php

namespace Dooki;

use Dooki\DookiRequestException;
use Dooki\DookiResponsePagination;

class DookiResponse extends DookiResponsePagination
{
    protected $request = array();

    private $response = array();

    private $data = array();

    /**
     * Sets Dooki's entire request.
     *
     * @param array $request
     */
    private function setRequest(array $request)
    {
        $this->request = $request;
    }

    /**
     * Sets Dooki's entire response.
     *
     * @param array $response
     */
    private function setResponse(array $response)
    {
        $this->response = $response;
    }

    /**
     * Sets Dooki data key's data.
     *
     * @param array $data
     */
    private function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * DookiResponse constructor.
     *
     * @param string $response
     */
    public function __construct(array $request, $response)
    {
        $this->setRequest($request);

        $response = json_decode($response, true);

        $this->setResponse($response);

        if (isset($response['data'])) {
            $this->setData($response['data']);
        }

        if (isset($response['meta']['pagination'])) {
            parent::__construct($response['meta']['pagination']);
        }
    }

    /**
     * Get Dooki response's HTTP status code.
     * 
     * @return integer
     */
    public function getStatusCode()
    {
        if (isset($this->response['status_code'])) {
            return $this->response['status_code'];
        }

        return 0;
    }

    /**
     * Gets Dooki's entire response.
     *
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Gets Dooki data key's data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}