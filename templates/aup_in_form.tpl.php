<?php
/**
 * Template form for attribute selection.
 *
 * Parameters:
 * - 'srcMetadata': Metadata/configuration for the source.
 * - 'dstMetadata': Metadata/configuration for the destination.
 * - 'yesTarget': Target URL for the yes-button. This URL will receive a POST request.
 * - 'yesData': Parameters which should be included in the yes-request.
 * - 'logoutLink': Where to redirect if the user aborts.
 * - 'logoutData': The Data to post to the logout link.
 * - 'sppp': URL to the privacy policy of the destination, or FALSE.
 *
 * @package SimpleSAMLphp
 */
assert('is_array($this->data["srcMetadata"])');
assert('is_array($this->data["dstMetadata"])');
assert('is_string($this->data["yesTarget"])');
assert('is_array($this->data["yesData"])');
assert('is_array($this->data["changedAups"])');
//assert('is_string($this->data["skipLink"])');
//assert('is_array($this->data["skipData"])');

assert('$this->data["sppp"] === false || is_string($this->data["sppp"])');

// Parse parameters

if (array_key_exists('name', $this->data['srcMetadata'])) {
  $srcName = $this->data['srcMetadata']['name'];
}
elseif (array_key_exists('OrganizationDisplayName', $this->data['srcMetadata'])) {
  $srcName = $this->data['srcMetadata']['OrganizationDisplayName'];
}
else {
  $srcName = $this->data['srcMetadata']['entityid'];
}

if (is_array($srcName)) {
  $srcName = $this->t($srcName);
}

if (array_key_exists('name', $this->data['dstMetadata'])) {
  $dstName = $this->data['dstMetadata']['name'];
}
elseif (array_key_exists('OrganizationDisplayName', $this->data['dstMetadata'])) {
  $dstName = $this->data['dstMetadata']['OrganizationDisplayName'];
}
else {
  $dstName = $this->data['dstMetadata']['entityid'];
}

if (is_array($dstName)) {
  $dstName = $this->t($dstName);
}

if (array_key_exists('changedAups', $this->data)) {
  $aups = $this->data['changedAups'];
}
if (array_key_exists('aupEndpoint', $this->data)) {
  $aupEndpoint = $this->data['aupEndpoint'];
}

$this->data['jquery'] = array('core' => TRUE, 'ui' => TRUE, 'css' => TRUE);
//$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/' . $this->data['baseurlpath'] . 'module.php/attrauthcomanage/resources/css/style.css" />' . "\n";
$this->data['head'] = '<script type="text/javascript">
iframe_url = "'.$aupEndpoint.'";
$(function() { $("#aups_link").on("click",function(){$(".container.js-spread").css("height", "auto");
if($("#aups_panel").length!=0){$("#aups_panel").toggle(); return;}
$("#iframe_container").append("<iframe id=\"aups_panel\" style=\"width: 100%; height: 70vh; position: relative; top:0px; padding:0px\" frameBorder=\"0\" src=\''.$aupEndpoint.'\'></iframe>");}) })</script>';
  $this->includeAtTemplateBase('includes/header.php');
?>
    <p>
      <?php
      print '<h3 class="text-center">' . $this->t('{aup:aup:updated_aup_title}') . '</h3>';
      ?>
    </p>

        <!--  Form that will be sumbitted on Yes -->
        <form style="display: inline; margin: 0px; padding: 0px" action="<?php print htmlspecialchars($this->data['yesTarget']); ?>">
            <p style="margin:5em 1em" class="text-center">
              <?php
              foreach($this->data['yesData'] as $name => $value) {
                print '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />';
              }
              ?>
            <button type="submit" name="yes" class="ssp-btn btn ssp-btn__action ssp-btns-container--btn__left text-uppercase" id="yesbutton">
              <?php print htmlspecialchars($this->t('{aup:aup:yes}')) ?>
            </button>
            </p>
        </form>
    <h3 class="text-center" style="text-decoration: underline;">List of Updated Acceptable Use Policies</h3>
    <div style="background-color: #fafafa;font-size: 1em;">
        <ol style="text-align: center; list-style-position: inside; list-style-type: none; padding:0px">
        <?php foreach($aups as $aup):?>
            <li class="text-center" style="padding: 5px; margin-top:1em"><?php print $aup['description']; ?></li>
        <?php endforeach;?>
        </ol>
    </div>
    <p class="text-center" style="margin-top:20px;margin-bottom:50px"><?php print $this->t('{aup:aup:updated_aup_description}', array('%HERE%' => '<a id="aups_link" onclick="return false;" href="#">here</a>')) ?></p>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12" id="iframe_container"></div>
    </div>
    <p></p>
    <?php
/*
if ($this->data['sppp'] !== false) {
  print "<p>" . htmlspecialchars($this->t('{aup:aup:aup_privacy_policy}')) . " ";
  print "<a target='_blank' href='" . htmlspecialchars($this->data['sppp']) . "'>" . $dstName . "</a>";
  print "</p>";
}*/

$this->includeAtTemplateBase('includes/footer.php');
