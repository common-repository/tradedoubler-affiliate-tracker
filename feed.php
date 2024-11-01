<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Tradedoubler
 *  @copyright 1999-2022 Tradedoubler
 *  @license   MIT
 */
//Load  WP
error_reporting(0);
ini_set('display_errors', 0);
include '../../../wp-load.php';
error_reporting(0);
ini_set('display_errors', 0);
$filename = "export-products-grow-".gmdate('Y-m-d-h-i');

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$filename}.csv");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

$out = fopen('php://output', 'w');
$products = TradedoublerActions::getProductsForFeed();

foreach($products as $product){
    $product = (array)$product;
    $category = $product['categories'][0][0]['name'];
    $res = [
        0 => $product['sourceProductId'],
        1 => $product['name'],
        2 => $product['productImage']['url'],
        3 => trim(preg_replace('/\s\s+/', ' ',wp_strip_all_tags($product['description']))),
        4 => $category,
        5 => $product['price'],
        6 => $product['productUrl'],
    ];
    fputcsv($out, $res);
}
die();