<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Interface AttemptsType
 * @package DeftCMS\Components\b1tc0re\Security\Attempt
 */
class AuthorizationAttempt implements IAttemptsType
{
    /**
     * Maximum number of warnings to block actions
     * Максимальное количество предупреждений для блокировки действий
     *
     * @var int
     */
    protected $excess = 10;

    /**
     * Maximum number of warnings for captcha output
     * Максимальное количество предупреждений для вывода капчи
     *
     * @var int
     */
    protected $captcha = 5;

    /**
     * AuthorizationAttempt constructor.
     * @param int $excess
     * @param int $captcha
     */
    public function __construct(int $excess = 10, int $captcha = 5)
    {
        $this->excess = $excess;
    }

    /**
     * Return the name of attempt type
     * Вернуть названия предуприждений
     *
     * @return string
     */
    public function getName(): string
    {
        return strtolower(get_class());
    }

    /**
     * Return the maximum number of warnings to block actions
     * Вернуть максимальное количество предупреждений для блокировки действий
     *
     * @return int
     */
    public function getExcessLocked(): int
    {
        return $this->excess;
    }

    /**
     * Return the maximum number of warnings for captcha output
     * Вернуть максимальное количество предупреждений для вывода капчи
     *
     * @return int
     */
    public function getExcessCaptcha(): int
    {
        return $this->captcha;
    }
}