<?php
namespace Drupal\Tests\termfilter\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\termfilter\TermfilterReplacement;

class TermFilterTest extends UnitTestCase {

  /**
   * TermfilterReplacement object.
   */
  protected $TermfilterReplacement;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->TermfilterReplacement = $this->getMock('\Drupal\termfilter\TermfilterReplacement', array('getTermByName', 'getUrlByTermId'));

    $this->TermfilterReplacement->expects($this->any())->method('getTermByName')
      ->willReturn([
        '7' => [
          (object) ['name' => 'foo', 'tid' => 7],
        ],
      ]);

    $this->TermfilterReplacement->expects($this->any())->method('getUrlByTermId')
      ->willReturn('<a href="/term/7">foo</a>');

  }

  /**
   * Test the replacement function.
   */
  public function testPerformSubs() {
    $this->assertSame($this->TermfilterReplacement->termfilterPerformSubs($this->getTestText(), $this->getTestTermList()), '<a href="/term/7">foo</a> bar');
  }

  /**
   * Get mocked text data.
   *
   * @return string
   *   Mocked text.
   */
  protected function getTestText() {
    return 'foo bar';
  }

  /**
   * Get mocked List data.
   *
   * @return array
   *   Mocked list.
   */
  protected function getTestTermList() {
    return [
      'foo' => 'tags',
    ];
  }
}
