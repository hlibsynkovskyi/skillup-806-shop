<?php
namespace App\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{

    public function getParent()
    {
        return RegistrationFormType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, ['label' => 'label.firstName'])
            ->add('lastName', null, ['label' => 'label.lastName'])
            ->add('address', null, ['label' => 'label.address'])
            ->remove('username');
    }

}
