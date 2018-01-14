<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Handler\ReuseFormHandler;
use App\Form\Type\RequestReuseUrlType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ReuseController extends AbstractController
{
    /**
     * @Route("/reuse", name="request_reuse_url")
     * @Template("Party/getReuseUrl.html.twig")
     * @Method({"GET", "POST"})
     */
    public function showRequestAction(Request $request, ReuseFormHandler $handler)
    {
        $form = $this->createForm(RequestReuseUrlType::class);

        if ($handler->handle($form, $request)) {
            return $this->redirectToRoute('homepage');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
