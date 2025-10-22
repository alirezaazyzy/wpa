<?php

// Main Class
require_once 'core.php';

class WpAutomaticMedium extends wp_automatic
{

    private $item_html = '';


    /**
     * Retrieves a post from Medium based on the provided campaign data.
     *
     * @param array $camp The campaign data used to fetch the Medium post.
     * @return mixed The retrieved Medium post data, or an error/false on failure.
     */
    public function medium_get_post($camp)
    {

        //ini keywords
        $camp_opt = $this->camp_opt;
        $camp_general = $this->camp_general;

        //page url cg_md_page
        $page_url = isset($camp_general['cg_md_page']) ? $camp_general['cg_md_page'] : '';

        //report page url 
        echo '<br>Page URL: ' . $page_url;

        $keyword = $page_md5 = md5($page_url);

        //when valid keyword
        if (wp_automatic_trim($page_url) != '') {



            // getting links from the db for that keyword
            $query = "select * from {$this->wp_prefix}automatic_general where item_type=  'md_{$camp->camp_id}_$keyword' ";
            $res = $this->db->get_results($query);

            // when no links lets get new links
            if (count($res) == 0) {

                //clean any old cache for this keyword
                $query_delete = "delete from {$this->wp_prefix}automatic_general where item_type='md_{$camp->camp_id}_$keyword' ";
                $this->db->query($query_delete);

                //get new links
                $this->places_fetch_items($page_url, $camp);

                // getting links from the db for that keyword
                $res = $this->db->get_results($query);
            }

            //check if already duplicated
            //deleting duplicated items
            $res_count = count($res);
            for ($i = 0; $i < $res_count; $i++) {

                $t_row = $res[$i];

                $t_data = unserialize(base64_decode($t_row->item_data));

                $t_link_url = $t_data['item_url'];

                if ($this->is_duplicate($t_link_url)) {

                    //duplicated item let's delete
                    unset($res[$i]);

                    echo '<br>Item (' . $t_data['item_url'] . ') found cached but duplicated <a href="' . get_permalink($this->duplicate_id) . '">#' . $this->duplicate_id . '</a>';

                    //delete the item
                    $query = "delete from {$this->wp_prefix}automatic_general where id= {$t_row->id} ";
                    $this->db->query($query);
                } else {
                    break;
                }
            }

            // check again if valid links found for that keyword otherwise skip it
            if (count($res) > 0) {

                // lets process that link
                $ret = $res[$i];

                $temp = unserialize(base64_decode($ret->item_data));

                //report link
                echo '<br>Found Link:' . $temp['item_url'];

                // update the link status to 1
                $query = "delete from {$this->wp_prefix}automatic_general where id={$ret->id}";
                $this->db->query($query);

                // if cache not active let's delete the cached videos and reset indexes
                if (!in_array('OPT_MD_CACHE', $camp_opt)) {
                    echo '<br>Cache disabled claring cache ...';
                    $query = "delete from {$this->wp_prefix}automatic_general where item_type='md_{$camp->camp_id}_$keyword' ";
                    $this->db->query($query);

                    // reset index
                    $query = "update {$this->wp_prefix}automatic_keywords set keyword_start =1 where keyword_camp={$camp->camp_id}";
                    $this->db->query($query);
                }

                //test
                //$temp['item_url'] = 'https://medium.com/@lilikazemi/llm-deployment-models-a-101-for-executives-eeec64522946';

                //load original item url 
                curl_setopt($this->ch, CURLOPT_URL, $temp['item_url']);

                //curl GET
                curl_setopt($this->ch, CURLOPT_HTTPGET, true);


                echo '<br>Loading item from Medium ...';

                //exec
                $html = curl_exec($this->ch);



                //echo size
                echo '<-- Return size: ' . strlen($html) . ' bytes';

                //report error 
                if (curl_errno($this->ch)) {
                    echo '<br>Error: ' . curl_error($this->ch);
                    return false;
                }

                //if not title found
                if (strpos($html, '<title') === false) {
                    echo '<br>No title found';
                    return false;
                }

                //set the item HTML 
                $this->item_html = $html;

                //title
                $temp['item_title'] = $this->get_title();

                //author name
                $temp['item_author'] = $this->get_author();

                //item image
                $temp['item_image'] = $this->get_image_url();

                //item content
                $temp['item_description'] = $this->get_content();

                //likes count
                $temp['item_likes'] = $this->get_likes_count();

                //story read time
                $temp['item_story_read_time'] = $this->get_story_read_time();

                //tags 
                $temp['item_tags'] = $this->get_tags();

                //if option OPT_MD_TAGS is set then set the tags_to_set to item_tags
                if (in_array('OPT_MD_TAGS', $camp_opt)) {
                    $temp['tags_to_set'] = $temp['item_tags'];
                }

                //if option OPT_MD_AUTHOR is set then set the author_to_set to item_author
                if (in_array('OPT_MD_AUTHOR', $camp_opt)) {
                    $temp['author_to_set'] = $temp['item_author'];
                }
                

                return $temp;
            } else {

                echo '<br>No links found for this keyword';
            }
        } // if trim


    }

