<?php

require_once 'easy_opt_out.civix.php';
// phpcs:disable
use CRM_EasyOptOut_ExtensionUtil as E;

// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function easy_opt_out_civicrm_config(&$config)
{
    _easy_opt_out_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function easy_opt_out_civicrm_xmlMenu(&$files)
{
    _easy_opt_out_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function easy_opt_out_civicrm_install()
{
    _easy_opt_out_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function easy_opt_out_civicrm_postInstall()
{
    _easy_opt_out_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function easy_opt_out_civicrm_uninstall()
{
    _easy_opt_out_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function easy_opt_out_civicrm_enable()
{
    _easy_opt_out_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function easy_opt_out_civicrm_disable()
{
    _easy_opt_out_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function easy_opt_out_civicrm_upgrade($op, CRM_Queue_Queue $queue = null)
{
    return _easy_opt_out_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function easy_opt_out_civicrm_managed(&$entities)
{
    _easy_opt_out_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function easy_opt_out_civicrm_caseTypes(&$caseTypes)
{
    _easy_opt_out_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function easy_opt_out_civicrm_angularModules(&$angularModules)
{
    _easy_opt_out_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function easy_opt_out_civicrm_alterSettingsFolders(&$metaDataFolders = null)
{
    _easy_opt_out_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function easy_opt_out_civicrm_entityTypes(&$entityTypes)
{
    _easy_opt_out_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function easy_opt_out_civicrm_themes(&$themes)
{
    _easy_opt_out_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function easy_opt_out_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function easy_opt_out_civicrm_navigationMenu(&$menu) {
//  _easy_opt_out_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _easy_opt_out_civix_navigationMenu($menu);
//}

// The functions below are implemented by us.

/**
 * Implements hook_civicrm_tokens().
 */
function easy_opt_out_civicrm_tokens(&$tokens)
{
    $tokens['EasyOptOut'] = [
        'EasyOptOut.user_opt_out_link' => E::ts('Opt out from Bulk Mailing'),
    ];
}

/**
 * implementation of hook_civicrm_container
 */
function easy_opt_out_civicrm_container($container)
{
    $container->addResource(new \Symfony\Component\Config\Resource\FileResource(__FILE__));
    $container->findDefinition('dispatcher')->addMethodCall(
        'addListener',
        ['civi.token.eval', 'easy_opt_out_evaluate_tokens']
    );
}

function easy_opt_out_evaluate_tokens(\Civi\Token\Event\TokenValueEvent $e)
{
    foreach ($e->getRows() as $row) {
        $urlParams = [
            'reset' => 1,
            'cid' => $row->context['contactId'],
            'cs' => CRM_Contact_BAO_Contact_Utils::generateChecksum($row->context['contactId']),
        ];
        $url = CRM_Utils_System::url('civicrm/eoo/user-email/opt-out', $urlParams, true, null, true, true);
        $row->format('text/html');
        $row->tokens('EasyOptOut', 'user_opt_out_link', ts("<a href='%1' target='_blank'>Opt Out</a><div>%2</div>", [
            1 => $url,
            2 => var_export($row, true),
        ]));
    }
}
