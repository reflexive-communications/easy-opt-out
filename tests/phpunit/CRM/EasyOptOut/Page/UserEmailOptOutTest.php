<?php

use CRM_EasyOptOut_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * It checks the opt out process.
 * Given:
 * - contact
 * - email
 * - group
 * - contact is added to the group
 * - mosaico message, with the group as include group
 * - process mailing jobs
 * When:
 * - call the easy-opt-out landing
 *
 * @group headless
 */
class CRM_EasyOptOut_Page_UserEmailOptOutTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface
{
    public function setUpHeadless()
    {
        return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Apply a forced rebuild of DB, thus
     * create a clean DB before running tests
     *
     * @throws \CRM_Extension_Exception_ParseException
     */
    public static function setUpBeforeClass(): void
    {
        // Resets DB and install depended extension
        \Civi\Test::headless()
            ->install('org.civicrm.flexmailer')
            ->installMe(__DIR__)
            ->apply(true);
    }

    /**
     * Create a clean DB before running tests
     *
     * @throws CRM_Extension_Exception_ParseException
     */
    public static function tearDownAfterClass(): void
    {
        \Civi\Test::headless()
            ->uninstallMe(__DIR__)
            ->uninstall('org.civicrm.flexmailer')
            ->apply(true);
    }

    /*
     * On case of missing parameters (jid, qid, h) it has to throw exception.
     */
    public function testRunMissingParameters()
    {
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
        $page = new CRM_EasyOptOut_Page_UserEmailOptOut();
        self::expectException(CRM_Core_Exception::class);
        self::expectExceptionMessage(ts('Missing input parameters'));
        $page->run();
    }
    public function testRunInvalidParameters()
    {
        $_GET = [
            'jid' => '10',
            'qid' => '10',
            'h' => 'wronghash',
        ];
        $_POST = [];
        $_REQUEST = [
            'jid' => '10',
            'qid' => '10',
            'h' => 'wronghash',
        ];
        $page = new CRM_EasyOptOut_Page_UserEmailOptOut();
        self::expectException(CRM_Core_Exception::class);
        self::expectExceptionMessage(ts('There was an error in your request'));
        $page->run();
    }
    private function createGroup(): int
    {
        $result = civicrm_api3('Group', 'create', [
            'title' => "Test title",
            'visibility' => "Public Pages",
            'group_type' => "Mailing List",
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing group id.');
        return $result['id'];
    }
    private function addNewContactWithEmailToGroup(int $groupId): int
    {
        $result = civicrm_api3('Contact', 'create', [
            'contact_type' => 'Individual',
            'first_name' => 'Bob',
            'last_name' => 'Lastname',
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing contact id for the new user.');
        $contactId = $result['id'];
        $result = civicrm_api3('GroupContact', 'create', [
            'group_id' => $groupId,
            'contact_id' => $contactId,
            'status' => "Added",
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('added', $result), 'Missing added key from groupContact.');
        self::assertSame(1, $result['added'], 'One contact has to be added to the group.');
        $result = civicrm_api3('Email', 'create', [
            'contact_id' => $contactId,
            'email' => 'individual.bob.lastname@email.com',
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing email id.');
        return $contactId;
    }
    private function setupMailing()
    {
        $domainName = 'my-domain';
        $result = civicrm_api3('Domain', 'create', [
            'name' => $domainName,
            'domain_version' => '5.37.1',
            'id' => 1,
            'contact_id' => 1,
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing id from the domain update.');
        $result = civicrm_api3('MailSettings', 'create', [
            'id' => 1,
            'domain_id' => $domainName,
            'name' => 'myMailerAccount',
            'domain' => 'civicrm-base.com',
            'protocol' => 'POP3',
            'username' => 'admin',
            'password' => 'admin',
            'activity_status' => 'Completed',
            'is_default' => 1,
            'is_ssl' => 0,
            'is_non_case_email_skipped' => 0,
            'is_contact_creation_disabled_if_no_match' => 0,
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing id from the MailSettings update.');
        $result = civicrm_api3('Setting', 'create', [
            'mailing_backend' => ["outBound_option"=>5,"smtpUsername"=>"admin","smtpPassword"=>"admin"]
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing id from the mailing_backend update.');
        $result = civicrm_api3('OptionValue', 'create', [
            'option_group_id' => 'from_email_address',
            'label' => '"info" <info@civicrm-base.com>',
            'name' => '"info" <info@civicrm-base.com>',
            'domain_id' => $domainName,
            'is_default' => 1,
            'is_active' =>1,
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing id from the OptionValue update.');
    }
    private function processMailing(int $groupId)
    {
        $result = civicrm_api3('Mailing', 'create', [
            'subject' => "email subject",
            'name' => "email name",
            'template_type' => "traditional",
            'body_html' => "<html><head><title>T</title></head><body><div>{EasyOptOut.user_opt_out_url}</div></body></html>",
            'scheduled_id' => $groupId,
            'scheduled_date' => "2021-05-20 17:09:16",
            'group' => ['include' => [$groupId], 'exclude' => []],
            'approver_id' => $groupId,
            'approval_date' => "2021-05-20 17:09:16",
            'approval_status_id' => 1,
        ]);
        echo var_export($result, true)."\n";
        $result = civicrm_api3('Job', 'process_mailing');
        echo var_export($result, true)."\n";
    }
    private function processEmptyMailing(int $groupId, int $contactId)
    {
        $result = civicrm_api3('Mailing', 'create', [
            'subject' => "email subject",
            'name' => "email name",
            'template_type' => "traditional",
            'body_html' => "<div>Token supposed to be here. {domain.address}</div>",
            'body_text' => "Token supposed to be here. {domain.address}",
            'group' => ['include' => [$groupId], 'exclude' => []],
            'mailings' => ['include' => [], 'exclude' => []],
            'header_id' => '',
            'footer_id' => '',
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing id from the Mailing create.');
        $mailingId = $result['id'];
        // attachment replace that could be found in the browser console.
        $result = civicrm_api3('Attachment', 'replace', [
            'entity_table' => 'civicrm_mailing',
            'entity_id' => $mailingId,
            'values' => [],
        ]);
        echo "Attachment replace: ".var_export($result, true)."\n";
        // create logged in user.
        $result = civicrm_api3('UFMatch', 'get', ['uf_id' => 6, 'api.UFMatch.delete' => []]);
        self::assertSame(0, $result['is_error']);
        $result = civicrm_api3('UFMatch', 'create', [
            'contact_id' => $contactId,
            'uf_name' => 'superman',
            'uf_id' => 6,
        ]);
        self::assertSame(0, $result['is_error']);
        self::assertTrue(array_key_exists('id', $result), 'Missing id from the Mailing create.');
        $session = CRM_Core_Session::singleton();
        $session->set('userID', $contactId);

        $result = civicrm_api3('Mailing', 'submit', [
            'id' => $mailingId,
            'approval_date' => 'now',
            'scheduled_date' => 'now',
        ]);
        echo var_export($result, true)."\n";
        $result = civicrm_api3('Job', 'process_mailing');
        echo var_export($result, true)."\n";
    }
    public function testRunDoOptOutWithoutToken()
    {
        $this->setupMailing();
        $groupId = $this->createGroup();
        $contactId = $this->addNewContactWithEmailToGroup($groupId);
        $this->processEmptyMailing($groupId, $contactId);
    }
    public function testRunDoOptOut()
    {
        $groupId = $this->createGroup();
        //$this->addNewContactWithEmailToGroup($group['id']);
        //$this->setupMailing();
        //$this->processMailing($group['id']);
    }
}
