<?php

class MicroblogPosterPro_Auto_Update
{
    /**
     * The plugin current version
     * @var string
     */
    public $current_version;

    /**
     * The plugin remote update path
     * @var string
     */
    public $update_path;

    /**
     * Plugin Slug (plugin_directory/plugin_file.php)
     * @var string
     */
    public $plugin_slug;

    /**
     * Plugin name (plugin_file)
     * @var string
     */
    public $slug;
    
    /**
     * Customer License Key
     * @var string
     */
    public $license_key;

    /**
     * Initialize a new instance of the WordPress Auto-Update class
     * @param string $current_version
     * @param string $update_path
     * @param string $plugin_slug
     * @param string $customer_license_key
     */
    function __construct($current_version, $update_path, $plugin_slug, $customer_license_key)
    {
        // Set the class public variables
        $this->current_version = $current_version;
        $this->update_path = $update_path;
        $this->plugin_slug = $plugin_slug;
        $this->license_key = $customer_license_key;
        
        list ($t1, $t2) = explode('/', $plugin_slug);
        $this->slug = str_replace('.php', '', $t2);

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array($this, 'check_info'), 10, 3);
    }

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     * @return object $ transient
     */
    public function check_update($transient)
    {
        
        /*
        if (empty($transient->checked)) {
            return $transient;
        }*/

        // Get the remote version
        $remote_version = $this->getRemote_version();

        // If a newer version is available, add the update
        if (version_compare($this->current_version, $remote_version, '<')) 
        {
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $remote_version;
            $obj->url = "http://efficientscripts.com/microblogposterpro";
            $obj->package = $this->update_path.'/?license_key='.$this->license_key;
            $transient->response[$this->plugin_slug] = $obj;
        }
        //var_dump($transient);
        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $false
     * @param array $action
     * @param object $args
     * @return bool|object
     */
    public function check_info($false, $action, $args)
    {
        if ($args->slug === $this->slug) 
        {
            $information = $this->getRemote_information();
            return $information;
        }
        
        return false;
    }

    /**
     * Return the remote version
     * @return string $remote_version
     */
    public function getRemote_version()
    {
        $curl = new MicroblogPosterPro_Curl();
        $url = $this->update_path;
        $post_args = array(
            'action' => 'version'
        );

        $result = $curl->send_post_data($url, $post_args, null, 10);
        if(preg_match('|[0-9]\.[0-9]|i', $result))
        {
            return $result;
        }
        
        return false;
    }

    /**
     * Get information about the remote version
     * @return bool|object
     */
    public function getRemote_information()
    {
        $curl = new MicroblogPosterPro_Curl();
        $url = $this->update_path;
        $post_args = array(
            'action' => 'info'
        );

        $result = $curl->send_post_data($url, $post_args, null, 10);
        $result = unserialize($result);
        $result->download_link = $result->download_link . '/?license_key='.$this->license_key;
        
        if(is_object($result))
        {
            return $result;
        }
        
        return false;
    }

}