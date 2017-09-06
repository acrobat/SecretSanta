<?php
declare(strict_types=1);

namespace Intracto\SecretSantaBundle\ParamConverter;

use Intracto\SecretSantaBundle\Query\Season;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class SeasonParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();

        $year = ($request->get('year') !== 'all' ? $request->get('year') : null);

        $request->attributes->set($param, new Season($year));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration) : bool
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return Season::class === $configuration->getClass();
    }
}
