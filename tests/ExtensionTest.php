<?php

use voku\helper\HtmlMin;
use voku\twig\MinifyHtmlExtension;

/**
 * Class ExtensionTest
 */
class ExtensionTest extends \PHPUnit\Framework\TestCase
{
  /**
   * @return array
   */
  public function htmlProvider()
  {
    $original = '<html> <p> x  x </p> </html>';
    $compressed = '<html><p> x x';

    $testData = array();
    $testMethods = array(
        'Twig tag'      => '{% htmlcompress %}%s{% endhtmlcompress %}',
        'Twig function' => "{{ htmlcompress('%s') }}",
        'Twig filter'   => "{{ '%s' | htmlcompress }}",
    );

    foreach ($testMethods as $testMethod => $testTemplate) {
      $testData[$testMethod] = array(
          str_replace('%s', $original, $testTemplate),
          $original,
          $compressed,
      );
    }

    return $testData;
  }

  /**
   * @dataProvider htmlProvider
   *
   * @param $template
   * @param $original
   * @param $compressed
   */
  public function testExtensionMethod($template, $original, $compressed)
  {
    $loader = new \Twig_Loader_Array(array('test' => $template));
    $twig = new \Twig_Environment($loader);
    $minifier = new HtmlMin();
    $twig->addExtension(new MinifyHtmlExtension($minifier));
    self::assertEquals($compressed, $twig->render('test'));
  }

  /**
   * @dataProvider htmlProvider
   *
   * @param $template
   * @param $original
   * @param $compressed
   */
  public function testForceCompressionWhenDebug($template, $original, $compressed)
  {
    $loader = new \Twig_Loader_Array(array('test' => $template));
    $twig = new \Twig_Environment($loader, array('debug' => true));
    $minifier = new HtmlMin();
    $twig->addExtension(new MinifyHtmlExtension($minifier, true));

    // Assert that compression took place
    self::assertEquals($compressed, $twig->render('test'));
  }

  /**
   * @dataProvider htmlProvider
   *
   * @param $template
   * @param $original
   * @param $compressed
   */
  public function testNoCompressionWhenDebug($template, $original, $compressed)
  {
    $loader = new \Twig_Loader_Array(array('test' => $template));
    $twig = new \Twig_Environment($loader, array('debug' => true));
    $minifier = new HtmlMin();
    $twig->addExtension(new MinifyHtmlExtension($minifier));

    // Assert no compression took place
    self::assertEquals($original, $twig->render('test'));
  }
}
