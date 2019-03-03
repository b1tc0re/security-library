<?php  namespace DeftCMS\Components\b1tc0re\Security\CrossRequest;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface ICrossRequestForgery
 * @package DeftCMS\Components\b1tc0re\Security\CrossRequest
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