<?php

namespace Intracto\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class SendEmptyWishlistReminderCommand extends ContainerAwareCommand
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('intracto:sendWishlistReminderMails')
            ->setDescription('Send reminder to add items to wishlist');
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
        $wishlistMailQuery = $container->get('wishlist.query.wishlist_mail_query');
        $mailerService = $container->get('intracto_core.service.mail_service');
        $emptyWishlists = $entryMailQuery->findAllToRemindOfEmptyWishlist();
        $timeNow = new \DateTime();

        try {
            foreach ($emptyWishlists as $entry) {
                $itemCount = $wishlistMailQuery->countWishlistItemsOfParticipant($entry);

                if ($itemCount[0]['wishlistItemCount'] == 0) {
                    $mailerService->sendWishlistReminderMail($entry);

                    $entry->setEmptyWishlistReminderSentTime($timeNow);
                    $em->persist($entry);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $em->flush();
        }
    }
}
