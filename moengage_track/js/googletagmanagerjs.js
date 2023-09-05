/*
* @file googletagmanagerjs.js
* Contains all functionality related to contact validation
*/
(function (Drupal, $) {
  'use strict';
  Drupal.behaviors.moengage_track = {
    attach: function (context, settings) {
   }
  };
})(Drupal, jQuery);
(function ($, Drupal, drupalSettings) {
  $('.popup-form-box #pchildhidden').css("display", "none");
  //get query string
  function getUrlprofile()
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
//end query string


  $(document).ready(function (){   
    // Jquery ajax to save expert lead info
    $( "#user-form #edit-submit" ).click(function() {      
      var pname = $.trim($("input[id=edit-field-parent-name-0-value]").val());
      var pmobile = $.trim($("input[id=edit-field-mobile-no-0-value]").val());
      var apikey = drupalSettings.moengage_track.moengage_api_key;
      var scripturl = drupalSettings.moengage_track.moengage_script_url;
      var moengagedebug = drupalSettings.moengage_track.moengage_debug;
      var i = 0;
      var childarray = [];
      var gradearray = [];
      $('.field-multiple-table tbody tr.draggable').each(function (i) {       
          var cname = $.trim($('input[name="field_pchild_name[' + i + '][subform][field_child_name][0][value]"]').val());
          var grade = $.trim($('select[name="field_pchild_name[' + i + '][subform][field_pgrade]"]').val()); 
          var i = i++;
          childarray.push(cname);
          gradearray.push(grade);
      });
      var gtm_grade = gradearray.join(',');
      var gtm_cname = childarray.join(',');
      var b2c_lead = $.trim($("input[name=b2c_lead]").val());
      if(b2c_lead == ''){
        var b2c_lead = 'B2C Lead submitted';
      }
      var post_data = "&pname=" + pname + "&pmobile=" + pmobile + "&b2c_lead=" + b2c_lead + "&apikey=" + apikey + "&scripturl=" + scripturl + "&moengagedebug=" + moengagedebug;     
      var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
      var expert_flag = true;
      if (pname == '') {
        $('.pname_error_msg').html('Please Provide Parent Name !');
        $("input[id=edit-field-parent-name-0-value]").removeClass('valid').addClass('invalid');
        expert_flag = false;
      }
      
      if (expert_flag) {
        $.ajax({
          type: "POST",
          data: post_data,
          cache: false,
          async: false,
          beforeSend :function(data) { },
          success: function (data) {
            if(data) {            
              //datalayer for B2C Lead submitted events
              var dataLayer = window.dataLayer || [];
              dataLayer.push({
                'event': b2c_lead,
                'myprofileAddchildData': {
                  'name': pname,
                  'mobile': pmobile,
                  'grade and child': [{ 
                          'grade applying for': gtm_grade,
                          'student name': gtm_cname
                }]
                }
              });
              //moengage tracking for my profie section
          (function(i,s,o,g,r,a,m,n){i.moengage_object=r;t={};q=function(f){return function(){(i.moengage_q=i.moengage_q||[]).push({f:f,a:arguments})}};f=['track_event','add_user_attribute','add_first_name','add_last_name','add_email','add_mobile','add_user_name','add_gender','add_birthday','destroy_session','add_unique_user_id','moe_events','call_web_push','track','location_type_attribute'],h={onsite:["getData","registerCallback"]};for(k in f){t[f[k]]=q(f[k])}for(k in h)for(l in h[k]){null==t[k]&&(t[k]={}),t[k][h[k][l]]=q(k+"."+h[k][l])}a=s.createElement(o);m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m);i.moe=i.moe||function(){n=arguments[0];return t};a.onload=function(){if(n){i[r]=moe(n)}}})(window,document,'script',scripturl,'Moengage');
              Moengage = moe({
                app_id: apikey,
                debug_logs: moengagedebug
              });
              Moengage.track_event(b2c_lead, {"B2C Lead Category": "Lead School", "moe_non_interactive": 1});
              Moengage.add_user_attribute("Add Child From Parent Profile");
              Moengage.add_first_name(pname);
              Moengage.add_mobile(pmobile);
              Moengage.add_unique_user_id(pmobile);
              Moengage.add_user_attribute("grade applying for", gtm_grade);
              Moengage.add_user_attribute("student name", gtm_cname);     
            // End Moengage tracking system code                  
           }else{
              console.log("Something wrong");            
            }  
          },
          error: function (error) {
          }
        });
      }
    });

    $('.childenquiry-subinfo').on('click', function () {
    var apikey = drupalSettings.moengage_track.moengage_api_key;
    var scripturl = drupalSettings.moengage_track.moengage_script_url;
    var moengagedebug = drupalSettings.moengage_track.moengage_debug;
    var parentmobile = drupalSettings.moengage_track.parent_mobile;
    var puseremail = drupalSettings.moengage_track.puser_email;
    var parentname = drupalSettings.moengage_track.parent_name;
    var pgrade = drupalSettings.moengage_track.pgrade;
    var schoolid = $.trim($("input[name=schoolid]").val());
   
    var vals = [];
    $('.childchk:checked').each(function () { //could also use .map here
      vals.push($(this).next().text());
    });
    var b2c_lead = $.trim($("input[name=b2c_lead]").val());
      if(b2c_lead == ''){
        var b2c_lead = 'B2C Lead submitted';
      }
    var expert_flag = true;
    $('.pchname_error_msg').html('');
    $(".pchname").removeClass('invalid').addClass('valid');

    if ($('.childchk:checkbox').filter(':checked').length < 1) {
      $('.pchname_error_msg').html('Please select at least one child.');
      $(".pchname").removeClass('valid').addClass('invalid');
      expert_flag = false;
      expert_flag = false;
    }
    var childid = vals.join(',');
    var post_data = "&schoolid=" + schoolid + "&childid=" + childid;
    if (expert_flag) {
      $.ajax({
        type: "POST",
        data: post_data,
        cache: false,
        beforeSend: function () {
          $('.loader-wrapper').css('display', 'block');
        },
        success: function (datavalue) {
          if(datavalue) {
            //datalayer for B2C Lead submitted events
            var dataLayer = window.dataLayer || [];
            dataLayer.push({
              'event': b2c_lead,
              'enquirenowchildData': {
                'schoolid': schoolid,
                'student name': childid,
                'mobile': parentmobile,
                'email': puseremail,
                'parent name': parentname
              }
            });
            //moengage tracking for my profie section
          (function(i,s,o,g,r,a,m,n){i.moengage_object=r;t={};q=function(f){return function(){(i.moengage_q=i.moengage_q||[]).push({f:f,a:arguments})}};f=['track_event','add_user_attribute','add_first_name','add_last_name','add_email','add_mobile','add_user_name','add_gender','add_birthday','destroy_session','add_unique_user_id','moe_events','call_web_push','track','location_type_attribute'],h={onsite:["getData","registerCallback"]};for(k in f){t[f[k]]=q(f[k])}for(k in h)for(l in h[k]){null==t[k]&&(t[k]={}),t[k][h[k][l]]=q(k+"."+h[k][l])}a=s.createElement(o);m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m);i.moe=i.moe||function(){n=arguments[0];return t};a.onload=function(){if(n){i[r]=moe(n)}}})(window,document,'script',scripturl,'Moengage');
          Moengage = moe({
            app_id: apikey,
            debug_logs: moengagedebug
          });
          Moengage.track_event(b2c_lead, {"B2C Lead Category": "Lead School", "moe_non_interactive": 1});
          Moengage.add_user_attribute("Enquire Now Tracking");
          Moengage.add_user_attribute("school id", schoolid);
          Moengage.add_user_attribute("student name", childid);
          if(puseremail != '') {
            Moengage.add_email(puseremail);
          }
          if(parentname != '') {
            Moengage.add_first_name(parentname);
          }
          if(parentmobile != '') {
            Moengage.add_mobile(parentmobile);
          }
          if(parentmobile != '' && pgrade != '') {     
            Moengage.add_unique_user_id(parentmobile+pgrade); 
          }     
        // End Moengage tracking system code
      
          }else{
            console.log("Something wrong");            
          }

        },
        error: function (error) {

        }
      });
    }
      });
      


  });
})(jQuery, Drupal, drupalSettings);
