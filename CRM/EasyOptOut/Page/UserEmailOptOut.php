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
        $jobId = CRM_Utils_Request::retrieve('jid', 'Int');
        $queueId = CRM_Utils_Request::retrieve('qid', 'Int');
        $hash = CRM_Utils_Request::retrieve('h', 'String');
        if (!$jobId || !$queueId || !$hash) {
            throw new CRM_Core_Exception(ts("Missing input parameters"));
        }
        // verify that the three numbers above match
        $q = CRM_Mailing_Event_BAO_Queue::verify($jobId, $queueId, $hash);
        if (!$q) {
            throw new CRM_Core_Exception(ts("There was an error in your request"));
        }
        // Opt out contact.
        if (self::doOptOut($cid, $queueId)) {
            CRM_Mailing_Event_BAO_Unsubscribe::send_unsub_response($queueId, null, true, $jobId);
        }
        CRM_Utils_System::setTitle(E::ts('UserEmailOptOut'));
        // Get email to show it to the user on the landing page.
        list($displayName, $email) = CRM_Mailing_Event_BAO_Queue::getContactInfo($queueId);
        $statusMsg = ts(
            '%1 opt out confirmed.',
            [1 => $email]
        );

        // Example: Assign a variable for use in a template
        $this->assign('message', $statusMsg);

        parent::run();
    }

    private static function doOptOut(int $contactId, int $queueId)
    {
        $transaction = new CRM_Core_Transaction();

        $contact = new CRM_Contact_BAO_Contact();
        $contact->id = $contactId;
        $contact->is_opt_out = true;
        $contact->save();

        $ue = new CRM_Mailing_Event_BAO_Unsubscribe();
        $ue->event_queue_id = $queueId;
        $ue->org_unsubscribe = 1;
        $ue->time_stamp = date('YmdHis');
        $ue->save();

        $shParams = [
            'contact_id' => $contactId,
            'group_id' => null,
            'status' => 'Removed',
            'method' => 'Email',
            'tracking' => $ue->id,
        ];
        CRM_Contact_BAO_SubscriptionHistory::create($shParams);

        $transaction->commit();

        return true;
    }
}
