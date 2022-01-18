<?php

declare(strict_types=1);

namespace racacax\XmlTvTest\Integration;

use PHPUnit\Framework\TestCase;
use racacax\XmlTv\Component\ProviderInterface;
use racacax\XmlTv\Configurator;
use racacax\XmlTv\ValueObject\Channel;

class ProvidersTest extends TestCase
{
    /**
     * @dataProvider dataProviderListProvider
     */
    public function testOneChannelOnAllProvider(ProviderInterface $provider): void
    {
        $channels = $provider->getChannelsList();
        $this->assertGreaterThanOrEqual(1, count($channels), 'Provider without channel');
        $channelObj = null;
        foreach ($channels as $channelCode => $_) {
            $channelObj = $provider->constructEPG($channelCode, date('Y-m-d'));
            if (false !== $channelObj && $channelObj->getProgramCount()>0) {
                break;
            }
        }

        $this->assertNotEmpty($channelObj, 'Error on provider : ' .get_class($provider));

        $this->assertSame(Channel::class, get_class($channelObj));
        $this->assertGreaterThan(1, $channelObj->getProgramCount(), 'Channel without programs');
    }


    public function dataProviderListProvider(): \Generator
    {
        $configurator = new Configurator();
        $providers = $configurator->getGenerator()->getProviders();

        foreach ($providers as $provider) {
            yield [$provider];
        }
    }
}
