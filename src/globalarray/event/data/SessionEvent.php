<?php

declare(strict_types=1);

namespace globalarray\event\player;

use globalarray\session\PlayerSession;

abstract class SessionEvent{

    public static $handlerList = null;

    protected $session;

    public function __construct(
        /*protected readonly*/ PlayerSession $session
    ) {
        $this->session = $session;
    }

    public function getSession() : PlayerSession{
        return $this->session;
    }
}