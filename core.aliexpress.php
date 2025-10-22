<?php

// Main Class
require_once 'core.php';
class WpAutomaticaliexpress extends wp_automatic
{

    private $appKey = "";
    private $appSecret = "";
    private $trackingId = "";

    //construct
    public function __construct()
    {
        //call parent constructor
        parent::__construct();
    
        //set app key and secret
        $this->appKey = trim(get_option('wp_automatic_ali_app_id', ''));
        $this->appSecret = trim(get_option('wp_automatic_ali_app_secret', ''));
        $this->trackingId = trim(get_option('wp_automatic_ali_tracking_id', ''));

    }

    public function aliexpress_get_post($camp)
    {

        // ini keywords
        $camp_opt = $this->camp_opt;
        $keywords = explode(',', $camp->camp_keywords);
        $camp_general = $this->camp_general;

        

        if (in_array('OPT_ALIEXPRESS_CUR', $camp_opt)) {

            $cg_ae_custom_cur = wp_automatic_trim($camp_general['cg_ae_custom_cur']);
            echo '<br>Custom currency is requested...' . $cg_ae_custom_cur;

            //price cookie aep_usuc_f=site=ara&c_tp=CAD
            $cookie_value = $this->cookie_content('aliexpress');

            if (!stristr($cookie_value, 'c_tp=' . $cg_ae_custom_cur)) {
                echo ' Found not set, let us set it ';
                $this->cookie_delete('aliexpress');

                $aep_usuc_f = 'aep_usuc_f=c_tp=' . $cg_ae_custom_cur;

                //global domain name site=glo
                if (!in_array('OPT_ALIEXPRESS_DOMAIN', $camp_opt)) {
                    $aep_usuc_f .= '&site=glo&b_locale=en_US';
                }

                curl_setopt($this->ch, CURLOPT_COOKIE, $aep_usuc_f);
            } else {
                echo ' Currency is already set to ' . $cg_ae_custom_cur;
            }
        } else {

            if (!in_array('OPT_ALIEXPRESS_DOMAIN', $camp_opt)) {

                //no currency is required but lets make sure that the domain is set to glo
                //price cookie aep_usuc_f=site=ara&c_tp=CAD
                $cookie_value = $this->cookie_content('aliexpress');

                if (!stristr($cookie_value, 'site=glo')) {
                    echo '<br>Found site glo not set, let us set it ';
                    $this->cookie_delete('aliexpress');

                    $aep_usuc_f = 'aep_usuc_f=site=glo&c_tp=USD&isb=y&b_locale=en_US';

                    curl_setopt($this->ch, CURLOPT_COOKIE, $aep_usuc_f);
                } else {
                    echo '<br>Site found set to Glo';
                }
            }
        }

        //  cookie load
        $this->load_cookie('aliexpress');

        // looping keywords
        foreach ($keywords as $keyword) {

            $keyword = wp_automatic_trim($keyword);

            // update last keyword
            update_post_meta($camp->camp_id, 'last_keyword', wp_automatic_trim($keyword));

            // when valid keyword
            if (wp_automatic_trim($keyword) != '') {

                // record current used keyword
                $this->used_keyword = $keyword;

                echo '<br>Let\'s post AliExpress product for the key:' . $keyword;

                // getting links from the db for that keyword
                $query = "select * from {$this->wp_prefix}automatic_general where item_type=  'ae_{$camp->camp_id}_$keyword' ";
                $res = $this->db->get_results($query);

                // when no links lets get new links
                if (count($res) == 0) {

                    // clean any old cache for this keyword
                    $query_delete = "delete from {$this->wp_prefix}automatic_general where item_type='ae_{$camp->camp_id}_$keyword' ";
                    $this->db->query($query_delete);

                    // get new links
                    $this->aliexpress_fetch_items($keyword, $camp);

                    // getting links from the db for that keyword
                    $res = $this->db->get_results($query);
                }

                // check if already duplicated
                // deleting duplicated items

                $item_count = count($res);

                for ($i = 0; $i < $item_count; $i++) {

                    $t_row = $res[$i];

                    $t_data = unserialize(base64_decode($t_row->item_data));

                    $t_link_url = $t_data['item_url'];

                    echo '<br>Link:' . $t_link_url;

                    // check if link is duplicated
                    if ($this->is_duplicate($t_link_url)) {

                        // duplicated item let's delete
                        unset($res[$i]);

                        echo '<br>AliExpress product (' . $t_data['item_url'] . ') found cached but duplicated <a href="' . get_permalink($this->duplicate_id) . '">#' . $this->duplicate_id . '</a>';

                        // delete the item
                        $query = "delete from {$this->wp_prefix}automatic_general where id={$t_row->id}";
                        $this->db->query($query);
                    } else {

                        break;
                    }
                } // end for

                // check again if valid links found for that keyword otherwise skip it
                if (count($res) > 0) {

                    // lets process that link
                    $ret = $res[$i];

                    $temp = unserialize(base64_decode($ret->item_data));

                    //get the item info for this video
                    $current_item_url = $temp['item_url'];

                    // update the link status to 1
                    $query = "delete from {$this->wp_prefix}automatic_general where id={$ret->id}";
                    $this->db->query($query);


                    //get getProductDetails by id 
                    echo '<br>Getting product details for product id:' . $ret->item_id;

                      

                    require_once dirname(__FILE__) . '/inc/class.aliexpress.php';

                    $aliexpress = new wp_automatic_aliexpress($this->appKey, $this->appSecret,$this->trackingId);

                    try {

                        $args = array();
                        $args['product_ids'] = $ret->item_id;
                        $args['country'] = $this->get_country_code();
                        $args['target_language'] = $this->get_language_code();
                        $args['target_currency'] = $this->get_currency_code();

                        $productDetails = $aliexpress->getProductDetails($args);
                    } catch (Exception $e) {

                        //error message in red color
                        echo '<span style="color:red;">ERROR: ', $e->getMessage(), "</span>\n";
                        return false;
                    }

                    $response = $productDetails;


                    //title
                    $title = $response['product_title'];
                    $temp['item_title'] = $title;

                    //set the item_description to the title as well because the lame api does not return description
                    $temp['item_description'] = $title;

                    //rating PC_RATING->rating
                    //$temp['item_rating'] = isset($response->data->result->PC_RATING->rating) ? $response->data->result->PC_RATING->rating : '';

                    //TradeCount PC_RATING->otherText
                    //$temp['item_orders'] = isset($response->data->result->PC_RATING->otherText) ? $response->data->result->PC_RATING->otherText : '';

                    //SKU->selectedSkuId
                    $temp['item_sku'] = $response['sku_id'];

                    //item_price_current
                    $temp['item_price_current'] = $this->formatPrice($response['target_sale_price'],$response['target_original_price_currency']) ;

                  
                    //item_price_original default to current price
                    $temp['item_price_original'] = $this->formatPrice($response['target_original_price'],$response['target_original_price_currency']);


                    //images list HEADER_IMAGE_PC->imagePathList 
                    $temp['item_images'] = implode(',', $response['product_small_image_urls']['string']);

                    //ship from SHIPPING->deliveryLayoutInfo[0]->bizData->shipFrom
                    //$temp['item_ship_from'] = $response['shipping']['deliveryLayoutInfo'][0]['bizData']['shipFrom'];

                    //deliveryDayMax
                    // $temp['item_delivery_time'] = $response->data->result->SHIPPING->deliveryLayoutInfo[0]->bizData->deliveryDayMax;

                    // shippingFee
                    //$temp['item_ship_cost'] = $response->data->result->SHIPPING->deliveryLayoutInfo[0]->bizData->shippingFee;

                    // WISHLIST->wishItemCount
                    //$temp['item_wish_count'] = $response->data->result->WISHLIST->wishItemCount;

                    //descriptionUrl DESC->pcDescUrl
                    $temp['item_description_url'] = $response['product_detail_url'];

                    //second_level_category_name
                    $temp['item_category'] = $response['first_level_category_name'] . ' > ' . $response['second_level_category_name'];

                    //shop_url
                    $temp['item_shop_url'] =  $response['shop_url'];

                    //product_video_url
                    $temp['item_video_url'] = $response['product_video_url'];

                    //promotion_link
                    $temp['item_affiliate_url'] = $response['promotion_link'];

                    //shop_name
                    $temp['item_shop_name'] = $response['shop_name'];

                    //shop_id
                    $temp['item_shop_id'] = $response['shop_id'];

                    // report link
                    echo '<br>Found Link:' . $temp['item_url'];

                    // if cache not active let's delete the cached videos and reset indexes
                    if (!in_array('OPT_AE_CACHE', $camp_opt)) {
                        echo '<br>Cache disabled claring cache ...';
                        $query = "delete from {$this->wp_prefix}automatic_general where item_type='ae_{$camp->camp_id}_$keyword' ";
                        $this->db->query($query);

                        // reset index
                        $query = "update {$this->wp_prefix}automatic_keywords set keyword_start =1 where keyword_camp={$camp->camp_id}";
                        $this->db->query($query);
                    }

                    // imgs html
                    if (in_array('OPT_AM_FULL_IMG', $this->camp_opt)) {
                        $cg_am_full_img_t = stripslashes(@$camp_general['cg_ae_full_img_t']);
                    } else {
                        $cg_am_full_img_t = '';
                    }

                    if (wp_automatic_trim($cg_am_full_img_t) == '') {
                        $cg_am_full_img_t = '<img src="[img_src]" class="wp_automatic_gallery" />';
                    }

                    $product_imgs_html = '';

                    $allImages = explode(',', $temp['item_images']);
                    $allImages_html = '';

                    foreach ($allImages as $singleImage) {

                        //first image
                        if (!isset($temp['item_img'])) {
                            $temp['item_img'] = $singleImage;
                        }

                        $singleImageHtml = $cg_am_full_img_t;
                        $singleImageHtml = wp_automatic_str_replace('[img_src]', $singleImage, $singleImageHtml);
                        $allImages_html .= $singleImageHtml;
                    }

                    $temp['item_imgs_html'] = $allImages_html;

                    // item images ini
                    $temp['item_image_html'] = '<img src="' . $temp['item_img'] . '" />';

                    //get description content from descriptionUrl
                    /*if (wp_automatic_trim($temp['item_description_url']) != '') {
                        echo '<br>Finding item description from description URL:' . $temp['item_description_url'];

                        //curl get
                        $x = 'error';
                        $url = $temp['item_description_url'];
                        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
                        curl_setopt($this->ch, CURLOPT_URL, wp_automatic_trim($url));
                        $exec = curl_exec($this->ch);
                        $x = curl_error($this->ch);

                        $temp['item_description'] = $exec;
                    }*/

                    $temp['item_price_numeric'] = $this->get_numberic_price($temp['item_price_current']);
                    $temp['item_price_original_numeric'] = $this->get_numberic_price($temp['item_price_original']);

                    //print_r($temp);
                    

                    return $temp;
                } else {
                    echo '<br>No links found for this keyword';
                }
            } // if trim
        } // foreach keyword
    }
    public function aliexpress_fetch_items($keyword, $camp)
    {

        // report
        echo "<br>So I should now get some items from AliExpress for keyword :" . $keyword;

        // Affiliate app credentials
        $wp_automatic_ali_app_id = trim(get_option('wp_automatic_ali_app_id', ''));
        $wp_automatic_ali_app_secret = trim(get_option('wp_automatic_ali_app_secret', ''));
        $wp_automatic_ali_tracking_id = trim(get_option('wp_automatic_ali_tracking_id', ''));

        // ini options
        $camp_opt =  $this->camp_opt;

        // camp general
        $camp_general = $this->camp_general;

        // get start-index for this keyword
        $query = "select keyword_start ,keyword_id from {$this->wp_prefix}automatic_keywords where keyword_name='$keyword' and keyword_camp={$camp->camp_id}";
        $rows = $this->db->get_results($query);
        $row = $rows[0];
        $kid = $row->keyword_id;
        $start = $row->keyword_start;

        if ($start == 0) {
            $start = 1;
        }

        if ($start == -1) {

            echo '<- exhausted keyword';

            if (!in_array('OPT_AE_CACHE', $camp_opt)) {
                $start = 1;
                echo '<br>Cache disabled resetting index to 1';
            } else {

                // check if it is reactivated or still deactivated
                if ($this->is_deactivated($camp->camp_id, $keyword)) {
                    $start = 1;
                } else {
                    // still deactivated
                    return false;
                }
            }
        } else {

            if (!in_array('OPT_AE_CACHE', $camp_opt)) {
                $start = 1;
                echo '<br>Cache disabled resetting index to 1';
            }
        }

        echo ' index:' . $start;

        // update start index to start+1
        $nextstart = $start + 1;
        $query = "update {$this->wp_prefix}automatic_keywords set keyword_start = $nextstart where keyword_id=$kid ";
        $this->db->query($query);

        // pagination
        if ($start == 1) {

            echo ' Posting from the first page...';
        } else {

            // not first page get the bookmark
            $wp_tiktok_next_max_id = get_post_meta($camp->camp_id, 'wp_tiktok_next_max_id' . md5($keyword), 1);

            if (wp_automatic_trim($wp_tiktok_next_max_id) == '') {
                echo '<br>No new page max id';
                $wp_tiktok_next_max_id = 0;
            } else {
                if (in_array('OPT_IT_CACHE', $camp_opt)) {
                    echo '<br>max_id:' . $wp_tiktok_next_max_id;
                } else {
                    $start = 1;
                    echo '<br>Cache disabled resetting index to 1';
                    $wp_tiktok_next_max_id = 0;
                }
            }
        }

        $aliexpress_encoded_keyword = urlencode(wp_automatic_trim($keyword));

        if (in_array('OPT_ALIEXPRESS_CUSTOM', $camp_opt)) {

            //custom search link
            $cg_ae_custom_urls = $camp_general['cg_ae_custom_urls'];

            $aliexpress_url = wp_automatic_str_replace('[keyword]', urlencode(wp_automatic_trim($keyword)), $cg_ae_custom_urls);

            $aliexpress_url_parts = explode('aliexpress.com', $aliexpress_url);
            $aliexpress_domain = wp_automatic_trim($aliexpress_url_parts[0]) . 'aliexpress.com';
        } else {
            // prepare keyword https://www.aliexpress.com/af/search.html?SearchText=red+duck
            //$aliexpress_url = 'https://www.aliexpress.com/af/search.html?SearchText=' . urlencode(wp_automatic_trim($keyword)) ;

            // new search URL https://www.aliexpress.com/w/wholesale-pizza-box.html?catId=0&initiative_id=SB_20230718094010&SearchText=pizza+box&spm=a2g0o.productlist.1000002.0
            // short fom https://www.aliexpress.com/w/wholesale-pizza-box.html?SearchText=pizza+box
            $aliexpress_domain = 'https://www.aliexpress.com';

            //create an initiative_id like SB_20230718094010
            $initiative_id = 'SB_' . date('YmdHis');

            //$aliexpress_url = 'https://www.aliexpress.com/w/wholesale-' . $aliexpress_encoded_keyword . '.html?SearchText=' . $aliexpress_encoded_keyword;
            $aliexpress_url = 'https://www.aliexpress.com/w/wholesale-' . $aliexpress_encoded_keyword . '.html?SearchText=' . $aliexpress_encoded_keyword . '&catId=0&initiative_id=' . $initiative_id . '&spm=a2g0o.productlist.1000002.0&trafficChannel=main&g=y';
        }

        //custom country domain name
        if (in_array('OPT_ALIEXPRESS_DOMAIN', $camp_opt)) {
            $cg_ae_custom_domain = wp_automatic_trim($camp_general['cg_ae_custom_domain']);

            if (stristr($cg_ae_custom_domain, 'aliexpress.com')) {
                echo '<br>Custom country/domain is requested: ' . $cg_ae_custom_domain;
                $cg_ae_custom_domain = preg_replace('!/$!', '', $cg_ae_custom_domain);
                $aliexpress_url = wp_automatic_str_replace('https://www.aliexpress.com', $cg_ae_custom_domain, $aliexpress_url);
                $aliexpress_domain = $cg_ae_custom_domain;
            }
        } else {

            //set to US by default
            $cookie_value = $this->cookie_content('aliexpress');

            if (!stristr($cookie_value, 'site=glo')) {
                echo ' Found global site not set, let us set it ';
                $this->cookie_delete('aliexpress');

                curl_setopt($this->ch, CURLOPT_COOKIE, 'aep_usuc_f=site=glo');
            } else {
                echo ' Site is already set to Glo';
            }
        }

        //pagination
        if ($start != 1) {
            $aliexpress_url .= '&page=' . $start;
        }

        //if the keyword is a product id on the form 1005006869409001
        //{"productId":"1005006869409001"}

        if (preg_match('!^\d+$!', $keyword)) {
            echo '<br>Keyword is a product id, we will use the product ID in the URL...';

            //exec {"productId":"1005006869409001"}
            $exec = '{"productId":"' . trim($keyword) . '"}';

            //deactivate permanently
            $this->deactivate_key($camp->camp_id, $keyword, 0);
        } elseif (in_array('OPT_TT_INFINITE', $camp_opt)) {

            echo '<br>Loading the items from the added HTML...';
            $exec = $camp_general['cg_tt_html'];
        } else {

            // doing a search request
            //include the class aliexpress
            require_once(dirname(__FILE__) . '/inc/class.aliexpress.php');

            $aliexpress = new wp_automatic_aliexpress($this->appKey, $this->appSecret,$this->trackingId);

            //prepare args
            $args = [
                'keywords' => $keyword,
                'ship_to_country' => $this->get_country_code(),
                'page_size' => 20,
                'category_ids' => '',
                'page_no' => $start,
                'sort' => 'SALE_PRICE_ASC',
                'target_currency' => $this->get_currency_code(),
                'target_language' => $this->get_language_code(),

            ];

            //add min and max price if set
            $args = $this->add_min_price_arg($args);
            $args = $this->add_max_price_arg($args);

            //add sorting order
            $args = $this->add_orderby_arg($args);

            //'tracking_id' => 'batmans'

            //search products
            try {
                $products = $aliexpress->search($args);
            } catch (Exception $e) {
                
                //tracking error in red color
                echo '<span style="color:red;">ERROR: ', $e->getMessage(), "</span>\n";
            }

            //loop products and build exec with product ids
            $exec = '';
            if (isset($products['product'])) {
                foreach ($products['product'] as $product) {
                    $exec .= '{"productId":"' . $product['product_id'] . '"}';
                }
            }
        }

        $items = array();

        // "productId":"1005003141253710"
        if (strpos($exec, '"productId":"')) {

            //extract video links
            preg_match_all('{"productId":"(\d*)"}s', $exec, $found_items_matches);

            $items_ids = $found_items_matches[1];

            // reverse
            if (in_array('OPT_TT_REVERSE', $camp_opt)) {
                echo '<br>Reversing order';

                $items_ids = array_reverse($items_id);
            }

            echo '<ol>';

            // loop items
            $i = 0;
            foreach ($items_ids as $item) {

                // clean itm
                unset($itm);

                // build item
                $itm['item_id'] = $item;
                $itm['item_url'] = "{$aliexpress_domain}/item/" . $item . ".html";

                $data = base64_encode(serialize($itm));

                $i++;

                echo '<li>' . $itm['item_url'] . '</li>';

                if (!$this->is_duplicate($itm['item_url'])) {
                    $query = "INSERT INTO {$this->wp_prefix}automatic_general ( item_id , item_status , item_data ,item_type) values (    '{$itm['item_id']}', '0', '$data' ,'ae_{$camp->camp_id}_$keyword')  ";
                    $this->db->query($query);
                } else {
                    echo ' <- duplicated <a href="' . get_edit_post_link($this->duplicate_id) . '">#' . $this->duplicate_id . '</a>';
                }

                echo '</li>';
            }

            echo '</ol>';

            echo '<br>Total ' . $i . ' products found & cached';

            // check if nothing found so deactivate
            if ($i == 0) {
                echo '<br>No new items got found ';
                echo '<br>Keyword have no more items deactivating...';
                $query = "update {$this->wp_prefix}automatic_keywords set keyword_start = -1 where keyword_id=$kid ";
                $this->db->query($query);

                if (!in_array('OPT_NO_DEACTIVATE', $camp_opt)) {
                    $this->deactivate_key($camp->camp_id, $keyword);
                }
            } else {

                //we got products

            }
        } else {

            // no valid reply
            echo '<br>No Valid reply for AliExpress search ';

            echo '<br>Reply: ' . $exec;

            echo '<br>No new items got found ';
            echo '<br>Keyword have no more items deactivating...';
            $query = "update {$this->wp_prefix}automatic_keywords set keyword_start = -1 where keyword_id=$kid ";
            $this->db->query($query);

            if (!in_array('OPT_NO_DEACTIVATE', $camp_opt)) {
                $this->deactivate_key($camp->camp_id, $keyword);
            }
        }
    }

