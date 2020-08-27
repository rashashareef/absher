<?php

/**
 * @file
 * Contains \Drupal\custom_payment\Controller\MipsHandler
 */

namespace Drupal\custom_payment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Render\Markup;

class MipsHandler extends ControllerBase {

    public function content() {

        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $order_info = $_GET['vpc_OrderInfo'];

        //dsm($_GET['vpc_Message']);
        if ($_GET['vpc_Message'] != 'Declined') {
            //accepted
            $nid = $order_info;
            $order = Node::load($nid);

            $order->field_status = 1;
            $order->save();

            $rendered_message = \Drupal\Core\Render\Markup::create(t('Your order has been placed.'));
            drupal_set_message($rendered_message);
            if ($language == 'ar') {
                //send email in arabic
                $this->send_email_custom($nid);
               $response = new RedirectResponse('/ar/summary_page?nid=' . $nid);
            } else {

                $this->send_email_custom($nid);
               $response = new RedirectResponse('/summary_page?nid=' . $nid);
                //send email in english
            }
        } else {
            //declined
            $rendered_message = \Drupal\Core\Render\Markup::create(t('Your payment has been declined. Please try again.'));
            drupal_set_message($rendered_message);
            if ($language == 'en') {
                $response = new RedirectResponse('/passangers_info/' . $nid);
            } else {
                $response = new RedirectResponse('/ar/passangers_info/' . $nid);
            }
        }
       $response->send();
        $build['output'] = [
            '#type' => 'inline_template',
            '#template' => $output,
        ];
        return $build;
    }

    private function send_email_custom($order_nid) {
       $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $order = Node::load($order_nid);
        $packageNid = $order->get('field_package')->target_id;
        $packageName = $this->getNameFromNidTid($packageNid, 'term');

        $extraServicesCount = $this->getExtraServicesCount($order_nid);

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

        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'custom_payment';
        $key = 'confirmation_email';
        $to = \Drupal::currentUser()->getEmail();
        //$to = 'lhmeid@dot.jo';


        $params['message'] = 'Thank you for your reservation , Your order has been placed .' . "\r\n" . "\r\n" . 'Package Name : ' . $packageName . '.' . "\r\n" . 'Extra Services : ' . $extraServicesCount . '.' . "\r\n" .
                    'Adults : ' . $person . '.' . "\r\n" . 'Origin : ' . $origin . '.' . "\r\n" . 'Arrival : ' . $arrival . '.' . "\r\n" .
                    'Flight Number : ' . $flightNumber . '.' . "\r\n" . 'Date : ' . $date . '.' . "\r\n" . 'Time : ' . $time . '.' . "\r\n" . 'Final Price : ' . $finalPrice . 'JOD .';


        $params['order_nid'] = $order_nid;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

        //dsm($result['result']);
    }

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
