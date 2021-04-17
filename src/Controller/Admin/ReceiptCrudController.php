<?php

namespace App\Controller\Admin;

use App\Entity\Receipt;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReceiptCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Receipt::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt', 'Passée le'),
            TextField::new('user.getFullName', 'Client'),
            MoneyField::new('total')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0, 
                'Payée' => 1, 
                'Préparation en cours' => 2, 
                'Livraison en cours' => 3
                ]),
            ArrayField::new('orderDetails', 'Produits achetées')->hideOnIndex()
        ];
    }
    
}
