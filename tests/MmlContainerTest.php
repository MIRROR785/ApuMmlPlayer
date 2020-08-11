<?php
require_once('MmlSample.php');

use PHPUnit\Framework\TestCase;
use MIRROR785\ApuMmlPlayer\Mml\MmlContainer;

class MmlContainerTest extends TestCase
{
    public function test_parse() {
        //var_dump(MmlSample::$penguin);
        $container = new MmlContainer(MmlSample::$penguin);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(4, count($container->tracks));

        $this->assertSame('t120'."\n", join($container->tracks[0]));
        $this->assertSame('l8Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr'."\n", join($container->tracks[1]));
        $this->assertSame('l8Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed'."\n", join($container->tracks[2]));
        $this->assertSame('l8Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba'."\n", join($container->tracks[3]));
    }

    public function test_json_parse() {
        //var_dump(MmlSample::$penguin_json);
        //echo json_encode(MmlSample::$penguin, true);
        $container = new MmlContainer(MmlSample::$penguin_json);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(4, count($container->tracks));

        $this->assertSame('t120'."\n", join($container->tracks[0]));
        $this->assertSame('l8Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr'."\n", join($container->tracks[1]));
        $this->assertSame('l8Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed'."\n", join($container->tracks[2]));
        $this->assertSame('l8Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba'."\n", join($container->tracks[3]));
    }

    public function test_text_parse() {
        $container = MmlContainer::parse(MmlSample::$penguin_text);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(4, count($container->tracks));

        $this->assertSame('t120'."\n", join($container->tracks[0]));
        $this->assertSame('l8Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr'."\n", join($container->tracks[1]));
        $this->assertSame('l8Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed'."\n", join($container->tracks[2]));
        $this->assertSame('l8Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba'."\n", join($container->tracks[3]));
    }

    public function test_comment_parse() {
        $container = MmlContainer::parse(MmlSample::$penguin_with_comment);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(2, count($container->tracks));

        $this->assertSame('t120'."\n", join($container->tracks[0]));
        $this->assertSame('l8Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr'."\n", join($container->tracks[1]));
    }

    public function test_line_comment1_parse() {
        $container = MmlContainer::parse(MmlSample::$penguin_with_line_comment1);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(3, count($container->tracks));

        $this->assertSame('t120'."\n", join($container->tracks[0]));
        $this->assertSame('l8Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr'."\n", join($container->tracks[1]));
        $this->assertSame('l8Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba'."\n", join($container->tracks[3]));
    }

    public function test_line_comment2_parse() {
        $container = MmlContainer::parse(MmlSample::$penguin_with_line_comment2);
        //var_dump($container);

        $this->assertSame('ICE BALLER - Penguin', $container->title);
        $this->assertSame('Alma', $container->composer);
        $this->assertSame('@MIRROR_', $container->arranger);
        $this->assertSame(3, count($container->tracks));

        $this->assertSame('t120'."\n", join($container->tracks[0]));
        $this->assertSame('l8Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed'."\n", join($container->tracks[2]));
        $this->assertSame('l8Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba'."\n", join($container->tracks[3]));
    }
}
