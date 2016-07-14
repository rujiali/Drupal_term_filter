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
   * Mocked TermfilterHelper class.
   */
  protected $TermfilterHelper;
  
  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->TermfilterHelper = $this->getMock('\Drupal\termfilter\TermfilterHelper');
    
    $this->TermfilterHelper->expects($this->any())->method('getTermByName')
      ->willReturn([
        '1' => [
          (object) ['name' => 'foo', 'tid' => 1],
        ],
      ]);

    $this->TermfilterHelper->expects($this->any())->method('getUrlByTermId')
      ->willReturn('<a href="/term/1">foo</a>');

    $this->TermfilterHelper->expects($this->any())->method('getTermId')
      ->willReturn(1);
    
    $this->TermfilterReplacement = new TermfilterReplacement($this->TermfilterHelper);
  }

  /**
   * Test the replacement function.
   */
  public function testPerformSubs() {
    $this->assertSame($this->TermfilterReplacement->termfilterPerformSubs($this->getTestText(), $this->getTestTermList()), '<a href="/term/1">foo</a> bar');
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
