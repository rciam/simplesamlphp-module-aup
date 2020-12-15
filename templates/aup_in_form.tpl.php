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
} elseif (array_key_exists('OrganizationDisplayName', $this->data['srcMetadata'])) {
  $srcName = $this->data['srcMetadata']['OrganizationDisplayName'];
} else {
  $srcName = $this->data['srcMetadata']['entityid'];
}

if (is_array($srcName)) {
  $srcName = $this->t($srcName);
}

if (array_key_exists('name', $this->data['dstMetadata'])) {
  $dstName = $this->data['dstMetadata']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $this->data['dstMetadata'])) {
  $dstName = $this->data['dstMetadata']['OrganizationDisplayName'];
} else {
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

$this->data['jquery'] = array('core' => true, 'ui' => true, 'css' => true);
//$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/' . $this->data['baseurlpath'] . 'module.php/attrauthcomanage/resources/css/style.css" />' . "\n";
$this->data['head'] = '<style type="text/css">
.aup_content{
   cursor: pointer;
}
li:nth-child(odd) {
background: #fafafa; 
}

li:nth-child(even) {
background: #ffffff;
}
html, body {
height:100%;
}
#loader {
height:100vh;
}
</style>
<script type="text/javascript">
$(function() {
    var height = $("#content").height() + $(".header").height();
    console.log(height);
    $("body").prepend("<div id=\"loader\" style=\"height:"+height+"px\"><div class=\"sk-circle\"><div class=\"sk-circle1 sk-child\"></div><div class=\"sk-circle2 sk-child\"></div><div class=\"sk-circle3 sk-child\"></div><div class=\"sk-circle4 sk-child\"></div><div class=\"sk-circle5 sk-child\"></div><div class=\"sk-circle6 sk-child\"></div><div class=\"sk-circle7 sk-child\"></div><div class=\"sk-circle8 sk-child\"></div><div class=\"sk-circle9 sk-child\"></div><div class=\"sk-circle10 sk-child\"></div><div class=\"sk-circle11 sk-child\"></div><div class=\"sk-circle12 sk-child\"></div></div></div>")
    
    $("#yesbutton").on("click", function(){
        $("#loader").show();
    })
    $(".aup_content").on("click", function(){
        $("#exampleModal .modal-header").html("<h2>"+$(this).data("description")+"</h2>")  
        $("#exampleModal .modal-body").html("<iframe id=\"aups_panel\" style=\"width: 100%; height: 70vh; position: relative; top:0px; padding:0px\" frameBorder=\"0\" src=\'"+$(this).data("url")+"\'></iframe>");
        $("#exampleModal").modal("show");
    })
    $("#aups_link").on("click",function(){$(".container.js-spread").css("height", "auto");
    if($("#aups_panel").length!=0){$("#aups_panel").toggle(); return;}
    $("#iframe_container").append("<iframe id=\"aups_panel\" style=\"width: 100%; height: 70vh; position: relative; top:0px; padding:0px\" frameBorder=\"0\" src=\'' . $aupEndpoint . '\'></iframe>");}) })
</script>';

$this->includeAtTemplateBase('includes/header.php');
?>

<p>
  <?php
  print '<h3 class="text-center">' . $this->t('{aup:aup:updated_aup_title}') . '</h3>';
  ?>
</p>

<!--  Form that will be sumbitted on Yes -->
<form style="display: inline; margin: 0px; padding: 0px" action="<?php
print htmlspecialchars($this->data['yesTarget']); ?>">
    <h3 class="text-center" style="margin-top:3em; text-decoration: underline;">List of Updated Acceptable Use
        Policies</h3>
    <div style="font-size: 1em;">
        <ol style="text-align: center; list-style-position: inside; list-style-type: none; padding:0px">
          <?php
          foreach ($aups as $aup): ?>
              <li class="text-center" style="padding:7px 0 7px; margin-top:1em">
                  <h3><a class="aup_content " data-description="<?php
                    print $aup['description']; ?>" data-url="<?php
                    print $aup['url']; ?>"><?php
                      print $aup['description']; ?></a>&nbsp;&nbsp; <input type="checkbox"
                                                                           name="terms_and_conditions_<?php
                                                                           print $aup['id'] ?>"/><span
                              style="font-size: 0.9em"> I Agree</span></h3>
              </li>
          <?php
          endforeach; ?>
        </ol>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12" id="iframe_container"></div>
    </div>
    <p></p>
    <p style="margin:0em 1em 5em " class="text-center">
      <?php
      foreach ($this->data['yesData'] as $name => $value) {
        print '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />';
      }
      ?>
        <button type="submit" name="yes"
                class="ssp-btn btn ssp-btn__action ssp-btns-container--btn__left text-uppercase" id="yesbutton">
          <?php
          print htmlspecialchars($this->t('{aup:aup:yes}')) ?>
        </button>
    </p>
</form>
<p class="text-center" style="margin-top:20px;margin-bottom:50px"><?php
  print $this->t(
    '{aup:aup:updated_aup_description}',
    array('%HERE%' => '<a id="aups_link" onclick="return false;" href="#">here</a>')
  ) ?></p>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>


<?php
/*
if ($this->data['sppp'] !== false) {
  print "<p>" . htmlspecialchars($this->t('{aup:aup:aup_privacy_policy}')) . " ";
  print "<a target='_blank' href='" . htmlspecialchars($this->data['sppp']) . "'>" . $dstName . "</a>";
  print "</p>";
}*/

$this->includeAtTemplateBase('includes/footer.php');
