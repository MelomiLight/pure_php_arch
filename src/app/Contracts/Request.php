<?php

namespace app\Contracts;

use app\Validations\RequestValidator;
use helpers\HttpHelpers;

class Request
{
    private $validator;
    private $data;
    private $user;

    public function __construct()
    {
        $this->data = $this->parseInput();

        if(isset($_SERVER['user'])){
            $this->user = $_SERVER['user'];
        }
        $this->validator = new RequestValidator();
        if ($this->errors()) {
            HttpHelpers::responseJson(['errors' => $this->errors()], 400);
        }
    }

    public function errors()
    {
        return $this->validator->validate($this->data, $this->rules());
    }

    public function rules()
    {
        return [];
    }

    protected function parseInput(): array
    {
        $data = $_REQUEST;

        if ($this->isJsonRequest()) {
            $jsonInput = file_get_contents('php://input');
            $jsonData = json_decode($jsonInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = array_merge($data, $jsonData);
            }
        }

        return $data;
    }

    protected function isJsonRequest(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function user()
    {
        return $this->user;
    }

    public function validated()
    {
        $keys = array_keys($this->rules());
        return array_intersect_key($this->data, array_flip($keys));
    }
}