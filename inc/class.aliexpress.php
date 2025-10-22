<?php

class wp_automatic_aliexpress
{
    private $url = "https://api-sg.aliexpress.com/sync";
    private $appKey = "";
    private $appSecret = "";
    private $trackingId = "";

    //construct 
    public function __construct(string $appKey = '', string $appSecret = '', string $trackingId = '')
    {

        //set app key and secret 
        if ($appKey != '') {
            $this->appKey = $appKey;
        }

        if ($appSecret != '') {
            $this->appSecret = $appSecret;
        }

        if ($trackingId != '') {
            $this->trackingId = $trackingId;
        }
    }

    /**
     * Searches for products on AliExpress based on the provided arguments.
     *
     * @param array $args Optional. An array of search parameters to filter results.
     * @return mixed The search results from AliExpress, format may vary depending on implementation.
     * @docs https://openservice.aliexpress.com/doc/api.htm?spm=a2o9m.11193487.0.0.1e00ee0cuGOd1U#/api?cid=21407&path=aliexpress.affiliate.product.query&methodType=GET/POST
     */
    public function search($args = [])
    {

        //example of args
        /*
        $postFields = [
            'keywords' => 'Iphone 11',
            'ship_to_country' => 'US',
            'page_size' => 50,
            'category_ids' => '',
            'page_no' => 1,
            'sort' => 'SALE_PRICE_ASC',
            'tracking_id' => 'batmans'
        ];
        */

        $systemParams = [
            'app_key' => $this->appKey,
            'method' => 'aliexpress.affiliate.product.query',
            'sign_method' => 'sha256',
            'timestamp' => gmdate("Y-m-d H:i:s"),
            'format' => 'json',
            'category_ids' => '37749',
        ];

        //add tracking id if set
        $systemParams = $this->add_tracking_id_arg($systemParams);

        //merge both arrays
        $postFields = array_merge($args, $systemParams);

        // Generate signature
        $postFields['sign'] = $this->generateSign('aliexpress.affiliate.product.query', $postFields);

        //print_r($postFields);


        // Send POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

         curl_close($ch);

        //if empty response
        if (empty($response)) {
            throw new Exception('Empty response from AliExpress API');
        }

        //json decode
        $responseData = json_decode($response, true);

        //if json decode error
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        
        //if api error responseData['aliexpress_affiliate_product_query_response']['resp_result']['resp_msg']
        if (isset($responseData['aliexpress_affiliate_product_query_response']) && isset($responseData['aliexpress_affiliate_product_query_response']['resp_result']) && isset($responseData['aliexpress_affiliate_product_query_response']['resp_result']['resp_code']) && $responseData['aliexpress_affiliate_product_query_response']['resp_result']['resp_code'] != '200')  {
            $errorMsg = $responseData['aliexpress_affiliate_product_query_response']['resp_result']['resp_msg'] ;
            throw new Exception('API error: ' . $errorMsg);
        }
 
        $products = $responseData['aliexpress_affiliate_product_query_response']['resp_result']['result']['products'] ;

         //if array and count > 0
        if (is_array($products) && count($products) > 0) {
            return $products;
        } else {
            throw new Exception('No products found');
        }

        
    }

    //aliexpress.affiliate.productdetail.get
        /**
         * Retrieves product details from AliExpress based on the provided arguments.
         *
         * @param array $args An associative array of parameters required to fetch product details.
         * @return array|WP_Error Returns an array containing product details on success, or WP_Error on failure.
         * @docs https://openservice.aliexpress.com/doc/api.htm?spm=a2o9m.11193487.0.0.1e00ee0cuGOd1U#/api?cid=21407&path=aliexpress.affiliate.productdetail.get&methodType=GET/POST
         */
        public function getProductDetails($args)
        {



            $systemParams = [
                'app_key' => $this->appKey,
                'method' => 'aliexpress.affiliate.productdetail.get',
                'sign_method' => 'sha256',
                'timestamp' => gmdate("Y-m-d H:i:s"),
                'format' => 'json',
            ];

            //add tracking id if set
            $systemParams = $this->add_tracking_id_arg($systemParams);

            //merge both arrays
            $postFields = array_merge($args, $systemParams);
    
            // Generate signature
            $postFields['sign'] = $this->generateSign('aliexpress.affiliate.productdetail.get', $postFields);
    
            //print_r($postFields);
    
            // Send POST request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
 
    
            if (curl_errno($ch)) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }
    
            curl_close($ch);
    
            //if empty response
            if (empty($response)) {
                throw new Exception('Empty response from AliExpress API');
            }
    
            //json decode
            $responseData = json_decode($response, true);
    
            //if json decode error
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }
    
            
            //if api error responseData['aliexpress_affiliate_productdetail_get_response']['resp_result']['resp_msg']
            if (isset($responseData['aliexpress_affiliate_productdetail_get_response']) && isset($responseData['aliexpress_affiliate_productdetail_get_response']['resp_result']) && isset($responseData['aliexpress_affiliate_productdetail_get_response']['resp_result']['resp_code']) && $responseData['aliexpress_affiliate_productdetail_get_response']['resp_result']['resp_code'] != '200')  {
                $errorMsg = $responseData['aliexpress_affiliate_productdetail_get_response']['resp_result']['resp_msg'] ;
                throw new Exception('API error: ' . $errorMsg);
            }

            $product = $responseData['aliexpress_affiliate_productdetail_get_response']['resp_result']['result']['products']['product'];

            //if array and count > 0
            if (is_array($product) && count($product) > 0) {
                return $product[0];
            } else {
                throw new Exception('No product found');
            }
        }

    protected function generateSign($apiName, $params)
    {
        ksort($params);

        $stringToBeSigned = '';
        if (str_contains($apiName, '/')) { //rest服务协议
            $stringToBeSigned .= $apiName;
        }
        foreach ($params as $k => $v) {
            $stringToBeSigned .= "$k$v";
        }
        unset($k, $v);

        return strtoupper(hash_hmac('sha256', $stringToBeSigned, $this->appSecret));
    }

    protected function add_tracking_id_arg($args)
    {
        if ($this->trackingId != '') {
            $args['tracking_id'] = $this->trackingId;
        }
        return $args;
    }
}
