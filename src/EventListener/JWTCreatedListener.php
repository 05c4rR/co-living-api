<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Entity\User;

class JWTCreatedListener {

    public function onJWTCreated(JWTCreatedEvent $event)
    {   
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }
    
        $payload = $event->getData();
        $payload['firstname'] = $user->getFirstname();
        $payload['lastname'] = $user->getLastname();
    
        $event->setData($payload);
    
        $header = $event->getHeader();
    
        $event->setHeader($header);
    }

}