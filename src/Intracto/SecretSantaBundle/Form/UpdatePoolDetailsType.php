<?php

namespace Intracto\SecretSantaBundle\Form;

use Genemu\Bundle\FormBundle\Form\JQuery\Type\DateType;
use Intracto\Domain\Pool\Model\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatePoolDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eventdate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.date_party',
                'format' => 'dd-MM-yyyy',
                'configs' => [
                    'minDate' => 0,
                ],
            ])
            ->add('amount', TextType::class, ['label' => 'label.amount_to_spend'])
            ->add('location', TextType::class, ['label' => 'label.location']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pool::class,
        ]);
    }
}
