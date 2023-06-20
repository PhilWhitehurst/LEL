<?php
/**
 * This file is used for translation strings.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/includes
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
}// Exit if accessed directly
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
      $wp_langs = array();
      $wp_langs["af"] = "Afrikaans";
      $wp_langs["ak"] = "Akan";
      $wp_langs["sq"] = "Shqip";
      $wp_langs["arq"] = "الدارجة الجزايرية";
      $wp_langs["am"] = "አማርኛ";
      $wp_langs["hy"] = "Հայերեն";
      $wp_langs["rup_mk"] = "Armãneashce";
      $wp_langs["frp"] = "Arpitan";
      $wp_langs["as"] = "অসমীয়া";
      $wp_langs["ast"] = "Asturianu";
      $wp_langs["az"] = "Azərbaycan dili";
      $wp_langs["az_tr"] = "Azərbaycan Türkcəsi";
      $wp_langs["bcc"] = "بلوچی مکرانی";
      $wp_langs["ba"] = "башҡорт теле";
      $wp_langs["eu"] = "Euskara";
      $wp_langs["bel"] = "Беларуская мова";
      $wp_langs["bn_bd"] = "বাংলা";
      $wp_langs["bs_ba"] = "Bosanski";
      $wp_langs["bre"] = "Brezhoneg";
      $wp_langs["bg_bg"] = "Български";
      $wp_langs["ca"] = "Català";
      $wp_langs["bal"] = "Català (Balear)";
      $wp_langs["ceb"] = "Cebuano";
      $wp_langs["zh_hk"] = "香港中文版 ";
      $wp_langs["zh_tw"] = "繁體中文";
      $wp_langs["co"] = "Corsu";
      $wp_langs["hr"] = "Hrvatski";
      $wp_langs["dv"] = "ދިވެހި";
      $wp_langs["nl_be"] = "Nederlands (België)";
      $wp_langs["dzo"] = "རྫོང་ཁ";
      $wp_langs["eo"] = "Esperanto";
      $wp_langs["et"] = "Eesti";
      $wp_langs["fo"] = "Føroyskt";
      $wp_langs["fi"] = "Suomi";
      $wp_langs["fr_be"] = "Français de Belgique";
      $wp_langs["fr_ca"] = "Français du Canada";
      $wp_langs["fy"] = "Frysk";
      $wp_langs["fur"] = "Friulian";
      $wp_langs["fuc"] = "Pulaar";
      $wp_langs["gl_es"] = "Galego";
      $wp_langs["ka_ge"] = "ქართული";
      $wp_langs["de_ch"] = "Deutsch (Schweiz)";
      $wp_langs["kal"] = "Kalaallisut";
      $wp_langs["gn"] = "Avañe'ẽ";
      $wp_langs["gu"] = "ગુજરાતી";
      $wp_langs["hat"] = "Kreyol ayisyen";
      $wp_langs["haw_us"] = "Ōlelo Hawaiʻi";
      $wp_langs["haz"] = "هزاره گی";
      $wp_langs["hi_in"] = "हिन्दी";
      $wp_langs["is_is"] = "Íslenska";
      $wp_langs["ido"] = "Ido";
      $wp_langs["id_id"] = "Bahasa Indonesia";
      $wp_langs["ga"] = "Gaelige";
      $wp_langs["it_it"] = "Italiano";
      $wp_langs["ja"] = "日本語";
      $wp_langs["jv_id"] = "Basa Jawa";
      $wp_langs["kab"] = "Taqbaylit";
      $wp_langs["kn"] = "ಕನ್ನಡ";
      $wp_langs["kk"] = "Қазақ тілі";
      $wp_langs["km"] = "ភាសាខ្មែរ";
      $wp_langs["kin"] = "Ikinyarwanda";
      $wp_langs["ky_ky"] = "кыргыз тили";
      $wp_langs["ko_kr"] = "한국어";
      $wp_langs["ckb"] = "كوردی‎";
      $wp_langs["lo"] = "ພາສາລາວ";
      $wp_langs["lv"] = "Latviešu valoda";
      $wp_langs["li"] = "Limburgs";
      $wp_langs["lin"] = "Ngala";
      $wp_langs["lt_lt"] = "Lietuvių kalba";
      $wp_langs["lb_lu"] = "Lëtzebuergesch";
      $wp_langs["mk_mk"] = "Македонски јазик";
      $wp_langs["mg_mg"] = "Malagasy";
      $wp_langs["ms_my"] = "Bahasa Melayu";
      $wp_langs["ml_in"] = "മലയാളം";
      $wp_langs["mri"] = "Te Reo Māori";
      $wp_langs["mr"] = "मराठी";
      $wp_langs["xmf"] = "მარგალური ნინა";
      $wp_langs["mn"] = "Монгол";
      $wp_langs["me_me"] = "Crnogorski jezik";
      $wp_langs["ary"] = "العربية المغربية";
      $wp_langs["my_mm"] = "ဗမာစာ";
      $wp_langs["ne_np"] = "नेपाली";
      $wp_langs["nn_no"] = "Norsk nynorsk";
      $wp_langs["oci"] = "Occitan";
      $wp_langs["ory"] = "ଓଡ଼ିଆ";
      $wp_langs["os"] = "Ирон";
      $wp_langs["ps"] = "پښتو";
      $wp_langs["fa_ir"] = "فارسی";
      $wp_langs["fa_af"] = "(فارسی (افغانستان";
      $wp_langs["pl_pl"] = "Polski";
      $wp_langs["pa_in"] = "ਪੰਜਾਬੀ";
      $wp_langs["rhg"] = "Ruáinga";
      $wp_langs["roh"] = "Rumantsch Vallader";
      $wp_langs["rue"] = "Русиньскый";
      $wp_langs["sah"] = "Сахалыы";
      $wp_langs["sa_in"] = "भारतम्";
      $wp_langs["srd"] = "Sardu";
      $wp_langs["gd"] = "Gàidhlig";
      $wp_langs["sr_rs"] = "Српски језик";
      $wp_langs["szl"] = "Ślōnskŏ gŏdka";
      $wp_langs["snd"] = "سنڌي";
      $wp_langs["si_lk"] = "සිංහල";
      $wp_langs["sk_sk"] = "Slovenčina";
      $wp_langs["sl_si"] = "Slovenščina";
      $wp_langs["so_so"] = "Afsoomaali";
      $wp_langs["azb"] = "گؤنئی آذربایجان";
      $wp_langs["es_ar"] = "Español de Argentina";
      $wp_langs["es_cl"] = "Español de Chile";
      $wp_langs["es_co"] = "Español de Colombia";
      $wp_langs["es_gt"] = "Español de Guatemala";
      $wp_langs["es_pe"] = "Español de Perú";
      $wp_langs["es_pr"] = "Español de Puerto Rico";
      $wp_langs["es_es"] = "Español";
      $wp_langs["es_ve"] = "Español de Venezuela";
      $wp_langs["su_id"] = "Basa Sunda";
      $wp_langs["sw"] = "Kiswahili";
      $wp_langs["gsw"] = "Schwyzerdütsch";
      $wp_langs["tl"] = "Tagalog";
      $wp_langs["tah"] = "Reo Tahiti";
      $wp_langs["tg"] = "Тоҷикӣ";
      $wp_langs["tzm"] = "ⵜⴰⵎⴰⵣⵉⵖⵜ";
      $wp_langs["ta_in"] = "தமிழ்";
      $wp_langs["ta_lk"] = "தமிழ்";
      $wp_langs["tt_ru"] = "Татар теле";
      $wp_langs["te"] = "తెలుగు";
      $wp_langs["bo"] = "བོད་སྐད";
      $wp_langs["tir"] = "ትግርኛ";
      $wp_langs["tuk"] = "Türkmençe";
      $wp_langs["twd"] = "Twents";
      $wp_langs["ug_cn"] = "Uyƣurqə";
      $wp_langs["ur"] = "اردو";
      $wp_langs["uz_uz"] = "O‘zbekcha";
      $wp_langs["wa"] = "Walon";
      $wp_langs["cy"] = "Cymraeg";
      $wp_langs["yor"] = "Yorùbá";
      $locale = strtolower(get_locale());
      if (array_key_exists("$locale", $wp_langs)) {
         $language = $wp_langs["$locale"];
         $mb_message_translate_help = __("If you would like to translate in $language & help the wordpress community, please fill in the form ", "wp-mail-bank");
         $mb_message_translate_here = __("here.", "wp-mail-bank");
      } else {
         $mb_message_translate_help = "";
         $mb_kindly_click = "";
         $mb_message_translate_here = "";
      }

      //Disclaimer
      $mb_upgrade = __("Upgrade", "wp-mail-bank");
      $mb_premium_edition_label = __("Premium Edition", "wp-mail-bank");
      $mb_message_premium_edition = __("This feature is available only in Premium Editions! <br> Kindly Purchase to unlock it!", "wp-mail-bank");


      //wizard
      $mb_wizard_basic_info = __("Basic Info", "wp-mail-bank");
      $mb_wizard_account_setup = __("Account Setup", "wp-mail-bank");
      $mb_wizard_confirm = __("Confirm", "wp-mail-bank");

      // Menus
      $wp_mail_bank = __("Mail Bank", "wp-mail-bank");
      $mb_email_configuration = __("Email Setup", "wp-mail-bank");
      $mb_email_logs = __("Email Logs", "wp-mail-bank");
      $mb_test_email = __("Test Email", "wp-mail-bank");
      $mb_settings = __("Plugin Settings", "wp-mail-bank");
      $mb_feedbacks = __("Feedbacks", "wp-mail-bank");
      $mb_roles_and_capabilities = __("Roles & Capabilities", "wp-mail-bank");
      $mb_system_information = __("System Information", "wp-mail-bank");

      // Footer
      $mb_success = __("Success!", "wp-mail-bank");
      $mb_update_email_configuration = __("Email Setup has been updated Successfully", "wp-mail-bank");
      $mb_update_feedbacks = __("Your Feedback has been sent Successfully", "wp-mail-bank");
      $mb_test_email_sent = __("Test Email was sent Successfully!", "wp-mail-bank");
      $mb_test_email_not_send = __("Test Email was not sent!", "wp-mail-bank");
      $mb_update_settings = __("Plugin Settings have been updated Successfully", "wp-mail-bank");
      $oauth_not_supported = __("The OAuth is not supported by providing SMTP Host, kindly provide username and password", "wp-mail-bank");
      $mb_popup_query = __("Query", "wp-mail-bank");
      $mb_language_interested_to_translate = __("Language Interested to Translate", "wp-mail-bank");
      $mb_language_interested_to_translate_tooltip = __("Please enter the language you want to translate.", "wp-mail-bank");
      $mb_language_interested_to_translate_placeholder = __("Please enter the language", "wp-mail-bank");
      $mb_popup_your_name_tooltip = __("In this field, You would need to provide your name which will be sent along with your request", "wp-mail-bank");
      $mb_important_disclaimer = __("Important Disclaimer!", "wp-mail-bank");
      $mb_popup_your_email_tooltip = __("In this field, You would need to provide your valid email which wiil be sent along with your request", "wp-mail-bank");
      $mb_popup_query_tooltip = __("Please enter your query.", "wp-mail-bank");
      $mb_popup_query_placeholder = __("Please enter the query.", "wp-mail-bank");
      $mb_manage_backups_close = __("Close", "wp-mail-bank");
      $mb_feature_requests_send_request = __("Send Request", "wp-mail-bank");
      $mb_translation_request = __("Translation Request", "wp-mail-bank");
      $mb_confirm_close = __("Are you sure you want to close without sending Translation Request?", "wp-mail-bank");
      $mb_feature_requests_your_email = __("Your Email", "wp-mail-bank");
      $mb_feature_requests_your_name = __("Your Name", "wp-mail-bank");
      $mb_feature_requests_your_name_placeholder = __("Please provide your Name", "wp-mail-bank");


      // Common Variables
      $mb_status_sent = __("Sent", "wp-mail-bank");
      $mb_status_not_sent = __("Not Sent", "wp-mail-bank");
      $mb_user_access_message = __("You don't have Sufficient Access to this Page. Kindly contact the Administrator for more Privileges", "wp-mail-bank");
      $mb_enable = __("Enable", "wp-mail-bank");
      $mb_disable = __("Disable", "wp-mail-bank");
      $mb_override = __("Override", "wp-mail-bank");
      $mb_dont_override = __("Don't Override", "wp-mail-bank");
      $mb_save_changes = __("Save Settings", "wp-mail-bank");
      $mb_subject = __("Subject", "wp-mail-bank");
      $mb_next_step = __("Next Step", "wp-mail-bank");
      $mb_previous_step = __("Previous Step", "wp-mail-bank");
      $mb_upgrade_to = __("Upgrade to Premium Editions", "wp-mail-bank");

      // Email Setup
      $mb_email_configuration_cc_label = __("Cc", "wp-mail-bank");
      $mb_email_configuration_bcc_label = __("Bcc", "wp-mail-bank");
      $mb_email_configuration_cc_email_address_tooltip = __("Please provide valid Cc Email Address", "wp-mail-bank");
      $mb_email_configuration_bcc_email_address_tooltip = __("Please provide valid Bcc Email Address", "wp-mail-bank");
      $mb_email_configuration_cc_email_placeholder = __("Please provide Cc Email", "wp-mail-bank");
      $mb_email_configuration_bcc_email_placeholder = __("Please provide Bcc Email", "wp-mail-bank");
      $mb_email_configuration_mailer_settings_tab = __("Mailer Settings", "wp-mail-bank");
      $mb_email_configuration_test_email_tab = __("Test Email", "wp-mail-bank");
      $mb_email_configuration_from_name = __("From Name", "wp-mail-bank");
      $mb_email_configuration_from_name_tooltip = __("From Name is the inbox field that tells your recipient who sent the messages. If you would like to override the pre-configured From Name, then you would need to insert the name in the inbox field", "wp-mail-bank");
      $mb_email_configuration_from_name_placeholder = __("Please provide From Name", "wp-mail-bank");
      $mb_email_configuration_from_email = __("From Email", "wp-mail-bank");
      $mb_email_address_tooltip = __("From Email is the inbox field that tells your recipient from which Email Address the messages are coming. If you would like to override the pre-configured From Email, then you would need to insert an Email Address in the inbox field", "wp-mail-bank");
      $mb_email_configuration_from_email_placeholder = __("Please provide From Email Address", "wp-mail-bank");
      $mb_email_configuration_mailer_type = __("Mailer Type", "wp-mail-bank");
      $mb_email_configuration_mailer_type_tooltip = __("This field provides you an ability to choose a specific option for Mailer Type. If you would like to send an Email via SMTP mailer, you would need to select Send Email via SMTP from the drop down or you could use PHP mail () Function", "wp-mail-bank");
      $mb_email_configuration_send_email_via_smtp = __("Send Email via SMTP", "wp-mail-bank");
      $mb_email_configuration_use_php_mail_function = __("Use The PHP mail() Function", "wp-mail-bank");
      $mb_email_configuration_smtp_host = __("SMTP Host", "wp-mail-bank");
      $mb_email_configuration_smtp_host_tooltip = __("If you would like to send an Email via different host, you would need to insert that specific host name like smtp.gmail.com in the inbox field", "wp-mail-bank");
      $mb_feature_requests_your_email_placeholder = __("Please provide your Email Address", "wp-mail-bank");
      $mb_email_configuration_smtp_host_placeholder = __("Please provide SMTP Host", "wp-mail-bank");
      $mb_email_configuration_smtp_port = __("SMTP Port", "wp-mail-bank");
      $mb_email_configuration_smtp_port_tooltip = __("This inbox field is specified to provide a valid SMTP Port for authentication", "wp-mail-bank");
      $mb_email_configuration_smtp_port_placeholder = __("Please provide SMTP Port", "wp-mail-bank");
      $mb_email_configuration_encryption = __("Encryption", "wp-mail-bank");
      $mb_email_configuration_encryption_tooltip = __("It provides you an ability to choose a specific option for Encryption. If you would like to send an Email with TLS encryption, you would need to select Use TLS Encryption from the drop down or you could use SSL Encryption. For that you would need to select Use SSL Encryption from the drop down. If you would like to send an Email without encryption, you would need to select No Encryption from the drop down", "wp-mail-bank");
      $mb_email_configuration_no_encryption = __("No Encryption", "wp-mail-bank");
      $mb_email_configuration_use_ssl_encryption = __("Use SSL Encryption", "wp-mail-bank");
      $mb_email_configuration_use_tls_encryption = __("Use TLS Encryption", "wp-mail-bank");
      $mb_email_configuration_authentication = __("Authentication", "wp-mail-bank");
      $mb_email_configuration_authentication_tooltip = __("This inbox field allows you to choose an appropriate option for authentication. It provides you the Two-way authentication factor; If you would like to authenticate yourself via Username and Password, you would need to select Use Username and Password from the drop down. You can also authenticate by an OAuth 2.0 protocol, which requires Client Id and Secret Key, for that you would need to select Use OAuth (Client ID and Secret Key) from the drop down. You can easily get Client Id and Secret Key from respective SMTP Server Developers section", "wp-mail-bank");
      $mb_email_configuration_use_smtp_authentication = __("Use SMTP Authentication", "wp-mail-bank");
      $mb_email_configuration_donot_use_authentication = __("Don't Use SMTP Authentication", "wp-mail-bank");
      $mb_email_configuration_test_email_address = __("Test Email Address", "wp-mail-bank");
      $mb_email_configuration_test_email_address_tooltip = __("In this field, you would need to provide a valid Email Address in the inbox field on which you would like to receive Test email", "wp-mail-bank");
      $mb_email_configuration_test_email_address_placeholder = __("Please provide Test Email Address", "wp-mail-bank");
      $mb_email_configuration_subject_test_tooltip = __("In this field, you would need to provide a subject for Test Email", "wp-mail-bank");
      $mb_email_configuration_subject_test_placeholder = __("Please provide Subject", "wp-mail-bank");
      $mb_email_configuration_content = __("Email Content", "wp-mail-bank");
      $mb_email_configuration_content_tooltip = __("In this field, you would need to provide the content for Test Email", "wp-mail-bank");
      $mb_email_configuration_send_test_email = __("Send Test Email", "wp-mail-bank");
      $mb_email_configuration_smtp_debugging_output = __("SMTP Debugging Output", "wp-mail-bank");
      $mb_email_configuration_send_test_email_textarea = __("Checking your settings", "wp-mail-bank");
      $mb_email_configuration_result = __("Result", "wp-mail-bank");
      $mb_email_configuration_send_another_test_email = __("Send Another Test Email", "wp-mail-bank");
      $mb_email_configuration_enable_from_name = __("From Name Configuration", "wp-mail-bank");
      $mb_email_configuration_enable_from_name_tooltip = __("If you would like to override the pre-configured name which will be used while sending Emails, then you would need to choose Override from the drop down and vice-versa", "wp-mail-bank");
      $mb_email_configuration_enable_from_email = __("From Email Configuration", "wp-mail-bank");
      $mb_email_configuration_enable_from_email_tooltip = __("If you would like to override your pre-configured Email Address which will be used while sending Emails, then you would need to choose Override from the drop down and vice-versa", "wp-mail-bank");
      $mb_email_configuration_username = __("Username", "wp-mail-bank");
      $mb_email_configuration_username_tooltip = __("In this field, you would need to provide a username to authenticate your SMTP details", "wp-mail-bank");
      $mb_email_configuration_username_placeholder = __("Please provide username", "wp-mail-bank");
      $mb_email_configuration_password = __("Password", "wp-mail-bank");
      $mb_email_configuration_password_tooltip = __("In this field, you would need to provide a password to authenticate your SMTP details", "wp-mail-bank");
      $mb_email_configuration_password_placeholder = __("Please provide password", "wp-mail-bank");
      $mb_email_configuration_redirect_uri = __("Redirect URI", "wp-mail-bank");
      $mb_email_configuration_redirect_uri_tooltip = __("Please copy this Redirect URI and Paste into Redirect URI field when creating your app", "wp-mail-bank");
      $mb_email_configuration_use_oauth = __("Use OAuth (Client Id and Secret Key required)", "wp-mail-bank");
      $mb_email_configuration_none = __("None", "wp-mail-bank");
      $mb_email_configuration_use_plain_authentication = __("Plain Authentication", "wp-mail-bank");
      $mb_email_configuration_cram_md5 = __("Cram-MD5", "wp-mail-bank");
      $mb_email_configuration_login = __("Login", "wp-mail-bank");
      $mb_email_configuration_client_id = __("Client Id", "wp-mail-bank");
      $mb_email_configuration_client_secret = __("Secret Key", "wp-mail-bank");
      $mb_email_configuration_client_id_tooltip = __("Please provide valid Client Id issued by your SMTP Host", "wp-mail-bank");
      $mb_email_configuration_client_secret_tooltip = __("Please provide valid Secret Key issued by your SMTP Host", "wp-mail-bank");
      $mb_email_configuration_client_id_placeholder = __("Please provide Client Id", "wp-mail-bank");
      $mb_email_configuration_client_secret_placeholder = __("Please provide Secret Key", "wp-mail-bank");
      $mb_email_configuration_tick_for_sent_mail = __("Yes, automatically send a Test Email upon clicking on the Next Step Button to verify settings", "wp-mail-bank");
      $mb_email_configuration_email_address = __("Email Address", "wp-mail-bank");
      $mb_email_configuration_email_address_tooltip = __("In this field, you would need to add a valid Email Address in the inbox field from which you would like to send Emails", "wp-mail-bank");
      $mb_email_configuration_email_address_placeholder = __("Please provide valid Email Address", "wp-mail-bank");
      $mb_email_configuration_reply_to = __("Reply To", "wp-mail-bank");
      $mb_email_configuration_reply_to_tooltip = __("In this field, you would need to add a valid Email Address that is automatically inserted into the Reply To field when a user replies to an email message", "wp-mail-bank");
      $mb_email_configuration_reply_to_placeholder = __("Please provide Reply To Email Address", "wp-mail-bank");
      $mb_email_configuration_get_google_credentials = __("Get Google Client Id and Secret Key", "wp-mail-bank");
      $mb_email_configuration_get_microsoft_credentials = __("Get Microsoft Client Id and Secret Key", "wp-mail-bank");
      $mb_email_configuration_get_yahoo_credentials = __("Get Yahoo Client Id and Secret Key", "wp-mail-bank");

      // Email Logs
      $mb_start_date_title = __("Start Date", "wp-mail-bank");
      $mb_resend = __("Resend", "wp-mail-bank");
      $mb_start_date_placeholder = __("Please provide Start Date", "wp-mail-bank");
      $mb_start_date_tooltip = __("This field shows starting date of Email Logs", "wp-mail-bank");
      $mb_end_date_title = __("End Date", "wp-mail-bank");
      $mb_end_date_placeholder = __("Please provide End Date", "wp-mail-bank");
      $mb_end_date_tooltip = __("This field shows ending date of Email Logs", "wp-mail-bank");
      $mb_submit = __("Submit", "wp-mail-bank");
      $mb_email_logs_bulk_action = __("Bulk Action", "wp-mail-bank");
      $mb_email_logs_delete = __("Delete", "wp-mail-bank");
      $mb_email_logs_apply = __("Apply", "wp-mail-bank");
      $mb_email_logs_email_to = __("Email To", "wp-mail-bank");
      $mb_email_logs_actions = __("Action", "wp-mail-bank");
      $mb_email_logs_show_details = __("Show Details", "wp-mail-bank");
      $mb_email_logs_close = __("Close", "wp-mail-bank");
      $mb_email_logs_debugging_output = __("Debugging Output", "wp-mail-bank");
      $mb_email_logs_show_outputs = __("Show Debugging Output", "wp-mail-bank");
      $mb_email_sent_to = __("Email Sent To", "wp-mail-bank");
      $mb_date_time = __("Date/Time", "wp-mail-bank");
      $mb_email_logs_status = __("Status", "wp-mail-bank");
      $mb_from = __("From", "wp-mail-bank");
      $mb_back_to_email_logs = __("Back to Email Logs", "wp-mail-bank");

      // Settings
      $mb_settings_debug_mode = __("Debug Mode", "wp-mail-bank");
      $mb_settings_debug_mode_tooltip = __("Please choose a specific option for Debug Mode", "wp-mail-bank");
      $mb_remove_tables_title = __("Remove Tables at Uninstall", "wp-mail-bank");
      $mb_remove_tables_tooltip = __("Please choose a specific option whether to allow Remove Tables at Uninstall", "wp-mail-bank");
      $mb_monitoring_email_log_title = __("Monitoring Email Logs", "wp-mail-bank");
      $mb_monitoring_email_log_tooltip = __("This field is used to allow Email Logs to monitor or not", "wp-mail-bank");

      // Roles and Capabilities
      $mb_roles_capabilities_show_menu = __("Show Mail Bank Menu", "wp-mail-bank");
      $mb_roles_capabilities_show_menu_tooltip = __("Please choose a specific role who can see Sidebar Menu", "wp-mail-bank");
      $mb_roles_capabilities_administrator = __("Administrator", "wp-mail-bank");
      $mb_roles_capabilities_author = __("Author", "wp-mail-bank");
      $mb_roles_capabilities_editor = __("Editor", "wp-mail-bank");
      $mb_roles_capabilities_contributor = __("Contributor", "wp-mail-bank");
      $mb_roles_capabilities_subscriber = __("Subscriber", "wp-mail-bank");
      $mb_roles_capabilities_others = __("Others", "wp-mail-bank");
      $mb_roles_capabilities_topbar_menu = __("Show Mail Bank Top Bar Menu", "wp-mail-bank");
      $mb_roles_capabilities_topbar_menu_tooltip = __("Please choose a specific option from Mail Bank Top Bar Menu", "wp-mail-bank");
      $mb_roles_capabilities_administrator_role = __("An Administrator Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_administrator_role_tooltip = __("Please choose specific page available for the Administrator Access", "wp-mail-bank");
      $mb_roles_capabilities_full_control = __("Full Control", "wp-mail-bank");
      $mb_roles_capabilities_author_role = __("An Author Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_author_role_tooltip = __("Please choose specific page available for Author Access", "wp-mail-bank");
      $mb_roles_capabilities_editor_role = __("An Editor Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_editor_role_tooltip = __("Please choose specific page available for Editor Access", "wp-mail-bank");
      $mb_roles_capabilities_contributor_role = __("A Contributor Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_contributor_role_tooltip = __("Please choose specific page available for Contributor Access", "wp-mail-bank");
      $mb_roles_capabilities_other_role = __("Other Roles can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_other_role_tooltip = __("Please choose specific page available for Others Role Access", "wp-mail-bank");
      $mb_roles_capabilities_other_roles_capabilities = __("Please tick the appropriate capabilities for security purposes ", "wp-mail-bank");
      $mb_roles_capabilities_other_roles_capabilities_tooltip = __("Only users with these capabilities can access Mail Bank", "wp-mail-bank");
      $mb_roles_capabilities_subscriber_role = __("A Subscriber Role can do the following", "wp-mail-bank");
      $mb_roles_capabilities_subscriber_role_tooltip = __("Please choose specific page available for Subscriber Access", "wp-mail-bank");

      // Feedbacks
      $mb_feedbacks_thank_you = __("Thank You!", "wp-mail-bank");
      $mb_feedbacks_suggest_some_features = __("Kindly fill in the below form, if you would like to suggest some features which are not in the Plugin", "wp-mail-bank");
      $mb_feedbacks_suggestion_complaint = __("If you also have any suggestion/complaint, you can use the same form below", "wp-mail-bank");
      $mb_feedbacks_write_us_on = __("You can also write us on", "wp-mail-bank");
      $mb_feedbacks_your_name = __("Your Name", "wp-mail-bank");
      $mb_feedbacks_your_name_tooltip = __("Please provide your Name which will be sent along with your Feedback", "wp-mail-bank");
      $mb_feedbacks_your_name_placeholder = __("Please provide your Name", "wp-mail-bank");
      $mb_feedbacks_your_email = __("Your Email", "wp-mail-bank");
      $mb_feedbacks_your_email_tooltip = __("Please provide your Email Address which will be sent along with your Feedback", "wp-mail-bank");
      $mb_feedbacks_your_email_placeholder = __("Please provide your Email Address", "wp-mail-bank");
      $mb_feedbacks_tooltip = __("Please provide your Feedback which will be sent along", "wp-mail-bank");
      $mb_feedbacks_placeholder = __("Please provide your Feedback", "wp-mail-bank");
      $mb_feedbacks_send_feedback = __("Send Feedback", "wp-mail-bank");
      $mb_feature_request_message = __("Your request Email has been sent Successfully", "wp-mail-bank");

      // Test Email
      $mb_test_email_sending_test_email = __("Sending Test Email to", "wp-mail-bank");
      $mb_test_email_status = __("Email Status", "wp-mail-bank");

      // Connectivity Test
      $mb_connectivity_test = __("Connectivity Test", "wp-mail-bank");
      $mb_transport = __("Transport", "wp-mail-bank");
      $mb_socket = __("Socket", "wp-mail-bank");
      $mb_status = __("Status", "wp-mail-bank");
      $mb_service_available = __("Service Available", "wp-mail-bank");
      $mb_server_id = __("Server ID", "wp-mail-bank");
      $mb_authentication = __("Authentication", "wp-mail-bank");
      $mb_plain = __("Plain", "wp-mail-bank");
      $mb_cram_md5 = __("CRAM-MD5", "wp-mail-bank");
      $mb_0auth = __("OAuth 2.0", "wp-mail-bank");
      $mb_smtp = __("SMTP", "wp-mail-bank");
      $mb_gmail_api = __("Gmail API", "wp-mail-bank");
      $mb_mail_server_host = __("Outgoing Mail Server Hostname", "wp-mail-bank");
      $mb_begin_test = __("Begin Test", "wp-mail-bank");
      $mb_localhost = __("localhost", "wp-mail-bank");
      $mb_mail_server_tooltip = __("In this field, you would need to provide Outgoing Mail Server Hostname", "wp-mail-bank");
   }
}