<?php

/**
 * Authproc filter for informing the user for updated AUP(s) and asking them to re-accept them
 * in order to proceed
 *
 * Example configuration:
 *
 *    'authproc' => [
 *       ...
 *       '82' => [
 *            'class' => 'aup:UpdateAUP',
 *            'aupApiEndpoint' => '',
 *            'aupListEndpoint' => '',
 *            'apiUsername' => '',
 *            'apiPassword' => '',
 *            'apiTimeout' => 15,
 *            'userIdAttribute' => '',
 *            'spBlacklist' => [],
 *            'userIdBlacklist' => []
 *       ],
 *
 * @author Nick Mastoris <nmastoris@admin.grnet.gr>
 */
namespace SimpleSAML\Module\aup\Auth\Process;

use SimpleSAML\Auth\State;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Error;

class UpdateAUP extends \SimpleSAML\Auth\ProcessingFilter
{

    public function __construct($config, $reserved)
    {
        // Default value for api timeout
        $this->config['apiTimeout'] = 15;

        parent::__construct($config, $reserved);
        $params = array(
            'aupApiEndpoint',
            'aupListEndpoint',
            'apiUsername',
            'apiPassword'
        );
        foreach ($params as $param) {
            if (!array_key_exists($param, $config)) {
                throw new Error\Exception(
                    'Missing required configuration parameter: ' .$param);
            }
            $this->config[$param] = $config[$param];
        }

        $optionals = array(
            'userIdAttribute',
            'spBlacklist',
            'userIdBlacklist',
            'apiTimeout'
        );
        foreach ($optionals as $optional) {
            if(!empty($config[$optional])) {
                $this->config[$optional] = $config[$optional];
            }
        }
    }

    /**
     * @param array $state
     */
    public function process(&$state)
    {
        if (!empty($this->config['spBlacklist']) && isset($state['SPMetadata']['entityid']) && in_array($state['SPMetadata']['entityid'], $this->config['spBlacklist'], true)) {
            Logger::debug("[aup:UpdateAUP] Skipping blacklisted SP ". var_export($state['SPMetadata']['entityid'], true));
            return;
        }
        // Check if user is in blacklist
        if ((!empty($this->config['userIdAttribute']) && !empty($state['Attributes'][$this->config['userIdAttribute']]) && !empty($this->config['userIdBlacklist']) && !empty(array_intersect(
            $state['Attributes'][$this->config['userIdAttribute']],
            $this->config['userIdBlacklist'])))) {
            Logger::debug("[aup:UpdateAUP] Skipping blacklisted user with id " . var_export($state['Attributes'][$this->config['userIdAttribute']], true));
            return;
        }
        // Check if $state['rciamAttributes']['aup'] is empty
        if (empty($state['rciamAttributes']['aup'])) {
            Logger::debug("[aup:UpdateAUP] No AUP information found in state - skipping");
            return;
        }
        try {
            // Check if there are updated aup(s)
            $changed_aups = array();

            foreach ($state['rciamAttributes']['aup'] as $aup) {
                if (empty($aup['agreed']) || $aup['version'] != $aup['agreed']['version']) {
                    $changed_aups[] = $aup;
                }
            }
            if (!empty($changed_aups) && !empty($state["rciamAttributes"]) && !empty($state["rciamAttributes"]["registryUserId"])) {
                    $state['aup:changedAups'] = $changed_aups;
                    $state['aup:aupListEndpoint'] = str_replace("%registryUserId%", $state["rciamAttributes"]["registryUserId"], $this->config['aupListEndpoint']);
                    $state['aup:aupApiEndpoint'] = $this->config['aupApiEndpoint'];
                    $state['aup:apiUsername'] = $this->config['apiUsername'];
                    $state['aup:apiPassword'] = $this->config['apiPassword'];
                    $state['aup:apiTimeout'] = $this->config['apiTimeout'];

                    $id = State::saveState($state, 'aup_state');
                    $url = Module::getModuleURL('aup/aup_in_form.php');
                    HTTP::redirectTrustedURL($url, array('StateId' => $id));
          }
          return;
        } catch (\Exception $e) {
            $this->showException($e);
        }

    }

    /**
      * @param $e
      * @throws Exception
      */
    private function showException($e)
    {
        $globalConfig = SimpleSAML\Configuration::getInstance();
        $t = new SimpleSAML\XHTML\Template($globalConfig, 'aup:exception.tpl.php');
        $t->data['e'] = $e->getMessage();
        $t->show();
        exit();
    }
}
