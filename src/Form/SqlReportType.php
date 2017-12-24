<?php

namespace Eziat\SqlReportBundle\Form;

use Eziat\SqlReportBundle\Entity\SqlReport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SqlReportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title', null, [
                    'required' => true,
                    'label'    => "Title",
                    'attr'     => [
                        'maxlength' => 164.,
                    ],
                ]
            )
            ->add(
                'description', TextareaType::class, [
                    'label'    => "Description",
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add(
                'query', TextareaType::class, [
                    'label'    => "Query",
                    'required' => true,
                ]
            )
            ->add(
                'headers', TextareaType::class, [
                    'label'    => "Headers (separate with ,)",
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add(
                'projection', TextareaType::class, [
                    'label'    => "Projection expression (separate with ,)",
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add(
                'type', ChoiceType::class, [
                    'choices' => [
                        'SQL' => SqlReport::SQL,
                        'DQL' => SqlReport::DQL,
                    ],
                    'label'   => 'Type',
                ]
            )
            ->add(
                'sortBy', null, [
                    'label'    => "Order (number)",
                    'required' => false,
                ]
            )
            ->add(
                'active', CheckboxType::class, [
                    'label'    => 'Active',
                    'required' => false,
                ]
            )
            ->add(
                'reportGroup', null, [
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add('submit', SubmitType::class);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Eziat\SqlReportBundle\Entity\SqlReport',
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'eziat_sql_report_bundle_sqlreport';
    }
}
