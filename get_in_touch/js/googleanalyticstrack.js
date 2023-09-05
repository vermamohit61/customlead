/*
 * @file expertblock.js
 * Contains all functionality related to contact validation
 */
(function (Drupal) {
  'use strict';
  Drupal.behaviors.get_in_touch = {
    attach: function () {

    }
 };
})(Drupal, jQuery);
(function ($) {
  $(document).ready(function () {
    $(".expert-subinfo").attr("href", "javascript:void(0)");
    // Jquery ajax to save expert lead info
    $("body").on("click", ".expert-subinfo", function () {
      var exname = $.trim($("input[name=exname]").val());
      var exlname = $.trim($("input[name=exlname]").val());
      var exmail = $.trim($("input[name=exmail]").val());
      var exmobile = $.trim($("input[name=exmobile]").val());
      var subscribe_event = $.trim($("input[name=subscribe_event]").val());
      var exmessage = $.trim($("#edit-exmessage").val());
      var terms = $('#edit-exptermscondition').is(":checked")?1:0;
      var post_data = "&exname=" + exname + "&exlname=" + exlname + "&exmail=" + exmail + "&exmobile=" + exmobile + "&exmessage=" + exmessage + "&terms=" + terms + "&subscribe_event=" + subscribe_event;
      var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
      var expert_flag = true;
      if (exname == '') {
        $('.exname_error_msg').html('Please provide First Name !');
        $("input[name=exname]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if(exmail != '') {
        if (!pattern.test(exmail)) {
          $('.exmail_error_msg').html('Please Provide valid email !');
          $("input[name=exmail]").removeClass('valid').addClass('invalid');
          expert_flag = false;
        }
      }
      if (exmobile.length != 10) {
        $('.exmobile_error_msg').html('Please provide valid mobile number !');
        $("input[name=exmobile]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if (expert_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "save_getintouch",
          data: post_data,
          cache: false,
          beforeSend: function () {
            $('.loader-wrapper').css('display', 'block');
          },
          success: function (data) {
            $('.loader-wrapper').css('display', 'none');
            if (data.expert_lead_status == 'success') {
                 //datalayer for subscriber
              var dataLayer = window.dataLayer || [];
              dataLayer.push({
                'event': subscribe_event,
                    'subscriberData': {
                      'fname': exname,
                      'lname': exlname,
                      'email': exmail,
                      'mobile': exmobile,
                      'message': exmessage,
                      'terms': terms 
                    }                         
              });
             } else {
              console.log('Something Went Wrong');
            }
          },
          error: function () {
          }
        });
      }
    });   
  });
})(jQuery);
