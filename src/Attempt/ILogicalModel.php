<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface AttemptsType
 * @package DeftCMS\Components\b1tc0re\Security\Attempt
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
}