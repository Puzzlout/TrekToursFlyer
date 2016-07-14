<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerInfoRequestType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, array(
                'label' => 'form_input_first_name',
                'constraints' => array(
                    new Constraints\NotBlank(['message' => 'contact_form_blank_first_name']),
                    new Constraints\Length(['min' => 3, 'max' => 100,
                            'minMessage' => 'contact_form_minlength_first_name',
                            'maxMessage' => 'contact_form_maxlength_first_name']
                    ),
                    new Constraints\Regex([
                        'pattern' => '/^[\p{L}]+$/ui',
                        'message' => 'contact_form_regex_first_name']))
            ))
            ->add('last_name', TextType::class, array(
                'label' => 'form_input_last_name',
                'constraints' => array(
                    new Constraints\NotBlank(['message' => 'contact_form_blank_last_name']),
                    new Constraints\Length(['min' => 3, 'max' => 100,
                        'minMessage' => 'contact_form_minlength_last_name',
                        'maxMessage' => 'contact_form_maxlength_last_name',
                    ]),
                    new Constraints\Regex([
                        'pattern' => '/^[\p{L}]+$/ui',
                        'message' => 'contact_form_regex_last_name']))
            ))
            ->add('email', EmailType::class, array(
                'label' => 'form_input_email',
                'constraints' => array(
                    new Constraints\NotBlank(['message' => 'contact_form_blank_email']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'contact_form_maxlength_email']),
                    new Constraints\Regex([
                        'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                        'message' => 'contact_form_regex_email'
                    ]))

            ))
            ->add('send_copy_to_client', CheckboxType::class, array(
                'label' => 'form_input_send_copy_to_client',
                'required' => false,
                'value' => 1
            ))
            ->add('phone_number', TextType::class, array(
                'label' => 'form_input_phone_number',
                'constraints' => array(
                    new Constraints\Regex(['pattern' => '/^\+\d+$/', 'message' => 'contact_form_regex_phone_number'])),
                'attr' => array('placeholder' => '+XXXYYYYYYYYY'),
                'required' => false
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'form_textarea_message',
                'constraints' => array(
                    new Constraints\NotBlank(['message' => 'contact_form_blank_message']),
                    new Constraints\Regex([
                        'pattern' => '/^(?!.*<[^>]+>).*/',
                        'message' => 'contact_form_regex_message']))
            ))
            ->add('send', SubmitType::class, ['label' => 'form_button_send'])
            ->get('send_copy_to_client')->addModelTransformer(new CallbackTransformer(
                function ($sendCopyToClient) {
                    return $sendCopyToClient;
                },
                function ($sendCopyToClient) {
                    return ($sendCopyToClient) ? 1 : 0;
                }
            ));
    }
}