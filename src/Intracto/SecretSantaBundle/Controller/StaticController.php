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
     * @return array
     */
    public function privacyPolicyAction()
    {
        return new Response([]);
    }

    /**
     * @return array
     */
    public function faqAction()
    {
        return new Response([]);
    }
}
