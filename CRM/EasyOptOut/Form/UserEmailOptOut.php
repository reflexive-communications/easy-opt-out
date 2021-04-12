<?php

use CRM_EasyOptOut_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_EasyOptOut_Form_UserEmailOptOut extends CRM_Core_Form
{
    public $cid;
    public $checkSum;

    /**
     * Preprocess form. It validates the checksum.
     *
     * @throws CRM_Core_Exception
     */
    public function preProcess()
    {
        // The auto submit process will be implemented with js, so that i will need to
        // some custom js files here.
        // Also the checksum has to be validated agains the cid.
        if (!self::isSubmitted()) {
            $this->cid = CRM_Utils_Request::retrieve('cid', 'Int');
            $this->checkSum = CRM_Utils_Request::retrieve('cs', 'String');
            if (!CRM_Contact_BAO_Contact_Utils::validChecksum($this->cid, $this->checkSum)) {
                throw new CRM_Core_Exception(ts('Invalid URL'));
            }
        }
        parent::preProcess();
    }

    public function buildQuickForm()
    {
        // Only hidden inputs supposed to be added here, as it will be submitted with the js.
        $this->add('hidden', 'cid');
        $this->add('hidden', 'checkSum');
        $this->addButtons([
            [
                'type' => 'submit',
                'name' => E::ts('Save'),
                'isDefault' => TRUE,
            ],
        ]);
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
        $this->_defaults['cid'] = $this->cid;
        $this->_defaults['checkSum'] = $this->checkSum;
        return $this->_defaults;
    }

    public function postProcess()
    {
        // apply user opt out
        $submittedValues = $this->exportValues();
        $contactId = $submittedValues['cid'];
        $contactData = [
            'contact_id' => $contactId,
            'is_opt_out' => 1,
        ];
        civicrm_api3('Contact', 'create', $contactData);
        parent::postProcess();
    }
}
