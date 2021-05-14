<?php
use CRM_EasyOptOut_ExtensionUtil as E;

class CRM_EasyOptOut_Page_UserEmailOptOut extends CRM_Core_Page
{
    public function run()
    {
        // URL validation.
        $cid = CRM_Utils_Request::retrieve('cid', 'Int');
        $checkSum = CRM_Utils_Request::retrieve('cs', 'String');
        if (!CRM_Contact_BAO_Contact_Utils::validChecksum($cid, $checkSum)) {
            throw new CRM_Core_Exception(ts('Invalid URL'));
        }
        // Opt-out the contact
        $contactData = [
            'contact_id' => $cid,
            'is_opt_out' => 1,
        ];
        civicrm_api3('Contact', 'create', $contactData);
        // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
        CRM_Utils_System::setTitle(E::ts('UserEmailOptOut'));

        // Example: Assign a variable for use in a template
        $this->assign('message', ts('Done'));

        parent::run();
    }
}
