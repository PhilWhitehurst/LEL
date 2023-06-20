<?php
/**
 * This file is used for displaying sidebar menus.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/includes
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
      ?>
      <div class="page-sidebar-wrapper-tech-banker">
         <div class="page-sidebar-tech-banker navbar-collapse collapse">
            <div class="sidebar-menu-tech-banker">
               <ul class="page-sidebar-menu-tech-banker" data-slide-speed="200">
                  <div class="sidebar-search-wrapper" style="padding:20px;text-align:center">
                     <a class="plugin-logo" href="<?php echo tech_banker_beta_url; ?>" target="_blank">
                        <img src="<?php echo plugins_url("assets/global/img/mail-bank-logo.png", dirname(__FILE__)); ?>" alt="Mail Bank">
                     </a>
                  </div>
                  <li id="ux_mb_li_email_configuration">
                     <a href="admin.php?page=mb_email_configuration">
                        <i class="icon-custom-envelope-open"></i>
                        <span class="title">
                           <?php echo $mb_email_configuration; ?>
                        </span>
                     </a>
                  </li>
                  <li id="ux_mb_li_test_email">
                     <a href="admin.php?page=mb_test_email">
                        <i class="icon-custom-envelope "></i>
                        <span class="title">
                           <?php echo $mb_test_email; ?>
                        </span>
                     </a>
                  </li>
                  <li id="ux_mb_li_connectivity_test">
                     <a href="admin.php?page=mb_connectivity_test">
                        <i class="icon-custom-globe"></i>
                        <span class="title">
                           <?php echo $mb_connectivity_test; ?>
                        </span>
                     </a>
                  </li>
                  <li id="ux_mb_li_email_logs">
                     <a href="admin.php?page=mb_email_logs">
                        <i class="icon-custom-note"></i>
                        <span class="title">
                           <?php echo $mb_email_logs; ?>
                        </span>
                        <span class="badge">
                           Pro
                        </span>
                     </a>
                  </li>
                  <li id="ux_mb_li_settings">
                     <a href="admin.php?page=mb_settings">
                        <i class="icon-custom-paper-clip"></i>
                        <span class="title">
                           <?php echo $mb_settings; ?>
                        </span>
                     </a>
                  </li>
                  <li id="ux_mb_li_roles_and_capabilities">
                     <a href="admin.php?page=mb_roles_and_capabilities">
                        <i class="icon-custom-user"></i>
                        <span class="title">
                           <?php echo $mb_roles_and_capabilities; ?>
                        </span>
                        <span class="badge">Pro</span>
                     </a>
                  </li>
                  <li id="ux_mb_li_feedbacks">
                     <a href="admin.php?page=mb_feedbacks">
                        <i class="icon-custom-star"></i>
                        <span class="title">
                           <?php echo $mb_feedbacks; ?>
                        </span>
                     </a>
                  </li>
                  <li id="ux_mb_li_system_information">
                     <a href="admin.php?page=mb_system_information">
                        <i class="icon-custom-screen-desktop"></i>
                        <span class="title">
                           <?php echo $mb_system_information; ?>
                        </span>
                     </a>
                  </li>
                  <li class="" id="ux_li_upgrade">
                     <a href="admin.php?page=mb_upgrade">
                        <i class="icon-custom-briefcase"></i>
                        <span class="title">
                           <?php echo $mb_upgrade; ?>
                        </span>
                     </a>
                  </li>
               </ul>
            </div>
         </div>
      </div>
      <div class="page-content-wrapper">
         <div class="page-content">
            <div style="margin-bottom:10px">
               <a href="http://beta.tech-banker.com/products/mail-bank/" target="_blank">
                  <img src="<?php echo plugins_url("assets/global/img/mail-bank-banner.png", dirname(__FILE__)); ?>" title="Mail Bank" style="width: 100%;">
               </a>
            </div>
            <div class="container-fluid page-header-container">
               <div class="row">
                  <div class="col-md-3 page-header-column">
                     <h4>Get Started</h4>
                     <a class="btn" href="#" target="_blank">Watch Video!</a>
                     <p>or <a href="http://beta.tech-banker.com/products/mail-bank/user-guide/" target="_blank">read documentation here</a></p>
                  </div>
                  <div class="col-md-3 page-header-column">
                     <h4>Go Premium</h4>
                     <ul>
                        <li><a href="http://beta.tech-banker.com/products/mail-bank/" target="_blank">Features</a></li>
                        <li><a href="http://beta.tech-banker.com/products/mail-bank/demos/" target="_blank">Online Demos</a></li>
                        <li><a href="http://beta.tech-banker.com/products/mail-bank/pricing/" target="_blank">Pricing Plans</a></li>
                     </ul>
                  </div>
                  <div class="col-md-3 page-header-column">
                     <h4>User Guide</h4>
                     <ul>
                        <li><a href="http://beta.tech-banker.com/products/mail-bank/user-guide/" target="_blank">Documentation</a></li>
                        <li><a href="https://wordpress.org/support/plugin/wp-mail-bank" target="_blank">Support Question!</a></li>
                        <li><a href="http://beta.tech-banker.com/contact-us/" target="_blank">Contact Us</a></li>
                     </ul>
                  </div>
                  <div class="col-md-3 page-header-column">
                     <h4>More Actions</h4>
                     <ul>
                        <li><a href="https://wordpress.org/support/plugin/wp-mail-bank/reviews/?filter=5" target="_blank">Rate Us!</a></li>
                        <li><a href="http://beta.tech-banker.com/products/" target="_blank">Our Other Products</a></li>
                        <li><a href="http://beta.tech-banker.com/services/" target="_blank">Our Other Services</a></li>
                     </ul>
                  </div>
               </div>
            </div>
            <?php
         }
      }