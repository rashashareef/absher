<?php

/**
 * @file
 * Contains \Drupal\custom_payment\Controller\SummaryPage
 */

namespace Drupal\custom_payment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class SummaryPage extends ControllerBase {

    public function content() {
        //$rendered_message = \Drupal\Core\Render\Markup::create(t('Your order has been placed.'));
        //drupal_set_message($rendered_message);

        if (isset($_GET['nid'])) {
            $nid = $_GET['nid'];
            $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

            $order = Node::load($nid);

            $packageNid = $order->get('field_package')->target_id;
            $packageName = $this->getNameFromNidTid($packageNid, 'term');

            $packageTotal = $order->get('field_package_price')->value;
            $packageTotal = $finalPrice - $packageTotal;

            $extraServicesCount = $this->getExtraServicesCount($nid);

            $adults = intval($order->get('field_adults')->value);
            $childrens = $order->get('field_children')->value;
            $infants = $order->get('field_infants')->value;
            $person = $adults . ' ' . t('Adults') . ', ' . $childrens . ' ' . t('Children') . ', ' . $infants . ' ' . t('Infants');

            $origin = $order->get('field_origin_airport')->target_id;
            $origin = $this->getNameFromNidTid($origin, 'term');

            $arrival = $order->get('field_arrival_airport')->target_id;
            $arrival = $this->getNameFromNidTid($arrival, 'term');

            $flightNumber = $order->get('field_flight_number')->value;

            $date = $order->get('field_arrival_departure_date')->getValue()[0]['value'];
            $datetime = strtotime($date);
            $date = date('d / m / Y', $datetime);

            $time = date('h:i a ', $datetime);
            
            $finalPrice = $order->get('field_total')->value;
        }

        $output = '<div class="full-content"><h3>'.t('Your order has been placed.').'</h3>'
                . '<div class="row-of 1"><strong>'. t('Package Name:').'</strong><span class="row-value 1">' . $packageName . '</span></div>'
                . '<div class="row-of 2"><strong>'. t('Extra Services:').'</strong><span class="row-value 2">' . $extraServicesCount . '</span></div>'
                . '<div class="row-of 3"><strong>'. t('Adults:').'</strong><span class="row-value 3">' . $person . '</span></div>'
                . '<div class="row-of 4"><strong>'. t('Origin:').'</strong><span class="row-value 4">' . $origin . '</span></div>'
                . '<div class="row-of 5"><strong>'. t('Arrival:').'</strong><span class="row-value 5">' . $arrival . '</span></div>'
                . '<div class="row-of 6"><strong>'. t('Flight Number:').'</strong><span class="row-value 6">' . $flightNumber . '</span></div>'
                . '<div class="row-of 7"><strong>'. t('Date:').'</strong><span class="row-value 7">' . $date . '</div>'
                . '<div class="row-of 8"><strong>'. t('Time:').'</strong><span class="row-value 8">' . $time . '</div>'
                . '<div class="row-of 9"><strong>'. t('Final Price:').'</strong><span class="row-value 9">' . $finalPrice .' JD' .'</span></div>'
                .'</div>';

        $build['output'] = [
            '#type' => 'inline_template',
            '#template' => $output
        ];
        return $build;
    }

    /**
     * (@inheritdoc) 
     */
    private function getNameFromNidTid($id, $type) {
        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        if ($type == 'term') {
            $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($id);
            if ($language == 'ar') {
                $term = \Drupal::service('entity.repository')->getTranslationFromContext($term, 'ar');
                $name = $term->getName();
            } else {
                $name = $term->getName();
            }
        } else if ($type == 'node') {
            $entity = Node::load($id);
//            $title = $entity->get('name')->value;
        }

        return $name;
    }

    private function getExtraServicesCount($nid) {

        $connection = \Drupal::database();

        $query = $connection->query("SELECT COUNT(nid) FROM {dotjo_tmp_extraservices} WHERE nid = " . $nid);
        $query->execute();
        $count = $query->fetchAssoc();

        return $count['COUNT(nid)'];
    }

}



