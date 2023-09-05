/*
* @file stateCitiesList.js
* Contains all functionality related to state city filter in school-listing page
*/
(function (Drupal, $) {
  'use strict';
   Drupal.behaviors.expertblock = {
    attach: function (context, settings) {
    }
  };
})(Drupal, jQuery);
(function ($) {
  function filterdata(){
    var state_id = $('.views-exposed-form #edit-state').find(":selected").val();
    $.ajax({
        url:drupalSettings.path.baseUrl +"getcitylist/" +state_id,
        success: function(data) {
          if(state_id == 'All'){
            $('#edit-city').html('<option value="All"> Select City </option>');
          }else{
            var obj = jQuery.parseJSON(data);
            var city_html = '<option value="All"> Select City </option>';
            $.each(obj, function(key,value) {              
                city_html += '<option value="'+ value.entity_id +'">'+value.name+'</option>'
            });      
            $('#edit-city').html(city_html);
          }
       }
    });
  }
  $("#views-form-school-list-for-admin-page-1 #edit-action").hide();
  $("#views-form-school-list-for-admin-page-1 #edit-submit--2").hide();
  $(document).ready(function () {
    $('#edit-items-selected').on('click',function(){
      if(this.checked){
          $('.form-checkbox').each(function(){
              this.checked = true;
          });
      }else{
           $('.form-checkbox').each(function(){
              this.checked = false;
          });
      }
   });
    filterdata();
    var sclistURL = $(location).attr("href");
    var sclisthref = window.location.href;
    var sclistbreak = sclisthref.split('?')[1];
    if(sclistbreak == undefined){
         $('#edit-city').html('<option value="All"> Select City </option>');
       }else{
        var sclistcity = sclistbreak.split('&city=')[1];
        var sclistcitybreak = sclistcity.split('&')[0];
        if(sclistcitybreak != undefined ){
          setTimeout(function(){ 
            $("#edit-city option[value="+sclistcitybreak+"]").prop("selected",true);
           }, 200);
         }
      }
      $('.views-exposed-form #edit-state').on('change',function(){
        filterdata();
        });
     });
  })(jQuery);