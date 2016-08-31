<?php

namespace Intracto\SecretSantaBundle\Controller;

use Intracto\SecretSantaBundle\Entity\Entry;
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
                    $this->em->persist($item);
                    // keep track of rank
                    if ($item->getRank() < $lastRank) {
                        $inOrder = false;
                    }
                    $lastRank = $item->getRank();
                }

                // remove entries not passed
                foreach ($currentWishlistItems as $item) {
                    if (!$newWishlistItems->contains($item)) {
                        $this->em->remove($item);
                    }
                }

                // For now assume that a save of entry means the list has changed
                $timeNow = new \DateTime();
                $entry->setWishlistUpdated(true);
                $entry->setWishlistUpdatedTime($timeNow);

                $this->em->persist($entry);
                $this->em->flush();

                if (!$request->isXmlHttpRequest()) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        $this->translator->trans('flashes.entry.wishlist_updated')
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
                    $return = ['responseCode' => 200, 'message' => 'Added!'];

                    return new JsonResponse($return);
                }
            }
        }

        $secretSanta = $entry->getEntry();
        $eventDate = date_format($entry->getPool()->getEventdate(), 'Y-m-d');
        $oneWeekFromEventDate = date('Y-m-d', strtotime($eventDate.'- 1 week'));

        if (!$request->isXmlHttpRequest()) {
            return [
                'entry' => $entry,
                'form' => $form->createView(),
                'secret_santa' => $secretSanta,
                'oneWeekFromEventDate' => $oneWeekFromEventDate,
            ];
        }

        return $this->render();
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
        $entry = $this->entryRepository->find($entryId);

        if ($entry->getPool()->getListurl() === $listUrl) {
            $emailAddress = new EmailAddress($request->request->get('email'));
            $emailAddressErrors = $this->validator->validate($emailAddress);

            if (count($emailAddressErrors) > 0) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->translator->trans('flashes.entry.edit_email')
                );
            } else {
                $entry->setEmail((string) $emailAddress);
                $this->em->flush($entry);

                $this->mailerService->sendSecretSantaMailForEntry($entry);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->translator->trans('flashes.entry.saved_email')
                );
            }
        }

        return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));

        return $this->render();
    }

    /**
     * @return Response
     */
    public function dumpEntriesAction()
    {
        $startCrawling = new \DateTime();
        $startCrawling->sub(new \DateInterval('P4M'));

        return ['entries' => $this->entryRepository->findAfter($startCrawling)];

        return $this->render();
    }

    /**
     * @param string $url
     * @param int    $entryId
     *
     * @return Response
     */
    public function pokeBuddyAction($url, $entryId)
    {
        $entry = $this->entryRepository->find($entryId);

        $this->mailerService->sendPokeMailToBuddy($entry);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->translator->trans('flashes.entry.poke_buddy')
        );

        return $this->redirect($this->generateUrl('intracto.secretsanta.entry.index', ['url' => $url]));

        return $this->render();
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

        $correctConfirmation = ($request->get('confirmation') === $this->translator->trans('remove_participant.phrase_to_type'));
        if ($correctConfirmation === false || $correctCsrfToken === false) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->translator->trans('flashes.remove_participant.wrong')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        $entry = $this->entryRepository->find($entryId);
        $pool = $entry->getPool()->getEntries();

        $eventDate = date_format($entry->getPool()->getEventdate(), 'Y-m-d');
        $oneWeekFromEventDate = date('Y-m-d', strtotime($eventDate.'- 1 week'));
        if (date('Y-m-d') > $oneWeekFromEventDate) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->translator->trans('flashes.modify_list.warning')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        if (count($pool) <= 3) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->translator->trans('flashes.remove_participant.danger')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        if ($entry->isPoolAdmin()) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->translator->trans('flashes.remove_participant.warning')
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
                $this->translator->trans('flashes.remove_participant.excluded_entries')
            );

            return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));
        }

        $secretSanta = $entry->getEntry();
        $buddyId = $this->entryQuery->findBuddyByEntryId($entryId);
        $buddy = $this->entryRepository->find($buddyId[0]['id']);

        $this->em->remove($entry);
        $this->em->flush();

        $buddy->setEntry($secretSanta);
        $this->em->persist($buddy);
        $this->em->flush();

        $this->mailerService->sendRemovedSecretSantaMail($buddy);

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->translator->trans('flashes.remove_participant.success')
        );

        return $this->redirect($this->generateUrl('pool_manage', ['listUrl' => $listUrl]));

        return $this->render();
    }
}
