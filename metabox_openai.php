<?php

// Globals
global $post;
global $wpdb;
global $camp_general;
global $post_id;
global $camp_options;
global $post_types;
global $camp_post_category;

 
?>

<div class="TTWForm-container" dir="ltr">
    <div class="TTWForm">
        <div class="panes">

            <p>Now you can use [gpt] shortcode on the post template above, here are examples:-<br>

            <ol>
                <li><strong>[gpt]Summarize this content to 100 words: [matched_content][/gpt]</strong>

                    <p>This should summarize the content of the article and return only 100 words</p>

                </li>
                <li><strong>[gpt]Write an article in French about: [original_title] [/gpt]</strong>

                    <p>This should write an article in French language about the title</p>


                </li>
                <li><strong>[gpt]rewrite this title in other words: [original_title][/gpt]</strong>

                    <p>This should rewrite the title</p>

                </li>
                <li><strong>[gpt]rewrite this content and keep HTML tags as is: [matched_content][/gpt]</strong>

                    <p>This should rewrite the content and keep the HTML tags</p>

                 </li>



            </ol>

            </p>

            <!-- Backward compatibility if OPT_USE_OPENROUTER is set set the cg_openai_provider to openrouter -->
            <?php
            if ( stristr($camp_options, 'OPT_USE_OPENROUTER') !== false ) {
                $camp_general['cg_openai_provider'] = 'openrouter';
            }
            ?>

            <!-- Selectbox field to select the AI provider from OpenAI,OpenRouter,Google Gemini-->
            <div class="field f_100">
                <label>
                    Select AI provider
                </label>
                <select name="cg_openai_provider">
                    <option value="openai" <?php echo isset($camp_general['cg_openai_provider']) && $camp_general['cg_openai_provider'] == 'openai' ? 'selected' : '' ?>>OpenAI</option>
                    <option value="openrouter" <?php echo isset($camp_general['cg_openai_provider']) && $camp_general['cg_openai_provider'] == 'openrouter' ? 'selected' : '' ?>>OpenRouter</option>
                    <option value="google_gemini" <?php echo isset($camp_general['cg_openai_provider']) && $camp_general['cg_openai_provider'] == 'google_gemini' ? 'selected' : '' ?>>Google Gemini</option>
                </select>
                <div class="description">Select the AI provider you want to use for this campaign</div>

                <div class="clear"></div>
            </div>


            

            <!-- checkbox field to set the post status to pending if openai prompt failed -->
            <div class="field f_100">
                <div class="option clearfix">
                    <input name="camp_options[]" value="OPT_OPENAI_PENDING" type="checkbox">
                    <span class="option-title">
                        Set post status to pending if AI prompt failed
                    </span>
                    <br>
                    <div class="description">Enable if you want the article post status to be set to pending if the prompt failed processing for any reason</div>
                </div>
                <div class="clear"></div>
            </div>

            <div class="field f_100">
                    <div class="option clearfix">
                        <input data-controls="wp_automatic_openai_advanced" name="camp_options[]" value="OPT_OPENAI_CUSTOM" type="checkbox">
                        <span class="option-title">
                            Modify OpenAI call parameters (advanced)
                        </span>
                    </div>

                    <div id="wp_automatic_openai_advanced" class = "field f_100">


                    <!-- model field -->
             

                <!-- model selection field -->
                <label>
                            OpenAI Model
                        </label>

                        <!-- model selection field gpt3.5-turbo, 	gpt-4, gpt-4-0314, gpt-4-32k, gpt-4-32k-0314, gpt-3.5-turbo, gpt-3.5-turbo-0301 -->
                        <select name="cg_openai_model">
                            <option value="gpt-4o-mini" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4o-mini' ? 'selected' : '' ?>>gpt-4o-mini (128k) (BEST) ($0.15 • $0.6)</option>
                            <option value="gpt-5-mini" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-5-mini' ? 'selected' : '' ?>>gpt-5-mini (128k) (BEST) ($0.25 • $2)</option>
                            <option value="gpt-5" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-5' ? 'selected' : '' ?>>gpt-5 (128k) (BEST) ($0.25 • $2)</option>
                            <option value="gpt-4.1-nano" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4.1-nano' ? 'selected' : '' ?>>gpt-4.1-nano (Cheapest 4.1 model)</option>
                            <option value="gpt-4.1-mini" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4.1-mini' ? 'selected' : '' ?>>gpt-4.1-mini</option>
                            <option value="gpt-4.1" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4.1' ? 'selected' : '' ?>>gpt-4.1</option>
                        
                            <option value="gpt-4o" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4o' ? 'selected' : '' ?>>gpt-4o (128k) (NEW)  (Output limited to 4k tokens)</option>    
                            
                            
                            <option value="gpt-4" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4' ? 'selected' : '' ?>>gpt-4 (OLD) (Up to Sep 2021)                            </option>
                            <option value="gpt-4-turbo" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4-turbo' ? 'selected' : '' ?>>gpt-4-turbo (128k)</option>
                            <option value="gpt-4-turbo-preview" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4-turbo-preview' ? 'selected' : '' ?>>gpt-4-turbo-preview (128k)</option>
                            <option value="gpt-4-0613" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4-0613' ? 'selected' : '' ?>>gpt-4-0613 (OLD)</option>
                            <option value="gpt-4-0314" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4-0314' ? 'selected' : '' ?>>gpt-4-0314 (OLD)</option>

                            <option value="gpt-3.5-turbo" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-3.5-turbo' ? 'selected' : '' ?>>gpt-3.5-turbo</option>
                            <option value="gpt-4.5-preview" <?php echo isset($camp_general['cg_openai_model']) && $camp_general['cg_openai_model'] == 'gpt-4.5-preview' ? 'selected' : '' ?>>gpt-4.5-preview (SMARTEST) (Expensive) (Latest)</option>
                            

                        </select>
                        <div class="description">Model gpt-4o-mini is affordable and intelligent small model for fast, lightweight tasks.<br> GPT-4o is the high-intelligence flagship model for complex, multi-step tasks<br><br>Full list of models <a href="https://platform.openai.com/docs/models">here</a>.</div>

                        <br>

                        <!-- enable web search in this campiagn checkbox -->
                        <div class="option clearfix">
                            <input name="camp_options[]" value="OPT_OPENAI_WEB_SEARCH" type="checkbox" <?php echo isset($camp_options) && stristr($camp_options, 'OPT_OPENAI_WEB_SEARCH') !== false ? 'checked' : '' ?>>
                            <span class="option-title">
                                Enable web search in this campaign
                            </span>
                            <br>
                            <div class="description">Enable this option if you want the AI to search the web for information before generating the content. This will make the AI more accurate and up-to-date, but it will also increase the processing time and cost of the request.</div>
                        </div>

                        <!-- temprature field -->
                        <label for="field6">
                            Temperature (Optional)(Dangerous)
                        </label>
                        <input name="cg_openai_temp" value="<?php echo isset($camp_general['cg_openai_temp']) ? $camp_general['cg_openai_temp'] : '' ?>" type="text">
                        <div class="description">What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. Defaults to 1<br><br>Tests showed that setting this value to something high makes the request processing time go from 30 seconds to more than 5 minutes, better leave as-is.</div>

                        <br>



                        <!-- top_p field -->
                        <label for="field6">
                            Top_p (Optional)
                        </label>
                        <input name="cg_openai_top_p" value="<?php echo isset($camp_general['cg_openai_top_p']) ? $camp_general['cg_openai_top_p'] : '' ?>" type="text">
                        <div class="description">An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.

