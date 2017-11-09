<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Stores\StoreInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\NotifierInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

class Agent
{
    /** @var  array */
    protected $sensors = [];

    /** @var  array */
    protected $actuators = [];

    /** @var  array */
    protected $controllers = [];

    /** @var array */
    protected $stores = [];

    /** @var array  */
    protected $interpreters = [];

    /** @var EventDispatcher */
    protected $dispatcher;

    /** @var  Logger */
    protected $logger;

    /** @var  Response */
    protected $http_response = null;

    /**
     * Agent constructor.
     *
     * @param EventDispatcher $dispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * Based on the type of component it gets added to the appropriate array. Also any other services that the components
     * may need are bound.
     *
     * @param ComponentInterface $component
     */
    public function addComponent(ComponentInterface $component) {
        switch(true) {
            case $component instanceof SensorInterface:
                $this->sensors[$component->getKey()] = $component;
                break;
            case $component instanceof ActuatorInterface:
                $this->actuators[$component->getKey()] = $component;
                break;
            case $component instanceof ControllerInterface:
                $this->controllers[$component->getKey()] = $component;
                break;
            case $component instanceof StoreInterface:
                $this->stores[$component->getKey()] = $component;
                break;
            case $component instanceof InterpreterInterface:
                $this->interpreters[$component->getKey()] = $component;
                break;
        }

        //Inject the agent back in the components
        $component->setAgent($this);

        $this->bindLogger($component);
        $this->bindDispatcher($component);
        $this->bindListener($component);
    }

    /**
     * Returns a store based on the key.
     *
     * @param $key
     * @return mixed
     */
    public function getStore($key)
    {
        return $this->stores[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getSensor($key)
    {
        return $this->sensors[$key];
    }

    public function setHttpReaction(Response $response)
    {
        $this->http_response = $response;
    }

    public function httpReact()
    {
        if ($this->http_response == null) {
            return new Response('', Response::HTTP_OK, ['content-type' => 'text/html']);
        }
        return $this->http_response;
    }

    /**
     * @param $component
     */
    private function bindListener($component){
        if ($component instanceoF ListenerInterface) {
            $this->listenForEvents($component);
        }
    }

    /**
     * Registers a listener for the event that the listener itself says they are interested in.
     *
     * @param ListenerInterface $listener
     */
    private function listenForEvents(ListenerInterface $listener)
    {
        foreach ($listener->listensForEvents() as $event_key) {
            $this->dispatcher->addListener($event_key, array($listener, 'listen'));
        }

    }

    /**
     * Adds the logger to classes that are LaggerAware
     * @param $component
     */
    private function bindLogger($component) {
        if ($component instanceOf LoggerAwareInterface) {
            $component->setLogger($this->logger);
        }
    }

    /**
     * Adds the dispatcher to classes that are Notifiers
     * @param $component
     */
    private function bindDispatcher($component) {
        if ($component instanceOf NotifierInterface) {
            $component->setDispatcher($this->dispatcher);
        }
    }

}