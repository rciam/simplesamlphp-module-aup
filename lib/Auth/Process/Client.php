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
 *            class' => 'aup:Client',
 *            'aupApiEndpoint' => 'https://comanage.example.org/registry/co_t_and_c_agreements/add.json',
 *            'aupListEndpoint' => 'https://comanage.example.org/registry/co_terms_and_conditions/review/copersonid:%registryUserId%',
 *            'apiUsername' => 'bob',
 *            'apiPassword' => 'secret',
 *            'spBlacklist' => array(),
 *       ),
 *
 * @author Nick Mastoris <nmastoris@admin.grnet.gr>
 */
class sspmod_aup_Auth_Process_Client extends SimpleSAML_Auth_ProcessingFilter
{
    // List of SP entity IDs that should be excluded from this filter.
    private $spBlacklist = array();

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        $params = array(
            'aupApiEndpoint',
            'aupListEndpoint',
            'apiUsername',
            'apiPassword',         
        );
        foreach ($params as $param) {
            if (!array_key_exists($param, $config)) {
                throw new SimpleSAML_Error_Exception(
                    'Missing required configuration parameter: ' .$param);
            }
            $this->config[$param] = $config[$param];
        }

        $optionals = array(
            'spBlacklist'
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
        try {
            SimpleSAML_Logger::info('[aup] process: eduPersonUniqueId'. var_export($state['Attributes']['eduPersonUniqueId'],true));
            // Check if we have an updated aup
            $changed_aups = array();

            foreach ($state['rciamAttributes']['aup'] as $aup) {
                if ($aup['version'] != $aup['agreed']['version']) {
                    $changed_aups[] = $aup;
                }
            }
            if (!empty($changed_aups)) {
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
