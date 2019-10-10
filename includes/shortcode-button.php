<?php
if (!defined('ABSPATH')) {
    exit;
}

// [redsysbutton]
function redsysbutton_func($atts = null, $content = null) {

    $apiObj = new RedsysAPI;

    $button_id = $atts['id'];
    $qty = $atts['qty'];
    $post_id = $atts['book'];
    $post = get_post($button_id); 
    $title = $post->post_title;
    $price = get_post_meta( $button_id, '_button_price', true );

    $environment = get_option('_redsys_environment');
    if($environment == 'TEST'){
        $url_tpv = get_option('_redsys_url_tpv_test');
        $clave = get_option('_redsys_keycode_test');
    }else{
        $url_tpv = get_option('_redsys_url_tpv_prod');
        $clave = get_option('_redsys_keycode_prod');
    }
    $version = "HMAC_SHA256_V1"; 
    $clave = get_option('_redsys_keycode');
    $name = get_option('_redsys_name');
    $code = get_option('_redsys_fuc_code');
    $terminal = get_option('_redsys_terminal');
    $order = $post_id.'-'.date('mdhi');
    $amount = $qty * $price * 100;
    $currency = get_option('_redsys_currency');
    $consumerlng = '001';
    $transactionType = '0';
    $urlMerchant = get_option('_redsys_url_merchant');
    $urlweb_ok = get_option('_redsys_url_ok');
    $urlweb_ko = get_option('_redsys_url_ko');

    $apiObj->setParameter("DS_MERCHANT_ORDER",$order);
    $apiObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
    $apiObj->setParameter("DS_MERCHANT_CURRENCY",$currency);
    $apiObj->setParameter("DS_MERCHANT_MERCHANTCODE",$code);
    $apiObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
    $apiObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$transactionType);
    $apiObj->setParameter("DS_MERCHANT_MERCHANTURL",$urlMerchant);
    $apiObj->setParameter("DS_MERCHANT_URLOK",$urlweb_ok);      
    $apiObj->setParameter("DS_MERCHANT_URLKO",$urlweb_ko);
    $apiObj->setParameter("DS_MERCHANT_MERCHANTNAME",$name); 
    $apiObj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE",$consumerlng);    

    $params = $apiObj->createMerchantParameters();
    $signature = $apiObj->createMerchantSignature($clave);

    echo '<form name="from" action="'.$url_tpv.'" method="POST">';
    echo '<input type="hidden" name="Ds_SignatureVersion" value="'.$version.'">';
    echo '<input type="hidden" name="Ds_MerchantParameters" value="'.$params.'">';
    echo '<input type="hidden" name="Ds_Signature" value="'.$signature.'">';
    echo '<input class="dx-btn dx-btn-lg" type="submit" value="Realizar pago" />';
    echo '</form>';

}
add_shortcode('redsysbutton', 'redsysbutton_func');
