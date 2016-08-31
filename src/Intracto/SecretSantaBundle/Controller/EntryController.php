<?php

namespace Intracto\SecretSantaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Intracto\SecretSantaBundle\Entity\Entry;
use Intracto\SecretSantaBundle\Entity\EmailAddress;
use Intracto\SecretSantaBundle\Entity\WishlistItem;
use Intracto\SecretSantaBundle\Form\WishlistType;
use Intracto\SecretSantaBundle\Form\WishlistNewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntryController extends Controller
{
    /** @var Entry */
    public $entry;

    /**
     * @Route("/entry/{url}", name="entry_view")
     * @Template()
     */
    public function indexAction(Request $request, $url)
    {
        $this->getEntry($url);

        if ($this->entry->getWishlist() !== null && $this->entry->getWishlist() != '') {
            $legacyWishlist = true;
            $form = $this->createForm(WishlistType::class, $this->entry);
        } else {
            $legacyWishlist = false;
            $form = $this->createForm(WishlistNewType::class, $this->entry);
        }

        // Log visit on first access
        if ($this->entry->getViewdate() === null) {
            $this->entry->setViewdate(new \DateTime());
            $this->get('doctrine.orm.entity_manager')->flush($this->entry);
        }

        // Log ip address on first access
        if ($this->entry->getIp() === null) {
            $ip = $request->getClientIp();
            $this->entry->setIp($ip);
            $this->get('doctrine.orm.entity_manager')->flush($this->entry);
        }

        if ('POST' === $request->getMethod()) {
            // get current items to compare against items later on
            $currentWishlistItems = new ArrayCollection();
            /** @var WishlistItem $item */
            foreach ($this->entry->getWishlistItems() as $item) {
                $currentWishlistItems->add($item);
            }

            $form->submit($request);

            if ($form->isValid()) {
                // save entries passed and check rank
                $inOrder = true;
                $lastRank = 0;
                $newWishlistItems = $this->entry->getWishlistItems();

                foreach ($newWishlistItems as $item) {
                    $item->setEntry($this->entry);
                    $this->get('doctrine.orm.entity_manager')->persist($item);
                    // keep track of rank
                    if ($item->getRank() < $lastRank) {
                        $inOrder = false;
                    }
                    $lastRank = $item->getRank();
                }

                // remove entries not passed
                foreach ($currentWishlistItems as $item) {
                    if (!$newWishlistItems->contains($item)) {
                        $this->get('doctrine.orm.entity_manager')->remove($item);
                    }
                }

                // For now assume that a save of entry means the list has changed
                $time_now = new \DateTime();
                $this->entry->setWishlistUpdated(true);
                $this->entry->setWishlistUpdatedTime($time_now);

                $this->get('doctrine.orm.entity_manager')->persist($this->entry);
                $this->get('doctrine.orm.entity_manager')->flush();

                if (!$request->isXmlHttpRequest()) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        $this->get('translator')->trans('flashes.entry.wishlist_updated')
                    );

                    if (!$inOrder) {
                        // redirect to force refresh of form and entity
                        return $this->redirect($this->generateUrl('entry_view', ['url' => $url]));
                    }

                    if ($legacyWishlist && ($this->entry->getWishlist() === null || $this->entry->getWishlist() === '')) {
                        // started out with legacy, wishlist is empty now, reload page to switch to new wishlist
                        return $this->redirect($this->generateUrl('entry_view', ['url' => $url]));
                    }
                }

                if ($request->isXmlHttpRequest()) {
                    $return = ['responseCode' => 200, 'message' => 'Added!'];

                    return new JsonResponse($return);
                }
            }
        }

        $secret_santa = $this->entry->getEntry();
        $eventDate = date_format($this->entry->getPool()->getEventdate(), 'Y-m-d');
        $oneWeekFromEventDate = date('Y-m-d', strtotime($eventDate.'- 1 week'));

        if (!$request->isXmlHttpRequest()) {
            return [
                'entry' => $this->entry,
                'form' => $form->createView(),
                'secret_santa' => $secret_santa,
                'oneWeekFromEventDate' => $oneWeekFromEventDate,
            ];
        }
    }

    /**
     * Retrieve entry by url.
     *
     * @param string $url
     *
     * @throws NotFoundHttpException
     *
     * @return bool
     */
    protected function getEntry($url)
    {
        $this->entry = $this->get('entry_repository')->findOneByUrl($url);

        if (!is_object($this->entry)) {
            throw new NotFoundHttpException();
        }

        return true;
    }

    /**
     * @Route("/entry/edit-email/{listUrl}/{entryId}", name="entry_email_edit")
     * @Template()
     */
    public function editEmailAction(Request $request, $listUrl, $entryId)
    {
        /** @var Entry $entry */
        $entry = $this->get('entry_repository')->find($entryId);

        if ($entry->getPool()->getListurl() === $listUrl) {
            $emailAddress = new EmailAddress($request->request->get('email'));
            $emailAddressErrors = $this->get('validator')->validate($emailAddress);

            if (count($emailAddressErrors) > 0) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('flashes.entry.edit_email')
                );
            } else {
                $entry->setEmail((string) $emailAddress);
                $this->get('doctrine.orm.entity_manager')->flush($entry);

                $this->get('intracto_secret_santa.mail')->sendSecretSantaMailForEntry($entry);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('flashes.entry.saved_email')
                );
            }
        }

        return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
    }

    /**
     * @Route("/dump-entries", name="dump_entries")
     * @Template()
     */
    public function dumpEntriesAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADWORDS');

        $startCrawling = new \DateTime();
        $startCrawling->sub(new \DateInterval('P4M'));

        return ['entries' => $this->get('entry_repository')->findAfter($startCrawling)];
    }

    /**
     * @Route("/poke/{url}/{entryId}", name="poke_buddy")
     * @Template()
     */
    public function pokeBuddyAction($url, $entryId)
    {
        $entry = $this->get('entry_repository')->find($entryId);

        $this->get('intracto_secret_santa.mail')->sendPokeMailToBuddy($entry);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('flashes.entry.poke_buddy')
        );

        return $this->redirect($this->generateUrl('entry_view', ['url' => $url]));
    }

    /**
     * @Route("/entry/remove/{listUrl}/{entryId}", name="entry_remove")
     * @Template()
     */
    public function removeEntryFromPoolAction(Request $request, $listUrl, $entryId)
    {
        $correctCsrfToken = $this->isCsrfTokenValid(
            'delete_participant',
            $request->get('csrf_token')
        );

        $correctConfirmation = ($request->get('confirmation') === $this->get('translator')->trans('remove_participant.phrase_to_type'));
        if ($correctConfirmation === false || $correctCsrfToken === false) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->get('translator')->trans('flashes.remove_participant.wrong')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        $entry = $this->get('entry_repository')->find($entryId);
        $pool = $entry->getPool()->getEntries();

        $eventDate = date_format($entry->getPool()->getEventdate(), 'Y-m-d');
        $oneWeekFromEventDate = date('Y-m-d', strtotime($eventDate.'- 1 week'));
        if (date('Y-m-d') > $oneWeekFromEventDate) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->get('translator')->trans('flashes.modify_list.warning')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        if (count($pool) <= 3) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->get('translator')->trans('flashes.remove_participant.danger')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        if ($entry->isPoolAdmin()) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->get('translator')->trans('flashes.remove_participant.warning')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        $excludeCount = 0;

        foreach ($pool as $p) {
            if (count($p->getExcludedEntries()) > 0) {
                ++$excludeCount;
            }
        }

        if ($excludeCount > 0) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->get('translator')->trans('flashes.remove_participant.excluded_entries')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        $secretSanta = $entry->getEntry();
        $buddyId = $this->get('intracto_secret_santa.entry')->findBuddyByEntryId($entryId);
        $buddy = $this->get('entry_repository')->find($buddyId[0]['id']);

        $this->get('doctrine.orm.entity_manager')->remove($entry);
        $this->get('doctrine.orm.entity_manager')->flush();

        $buddy->setEntry($secretSanta);
        $this->get('doctrine.orm.entity_manager')->persist($buddy);
        $this->get('doctrine.orm.entity_manager')->flush();

        $this->get('intracto_secret_santa.mail')->sendRemovedSecretSantaMail($buddy);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('flashes.remove_participant.success')
        );

        return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
    }
}
