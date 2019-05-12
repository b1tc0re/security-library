<?php namespace DeftCMS\Components\b1tc0re\Security\ReCaptcha;

use DeftCMS\Components\b1tc0re\Request\RequestClient;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Request client Google ReCaptcha V2
 *
 * @package	    DeftCMS
 * @category	Model
 * @author	    b1tc0re
 * @copyright   (c) 2018-2019, DeftCMS (http://deftcms.org)
 * @since	    Version 0.0.1
 */
class CaptchaClient extends RequestClient
{
    /**
     * Service domain
     * @var string
     */
    protected $serviceDomain = 'www.google.com';

    /**
     * Verifying the user's response
     *
     * @param array $params Request parameters
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function siteVerify(array $params)
    {
        return $this->getServiceResponse('recaptcha/api/siteverify', 'POST', $params);
    }
}