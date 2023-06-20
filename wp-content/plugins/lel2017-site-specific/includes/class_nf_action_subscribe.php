<?php

if (!defined('ABSPATH'))
    exit;

/**
 * Class for our custom action type to subscribe to mailing lists.
 *
 * @package     lel-2017
 * @subpackage  lel-2017/includes
 * @copyright   Copyright (c) 2015 , Phil Whitehurst
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
// I know this says NF_Notification_Base_Type, but the name will eventually be changed to reflect the action nomenclature.
class LEL2017_Action_Volunteer extends NF_Notification_Base_Type {

    /**
     * Get things rolling
     */
    function __construct() {
        $this->name = __('Subscribe');
    }

    /**
     * Output our edit screen
     *
     * @access public
     * @since 0.1
     * @return void
     */
    public function edit_screen($id = '') {
        //$form_id = ( '' != $id ) ? Ninja_Forms()->notification($id)->form_id : '';

        $mailing_list = Ninja_Forms()->notification($id)->get_setting('mailing_list');
        /*
         * Retrieve a list of available mailing lists from the
         * English London Edinburgh London blog
         */
        switch_to_blog(1);
        if ($mailinglists === wpml_get_mailinglists(array('privatelist' => "N"))) {
            $output = '<tr>';
            $output .= '<th scope="row"><label for="settings-mailing_list">' . __('Mailing List') . '</label></th>';
            $output .= '<td>';
            $output .= '<select name="settings[mailing_list]" id="settings-mailing_list">';


            foreach ($mailinglists as $mailinglist) {

                $selected = ($mailinglist->id === $mailing_list ? "selected" : "");
                $output .= '<option value="' . $mailinglist->id . '"' . ' ' . $selected . '>' . $mailinglist->title . '</option>';
            }
            $output .= '</select>';
            $output .= '</td>';
            $output .= '</tr>';

            echo $output;
        }
        restore_current_blog();

        /*
          By default, settings are output into a table. We need to wrap our settings in <tr> and <td> tags.
          This lets all of our settings within the action page to be similar.

          The most important thing to keep in mind is the naming convention for your settings: settings[setting_name]
          This will allow Ninja Forms to save the setting for you.
         */
        ?>

        <?php

    }

    /**
     * Process our Redirect notification
     *
     * @access public
     * @since 2.8
     * @return void
     */
    public function process($id) {
        /*
          We declare our $ninja_forms_processing global so that we can access submitted values.
         */
        global $ninja_forms_processing;

        /*
          Get the mailing list id
         */
        $mailing_list = Ninja_Forms()->notification($id)->get_setting('mailing_list');

        /*
         * Find the first, name, last name, and email field ids on the form
         */


        $form_id = $ninja_forms_processing->get_form_ID();
        $all_fields = ninja_forms_get_fields_by_form_id($form_id);

        foreach ($all_fields as $field) {

            if ($field['data']['first_name'] === '1') {
                $fn_id = $field['id'];
            }
            if ($field['data']['last_name'] === '1') {
                $ln_id = $field['id'];
            }
            if ($field['data']['user_email'] === '1') {
                $em_id = $field['id'];
            }
        }

        $first_name = $ninja_forms_processing->get_field_value($fn_id);
        $last_name = $ninja_forms_processing->get_field_value($ln_id);
        $email = $ninja_forms_processing->get_field_value($em_id);

        if (class_exists('wpMail')) {
            $wpMail = new wpMail();
            global $Subscriber;
            $data = array('email' => $email, 'firstname' => $first_name, 'lastname' => $last_name, 'list_id' => array($mailing_list));

            if ($Subscriber->optin($data, false, false)) {
                $ninja_forms_processing->add_success_msg('lel2017_subscribe', '<p class="alert alert-success">Thank you, your sign-up request was successful! Please check your email inbox to confirm.</p>');
            } else {

                $ninja_forms_processing->add_error('lel2017_subscribe_error', '<p class="alert alert-danger">There was an error, please try again later. If this persists contact us via the contact page to let us know.</p>', 'general');
            }
        }
    }

}

return new LEL2017_Action_Volunteer();

