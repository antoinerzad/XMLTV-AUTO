<?php

declare(strict_types=1);

namespace racacax\XmlTv\Component\Provider;

use GuzzleHttp\Client;
use racacax\XmlTv\Component\ProviderInterface;
use racacax\XmlTv\Component\ResourcePath;
use racacax\XmlTv\ValueObject\Channel;
use racacax\XmlTv\ValueObject\Program;

/*
 * @author Racacax
 * @version 0.1 : 05/09/2021
 */
class TV5 extends AbstractProvider implements ProviderInterface
{
    public function __construct(Client $client, ?float $priority = null)
    {
        parent::__construct($client, ResourcePath::getInstance()->getChannelPath('channels_tv5.json'), $priority ?? 0.6);
    }

    public function constructEPG(string $channel, string $date)
    {
        $channelObj = parent::constructEPG($channel, $date);

        if (!$this->channelExists($channel)) {
            return false;
        }
        $content = $this->getContentFromURL($this->generateUrl($channelObj, new \DateTimeImmutable($date)));
        $json = json_decode($content, true);
        if (!@isset($json['data'][0])) {
            return false;
        }
        foreach ($json['data'] as $val) {
            $program = new Program(strtotime($val['utcstart'].'+00:00'), strtotime($val['utcend'].'+00:00'));
            $program->addTitle($val['title']);
            $program->addDesc((!empty($val['description'])) ? $val['description'] : 'Pas de description');
            $program->addCategory($val['category']);
            $program->setIcon(!empty($val['image']) ? ''.$val['image'] : '');
            if (isset($val['season'])) {
                if ($val['season'] =='') {
                    $val['season'] ='1';
                }
                if ($val['episode'] =='') {
                    $val['episode'] ='1';
                }
                $program->addSubtitle($val['episode_name']);
                $program->setEpisodeNum($val['season'], $val['episode']);
            }
            $channelObj->addProgram($program);
        }

        return $channelObj;
    }

    public function generateUrl(Channel $channel, \DateTimeImmutable $date): string
    {
        $channel_id = $this->channelsList[$channel->getId()];
        $start = $date->format('Y-m-d\T00:00:00');
        $end = $date->modify('+1 days')->format('Y-m-d\T00:00:00');

        return 'https://bo-apac.tv5monde.com/tvschedule/full?start='.$start.'&end='.$end.'&key='.$channel_id.'&timezone=Europe/Paris&language=EN';
    }
}
