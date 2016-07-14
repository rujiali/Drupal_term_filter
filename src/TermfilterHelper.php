<?php
namespace Drupal\termfilter;

use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Core\Url;
use Drupal\Core\Link;

class TermfilterHelper {

  /**
   * Get all terms in given vocabulary.
   *
   * @return array
   *   An array of terms, key by term name.
   */
  public function getTermfilterList() {
    $list = [];

    $vocabName = \Drupal::config('termfilter.settings')->get('vocablist');
    $vocabulary = Vocabulary::load($vocabName);
    $container = \Drupal::getContainer();
    $terms = $container->get('entity_type.manager')
      ->getStorage('taxonomy_term')
      ->loadTree($vocabulary->id());

    foreach ($terms as $term) {
      $list[$term->name] = $vocabulary->id();
    }

    return $list;
  }

  /**
   * Wrapper function to return term object by term name and vocabulary ID.
   *
   * @param $word
   *   Term name.
   * @param $vid
   *   Vocabulary ID.
   *
   * @return array
   *   Array of term objects.
   */
  public function getTermByName($word, $vid) {
    return taxonomy_term_load_multiple_by_name($word, $vid);
  }

  /**
   * Wrapper function to return term link by term ID.
   *
   * @param $id
   *   Term ID.
   *
   * @param $word
   *   Link Text.
   *
   * @return String
   *   Term URL.
   */
  public function getUrlByTermId($id, $word) {
    $link = Link::fromTextAndUrl($word, Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $id]));

    return $link->toString();
  }

  /**
   * Wrapper function to get term ID from given term object.
   *
   * @param $term
   *   Drupal term object.
   *
   * @return mixed
   *   Term ID.
   */
  public function getTermId($term) {
    return $term->id();
  }
}
