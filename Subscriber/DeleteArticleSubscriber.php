<?php

namespace DpnOneoffCosts\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteArticleSubscriber implements SubscriberInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'sBasket::sDeleteArticle::after' => 'onDeleteArticleAfter',
        ];
    }

    /**
     * @param \Enlight_Hook_HookArgs $args
     */
    public function onDeleteArticleAfter(\Enlight_Hook_HookArgs $args)
    {
        $basketId = $args->get('id');
        $db = $this->container->get('db');
        $session = $this->container->get('session');
        $db->delete(
            's_order_basket',
            [
                'sessionID = ?' => $session->get('sessionId'),
                'config = ?' => $basketId,
            ]
        );
    }

}