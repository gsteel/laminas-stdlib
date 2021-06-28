<?php

declare(strict_types=1);

namespace LaminasTest\Stdlib;

use ArrayObject;
use BadMethodCallException;
use InvalidArgumentException;
use Laminas\Stdlib\Exception;
use LaminasTest\Stdlib\TestAsset\TestOptions;
use LaminasTest\Stdlib\TestAsset\TestOptionsDerived;
use LaminasTest\Stdlib\TestAsset\TestOptionsNoStrict;
use LaminasTest\Stdlib\TestAsset\TestOptionsWithoutGetter;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    public function testConstructionWithArray()
    {
        $options = new TestOptions(['test_field' => 1]);

        self::assertEquals(1, $options->test_field);
    }

    public function testConstructionWithTraversable()
    {
        $config  = new ArrayObject(['test_field' => 1]);
        $options = new TestOptions($config);

        self::assertEquals(1, $options->test_field);
    }

    public function testConstructionWithOptions()
    {
        $options = new TestOptions(new TestOptions(['test_field' => 1]));

        self::assertEquals(1, $options->test_field);
    }

    public function testInvalidFieldThrowsException()
    {
        $this->expectException(BadMethodCallException::class);

        new TestOptions(['foo' => 'bar']);
    }

    public function testNonStrictOptionsDoesNotThrowException()
    {
        self::assertInstanceOf(
            TestOptionsNoStrict::class,
            new TestOptionsNoStrict(['foo' => 'bar'])
        );
    }

    public function testConstructionWithNull()
    {
        self::assertInstanceOf(TestOptions::class, new TestOptions(null));
    }

    public function testUnsetting()
    {
        $options = new TestOptions(['test_field' => 1]);

        self::assertEquals(true, isset($options->test_field));
        unset($options->testField);
        self::assertEquals(false, isset($options->test_field));
    }

    public function testUnsetThrowsInvalidArgumentException()
    {
        $options = new TestOptions();

        $this->expectException(InvalidArgumentException::class);

        unset($options->foobarField);
    }

    public function testGetThrowsBadMethodCallException()
    {
        $options = new TestOptions();

        $this->expectException(BadMethodCallException::class);

        $options->fieldFoobar;
    }

    public function testSetFromArrayAcceptsArray()
    {
        $array   = ['test_field' => 3];
        $options = new TestOptions();

        self::assertSame($options, $options->setFromArray($array));
        self::assertEquals(3, $options->test_field);
    }

    public function testSetFromArrayThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $options = new TestOptions();
        $options->setFromArray('asd');
    }

    public function testParentPublicProperty()
    {
        $options = new TestOptionsDerived(['parent_public' => 1]);

        self::assertEquals(1, $options->parent_public);
    }

    public function testParentProtectedProperty()
    {
        $options = new TestOptionsDerived(['parent_protected' => 1]);

        self::assertEquals(1, $options->parent_protected);
    }

    public function testParentPrivateProperty()
    {
        $this->expectException(Exception\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'The option "parent_private" does not have a callable "setParentPrivate" ("setparentprivate")'
            . ' setter method which must be defined'
        );

        new TestOptionsDerived(['parent_private' => 1]);
    }

    public function testDerivedPublicProperty()
    {
        $options = new TestOptionsDerived(['derived_public' => 1]);

        self::assertEquals(1, $options->derived_public);
    }

    public function testDerivedProtectedProperty()
    {
        $options = new TestOptionsDerived(['derived_protected' => 1]);

        self::assertEquals(1, $options->derived_protected);
    }

    public function testDerivedPrivateProperty()
    {
        $this->expectException(Exception\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'The option "derived_private" does not have a callable "setDerivedPrivate" ("setderivedprivate")'
            . ' setter method which must be defined'
        );

        new TestOptionsDerived(['derived_private' => 1]);
    }

    public function testExceptionMessageContainsActualUsedSetter()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            'The option "foo bar" does not have a callable "setFooBar" ("setfoo bar")'
            . ' setter method which must be defined'
        );

        new TestOptions([
            'foo bar' => 'baz',
        ]);
    }

    /**
     * @group 7287
     */
    public function testIssetReturnsFalseWhenMatchingGetterDoesNotExist()
    {
        $options = new TestOptionsWithoutGetter([
            'foo' => 'bar',
        ]);
        self::assertFalse(isset($options->foo));
    }

    /**
     * @group 7287
     */
    public function testIssetDoesNotThrowExceptionWhenMatchingGetterDoesNotExist()
    {
        $options = new TestOptionsWithoutGetter();

        isset($options->foo);

        $this->addToAssertionCount(1);
    }

    /**
     * @group 7287
     */
    public function testIssetReturnsTrueWithValidDataWhenMatchingGetterDoesNotExist()
    {
        $options = new TestOptions([
            'test_field' => 1,
        ]);
        self::assertTrue(isset($options->testField));
    }
}
