<?php
if (!function_exists('c55_syncAllProducts')) {
    function c55_syncAllProducts() {
        $endPoint = 'Products';
        $filterParams = [
            'ProductGroup' => PRODUCT_GROUP,
            'includeAttributes' => 'true'
        ];

        echo '<p> Fetching all Products ...</p>';
        $response = get_remote_unleashed_url($endPoint, $filterParams);
        if ($response && array_key_exists('body', $response)) {
            $model = json_decode($response['body'], true);
            return $model;
        }
        c55_plugin_log($response);
    }
}
