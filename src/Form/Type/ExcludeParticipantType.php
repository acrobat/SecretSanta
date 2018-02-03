<?php

namespace App\Form\Type;

use Doctrine\ORM\EntityRepository;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcludeParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $me = $event->getData();
            $form = $event->getForm();

            $form->add('excluded_participants', EntityType::class, [
                'class' => Participant::class,
                'multiple' => true,
                'expanded' => false,
                'choice_label' => 'name',
                'label' => $me->getName(),
                'attr' => ['data-participant' => $me->getId(), 'class' => 'js-selector-participant'],
                'query_builder' => function (EntityRepository $er) use ($me) {
                    return $er->createQueryBuilder('e')
                        ->where('e.party = :party')
                        ->andWhere('e != :me')
                        ->setParameters([
                            'party' => $me->getParty(),
                            'me' => $me,
                        ]);
                },
                'required' => false,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