    /**
     * function places_fetch_items: get new items from places for specific keyword
     * @param unknown $keyword
     * @param unknown $camp
     */
    public function places_fetch_items($page_url, $camp)
    {

        //report
        echo "<br>So I should now get some articles from Medium using :" . $page_url;

        //keyword md5 of the page
        $keyword = md5($page_url);

        // ini options
        $camp_opt = $this->camp_opt;
        $camp_general = $this->camp_general;

        // get start-index for this keyword from a custom field 
        $kid = $camp->camp_id;
        $start = get_post_meta($camp->camp_id, 'wp_places_start_index_' . $keyword, 1);

        echo '<br>Start index: ' . $start;

        if ($start == -1) {
            echo '<- exhausted page';

            if (!in_array('OPT_MD_CACHE', $camp_opt)) {
                $start = 1;
                echo '<br>Cache disabled resetting index to 1';
            } else {

                //check if it is reactivated or still deactivated
                if ($this->is_deactivated($camp->camp_id, $keyword)) {
                    $start = 1;
                } else {
                    //still deactivated
                    return false;
                }
            }
        } elseif (!in_array('OPT_MD_CACHE', $camp_opt)) {
            $start = '';
            echo '<br>Cache disabled resetting index to 1';

            //delete next page token
            delete_post_meta($camp->camp_id, 'wp_places_bookmark_' . $keyword);
        }



        //load the page URL 
        $page_url = wp_automatic_trim($page_url);

        if ($page_url == '') {
            echo '<br>Page URL is empty, skipping...';
            return false;
        }

        //type cg_md_type
        $type = isset($camp_general['cg_md_type']) ? $camp_general['cg_md_type'] : 'tag';


        //class wp_automatic_MediumScraper
        require_once 'inc/class.medium.php';
        $scraper = new wp_automatic_MediumScraper($this->ch);

        //fetch articles by tag
        try {
            $links = $scraper->fetchArticles($page_url, $start, $type);
            $cursor = $scraper->cursor; //get the cursor for pagination

        } catch (Exception $e) {
            echo '<br>Error fetching articles: ' . $e->getMessage();

            //reset cursor
            delete_post_meta($camp->camp_id, 'wp_places_start_index_' . $keyword);

            return false;
        }

        //validating reply
        if (is_array($links) && count($links) > 0) {
            //valid reply

            echo '<ol>';

            //loop items
            $i = 0;
            foreach ($links as $item) {

                echo '<li>';
                echo 'Item URL: ' . $item;

                //item_url from place id
                $itm['item_url'] = $item;
                $itm['item_id'] = md5($item);
                $data = base64_encode(serialize($itm));

                if ($this->is_execluded($camp->camp_id, $itm['item_url'])) {
                    echo '<-- Excluded';
                    continue;
                }

                if (!$this->is_duplicate($itm['item_url'])) {
                    $query = "INSERT INTO {$this->wp_prefix}automatic_general ( item_id , item_status , item_data ,item_type) values (    '{$itm['item_id']}', '0', '$data' ,'md_{$camp->camp_id}_$keyword')  ";
                    $this->db->query($query);
                } else {
                    echo ' <- duplicated <a href="' . get_edit_post_link($this->duplicate_id) . '">#' . $this->duplicate_id . '</a>';
                }

                echo '</li>';
                $i++;
            }

            echo '</ol>';

            echo '<br>Total ' . $i . ' items found & cached';

            //check if nothing found so deactivate
            if ($i == 0) {
                echo '<br>No new items found ';
                echo '<br>Link has no more items deactivating...';

                delete_post_meta($camp->camp_id, 'wp_places_start_index_' . $keyword);

                if (!in_array('OPT_NO_DEACTIVATE', $camp_opt)) {
                    $this->deactivate_key($camp->camp_id, $keyword);
                }

                //delete bookmark value
                delete_post_meta($camp->camp_id, 'wp_places_bookmark' . md5($keyword));
            } else {

                echo '<br>Updating next start index to ' . $cursor;

                //save wp_places_start_index_ . $keyword
                update_post_meta($camp->camp_id, 'wp_places_start_index_' . $keyword, $cursor);
            }
        } else {

            //no valid reply
            echo '<br>Invalid reply, no places found for this keyword';
        }
    }

