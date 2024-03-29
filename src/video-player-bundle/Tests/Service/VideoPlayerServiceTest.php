<?php

/*
 * This file is part of the VideoPlayerBundle package.
 *
 * (c) Life in the cloud <http://lifeinthecloud.fr/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VideoPlayerBundle\Tests\Service;

use VideoPlayerBundle\Service\VideoPlayerService;
use VideoPlayerBundle\Server\ListServer;

/**
 * Class VideoPlayerServiceTest
 * @author Antoine DARCHE <darche.antoine@gmail.com>
 * @version 1.0
 * @package LITC\VideoPlayerBundle\Tests\Service
 */
class VideoPlayerServiceTest extends \PHPUnit_Framework_TestCase
{
    /* @var VideoPlayerService */
    private $videoPlayerService = null;

    /* @var array default youtube configuration */
    private static $defaultYoutubeSettings = array(
        'server' => array(
            'id'        => '7qfxCvwyxms'
        ),
    );

    /* @var array default vimeo configuration */
    private static $defaultVimeoSettings = array(
        'server' => array(
            'id'        => '51140690'
        )
    );

    /* @var array default dailymotion configuration */
    private static $defaultDailymotionSettings = array(
        'server' => array(
            'id'        => 'x385_saschienne-unknown-unknown-album_fun'
        ),
    );

    /* @var array default player settings */
    private static $defaultPlayerSettings =  array(
        'width'     => 800,
        'height'    => 500
    );

    /**
     * Initialize VideoPlayerService
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     */
    public function __construct()
    {
        $this->videoPlayerService = new VideoPlayerService();
    }

    /**
     * Initialize tests
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     *
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        // Initialize default server
        self::setDefaultVideoServer(self::$defaultYoutubeSettings, 'Youtube');
        self::setDefaultVideoServer(self::$defaultVimeoSettings, 'Vimeo');
        self::setDefaultVideoServer(self::$defaultDailymotionSettings, 'Dailymotion');

        // Initialize player settings
        self::$defaultYoutubeSettings['player']     = self::$defaultPlayerSettings;
        self::$defaultVimeoSettings['player']       = self::$defaultPlayerSettings;
        self::$defaultDailymotionSettings['player'] = self::$defaultPlayerSettings;
    }

    /**
     * Change default video server settings
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     *
     * @param array $defaultSettingsServer
     * @param string $server
     * @throws \Exception
     */
    private function setDefaultVideoServer(&$defaultSettingsServer, $server)
    {
        if(empty($defaultSettingsServer) && !is_array($defaultSettingsServer)) {
            throw new \Exception('Default server parameters must be an array');
        }

        if(empty($server)) {
            throw new \Exception('Parameter server can not be empty');
        }

        $defaultSettingsServer['server']['server'] = ListServer::getServerId($server);
    }

    /**
     * Test player
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     */
    public function testPlayYoutubeVideo()
    {
        $videoHtml = $this->videoPlayerService
            ->play(self::$defaultYoutubeSettings);
        
	    $this->assertNotNull($videoHtml);
    }

    /**
     * Test player
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     */
    public function testPlayVimeoVideo()
    {
        $videoHtml = $this->videoPlayerService
            ->play(self::$defaultVimeoSettings);

	    $this->assertNotNull($videoHtml);
    }

    /**
     * Test player
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     */
    public function testPlayDailymotionVideo()
    {
        $videoHtml = $this->videoPlayerService
            ->play(self::$defaultDailymotionSettings);

	    $this->assertNotNull($videoHtml);
    }

    /**
     * Test to string function
     *
     * @author Antoine DARCHE <darche.antoine@gmail.com>
     */
    public function testToString()
    {
        $this->videoPlayerService
            ->setParameters(self::$defaultYoutubeSettings);

        $videoHtml = $this->videoPlayerService
            ->__tostring();

	    $this->assertNotNull($videoHtml);
    }
}