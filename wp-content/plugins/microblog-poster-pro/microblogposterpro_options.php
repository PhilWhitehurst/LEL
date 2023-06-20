<?php

class MicroblogPoster_Poster_Pro_Options
{
    
    /**
    * Gets the page access token from facebook API
    *
    * @param object $curl
    * @param int $user_id
    * @param string $access_token
    * @param int $page_id
    * @param string $app_access_token  
    * @return array
    */
    public static function get_facebook_page_access_token($curl, $user_id, $access_token, $page_id, $app_access_token)
    {
        $result = array();
        
        $page_access_url = "https://graph.facebook.com/{$user_id}/accounts/?access_token={$access_token}";
        
        $response = $curl->fetch_url($page_access_url);
        $params3 = json_decode($response, true);
        if(is_array($params3['data']))
        {
            foreach($params3['data'] as $params3_acc)
            {
                if($params3_acc['id']==$page_id)
                {
                    $result['access_token'] = $params3_acc['access_token'];
                    $debug_url = "https://graph.facebook.com/debug_token?input_token={$params3_acc['access_token']}&access_token={$app_access_token}";

                    $response = $curl->fetch_url($debug_url);
                    $params4 = json_decode($response, true);
                    if(isset($params4['data']['expires_at']))
                    {
                        $result['expires'] = $params4['data']['expires_at'];
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
    * Gets the group access token from facebook API
    *
    * @param object $curl
    * @param int $user_id
    * @param string $access_token
    * @param string $app_access_token  
    * @return array
    */
    public static function get_facebook_group_access_token($curl, $user_id, $access_token, $app_access_token)
    {
        $result = array();
        $result['access_token'] = $access_token;
        
        $page_access_url = "https://graph.facebook.com/{$user_id}/accounts/?access_token={$access_token}";
                    
        $response = $curl->fetch_url($page_access_url);

        $debug_url = "https://graph.facebook.com/debug_token?input_token={$access_token}&access_token={$app_access_token}";

        $response = $curl->fetch_url($debug_url);
        $params4 = json_decode($response, true);
        if(isset($params4['data']['expires_at']))
        {
            $result['expires'] = $params4['data']['expires_at'];
        }
        
        return $result;
    }
    
    /**
    * Verify the license key
    *
    * @param object $curl
    * @param string $license_key 
    * @return boolean
    */
    public static function verify_license_key($curl, $license_key)
    {
        $page_verify_url = "http://efficientscripts.com/api/microblogposterpro_verify_license_key.php/?key={$license_key}";
                    
        $response = $curl->fetch_url($page_verify_url);
        if(trim($response) == 'valid')
        {
            return true;
        }
            
        return false;
    }
    
}
?>