    public function get_numberic_price($text_price)
    {

        $item_price_current = $text_price;
        $item_price_current_pts = explode('-', $item_price_current);
        $item_price_current = $item_price_current_pts[0];

        preg_match('![\d\.\,]+!', $item_price_current, $price_matchs);
        if (isset($price_matchs[0])) {
            return $price_matchs[0];
        } else {
            return '';
        }
    }

    /**
     * Get _m_h5_tk, _m_h5_tk_enc from cookie
     */
    public function get_m_h5_tk()
    {

        //curl request to https://acs.youku.com/h5/mtop.com.youku.aplatform.weakget/1.0/?jsv=2.5.1&appKey=24679788

        //curl ini
        $x = 'error';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://acs.youku.com/h5/mtop.com.youku.aplatform.weakget/1.0/?jsv=2.5.1&appKey=12574478');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //return header too
        curl_setopt($ch, CURLOPT_HEADER, 1);

        //execute
        $exec = curl_exec($ch);


        //error
        $x = curl_error($ch);

        //close
        curl_close($ch);

        //extract _m_h5_tk, _m_h5_tk_enc
        preg_match('!_m_h5_tk=(.*?);!', $exec, $m_h5_tk_matches);

        preg_match('!_m_h5_tk_enc=(.*?);!', $exec, $m_h5_tk_enc_matches);

        //if not found, throw error
        if (!isset($m_h5_tk_matches[1]) || trim($m_h5_tk_matches[1]) == '' || !isset($m_h5_tk_enc_matches[1]) || trim($m_h5_tk_enc_matches[1]) == '') {
            throw new Exception('Could not get _m_h5_tk, _m_h5_tk_enc');
        }

        //get cna cookie by request to https://log.mmstat.com/eg.js
        //set url to https://log.mmstat.com/eg.js
        $url = 'https://log.mmstat.com/eg.js';
        curl_setopt($ch, CURLOPT_URL, $url);

        //execute
        $exec = curl_exec($ch);


        //extract cna cna=eCJOH40wN2QCAZzNrh/9Eyt1
        preg_match('!cna=(.*?);!', $exec, $cna_matches);

        //if not found, throw error
        if (!isset($cna_matches[1]) || trim($cna_matches[1]) == '') {
            throw new Exception('Could not get cna');
        }


        //return
        return array($m_h5_tk_matches[1], $m_h5_tk_enc_matches[1], $cna_matches[1]);
    }

