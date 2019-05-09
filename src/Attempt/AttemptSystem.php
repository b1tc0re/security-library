<?php  namespace DeftCMS\Components\b1tc0re\Security\Attempt;

use DeftCMS\Engine;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Система предуприждений
 *
 * @package     DeftCMS
 * @author	    b1tc0re
 * @copyright   2018-2019 DeftCMS (https://deftcms.org/)
 * @since	    Version 0.0.1
 */
class AttemptSystem
{
    /**
     * @var string
     */
    private $identity;

    /**
     * @var ILogicalModel
     */
    private $logicalModel;

    /**
     * @var int
     */
    protected $currentAttempts;

    /**
     * @var IAttemptsType
     */
    private $attemptType;

    /**
     * AttemptSystem constructor.
     * @param IAttemptsType $attemptType
     * @param ILogicalModel $model
     * @param null|string $identity default is user ip address
     */
    public function __construct(IAttemptsType $attemptType, ILogicalModel $model, $identity = null)
    {
        $this->identity     = $identity || Engine::$DT->input->ip_address();
        $this->attemptType  = $attemptType;
        $this->logicalModel = $model;
    }

    /**
     * Verification if the maximum number of attempts exceeded
     * @return bool
     */
    public function canExcess()
    {
        if( $this->attemptType->getExcessLocked() == 0)
        {
            return FALSE;
        }

        $this->clearingAttempt();
        return ($this->count() >= $this->attemptType->getExcessLocked());
    }

    /**
     * Verification if the maximum number of attempts exceeded for show captcha
     * @return bool
     */
    public function canCaptcha()
    {
        if( $this->attemptType->getExcessCaptcha() == 0)
        {
            return FALSE;
        }

        $this->clearingAttempt();
        return ($this->count() >= $this->attemptType->getExcessCaptcha());
    }

    /**
     * Increase one attempt
     */
    public function increaseAttempt()
    {
        $this->currentAttempts = $this->count() + 1;
        $this->logicalModel->increaseAttempt($this->identity, $this->attemptType->getName());
    }

    /**
     * Clearing all attempts
     */
    public function clearingAttempt()
    {
        $this->logicalModel->cleaningAttempt($this->identity, $this->attemptType->getName());
        $this->currentAttempts = 0;
    }

    /**
     * Clearing old attempts
     *
     */
    public function clearingOverdue()
    {
        $this->logicalModel->clearingOverdue($this->identity, $this->attemptType->getExpireTime());
    }

    /**
     * Get the number of warnings
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