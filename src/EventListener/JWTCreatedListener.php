<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Entity\User;

class JWTCreatedListener {

    public function onJWTCreated(JWTCreatedEvent $event)
    {   

        
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }
    
        $payload = $event->getData();
        $payload['firstname'] = $user->getFirstname();
        $payload['lastname'] = $user->getLastname();
        $payload['id'] = $user->getId();

    
        $event->setData($payload);
    
        $header = $event->getHeader();
    
        $event->setHeader($header);
    }

}