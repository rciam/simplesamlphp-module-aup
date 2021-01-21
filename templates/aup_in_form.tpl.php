<?php

/**
 * Template form for attribute selection.
 *
 * Parameters:
 * - 'yesTarget': Target URL for the yes-button. This URL will receive a POST request.
 * - 'yesData': Parameters which should be included in the yes-request.
 * - 'changedAups': AUPs that have been updated.
 * - 'aupListEndpoint': AUP List Endpoint showing all AUPs for user.
 *
 * @package SimpleSAMLphp
 */
assert('is_string($this->data["yesTarget"])');
assert('is_array($this->data["yesData"])');
assert('is_array($this->data["changedAups"])');

// Parse parameters
if (array_key_exists('changedAups', $this->data)) {
  $aups = $this->data['changedAups'];
}
if (array_key_exists('aupListEndpoint', $this->data)) {
  $aupListEndpoint = $this->data['aupListEndpoint'];
}

// Get Configuration and set the loader
$globalConfig = SimpleSAML_Configuration::getInstance();
$theme_use = $globalConfig->getString('theme.use', 'default');
if ($theme_use !== 'default') {
    $theme_config_file = 'module_' . explode(':', $theme_use)[0] . '.php';
    $themeConfig       = SimpleSAML_Configuration::getConfig($theme_config_file);
    $loader = $themeConfig->getValue('loader');
    if (!empty($loader)) {
        $this->includeAtTemplateBase('includes/' . $loader . '.php');
    }
}

$this->data['jquery'] = array('core' => true, 'ui' => true, 'css' => true);
// Configure HTML head
$this->data['head'] = header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");                //Date in the past in order not to keep cache if someone goes back to that page
$this->data['head'] .= header("Cache-Control: no-store, no-cache, must-revalidate");   //HTTP/1.1
// Include custom javascript
$this->data['head'] .= '<script type="text/javascript" src="/proxy/module.php/aup/resources/js/aup.js"></script>';
// Include custom style
$this->data['head'] .= '<link rel="stylesheet" type="text/css" href="resources/css/aup.css" />' . "\n";


$this->includeAtTemplateBase('includes/header.php');
?>

    <h1 class="text-center" style="padding-bottom: 0.8em; font-size: xx-large; font-weight: 700;">
        <?php print $this->t('{aup:aup:updated_aup_notice}'); ?>
    </h1>
    <h2 class="text-center">
        <?php print $this->t('{aup:aup:updated_aup_title}'); ?>
    </h2>
    <div class="text-center" style="font-size:1.2em; margin-top:20px; line-height: 1.6em;">
        <?php print $this->t('{aup:aup:updated_aup_description}'); ?>
    </div>

    <!--  Form that will be sumbitted on Yes -->
    <form style="display: inline; margin: 0px; padding: 0px" action="<?php print htmlspecialchars($this->data['yesTarget']); ?>">

        <div style="font-size: 1em;margin-top:2em;">
              <?php foreach ($aups as $aup): ?>
                  <div class="row aup_rows" style="padding:7px 0px">
                      <div class="col-lg-5 col-lg-offset-2 col-md-offset-2 col-md-5 col-sm-7">
                          <h3>
                              <a class="aup_content"
                                 data-description="<?php print $aup['description']; ?>"
                                 data-url="<?php print $aup['url']; ?>">
                                <?php print $aup['description']; ?>
                              </a>
                          </h3>
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-5">
                          <h3>
                              <input type="checkbox" name="terms_and_conditions_<?php print $aup['id'] ?>"/>
                              <span style="font-size: 0.9em"> I Agree</span>
                          </h3>
                      </div>
                  </div>
              <?php endforeach; ?>
        </div>
        <p style="margin:1em 1em 5em " class="text-center">
            <?php foreach ($this->data['yesData'] as $name => $value): ?>
            <input type="hidden"
                   name="<?php print htmlspecialchars($name); ?>"
                   value="<?php print htmlspecialchars($value); ?>" />
            <?php endforeach; ?>
            <button type="submit" name="yes" disabled="disabled"
                    class="ssp-btn btn ssp-btn__action ssp-btns-container--btn__left text-uppercase" id="yesbutton">
              <?php print htmlspecialchars($this->t('{aup:aup:yes}')) ?>
            </button>
        </p>
    </form>
    <p class="text-center" style="margin-top:20px;margin-bottom:50px">
        <?php print $this->t(
        '{aup:aup:updated_aup_more_information}',
        array('%HERE%' => '<a href="'.$aupListEndpoint.'">here</a>')
      ) ?>
    </p>
<?php
// Include Modal
$this->includeAtTemplateBase('includes/modal.php');
// Include Footer
$this->includeAtTemplateBase('includes/footer.php');
