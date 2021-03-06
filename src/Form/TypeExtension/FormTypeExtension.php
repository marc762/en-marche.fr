<?php

namespace AppBundle\Form\TypeExtension;

use AppBundle\Form\DataTransformer\NullToStringTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['cast_null_to_string']) {
            $builder->addModelTransformer(new NullToStringTransformer());
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['form_full'] = $options['form_full'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'cast_null_to_string' => false,
                'form_full' => false,
            ])
            ->setAllowedTypes('cast_null_to_string', 'bool')
            ->setAllowedTypes('form_full', 'bool')
        ;
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}
