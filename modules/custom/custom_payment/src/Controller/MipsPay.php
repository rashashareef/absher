<?php

/**
 * @file
 * Contains \Drupal\custom_payment\Controller\MipsPay
 */

namespace Drupal\custom_payment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Render\Markup;

class MipsPay extends ControllerBase {

    public function content() {
        if (isset($_GET['nid'])) {

            $base_url = \Drupal::request()->getHost();

            $nid = $_GET['nid'];
            $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $order_info = $_GET['vpc_OrderInfo'];

            $order = Node::load($nid);
            $amount = $order->get('field_total')->value;
            //convert JOD to USD
            //$amount = round($amount / 0.71);
            $vpc_amount = (ceil((floatval(trim($amount)))));
            $vpc_amount = $vpc_amount * 1000;
            $MerchTxnRef = substr(time(), 7) . "_" . time();
//            $migs_url = 'https://migs.mastercard.com.au/vpcpay';
            $migs_url = 'https://migs-mtf.mastercard.com.au/vpcpay'; // post url


            $Merchant = '9800026800';
            $vpc_AccessCode = '64261E31';
            $SecureSecret = 'DC16605B247C566AEBF52859A49F112E';
            if ($_GET['language'] == 'ar') {
                $ReturnURL = 'http://' . $base_url . '/ar/mips_handler?nid=' . $nid;
            } else {
                $ReturnURL = 'http://' . $base_url . '/mips_handler?nid=' . $nid;
            }

            $OrderInfo = $nid;
            $data = array();

            $data['vpc_Version'] = '1';
            $data['vpc_Command'] = 'pay';
            $data['vpc_MerchTxnRef'] = $MerchTxnRef;
            $data['vpc_AccessCode'] = $vpc_AccessCode;
            $data['vpc_Merchant'] = $Merchant;
            $data['vpc_Currency'] = 'JOD';
            $data['vpc_OrderInfo'] = $OrderInfo;
            $data['vpc_Amount'] = (string) $vpc_amount; //fels
            $data['vpc_Locale'] = 'en';
            $data['vpc_ReturnURL'] = $ReturnURL;

            ksort($data);

            foreach ($data as $key => $value) {
                $data_hash[] = $key . "=" . $value;
            }

            $stringToHash = implode("&", $data_hash);
            $md5hash = hash_hmac("sha256", $stringToHash, pack("H*", $SecureSecret));

            $output = "<form action='" . $migs_url . "' method='post' id='checkout-form'>
                <input type='hidden' name='vpc_AccessCode' value='" . $vpc_AccessCode . "'>
                <input type='hidden' name='vpc_Amount' value='" . (string) $vpc_amount . "'> 
                <input type='hidden' name='vpc_Command' value='pay'>
                <input type='hidden' name='vpc_Currency' value='JOD'>
                <input type='hidden' name='vpc_Locale' value='en'>
                <input type='hidden' name='vpc_MerchTxnRef' value='" . $MerchTxnRef . "'>
                <input type='hidden' name='vpc_Merchant' value='" . $Merchant . "'>
                <input type='hidden' name='vpc_OrderInfo' value='" . $OrderInfo . "'>
                <input type='hidden' name='vpc_ReturnURL' value='" . $ReturnURL . "'>
                <input type='hidden' name='vpc_Version' value='1'>
                <input type='hidden' name='vpc_SecureHash' value='" . $md5hash . "'>
                <input type='hidden' name='vpc_SecureHashType' value='SHA256'>
            </form>";
            $output .= '<input type="button" value="Confirm Order" id="submit-meps" onclick=document.getElementById("checkout-form").submit(); class="button">';
            $build['output'] = [
                '#type' => 'inline_template',
                '#template' => $output,
            ];
            return $build;
        }
    }

   

}
