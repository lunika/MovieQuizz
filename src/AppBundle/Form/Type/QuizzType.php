<?php

namespace AppBundle\Form\Type;

use AppBundle\Tool\Signature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class QuizzType.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class QuizzType extends AbstractType
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $secret = $this->secret;
        $builder
            ->add('movie', 'hidden', [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('actor', 'hidden', [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('signature', 'hidden', [
                'constraints' => new Callback([
                    'callback' => function ($object, ExecutionContextInterface $context) use ($secret) {
                        $data = $context->getRoot()->getData();

                        $signature = Signature::generate($data['movie'], $data['actor'], $secret);

                        if ($signature != $object) {
                            $context->buildViolation('It is forbidden to change data')
                                ->atPath('signature')
                                ->addViolation();
                        }
                    },
                ]),
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
