$(function() {
  $(".aup_content").on("click", function(){
    $("#themeModal .modal-header").html("<h2>"+$(this).data("description")+"</h2>");
    $("#themeModal .modal-body").html("<iframe id=\"aups_panel\" frameBorder=\"0\" src=\'"+$(this).data("url")+"\'></iframe>");
    $("#themeModal").modal("show");
  });

  $("input[name^='terms_and_conditions_']").on("click", function(){
    all_enabled = true;
    $("input[name^='terms_and_conditions_']").each(function(){
      if(!$(this).is(':checked')) {
        all_enabled = false;
        return;
      }
    });
    if (all_enabled === true) {
      $("button[name='yes']").removeAttr("disabled");
    }
    else {
      $("button[name='yes']").attr("disabled","disabled");
    }
  });
});