<?php
namespace Drupal\termfilter;

use Drupal\Core\Language\Language;
use Drupal\taxonomy\Entity\Vocabulary;

class TermfilterData {
 
  public function getTermfilterList() {
    $list = array();

    $vocabName = \Drupal::config('termfilter.settings')->get('vocabList');
    $vocabulary = Vocabulary::load($vocabName);
    $container = \Drupal::getContainer();
    $terms = $container->get('entity.manager')->getStorage('taxonomy_term')->loadTree($vocabulary->id());

    foreach($terms as $term) {
      $list[$term->label()] = $vocabulary->id();
    }
    
    return $list;
  }
  
  /**
   * Perform the actual term substitution.
   *
   * We specifically match text that is outside of HTML tags
   * so that for example <img src="http://vhs.org/image.jpg" /> doesn't get the
   * 'VHS' part substituted as that would break the image. We use
   * preg_replace_callback to call our anonymous function for each matching
   * group that is found.
   *
   * In each match, we split on word boundaries, and then check each piece of the
   * split against the list of abbreviations.
   *
   */
  public function termfilterPerformSubs($text, $list) {
    // We prepare a keyed array called $fast_array because this is the
    // quickest way to search later on (using isset()).
    $fast_array = array();
    foreach ($list as $item => $vid) {
      // We want to split on word boundaries, unfortunately PCRE considers words
      // to include underscores but not other characters like dashes and slashes,
      // so we have this hack that subs all characters we want to allow in
      // abbreviations and the target with this massive random blob of all word
      // characters, so that we can correctly split, switching it back later.
      $key = preg_replace('#-#u', '___999999DASH___', $item);
      $key = preg_replace('#/#u', '___111111SLASH___', $key);
      $fast_array[$key] = $vid;
    }

    // Provide an anonymous function for the preg_replace. This function gets
    // called a LOT, so be careful about optimization of anything that goes in
    // here.
    $callback = function($matches) use ($fast_array) {
      // Split the text into an array of words, on word boundaries.
      $words = preg_split('/\b/u', $matches[0]);

      // For each word, check if it matches our term filter.
      foreach ($words as $key => $word) {
        if (!empty($word)) {
          if (isset($fast_array[$word])) {
            $term = taxonomy_term_load_multiple_by_name($word, $fast_array[$word]);
            $url = \Drupal::service('path.alias_manager')->getAliasByPath('taxonomy/term/' . $term[1]->tid, Language::LANGCODE_NOT_SPECIFIED);
            $words[$key] = '<a title="' . $fast_array[$word] . '" href="/' . $url . '">' . $word . '</a>';
          }
        }
      }
      return implode('', $words);
    };

    // Match all content that is not part of a tag, i.e. not between < and >.
    // (?:) = create a non-capturing group.
    // (?:^|>) = the beginning of the string or a closing HTML tag.
    // (?:[^<]|$)+ = characters that are not an opening tag or end of the string.
    //
    // Don't mess with this regular expression unless you understand the PCRE
    // stack limitations. Basically, removing the double plus signs causes a
    // stack overflow and thus a segmentation fault in PHP, as PCRE recurses too
    // deeply. @see http://www.manpagez.com/man/3/pcrestack/ the section on
    // reducing stack usage.
    $text = preg_replace_callback('/(?:^|>)++(?:[^<]++|$)+/u', $callback, $text);

    $text = preg_replace('/___999999DASH___/u', '-', $text);
    $text = preg_replace('/___111111SLASH___/u', '/', $text);

    return $text;
  }
}
