<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints;

class CustomerInfoRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 3, 'max' => 100)),
                    new Constraints\Regex(array('pattern' => '/^[\p{L}]+$/ui')))
                ))
            ->add('last_name', TextType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 3, 'max' => 100)),
                    new Constraints\Regex(array('pattern' => '/^[\p{L}]+$/ui')))
            ))
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('max' => 100)))
            ))
            ->add('phone_number', TextType::class, array(
                'constraints' => array(
                    new Constraints\Regex(array('pattern' => '/^\+\d+$/'))),
                'attr' => array('placeholder' => '+XXXYYYYYYYYY')
            ))
            ->add('message', TextareaType::class,array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Regex(array('pattern' => '/^(?!.*<[^>]+>).*/')))
            ))
            ->add('send', SubmitType::class)
        ;
    }
}