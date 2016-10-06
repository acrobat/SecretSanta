<?php

namespace Intracto\CoreBundle\Context\Pool\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CreateType.
 */
class CreateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextType::class)
//            ->add(
//                'entries',
//                CollectionType::class,
//                [
//                    'entry_type' => EntryType::class,
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'by_reference' => false,
//                ]
//            )
            ->add(
                'eventdate',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'label' => 'label.date_party',
//                    'format' => 'dd-MM-yyyy',
//                    'configs' => [
//                        'minDate' => 0,
//                    ],
                ]
            )
            ->add(
                'amount',
                TextType::class,
                [
                    'label' => 'label.amount_to_spend',
                ]
            )
            ->add(
                'location',
                TextType::class,
                [
                    'label' => 'label.location',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CreateDto::class,
                'action' => '#mysanta',
//                'attr' => [
//                    'novalidate' => 'novalidate',
//                ],
            ]
        );
    }
}