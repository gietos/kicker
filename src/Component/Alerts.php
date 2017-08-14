<?php

namespace Gietos\Kicker\Component;

use Symfony\Component\HttpFoundation\Session\Session;

class Alerts
{
    /**
     * @var Session
     */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function add(string $class, string $message)
    {
        $this->session->getFlashBag()->add('alerts', json_encode(compact('class', 'message')));
    }

    public function get()
    {
        return array_map(function ($alert) {
            return json_decode($alert, true);
        }, $this->session->getFlashBag()->get('alerts'));
    }
}
