<?php

require_once 'extension.civix.php';
use CRM_Extension_ExtensionUtil as E;


function extension_civicrm_summary( $contactID, &$content, &$contentPlacement = CRM_Utils_Hook::SUMMARY_BELOW) {
   $contentPlacement = CRM_Utils_Hook::SUMMARY_ABOVE;
 
   $content  = show_contribution_total( $contactID );
   $content .= show_memberships( $contactID );
 
 
 }
 
 //
   // How to pass variables into .tpl files
 
 //function extension_civicrm_pageRun( &$page ) {
   //if ($page->_name == CRM_Contact_Page_View_Summary) {
    // $page->assign('name', 'Joe');
   //}
 
 function show_contribution_total( $contactID ) {
 
   //set query
   $contribSql = "
     SELECT * 
     FROM civicrm_extension_settings
     WHERE name  = %1
       AND value = %2";
 
   // run query
   $contribDAO = CRM_Core_DAO::executeQuery($contribSql, array(
     '1' => array('display_contributions', 'String'),
     '2' => array(1, 'Integer')
     ));
 
   if ($contribDAO->fetch()) {
     // if it's set, then display contributions
 
     //api call...
     $result = civicrm_api3('Contribution', 'get', array(
       'sequential' => 1,
       'contact_id' => $contactID,
     ));
 
     // set the total to start
     $total = 0;
 
 
     // lop through to add all valid
     foreach ($result['values'] as $key => $value) {
       # code...
       $total += $value['total_amount'];
     }
 
 
     if ($total > 0) {
       $total = CRM_Utils_Money::format($total);
       $content =<<<EOT
         <div id="contribution-summary">
           <div>
               <h3 class="crm_header">Contributions</h3>
               <span>Total Contributions to date: {$total}</span>
           </div>
         </div>
EOT;
     }
     return $content;
   }
 }



function show_memberships( $contactID ) {

  watchdog('marsh', 'showing memberships');
  // SQL to query "is display_memberships set"
  $membSql = "
    SELECT value  
    FROM civicrm_extension_settings 
    WHERE name  = %1 
    AND value = %2";

  // run query 
  $membDAO = CRM_Core_DAO::executeQuery($membSql, array(
        '1' => array('display_memberships', 'String'),
        '2' => array(1, 'Integer')
        ));


  // if there was a result, display the section
  if ($membDAO->fetch()) {

    // api call...
    $result = civicrm_api3('Membership', 'get', array(
          'sequential' => 1,
          'status_id.name' => array('IN' => array("New", "Current", "Grace")),
          'contact_id' => $contactID,
          ));

    CRM_Core_Error::debug_log_message( 'Got Memberships: ' . print_r( $result, TRUE ) . print_r( $contactID, TRUE));


    // skip if there are no Memberships to show
    if ( $result['count'] > 0 ) {
      $columns = array(
          'membership_name' => "Membership Type",
          'start_date' => 'Start Date',
          'end_date' => 'End Date'
          );

      // create the HTML section
      $content =<<<EOT
        <div id="membership-summary">
        <h3 class="crm_header"> Current Memberships </h3>
        <table>



EOT;
      foreach ( $result['values'] as $key => $value) { $content .=<<<EOT
        <tr>

EOT;
        // row headers
        foreach ( $columns as $key => $value) { $content .=<<<EOT
          <th>{$value}</th>

EOT;
        }
        // end of row
        $content .=<<<EOT
          </tr>

EOT;
      }
      // membership rowa
      foreach ($result['values'] as $key => $value) {
        $content .=<<<EOT
          <tr>
            <td><div class="crm-summary-row"> {$value['membership_name']}</div></td>
            <td><div class="crm-summary-row"> {$value['start_date']}  </div></td>
            <td><div class="crm-summary-row"> {$value['end_date']}  </div></td>
          </tr>

EOT;
        CRM_Core_Error::debug_log_message( 'marsh: Membership type: ' . print_r( $value['membership_name'], TRUE ));

      }

      // close the HTML section (from opening above)
      $content .=<<<EOT
        </tr>
        </table>
        </div>

EOT;


      CRM_Core_Error::debug_log_message( 'marsh: HTML: ' . print_r( $content, TRUE ));

      return $content;
    }
  }


}



/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function extension_civicrm_config(&$config) {
  _extension_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function extension_civicrm_xmlMenu(&$files) {
  _extension_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function extension_civicrm_install() {
  _extension_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function extension_civicrm_postInstall() {
  _extension_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function extension_civicrm_uninstall() {
  _extension_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function extension_civicrm_enable() {
  _extension_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function extension_civicrm_disable() {
  _extension_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function extension_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _extension_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function extension_civicrm_managed(&$entities) {
  _extension_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function extension_civicrm_caseTypes(&$caseTypes) {
  _extension_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function extension_civicrm_angularModules(&$angularModules) {
  _extension_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function extension_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _extension_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function extension_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function extension_civicrm_navigationMenu(&$menu) {
  _extension_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _extension_civix_navigationMenu($menu);
} // */
