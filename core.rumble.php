<?php

// Main Class
require_once 'core.php';
class WpAutomaticRumble extends wp_automatic
{

    /*
     * ---* Fetch a new list of items
     */
    public function rumble_fetch_items($keyword, $camp)
    {
        echo "<br>So I should now get some items from Rumble ...";

        // ini options
        $camp_opt = $this->camp_opt;
        $camp_general = $this->camp_general;

        // items url
        $cg_rm_page = wp_automatic_trim($camp_general['cg_rm_page']);
        $cg_rm_page_md = md5($cg_rm_page);

        //if empty page return false and ask the user to add a correct rumble page URL on the format https://rumble.com/search/all?q=crypto
        if (wp_automatic_trim($cg_rm_page) == '') {
            echo '<br>Rumble page URL is empty please visit rumble.com and get a correct one ';
            return false;
        }

        
        //verify if page contains rumble and if not, ask the user to add a correct page URL
        if (!(stristr($cg_rm_page, 'rumble.')  )) {
            echo '<br>Rumble page URL is not correct please visit rumble.com and get a correct one on the format https://rumble.com/search/all?q=crypto';
            return false;
        }

        // get start-index for this keyword
        $query = "select keyword_start ,keyword_id from {$this->wp_prefix}automatic_keywords where keyword_name='$keyword' and keyword_camp={$camp->camp_id}";
        $rows = $this->db->get_results($query);
        @$row = $rows[0];

        // If no rows add a keyword record
        if (count($rows) == 0) {
            $query = "insert into {$this->wp_prefix}automatic_keywords(keyword_name,keyword_camp,keyword_start) values ('$keyword','{$camp->camp_id}',1)";
            $this->db->query($query);
            $kid = $this->db->insert_id;
            $start = 0;
        } else {
            $kid = $row->keyword_id;
            $start = $row->keyword_start;
        }

        if ($start == -1) {
            echo '<- exhausted link';

            if (!in_array('OPT_IT_CACHE', $camp_opt)) {
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
        }

        // page tag
        if (in_array('OPT_RM_CACHE', $camp_opt) ) {

                $page = $start + 1; // page starts from 1

                if($page > 1 ){
                        //add page number to the URL
                    if (stristr($cg_rm_page, '?')) {
                        $cg_rm_page .= '&page=' . $page;
                    } else {
                        $cg_rm_page .= '?page=' . $page;
                    }
               }
            
             
        }

        echo '<br>Rumble items url:' . $cg_rm_page;

        echo ' index:' . $start;

        // update start index to start+1
        $nextstart = $start + 1;

        $query = "update {$this->wp_prefix}automatic_keywords set keyword_start = $nextstart where keyword_id=$kid ";
        $this->db->query($query);

        // get items
        // curl get
        $x = 'error';
        $url = $cg_rm_page;
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_URL, wp_automatic_trim($url));
        $exec = curl_exec($this->ch);
        $x = curl_error($this->ch);

        // error check
        if (wp_automatic_trim($x) != '') {
            echo '<br>Curl error:' . $x;
            return false;
        }

        //echo length of returned data
        echo '<br>Returned data length:' . strlen($exec) . ' chars';

        
       //extract items links by class video-item--a
       //search page:<a class="video-item--a" href="/v6tgvn5-crypto-exchanges-the-heart-and-soul-of-crypto.html"><div class="video-item--img-wrapper"><img class="video-item--img" src="https://1a-1791.com/video/fww1/62/s8/1/X/K/X/K/XKXKy.oq1b.2-small-Crypto-Exchanges-The-Heart-.jpg" alt="Crypto Exchanges, The Heart &amp; Soul Of Crypto"></div><span class="video-item--duration" data-value="1:05:31"></span></a>
    //profile page: <a class="videostream__link link" draggable="false" href="/v6tsf81-fresh-and-fit-subathon.html?e9s=src_v1_ucp"></a>

       //check if contains class="video-item--a"
        $allItms = array(); //ini all items array
        if (stristr($exec, 'class="video-item--a"')) {

            echo '<br>Found class="video-item--a" extracting items links...';

            //extract all items links
            preg_match_all('/<a class="video-item--a" href="([^"]+)"/', $exec, $matches);

            
            //if matches found
            if (count($matches[1]) > 0) {
                $allItms = $matches[1];
            }  

        }elseif (stristr($exec, 'class="videostream__link link"')) {

            echo '<br>Found class="videostream__link link" extracting items links...';

            //extract all items links
            preg_match_all('/<a class="videostream__link link" .*?href="([^"]+)"/', $exec, $matches);

            //if matches found
            if (count($matches[1]) > 0) {
                $allItms = $matches[1];
            } 

        }else {

            echo '<br>Did not find any items links, please check the page URL or the page structure';
             
        }

    
        // Check returned items count
        if (count($allItms) > 0) {
 
            echo '<br>Valid reply returned with ' . count($allItms) . ' item';

            
            //if option OPT_TE_REVERSE is enabled, reverse the array
            if (!in_array('OPT_TE_REVERSE', $camp_opt)) {
                echo '<br>Reversing posts order...';
                $allItms = array_reverse($allItms);
            }

            //if option OPT_TE_TOP is enabled, get only the first item
            if (in_array('OPT_TE_TOP', $camp_opt)) {
                echo '<br>Getting only the first item...';
                $allItms = array_slice($allItms, 0, 1);
            }

        } else {

            echo '<br>No items found';
            delete_post_meta($camp->camp_id, 'after_tag');

            echo '<br>Keyword have no more images deactivating...';
            $query = "update {$this->wp_prefix}automatic_keywords set keyword_start = -1 where keyword_id=$kid ";
            $this->db->query($query);

            if (!in_array('OPT_NO_DEACTIVATE', $camp_opt)) {
                $this->deactivate_key($camp->camp_id, $keyword);
            }

        }

        echo '<ol>';

        foreach ($allItms as $itemTxt) {

            //echo $itemTxt;

            $item = array(); //ini item array
             
            $item_link = 'https://rumble.com' . $itemTxt; //set item link to the full URL

            //remove the after .html from the item link
            $item_link = preg_replace('/\.html.*/', '.html', $item_link);

            //item id
            $id = $item_id = $this->get_item_id($item_link); 

            $item['item_id'] = $item_id; //set item id
            $item['item_url'] = $item_link; //set item link
 

            $data = (base64_encode(serialize($item)));

            echo '<li> Link:' . $item_link;

             if ($this->is_execluded($camp->camp_id, $item_link)) {
                echo '<-- Excluded';
                continue;
            }

            if (!$this->is_duplicate($item_link)) {
                $query = "INSERT INTO {$this->wp_prefix}automatic_general ( item_id , item_status , item_data ,item_type) values (  '$id', '0', '$data' ,'te_{$camp->camp_id}_$keyword')  ";
                $this->db->query($query);
            } else {
                echo ' <- duplicated <a href="' . get_edit_post_link($this->duplicate_id) . '">#' . $this->duplicate_id . '</a>';
            }
        }

        echo '</ol>';
    }

