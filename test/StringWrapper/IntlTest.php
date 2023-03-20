<?php

declare(strict_types=1);

namespace LaminasTest\Stdlib\StringWrapper;

use Laminas\Stdlib\Exception;
use Laminas\Stdlib\StringWrapper\Intl;

use function array_shift;
use function extension_loaded;

class IntlTest extends CommonStringWrapperTestCase
{
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            try {
                new Intl('utf-8');
                $this->fail('Missing expected Laminas\Stdlib\Exception\ExtensionNotLoadedException');
            } catch (Exception\ExtensionNotLoadedException) {
                $this->markTestSkipped('Missing ext/intl');
            }
        }

        parent::setUp();
    }

    /**
     * @param null|string $encoding
     * @param null|string $convertEncoding
     * @return Intl|false
     */
    protected function getWrapper($encoding = null, $convertEncoding = null)
    {
        if ($encoding === null) {
            $supportedEncodings = Intl::getSupportedEncodings();
            $encoding           = array_shift($supportedEncodings);
        }

        self::assertIsString($encoding);

        if (! Intl::isSupported($encoding, $convertEncoding)) {
            return false;
        }

        $wrapper = new Intl();
        $wrapper->setEncoding($encoding, $convertEncoding);
        return $wrapper;
    }
}
