<?php
namespace Drupal\termfilter;

class TermfilterData {
 
  public function getTermfilterList() {
    $vocabName = \Drupal::config('termfilter.settings')->get('vocabList');
  }
}