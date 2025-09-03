<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class AttributeMetadataPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('serializer.mapping.attribute_loader')) {
            return;
        }

        $resolve = $container->getParameterBag()->resolveValue(...);
        $taggedClasses = [];
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!$definition->hasTag('serializer.attribute_metadata')) {
                continue;
            }
            if (!$definition->hasTag('container.excluded')) {
                throw new InvalidArgumentException(\sprintf('The resource "%s" tagged "serializer.attribute_metadata" is missing the "container.excluded" tag.', $id));
            }
            $taggedClasses[$resolve($definition->getClass())] = true;
        }

        ksort($taggedClasses);

        if ($taggedClasses) {
            $container->getDefinition('serializer.mapping.attribute_loader')
                ->replaceArgument(1, array_keys($taggedClasses));
        }
    }
}
