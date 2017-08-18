<?php

namespace UserBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UserBundle\Entity\Preference;
use UserBundle\Entity\User;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FilterUserResponseEvent $event)
    {
        /** @var $user User */
        $user = $this->em->getRepository("UserBundle:User")->find($event->getUser()->getId());

        $preferences = new Preference();
        $preferences->setTranscriptionDeskPosition("leftRead-centerHelp-rightImage");
        $preferences->setUser($user);

        $this->em->persist($preferences);
        $this->em->flush();
    }
}