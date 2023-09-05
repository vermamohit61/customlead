/*
* @file parentloginjs.js
* Contains all functionality related to contact validation
*/
(function (Drupal, $) {
  'use strict';
  Drupal.behaviors.parent_login_registration = {
    attach: function (context, settings) {     
     
    }
  };
})(Drupal, jQuery);
(function ($) {
  //get query string
  function getUrlschool()
    {
      var vars = [], hash;
      var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
      for(var i = 0; i < hashes.length; i++)
      {
          hash = hashes[i].split('=');
          vars.push(hash[0]);
          vars[hash[0]] = hash[1];
      }
      return vars;
    }

    function get_custom_url(){
      var current_url = window.location.href;
      var url_array = current_url.split("dest");
      if(typeof url_array[1] == 'undefined') {
        var url_string = window.location.origin;
      }
      else{
        var url_string = url_array[1].substring(1);
      }
      
      return url_string;
    }
    //end query string
  function moengage_trackdata(cdnmoengage, appid, pname, pmobile, grade, plmobile, plotp, trackevent_type, adduserattribute) {
    //Moengage tracking system code
    (function (i, s, o, g, r, a, m, n) {
      i.moengage_object = r;
      t = {};
      q = function (f) {
        return function () {
          (i.moengage_q = i.moengage_q || []).push({ f: f, a: arguments });
        };
      };
      (f = [
        "track_event",
        "add_user_attribute",
        "add_first_name",
        "add_last_name",
        "add_email",
        "add_mobile",
        "add_user_name",
        "add_gender",
        "add_birthday",
        "destroy_session",
        "add_unique_user_id",
        "moe_events",
        "call_web_push",
        "track",
        "location_type_attribute",
      ]),
        (h = { onsite: ["getData", "registerCallback"] });
      for (k in f) {
        t[f[k]] = q(f[k]);
      }
      for (k in h)
        for (l in h[k]) {
          null == t[k] && (t[k] = {}), (t[k][h[k][l]] = q(k + "." + h[k][l]));
        }
      a = s.createElement(o);
      m = s.getElementsByTagName(o)[0];
      a.async = 1;
      a.src = g;
      m.parentNode.insertBefore(a, m);
      i.moe =
        i.moe ||
        function () {
          n = arguments[0];
          return t;
        };
      a.onload = function () {
        if (n) {
          i[r] = moe(n);
        }
      };
    })(
      window,
      document,
      "script",
      cdnmoengage,
      "Moengage"
    );
    Moengage = moe({
      app_id: appid, // here goes your App Id
      debug_logs: 1,
    });
    Moengage.track_event(trackevent_type, { "NewsCategory": "Politics", "moe_non_interactive": 1 });
    Moengage.add_user_attribute(adduserattribute);
    if (pname != '') {
      Moengage.add_first_name(pname);
    }
    if (pmobile != '') {
      Moengage.add_mobile(pmobile);
    }
    if (pmobile != '' && grade != '') {
      Moengage.add_unique_user_id(pmobile + grade);
    }
    if (plotp != '') {
      Moengage.add_user_attribute(plotp);
    }
    if (plmobile != '') {
      Moengage.add_mobile(plmobile);
    }
    // End Moengage tracking system code
  }
  $(document).ready(function () {
    $(".registration-subinfo").attr("href", "javascript:void(0)");
    // Jquery ajax to save expert lead info
    $("body").on("click", ".registration-subinfo", function () {
      var pname = $.trim($("input[name=pname]").val());
      var pmobile = $.trim($("input[name=pmobile]").val());
      var potp = $.trim($("input[name=potp]").val());
      var cname = $.trim($("input[name=cname]").val());
      var grade = $.trim($("#edit-gradeapplying").val());
      var appid = $.trim($("input[name=appid]").val());
      var cdnmoengage = $.trim($("input[name=cdnmoengage]").val());
      var prsignUp = $.trim($("input[name=prsignUp]").val());
      var terms = $('#edit-exptermscondition').is(":checked") ? 1 : 0;
      var utm_source = $.trim($("input[name=utm_source]").val());
      var utm_medium = $.trim($("input[name=utm_medium]").val());
      var utm_campaign = $.trim($("input[name=utm_campaign]").val());
      var utm_term = $.trim($("input[name=utm_term]").val());
      var utm_content = $.trim($("input[name=utm_content]").val());
      var gclid = $.trim($("input[name=gclid]").val());
      var fbclid = $.trim($("input[name=fbclid]").val());
      var post_data = "&pname=" + pname + "&pmobile=" + pmobile + "&potp=" + potp + "&cname=" + cname + "&grade=" + grade + "&terms=" + terms + "&utm_source=" + utm_source + "&utm_campaign=" + utm_medium + "&utm_campaign=" + utm_campaign + "&utm_term=" + utm_term + "&utm_content=" + utm_content + "&gclid=" + gclid + "&fbclid=" + fbclid + "&appid=" + appid + "&cdnmoengage=" + cdnmoengage + "&prsignUp=" + prsignUp;
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
      $("input[name=cname]").removeClass('invalid').addClass('valid');
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
      if (potp == '') {
        $('.potp_error_msg').html('Please Enter OTP !');
        $("input[name=potp]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if (cname == '') {
        $('.cname_error_msg').html('Please Enter Child Name !');
        $("input[name=cname]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if (grade == '') {
        $('.grade_error_msg').html('Please Select Grade !');
        $("#edit-gradeapplying").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      if (expert_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "getregistersave",
          data: post_data,
          cache: false,
          beforeSend: function () {
            $('.loader-wrapper').css('display', 'block');
          },
          success: function (data) {
            $('.loader-wrapper').css('display', 'none');
            if (data.expert_lead_status == 'success') {
              //datalayer for registration events
              var dataLayer = window.dataLayer || [];
              dataLayer.push({
                'event': prsignUp,
                'signUpData': {
                  'name': pname,
                  'mobile': pmobile,
                  'otp': potp,
                  'student name': cname,
                  'grade applying for': grade
                }
              });
              var trackevent_type = 'Profile Creation';
              var adduserattribute = 'Parents Registration';
              moengage_trackdata(cdnmoengage, appid, pname, pmobile, grade, trackevent_type, adduserattribute);
              $(".register_result_message").addClass("messages--status");
              $('.register_result_message').html('Submitted Successfully !');
              $('#parentregister-form')[0].reset();
              var new_url = get_custom_url();
              /*const d = new Date();
              d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
              let expires = "expires="+d.toUTCString();
              document.cookie = "newly_register=yes;" + expires + ";path=/";*/
              location.href = new_url;
            } else if (data.expert_lead_status == 'error') {
              $('.pmobile_error_msg').html('Mobile number is already exits !');
              $("input[name=pmobile]").removeClass('valid').addClass('invalid');
            } else if (data.expert_lead_status == 'otp_invalid') {
              $('.potp_error_msg').html('Invalid OTP');
              $("input[name=potp]").removeClass('valid').addClass('invalid');
            } else {
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

    $("#edit-cname").keypress(function (e) {
      var firstChar = $("#edit-cname").val()
      var key = e.keyCode;
      var regex = new RegExp("^[a-zA-Z]+$");
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (regex.test(str) || key == 32) {
        if (e.keyCode == 32) {
          var cursorPos = $("#edit-cname").prop("selectionStart");
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
    // login form


    $(".parentloginsend--subinfo").attr("href", "javascript:void(0)");
    // Jquery ajax to save expert lead info
    $("body").on("click", ".parentlogin--subinfo", function () {
      var plmobile = $.trim($("input[name=plmobile]").val());
      var plotp = $.trim($("input[name=plotp]").val());
      var appid = $.trim($("input[name=appid]").val());
      var cdnmoengage = $.trim($("input[name=cdnmoengage]").val());
      var post_data = "&plmobile=" + plmobile + "&plotp=" + plotp + "&appid=" + appid + "&cdnmoengage=" + cdnmoengage;
      var expert_flag = true;
      // initial blank error message
      $('.plmobile_error_msg').html('');
      $('.plotp_error_msg').html('');
      $("input[name=plmobile]").removeClass('invalid').addClass('valid');
      $("select[name=plotp]").removeClass('invalid').addClass('valid');
      if (plmobile.length != 10) {
        $('.plmobile_error_msg').html('Please Provide Valid Mobile Number !');
        $("input[name=plmobile]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }

      if (expert_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "login-mobile",
          data: post_data,
          cache: false,
          beforeSend: function () {
            $('.loader-wrapper').css('display', 'block');
          },
          success: function (data) {
            $('.loader-wrapper').css('display', 'none');
            if (data.expert_lead_status == 'otp_require') {
              $('#editpllogin-submit').css("display", "block");
              $('#editpl-submit').css("display", "none");
              $('#edit-plotp').css("display", "block");
              $('.plotp_error_msg').html('Please Enter OTP !');
              $('.plotp_success_msgg').html('4 Digit OTP sent on above mobile no').show();
              $('.form-group-expert .plotp-rsend-lnk').html('<a href="javascript:void(0)" class="rsendcall">Resend OTP</a>');
            } else if (data.expert_lead_status == 'error') {
              $('.plmobile_error_msg').html('This Mobile number is not registered with lead school.');
            } else {
              console.log('Something Went Wrong');
            }

          },
          error: function (error) {

          }
        });
      }
    });

    $("body").on("click", ".parentloginsend--subinfo", function () {
      var plmobile = $.trim($("input[name=plmobile]").val());
      var plotp = $.trim($("input[name=plotp]").val());
      var appid = $.trim($("input[name=appid]").val());
      var prlogin = $.trim($("input[name=prlogin]").val());
      var cdnmoengage = $.trim($("input[name=cdnmoengage]").val());
      var post_data = "&plmobile=" + plmobile + "&plotp=" + plotp + "&appid=" + appid + "&cdnmoengage=" + cdnmoengage + "&prlogin=" + prlogin;
      var expert_flag = true;
      $('.plmobile_error_msg').html('');
      $('.plotp_error_msg').html('');
      $("input[name=plmobile]").removeClass('invalid').addClass('valid');
      $("select[name=plotp]").removeClass('invalid').addClass('valid');

      if (plmobile.length != 10) {
        $('.plmobile_error_msg').html('Please Provide Valid Mobile Number !');
        $("input[name=plmobile]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }

      if (plotp == '') {
        $('.plotp_error_msg').html('Please Enter OTP !');
        $("input[name=plotp]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }

      if (expert_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "login-mobilesend",
          data: post_data,
          cache: false,
          beforeSend: function () {
            $('.loader-wrapper').css('display', 'block');
          },
          success: function (data) {
            $('.loader-wrapper').css('display', 'none');
            if (data.expert_lead_status == 'success') {
              //datalayer for parentlogin events
              var dataLayer = window.dataLayer || [];
              dataLayer.push({
                'event': prlogin,
                'loginData': {
                  'mobile': plmobile,
                  'otp': plotp
                }
              });
              //moengage
              var trackevent_type = 'User Login';
              var adduserattribute = 'User Login Form';
              moengage_trackdata(cdnmoengage, appid, plmobile, plotp, trackevent_type, adduserattribute);
              // End Moengage tracking system code
              var dest = get_custom_url();
              location.href = dest;
              /*if (getUrlschool()["destination"]) {
                var destination = getUrlschool()["destination"];
                location.href =  destination;
              } else {
                //location.href = drupalSettings.path.baseUrl + 'loginrediect?dest=' + getUrlschool()["dest"];
                location.href = data.base_url;
              }*/

              $('.form-group-expert .plotp-rsend-lnk').html('');

            } else if (data.expert_lead_status == 'otp_invalid') {
              $('#edit-plotp').css("display", "block");
              $('.plotp_error_msg').html('Please Enter valid OTP !');
              $('.plotp_success_msg').html('');
              $('.form-group-expert .plotp-rsend-lnk').html('<a href="javascript:void(0)" class="rsendcall">Resend OTP</a>');
            } else {
              console.log('Something Went Wrong');
            }

          },
          error: function (error) {

          }
        });
      }
    });
    // Jquery ajax to resend otp
    $("body").on("click", ".rsendcall", function () {
      $(".plotp-rsend-lnk .rsendcall").hide();
      $(".plotp_success_msg").hide();
      setTimeout(function () {
        $(".plotp-rsend-lnk .rsendcall").show();        
      }, 20000);
      setTimeout(function () {
        $(".plotp_success_msg").show();     
      }, 10);
      var mobileval = $.trim($("input[name=plmobile]").val());
      var resend_data = "&plmobile=" + mobileval;
      var resend_flag = true;
      // initial blank error message
      $('.plmobile_error_msg').html('');
      // Add valid class in text field
      $("input[name=plmobile]").removeClass('invalid').addClass('valid');
      if (mobileval.length != 10) {
        $('.plmobile_error_msg').html('Please Provide Valid Mobile Number !');
        $("input[name=plmobile]").removeClass('valid').addClass('invalid');
        resend_flag = false;
      }

      if (resend_flag) {
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "resend-otp-self-callback",
          data: resend_data,
          cache: false,
          beforeSend: function () {
          },
          success: function (data) {
            console.log(data);
            if (data.resend_otp_status == 'success') {
              $('.plotp_success_msg').html('4 Digit OTP sent on above mobile no').show();
              $('.plotp_error_msg').html('');
            } else if (data.resend_otp_status == 'successnot') {
              $('.plotp_error_msg').html('There was some problem, Please try again later !');
            } else {
              console.log('Something Went Wrong');
            }

          },
          error: function (error) {

          }
        });
      }
    });
    $('#edit-plmobile').on('blur', function () {
      $(".signup-otp").hide();
      setTimeout(function () {
        $(".signup-otp").show();
      }, 20000);
      var mobileval = $.trim($("#edit-plmobile").val());
      var otp_flag = true;
      // condition to check mobile number
      if (mobileval.length != 10) {
        $('.otp-msg .error-text').html('Please Provide Valid Mobile Number.');
        otp_flag = false;
      }
      // If flag is true call controller via ajax
      if (otp_flag) {
        var otp_data = "&plmobile=" + mobileval;
        $.ajax({
          type: "POST",
          url: drupalSettings.path.baseUrl + "login-otp-verify",
          data: otp_data,
          cache: false,
          beforeSend: function () {
          },
          success: function (data) {
            console.log(data.otp_verify_status);
            if (data.otp_verify_status == 'otp_require') {
              $('.plotp_success_msg').html('4 Digit OTP sent on above mobile no').show();
              $('.plotp_error_msg .error-text').html('');
              $('.plmobile_error_msg').html('');
              $('.form-group-expert .plotp-rsend-lnk').html('<a href="javascript:void(0)" class="rsendcall">Resend OTP</a>');
              $(".unotp").attr('style', 'display: block');
              $(".unotp").focus();
            } else if (data.otp_verify_status == 'error') {
              var link = "/user/registerform";
              var anchor = $('<a />', {
                "href": link,
                "text": "Click here to SignUp",
                "class": "sign-up" 
              })
              $('.plmobile_error_msg').html('This Mobile number is not registered with lead school. ');
              $('.plmobile_error_msg').append(anchor);
            } else {
              console.log('Something Went Wrong');
            }
          },
          error: function (error) {

          }
        });
      }
    });
  });

})(jQuery);
