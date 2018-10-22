<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function mainMenu(array $options)
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => ['class' => 'navbar-nav mr-auto']
        ]);
        $menu->addChild('Стартовая', ['route' => 'homepage']);
        $menu->addChild('Контакты', ['route' => 'feedback']);

        return $menu;
    }

}
