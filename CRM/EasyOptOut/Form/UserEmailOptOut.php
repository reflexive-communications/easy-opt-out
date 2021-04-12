<?php

use CRM_EasyOptOut_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_EasyOptOut_Form_UserEmailOptOut extends CRM_Core_Form
{
    /**
     * Preprocess form
     *
     * @throws CRM_Core_Exception
     */
    public function preProcess()
    {
        // The auto submit process will be implemented with js, so that i will need to
        // some custom js files here.
        parent::preProcess();
    }

    public function buildQuickForm()
    {
        // Only hidden inputs supposed to be added here, as it will be submitted with the js.
        parent::buildQuickForm();
    }

    /**
     * Set default values
     *
     * @return array
     */
    public function setDefaultValues()
    {
        // set the value of the hidden inputs
        return $this->_defaults;
    }

    public function postProcess()
    {
        // apply user opt out
        parent::postProcess();
    }
}
