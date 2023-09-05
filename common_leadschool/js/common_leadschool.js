(function (Drupal, $) { 
    Drupal.behaviors.common_leadschool = {
      attach: function (context, settings) {      
          
          // click on formpopup
  $('.enqmc, .multienq').click(function (e) {
    //$('.formpop').show();
    $('.mchilds').show();
    var element = $(this);
    var id = $(element).data('id');
    $("#schoolid").val(id);

  });

  var $checkboxes = $('.school-list input[type="checkbox"]');

  $checkboxes.change(function () {
    var countCheckedCheckboxes = $checkboxes.filter(':checked').length;
    if (countCheckedCheckboxes > 1) {
      $('.school-list .action-btn a.enqd').hide();
      $('.action-btn a.enqmc').hide();
      $('.multplbtn').show();
    } else {
      $('.action-btn a.enqmc').show();
      $('.school-list .action-btn a.enqd').show();
      $('.multplbtn').hide();
    }
  });

  $('.checkschool').on('change', function () {
    var vals = [];
    $('.checkschool:checked').each(function () { //could also use .map here
      vals.push($(this).val());
    });
    $('.multienqc, .multienq, .nonlogin').attr("data-id", vals.join(','));
  });

  /*$('.multienqc, .enq').on('click', function () {
    var schoolid = $(this).attr("data-id");
    var post_data = "&schoolid=" + schoolid;

    $.ajax({
      type: "POST",
      url: drupalSettings.path.baseUrl + "singlechildenquirysave",
      data: post_data,
      cache: false,
      beforeSend: function () {
      },
      success: function (data) {
        if (data.expert_lead_status == 'success') {
          $('.messclass').show();
          var school_markup = '<p>For The Following Schools</p>';
          if(data.school_names.length > 1){
            console.log('in');
            for(var i=0;i<data.school_names.length;i++){
              school_markup += '<li>'+data.school_names[i]+'</li>';
            }
            $('.messclass .custom-school-names').html(school_markup);
          }
          else {
            console.log('out');
            $('.messclass .custom-school-names').css('display','none');
          }
        } else if (data.expert_lead_status == 'error') {
          console.log(0)
        } else {
          console.log('Something Went Wrong');
        }
      },
      error: function (error) {
      }
    });
  });*/


  /*$('.nonlogin').on('click', function () {
    var schoolid = $(this).attr("data-id");
    var datapath = $(this).attr("data-info");
    var post_data = "&schoolid=" + schoolid;

    $.ajax({
      type: "POST",
      url: drupalSettings.path.baseUrl + "singlenonloginenquirysave",
      data: post_data,
      cache: false,
      beforeSend: function () {
      },
      success: function (data) {
        location.href = drupalSettings.path.baseUrl + "user/loginform?dest=" + datapath;
      },
      error: function (error) {
      }
    });
  });*/

  $(".childenquiry-subinfo").attr("href", "javascript:void(0)");
  $('.childenquiry-subinfo').on('click', function () {
    var schoolid = $.trim($("input[name=schoolid]").val());
    var vals = [];
    $('.childchk:checked').each(function () { //could also use .map here
      vals.push($(this).val());
    });
    var expert_flag = true;
    $('.pchname_error_msg').html('');
    $(".pchname").removeClass('invalid').addClass('valid');

    if ($('.childchk:checkbox').filter(':checked').length < 1) {
      $('.pchname_error_msg').html('Please select at least one child.');
      $(".pchname").removeClass('valid').addClass('invalid');
      expert_flag = false;
    }
    var childid = vals.join(',');
    var post_data = "&schoolid=" + schoolid + "&childid=" + childid;
    if (expert_flag) {
      $.ajax({
        type: "POST",
        url: drupalSettings.path.baseUrl + "multiplechildenquirysave",
        data: post_data,
        cache: false,
        beforeSend: function () {
          $('.loader-wrapper').css('display', 'block');
        },
        success: function (data) {
          $('.loader-wrapper').css('display', 'none');
          if (data.expert_lead_status == 'success') {
            console.log(data);
            var school_markup = '<p>For The Following Schools</p>';
            if(data.school_names.length > 1){
              for(var i=0;i<data.school_names.length;i++){
                school_markup += '<li>'+data.school_names[i]+'</li>';
              }
              $('.messclass .custom-school-names').html(school_markup);
            }
            else{
              $('.messclass .custom-school-names').css('display','none');
            }
            $('.messclass').show();
            $('.mchilds').hide();
          } else if (data.expert_lead_status == 'error') {
            console.log(0)
          } else {
            console.log('Something Went Wrong');
          }

        },
        error: function (error) {

        }
      });
    }
            });

            var school_list = $(".path-frontpage .school-list ul li").filter(".school-row-listing");
  var nids = [];
  if(school_list.length == 1 ){
    for(var i=0;i<3;i++){
        $(school_list[i].children[0]).prop('checked','true');
    }
  }
  else if(school_list.length == 2 ){
    for(var i=0;i<3;i++){
      nids.push($(school_list[i].children[0]).val());
      $(school_list[i].children[0]).prop('checked','true');
    }
    var nid = nids.toString();
    $(".path-frontpage .multplbtn").css('display','block');
    $(".path-frontpage .multplbtn a").attr('data-id',nid);
  }
  else if(school_list.length >= 3 ){
    for(var i=0;i<3;i++){
      nids.push($(school_list[i].children[0]).val());
      $(school_list[i].children[0]).prop('checked','true');
    }
    var nid = nids.toString();
    $(".path-frontpage .multplbtn").css('display','block');
    $(".path-frontpage .multplbtn a").attr('data-id',nid);
  }

  setTimeout(function () {
    $('.path-frontpage .view-school-listing .school-list > ul').addClass('owl-carousel');
    $('.path-frontpage .school-list > ul').owlCarousel({
      loop: $(".owl-carousel").children().length > 1,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      margin: 50,
      responsiveClass: true,
      nav: false,      
      responsive: {
        0: {
          items: 1.2,
      margin: 20,
        },
        700: {
          items: 2,
        },
        1000: {
          items: 4,
          loop: false
        },
        1150: {
          nav: true,
        }
      }
    });
    }, 2000);
          
      }

    };
  
  })(Drupal, jQuery);