    /*
     * ---* rumble fetch post ---
     */
    public function rumble_get_post($camp)
    {

        // Campaign options and general fields from db
        $camp_opt = $this->camp_opt;
        $camp_general = $this->camp_general;

        //mocking a keyword
        $keywords = array(
            '*',
        );

        foreach ($keywords as $keyword) {

            $keyword = wp_automatic_trim($keyword);

            // update last keyword
            update_post_meta($camp->camp_id, 'last_keyword', wp_automatic_trim($keyword));

            if (wp_automatic_trim($keyword) != '') {

                // report posting for cg_te_page
                $cg_te_page = wp_automatic_trim($camp_general['cg_rm_page']);
                echo '<br>Posting from Rumble page:' . $cg_te_page;

                // getting links from the db for that keyword
                $query = "select * from {$this->wp_prefix}automatic_general where item_type=  'te_{$camp->camp_id}_$keyword' ";
                $this->used_keyword = $keyword;
                $res = $this->db->get_results($query);

                // when no links lets get new links
                if (count($res) == 0) {

                    // clean any old cache for this keyword
                    $query_delete = "delete from {$this->wp_prefix}automatic_general where item_type='te_{$camp->camp_id}_$keyword' ";
                    $this->db->query($query_delete);

                    // get new fresh items
                    $this->rumble_fetch_items($keyword, $camp);

                    // getting links from the db for that keyword
                    $res = $this->db->get_results($query);
                }

                // check if already duplicated
                // deleting duplicated items
                $res_count = count($res);
                for ($i = 0; $i < $res_count; $i++) {

                    $t_row = $res[$i];

                    $t_data = unserialize(base64_decode($t_row->item_data));

                    $t_link_url = $t_data['item_url'];

                    if ($this->is_duplicate($t_link_url)) {

                        // duplicated item let's delete
                        unset($res[$i]);

                        echo '<br>Rumble item (' . $t_data['item_id'] . ') found cached but duplicated <a href="' . get_permalink($this->duplicate_id) . '">#' . $this->duplicate_id . '</a>';

                        // delete the item
                        $query = "delete from {$this->wp_prefix}automatic_general where id='{$t_row->id}' ";
                        $this->db->query($query);
                    } else {
                        break;
                    }
                }

                // check again if valid links found for that keyword otherwise skip it
                if (count($res) > 0) {

                    // lets process that link
                    $ret = $res[$i];

                    $data = unserialize(base64_decode($ret->item_data));

                    $temp = $data;

                    echo '<br>Found Link:' . $temp['item_url'];
 
                    //try catch to scrape the item
                    try {
                        $item = $this->rumble_scrape_item($temp['item_url']);
                    } catch (Exception $e) {
                        echo '<br>Error: ' . $e->getMessage();
                        continue; // skip this item
                    } 
                    
                    //merge the item with the temp data
                    $item = array_merge($temp, $item);
                      

                    return $item;
                } else {

                    echo '<br>No links found for this keyword';
                }
            } // if trim
        } // foreach keyword
    }

