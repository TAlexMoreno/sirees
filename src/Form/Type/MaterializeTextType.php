<?php

namespace App\Form\Type;

use App\Form\Utils\MaterializeTextTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterializeTextType extends AbstractType 
{
    public function getParent()
    {
        return TextType::class;    
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("helperData", null);
        $resolver->setAllowedTypes("helperData", MaterializeTextTypeHelper::class);    
    }
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars["helperData"] = $options["helperData"];
    }
}