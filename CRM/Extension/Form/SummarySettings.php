<?php

use CRM_Extension_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Extension_Form_SummarySettings extends CRM_Core_Form {
  public function buildQuickForm() {


    // add form elements
    $this->add(
        'checkbox', // field type
        'display_memberships', // field name
        ts('Memberships in Summary') // field label
        );

    $this->add(
        'checkbox', // field type
        'display_contributions', // field name
        ts('Contributions in Summary') // field label
        );


    $this->addButtons(array(
          array(
            'type' => 'submit',
            'name' => E::ts('Submit'),
            'isDefault' => TRUE,
            ),
          ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();

}



  public function postProcess() {
    $params = $this->controller->exportValues($this->_name) ;
    CRM_Core_Error::debug_log_message('marsh: ' . print_r($this->_name, TRUE));
    foreach (array(
          'display_contributions',
          'display_memberships', 
          ) 
        as $field) {
      $daoparam = array();
      $daoparam['name'] = $field;
      CRM_Core_Error::debug_log_message('before CRM_Utils_Array::' . print_r($daoparam, TRUE));
      $daoparam['value'] = (int) CRM_UTILS_Array::value($field, $params);
      CRM_Core_Error::debug_log_message('after CRM_Utils_Array::' . print_r($daoparam, TRUE));
      CRM_Extension_BAO_Settings::create($daoparam);
      CRM_Core_Error::debug_log_message('marsh: saved: ' . print_r($daoparam, TRUE));
      CRM_Core_Session::setStatus(ts('Settings saved.'));
    }
  }

  public function setDefaultValues() {
  $defaults = array();
  $sql = "SELECT * FROM civicrm_extension_settings";
  $dao = CRM_Core_DAO::executeQuery($sql);
  while ($dao->fetch()) {
    $defaults[$dao->name] = $dao->value;
  }
  return $defaults;
  }

          /**
           * Get the fields/elements defined in this form.
           *
           * @return array (string)
           */
          public function getRenderableElementNames() {
          // The _elements list includes some items which should not be
          // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
          // items don't have labels.  We'll identify renderable by filtering on
          // the 'label'.

            $elementNames = array();
            foreach ($this->_elements as $element) {
              /** @var HTML_QuickForm_Element $element */
              $label = $element->getLabel();
              if (!empty($label)) {
                $elementNames[] = $element->getName();
              }
            }
            return $elementNames;
          }

}
