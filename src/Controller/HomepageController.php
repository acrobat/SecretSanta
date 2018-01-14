<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\PartyType;
use App\Entity\Party;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Template("Party/create.html.twig")
     * @Method("GET")
     */
    public function indexAction()
    {
        $partyForm = $this->createForm(PartyType::class, new Party(), [
            'action' => $this->generateUrl('create_party'),
        ]);

        return [
            'form' => $partyForm->createView(),
        ];
    }
}
