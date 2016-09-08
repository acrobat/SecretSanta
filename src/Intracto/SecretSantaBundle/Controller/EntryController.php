<?php

namespace Intracto\SecretSantaBundle\Controller;

use Intracto\SecretSantaBundle\Entity\Entry;
use Intracto\SecretSantaBundle\Form\WishlistNewType;
use Intracto\SecretSantaBundle\Form\WishlistType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EntryController.
 */
class EntryController extends Controller
{
    /**
     * @param Request $request
     * @param string  $url
     *
     * @return Response
     */
    public function indexAction(Request $request, $url)
    {
        /** @var Entry $entry */
        $entry = $this->getDoctrine()->getRepository('IntractoSecretSantaBundle:Entry')->findOneBy(['url' => $url]);
        if (!$entry instanceof Entry) {
            throw new NotFoundHttpException();
        }

        if ($entry->getWishlist() !== null && $entry->getWishlist() != '') {
            $legacyWishlist = true;
            $form = $this->createForm(WishlistType::class, $entry);
        } else {
            $legacyWishlist = false;
            $form = $this->createForm(WishlistNewType::class, $entry);
        }

        // Log visit date + ip on first access
        if ($entry->getViewdate() === null || $entry->getIp() === null) {
            $entry->setViewdate(new \DateTime());
            $entry->setIp($request->getClientIp());
            $this->getDoctrine()->getManager()->flush($entry);
        }

        if ('POST' === $request->getMethod()) {
            // get current items to compare against items later on
            $currentWishlistItems = new ArrayCollection();
            /** @var WishlistItem $item */
            foreach ($entry->getWishlistItems() as $item) {
                $currentWishlistItems->add($item);
            }

            $form->submit($request);

            if ($form->isValid()) {
                // save entries passed and check rank
                $inOrder = true;
                $lastRank = 0;
                $newWishlistItems = $entry->getWishlistItems();

                foreach ($newWishlistItems as $item) {
                    $item->setEntry($entry);
                    $this->getDoctrine()->getManager()->persist($item);
                    // keep track of rank
                    if ($item->getRank() < $lastRank) {
                        $inOrder = false;
                    }
                    $lastRank = $item->getRank();
                }

                // remove entries not passed
                foreach ($currentWishlistItems as $item) {
                    if (!$newWishlistItems->contains($item)) {
                        $this->getDoctrine()->getManager()->remove($item);
                    }
                }

                // For now assume that a save of entry means the list has changed
                $timeNow = new \DateTime();
                $entry->setWishlistUpdated(true);
                $entry->setWishlistUpdatedTime($timeNow);

                $this->getDoctrine()->getManager()->persist($entry);
                $this->getDoctrine()->getManager()->flush();

                if (!$request->isXmlHttpRequest()) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        $this->get('translator')->trans('flashes.entry.wishlist_updated')
                    );

                    if (!$inOrder) {
                        // redirect to force refresh of form and entity
                        return $this->redirect($this->generateUrl('intracto.secretsanta.entry.index', ['url' => $url]));
                    }

                    if ($legacyWishlist && ($entry->getWishlist() === null || $entry->getWishlist() === '')) {
                        // started out with legacy, wishlist is empty now, reload page to switch to new wishlist
                        return $this->redirect($this->generateUrl('intracto.secretsanta.entry.index', ['url' => $url]));
                    }
                }

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['responseCode' => 200, 'message' => 'Added!']);
                }
            }
        }

        $secretSanta = $entry->getEntry();
        $eventDate = date_format($entry->getPool()->getEventdate(), 'Y-m-d');
        $oneWeekFromEventDate = date('Y-m-d', strtotime($eventDate.'- 1 week'));

        if (!$request->isXmlHttpRequest()) {
            return $this->render('IntractoSecretSantaBundle:Entry:index.html.twig', [
                'entry' => $entry,
                'form' => $form->createView(),
                'secret_santa' => $secretSanta,
                'oneWeekFromEventDate' => $oneWeekFromEventDate,
            ]);
        }
    }

    /**
     * @param Request $request
     * @param string  $listUrl
     * @param int     $entryId
     *
     * @return Response
     */
    public function editEmailAction(Request $request, $listUrl, $entryId)
    {
        /** @var Entry $entry */
        $entry = $this->getDoctrine()->getRepository('IntractoSecretSantaBundle:Entry')->find($entryId);
        if (!$entry instanceof Entry) {
            throw new NotFoundHttpException();
        }

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
                $this->getDoctrine()->getManager()->flush($entry);

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
     * @return Response
     */
    public function dumpEntriesAction()
    {
        $startCrawling = new \DateTime();
        $startCrawling->sub(new \DateInterval('P4M'));

        return $this->render('IntractoSecretSantaBundle:Entry:dumpEntries.html.twig', [
            'entries' => $this->getDoctrine()->getRepository('IntractoSecretSantaBundle:Entry')->findAfter($startCrawling),
        ]);
    }

    /**
     * @param string $url
     * @param int    $entryId
     *
     * @return Response
     */
    public function pokeBuddyAction($url, $entryId)
    {
        /** @var Entry $entry */
        $entry = $this->getDoctrine()->getRepository('IntractoSecretSantaBundle:Entry')->find($entryId);
        if (!$entry instanceof Entry) {
            throw new NotFoundHttpException();
        }

        $this->get('intracto_secret_santa.mail')->sendPokeMailToBuddy($entry);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('flashes.entry.poke_buddy')
        );

        return $this->redirect($this->generateUrl('intracto.secretsanta.entry.index', ['url' => $url]));
    }

    /**
     * @param Request $request
     * @param string  $listUrl
     * @param int     $entryId
     *
     * @return Response
     */
    public function removeEntryFromPoolAction(Request $request, $listUrl, $entryId)
    {
        $correctCsrfToken = $this->isCsrfTokenValid(
            'delete_participant',
            $request->get('csrf_token')
        );

        /** @var Entry $entry */
        $entry = $this->getDoctrine()->getRepository('IntractoSecretSantaBundle:Entry')->find($entryId);
        if (!$entry instanceof Entry) {
            throw new NotFoundHttpException();
        }

        $correctConfirmation = ($request->get('confirmation') === $this->get('translator')->trans('remove_participant.phrase_to_type'));
        if ($correctConfirmation === false || $correctCsrfToken === false) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->get('translator')->trans('flashes.remove_participant.wrong')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

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
        $buddy = $this->getDoctrine()->getRepository('IntractoSecretSantaBundle:Entry')->find($buddyId[0]['id']);

        $this->getDoctrine()->getManager()->remove($entry);
        $this->getDoctrine()->getManager()->flush();

        $buddy->setEntry($secretSanta);
        $this->getDoctrine()->getManager()->persist($buddy);
        $this->getDoctrine()->getManager()->flush();

        $this->get('intracto_secret_santa.mail')->sendRemovedSecretSantaMail($buddy);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('flashes.remove_participant.success')
        );

        return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
    }
}
