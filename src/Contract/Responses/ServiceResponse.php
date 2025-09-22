<?php

namespace App\Contract\Responses;

use App\Contract\Interfaces\ServiceResponseInterface;

class ServiceResponse implements ServiceResponseInterface
{
    private int $http_code;

    function setHttpCode(int $http_code): self
    {
        $this->http_code = $http_code;
        return $this;
    }

    /**
     * Setter of response service
     *
     * @param $result
     * @param string $message
     * @param bool $status
     */
    public function __construct(public $result, public string $message, public bool $status = true)
    {
    }

    public function status(): bool
    {
        return $this->status;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function result()
    {
        return $this->result;
    }

    public function httpCode(): int
    {
        return $this->http_code;
    }

    public function ok(): bool
    {
        return $this->status == 200;
    }

    public function fail(): bool
    {
        return $this->status != 200;
    }
}
