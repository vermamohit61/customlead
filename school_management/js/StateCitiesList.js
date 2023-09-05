/*
 * @file classroom_common.js
 * Contains js functionality related to classroom
 */
(function (Drupal, $) {

  Drupal.behaviors.school_management = {
    attach: function (context, settings) {

      // Code to change the dropdown select city in school listing
      $("body").on("change", "form[id*='views-exposed-form-school-listing-page-1'] select[name='state']", function () {
        var state_id = $("form[id*='views-exposed-form-school-listing-page-1'] select[name='state']").find(':selected').attr('data-id');
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "getcitylist/" + state_id,
          dataType: 'json',
          cache: false,
          beforeSend: function () {
          },
          success: function (data) {
            if (data) {
              // empty contents of centers dropdown
              $("select[name='city']").html("");
              $("select[name='city']").append("<option value=''>Select City</option>");
              // put new dropdown values to city dropdown
              jQuery.each(data, function (key, val) {
              $('select[name="city"]').append('<option value="' + key + '">' + val + '</option>');
              });
            }
          },
          error: function (error) {
          }
        });
      });

    }
};
})(Drupal, jQuery);

(function ($, Drupal) {
  $(document).ready(function () {
    $('#views-exposed-form-school-listing-page-1 .form-item-search').css("display", "none");
    $('#views-exposed-form-school-listing-page-1 .form-item-grade-available').css("display", "none");
  });
})(jQuery, Drupal);

(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.school_managementBehavior = {
    attach: function (context, settings) {
      // get value with "drupalSettings.school_management.variables"
      var state = drupalSettings.school_management.state;
      if(state != 1){
        $('#views-exposed-form-school-listing-page-1 .form-item-state').css("display", "none");
      }
      var city = drupalSettings.school_management.city;
      if(city != 1){
        $('#views-exposed-form-school-listing-page-1 .form-item-city').css("display", "none");
      }
      var board_affiliation = drupalSettings.school_management.board_affiliation;
      if(board_affiliation != 1){
        $('#views-exposed-form-school-listing-page-1 .form-item-board').css("display", "none");
      }
      var pincode = drupalSettings.school_management.pincode;
      if(pincode != 1){
        $('#views-exposed-form-school-listing-page-1 .form-item-pincode').css("display", "none");
      }
      var classes = drupalSettings.school_management.classes;
      if(classes != 1){
        $('#views-exposed-form-school-listing-page-1 .form-item-grade').css("display", "none");
      }
      var infrastructure = drupalSettings.school_management.infrastructure;
      if(infrastructure != 1){
        $('#views-exposed-form-school-listing-page-1 .form-item-facility').css("display", "none");
      }
    }
  };
  $("#edit-pincode").keyup(function (){
    var $this = $(this);
    $this.val($this.val().replace(/[^\d.]/g, ''));
  });
})(jQuery, Drupal, drupalSettings);

