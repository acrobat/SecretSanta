<?php

declare(strict_types=1);

namespace App\Controller\Participant;

use App\Entity\Party;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExposeParticipantsController extends AbstractController
{
    /**
     * @Route("/participants/expose/{listurl}", name="expose_participants")
     * @Template("Participant/exposeAll.html.twig")
     * @Method("GET")
     */
    public function indexAction(Party $party)
    {
        return [
            'party' => $party,
        ];
    }
}