    //get item id from the link
    //https://rumble.com/v6t7y2t-why-these-sx-workers-lost-their-chance-at-having-a-man.html
    //id is v6t7y2t
    public function get_item_id($item_link)
    {

        //extract the id from the link
        $item_id = '';
        if (preg_match('/\/([a-z0-9]+)-/', $item_link, $matches)) {
            $item_id = $matches[1];
        }

       
        return $item_id;
    }

    //function to scrape the item from the link and parse all the data 
    public function rumble_scrape_item($item_link)
    {

        //result array
        $item = array();
        
        //curl get 
        curl_setopt($this->ch, CURLOPT_URL, $item_link);
        $exec = curl_exec($this->ch);

        $x = curl_error($this->ch);

        //http code 
        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        //if not 200 throw error
        if ($http_code != 200) {
            throw new Exception('Rumble item not found or removed, HTTP code: ' . $http_code);
        }

        //if error, throw error
        if (wp_automatic_trim($x) != '') {
            throw new Exception('Rumble item not found or removed, curl error: ' . $x);
        }

        //echo length of returned data
         echo '<br>Rumble item page length:' . strlen($exec) . ' chars';

         //extract title
        preg_match('/<title>(.*?)<\/title>/', $exec, $matches);

        $title = isset($matches[1]) ? $matches[1] : '';


        //if title is empty throw error
        if (wp_automatic_trim($title) == '') {
            throw new Exception('Rumble item title not found or removed');
        }

        //set item title
        $item['item_title'] = $title;
        echo '<br>Rumble item title:' . $title;

        //require class.dom
        require_once 'inc/class.dom.php';
        $wpAutomaticDom = new wpAutomaticDom ( $exec );

        //get content by class="media-description
        $description_arr = $wpAutomaticDom->getContentByClass('content');

        $description = isset($description_arr[0]) ? $description_arr[0] : '';

        //remove all buttons using regex 
        $description = preg_replace('/<button.*?<\/button>/', '', $description);

        //set item description
        $item['item_description'] = trim($description);

        //get channel name by class media-heading-name
        $channel_name_arr = $wpAutomaticDom->getContentByClass('media-heading-name');

        $channel_name = isset($channel_name_arr[0]) ? $channel_name_arr[0] : '';

        //set item channel name
        $item['item_channel_name'] = trim($channel_name);

        //channel link by regex <a class="media-by--a" href="/c/DiscoverCrypto" 
        preg_match('/<a class="media-by--a" href="([^"]+)"/', $exec, $matches);
        $channel_link = isset($matches[1]) ? 'https://rumble.com' . $matches[1] : '';
        //set item channel link
        $item['item_channel_link'] = $channel_link;

        //upvotes using regex <span data-js="rumbles_up_votes">
        preg_match('{span data-js="rumbles_up_votes">(.*?)</span>}s', $exec, $matches);
        $upvotes = isset($matches[1]) ? trim($matches[1]) : '0';

        //set item upvotes
        $item['item_upvotes'] = $upvotes;

        //down vote
        preg_match('{span data-js="rumbles_down_votes">(.*?)</span>}s', $exec, $matches);
        $downvotes = isset($matches[1]) ? trim($matches[1]) : '0';

        //set item downvotes
        $item['item_downvotes'] = $downvotes;

        //tags class="media-description-tags-container"/a
        $tags_arr = $wpAutomaticDom->getContentByXPath('//div[@class="media-description-tags-container"]/a');

        //if count tags is not 0, implode by comma
        if (count($tags_arr) > 0) {

            //trim each
            $tags_arr = array_map('trim', $tags_arr);

            $item['item_tags'] = implode(',', $tags_arr);
        } else {
            $item['item_tags'] = '';
        }

        //"thumbnailUrl":"
        preg_match('/"thumbnailUrl":"([^"]+)"/', $exec, $matches);

        $thumbnail = isset($matches[1]) ? $matches[1] : '';

        //set item thumbnail
        $item['item_image'] = $thumbnail;

        //"uploadDate":"2025-04-22T13:34:59+00:00"
        preg_match('/"uploadDate":"([^"]+)"/', $exec, $matches);
        $upload_date = isset($matches[1]) ? $matches[1] : '';
        
        //set item upload date
        $item['item_upload_date'] = $upload_date;

        //"embedUrl":"
        preg_match('/"embedUrl":"([^"]+)"/', $exec, $matches);
        $embed_url = isset($matches[1]) ? $matches[1] : '';

        //set item embed url
        $item['item_embed_url'] = $embed_url;

        //embed iframe
        $item['item_embed_iframe'] = '<iframe width="560" height="315" src="' . $embed_url . '" frameborder="0" allowfullscreen></iframe>';

        //tags_to_set to item_tags if OPT_RM_TAGS is set
        if (in_array('OPT_RM_TAGS', $this->camp_opt) && isset($item['item_tags']) && wp_automatic_trim($item['item_tags']) != '') {
            $item['tags_to_set'] = $item['item_tags'];
        }  

        //return item
        return $item;


    }

}