    /**
     * Formats a given amount into a price string with the specified currency.
     *
     * @param float|int $amount   The numeric amount to format.
     * @param string    $currency The currency code or symbol to use in formatting.
     *
     * @return string The formatted price string.
     */
    function formatPrice($amount, $currency)
    {
        // Map of common currency symbols
        $symbols = [
            "USD" => "$",
            "EUR" => "€",
            "GBP" => "£",
            "JPY" => "¥",
            "CNY" => "¥",
            "BRL" => "R$",
            "INR" => "₹",
            "AUD" => "A$",
            "CAD" => "C$",
            "CHF" => "CHF",
            "SEK" => "kr",
            "NOK" => "kr",
            "DKK" => "kr",
            "RUB" => "₽",
            "TRY" => "₺",
            "MXN" => "MX$",
            "ZAR" => "R",
            "AED" => "د.إ",
            "SAR" => "﷼",
        ];

        // Use symbol if available, otherwise fallback to currency code
        $symbol = isset($symbols[$currency]) ? $symbols[$currency] : $currency . " ";

        // Always format with 2 decimals (e.g. 23 -> 23.00)
        $formattedAmount = number_format($amount, 2, '.', ',');

        // Most currencies prefix symbol, but some postfix
        $postfix = ["SEK", "NOK", "DKK"]; // examples where symbol goes after amount

        if (in_array($currency, $postfix)) {
            return $formattedAmount . " " . $symbol;
        } else {
            return $symbol . $formattedAmount;
        }
    }

