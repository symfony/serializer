<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Mapping\Loader;

use Symfony\Component\Serializer\Exception\MappingException;
use Symfony\Component\Serializer\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Tests\Fixtures\VoidNeverReturnTypeDummy;

class AttributeLoaderWithAttributesTest extends AttributeLoaderTestCase
{
    protected function createLoader(): AttributeLoader
    {
        return new AttributeLoader();
    }

    protected function getNamespace(): string
    {
        return 'Symfony\Component\Serializer\Tests\Fixtures\Attributes';
    }

    public function testLoadWithInvalidAttribute()
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Could not instantiate attribute "Symfony\Component\Serializer\Attribute\Groups" on "Symfony\Component\Serializer\Tests\Fixtures\Attributes\BadAttributeDummy::myMethod()".');

        $classMetadata = new ClassMetadata($this->getNamespace().'\BadAttributeDummy');

        $this->loader->loadClassMetadata($classMetadata);
    }

    public function testSkipVoidNeverReturnTypeAccessors()
    {
        $classMetadata = new ClassMetadata(VoidNeverReturnTypeDummy::class);
        $this->loader->loadClassMetadata($classMetadata);

        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $this->assertArrayHasKey('normalProperty', $attributesMetadata);
        $this->assertArrayNotHasKey('voidProperty', $attributesMetadata);
        $this->assertArrayNotHasKey('neverProperty', $attributesMetadata);
    }
}
