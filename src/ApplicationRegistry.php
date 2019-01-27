<?php

namespace rsavinkov\SmsSender;


class ApplicationRegistry
{
    private const CONFIG_MESSAGE_BIRD_API_KEY = 'MESSAGE_BIRD_API_KEY';
    private const CONFIG_ENVIRONMENT = 'ENVIRONMENT';

    public const ENVIRONMENT_PROD = 'prod';
    public const ENVIRONMENT_DEV = 'dev';

    private static $instance;
    private $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function instance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self(include('../config/config.php'));
        }

        return self::$instance;
    }

    public function getMessageBirdApiKey(): string
    {
        return $this->config[self::CONFIG_MESSAGE_BIRD_API_KEY];
    }

    public function getEnvironment(): string
    {
        return $this->config[self::CONFIG_ENVIRONMENT];
    }
}
