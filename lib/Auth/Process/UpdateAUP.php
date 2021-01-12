<?php

/**
 * Authproc filter for informing the user for updated AUP(s) and asking them to re-accept them
 * in order to proceed
 *
 * Example configuration:
 *
 *    'authproc' => array(
 *       ...
 *       '82' => array(
 *            'class' => 'aup:UpdateAUP',
 *            'aupApiEndpoint' => '',
 *            'aupListEndpoint' => '',
 *            'apiUsername' => '',
 *            'apiPassword' => '',
 *            'userIdAttribute' => '',
 *            'spBlacklist' => array(),
 *            'userIdBlacklist' => array()
 *       ),
 *
 * @author Nick Mastoris <nmastoris@admin.grnet.gr>
 */
class sspmod_aup_Auth_Process_UpdateAUP extends SimpleSAML_Auth_ProcessingFilter
{

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        $params = array(
            'aupApiEndpoint',
            'aupListEndpoint',
            'apiUsername',
            'apiPassword'
        );
        foreach ($params as $param) {
            if (!array_key_exists($param, $config)) {
                throw new SimpleSAML_Error_Exception(
                    'Missing required configuration parameter: ' .$param);
            }
            $this->config[$param] = $config[$param];
        }

        $optionals = array(
            'userIdAttribute',
            'spBlacklist',
            'userIdBlacklist'
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
            SimpleSAML_Logger::debug("[aup] process: Skipping blacklisted SP ". var_export($state['SPMetadata']['entityid'], true));
            return;
        }
        // Check if user is in blacklist
        if(!empty($this->config['userIdAttribute']) && !empty($this->config['userIdBlacklist']) &&  !empty(array_intersect($state['Attributes'][$this->config['userIdAttribute']], $this->config['userIdBlacklist']))) {
            return;
        }
        try {
            SimpleSAML_Logger::debug('[aup] process: ' . $this->config['userIdAttribute'] . ' ' . var_export($state['Attributes'][$this->config['userIdAttribute']],true));
            // Check if there are updated aup(s)
            $changed_aups = array();

            foreach ($state['rciamAttributes']['aup'] as $aup) {
                if (!empty($aup['agreed']) && $aup['version'] != $aup['agreed']['version']) {
                    $changed_aups[] = $aup;
                }
            }
            if (!empty($changed_aups) && !empty($state["rciamAttributes"]) && !empty($state["rciamAttributes"]["registryUserId"])) {
                    $state['aup:changedAups'] = $changed_aups;
                    $state['aup:aupListEndpoint'] = str_replace("%registryUserId%", $state["rciamAttributes"]["registryUserId"], $this->config['aupListEndpoint']);
                    $state['aup:aupApiEndpoint'] = $this->config['aupApiEndpoint'];
                    $state['aup:apiUsername'] = $this->config['apiUsername'];
                    $state['aup:apiPassword'] = $this->config['apiPassword'];

                    $id = SimpleSAML_Auth_State::saveState($state, 'aup_state');
                    $url = SimpleSAML_Module::getModuleURL('aup/aup_in_form.php');
                    \SimpleSAML\Utils\HTTP::redirectTrustedURL($url, array('StateId' => $id));
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
        $globalConfig = SimpleSAML_Configuration::getInstance();
        $t = new SimpleSAML_XHTML_Template($globalConfig, 'aup:exception.tpl.php');
        $t->data['e'] = $e->getMessage();
        $t->show();
        exit();
    }
}
