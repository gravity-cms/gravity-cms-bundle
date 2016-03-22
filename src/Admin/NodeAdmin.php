<?php

namespace Gravity\CmsBundle\Admin;

use Gravity\CmsBundle\Entity\Node;
use Gravity\CmsBundle\Field\Admin\AbstractFieldableAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class NodeAdmin extends AbstractFieldableAdmin
{

    public function getNewInstance()
    {
        /** @var Node $entity */
        $className     = $this->getClass();
        $entity        = new $className();
        $fieldMappings = $this->fieldManager->getEntityFieldMapping($this->getClass());


        foreach ($fieldMappings as $field => $fieldMapping) {
            $fieldDefinition = $this->fieldManager->getFieldDefinition($fieldMapping['type']);

            $optionsResolver = $this->fieldManager->createFieldOptionsResolver();
            $fieldDefinition->setOptions($optionsResolver);
            $resolvedOptions = $optionsResolver->resolve($fieldMapping['options']);

            if ($fieldMapping['dynamic'] && $resolvedOptions['limit'] == 1) {
                $fieldClass = $fieldDefinition->getEntityClass();
                if ($fieldClass) {
                    $entity->{"set{$field}"}(
                        new $fieldClass()
                    );
                }
            }

        }

        return $entity;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'GravityCmsBundle:Node:edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->with('Content')
            ->add('title')
        ->end();

        $formMapper->with('Publishing')
            ->add(
                'published',
                'checkbox',
                [
                    'required' => false,
                ]
            )
            ->add(
                'publishedFrom',
                'sonata_type_datetime_picker',
                [
                    'required' => false,
                ]
            )
            ->add(
                'publishedTo',
                'sonata_type_datetime_picker',
                [
                    'required' => false,
                ]
            )
        ->end();

        parent::configureFormFields($formMapper);
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('published')
            ->add('publishedFrom')
            ->add('publishedTo')
            ->add('createdOn')
            ->add('editedOn')
            ->add('deletedOn');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('path', 'url')
            ->add('published')
            ->add('publishedFrom')
            ->add('publishedTo')
            ->add('createdOn')
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show'   => [],
                        'edit'   => [],
                        'delete' => [],
                    ]
                ]
            );
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('published')
            ->add('publishedFrom')
            ->add('publishedTo')
            ->add('createdOn')
            ->add('editedOn')
            ->add('deletedOn');
    }
}
