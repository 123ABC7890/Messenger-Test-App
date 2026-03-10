<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonate = Action::new('impersonate', 'Inloggen als', 'fas fa-user-secret')
            ->linkToRoute('app_admin_user_impersonate', static fn (User $user): array => ['id' => $user->getId()])
            ->renderAsLink()
            ->asPrimaryAction();

        return $actions
            ->add(Crud::PAGE_INDEX, $impersonate)
            ->add(Crud::PAGE_DETAIL, $impersonate);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('fullName', 'Naam'),
            TextEditorField::new('skills', 'Vaardigheden'),
            ArrayField::new('roles'),
            BooleanField::new('isBlocked', 'Geblokkeerd'),
        ];
    }
}
