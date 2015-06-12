<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class QuizzType
 * @package AppBundle\Form\Type
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class QuizzType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('movie', 'hidden', [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('actor', 'hidden', [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('yes', 'submit', ['label' => 'Yes'])
            ->add('no', 'submit', ['label' => 'No']);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'quizz';
    }
}
