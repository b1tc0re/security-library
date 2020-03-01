<?php namespace DeftCMS\Components\b1tc0re\Security\Attempt;

use DeftCMS\Components\b1tc0re\Security\Attempt\Interfaces\IAttemptsType;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Класс базовых предупреждении
 * Base Warning Class
 *
 * @package     DeftCMS
 * @author	    b1tc0re
 * @copyright   2018-2020 DeftCMS (https://deftcms.ru/)
 * @since	    Version 0.0.9
 */
class BaseAttempt implements IAttemptsType
{
    /**
     * Name of attempt type
     * Названия предуприждений
     *
     * @var string
     */
    protected $name = 'base';

    /**
     * Maximum number of warnings to block actions
     * Максимальное количество предупреждений для блокировки действий
     * @var int
     */
    protected $excessLocked     = 15;

    /**
     * Maximum number of warnings for captcha output
     * Максимальное количество предупреждений для вывода капчи
     *
     * @var int
     */
    protected $excessCaptcha    = 3;

    /**
     * The lifetime of one warning in seconds
     * Время жизни одного предупреждения в секундах
     *
     * @var int
     */
    protected $expireTime       = 1800;

    /**
     * BaseAttempt constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $property => $value)
        {
            if( property_exists($this, $property) )
            {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * Create instance from array options
     * Создать экземпляр из массива параметров
     *
     * @param array $options
     * @return BaseAttempt
     *
     * @see  BaseAttempt::createFromArray([
     *          'name'          => 'base',
     *          'excessLocked'  => 5,
     *          'excessCaptcha' => 15
     *      ]);
     */
    public static function createFromArray(array $options)
    {
        return new static($options);
    }

    /**
     * Return the name of attempt type
     * Вернуть названия предуприждений
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? 'base';
    }

    /**
     * Return the maximum number of warnings to block actions
     * Вернуть максимальное количество предупреждений для блокировки действий
     *
     * @return int
     */
    public function getExcessLocked(): int
    {
        return $this->excessLocked ?? 15;
    }

    /**
     * Return the maximum number of warnings for captcha output
     * Вернуть максимальное количество предупреждений для вывода капчи
     *
     * @return int
     */
    public function getExcessCaptcha(): int
    {
        return $this->excessCaptcha ?? 5;
    }

    /**
     * Return the lifetime of one warning
     * Вернуть время жизни одного предупреждения
     *
     * @return int
     */
    public function getExpireTime(): int
    {
        return $this->expireTime ?? 1800;
    }
}