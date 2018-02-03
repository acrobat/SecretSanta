<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class RequestReuseUrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email([
                        'strict' => true,
                        'message' => "The email '{{ value }}' is not a valid email.",
                        'checkMX' => true,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-large btn-primary btn-create-event'],
                'label' => 'party-get_reuse_url.form.submit-btn',
            ])
        ;
    }
}
