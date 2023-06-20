<?php
/**
 *
 * Plugin Name: Microblog Poster Pro Add-on
 * Plugin URI: http://efficientscripts.com/microblogposterpro
 * Description: Provides additional features to Microblog Poster Wordpress plugin.
 * Version: 1.8.3
 * Author: Efficient Scripts
 * Author URI: http://efficientscripts.com/
 * Text Domain: microblog-poster-pro
 *
 */


add_action('init', 'mbp_pro_activate_au_microblogposter');
function mbp_pro_activate_au_microblogposter()
{
    require_once "microblogposterpro_curl.php";
    require_once "microblogposterpro_auto_update.php";
    $plugin_current_version = '1.8.3';
    $plugin_remote_path = 'http://efficientscripts.com/api/microblogposterpro_api_auto_update.php';
    $plugin_slug = plugin_basename(__FILE__);
    $customer_license_key_name = "microblogposterpro_plg_customer_license_key";
    $customer_license_key_value_enc = get_option($customer_license_key_name, "");
    $customer_license_key_value_arr = json_decode($customer_license_key_value_enc, true);

    $customer_license_key_value = '';
    if(is_array($customer_license_key_value_arr))
    {
        $customer_license_key_value = $customer_license_key_value_arr['key'];
    }
    
    new MicroblogPosterPro_Auto_Update($plugin_current_version, $plugin_remote_path, $plugin_slug, $customer_license_key_value);
    
}

add_action('plugins_loaded', 'microblogposter_mbp_pro_load_languages');
function microblogposter_mbp_pro_load_languages()
{
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('microblog-poster-pro', false, $plugin_dir . '/languages');
}

class MicroblogPoster_Poster_Pro
{
    /**
    * Updates facebook page
    *
    * @param array  $acc_extra
    * @param array $post_data 
    * @return string
    */
    public static function update_facebook_page($curl, $acc_extra, $post_data)
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            return __( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' );
        }
        
        $url = "https://graph.facebook.com/{$acc_extra['page_id']}/feed";
        $post_args = array(
            'access_token' => $acc_extra['access_token'],
            'message' => $post_data['update']
        );

        if(isset($acc_extra['post_type']) && $acc_extra['post_type'] == 'link')
        {
            $post_args['name'] = $post_data['post_title'];
            $post_args['link'] = $post_data['permalink'];
            $post_args['description'] = $post_data['post_content_actual'];
            $picture_url = '';
            if(isset($acc_extra['default_image_url']) && $acc_extra['default_image_url'])
            {
                $picture_url = $acc_extra['default_image_url'];
            }
            if($post_data['featured_image_src'])
            {
                $picture_url = $post_data['featured_image_src'];
            }
            $post_args['picture'] = $picture_url;
        }

        $result = $curl->send_post_data($url, $post_args);
        
