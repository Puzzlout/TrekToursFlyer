<?php
namespace Tests\AppBundle\Form\Type;

use AppBundle\Form\Type\CustomerInfoRequestType;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class CustomerInfoRequestTypeTest extends TypeTestCase
{

    private $validator;

    protected function getExtensions()
    {
        $this->validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')->getMock();
        $this->validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));
        $metadata = $this->getMockBuilder('Symfony\Component\Validator\Mapping\ClassMetadata')
            ->disableOriginalConstructor()->getMock();
        $this->validator
           ->method('getMetadataFor')
           ->will($this->returnValue($metadata));
        return array(
            new ValidatorExtension($this->validator),
        );
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testSubmitValidData($data)
    {

        $form = $this->factory->create(CustomerInfoRequestType::class);

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'email' => 'test@test.com',
                    'first_name' => 'Tèst',
                    'last_name' => 'Testá',
                    'phone_number' => '+111222333444',
                    'message' => 'This is the test message',
                    'send_copy_to_client' => 1
                )
            ),
            array(
                'data' => array(
                    'email' => 'aaaa',
                    'first_name' => 'Test',
                    'message' => 'This is the test message number 2'
                )
            )
        );
    }
}