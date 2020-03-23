<?php  namespace DeftCMS\Components\b1tc0re\Security\Attempt;

use DeftCMS\Components\b1tc0re\Security\Attempt\Interfaces\IAttemptsType;
use DeftCMS\Components\b1tc0re\Security\Attempt\Interfaces\ILogicalModel;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Система предупреждений
 * Warning system
 *
 * @package     DeftCMS
 * @author	    b1tc0re
 * @copyright   2018-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class AttemptSystem
{
    /**
     * Идентификатор предупреждения
     * @var string
     */
    private $identity;

    /**
     * Модель логики для хранение предупреждении
     * Logic Model for Warning Storage
     *
     * @var ILogicalModel
     */
    private $logicalModel;

    /**
     * Модель настроек для получение настроек предупреждении
     * Settings model for receiving alert settings
     *
     * @var IAttemptsType
     */
    private $attemptType;

    /**
     * Количество текущих предупреждения
     * Number of current alerts
     *
     * @var int
     */
    protected $currentAttempts;

    /**
     * AttemptSystem constructor.
     *
     * @param IAttemptsType $attemptType - Модель настроек для получение настроек предупреждении
     * @param ILogicalModel $model       - Модель логики для хранение предупреждении
     * @param null|string $identity      - IP адресс пользователя
     */
    public function __construct(IAttemptsType $attemptType, ILogicalModel $model, $identity = null)
    {
        $this->identity     = $identity ?? \DeftCMS\Engine::$DT->input->ip_address();
        $this->attemptType  = $attemptType;
        $this->logicalModel = $model;
    }

    /**
     * Проверка, превышено ли максимальное количество попыток
     * Verification if the maximum number of attempts exceeded
     *
     * @return bool
     */
    public function canExcess()
    {
        if( $this->attemptType->getExcessLocked() == 0)
        {
            return FALSE;
        }

        $this->clearingOverdue();
        return ($this->count() >= $this->attemptType->getExcessLocked());
    }

    /**
     * Проверка, превышено ли максимальное количество попыток для показа капчи
     * Verification if the maximum number of attempts exceeded for show captcha
     *
     * @return bool
     */
    public function canCaptcha()
    {
        if( $this->attemptType->getExcessCaptcha() == 0)
        {
            return FALSE;
        }

        $this->clearingOverdue();
        return ($this->count() >= $this->attemptType->getExcessCaptcha());
    }

    /**
     * Увеличить одно предупреждение
     * Increase one attempt
     *
     * @return void
     */
    public function increaseAttempt()
    {
        $this->currentAttempts = $this->count() + 1;
        $this->logicalModel->increaseAttempt($this->identity, $this->attemptType->getName());
    }

    /**
     * Очистка всех предупреждении
     * Clearing all attempts
     *
     * @return void
     */
    public function clearingAttempt()
    {
        $this->logicalModel->cleaningAttempt($this->identity, $this->attemptType->getName());
        $this->currentAttempts = 0;
    }

    /**
     * Очистка старых предупреждении
     * Clearing old attempts
     *
     * @return void
     *
     */
    public function clearingOverdue()
    {
        $this->logicalModel->clearingOverdue($this->identity,  $this->attemptType->getName(), $this->attemptType->getExpireTime());
    }

    /**
     * Получить настройки системы
     * Get system settings
     *
     * @return IAttemptsType
     */
    public function getAttemptType()
    {
        return $this->attemptType;
    }

    /**
     * Получить время через которое истечет первое предупреждение
     * Get the time after which the first warning expires
     *
     * @return int
     */
    public function getExcessTime()
    {
        $timestamp =  $this->logicalModel->getExcessTime($this->identity, $this->attemptType->getName());
        $locked = $timestamp + $this->attemptType->getExpireTime() - time();
        return $locked > 0 ? $locked : 0;
    }

    /**
     * Получить количество предупреждений
     * Get the number of warnings
     *
     * @return int
     */
    protected function count()
    {
        if( !is_int($this->currentAttempts) )
        {
            $this->currentAttempts = $this->logicalModel->getAttempts($this->identity, $this->attemptType->getName());
        }

        return $this->currentAttempts;
    }
}
