<?php namespace DeftCMS\Components\b1tc0re\Security\Captcha;

use DeftCMS\Components\b1tc0re\Security\Captcha\Handlers\ReCaptchaV2;
use DeftCMS\Components\b1tc0re\Security\Captcha\Interfaces\IHandler;
use DeftCMS\Core\FactoryHandlers\Exceptions\ExceptionFactory;
use DeftCMS\Core\FactoryHandlers\Factory;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Драйвер для работы с капчей
 * Captcha driver
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2020, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class CaptchaFactory extends Factory
{
    /**
     * Действительные обработчики
     * Valid handlers
     *
     * @var array
     */
    protected static $validHandlers = [
        'system'           => SystemCaptcha::class,
        'reCaptchaV2'      => ReCaptchaV2::class
    ];

    /**
     * Обработчик по умолчанию
     * Default handler
     *
     * @var string
     */
    protected static $handler;

    /**
     * Создать обработчик на основе $handlerName
     * Create a handler based on $handlerName
     *
     * @param string $handlerName
     * @param array $validHandlers
     * @return IHandler
     */
    public static function getHandler(string $handlerName = null, array $validHandlers = [] )
    {
        self::$handler = \DeftCMS\Engine::$DT->config->item('settings')['captcha']['captcha_handler'] ?? 'system';
        return parent::getHandler($handlerName ?? self::$handler, self::$validHandlers);
    }
}