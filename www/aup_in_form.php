<?php
/**
 * User Inform Form
 *
 * This script displays a page to the user, which requests that the user
 * authorizes the release of attributes.
 *
 * @package SimpleSAMLphp
 */
/**
 * Explicit instruct attribute selection page to send no-cache header to browsers to make
 * sure the users attribute information are not store on client disk.
 *
 * In an vanilla apache-php installation is the php variables set to:
 *
 * session.cache_limiter = nocache
 *
 * so this is just to make sure.
 */
session_cache_limiter('nocache');
$globalConfig = SimpleSAML_Configuration::getInstance();
if (!array_key_exists('StateId', $_REQUEST)) {
  throw new SimpleSAML_Error_BadRequest(
    'Missing required StateId query parameter.'
  );
}
$id = $_REQUEST['StateId'];
/* Restore state */
$state = SimpleSAML_Auth_State::loadState($id, 'aup_state');

// Get the spEntityId for the privacy policy section
if (array_key_exists('core:SP', $state)) {
  $spentityid = $state['core:SP'];
} else if (array_key_exists('saml:sp:State', $state)) {
  $spentityid = $state['saml:sp:State']['core:SP'];
} else {
  $spentityid = 'UNKNOWN';
}
/*
// The user has pressed the yes-button
if ( array_key_exists('yes', $_REQUEST) || array_key_exists('no', $_REQUEST) ) {
  // Remove the fields that we do not want any more
  if (array_key_exists('attrauthgocdb:error_msg', $state)) {
    unset($state['attrauthgocdb:error_msg']);
  }
}
*/
// The user has pressed the yes-button
// The resumeProcessing function needs a ReturnUrl or a ReturnCall in order to proceed
if (array_key_exists('yes', $_REQUEST)) {
  if (array_key_exists('aup:changed_aups', $state)) {
    unset($state['aup:changed_aups']);
  }
  if (array_key_exists('aup:aupEndpoint', $state)) {
    unset($state['aup:aupEndpoint']);
  }
    SimpleSAML_Auth_ProcessingChain::resumeProcessing($state);
}


////////////// End of handling users choice
///
///

// Make, populate and layout informed failure consent form
$t = new SimpleSAML_XHTML_Template($globalConfig, 'aup:aup_in_form.tpl.php');
$t->data['srcMetadata'] = $state['Source'];
$t->data['dstMetadata'] = $state['Destination'];
$t->data['yesTarget'] = SimpleSAML_Module::getModuleURL('aup/aup_in_form.php');
$t->data['yesData'] = array('StateId' => $id);
$t->data['changedAups'] = $state['aup:changedAups'];
$t->data['aupEndpoint'] = $state['aup:aupEndpoint'];
// Fetch privacypolicy
if (array_key_exists('privacypolicy', $state['Destination'])) {
  $privacypolicy = $state['Destination']['privacypolicy'];
} elseif (array_key_exists('privacypolicy', $state['Source'])) {
  $privacypolicy = $state['Source']['privacypolicy'];
} else {
  $privacypolicy = false;
}
if ($privacypolicy !== false) {
  $privacypolicy = str_replace(
    '%SPENTITYID%',
    urlencode($spentityid),
    $privacypolicy
  );
}
$t->data['sppp'] = $privacypolicy;

$t->show();
