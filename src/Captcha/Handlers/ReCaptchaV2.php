<?php namespace DeftCMS\Components\b1tc0re\Security\Captcha\Handlers;

use DeftCMS\Components\b1tc0re\Security\Captcha\Clients\ReCaptchaClient;
use DeftCMS\Components\b1tc0re\Security\Captcha\Interfaces\IHandler;
use GuzzleHttp\Exception\GuzzleException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Обработчик для ReCaptcha V2
 * Handler for ReCaptcha V2
 *
 * @package	    DeftCMS
 * @category    Core
 * @author	    b1tc0re
 * @copyright   (c) 2018-2022, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
 */
class ReCaptchaV2 implements IHandler
{
    /**
     * Публичный ключ
     * Public key
     *
     * @var string
     */
    protected $publicKey;

    /**
     * Приватный ключ
     * Private key
     *
     * @var string
     */
    protected $privateKey;

    /**
     * HTTP клиент для взаимодействия с серверами google
     * HTTP client for interacting with google servers
     *
     *
     * @var ReCaptchaClient
     */
    protected $httpClient;

    /**
     * Коды ошибок
     * Error Codes
     *
     * @var array
     */
    protected $errorsResponse = [
        'invalid-keys'                      => 'The secret parameter is invalid.',
        'missing-input-secret'              => 'The secret parameter is missing.',
        'invalid-input-secret'              => 'The secret parameter is invalid or malformed.',
        'missing-input-response'            => 'The response parameter is missing.',
        'invalid-input-response'            => 'The response parameter is invalid or malformed.',
        'bad-request'                       => 'The request is invalid or malformed.',
        'timeout-or-duplicate'              => 'The response is no longer valid: either is too old or has been used previously.'
    ];

    /**
     * ReCaptchaV2 constructor.
     *
     * @throws \DeftCMS\Core\Exceptions\InvalidSettingsException
     */
    public function __construct()
    {
        // Global settings
        $params = fn_get_module_config('captcha')[strtolower($this->getName())];

        if( !array_key_exists('recaptcha_secret', $params) || !array_key_exists('recaptcha_sitekey', $params) )
        {
            throw new \DeftCMS\Core\Exceptions\InvalidSettingsException('Не найдены настройки google '. $this->getName());
        }

        $this->privateKey = $params['recaptcha_secret'];
        $this->publicKey  = $params['recaptcha_sitekey'];
        $this->httpClient = new ReCaptchaClient();
    }

    /**
     * Проверка капчи
     * Captcha check
     *
     * @param string $value
     * @param string|null $ip_address
     * @return bool
     */
    public function validate(string $value, string $ip_address = null)
    {
        try
        {
            $response = $this->httpClient->siteVerify([
                'secret'    => $this->privateKey,
                'response'  => $value,
                'remoteip'  => $ip_address || \DeftCMS\Engine::$DT->input->ip_address()
            ]);
        }
        catch (GuzzleException $ex)
        {
            \DeftCMS\Engine::$Log->critical(sprintf('ReCaptchaV2 request exception: %s', $ex->getMessage()));
            return false;
        }

        if( array_key_exists('error-codes', $response) && !empty($response['error-codes']) )
        {
            $response['error-codes'] = array_pop ($response['error-codes']);

            if( array_key_exists($response['error-codes'], $this->errorsResponse) )
            {
                \DeftCMS\Engine::$Log->critical('ReCaptchaV2 request error: [error_code]', [
                    '[error_code]' => $this->errorsResponse[$response['error-codes']]
                ]);
            }
            else
            {
                \DeftCMS\Engine::$Log->critical('ReCaptchaV2 request unknown error: [error_code]', [
                    '[error_code]' => $response['error-codes']
                ]);
            }

            return false;
        }

        return $response['success'];
    }

    /**
     * Сгенерировать изоброжение капчи
     * Generate captcha image
     *
     * @param array $params
     */
    public function getCaptcha(array $params)
    {
        // TODO: Implement getCaptcha() method.
    }

    /**
     * Получить html код для вывода в шаблон
     * Get html code for output to template
     *
     * @return string
     */
    public function getCaptchaTemplate()
    {
        $data = [
            'handler'  => $this->getName(),
            'sitekey' => $this->publicKey
        ];

        return \DeftCMS\Engine::$DT->template->renderLayer('captcha', [ 'captcha' => $data ], true);
    }

    /**
     * Вернуть название обработчика
     * Return handler name
     *
     * @return string
     */
    public function getName()
    {
        return 'reCaptchaV2';
    }

    /**
     * Получить параметры обработчика
     * Get handler parameters
     *
     * @return array
     */
    public function getParams()
    {
        return [
            'sitekey' => $this->publicKey
        ];
    }
}