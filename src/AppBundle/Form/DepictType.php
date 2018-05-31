<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DepictType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('qwd',        IntegerType::class, array("required" => false))
            ->add('labels',     CollectionType::class, array("required" => false, 'entry_type' => TextType::class, "allow_add" => true, "allow_delete" => true))
            ->add('status',     TextType::class, array("required" => true))
            ->add('entity',     \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array('class' => 'AppBundle:Entity', 'required' => true ));
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Depict'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_depict';
    }


}
