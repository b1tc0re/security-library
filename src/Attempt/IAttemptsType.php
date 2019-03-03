<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface AttemptsType
 * @package DeftCMS\Components\b1tc0re\Security\Attempt
 */
interface IAttemptsType
{
    /**
     * Return the name of attempt type
     * Вернуть названия предуприждений
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Return the maximum number of warnings to block actions
     * Вернуть максимальное количество предупреждений для блокировки действий
     *
     * @return int
     */
    public function getExcessLocked() : int;

    /**
     * Return the maximum number of warnings for captcha output
     * Вернуть максимальное количество предупреждений для вывода капчи
     *
     * @return int
     */
    public function getExcessCaptcha() : int;
}