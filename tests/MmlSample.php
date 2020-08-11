<?php
class MmlSample
{
    public static $penguin = [
        "Title" => "ICE BALLER - Penguin",
        "Composer" => "Alma",
        "Arranger" => "@MIRROR_",
        "Tracks" => [
            0 => "t120",
            1 => "l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr",
            2 => "l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed",
            3 => "l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba",
            ]
        ];

    public static $penguin_json = '{
        "Title": "ICE BALLER - Penguin",
        "Composer": "Alma",
        "Arranger": "@MIRROR_",
        "Tracks": {
            "0": "t120",
            "1": "l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr",
            "2": "l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed",
            "3": "l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba"
            }
        }';

    public static $penguin_text = <<<'EOD'
#Title ICE BALLER - Penguin
#Composer Alma
#Arranger @MIRROR_
TR0 t120
TR1 l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr
TR2 l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed
TR3 l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba
EOD;

    public static $penguin_with_comment = <<<'EOD'
#Title ICE BALLER - Penguin
#Composer Alma
#Arranger @MIRROR_
TR0 t120
TR1 l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr
/*
TR2 l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed
TR3 l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba
*/
EOD;

    public static $penguin_with_line_comment1 = <<<'EOD'
#Title ICE BALLER - Penguin
#Composer Alma
#Arranger @MIRROR_
TR0 t120
TR1 l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr
//TR2 l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed
TR3 l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba
EOD;

    public static $penguin_with_line_comment2 = <<<'EOD'
#Title ICE BALLER - Penguin
#Composer Alma
#Arranger @MIRROR_
TR0 t120
#TR1 l8 Lo6rgggggab>c<afarab>cd<gr>frfede<g>cerrrrr<gggggab>c<a>cfrfffggggggggfffeerrr
TR2 l8 Lo4cc<g>ccc<g>cffcfffcfgbb>ddd<bgcc<g>ccc<g>ccc<g>ccc<g>cffffg+g+g+g+ggb>d<ggb>d<ccefffed
TR3 l8 Lo6reeeeefgafcfrfgabdr>drdc<b>c<da>crrrrr<eeeeefgafa>crcccdededef<b>c<gb>ccc<ba
EOD;
}
