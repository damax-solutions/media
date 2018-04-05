<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Form\Type;

use Damax\Media\Bridge\Symfony\Bundle\Form\DataTransformer\MediaToIdTransformer;
use Damax\Media\Domain\Model\MediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    private $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            /*
            $builder
                ->addEventSubscriber(new MergeDoctrineCollectionListener())
                ->addEventSubscriber(new MergeCollectionListener(true, true))
                ->addViewTransformer(new CollectionToArrayTransformer())
                ->addViewTransformer(new MediaArrayToIdArrayTransformer($this->mediaRepository))
            ;
            */
        } else {
            $builder->addViewTransformer(new MediaToIdTransformer($this->repository), true);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'multiple' => false,
        ]);
    }
}
