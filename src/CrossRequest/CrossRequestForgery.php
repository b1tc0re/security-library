<?php  namespace DeftCMS\Components\b1tc0re\Security\CrossRequest;

use DeftCMS\Components\b1tc0re\Security\CrossRequest\Interfaces\ICrossRequestForgery;
use DeftCMS\Core\RequestNotify;
use DeftCMS\Engine;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Зашита от межсайтовой атак
 * Cross site protection
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2022, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
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
        $this->headerName       = Engine::$DT->config->item('csrf_header_name');
        $this->tokenName        = Engine::$DT->config->item('csrf_token_name');
        $this->cookieName       = Engine::$DT->config->item('csrf_cookie_name');
        $this->expire           = Engine::$DT->config->item('csrf_expire');
        $this->encryptionKey    = Engine::$DT->config->item('encryption_key');
    }

    /**
     * Ввести токен в форму, если существует
     * Inject token to form if exist
     *
     * @use Создайте ловушку для display_override и вызовите этот метод CrossRequestForgery::getInstance()->injectToken()
     */
    public function injectToken()
    {
        $output = Engine::$DT->output->get_output();

        $token = xss_clean(self::$token);

        // Inject into form
        $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
            '$0<input type="hidden" name="' . self::getInstance()->tokenName . '" value="' . $token . '">',
            $output);

        // Inject into <head>
        $output = preg_replace('/(<\/head>)/',
            '<meta name="' . self::getInstance()->headerName . '" content="' . $token . '">' . '$0',
            $output);


        Engine::$DT->output->_display($output);
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
        if (Engine::$DT->input->method(true) === 'POST')
        {
            // Is the token field set and valid?
            $posted_token = Engine::$DT->input->post($this->tokenName);
            $header_token = Engine::$DT->input->get_request_header($this->headerName);
            $cookie_token = Engine::$DT->input->cookie($this->cookieName);

            if( $posted_token !== null && $posted_token === $cookie_token ) {
                return;
            }

            if( $header_token !== null && $header_token === $cookie_token ) {
                return;
            }

            Engine::$DT->load->library('validation');

            if( Engine::$DT->validation->hasRequest() ) {

                Engine::$DT->validation->setData('cross_request_forgery', true);
                Engine::$DT->validation->setData('task', [
                    'updateToken' => $cookie_token
                ]);
                RequestNotify::getInstance()->messages(
                    __('Language::Error::cross_request_forgery'),
                    \DeftCMS\Core\RequestNotify::ERROR
                );

                Engine::$DT->validation->output();
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
     * @return void
     */
    public function generateToken()
    {
        if (Engine::$DT->input->cookie($this->cookieName) === NULL)
        {
            // Generate a token and store it on session, since old one appears to have expired.
            self::$token = md5(uniqid('', true) . microtime() . mt_rand() . $this->encryptionKey);

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
            self::$token = Engine::$DT->input->cookie($this->cookieName, true);

            if( !ctype_digit(self::$token) && !preg_match('/^[a-f0-9]{32}$/i', self::$token) )
            {
                Engine::$DT->input->set_cookie([
                    'name'      => $this->cookieName,
                    'value'     => '',
                    'httponly'  => true,
                    'expire'    => -1
                ]);

                return $this->generateToken();
            }
        }

        Engine::$DT->template->setData('crossRequest', [
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