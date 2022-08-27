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
 
function return_secondary_currency ($response_json,$price,$currency){
    try { 

        //decoding
        $response = json_decode($response_json);
        // Check for success
 
        if('success' === $response->result) {
 
            $base_price = $price;  
            $secondary_currency = round(($base_price * $response->conversion_rates->$currency), 2);
            if($secondary_currency)
            {

                return $secondary_currency;

            }else{
            
                return 247;
            }

        }else{
            return 347;
        }
    }
    catch(Exception $e) {
        return $e;
    }
}

function set_cache_currency($price,$currency){

    $api_key = 'API-KEY';
    $req_url = "https://v6.exchangerate-api.com/v6/$api_key/latest/USD";
    $response_json = file_get_contents($req_url);
    if($response_json)
    {
        $time_to_live = 3600 * 24 ; // hours
        set_transient( 'cached_currency',$response_json, $time_to_live );
        $cached_currency = return_secondary_currency($response_json,$price,$currency); 
        if($cached_currency){
            return $cached_currency;
        }else{
            return 987;
        } 

    } 
    

}
function convert_currency($price,$currency){
 
        $cached_currency = get_transient('cached_currency'); 
        empty($cached_currency) ? 
        $cached_currency = set_cache_currency($price,$currency):
        $cached_currency = return_secondary_currency($cached_currency,$price,$currency); 
 
        return $cached_currency; 
       
  
}

add_filter( 'wc_price', 'my_custom_price_format', 10, 3 );
function my_custom_price_format( $formatted_price, $price, $args ) {


    // The currency conversion custom calculation function
    $currency = 'EUR';
    $price_usd = convert_currency($price,$currency);

    // the currency symbol
    $currency_symbol = get_woocommerce_currency_symbol( $currency );
    $price_usd = $currency_symbol.$price_usd; // adding currency symbol

    // The USD formatted price
    $formatted_price_usd = " <span class='price-usd'> ($currency $price_usd)</span>";

    // Return both formatted currencies
    return $formatted_price .'<br>'. $formatted_price_usd;
}
