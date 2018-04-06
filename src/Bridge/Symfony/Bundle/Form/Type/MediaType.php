<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Form\Type;

use Damax\Media\Bridge\Symfony\Bundle\Form\DataTransformer\MediaCollectionToArrayTransformer;
use Damax\Media\Bridge\Symfony\Bundle\Form\DataTransformer\MediaToIdTransformer;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Type\Types;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\EventListener\MergeDoctrineCollectionListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\EventListener\MergeCollectionListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    private $repository;
    private $types;

    public function __construct(MediaRepository $repository, Types $types)
    {
        $this->repository = $repository;
        $this->types = $types;
    }

    public function getBlockPrefix(): string
    {
        return 'damax_media';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder
                ->addEventSubscriber(new MergeDoctrineCollectionListener())
                ->addEventSubscriber(new MergeCollectionListener(true, true))
                ->addViewTransformer(new CollectionToArrayTransformer())
                ->addViewTransformer(new MediaCollectionToArrayTransformer(new MediaToIdTransformer($this->repository)))
            ;
        } else {
            $builder->addViewTransformer(new MediaToIdTransformer($this->repository), true);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('type')
            ->setAllowedValues('type', $this->types->names())
            ->setDefaults([
                'required' => false,
                'multiple' => false,
                'image_params' => ['w' => 240, 'h' => 180],
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['image_params'] = $options['image_params'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['type_name'] = $options['type'];
        $view->vars['type'] = $this->types->definition($options['type']);
    }
}
