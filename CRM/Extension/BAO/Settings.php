<?php

class CRM_Extension_BAO_Settings
  extends CRM_Extension_DAO_Settings {

  public static function create($params) {
    $instance = new CRM_Extension_DAO_Settings();
    $instance->find(TRUE);
    $instance->copyvalues($params);
    $instance->save();
  }
}
