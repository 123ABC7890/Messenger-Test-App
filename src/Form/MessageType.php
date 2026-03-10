<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'choice_label' => static function (User $user): string {
                    return $user->getFullName() ?: $user->getEmail() ?: 'Gebruiker';
                },
                'query_builder' => $options['recipient_query_builder'],
                'placeholder' => 'Kies ontvanger',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Bericht',
                'attr' => ['rows' => 6],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'recipient_query_builder' => null,
        ]);
    }
}
