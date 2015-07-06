<?php
/**
 * Created by PhpStorm.
 * User: yevgen
 * Date: 06.07.15
 * Time: 15:45
 */

namespace SleepingOwl\Apist;


trait SuppressExceptionTrait
{
    /**
     * @var bool
     */
    protected $suppressExceptions = true;

    /**
     * @return boolean
     */
    public function isSuppressExceptions()
    {
        return $this->suppressExceptions;
    }

    /**
     * @param boolean $suppress
     */
    public function setSuppressExceptions($suppress)
    {
        $this->suppressExceptions = $suppress;
    }
}