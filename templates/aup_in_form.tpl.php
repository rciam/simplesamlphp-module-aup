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

$this->data['jquery'] = array('core' => true, 'ui' => true, 'css' => true);
$this->data['head'] = header("Expires: Thu, 19 Nov 1981 08:52:00 GMT"). //Date in the past in order not to keep cache if someone goes back to that page
header("Cache-Control: no-store, no-cache, must-revalidate"). //HTTP/1.1 
'<style type="text/css">
.aup_content{
   cursor: pointer;
}
.aup_rows:nth-child(odd) {
    background: #fafafa; 
}

.aup_rows:nth-child(even) {
    background: #ffffff;
}
html, body {
    height:100%;
}
.ssp-btn__action[disabled]:hover {
    background-color: #599adb; 
    color: #fff;
    border-color: transparent;
}
</style>
<script type="text/javascript">

$(function() {
    height = $("#content").height() + $(".header").height();
    $("body").prepend("<div id=\"loader\" style=\"height:"+height+"px\"><div class=\"sk-circle\"><div class=\"sk-circle1 sk-child\"></div><div class=\"sk-circle2 sk-child\"></div><div class=\"sk-circle3 sk-child\"></div><div class=\"sk-circle4 sk-child\"></div><div class=\"sk-circle5 sk-child\"></div><div class=\"sk-circle6 sk-child\"></div><div class=\"sk-circle7 sk-child\"></div><div class=\"sk-circle8 sk-child\"></div><div class=\"sk-circle9 sk-child\"></div><div class=\"sk-circle10 sk-child\"></div><div class=\"sk-circle11 sk-child\"></div><div class=\"sk-circle12 sk-child\"></div></div></div>")
    
    $("#yesbutton").on("click", function(){
        $("#loader").show();
        
    })
    $(".aup_content").on("click", function(){
        $("#aupModal .modal-header").html("<h2>"+$(this).data("description")+"</h2>")  
        $("#aupModal .modal-body").html("<iframe id=\"aups_panel\" style=\"width: 100%; height: 70vh; position: relative; top:0px; padding:0px\" frameBorder=\"0\" src=\'"+$(this).data("url")+"\'></iframe>");
        $("#aupModal").modal("show");
    })
    
    $("input[name^=\'terms_and_conditions_\']").on("click", function(){
        all_enabled = true;
        $("input[name^=\'terms_and_conditions_\']").each(function(){
            if(!$(this).is(\':checked\')) {
                all_enabled = false;
                return;
            }
        })
        if (all_enabled === true) {
            $("button[name=\'yes\']").removeAttr("disabled");
        }
        else {
            $("button[name=\'yes\']").attr("disabled","disabled");
        }
    })
   
})
</script>';

$this->includeAtTemplateBase('includes/header.php');
?>

<?php
print '<h1 class="text-center" style="padding-bottom: 0.8em; font-size: xx-large; font-weight: 700;">' . $this->t('{aup:aup:updated_aup_notice}') . '</h1>';
?>
<?php
print '<h2 class="text-center">' . $this->t('{aup:aup:updated_aup_title}') . '</h2>';
?>

<?php
print '<div class="text-center" style="font-size:1.2em; margin-top:20px; line-height: 1.6em;">' . $this->t(
    '{aup:aup:updated_aup_description}'
  ) . '</div>';
?>

    <!--  Form that will be sumbitted on Yes -->
    <form style="display: inline; margin: 0px; padding: 0px" action="<?php
    print htmlspecialchars($this->data['yesTarget']); ?>">

        <div style="font-size: 1em;margin-top:2em">
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
                              <input type="checkbox" name="terms_and_conditions_<?php
                            print $aup['id'] ?>"/>
                              <span style="font-size: 0.9em"> I Agree</span>
                          </h3>
                      </div>
                  </div>
              <?php endforeach; ?>
        </div>
        <p style="margin:1em 1em 5em " class="text-center">
            <?php
              foreach ($this->data['yesData'] as $name => $value) {
                print '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars(
                    $value
                  ) . '" />';
              }
              ?>
            <button type="submit" name="yes" disabled="disabled"
                    class="ssp-btn btn ssp-btn__action ssp-btns-container--btn__left text-uppercase" id="yesbutton">
              <?php
              print htmlspecialchars($this->t('{aup:aup:yes}')) ?>
            </button>
        </p>
    </form>
    <p class="text-center" style="margin-top:20px;margin-bottom:50px"><?php
      print $this->t(
        '{aup:aup:updated_aup_more_information}',
        array('%HERE%' => '<a href="'.$aupListEndpoint.'">here</a>')
      ) ?>
    </p>
    <!-- Modal -->
    <div class="modal fade" id="aupModal" tabindex="-1" role="dialog" aria-labelledby="aupModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aupModalLabel">Modal title</h5>
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
$this->includeAtTemplateBase('includes/footer.php');
