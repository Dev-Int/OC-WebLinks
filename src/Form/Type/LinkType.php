<?php

namespace WebLinks\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of LinkType
 *
 * @author dev-int
 */
class LinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => 'Title',
                'required'    => true,
                'constraints' => new Assert\NotBlank(),
                'label_attr'  => ['class' => 'col-md-4'],
            ])
            ->add('url', UrlType::class, [
                'label'       => 'Url',
                'required'    => false,
                'constraints' => new Assert\Url(),
                'label_attr'  => ['class' => 'col-md-4'],
            ]);
    }

    public function getBlockPrefix()
    {
        return 'link';
    }
}
