<?php

namespace Oro\Bundle\LayoutBundle\Layout\Extension;

use Oro\Component\Layout\ContextConfiguratorInterface;
use Oro\Component\Layout\ContextInterface;

class ExpressionContextConfigurator implements ContextConfiguratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureContext(ContextInterface $context)
    {
        $context->getResolver()
            ->setDefaults(
                [
                    'expressions_evaluate' => true
                ]
            )
            ->setDefined(['expressions_encoding'])
            ->setAllowedTypes(
                [
                    'expressions_evaluate' => 'bool',
                    'expressions_encoding' => ['string', 'null']
                ]
            );
    }
}
