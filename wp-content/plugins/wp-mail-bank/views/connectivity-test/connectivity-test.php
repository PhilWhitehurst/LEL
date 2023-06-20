<?php
/**
 * This Template is used for connectivity test.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/views/connectivity-test
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
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
   } else if (conectivity_test_email_mail_bank == "1") {
      $connectivity_test_nonce = wp_create_nonce("connectivity_test_nonce");
      ?>
      <div class="page-bar">
         <ul class="page-breadcrumb">
            <li>
               <i class="icon-custom-home"></i>
               <a href="admin.php?page=mb_email_configuration">
                  <?php echo $wp_mail_bank; ?>
               </a>
               <span>></span>
            </li>
            <li>
               <span>
                  <?php echo $mb_connectivity_test; ?>
               </span>
            </li>
         </ul>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="portlet box vivid-green">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="icon-custom-globe"></i>
                     <?php echo $mb_connectivity_test; ?>
                  </div>
                  <a href="http://beta.tech-banker.com/products/mail-bank/" target="_blank" class="premium-editions"><?php echo $mb_upgrade_to ?></a>
               </div>
               <div class="portlet-body form">
                  <form id="ux_frm_settings">
                     <div class="form-body">
                        <?php
                        if ($mb_message_translate_help != "") {
                           ?>
                           <div class="note note-danger">
                              <h4 class="block">
                                 <?php echo $mb_important_disclaimer; ?>
                              </h4>
                              <strong><?php echo $mb_message_translate_help; ?> <a href="javascript:void(0);" data-popup-open="ux_open_popup_translator" class="custom_links" onclick="show_pop_up_mail_bank();"><?php echo $mb_message_translate_here; ?></a></strong>
                           </div>
                           <?php
                        }
                        ?>
                        <div class="form-group">
                           <label class="control-label">
                              <?php echo $mb_mail_server_host; ?> :
                              <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_mail_server_tooltip; ?>" data-placement="right"></i>
                              <span class="required" aria-required="true">*</span>
                           </label>
                           <input type="text" class="form-control" name="ux_txt_conn_search" id="ux_txt_conn_search" value="" placeholder="<?php echo $mb_localhost; ?>">
                        </div>
                        <div class="form-actions">
                           <div class="pull-right">
                              <input type="submit" class="btn vivid-green" name="ux_btn_begin_test" id="ux_btn_begin_test" value="<?php echo $mb_begin_test; ?>">
                           </div>
                        </div>
                        <div class="line-separator"></div>
                        <div id="ux_div_connectivity_test" style="display:none;">
                           <table class="table table-striped table-bordered table-hover table-margin-top">
                              <thead>
                                 <tr>
                                    <th rowspan="2"><?php echo $mb_transport; ?></th>
                                    <th rowspan="2"><?php echo $mb_socket; ?></th>
                                    <th rowspan="2"><?php echo $mb_status; ?></th>
                                 </tr>
                              </thead>
                              <tbody id="ux_tbody_smtp">
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
      <?php
   } else {
      ?>
      <div class="page-bar">
         <ul class="page-breadcrumb">
            <li>
               <i class="icon-custom-home"></i>
               <a href="admin.php?page=mb_email_configuration">
                  <?php echo $wp_mail_bank; ?>
               </a>
               <span>></span>
            </li>
            <li>
               <span>
                  <?php echo $mb_connectivity_test; ?>
               </span>
            </li>
         </ul>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="portlet box vivid-green">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="icon-custom-paper-clip"></i>
                     <?php echo $mb_connectivity_test; ?>
                  </div>
               </div>
               <div class="portlet-body form">
                  <div class="form-body">
                     <strong><?php echo $mb_user_access_message; ?></strong>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php
   }
}