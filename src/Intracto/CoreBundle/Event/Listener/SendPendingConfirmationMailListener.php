<?php

namespace Intracto\CoreBundle\Event\Listener;

use Intracto\CoreBundle\Event\PoolEvent;
use Intracto\CoreBundle\Event\PoolEvents;
use Intracto\Domain\Pool\Model\Pool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SendPendingConfirmationMailListener implements EventSubscriberInterface
{
    /**
     * @var EngineInterface
     */
    private $templating;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $adminEmail;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public static function getSubscribedEvents()
    {
        return [
            PoolEvents::NEW_POOL_CREATED => 'onNewPool',
        ];
    }

    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer, $adminEmail, TranslatorInterface $translator)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
        $this->translator = $translator;
    }

    public function onNewPool(PoolEvent $event)
    {
        $this->sendPendingConfirmationMail($event->getPool());
    }

    private function sendPendingConfirmationMail(Pool $pool)
    {
        $this->translator->setLocale($pool->getLocale());

        $message = \Swift_Message::newInstance()
            ->setSubject($this->translator->trans('emails.pendingconfirmation.subject'))
            ->setFrom($this->adminEmail, $this->translator->trans('emails.sender'))
            ->setTo($pool->getOwnerEmail())
            ->setBody(
                $this->templating->render(
                    'IntractoSecretSantaBundle:Emails:pendingconfirmation.txt.twig',
                    ['pool' => $pool]
                )
            )
            ->addPart(
                $this->templating->render(
                    'IntractoSecretSantaBundle:Emails:pendingconfirmation.html.twig',
                    ['pool' => $pool]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}
