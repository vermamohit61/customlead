/*
* @file expertblock.js
* Contains all functionality related to contact validation
*/
(function (Drupal, $) {
  'use strict';

  Drupal.behaviors.expertblock = {
    attach: function (context, settings) {

    }
  };

})(Drupal, jQuery);

(function ($, Drupal) {
  $(document).ready(function () {
    $('.parentenquiry-form .checkgtm').css("display", "none");
    $('.parentenquiry-form .checkgtmgrade').css("display", "none");
  });
})(jQuery, Drupal);


(function ($) {
  $(document).ready(function () {


    $(".parentenquiry-subinfo").attr("href", "javascript:void(0)");
    // Jquery ajax to save expert lead info
    $("body").on("click", ".parentenquiry-subinfo", function () {

      var pname = $.trim($("input[name=pname]").val());
      var pmobile = $.trim($("input[name=pmobile]").val());
      var userid = $.trim($("input[name=userid]").val());
      if (userid == '0' || userid == '') {
        var cname = $.trim($("#edit-cname").val());
      } else {
        var vals = [];
        $('.checkch:checked').each(function () { //could also use .map here
          vals.push($(this).val());
        });
        var cname = vals.join(',');
      }
     //for GTM
      if (userid == '0' || userid == '') {
        var gtm_cname = $.trim($("#edit-cname").val());
        var gtm_gradename = $.trim($("#edit-gradeapplying").val());
      } else {
        //for GTM child name data
        var gtmvals = [];
        $('.checkch:checked').each(function() {
          gtmvals.push($(this).next().text());
        });
        var gtm_cname = gtmvals.join(',');
        //for GTM grade name data
        var gtmgradevals = [];
        $('.checkgtmgrade:checked').each(function() {
          gtmgradevals.push($(this).val());
        });
        var gtm_gradename = gtmgradevals.join(',');
      }
      var grade = $.trim($("#edit-gradeapplying").val());
      if (userid == '0' || userid == '') {
      var potp = $.trim($("input[name=potp]").val());
      }
      var appid = $.trim($("input[name=appid]").val());
      var cdnmoengage = $.trim($("input[name=cdnmoengage]").val());
      var moengagedebug = $.trim($("input[name=moengage_debug]").val());
      var terms = $('#edit-exptermscondition').is(":checked") ? 1 : 0;
      var schoolid = $.trim($("input[name=schoolid]").val());
      var nodepath = $.trim($("input[name=nodepath]").val());
      var utm_source = $.trim($("input[name=utm_source]").val());
      var utm_medium = $.trim($("input[name=utm_medium]").val());
      var utm_campaign = $.trim($("input[name=utm_campaign]").val());
      var utm_term = $.trim($("input[name=utm_term]").val());
      var utm_content = $.trim($("input[name=utm_content]").val());
      var gclid = $.trim($("input[name=gclid]").val());
      var fbclid = $.trim($("input[name=fbclid]").val());
      var b2b_lead = $.trim($("input[name=b2b_lead]").val());
      var post_data = "&pname=" + pname + "&pmobile=" + pmobile + "&potp=" + potp + "&cname=" + cname + "&grade=" + grade + "&terms=" + terms + "&schoolid=" + schoolid + "&utm_source=" + utm_source + "&utm_campaign=" + utm_medium + "&utm_campaign=" + utm_campaign + "&utm_term=" + utm_term + "&utm_content=" + utm_content + "&gclid=" + gclid + "&fbclid=" + fbclid + "&appid=" + appid + "&cdnmoengage=" + cdnmoengage + "&userid=" + userid + "&b2b_lead=" + b2b_lead + "&moengagedebug=" + moengagedebug;
      var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
      var expert_flag = true;
      // initial blank error message
      $('.pname_error_msg').html('');
      $('.pmobile_error_msg').html('');
      $('.potp_error_msg').html('');
      $('.cname_error_msg').html('');
      $('.grade_error_msg').html('');
      // Add valid class in text field
      $("input[name=pname]").removeClass('invalid').addClass('valid');
      $("input[name=pmobile]").removeClass('invalid').addClass('valid');
      $("input[name=potp]").removeClass('invalid').addClass('valid');
      $("#edit-cname").removeClass('invalid').addClass('valid');
      $("#edit-gradeapplying").removeClass('invalid').addClass('valid');

      if (pname == '') {
        $('.pname_error_msg').html('Please Provide Parent Name !');
        $("input[name=pname]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }

      if (pmobile.length != 10) {
        $('.pmobile_error_msg').html('Please Provide Valid Mobile Number !');
        $("input[name=pmobile]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if (userid == '0' || userid == '') {
      if (potp == '') {
        $('.potp_error_msg').html('Please Enter OTP !');
        $("input[name=potp]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
    }
      if (userid == '0' || userid == '') {
        if (grade == '') {
          $('.grade_error_msg').html('Please Select Grade !');
          $("#edit-gradeapplying").removeClass('valid').addClass('invalid');
          expert_flag = false;
        }
        if (cname == '') {
          $('.cname_error_msg').html('Please Enter Child Name !');
          $("#edit-cname").removeClass('valid').addClass('invalid');
          expert_flag = false;
        }
      } else {
        if ($('.checkch:checkbox').filter(':checked').length < 1) {
          $('.cname_error_msg').html('Please select at least one child.');
          $(".checkch").removeClass('valid').addClass('invalid');
          expert_flag = false;
        }
      }

      if (expert_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "getenquirysave",
          data: post_data,
          cache: false,
          beforeSend: function () {
            $('.loader-wrapper').css('display', 'block');
          },
          success: function (data) {
            $('.loader-wrapper').css('display', 'none');
            console.log(data.expert_lead_status);
            if (data.expert_lead_status == 'success') {
                //datalayer for School page events B2B Lead submitted
                var dataLayer = window.dataLayer || [];
                dataLayer.push({
                  'event': b2b_lead,
                  'B2BLeadsData': {
                    'name': pname,
                    'mobile': pmobile,
                    'grade applying for': gtm_gradename,
                    'student name': gtm_cname,
                    'utm source': utm_source,
                    'utm medium': utm_medium,
                    'utm campaign': utm_campaign,
                    'utm term': utm_term,
                    'utm content': utm_content,
                    'terms': terms
                  }
                });
                //moengage tracking for speak to school
                (function(i,s,o,g,r,a,m,n){i.moengage_object=r;t={};q=function(f){return function(){(i.moengage_q=i.moengage_q||[]).push({f:f,a:arguments})}};f=['track_event','add_user_attribute','add_first_name','add_last_name','add_email','add_mobile','add_user_name','add_gender','add_birthday','destroy_session','add_unique_user_id','moe_events','call_web_push','track','location_type_attribute'],h={onsite:["getData","registerCallback"]};for(k in f){t[f[k]]=q(f[k])}for(k in h)for(l in h[k]){null==t[k]&&(t[k]={}),t[k][h[k][l]]=q(k+"."+h[k][l])}a=s.createElement(o);m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m);i.moe=i.moe||function(){n=arguments[0];return t};a.onload=function(){if(n){i[r]=moe(n)}}})(window,document,'script',cdnmoengage,'Moengage');
                Moengage = moe({
                  app_id: appid,
                  debug_logs: moengagedebug
                });
                Moengage.track_event(b2b_lead, {"B2B Lead Category": "Lead School", "moe_non_interactive": 1});
                Moengage.add_user_attribute("Speak to school Form");
                Moengage.add_first_name(pname);
                Moengage.add_mobile(pmobile);
                Moengage.add_unique_user_id(pmobile);
                Moengage.add_user_attribute("grade applying for", gtm_gradename);
                Moengage.add_user_attribute("student name", gtm_cname);
                Moengage.add_user_attribute("Is otp verified", "True");
                Moengage.add_user_attribute("user utm source", utm_source);
                Moengage.add_user_attribute("user utm medium", utm_medium);
                Moengage.add_user_attribute("user utm campaign", utm_campaign);
                Moengage.add_user_attribute("user utm term", utm_term);
                Moengage.add_user_attribute("user utm content", utm_content);
                Moengage.add_user_attribute("whatsapp opt in", terms);
              // End Moengage tracking system code

                $('.messclass').show();
                $('.form-popup').fadeOut();


              //$('.register_result_message').html('Submitted Successfully !');

              //location.href = drupalSettings.path.baseUrl + "schools";
              $('#parentenquiry-form')[0].reset();
            } else if (data.expert_lead_status == 'otp_invalid') {
              $('.potp_error_msg').html('Invalid OTP');
              $("input[name=potp]").removeClass('valid').addClass('invalid');
            } else if (data.expert_lead_status == 'error') {
              $('.pmobile_error_msg').html('This Mobile number is already registed with lead school');
            }

            else {
              console.log('Something Went Wrong');
            }

          },
          error: function (error) {

          }
        });
      }
    });

    // Allow only digits in mobile number field
    $("#edit-pmobile, input[name=appmobile], #edit-plotp, #edit-plmobile").keyup(function () {
      var $this = $(this);
      $this.val($this.val().replace(/[^\d.]/g, ''));
    });

    $('#edit-exname').bind('paste', function () {
      var self = this;
      setTimeout(function () {
        if (!/^[a-zA-Z]+$/.test($(self).val()))
          $(self).val('');
      }, 0);
    });

    $("#edit-pname").keypress(function (e) {
      var firstChar = $("#edit-pname").val()
      var key = e.keyCode;
      var regex = new RegExp("^[a-zA-Z]+$");
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (regex.test(str) || key == 32) {
        if (e.keyCode == 32) {
          var cursorPos = $("#edit-pname").prop("selectionStart");
          //Prevents spaces in Beginning and more than on at the end
          if (cursorPos == 0 || (cursorPos == firstChar.length && firstChar[firstChar.length - 1] == " ")) {
            e.preventDefault();
          }
        }
        return true;
      } else {
        e.preventDefault();
        return false;
      }
    });

  });

})(jQuery);
