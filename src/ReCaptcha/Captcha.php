<?php namespace DeftCMS\Components\b1tc0re\Security\ReCaptcha;

use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Verify Google ReCaptcha V2 Response
 *
 * @package	    DeftCMS
 * @category	Model
 * @author	    b1tc0re
 * @copyright   (c) 2018-2019, DeftCMS (http://deftcms.org)
 * @since	    Version 0.0.1
 */

class Captcha
{
    /**
     * Captcha client request
     * @var CaptchaClient
     */
    protected $client;

    /**
     * Secret key
     * @var string
     */
    protected $secret;

    /**
     * Captcha constructor.
     */
    public function __construct()
    {
        $this->secret = Engine::$DT->config->item('settings')['captcha']['recaptcha_secret'];
        $this->client = new CaptchaClient();
    }

    /**
     * Verify captcha response
     * @param string $token
     * @return bool|array
     */
    public function verify($token)
    {
        try
        {
            $response = $this->client->siteVerify([
                'secret'    => $this->secret,
                'response'  => $token,
                'remoteip'  => Engine::$DT->input->ip_address()
            ]);
        }
        catch (\GuzzleHttp\Exception\GuzzleException $ex) {
            return false;
        }

        if( $response['success'] !== false ) {
            return true;
        }

        return $response['error-codes'];
    }
}