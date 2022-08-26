<?php
 
/*
 
Plugin Name: Muiltiple Currnecy With Wocommerce
 
Plugin URI: https://github.com/projectphoton/Muiltiple-Currency-With-Woocommerce.git
 
Description: The plugin is for implimenting dual currency with woocommerce 
 
Version: 1.0
 
Author: PROJECT PHOTON
 
License: MIT
 
Text Domain: PROJECT PHOTON
 
*/

function convert_idr_to_usd_cart( $price ){
    $convertion_rate = 1;
    $new_price = $price * $convertion_rate;
    return number_format($new_price, 2, '.', '');
}

add_filter( 'wc_price', 'my_custom_price_format', 10, 3 );
function my_custom_price_format( $formatted_price, $price, $args ) {

    // The currency conversion custom calculation function
    $price_usd = convert_idr_to_usd_cart($price);

    // the currency symbol for US dollars
    $currency = 'EUR';
    $currency_symbol = get_woocommerce_currency_symbol( $currency );
    $price_usd = $currency_symbol.$price_usd; // adding currency symbol

    // The USD formatted price
    $formatted_price_usd = " <span class='price-usd'> ($currency $price_usd)</span>";

    // Return both formatted currencies
    return $formatted_price .'<br>'. $formatted_price_usd;
}
