<?php namespace DeftCMS\Components\b1tc0re\Security\Captcha;

use DeftCMS\Components\b1tc0re\Security\Captcha\Handlers\ReCaptchaV2;
use DeftCMS\Components\b1tc0re\Security\Captcha\Handlers\SystemCaptcha;
use DeftCMS\Components\b1tc0re\Security\Captcha\Interfaces\IHandler;
use DeftCMS\Core\FactoryHandlers\Exceptions\ExceptionFactory;
use DeftCMS\Core\FactoryHandlers\Factory;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Драйвер для работы с капчей
 * Captcha driver
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2023, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
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
        'recaptchav2'      => ReCaptchaV2::class
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
     * @param string|null $handlerName
     * @param array $validHandlers
     * @return IHandler
     */
    public static function getHandler(string $handlerName = null, array $validHandlers = [] )
    {
        self::$handler = fn_get_module_config('captcha')['handler'];
        return parent::getHandler($handlerName ?? self::$handler, self::$validHandlers);
    }

    /**
     * Получить список обработчиков
     * @return array
     */
    public static function getHandlers()
    {
        return self::$validHandlers;
    }
}