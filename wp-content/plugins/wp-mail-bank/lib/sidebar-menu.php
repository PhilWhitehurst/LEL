<?php
/**
 * This file is used for creating sidebar menu.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/lib
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit; // Exit if accessed directly
}
if (!is_user_logged_in()) {
   return;
} else {
   $access_granted = false;
   foreach ($user_role_permission as $permission) {
      if (current_user_can($permission)) {
         $access_granted = true;
         break;
      }
   }
   if (!$access_granted) {
      return;
   } else {
      $flag = 0;

      $role_capabilities = $wpdb->get_var
          (
          $wpdb->prepare
              (
              "SELECT meta_value from " . mail_bank_meta() . "
				WHERE meta_key = %s", "roles_and_capabilities"
          )
      );

      $roles_and_capabilities_unserialized_data = maybe_unserialize($role_capabilities);
      $capabilities = explode(",", isset($roles_and_capabilities_unserialized_data["roles_and_capabilities"]) ? esc_attr($roles_and_capabilities_unserialized_data["roles_and_capabilities"]) : "");

      if (is_super_admin()) {
         $mb_role = "administrator";
      } else {
         $mb_role = check_user_roles_mail_bank();
      }
      switch ($mb_role) {
         case "administrator":
            $privileges = "administrator_privileges";
            $flag = $capabilities[0];
            break;

         case "author":
            $privileges = "author_privileges";
            $flag = $capabilities[1];
            break;

         case "editor":
            $privileges = "editor_privileges";
            $flag = $capabilities[2];
            break;

         case "contributor":
            $privileges = "contributor_privileges";
            $flag = $capabilities[3];
            break;

         case "subscriber":
            $privileges = "subscriber_privileges";
            $flag = $capabilities[4];
            break;

         default:
            $privileges = "other_roles_privileges";
            $flag = $capabilities[5];
            break;
      }

      foreach ($roles_and_capabilities_unserialized_data as $key => $value) {
         if ($privileges == $key) {
            $privileges_value = $value;
            break;
         }
      }

      $full_control = explode(",", $privileges_value);
      if (!defined("full_control"))
         define("full_control", "$full_control[0]");
      if (!defined("email_configuration_mail_bank"))
         define("email_configuration_mail_bank", "$full_control[1]");
      if (!defined("test_email_mail_bank"))
         define("test_email_mail_bank", "$full_control[2]");
      if (!defined("conectivity_test_email_mail_bank"))
         define("conectivity_test_email_mail_bank", "$full_control[3]");
      if (!defined("email_logs_mail_bank"))
         define("email_logs_mail_bank", "$full_control[4]");
      if (!defined("settings_mail_bank"))
         define("settings_mail_bank", "$full_control[5]");
      if (!defined("roles_and_capabilities_mail_bank"))
         define("roles_and_capabilities_mail_bank", "$full_control[6]");
      if (!defined("system_information_mail_bank"))
         define("system_information_mail_bank", "$full_control[7]");
      $check_wp_mail_bank_wizard = get_option("wp-mail-bank-wizard-set-up");
      if ($flag == "1") {
         global $wp_version;

         $icon = plugins_url("assets/global/img/icon.png", dirname(__FILE__));
         if ($check_wp_mail_bank_wizard) {
            add_menu_page($wp_mail_bank, $wp_mail_bank, "read", "mb_email_configuration", "", $icon);
         } else {
            add_menu_page($wp_mail_bank, $wp_mail_bank, "read", "wp_mail_bank_wizard", "", plugins_url("assets/global/img/icon.png", dirname(__FILE__)));
            add_submenu_page($wp_mail_bank, $wp_mail_bank, "", "read", "wp_mail_bank_wizard", "wp_mail_bank_wizard");
         }

         add_submenu_page("mb_email_configuration", $mb_email_configuration, $mb_email_configuration, "read", "mb_email_configuration", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_email_configuration");
         add_submenu_page("mb_email_configuration", $mb_test_email, $mb_test_email, "read", "mb_test_email", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_test_email");
         add_submenu_page("mb_email_configuration", $mb_connectivity_test, $mb_connectivity_test, "read", "mb_connectivity_test", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_connectivity_test");
         add_submenu_page("mb_email_configuration", $mb_email_logs, $mb_email_logs, "read", "mb_email_logs", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_email_logs");
         add_submenu_page("mb_email_configuration", $mb_settings, $mb_settings, "read", "mb_settings", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_settings");
         add_submenu_page("mb_email_configuration", $mb_roles_and_capabilities, $mb_roles_and_capabilities, "read", "mb_roles_and_capabilities", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_roles_and_capabilities");
         add_submenu_page("mb_email_configuration", $mb_feedbacks, $mb_feedbacks, "read", "mb_feedbacks", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_feedbacks");
         add_submenu_page("mb_email_configuration", $mb_system_information, $mb_system_information, "read", "mb_system_information", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_system_information");
         add_submenu_page("mb_email_configuration", $mb_upgrade, $mb_upgrade, "read", "mb_upgrade", $check_wp_mail_bank_wizard == "" ? "wp_mail_bank_wizard" : "mb_upgrade");
      }


      /*
        Function Name: wp_mail_bank_wizard
        Parameters: No
        Description: This function is used for creating wp_mail_bank_wizard.
        Created On: 21-04-2017 10:17
        Created By: Tech Banker Team
       */
      function wp_mail_bank_wizard() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/wizard/wizard.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/wizard/wizard.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_email_configuration
        Parameters: No
        Description: This function is used to create mb_email_configuration menu.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      function mb_email_configuration() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/email-setup/email-setup.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/email-setup/email-setup.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_test_email
        Parameters: No
        Description: This function is used to create mb_test_email menu.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      function mb_test_email() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/test-email/test-email.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/test-email/test-email.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_connectivity_test
        Parameters: No
        Description: This function is used to create mb_connectivity_test menu.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      function mb_connectivity_test() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/connectivity-test/connectivity-test.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/connectivity-test/connectivity-test.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_email_logs
        Parameters: No
        Description: This function is used to create mb_email_logs menu.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      function mb_email_logs() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/email-logs/email-logs.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/email-logs/email-logs.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_settings
        Parameters: No
        Description: This function is used to create mb_settings menu.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      function mb_settings() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/settings/settings.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/settings/settings.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_roles_and_capabilities
        Parameters: No
        Description: This function is used to create mb_roles_and_capabilities menu.
        Created On: 15-06-2016 12:59
        Created By: Tech Banker Team
       */
      function mb_roles_and_capabilities() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/roles-and-capabilities/roles-and-capabilities.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/roles-and-capabilities/roles-and-capabilities.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_feedbacks
        Parameters: No
        Description: This function is used to create mb_feedbacks menu.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      function mb_feedbacks() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/feedbacks/feedbacks.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/feedbacks/feedbacks.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_system_information
        Parameters: No
        Description: This function is used to create mb_system_information menu.
        Created On: 15-06-2016 02:29
        Created By: Tech Banker Team
       */
      function mb_system_information() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/system-information/system-information.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/system-information/system-information.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
      /*
        Function Name: mb_upgrade
        Parameter: No
        Description: This Function is used for upgrade.
        Created On: 13-08-2016 09:41
        Created By: Tech Banker Team
       */
      function mb_upgrade() {
         global $wpdb;
         $user_role_permission = get_users_capabilities_mail_bank();
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/translations.php")) {
            include MAIL_BANK_DIR_PATH . "includes/translations.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/header.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/header.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/sidebar.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/sidebar.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/queries.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/queries.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "views/premium-editions/premium-editions.php")) {
            include_once MAIL_BANK_DIR_PATH . "views/premium-editions/premium-editions.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/footer.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/footer.php";
         }
      }
   }
}