        return $result;
    }
    
    /**
    * Updates facebook group
    *
    * @param array  $acc_extra
    * @param array $post_data 
    * @return string
    */
    public static function update_facebook_group($curl, $acc_extra, $post_data)
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            return __( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' );
        }
        
        $url = "https://graph.facebook.com/{$acc_extra['group_id']}/feed";
        $post_args = array(
            'access_token' => $acc_extra['access_token'],
            'message' => $post_data['update']
        );

        if(isset($acc_extra['post_type']) && $acc_extra['post_type'] == 'link')
        {
            $post_args['name'] = $post_data['post_title'];
            $post_args['link'] = $post_data['permalink'];
            $post_args['description'] = $post_data['post_content_actual'];
            $picture_url = '';
            if(isset($acc_extra['default_image_url']) && $acc_extra['default_image_url'])
            {
                $picture_url = $acc_extra['default_image_url'];
            }
            if($post_data['featured_image_src'])
            {
                $picture_url = $post_data['featured_image_src'];
            }
            $post_args['picture'] = $picture_url;
        }

        $result = $curl->send_post_data($url, $post_args);
        
        return $result;
    }
    
    /**
    * Updates linkedin group
    *
    * @param array  $acc_extra
    * @param array $post_data 
    * @return string
    */
    public static function update_linkedin_group($curl, $acc_extra, $post_data)
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            return __( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' );
        }
        
        $url = "https://api.linkedin.com/v1/groups/{$acc_extra['group_id']}/posts/?oauth2_access_token={$acc_extra['access_token']}";
                    
        $body = new stdClass();
        $body->title = $post_data['post_title'];
        $body->summary = $post_data['update'];

        if(isset($acc_extra['post_type']) && $acc_extra['post_type'] == 'link')
        {
            $body->content = new stdClass();
            $body->content->title = $post_data['post_title'];
            $body->content->{'submitted-url'} = $post_data['permalink'];
            $body->content->description = $post_data['post_content_actual'];
            $picture_url = 'http://localhost/imageplaceholder.jpg';// 180 wid, 110 hei
            if(isset($acc_extra['default_image_url']) && $acc_extra['default_image_url'])
            {
                $picture_url = $acc_extra['default_image_url'];
            }
            if($post_data['featured_image_src'])
            {
                $picture_url = $post_data['featured_image_src'];
            }
            $body->content->{'submitted-image-url'} = $picture_url;
        }

        $body_json = json_encode($body);

        $curl->set_headers(array('Content-Type'=>'application/json'));
        $result = $curl->send_post_data_json($url, $body_json);
        
        return $result;
    }
    
    /**
    * Updates linkedin company
    *
    * @param array  $acc_extra
    * @param array $post_data 
    * @return string
    */
    public static function update_linkedin_company($curl, $acc_extra, $post_data)
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            return __( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' );
        }
        
        $url = "https://api.linkedin.com/v1/companies/{$acc_extra['company_id']}/shares/?oauth2_access_token={$acc_extra['access_token']}";
                    
        $body = new stdClass();
        $body->comment = $post_data['update'];

        if(isset($acc_extra['post_type']) && $acc_extra['post_type'] == 'link')
        {
            $body->content = new stdClass();
            $body->content->title = $post_data['post_title'];
            $body->content->{'submitted-url'} = $post_data['permalink'];
            $body->content->description = $post_data['post_content_actual'];
            $picture_url = 'http://localhost/imageplaceholder.jpg';// 180 wid, 110 hei
            if(isset($acc_extra['default_image_url']) && $acc_extra['default_image_url'])
            {
                $picture_url = $acc_extra['default_image_url'];
            }
            if($post_data['featured_image_src'])
            {
                $picture_url = $post_data['featured_image_src'];
            }
            $body->content->{'submitted-image-url'} = $picture_url;
            $body->visibility = new stdClass();
            $body->visibility->code = 'anyone';
        }

        $body_json = json_encode($body);

        $curl->set_headers(array('Content-Type'=>'application/json'));
        $result = $curl->send_post_data_json($url, $body_json);
        
        return $result;
    }
    
    /**
    * Updates tumblr link
    *
    * @param array  $tumblr_account
    * @param array $acc_extra
    * @param array $post_data 
    * @return string
    */
    public static function update_tumblr_link($tumblr_account, $acc_extra, $post_data)
    {
        if(MicroblogPoster_Poster_Pro::is_method_callable('MicroblogPoster_Poster', 'send_signed_request'))
        {
            $result = MicroblogPoster_Poster::send_signed_request(
                $tumblr_account['consumer_key'],
                $tumblr_account['consumer_secret'],
                $tumblr_account['access_token'],
                $tumblr_account['access_token_secret'],
                "http://api.tumblr.com/v2/blog/{$acc_extra['blog_hostname']}/post",
                array("type"=>'link',
                        "title"=>$post_data['post_title'],
                        "url"=>$post_data['permalink'],
                        "description"=>$post_data['post_content_actual']
                )
            );
            return $result;
        }
        
    }
    
    /**
    * Updates vkontakte community
    *
    * @param array  $tumblr_account
    * @param array $acc_extra
    * @param array $post_data 
    * @return string
    */
    public static function update_vkontakte_community($curl, $acc_extra, $post_data)
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            return __( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' );
        }
        
        $url = "https://api.vk.com/method/wall.post";
        $post_args = array(
            'access_token' => $acc_extra['access_token'],
            'owner_id' => '-'.$acc_extra['target_id'],
            'message' => $post_data['message'],
            'from_group' => '1'
        );

        if(isset($acc_extra['post_type']) && $acc_extra['post_type'] == 'link')
        {
            $post_args['attachments'] = $post_data['attachments'];
        }

        $result = $curl->send_post_data($url, $post_args);
        return $result;
    }
    
    /**
    * Sends OAuth signed request
    *
    * @param   string  $c_key Application consumer key
    * @param   string  $c_secret Application consumer secret
    * @param   string  $token Account access token
    * @param   string  $token_secret Account access token secret
    * @param   string  $api_url URL of the API end point
    * @param   string  $params Parameters to be passed
    * @return  void
    */
    public static function send_signed_request_and_upload($curl, $c_key, $c_secret, $token, $token_secret, $api_url, $params)
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            return __( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' );
        }
        
        $consumer = new MicroblogPosterOAuthConsumer($c_key, $c_secret);
        $access_token = new MicroblogPosterOAuthConsumer($token, $token_secret);
        
        $request = MicroblogPosterOAuthRequest::from_consumer_and_token(
                $consumer,
                $access_token,
                "POST",
                $api_url,
                null
        );
        $hmac_method = new MicroblogPosterOAuthSignatureMethod_HMAC_SHA1();
        $request->sign_request($hmac_method, $consumer, $access_token);
        
        $body = new stdClass();
        $body->media = $curl->fetch_url($params['image_url']);
        
        $headers = array(
            'Content-type'  => 'multipart/form-data',
        );
        $curl->set_headers($headers);
        $result = $curl->send_post_data_json($request->to_url(), $body);
        
        return $result;
    }
    
    /**
    * Filters single social account
    *
    * @param int $account_id
    * @return mixed
    */
    public static function filter_single_account($account_id)
    {
        global $wpdb;
        
        $table_accounts = $wpdb->prefix . 'microblogposter_accounts';
        
        $checkbox_name = 'mbp_social_account_microblogposter_'.$account_id;
        $active = false;
        
        if(isset($_POST[$checkbox_name]) && trim($_POST[$checkbox_name]) == '1')
        {
            $active = array();
            
            $message_format = trim($_POST['mbp_social_account_microblogposter_msg_'.$account_id]);
            $active['message_format'] = $message_format;
            $wpdb->escape_by_ref($message_format);
            
            $sql = "UPDATE {$table_accounts}
                SET message_format='{$message_format}' 
                WHERE account_id={$account_id}";

            $wpdb->query($sql);
        }
        
        return $active;
    }
    
    /**
    * Main function of this plugin called on publish_post action hook
    * 
    *
    * @param   int  $post_ID ID of the new/updated post
    * @return  void
    */
    public static function handle_old_posts_publish()
    {
        $microblogposter_plg_old_posts_nb_posts_name = "microblogposter_plg_old_posts_nb_posts";
        $microblogposter_plg_old_posts_min_age_name = "microblogposter_plg_old_posts_min_age";
        $microblogposter_plg_old_posts_max_age_name = "microblogposter_plg_old_posts_max_age";
        $microblogposter_plg_old_posts_expire_age_name = "microblogposter_plg_old_posts_expire_age";
        $excluded_categories_old_name = "microblogposter_excluded_categories_old";
        $enabled_custom_types_old_name = "microblogposter_enabled_custom_types_old";
        
        $nb_posts = get_option($microblogposter_plg_old_posts_nb_posts_name, 1);
        $min_age = get_option($microblogposter_plg_old_posts_min_age_name, 30);
        $max_age = get_option($microblogposter_plg_old_posts_max_age_name, 180);
        $expire_age = get_option($microblogposter_plg_old_posts_expire_age_name, 30);
        $excluded_categories_old_value = get_option($excluded_categories_old_name, "");
        $excluded_categories_old = json_decode($excluded_categories_old_value, true);
        $enabled_custom_types_old_value = get_option($enabled_custom_types_old_name, "");
        $enabled_custom_types_old_value = json_decode($enabled_custom_types_old_value, true);

        global  $wpdb;

        $table_old_items = $wpdb->prefix . 'microblogposter_old_items';
        $table_posts = $wpdb->prefix . 'posts';
        $table_term_relationships = $wpdb->prefix . 'term_relationships';
        $table_term_taxonomy = $wpdb->prefix . 'term_taxonomy';
        
        foreach($enabled_custom_types_old_value as $custom_type)
        {
            if(in_array($custom_type, array('post', 'page')))
            {
                continue;
            }
            
            $sql_old = "SELECT * FROM {$table_posts} AS p WHERE p.post_status = 'publish' AND p.post_type = '{$custom_type}'";
            if($min_age > 0)
            {
                $sql_old .= " AND p.post_date < DATE_SUB(NOW(), INTERVAL {$min_age} DAY)";
            }
            if($max_age > 0)
            {
                $sql_old .= " AND p.post_date > DATE_SUB(NOW(), INTERVAL {$max_age} DAY)";
            }
            if(is_array($excluded_categories_old) && !empty($excluded_categories_old))
            {
                $excluded_categories_string = "";
                foreach($excluded_categories_old as $excluded_category_old)
                {
                    if(intval($excluded_category_old))
                    {
                        $excluded_categories_string .= $excluded_category_old . ",";
                    }
                }
                $excluded_categories_string = rtrim($excluded_categories_string, ",");

                if($excluded_categories_string)
                {
                    $sql_old .= " AND p.ID NOT IN";
                    $sql_old .= " (SELECT termr.object_id FROM {$table_term_taxonomy} AS termt INNER JOIN {$table_term_relationships} AS termr";
                    $sql_old .= " ON termt.term_taxonomy_id=termr.term_taxonomy_id";
                    $sql_old .= " WHERE termt.term_id IN ({$excluded_categories_string}) AND termt.taxonomy='category')";
                }
            }
            if(MicroblogPoster_Poster::is_method_callable('MicroblogPoster_Poster_Ultimate','resolve_sql_allowed_authors'))
            {
                $sql_old .= MicroblogPoster_Poster_Ultimate::resolve_sql_allowed_authors();
            }

            $sql_old .= " AND p.ID NOT IN (SELECT item_id from {$table_old_items} WHERE item_type='{$custom_type}')";
            $sql_old .= " ORDER BY p.post_date ASC";
            $sql_old .= " LIMIT 10";

            $old_posts = $wpdb->get_results($sql_old, ARRAY_A);

            if(is_array($old_posts) && !empty($old_posts))
            {
                for($i = 0; $i < $nb_posts; $i++)
                {
                    if(isset($old_posts[$i]))
                    {
                        $post_id = $old_posts[$i]['ID'];
                        $sql="INSERT INTO {$table_old_items} (item_id,item_type) 
                               VALUES ('{$post_id}','{$custom_type}')";
                        $wpdb->query($sql);

                        MicroblogPoster_Poster::update_old_post($post_id);
                    }
                }
            }
        }
    }
    
    /**
    * Shows the MicroblogPoster's control dashboard
    *
    * @return string (html)
    */
    public static function show_control_dashboard()
    {
        if(!MicroblogPoster_Poster_Pro::is_license_key_verified())
        {
            echo "<br /> <span style='color:red;'>".__( 'Error: The MicroblogPoster\'s Pro/Enterprise Add-on License Key isn\'t valid.', 'microblog-poster' )."</span>";
            return;
        }
        
        ?>
        <br />
        <style>
            .mbp_social-network-accounts-site
            {
                margin-top: 10px;
                margin-left: 20px;
                width: 100%;
            }
            .mbp_social-network-accounts-site h4
            {
                background-color: #EBEBEB;
                margin: 0px 0px;
                padding: 3px 5px;
                border-radius: 5px;
                display: inline-block;
                vertical-align: top;
                font-size: 14px;
                width: 90%;
            }
            .mbp_social-network-accounts-site a
            {
                font-size: 10px;
            }
            .mbp_social-network-accounts-site div
            {
                margin-left: 250px;
            }
            .mbp_social-network-accounts-accounts
            {
                margin-left: 45px;
            }
            .mbp_social_account_microblogposter_msgc
            {
                width: 290px;
                /*resize: none;*/
            }
        </style>

        <script>
            function mbp_social_accounts_microblogposter_uncheck_all(type)
            {
                if(!jQuery('#microblogposteroff').is(':checked'))
                {
                    jQuery('.mbp_social_account_microblogposter_'+type).removeAttr('checked');
                }
            }
            function mbp_social_accounts_microblogposter_check_all(type)
            {
                if(!jQuery('#microblogposteroff').is(':checked'))
                {
                    jQuery('.mbp_social_account_microblogposter_'+type).attr('checked','checked');
                }
            }
            
            jQuery(document).ready(function($) {
                
                if($('#microblogposteroff').is(':checked'))
                {
                    $('.mbp_social_account_microblogposter_msgc').attr('disabled','disabled');
                    $('.mbp_social_account_microblogposter_boxc').attr('disabled','disabled');
                    $('#mbp_microblogposter_category_to_account').attr('disabled','disabled');
                }
                
                $('#microblogposteroff').on("click", function(){

                    if($(this).is(':checked'))
                    {
                        $('.mbp_social_account_microblogposter_msgc').attr('disabled','disabled');
                        $('.mbp_social_account_microblogposter_boxc').attr('disabled','disabled');
                        $('#mbp_microblogposter_category_to_account').attr('disabled','disabled');
                        $('#mbp_microblogposter_category_to_account').removeAttr('checked');
                    }
                    else
                    {
                        $('.mbp_social_account_microblogposter_msgc').removeAttr('disabled');
                        $('.mbp_social_account_microblogposter_boxc').removeAttr('disabled');
                        <?php if(MicroblogPoster_Poster::is_method_callable('MicroblogPoster_Poster_Enterprise','filter_single_account_cdriven')):?>
                        $('#mbp_microblogposter_category_to_account').removeAttr('disabled');
                        <?php endif;?>
                    }

                });
                
                <?php if(!MicroblogPoster_Poster::is_method_callable('MicroblogPoster_Poster_Enterprise','filter_single_account_cdriven')):?>
                        $('#mbp_microblogposter_category_to_account').attr('disabled','disabled');
                <?php endif;?>
                    
                <?php if(MicroblogPoster_Poster::is_method_callable('MicroblogPoster_Poster_Enterprise','filter_single_account_cdriven')):?>
                        if($('#mbp_microblogposter_category_to_account').is(':checked'))
                        {
                            $('.mbp_social_account_microblogposter_boxc').attr('disabled','disabled');
                        }

                        $('#mbp_microblogposter_category_to_account').on("click", function(){

                            if($(this).is(':checked'))
                            {
                                $('.mbp_social_account_microblogposter_boxc').attr('disabled','disabled');
                            }
                            else
                            {
                                $('.mbp_social_account_microblogposter_boxc').removeAttr('disabled');
                            }

                        });
                <?php endif;?> 
            });
            
            
        </script>

        <input type="hidden" name="mbp_control_dashboard_microblogposter" value="1" /> 
        <?php 
            $twitter_accounts = MicroblogPoster_Poster::get_accounts_object('twitter');
            if(!empty($twitter_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('twitter');
                foreach($twitter_accounts as $twitter_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($twitter_account, 'twitter');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>

        
        <?php 
            $plurk_accounts = MicroblogPoster_Poster::get_accounts_object('plurk');
            if(!empty($plurk_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('plurk');
                foreach($plurk_accounts as $plurk_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($plurk_account, 'plurk');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $friendfeed_accounts = MicroblogPoster_Poster::get_accounts_object('friendfeed');
            if(!empty($friendfeed_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('friendfeed'); 
                foreach($friendfeed_accounts as $friendfeed_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($friendfeed_account, 'friendfeed');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $delicious_accounts = MicroblogPoster_Poster::get_accounts_object('delicious');
            if(!empty($delicious_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('delicious'); 
                foreach($delicious_accounts as $delicious_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($delicious_account, 'delicious');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $facebook_accounts = MicroblogPoster_Poster::get_accounts_object('facebook');
            if(!empty($facebook_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('facebook'); 
                foreach($facebook_accounts as $facebook_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($facebook_account, 'facebook');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $diigo_accounts = MicroblogPoster_Poster::get_accounts_object('diigo');
            if(!empty($diigo_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('diigo'); 
                foreach($diigo_accounts as $diigo_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($diigo_account, 'diigo');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $linkedin_accounts = MicroblogPoster_Poster::get_accounts_object('linkedin');
            if(!empty($linkedin_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('linkedin'); 
                foreach($linkedin_accounts as $linkedin_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($linkedin_account, 'linkedin');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $tumblr_accounts = MicroblogPoster_Poster::get_accounts_object('tumblr');
            if(!empty($tumblr_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('tumblr'); 
                foreach($tumblr_accounts as $tumblr_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($tumblr_account, 'tumblr');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $blogger_accounts = MicroblogPoster_Poster::get_accounts_object('blogger');
            if(!empty($blogger_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('blogger'); 
                foreach($blogger_accounts as $blogger_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($blogger_account, 'blogger');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $instapaper_accounts = MicroblogPoster_Poster::get_accounts_object('instapaper');
            if(!empty($instapaper_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('instapaper'); 
                foreach($instapaper_accounts as $instapaper_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($instapaper_account, 'instapaper');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $vkontakte_accounts = MicroblogPoster_Poster::get_accounts_object('vkontakte');
            if(!empty($vkontakte_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('vkontakte'); 
                foreach($vkontakte_accounts as $vkontakte_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($vkontakte_account, 'vkontakte');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $xing_accounts = MicroblogPoster_Poster::get_accounts_object('xing');
            if(!empty($xing_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('xing'); 
                foreach($xing_accounts as $xing_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($xing_account, 'xing');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $pinterest_accounts = MicroblogPoster_Poster::get_accounts_object('pinterest');
            if(!empty($pinterest_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('pinterest'); 
                foreach($pinterest_accounts as $pinterest_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($pinterest_account, 'pinterest');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $flickr_accounts = MicroblogPoster_Poster::get_accounts_object('flickr');
            if(!empty($flickr_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('flickr'); 
                foreach($flickr_accounts as $flickr_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($flickr_account, 'flickr');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $wordpress_accounts = MicroblogPoster_Poster::get_accounts_object('wordpress');
            if(!empty($wordpress_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('wordpress'); 
                foreach($wordpress_accounts as $wordpress_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($wordpress_account, 'wordpress');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        <?php 
            $googleplus_accounts = MicroblogPoster_Poster::get_accounts_object('googleplus');
            if(!empty($googleplus_accounts)):
                MicroblogPoster_Poster_Pro::show_common_account_dashboard_head('googleplus'); 
                foreach($googleplus_accounts as $googleplus_account):
                    MicroblogPoster_Poster_Pro::show_common_account_dashboard($googleplus_account, 'googleplus');
        ?>
                
        <?php
                endforeach;
            endif;
        ?>
        
        
        <?php
    }
    
    /**
    * 
    * @param string $class_name
    * @param string $method_name
    * @return  bool
    */
    public static function is_method_callable($class_name, $method_name)
    {
        if( class_exists($class_name, false) && method_exists($class_name, $method_name) )
        {
            return true;
        }
        
        return false;
    }
    
    /**
    * Shows the MicroblogPoster's control dashboard
    *
    * @return string (html)
    */
    private static function show_common_account_dashboard($account, $site)
    {
        ?>
        <div class="mbp_social-network-accounts-accounts">
            <input type="checkbox" class="mbp_social_account_microblogposter_boxc mbp_social_account_microblogposter_<?php echo $site;?>" id="mbp_social_account_microblogposter_<?php echo $account->account_id;?>" name="mbp_social_account_microblogposter_<?php echo $account->account_id;?>" value="1" checked="checked" /> 
            <label for="mbp_social_account_microblogposter_<?php echo $account->account_id;?>"><?php echo $account->username;?></label>
            <br />
            <label for="mbp_social_account_microblogposter_msg_<?php echo $account->account_id;?>"><?php _e('Message Format:', 'microblog-poster');?></label>
            <textarea class="mbp_social_account_microblogposter_msgc" id="mbp_social_account_microblogposter_msg_<?php echo $account->account_id;?>" name="mbp_social_account_microblogposter_msg_<?php echo $account->account_id;?>" rows="2"><?php echo $account->message_format;?></textarea>
        </div>
        <?php
    }
    
    /**
    * Shows the MicroblogPoster's control dashboard
    *
    * @return string (html)
    */
    private static function show_common_account_dashboard_head($site)
    {
        ?>
        <div class="mbp_social-network-accounts-site">
            <img src="<?php echo plugins_url().'/microblog-poster/images/' . $site . '_icon.png';?>" />
            <?php
                $site_label = $site;
                if($site == 'vkontakte'){$site_label = 'vKontakte';}
                if($site == 'googleplus'){$site_label = 'google+';}
            ?>
            <?php if( in_array(get_locale(), array('fr_FR', 'pt_PT', 'pt_BR', 'es_ES', 'es_MX', 'es_PE', 'it_IT', 'ru_RU', 'uk', 'pl_PL')) ):?>
                <h4><?php _e('Accounts', 'microblog-poster');?> <?php echo ucfirst($site_label);?></h4>
            <?php else:?>
                <h4><?php echo ucfirst($site_label);?> <?php _e('Accounts', 'microblog-poster');?></h4>
            <?php endif;?>
            <div>
                <a href="#" onclick="mbp_social_accounts_microblogposter_uncheck_all('<?php echo $site;?>');return false;" ><?php _e('Uncheck All', 'microblog-poster');?></a> <a href="#" onclick="mbp_social_accounts_microblogposter_check_all('<?php echo $site;?>');return false;" ><?php _e('Check All', 'microblog-poster');?></a>
            </div>
        </div>
        <?php
    }
    
    /**
    * Get accounts from db
    *
    * @param   string  $type Type of account (=site)
    * @return  array
    */
    private static function get_accounts($type)
    {
        global  $wpdb;

        $table_accounts = $wpdb->prefix . 'microblogposter_accounts';
        
        $sql="SELECT * FROM $table_accounts WHERE type='{$type}'";
        $rows = $wpdb->get_results($sql);
        
        return $rows;
    }
    
    /**
    * Checks if the license key is verified
    *
    * @return boolean
    */
    public static function is_license_key_verified()
    {
        $customer_license_key_name = "microblogposterpro_plg_customer_license_key";
        $customer_license_key_value = get_option($customer_license_key_name, "");
        $customer_license_key_value = json_decode($customer_license_key_value, true);
        
        if(is_array($customer_license_key_value))
        {
            return (bool) $customer_license_key_value['verified'];
        }
        
        return false;
    }
    
}

require_once "microblogposterpro_options.php";

?>
