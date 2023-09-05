/*
* @file stateCitiesList.js
* Contains all functionality related to state city filter in school-listing page
*/
(function (Drupal, $) {
  'use strict';
   Drupal.behaviors.school_management = {
    attach: function (context, settings) {
      setTimeout(function(){
        $(".messages--error").html("Please Select School.").addClass("error-msg"); 
    }, 5);
      $('#views-form-school-list-for-admin-page-1 #school-no-result-found').css("display","none");
      //for admin/marketing-school-listing submit schools hide 
      var noresult = $('.view-empty #school-no-result-found').length;
      if(noresult == 1){
      $('#block-marketingpageblock #edit-submit').css("display","none");
      }       
    }
  };
})(Drupal, jQuery);
//for admin/school-listing filter
(function ($) {
  
  function static_filter(){
    var state_id = $('#views-exposed-form-school-list-for-admin-page-1 #edit-state').find(":selected").val();
    $.ajax({
        url:drupalSettings.path.baseUrl +"admingetcitylist/" +state_id,
        success: function(data) {
          if(state_id == 'All'){
            $('#views-exposed-form-school-list-for-admin-page-1 #edit-city').html('<option value="All"> Select City </option>');
          }else{
            var obj = jQuery.parseJSON(data);
            var city_html = '<option value="All"> Select City </option>';
            $.each(obj, function(key,value) {              
                city_html += '<option value="'+ value.entity_id +'">'+value.name+'</option>'
            });      
            $('#views-exposed-form-school-list-for-admin-page-1 #edit-city').html(city_html);
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
   static_filter();
    var sclistURL = $(location).attr("href");
    var sclisthref = window.location.href;
    var sclistbreak = sclisthref.split('?')[1];
    if(sclistbreak == undefined){
         $('#views-exposed-form-school-list-for-admin-page-1 #edit-city').html('<option value="All"> Select City </option>');
       }else{
        var sclistcity = sclistbreak.split('&city=')[1];
        var sclistcitybreak = sclistcity.split('&')[0];
        if(sclistcitybreak != undefined ){
          setTimeout(function(){ 
            $("#views-exposed-form-school-list-for-admin-page-1 #edit-city option[value="+sclistcitybreak+"]").prop("selected",true);
           }, 1000);
         }
      }
      $('#views-exposed-form-school-list-for-admin-page-1 #edit-state').on('change',function(){
        static_filter();
        });
     });
  })(jQuery);

//for admin/marketing-school-listing filter
  (function ($) {
    function dynamic_filter(){
      var state_id = $('#views-exposed-form-school-list-for-admin-page-2 #edit-state').find(":selected").val();
      $.ajax({
          url:drupalSettings.path.baseUrl +"admingetcitylist/" +state_id,
          success: function(data) {
            if(state_id == 'All'){
              $('#views-exposed-form-school-list-for-admin-page-2 #edit-city').html('<option value="All"> Select City </option>');
            }else{
              var obj = jQuery.parseJSON(data);
              var city_html = '<option value="All"> Select City </option>';
              $.each(obj, function(key,value) {              
                  city_html += '<option value="'+ value.entity_id +'">'+value.name+'</option>'
              });      
              $('#views-exposed-form-school-list-for-admin-page-2 #edit-city').html(city_html);
            }
         }
      });
    }
    
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
     dynamic_filter();
      var sclistURL = $(location).attr("href");
      var sclisthref = window.location.href;
      var sclistbreak = sclisthref.split('?')[1];
      if(sclistbreak == undefined){
           $('#views-exposed-form-school-list-for-admin-page-2 #edit-city').html('<option value="All"> Select City </option>');
         }else{
          var sclistcity = sclistbreak.split('&city=')[1];
          var sclistcitybreak = sclistcity.split('&')[0];
          if(sclistcitybreak != undefined ){
            setTimeout(function(){ 
              $("#views-exposed-form-school-list-for-admin-page-2 #edit-city option[value="+sclistcitybreak+"]").prop("selected",true);
             }, 1000);
           }
        }
        $('#views-exposed-form-school-list-for-admin-page-2 #edit-state').on('change',function(){
          dynamic_filter();
          });
       });
    })(jQuery);