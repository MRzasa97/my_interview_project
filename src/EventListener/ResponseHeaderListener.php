<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseHeaderListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        if($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('x-task', '1');
    }
}