<?php

namespace App\Form\Type;

use App\Entity\Usuario;
use App\Form\Utils\MaterializeTextTypeHelper;
use App\Form\Utils\PasswordGenerator;
use DateTime;
use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options){
        /** @var Usuario $usr*/
        $usr = $builder->getData();
        $builder->add("username", MaterializeTextType::class, [
            "label" => "Matricula",
            "data" => $usr?->getUsername() ?? $options["matriculaProvisional"],
            "attr" => [
                "readonly" => true,
            ],
            "helperData" => new MaterializeTextTypeHelper(!$usr ? "Este campo es provisional y sera rellenado automaticamente a la hora del guardado" : "")
        ]);
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
        $builder->add("curp", TextType::class, [
            "attr" => [
                "pattern" => "^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$"
            ]
        ]);
        $builder->add("correo", EmailType::class, [
            "attr" => [
                "col" => "s12 m6"
            ]
        ]);
        $builder->add("telefono", TextType::class, [
            "label" => "Teléfono",
            "attr" => [
                "pattern" => "\d{10}",
                "col" => "s12 m6"
            ]
        ]);
        $builder->add("roles", HiddenType::class, [
            "data" => '["ROLE_ALUMNO"]'
        ]);
        $builder->add("password", HiddenType::class, [
            "data" => PasswordGenerator::randomPassword()
        ]);
        $builder->add("createdAt", HiddenType::class, [
            "data" => "now"
        ]);
    }
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            "class" => Usuario::class,
            "matriculaProvisional" => ""
        ]);
    }
    public function getBlockPrefix()
    {
        return "";
    }
}