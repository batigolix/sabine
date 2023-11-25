<?php

namespace Drupal\dd8_tools\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Google translate block.
 *
 * @Block(
 *   id = "gtranslate",
 *   subject = @Translation("Google translate"),
 *   admin_label = @Translation("DD 8 tools: Google translate")
 * )
 */
class DdToolsGtranslate extends BlockBase {

  /**
   * Implements \Drupal\Core\Block\BlockBase::blockBuild().
   */
  public function build() {
    $build = [];
    $build['container']['#markup'] = '<div id="google_translate_element"></div>';
    $build['#attached']['library'][] = 'dd8_tools/gtranslate';
    $build['#attached']['library'][] = 'dd8_tools/gtranslate_external';
    return $build;
  }

}
