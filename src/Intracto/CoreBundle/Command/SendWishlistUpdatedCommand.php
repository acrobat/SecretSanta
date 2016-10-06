<?php

namespace Intracto\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class SendWishlistUpdatedCommand extends ContainerAwareCommand
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('intracto:sendWishlistUpdatedMails')
            ->setDescription('Send notification to buddy to alert them the wishlist has been updated');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $entryMailQuery = $container->get('entry.query.entry_mail_query');
        $mailerService = $container->get('intracto_core.service.mail_service');
        $secret_santas = $entryMailQuery->findAllToRemindOfUpdatedWishlist();
        $timeNow = new \DateTime();

        foreach ($secret_santas as $secret_santa) {
            $receiver = $secret_santa->getEntry();

            $mailerService->sendWishlistUpdatedMail($receiver, $secret_santa);

            $receiver->setWishlistUpdated(false);
            $receiver->setUpdateWishlistReminderSentTime($timeNow);
            $em->persist($receiver);
        }

        $em->flush();
    }
}
