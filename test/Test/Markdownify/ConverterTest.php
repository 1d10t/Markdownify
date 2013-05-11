<?php

/* This file is part of the Markdownify project, which is under LGPL license */

namespace Test\Markdownify;

use Markdownify\Converter;

require_once(__DIR__ . '/../../../vendor/autoload.php');

class ConverterTest extends \PHPUnit_Framework_TestCase
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $converter;


    /* UTILS
     *************************************************************************/
    public function setUp()
    {
        $this->converter = new Converter;
    }


    /* HEADING TEST METHODS
     *************************************************************************/
    /**
     * @dataProvider providerHeadingConversion
     */
    public function testHeadingConversion($level, $attributes=array())
    {
        $innerHTML = 'Heading '.$level;
        $md = str_pad('', $level, '#').' '.$innerHTML;
        $html = '<h'.$level.'>'.$innerHTML.'</h'.$level.'>';
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    /**
     * @dataProvider providerHeadingConversion
     */
    public function testHeadingConversion_withIdAttribute($level)
    {
        $innerHTML = 'Heading '.$level;
        $attributesHTML = ' id="idAttribute"';
        $md = '<h'.$level.' '.$attributesHTML.'>'.PHP_EOL
            .'  '.$innerHTML.PHP_EOL
            .'</h'.$level.'>';
        $html = '<h'.$level.' '.$attributesHTML.'>'.$innerHTML.'</h'.$level.'>';
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerHeadingConversion()
    {
        return array(
            array(1),
            array(2),
            array(3),
            array(4),
            array(5),
            array(6)
        );
    }


    /* ESCAPE TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerAutoescapeConversion
     */
    public function testAutoescapeConversion($html)
    {
        $this->assertEquals(html_entity_decode($html), $this->converter->parseString($html));
    }

    public function providerAutoescapeConversion()
    {
        return array(
            array('AT&amp;T'),
            array('4 &lt; 5'),
            array('&copy;')
        );
    }


    /* BLOCKQUOTE TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerBlockquoteConversion
     */
    public function testBlockquoteConversion($html, $md)
    {
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerBlockquoteConversion()
    {
        $data = array();
        $data['simple']['html'] = '<blockquote>blockquoted text goes here</blockquote>';
        $data['simple']['md'] = '> blockquoted text goes here';
        $data['paragraphs']['html'] = '<blockquote><p>paragraph1</p><p>paragraph2</p></blockquote>';
        $data['paragraphs']['md'] = '> paragraph1
> 
> paragraph2';
        $data['cascade']['html'] = '<blockquote><blockquote>cascading blockquote</blockquote></blockquote>';
        $data['cascade']['md'] = '> > cascading blockquote';
        $data['container']['html'] = '<blockquote><h2>This is a header.</h2></blockquote>';
        $data['container']['md'] = '> ## This is a header.';
        return $data;
    }


    /* LISTS TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerListConversion
     */
    public function testListConversion($html, $md)
    {
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerListConversion()
    {
        $data = array();
        $data['ordered']['html'] = '<ol><li>Bird</li><li>McHale</li><li>Parish</li></ol>';
        $data['ordered']['md'] = '1.  Bird
2.  McHale
3.  Parish';
        $data['unordered']['html'] = '<ul><li>Red</li><li>Green</li><li>Blue</li></ul>';
        $data['unordered']['md'] = '*   Red
*   Green
*   Blue';
        $data['paragraph']['html'] = '<ul><li><p>Bird</p></li><li><p>Magic</p></li></ul>';
        $data['paragraph']['md'] = '*   Bird

*   Magic';

        return $data;
    }


    /* CODE TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerCodeConversion
     */
    public function testCodeConversion($html, $md)
    {
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerCodeConversion()
    {
        $data = array();
        $data['inline']['html'] = '<p>Use the <code>printf()</code> function.</p>';
        $data['inline']['md'] = 'Use the `printf()` function.';
        $data['backtick']['html'] = '<p>A single backtick in a code span: <code>`</code></p>';
        $data['backtick']['md'] = 'A single backtick in a code span: `` ` ``';
        $data['double-backtick']['html'] = '<p>A backtick-delimited string in a code span: <code>`foo`</code></p>';
        $data['double-backtick']['md'] = 'A backtick-delimited string in a code span: `` `foo` ``';
        $data['inline-html']['html'] = '<p>Please don\'t use any <code>&lt;blink&gt;</code> tags.</p>';
        $data['inline-html']['md'] = 'Please don\'t use any `<blink>` tags.';
        $data['pre']['html'] = '<p>This is a normal paragraph:</p><pre><code>This is a code block.</code></pre>';
        $data['pre']['md'] = 'This is a normal paragraph:

    This is a code block.';
        $data['pre-indentation']['html'] = '<p>Here is an example of AppleScript:</p><pre><code>tell application "Foo"
    beep
end tell
</code></pre>';
        $data['pre-indentation']['md'] = 'Here is an example of AppleScript:

    tell application "Foo"
        beep
    end tell';
        $data['pre-html']['html'] = '<pre><code>&lt;div class="footer"&gt;
    &amp;copy; 2004 Foo Corporation
&lt;/div&gt;
</code></pre>';
        $data['pre-html']['md'] = '    <div class="footer">
        &copy; 2004 Foo Corporation
    </div>';

        return $data;
    }


    /* LINK TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerLinkConversion
     */
    public function testLinkConversion($html, $md)
    {
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerLinkConversion()
    {
        $data = array();
        $data['url']['html'] = '<p><a href="http://example.net/">This link</a> has no title attribute.</p>';
        $data['url']['md'] = '[This link][1] has no title attribute.

 [1]: http://example.net/';
        $data['url-title']['html'] = '<p>This is <a href="http://example.com/" title="Title">an example</a> inline link.</p>';
        $data['url-title']['md'] = 'This is [an example][1] inline link.

 [1]: http://example.com/ "Title"';
        $data['image']['html'] = '<img src="/path/to/img.jpg" alt="Alt text" />';
        $data['image']['md'] = '![Alt text][1]

 [1]: /path/to/img.jpg';
        $data['image-title']['html'] = '<img src="/path/to/img.jpg" alt="Alt text" title="Optional title attribute" />';
        $data['image-title']['md'] = '![Alt text][1]

 [1]: /path/to/img.jpg "Optional title attribute"';

        return $data;
    }


    /* EMPHASIS TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerEmphasisConversion
     */
    public function testEmphasisConversion($html, $md)
    {
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerEmphasisConversion()
    {
        $data = array();
        $data['strong']['html'] = '<strong>double asterisks</strong>';
        $data['strong']['md'] = '**double asterisks**';
        $data['strong-backslash']['html'] = '**double asterisks**';
        $data['strong-backslash']['md'] = '\*\*double asterisks\*\*';
        $data['strong-backslash2']['html'] = '__double asterisks__';
        $data['strong-backslash2']['md'] = '\_\_double asterisks\_\_';
        $data['em']['html'] = '<em>single asterisks</em>';
        $data['em']['md'] = '*single asterisks*';
        $data['em-backslash']['html'] = '*single asterisks*';
        $data['em-backslash']['md'] = '\*single asterisks\*';
        $data['em-backslash2']['html'] = '_single asterisks_';
        $data['em-backslash2']['md'] = '\_single asterisks\_';

        return $data;
    }


    /* RULES TEST METHODS
     *************************************************************************/

    /**
     * @dataProvider providerRulesConversion
     */
    public function testRulesConversion($html, $md)
    {
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function providerRulesConversion()
    {
        $data = array();
        $data['hr']['html'] = '<hr>';
        $data['hr']['md'] = '* * *';
        $data['escape-']['html'] = '-----------------------------------';
        $data['escape-']['md'] = '\---\---\---\---\---\---\---\---\---\---\-----';
        $data['escape-']['html'] = '*****************';
        $data['escape-']['md'] = '\***\***\***\***\*****';

        return $data;
    }

}