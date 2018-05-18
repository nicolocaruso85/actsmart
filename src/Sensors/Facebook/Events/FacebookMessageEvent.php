<?php

namespace actsmart\actsmart\Sensors\Facebook\Events;


class FacebookMessageEvent extends FacebookEvent
{
    const EVENT_NAME = 'event.facebook.message';

    protected $id;

    protected $time;

    protected $sender_id;

    protected $recipient_id;

    protected $timestamp;

    protected $mid;

    protected $seq;

    protected $text;

    public function __construct($subject, $arguments)
    {
        parent::__construct($subject, $arguments, $this::EVENT_NAME);

        $this->id = $subject->id ?? null;
        $this->time = $subject->time ?? null;

        $this->sender_id = $subject->messaging[0]->sender->id ?? null;
        $this->recipient_id = $subject->messaging[0]->recipient->id ?? null;
        $this->timestamp = $subject->messaging[0]->timestamp ?? null;
        $this->mid = $subject->messaging[0]->message->mid ?? null;
        $this->seq = $subject->messaging[0]->message->seq ?? null;
        $this->text = $subject->messaging[0]->message->text ?? null;
    }

    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return null
     */
    public function getSenderId()
    {
        return $this->sender_id;
    }

    /**
     * @return null
     */
    public function getRecipientId()
    {
        return $this->recipient_id;
    }

    /**
     * @return null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return null
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * @return null
     */
    public function getSeq()
    {
        return $this->seq;
    }

    /**
     * @return null
     */
    public function getText()
    {
        return $this->text;
    }

}