<?php  namespace DeftCMS\Components\b1tc0re\Security\CrossRequest\Interfaces;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Interface ICrossRequestForgery
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2022, DeftCMS (http://deftcms.ru/)
 * @since	    Version 0.0.9a
 */
interface ICrossRequestForgery
{

    /**
     * Inject token to form if exist
     * @use Create hook for display_override and call this method CrossRequestForgery::getInstance()->injectToken()
     *
     */
    public function injectToken();

    /**
     * Validate token
     * @use Create hook for post_controller_constructor and call this method CrossRequestForgery::getInstance()->validateToken()
     */
    public function validateToken();

    /**
     * Generate token and set cookie if need
     * @use Create hook for post_controller_constructor and call this method CrossRequestForgery::getInstance()->generateToken()
     * @return void
     */
    public function generateToken();
}