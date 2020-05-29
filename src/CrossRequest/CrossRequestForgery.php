<?php  namespace DeftCMS\Components\b1tc0re\Security\CrossRequest;

use DeftCMS\Components\b1tc0re\Security\CrossRequest\Interfaces\ICrossRequestForgery;
use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Зашита от межсайтовой атак
 * Cross site protection
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2020, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class CrossRequestForgery implements ICrossRequestForgery
{
    /**
     * Экземпляр класса CrossRequestForgery
     * CrossRequestForgery instance
     *
     * @var CrossRequestForgery
     */
    protected static $_instance;

    /**
     * Название заголовка токена
     * Token Header Name
     *
     * @var string
     */
    protected $headerName = 'XHttp-Authorize';

    /**
     * Название токена используемый в запросе POST
     * Token name used in POST request
     *
     * @var string
     */
    protected $tokenName  = '_token';

    /**
     * Имя файла cookie
     * Cookie name
     *
     * @var string
     */
    protected $cookieName = '_ctid';

    /**
     * Количество секунд, в течение которых токен должен истекать.
     * The number of seconds the token should expire.
     *
     * @var int
     */
    protected $expire = 7200;

    /**
     * Ключ шифрования (автоматически заполняется из настроек системы)
     * Encryption Key (automatically populated from the system settings)
     *
     * @var string
     */
    protected $encryptionKey = "12345678912345678912345678912345";

    /**
     * Значение токена
     * Token value
     *
     * @var string
     */
    protected static $token = '12345678912345678912345678912345';

    /**
     * Метод возврата статического экземпляра
     * Static Instance Return Method
     *
     * @uses $robots = CrossRequestForgery::getInstance();
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
        $this->headerName       = \DeftCMS\Engine::$DT->config->item('csrf_header_name');
        $this->tokenName        = \DeftCMS\Engine::$DT->config->item('csrf_token_name');
        $this->cookieName       = \DeftCMS\Engine::$DT->config->item('csrf_cookie_name');
        $this->expire           = \DeftCMS\Engine::$DT->config->item('csrf_expire');
        $this->encryptionKey    = \DeftCMS\Engine::$DT->config->item('encryption_key');
    }

    /**
     * Ввести токен в форму, если существует
     * Inject token to form if exist
     *
     * @use Создайте ловушку для display_override и вызовите этот метод CrossRequestForgery::getInstance()->injectToken()
     */
    public function injectToken()
    {
        $output = \DeftCMS\Engine::$DT->output->get_output();

        // Inject into form
        $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
            '$0<input type="hidden" name="' . self::getInstance()->tokenName . '" value="' . self::$token . '">',
            $output);

        // Inject into <head>
        $output = preg_replace('/(<\/head>)/',
            '<meta name="' . self::getInstance()->headerName . '" content="' . self::$token . '">' . '$0',
            $output);


        \DeftCMS\Engine::$DT->output->_display($output);
    }

    /**
     * Проверить токена
     * Validate token
     *
     * @use Создайте ловушку для post_controller_constructor и вызовите этот метод CrossRequestForgery::getInstance()->validateToken()
     */
    public function validateToken()
    {
        // Is this a post request?
        if (\DeftCMS\Engine::$DT->input->method(true) === 'POST')
        {
            // Is the token field set and valid?
            $posted_token = \DeftCMS\Engine::$DT->input->post($this->tokenName);
            $header_token = \DeftCMS\Engine::$DT->input->get_request_header($this->headerName);

            if( $posted_token !== null && $posted_token == \DeftCMS\Engine::$DT->input->cookie($this->cookieName) ) {
                return;
            }

            if( $header_token !== null && $header_token ==  \DeftCMS\Engine::$DT->input->cookie($this->cookieName) ) {
                return;
            }

            Engine::$DT->load->library('validation');

            if( DeftCMS\Engine::$DT->validation->hasRequest() ) {

                DeftCMS\Engine::$DT->validation->setData('cross_request_forgery', true);
                DeftCMS\Engine::$DT->validation->output();
                exit;
            }

            fn_redirect('/error/cross_request_forgery/');
            return;
        }
    }

    /**
     * Сгенерируйте токен и установите куки, если это необходимо
     * Generate token and set cookie if need
     *
     * @use Создайте ловушку для post_controller_constructor и вызовите этот метод CrossRequestForgery::getInstance()->generateToken()
     */
    public function generateToken()
    {
        if (\DeftCMS\Engine::$DT->input->cookie($this->cookieName) === NULL)
        {
            // Generate a token and store it on session, since old one appears to have expired.
            self::$token = md5(uniqid() . microtime() . rand() . $this->encryptionKey);

            \DeftCMS\Engine::$DT->input->set_cookie([
                'name'      => $this->cookieName,
                'value'     => self::$token,
                'httponly'  => true,
                'expire'    => $this->expire
            ]);
        }
        else
        {
            // Set it to local variable for easy access
            self::$token = \DeftCMS\Engine::$DT->input->cookie($this->cookieName);
        }

        \DeftCMS\Engine::$DT->template->setData('crossRequest', [
            'name'  => $this->tokenName,
            'value' => self::$token
        ]);
    }

    /**
     * Получить данные о текушем токене
     * @return array
     */
    public function getValues()
    {
        return [
            'input'     => $this->tokenName,
            'header'    => $this->headerName,
            'value'     => self::$token
        ];
    }
}