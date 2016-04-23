<?php

namespace common;

/**
 * Description of CronJobAbstract
 *
 * @author jne
 */
const DEFAULT_TTL = 60; // seconds

abstract class CronJobAbstract
{
    abstract function execute();
    abstract function getTTL();
}