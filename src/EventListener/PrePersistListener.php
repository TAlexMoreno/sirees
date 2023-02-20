<?php

namespace App\EventListener;

use App\Entity\Usuario;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PrePersistListener {
    public function prePersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        // $entity = $lifecycleEventArgs->getObject();

        // if ($entity instanceof Usuario){
        //     /** @var Usuario $entity */
        //     dd($entity);
        // }
    }
}