    //get currency returns USD by default or if cg_ae_custom_cur has a value
    public function get_currency_code()
    {
        $camp_general = $this->camp_general;
        $cg_ae_custom_cur = wp_automatic_trim($camp_general['cg_ae_custom_cur']);
        if ($cg_ae_custom_cur != '') {
            return $cg_ae_custom_cur;
        } else {
            return 'USD';
        }
    }

    //get country code returns US by default or if cg_ae_custom_country has a value
    public function get_country_code()
    {
        $camp_general = $this->camp_general;
        $cg_ae_custom_country = isset($camp_general['cg_ae_custom_country']) ? wp_automatic_trim($camp_general['cg_ae_custom_country']) : '';
        if ($cg_ae_custom_country != '') {
            return $cg_ae_custom_country;
        } else {
            return 'US';
        }
    }

    
    /**
     * Retrieves the language code to be used for AliExpress API requests.
     *
     * This method determines and returns the appropriate language code
     * based on plugin settings or other criteria.
     *
     * @return string The language code (e.g., 'en', 'ru', 'es').
     */
    public function get_language_code()
    {
        $camp_general = $this->camp_general;
        $cg_ae_custom_language = isset($camp_general['cg_ae_custom_lang']) ? wp_automatic_trim($camp_general['cg_ae_custom_lang']) : '';
        if ($cg_ae_custom_language != '') {
            return $cg_ae_custom_language;
        } else {
            return 'en_US';
        }
    }

