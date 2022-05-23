<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt\Interfaces;

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Интерфейс модели настроек
 *
 * @package     DeftCMS
 * @author	    b1tc0re
 * @copyright   2018-2022 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9a
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

    /**
     * Return the lifetime of one warning in seconds
     * Вернуть время жизни одного предупреждения в секундах
     *
     * @return int
     */
    public function getExpireTime() : int;
}