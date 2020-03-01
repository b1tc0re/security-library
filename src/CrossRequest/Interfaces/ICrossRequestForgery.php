<?php  namespace DeftCMS\Components\b1tc0re\Security\CrossRequest\Interfaces;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface ICrossRequestForgery
 *
 * @package	    DeftCMS
 * @category	Library
 * @author	    b1tc0re
 * @copyright   (c) 2018-2020, DeftCMS (http://deftcms.org)
 * @since	    Version 0.0.9
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
     *
     */
    public function generateToken();
}