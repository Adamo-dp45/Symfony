<?php

namespace App\Controller;

use App\Controller\Admin\EasyDashbordController;
use App\Controller\Admin\RecipeCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use Override;

final class RecipeCrudControllerTest extends AbstractCrudTestCase
{
    #[Override]
    protected function getControllerFqcn(): string
    {
        return RecipeCrudController::class;
    }

    #[Override]
    protected function getDashboardFqcn(): string
    {
        return EasyDashbordController::class;
    }

    public function testIndexPage()
    {
        $this->client->request('GET', $this->generateIndexUrl());
        static::assertResponseIsSuccessful();
    }
}