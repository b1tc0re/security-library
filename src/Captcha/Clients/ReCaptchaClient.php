<?php namespace DeftCMS\Components\b1tc0re\Security\Captcha\Clients;

use DeftCMS\Components\b1tc0re\Request\RequestClient;
use GuzzleHttp\Exception\GuzzleException;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Класс для реализации запросов к API Google ReCaptcha V2
 * Request client Google ReCaptcha V2
 *
 * @package	    DeftCMS
 * @category	Model
 * @author	    b1tc0re
 * @copyright   (c) 2018-2022, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
 */
class ReCaptchaClient extends RequestClient
{
    /**
     * Сервисный домен
     * Service domain
     *
     * @var string
     */
    protected $serviceDomain = 'www.google.com';

    /**
     * Проверка ответа пользователя
     * Verifying the user's response
     *
     * @param array $params Request parameters
     * @return array
     * @throws GuzzleException
     */
    public function siteVerify(array $params)
    {
        return $this->getServiceResponse('recaptcha/api/siteverify', 'POST', $params);
    }
}