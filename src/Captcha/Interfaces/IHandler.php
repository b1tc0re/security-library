<?php namespace DeftCMS\Components\b1tc0re\Security\Captcha\Interfaces;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Интерфейс обработчика капчи
 * Captcha handler interface
 *
 * @package	    DeftCMS
 * @category    Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2020, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9
 */
interface IHandler
{
    /**
     * Проверка капчи
     * Captcha check
     *
     * @param string $value
     * @param string $ip_address
     *
     * @return bool
     */
    public function validate(string $value, string $ip_address = null);

    /**
     * Сгенерировать изоброжение капчи
     * Generate captcha image
     *
     * @param array $params
     */
    public function getCaptcha(array $params);

    /**
     * Получить html код для вывода в шаблон
     * Get html code for output to template
     *
     *
     * @return string
     */
    public function getCaptchaTemplate();

    /**
     * Вернуть название обработчика
     * Return handler name
     *
     * @return string
     */
    public function getName();
}