    //function to add min_sale_price to the args array if the option OPT_ALIEXPRESS_MIN_PRICE is enabled and cg_ae_custom_min_price holds a value
    public function add_min_price_arg($args)
    {
        if (in_array('OPT_ALIEXPRESS_MIN_PRICE', $this->camp_opt)) {
            $camp_general = $this->camp_general;
            $cg_ae_custom_min_price = isset($camp_general['cg_ae_custom_min_price']) ? wp_automatic_trim($camp_general['cg_ae_custom_min_price']) : '';
            if ($cg_ae_custom_min_price != '' && is_numeric($cg_ae_custom_min_price)) {
                $args['min_sale_price'] = floatval($cg_ae_custom_min_price);
            }
        }
        return $args;

    }

    //function to add max_sale_price to the args array if the option OPT_ALIEXPRESS_MAX_PRICE is enabled and cg_ae_custom_max_price holds a value
    public function add_max_price_arg($args)
    {
        if (in_array('OPT_ALIEXPRESS_MAX_PRICE', $this->camp_opt)) {
            $camp_general = $this->camp_general;
            $cg_ae_custom_max_price = isset($camp_general['cg_ae_custom_max_price']) ? wp_automatic_trim($camp_general['cg_ae_custom_max_price']) : '';
            if ($cg_ae_custom_max_price != '' && is_numeric($cg_ae_custom_max_price)) {
                $args['max_sale_price'] = floatval($cg_ae_custom_max_price);
            }
        }
        return $args;
    }

    //function add_orderby_arg if OPT_ALIEXPRESS_SORT is set , add cg_ae_custom_sort to key sort
    public function add_orderby_arg($args)
    {
        if (in_array('OPT_ALIEXPRESS_SORT', $this->camp_opt)) {
            $camp_general = $this->camp_general;
            $cg_ae_custom_sort = isset($camp_general['cg_ae_custom_sort']) ? wp_automatic_trim($camp_general['cg_ae_custom_sort']) : '';
            if ($cg_ae_custom_sort != '') {
                $args['sort'] = $cg_ae_custom_sort;
            }
        }
        return $args;
    }

}