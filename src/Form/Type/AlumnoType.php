<?php

namespace App\Form\Type;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add("nombres", TextType::class, [
            "attr" => [
                "col" => "s12 m4"
            ]
        ]);
        $builder->add("apellidoPaterno", TextType::class, [
            "attr" => [
                "col" => "s12 m4"
            ]
        ]);
        $builder->add("apellidoMaterno", TextType::class, [
            "attr" => [
                "col" => "s12 m4"
            ]
        ]);
        $builder->add("correo", EmailType::class);
        $builder->add("curp", TextType::class, [
            "attr" => [
                "pattern" => "^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$"
            ]
        ]);
    }
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            "class" => Usuario::class
        ]);
    }
}