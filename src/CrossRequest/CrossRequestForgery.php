<?php  namespace DeftCMS\Components\b1tc0re\Security\CrossRequest;

use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Зашита от межсайтовой атки
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2019, DeftCMS (http://deftcms.org)
 * @since	    Version 0.0.1
 */
class CrossRequestForgery implements ICrossRequestForgery
{

    /**
     * CrossRequestForgery instance
     *
     * @var CrossRequestForgery
     */
    protected static $_instance;

    /**
     * Название заголовка токена
     * @var string
     */
    protected $headerName = 'XHttp-Authorize';

    /**
     * Название токена
     * @var string
     */
    protected $tokenName  = '_token';

    /**
     * Имя файла cookie
     * @var string
     */
    protected $cookieName = '_ctid';

    /**
     * Количество секунд, в течение которых токен должен истекать.
     * @var int
     */
    protected $expire = 7200;

    /**
     * CMS encryption key
     * @var null|string
     */
    protected $encryptionKey = "12345678912345678912345678912345";

    /**
     * Token
     * @var string
     */
    protected static $token = '12345678912345678912345678912345';

    /**
     * Метод возврата статического экземпляра
     *
     * @uses $robots = CrossRequestForgery::getInstance();
     *
     * @return $this
     */
    public static function getInstance()
    {
        self::$_instance || (self::$_instance = new static());
        return self::$_instance;
    }

    /**
     * CrossRequestForgery constructor.
     */
    public function __construct()
    {
        // Initialize config
        $this->headerName       = Engine::$DT->config->item('csrf_header_name');
        $this->tokenName        = Engine::$DT->config->item('csrf_token_name');
        $this->cookieName       = Engine::$DT->config->item('csrf_cookie_name');
        $this->expire           = Engine::$DT->config->item('csrf_expire');
        $this->encryptionKey    = Engine::$DT->config->item('encryption_key');
    }

    /**
     * Inject token to form if exist
     * @use Create hook for display_override and call this method CrossRequestForgery::getInstance()->injectToken()
     *
     */
    public function injectToken()
    {
        $output = Engine::$DT->output->get_output();

        // Inject into form
        $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
            '$0<input type="hidden" name="' . self::getInstance()->tokenName . '" value="' . self::$token . '">',
            $output);

        // Inject into <head>
        $output = preg_replace('/(<\/head>)/',
            '<meta name="' . self::getInstance()->headerName . '" content="' . self::$token . '">' . '$0',
            $output);


        Engine::$DT->output->_display($output);
    }

    /**
     * Validate token
     * @use Create hook for post_controller_constructor and call this method CrossRequestForgery::getInstance()->validateToken()
     */
    public function validateToken()
    {
        // Is this a post request?
        if (Engine::$DT->input->method(true) === 'POST')
        {
            // Is the token field set and valid?
            $posted_token = Engine::$DT->input->post($this->tokenName);
            $header_token = Engine::$DT->input->get_request_header($this->headerName);

            if( $posted_token !== FALSE && $posted_token !=  Engine::$DT->input->cookie($this->cookieName) )
            {
                return;
            }

            if( $header_token !== FALSE && $header_token !=  Engine::$DT->input->cookie($this->cookieName) )
            {
                return;
            }

            if( Engine::$DT->validation->hasRequest() && property_exists(Engine::$DT, 'validation') )
            {
                Engine::$DT->validation->setData('cross_request_forgery', true);
                Engine::$DT->validation->output();
                exit;
            }

            fn_redirect('/error/cross_request_forgery/');
            return;
        }
    }

    /**
     * Generate token and set cookie if need
     * @use Create hook for post_controller_constructor and call this method CrossRequestForgery::getInstance()->generateToken()
     *
     */
    public function generateToken()
    {
        if (Engine::$DT->input->cookie($this->cookieName) === NULL)
        {
            // Generate a token and store it on session, since old one appears to have expired.
            self::$token = md5(uniqid() . microtime() . rand() . $this->encryptionKey);

            Engine::$DT->input->set_cookie([
                'name'      => $this->cookieName,
                'value'     => self::$token,
                'httponly'  => true,
                'expire'    => $this->expire
            ]);
        }
        else
        {
            // Set it to local variable for easy access
            self::$token = Engine::$DT->input->cookie($this->cookieName);
        }

        Engine::$DT->template->setData('crossRequest', [
            'name' => $this->tokenName,
            'value' => self::$token
        ]);
    }
}