<?php

namespace WebThumbnailer\Finder;

use PHPUnit\Framework\TestCase;

/**
 * Class DefaultFinderTest
 *
 * @package WebThumbnailer\Finder
 */
class DefaultFinderTest extends TestCase
{
    /**
     * PHP builtin local server URL.
     */
    const LOCAL_SERVER = 'http://localhost:8081/';

    /**
     * Test the default finder with URL which match an image (.png).
     */
    public function testDefaultFinderImage()
    {
        $url = 'http://domains.tld/image.png';
        $finder = new DefaultFinder('', $url, [], []);
        $this->assertEquals($url, $finder->find());

        $url = 'http://domains.tld/image.JPG';
        $finder = new DefaultFinder('', $url, [], []);
        $this->assertEquals($url, $finder->find());

        $url = 'http://domains.tld/image.svg';
        $finder = new DefaultFinder('', $url, [], []);
        $this->assertEquals($url, $finder->find());
    }

    /**
     * Test the default finder with URL which does NOT match an image.
     */
    public function testDefaultFinderNotImage()
    {
        $file = __DIR__ . '/../workdir/nope';
        touch($file);
        $finder = new DefaultFinder('', $file, [], []);
        $this->assertFalse($finder->find());
        @unlink($file);
    }

    /**
     * Test the default finder downloading an image without extension.
     */
    public function testDefautFinderRemoteImage()
    {
        $file = __DIR__ . '/../workdir/image';
        // From http://php.net/imagecreatefromstring
        $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        file_put_contents($file, base64_decode($data));
        $finder = new DefaultFinder('', $file, null, null);
        $this->assertEquals($file, $finder->find());
        @unlink($file);
    }

    /**
     * Test the default finder trying to find an open graph link.
     */
    public function testDefaultFinderOpenGraph()
    {
        $url = __DIR__ . '/../resources/default/le-monde.html';
        $expected = 'https://img.lemde.fr/2016/10/21/107/0/1132/566/1440/720/60/0/fe3b107_3522-d2olbw.y93o25u3di.jpg';
        $finder = new DefaultFinder('', $url, null, null);
        $this->assertEquals($expected, $finder->find());
    }

    /**
     * Test the default finder trying to find an open graph link.
     */
    public function testDefaultFinderOpenGraphRemote()
    {
        $url = self::LOCAL_SERVER . 'default/le-monde.html';
        $expected = 'https://img.lemde.fr/2016/10/21/107/0/1132/566/1440/720/60/0/fe3b107_3522-d2olbw.y93o25u3di.jpg';
        $finder = new DefaultFinder('', $url, null, null);
        $this->assertEquals($expected, $finder->find());
    }

    /**
     * Test the default finder trying to find an image mime-type.
     */
    public function testDefaultFinderImageMimetype()
    {
        $url = self::LOCAL_SERVER . 'default/image-mimetype.php';
        $expected = $url;
        $finder = new DefaultFinder('', $url, null, null);
        $this->assertEquals($expected, $finder->find());
    }

    /**
     * Test the default finder finding a non 200 status code.
     */
    public function testDefaultFinderStatusError()
    {
        $url = self::LOCAL_SERVER . 'default/status-ko.php';
        $finder = new DefaultFinder('', $url, null, null);
        $this->assertFalse($finder->find());
    }

    /**
     * Test getName().
     */
    public function testGetName()
    {
        $finder = new DefaultFinder('', '', [], []);
        $this->assertEquals('default', $finder->getName());
    }
}
