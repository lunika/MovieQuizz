<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class HighScoreType
 * @package AppBundle\Form\Type
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class HighScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('submit', 'submit')
        ;
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'high_score';
    }
}
