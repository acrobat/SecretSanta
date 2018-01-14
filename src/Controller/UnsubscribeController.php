<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Participant;
use App\Form\Handler\UnsubscribeFormHandler;
use App\Form\Type\UnsubscribeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UnsubscribeController extends AbstractController
{
    /**
     * @Route("/unsubscribe/{url}", name="unsubscribe_confirm")
     * @Template("Participant/unsubscribe.html.twig")
     * @Method({"GET", "POST"})
     */
    public function confirmAction(Request $request, Participant $participant, UnsubscribeFormHandler $handler)
    {
        $form = $this->createForm(UnsubscribeType::class);

        if ($handler->handle($form, $request, $participant)) {
            return $this->redirectToRoute('homepage');
        }

        return [
            'unsubscribeForm' => $form->createView(),
            'participant' => $participant,
            'party' => $participant->getParty(),
        ];
    }
}
