<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt\Interfaces;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Интерфейс модели хранение предупреждении
 *
 * @package     DeftCMS
 * @author	    b1tc0re
 * @copyright   2018-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
interface ILogicalModel
{

    /**
     * Count current attempt by identity for attempt name
     *
     * @param string $identity User identity
     * @param string $name Attempt name
     * @return int
     */
    public function getAttempts($identity, $name) : int;

    /**
     * Increase attempt
     *
     * @param string $identity User identity
     * @param string $name Attempt name
     * @return int
     */
    public function increaseAttempt($identity, $name) : int;

    /**
     * Cleaning attempts
     *
     * @param string $identity User identity
     * @param string $name Attempt name
     * @return void
     */
    public function cleaningAttempt($identity, $name);

    /**
     * Clearing old attempts
     *
     * @param string $identity User identity
     * @param int $expire Expire time
     * @return void
     */
    public function clearingOverdue($identity, $expire);
}