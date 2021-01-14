
<?php
/**
 * Review Updated AUP Form
 *
 * This script displays a page to the user, which requests that the user
 * should review the updated AUPs.
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
$globalConfig = SimpleSAML\Configuration::getInstance();
if (!array_key_exists('StateId', $_REQUEST)) {
    throw new SimpleSAML\Error\BadRequest(
      'Missing required StateId query parameter.'
    );
}
$id = $_REQUEST['StateId'];
/* Restore state */
$state = SimpleSAML\Auth\State::loadState($id, 'aup_state');

// The user has pressed the yes-button
// The resumeProcessing function needs a ReturnUrl or a ReturnCall in order to proceed
if (array_key_exists('yes', $_REQUEST)) {
    SimpleSAML\Logger::debug("[aup] REQUEST". var_export($_REQUEST, true));
    $url = $state['aup:aupApiEndpoint'];
    foreach($state['aup:changedAups'] as $aup) {
        SimpleSAML\Logger::debug("[aup] Changed AUPS:". $aup['id']);
        SimpleSAML\Logger::debug("[aup] User Id:".   $state["rciamAttributes"]["registryUserId"]);

        if(!empty($_REQUEST['terms_and_conditions_'.$aup['id']])){
            // Make the post requests
            addCoTAndCAgreement($state["rciamAttributes"]["registryUserId"], $aup['id'], $url, $state['aup:apiUsername'], $state['aup:apiPassword']);
        }
    }
    if (array_key_exists('aup:changedAups', $state)) {
        unset($state['aup:changedAups']);
    }
    if (array_key_exists('aup:aupListEndpoint', $state)) {
        unset($state['aup:aupListEndpoint']);
    }
    if (array_key_exists('aup:aupApiEndpoint', $state)) {
        unset($state['aup:aupApiEndpoint']);
    }
    if (array_key_exists('aup:apiUsername', $state)) {
        unset($state['aup:aupUsername']);
    }
    if (array_key_exists('aup:apiPassword', $state)) {
        unset($state['aup:aupPassword']);
    }
    SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);
}

// Make, populate and layout informed failure consent form
$t = new SimpleSAML\XHTML\Template($globalConfig, 'aup:aup_in_form.tpl.php');
$t->data['yesTarget'] = SimpleSAML\Module::getModuleURL('aup/aup_in_form.php');
$t->data['yesData'] = array('StateId' => $id);
$t->data['changedAups'] = $state['aup:changedAups'];
$t->data['aupListEndpoint'] = $state['aup:aupListEndpoint'];
$t->show();

function addCoTAndCAgreement($coPersonId, $coTAndCId, $url, $apiUser, $apiPass)
{
    // Construct my data
    $reqDataArr = array();
    $reqDataArr['RequestType'] = 'CoTAndCAgreements';
    $reqDataArr['Version'] = '1.0';
    $reqDataArr['CoTAndCAgreements'][0]['Version'] = '1.0';
    $reqDataArr['CoTAndCAgreements'][0]['CoTermsAndConditionsId'] = (string) ($coTAndCId);
    $reqDataArr['CoTAndCAgreements'][0]['Person']['Type'] = 'CO';
    $reqDataArr['CoTAndCAgreements'][0]['Person']['Id'] = $coPersonId;
    $req = json_encode($reqDataArr);
    $res = http('POST', $url, $req, $apiUser, $apiPass);
    return $res;
}

function http($method, $url, $data = null, $apiUser, $apiPass)
{

    $ch = curl_init($url);
    curl_setopt_array(
        $ch,
        array(
          CURLOPT_CUSTOMREQUEST => $method,
          CURLOPT_CONNECTTIMEOUT => 5,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_USERPWD => $apiUser . ":" . $apiPass,
        )
    );
    if (($method == "POST" || $method == "PUT") && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                         'Content-Type: application/json',
                         'Content-Length: ' . strlen($data))
        );
    }

    // Send the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for error
    if ($http_code !== 200 && $http_code !== 201 && $http_code !== 204 && $http_code !== 302 && $http_code !== 404) {
        SimpleSAML\Logger::error("[aup] save acceptance of aup failed. http: method=" // TODO error logging
        . var_export($method, true) . ", url=" . var_export($url, true)
        . ", data=" . var_export($data, true)
        . ": API call failed: HTTP response code: "
        . var_export($http_code, true) . ", error message: '"
        . var_export(curl_error($ch), true) . "'\n");
    }
    // Close session
    curl_close($ch);
    $result = json_decode($response);
    SimpleSAML\Logger::debug("[aup] api call for renew AUP http: result="
        . var_export($result, true));
    assert('json_last_error()===JSON_ERROR_NONE');
    return $result;
}