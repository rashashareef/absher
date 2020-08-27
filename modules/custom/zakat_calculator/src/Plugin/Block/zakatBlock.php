<?php

/*
 * @file
 * custom booking blocks
 */

namespace Drupal\zakat_calculator\Plugin\Block;

use Drupal\Core\Block\BlockBase;



/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "Zakat Calculator",
 *   admin_label = @Translation("Zakat Calculator"),
 *   category = @Translation("Zakat Form"),
 * )
 */
class zakatBlock extends BlockBase {

    // Override BlockPluginInterface methods here.

    /**
    * {@inheritdoc}
    */
    public function build() {
        $form = \Drupal::formBuilder()->getForm('Drupal\zakat_calculator\Form\ZakatForm');
        return $form;
    }
}
