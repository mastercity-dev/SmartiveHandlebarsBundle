<?php

namespace Smartive\HandlebarsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

class HelperCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('smartive_handlebars.templating.renderer')) {
            return;
        }

        $definition = $container->findDefinition(
            'smartive_handlebars.templating.renderer'
        );

        try {
            $cacheClass = $container->getParameter('smartive_handlebars.templating.cache_service_id');
            if ($cacheClass !== null) {
                $definition->addMethodCall("setCache", [ new Reference($cacheClass) ]);
            }
        } catch(ParameterNotFoundException $e) {}


        $taggedHelpers = $container->findTaggedServiceIds(
            'smartive_handlebars.helper'
        );

        foreach ($taggedHelpers as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addHelper',
                    array($attributes['alias'], new Reference($id))
                );
            }
        }


    }
}
