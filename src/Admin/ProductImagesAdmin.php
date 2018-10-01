<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductImagesAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $form)
    {
        $form->add('imageFile', VichImageType::class, [
            'allow_delete' => false,
            'required' => false,
        ]);
        $form->add('position');
    }

}
