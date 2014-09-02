<?php
/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @All rights reserved
 */

namespace MarkMe\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Register extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => '\MarkMe\Entity\User',
            'cascade_validation' => true,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username', 'text', array('attr' => array('class' => 'form-control')))
            ->add('email', 'email', array('attr' => array('class' => 'form-control')))
            ->add('password', 'repeated', array('type' => 'password',
                'options' => array('attr' => array('class' => 'form-control')),
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "register";
    }
}