We generally recommend altering this or temperature but not both. Defaults to 1.</div>

                        <br>
                        <!-- presence_penalty field -->
                        <label>
                            Presence_penalty (Optional)
                        </label>
                        <input name="cg_openai_presence_penalty" value="<?php echo isset($camp_general['cg_openai_presence_penalty']) ? $camp_general['cg_openai_presence_penalty'] : '' ?>" type="text">
                        <div class="description">Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics. Defaults to 0.</div>
                        <br>

                        <!-- frequency_penalty field -->
                        <label>
                            Frequency_penalty (Optional)
                        </label>
                        <input name="cg_openai_frequency_penalty" value="<?php echo isset($camp_general['cg_openai_frequency_penalty']) ? $camp_general['cg_openai_frequency_penalty'] : '' ?>" type="text">
                        <div class="description">Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim. Defaults to 0.</div>

                        <!-- Fine tuned model -->
                        <br>
                        <label>
                            Fine tuned model (Optional)
                        </label>
                        <input name="cg_openai_fine_tuned_model" value="<?php echo isset($camp_general['cg_openai_fine_tuned_model']) ? $camp_general['cg_openai_fine_tuned_model'] : '' ?>" type="text">
                        <div class="description">If you have a fine tuned model, you can use it here, if you do not have one, leave this field empty.</div>

                        <!-- select Image generation model gpt-image-1,dall-e-3 -->

                        

                        <!-- Dalle 3 image size select Must be one of 1024x1024, 1792x1024, or 1024x1792 -->
                        <br>
                        <label>
                            Image generation model (Optional)
                        </label>
                        <select name="cg_openai_image_generation_model">
                            <option value="dall-e-3" <?php echo isset($camp_general['cg_openai_image_generation_model']) && $camp_general['cg_openai_image_generation_model'] == 'dall-e-3' ? 'selected' : '' ?>>dall-e-3</option>
                            <option value="gpt-image-1" <?php echo isset($camp_general['cg_openai_image_generation_model']) && $camp_general['cg_openai_image_generation_model'] == 'gpt-image-1' ? 'selected' : '' ?>>gpt-image-1 (*Require organization verification)</option>
                        </select>

                        <!-- Dalle 3 image size select Must be one of 1024x1024, 1792x1024, or 1024x1792 -->
                        <br>
                        <label>
                            Dalle 3 image size (Optional)
                        </label>
                        <select name="cg_openai_dalle_image_size">
                            
                            <!-- Must be one of 1024x1024, 1536x1024 (landscape), 1024x1536 (portrait), or auto (default value) for gpt-image-1 -->
                            <option value="1024x1024" <?php echo isset($camp_general['cg_openai_dalle_image_size']) && $camp_general['cg_openai_dalle_image_size'] == '1024x1024' ? 'selected' : '' ?>>1024x1024 (gpt-image-1)(dall-e-3)</option>
                            <option value="auto" <?php echo isset($camp_general['cg_openai_dalle_image_size']) && $camp_general['cg_openai_dalle_image_size'] == 'auto' ? 'selected' : '' ?>>auto (gpt-image-1)</option>
                            
                            <option value="1536x1024" <?php echo isset($camp_general['cg_openai_dalle_image_size']) && $camp_general['cg_openai_dalle_image_size'] == '1536x1024' ? 'selected' : '' ?>>1536x1024 (landscape) (gpt-image-1)</option>
                            <option value="1024x1536" <?php echo isset($camp_general['cg_openai_dalle_image_size']) && $camp_general['cg_openai_dalle_image_size'] == '1024x1536' ? 'selected' : '' ?>>1024x1536 (portrait) (gpt-image-1)</option>

                            <!-- Dalle 3-->
                            
                            <option value="1792x1024" <?php echo isset($camp_general['cg_openai_dalle_image_size']) && $camp_general['cg_openai_dalle_image_size'] == '1792x1024' ? 'selected' : '' ?>>1792x1024 (dall-e-3)</option>
                            <option value="1024x1792" <?php echo isset($camp_general['cg_openai_dalle_image_size']) && $camp_general['cg_openai_dalle_image_size'] == '1024x1792' ? 'selected' : '' ?>>1024x1792 (dall-e-3)</option>
                        </select>

                        <!-- Dalle 3 image style select must be vivid or natural -->
                        <br>
                        <label>
                            Dalle 3 image style (Optional)(dall-e-3 model only)
                        </label>
                        <select name="cg_openai_dalle_image_style">
                        <option value="natural" <?php echo isset($camp_general['cg_openai_dalle_image_style']) && $camp_general['cg_openai_dalle_image_style'] == 'natural' ? 'selected' : '' ?>>natural</option>    
                        <option value="vivid" <?php echo isset($camp_general['cg_openai_dalle_image_style']) && $camp_general['cg_openai_dalle_image_style'] == 'vivid' ? 'selected' : '' ?>>vivid</option>
                        </select><div class = "description">The style of the generated images. Must be one of vivid or natural. Vivid causes the model to lean towards generating hyper-real and dramatic images. Natural causes the model to produce more natural, less hyper-real looking images</div>


                    </div>


                <div class="clear"></div>
            </div>

            <!-- Modify OpenRouter model parameters (advanced) -->
            <div class="field f_100">
                <div class="option clearfix">
                    <input data-controls="openrouter_advanced" name="camp_options[]" value="OPT_OPENROUTER_CUSTOM" type="checkbox">
                    <span class="option-title">
                        Modify OpenRouter call parameters (advanced)
                    </span>
                </div>

                <div id="openrouter_advanced" class = "field f_100">

                <label>
                        OpenRouter Model (Optional)
                    </label>
                    <input name="cg_openrouter_model" value="<?php echo isset($camp_general['cg_openrouter_model']) ? $camp_general['cg_openrouter_model'] : '' ?>" type="text">
                    <div class="description">Enter the OpenRouter model name here, visit <a href="https://openrouter.ai/models" target="_blank">OpenRouter Models</a> to get the model name. example: google/gemini. <br><br>*If left empty,defaults to the model added to the settings page</div>

                </div>

                <div class="clear"></div>
            </div>

            <!-- Modify Google Gemini model parameters (advanced) -->
            <div class="field f_100">
                <div class="option clearfix">
                    <input data-controls="google_gemini_advanced" name="camp_options[]" value="OPT_GOOGLE_GEMINI_CUSTOM" type="checkbox">
                    <span class="option-title">
                        Modify Google Gemini call parameters (advanced)
                    </span>
                </div>

                <div id="google_gemini_advanced" class = "field f_100">

                <label>
                        Google Gemini Model (Optional)
                    </label>
                    <input name="cg_google_gemini_model" value="<?php echo isset($camp_general['cg_google_gemini_model']) ? $camp_general['cg_google_gemini_model'] : '' ?>" type="text">
                    <div class="description">Enter the Google Gemini model name here, visit <a href="https://ai.google.dev/gemini-api/docs/models" target="_blank">Gemini Models</a> to get the model name. example: gemini-2.0-flash. <br><br>*If left empty,defaults to the model added to the settings page</div>

                </div>

                <div class="clear"></div>
            </div>



        </div>
    </div>
</div>