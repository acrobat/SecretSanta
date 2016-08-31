<?php

namespace Intracto\SecretSantaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StaticController.
 */
class StaticController extends Controller
{
    /**
     * @return Response
     */
    public function privacyPolicyAction()
    {
        return $this->render('IntractoSecretSantaBundle:Static:privacyPolicy.html.twig');
    }

    /**
     * @return Response
     */
    public function faqAction()
    {
        return $this->render('IntractoSecretSantaBundle:Static:faq.html.twig');
    }
}
