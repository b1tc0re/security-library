<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt\Interfaces;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Интерфейс модели хранение предупреждении
 *
 * @package     DeftCMS
 * @author	    b1tc0re
 * @copyright   2018-2022 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9a
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
     * @param string $name Attempt name
     * @param int $expire Expire time
     * @return void
     */
    public function clearingOverdue($name, $expire);

    /**
     * Получить время через которое истечет первое предупреждение
     * Get the time after which the first warning expires
     *
     * @param string $identity
     * @param string $name
     * @return int
     */
    public function getExcessTime($identity, $name);
}