    /**

     * function get_title: extract title from item HTML

     * @return string

     */

    private function get_title()

    {


        preg_match('/<title.*?>(.*?)<\/title>/', $this->item_html, $matches);

        //remove additional text from the title ? | by Stephanie Shen | ILLUMINATION | Jul, 2025 | Medium

        $matches[1] = preg_replace('/\s*\|\s*.*$/', '', $matches[1]);

        //title found

        if (isset($matches[1])) {

            echo '<br>Title found: ' . $matches[1];

            return $matches[1];
        } else {

            return '';
        }
    }

    /**

     * function get_author: extract author name from item HTML

     * @return string

     */
    private function get_author()
    {
        //extract author name  name="author" content="Fareed Khan"
        preg_match('/name="author" content="([^"]+)"/', $this->item_html, $matches);

        //author found
        if (isset($matches[1])) {
            echo '<br>Author found: ' . $matches[1];
            return $matches[1];
        } else {
            echo '<br>No author found';
            return '';
        }
        return '';
    }

    /**
     * function get_image_url: get image url from og:image
     * @return string
     */
    private function get_image_url()
    {
        //get image url from og:image
        preg_match('/og:image" content="([^"]+)"/', $this->item_html, $matches);
        if (isset($matches[1])) {
            echo '<br>Image URL found: ' . $matches[1];
            return $matches[1];
        } else {
            echo '<br>No Image URL found';
            return '';
        }
    }

    /**
     * function get_content: get content from the first paragraph with class pw-post-body-paragraph
     * @return string
     */
    private function get_content()
    {

        //get the first paragraph with class pw-post-body-paragraph then get its parent div content then return

        //dom document
        $dom = new DOMDocument();

        $html = $this->item_html;

        //add meta tag utf-8 to avoid wrong formatting
        $charSetMeta = '<meta http-equiv="content-type" content="text/html; charset="utf-8"/>';

        //replace <head.*> with the meta tag
        $html = preg_replace('/<head.*?>/i', '<head>' . $charSetMeta, $html);

        @$dom->loadHTML($html);

        $xpath = new DOMXPath($dom);

        //remove the title data-testid="storyTitle"
        $titles_to_remove = $xpath->query('//h1[@data-testid="storyTitle"]');
        foreach ($titles_to_remove as $title) {
            $title->parentNode->removeChild($title);
        }

        //remove the div with the class speechify-ignore which contains the likes and comments box
        $divs_to_remove = $xpath->query('//*[contains(@class, "speechify-ignore")]');
        foreach ($divs_to_remove as $div) {
            $div->parentNode->removeChild($div);
        }


        //query for section 
        $section = $xpath->query('//section');

        //save html of the first section
        if ($section->length > 0) {
            $section_html = $dom->saveHTML($section[0]);
        }

        //return section html if not empty
        if (!empty($section_html)) {
            return $section_html;
        }



        echo '<br>No content found';
        return '';
    }

    /**
     * function get_likes_count: get likes count from the item HTML
     * @return int
     */
    private function get_likes_count()
    {
        //get likes count from the item HTML ,"clapCount":280
        preg_match('/"clapCount":(\d+)/', $this->item_html, $matches);

        //likes count found
        if (isset($matches[1])) {
            echo '<br>Likes count found: ' . $matches[1];
            return (int)$matches[1];
        } else {
            echo '<br>No likes count found';
            return 0;
        }
    }

    //story read time "storyReadTime">9 min read</span>
    private function get_story_read_time()
    {
        //get story read time from the item HTML
        preg_match('/"storyReadTime">(\d+ min read)<\/span>/', $this->item_html, $matches);

        //story read time found
        if (isset($matches[1])) {
            echo '<br>Story read time found: ' . $matches[1];
            return $matches[1];
        } else {
            echo '<br>No story read time found';
            return '';
        }
    }

    /**
     * function get_tags: get tags from the item HTML
     * @return string
     */
    private function get_tags()
    {
        //get tags from the item HTML from display title "displayTitle":"Football Transfers","normalizedTagSlug"
        preg_match_all('/"displayTitle":"([^"]+)","normalizedTagSlug/', $this->item_html, $matches);

        //tags found
        if (isset($matches[1]) && count($matches[1]) > 0) {
            $tags = array_map('trim', $matches[1]);
            echo '<br>Tags found: ' . implode(', ', $tags);
            return implode(', ', $tags);
        } else {
            echo '<br>No tags found';
            return '';
        }
    }
}//end class