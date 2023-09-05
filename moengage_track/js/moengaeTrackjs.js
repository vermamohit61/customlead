/*
* @file parentloginjs.js
* Contains all functionality related to contact validation
*/
(function (Drupal, $) {
  'use strict';
     Drupal.behaviors.moengage_track = {
    attach: function (context, settings) {
      
    }
  };
})(Drupal, jQuery);
(function ($) {
  
  $(document).ready(function () {

    var noresult = $('.view-empty #school-no-result-found').length;
    if(noresult == 1){
      var filterURL = $(location).attr("href");
      var breakurl = filterURL.split('/')[3];
      var appid = drupalSettings.moengage_track.moengage_api_key;
      var cdnmoengage = drupalSettings.moengage_track.moengage_script_url;
      var moengagedebug = drupalSettings.moengage_track.moengage_debug;
      var puseremail = drupalSettings.moengage_track.puser_email;
      var parentname = drupalSettings.moengage_track.parent_name;
      var parentmobile = drupalSettings.moengage_track.parent_mobile;
      var pgrade = drupalSettings.moengage_track.pgrade;
      var foundresult = $('#school-no-result-found').text();
        if(foundresult != '') {
          var notfoundresult = 1;
        } else {
          var notfoundresult = 0;
        }
      var post_data = "&appid=" + appid + "&cdnmoengage=" + cdnmoengage + "&moengagedebug=" + moengagedebug + "&filterURL=" + filterURL + "&puseremail=" + puseremail + "&parentname=" + parentname + "&parentmobile=" + parentmobile + "&pgrade=" + pgrade + "&foundresult=" + foundresult;
     
      var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
      $.ajax({
        type: "POST",
        url: drupalSettings.path.baseUrl + "schools-search-filter-track/" +breakurl,
        data: post_data,
        cache: false,
        beforeSend: function () {
          $('.loader-wrapper').css('display', 'block');
        },
        success: function (data) {
          $('.loader-wrapper').css('display', 'none');
          console.log(data.expert_lead_status);
          if (data.expert_lead_status == 'success') {
            
      //moengage tracking for my profie section
        (function(i,s,o,g,r,a,m,n){i.moengage_object=r;t={};q=function(f){return function(){(i.moengage_q=i.moengage_q||[]).push({f:f,a:arguments})}};f=['track_event','add_user_attribute','add_first_name','add_last_name','add_email','add_mobile','add_user_name','add_gender','add_birthday','destroy_session','add_unique_user_id','moe_events','call_web_push','track','location_type_attribute'],h={onsite:["getData","registerCallback"]};for(k in f){t[f[k]]=q(f[k])}for(k in h)for(l in h[k]){null==t[k]&&(t[k]={}),t[k][h[k][l]]=q(k+"."+h[k][l])}a=s.createElement(o);m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m);i.moe=i.moe||function(){n=arguments[0];return t};a.onload=function(){if(n){i[r]=moe(n)}}})(window,document,'script',cdnmoengage,'Moengage');
        Moengage = moe({
          app_id: appid,
          debug_logs: moengagedebug
        });   
      // End Moengage tracking system code
        Moengage.track_event("School Listing Filter", {"SchoolCategory": "Politics", "moe_non_interactive": 1});
        Moengage.add_user_attribute("School Listing Filter");
        if(filterURL != '') {
          Moengage.add_user_attribute("filter url",filterURL);
        }
        if(notfoundresult != '') {
          Moengage.add_user_attribute("result found",notfoundresult);
        }
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
             
          } else if (data.expert_lead_status == 'error') {
            console.log('Something error');
          } else {
            console.log('Something Went Wrong');
          }

        },
        error: function (error) {

        }
      });
      
    } 
  });
})(jQuery);
