<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 13:10
 */

namespace actsmart\actsmart\Controllers\Reactive;

use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Sensors\SensorEvent;


class ReactiveController implements ControllerInterface
{
    public function execute(SensorEvent $e = null)
    {

    }
}