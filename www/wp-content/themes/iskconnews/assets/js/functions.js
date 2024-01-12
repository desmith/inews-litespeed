jQuery(function(){

    allCarousel();

    forms();

    accord();

    others();

    tabContent();

    showSearchBox();

});



//All Caraosel

const allCarousel = () =>{ 

    jQuery('.single_slide').owlCarousel({

        autoplay: true,

        autoplayHoverPause:true,

        autoplayTimeout:5000,

        loop:true,

        nav: true,

        dots: false,

        mouseDrag: false,

        items: 1,

        autoHeight: false,

        animateOut: 'fadeOut',



        onTranslate: function(event) {



            var currentSlide, player, command;

    

            currentSlide = jQuery('.owl-item.active');

    

            player = currentSlide.find(".flex-video iframe").get(0);

    

            command = {

                "event": "command",

                "func": "pauseVideo"

            };

    

            if (player != undefined) {

                player.contentWindow.postMessage(JSON.stringify(command), "*");

    

            }

    

        }

    });

    jQuery('.relatedsingle').owlCarousel({

        autoplay: true,

        autoplayHoverPause:true,

        autoplayTimeout:5000,

        loop:false,

        nav: false,

        dots: true,

        mouseDrag: true,

        items: 1,

        margin: 20,

        animateOut: 'fadeOut',

    });

    jQuery('.fourslide').owlCarousel({

        autoplay: true,

        autoplayHoverPause:true,

        autoplayTimeout:5000,

        loop:false,

        nav: false,

        dots: true,

        mouseDrag: true,

        items: 4,

        margin:25,

        animateOut: 'fadeOut',

        responsive: {

            0: {

              items: 1,

              margin:25,

              dots: true

            },

            480: {

              items: 2,

              margin: 25,

              dots: true

            },

            991: {

              items: 3

            },

            1280: {

                items: 4

            }

          }

    });

    jQuery('.bestselling').owlCarousel({

        autoplay: false,

        autoplayHoverPause:true,

        autoplayTimeout:3000,

        loop:false,

        nav: true,

        dots: false,

        mouseDrag: true,

        margin:20,

        items: 4

    }); 

    jQuery('.review').owlCarousel({

        autoplay: false,

        autoplayHoverPause:true,

        autoplayTimeout:3000,

        loop:true,

        nav: false,

        dots: false,

        mouseDrag: true,

        margin:30,

        items: 5

    });

}





//All Forms

const forms = () =>{

    let allFormField = document.querySelectorAll('.form-field');

    setTimeout(function(){

        for(let i = 0; i < allFormField.length; i++){

            if(allFormField[i].value){

                allFormField[i].parentNode.classList.add('has-value');

            }

        }

    },100);

    for(let i = 0; i < allFormField.length; i++){

       

        allFormField[i].addEventListener('focus', function(){

            this.parentNode.classList.add('has-value');

        });

        allFormField[i].addEventListener('blur', function(){

            if(!this.value){

                this.parentNode.classList.remove('has-value');

            }

        });

    }

    

    jQuery('.form-elementfile input[type="file"]').on('change', function () {

        var infile = jQuery(this).val();

        var filename = infile.split("\\");

        filename = filename[filename.length - 1];

        jQuery(this).parents('.form-elementfile').find('#filename').text(filename);

        // jQuery(this).parent().addClass('hasValueall');

    });

}





//All Tabs

const tabContent = () =>{

    jQuery('body').on("click",'.tabMenu .item a',function (){  

        var indx = jQuery(this).parent().index();

        jQuery(".tabMenu .item a").removeClass("actv");

        jQuery(this).addClass("actv");

        jQuery(".tabContent").hide();

        jQuery(".tabContent").eq(indx).fadeIn();

    });

}





//All Modals

const openModal = (whichModal) =>{

    // close all open modal at first

    let openModals = document.querySelectorAll('.sds-modal');

    Array.from(openModals).forEach(function(openModal){

        openModal.classList.remove('is-active');

    });



    // target modal

    let targetModal = document.querySelector(`#${whichModal}`);



    // open target modal

    document.body.classList.add('bound');

    targetModal.classList.add('is-active');



    // exit target modal

    let exitModal = document.querySelectorAll('.sds-modal-exit');

    for(let i = 0; i < exitModal.length; i++){

        exitModal[i].addEventListener('click', function(){

            document.body.classList.remove('bound');

            targetModal.classList.remove('is-active');

        });

    }

}





//All Accords

const accord = () =>{



    jQuery('body').on("click",'.accord .accord-btn',function (e){  

        e.preventDefault();

        jQuery('.accord-target').not(jQuery(this).next('.accord-target')).slideUp();

        jQuery(this).next('.accord-target').slideToggle();



        jQuery('.accord-btn').not(jQuery(this)).removeClass('actv');

        jQuery(this).toggleClass('actv');

    });



    jQuery(".footerlinkwrap .hflinks").click(function(){

        jQuery(this).parent(".footerlinkbox").toggleClass("open"); 

    });



    if (screen.width <= 1069) {

        jQuery('body').on("click",'.site__menu .has__submenu > a',function (e){  

            e.preventDefault();

            jQuery('.submenu').not(jQuery(this).next('.submenu')).slideUp();

            jQuery(this).next('.submenu').slideToggle();

            jQuery('.site__menu .has__submenu > a').not(jQuery(this)).removeClass('actv');

            jQuery(this).toggleClass('actv');

        });

    }

}





//Others

const others = () =>{



    // hammenu

    jQuery('body').on('click', '.site__menu-btn', function(){

        jQuery('body').find('.site__menu').toggleClass('is-showing');

        jQuery('.site__menu-btn').toggleClass('is-working');

        jQuery('body').toggleClass('menufix');

    });



    jQuery(window).scroll(function() {

        var wScroll = jQuery(this).scrollTop();

        if (wScroll >= 200) {

            jQuery('#return-to-top').fadeIn(300);

        }

        else {

            jQuery('#return-to-top').fadeOut(300);

        }

        });

        jQuery('#return-to-top').click(function() {

        jQuery('body,html').animate({

            scrollTop : 0

        }, 500);

    });



    jQuery('.modal-exit').click(function(){

        jQuery('.sds-modal.is-active').removeClass('is-active');

    });





    jQuery(function () {

        if( jQuery('#vidBox').length > 0 ){

            jQuery('#vidBox').VideoPopUp({

                backgroundColor: "#17212a",

                opener: "videoBoxBtn",

                maxweight: "300",

            });

        }

    });



(function (jQuery) {



    jQuery.fn.VideoPopUp = function (options) {

        var defaults = {

            backgroundColor: "#000000",

            opener: "video",

            maxweight: "640",

            pausevideo: true,

            idvideo: ""

        };

        

        var patter = this.attr('id');



        var settings = jQuery.extend({}, defaults, options);



        var video = document.getElementById(settings.idvideo);

        function stopVideo() {

            var tag = jQuery('#' + patter + '').get(0).tagName;

            if (tag == 'video') {

                video.pause();

                video.currentTime = 0;

            }

        }

        jQuery('#' + patter + '').append('<div id="opct"></div>');

        jQuery('#opct').css("background", settings.backgroundColor);

        jQuery('#' + patter + '').css("z-index", "100001");

        jQuery('#' + patter + '').css("position", "fixed")

        jQuery('#' + patter + '').css("top", "0");

        jQuery('#' + patter + '').css("bottom", "0");

        jQuery('#' + patter + '').css("right", "0");

        jQuery('#' + patter + '').css("left", "0");

        jQuery('#' + patter + '').css("padding", "auto");

        jQuery('#' + patter + '').css("text-align", "center");

        jQuery('#' + patter + '').css("background", "none");

        jQuery('#' + patter + '').css("vertical-align", "vertical-align");

        jQuery("#videCont").css("z-index", "100002");

        //   jQuery('#' + patter + '').append('<div id="closer_videopopup"><img src="./images/close.png"></div>');

        jQuery("." + settings.opener + "").each(function(){

            jQuery(this).on('click', function () {

                var vlink = jQuery(this).attr("data-vlink");

                jQuery('body').find('#serVid').attr('src', vlink + '?rel=0');

                jQuery('#' + patter + "").show();

                jQuery('body').find('#serVid')[0].play();

            });

        })

        jQuery("#closer_videopopup, #opct").on('click', function () {

            jQuery('body').find('#serVid').removeAttr('src');

            jQuery('#' + patter + "").hide();

        });

        return this.css({



        });

    };







    }(jQuery));









    jQuery(document).on('click','.js-videoPoster',function(e) {

       

        e.preventDefault();

        var poster = jQuery(this);

        var wrapper = poster.closest('.js-videoWrapper');

        videoPlay(wrapper);

        

    });

      

      

    function videoPlay(wrapper) {

        var iframe = wrapper.find('.js-videoIframe');

        var src = iframe.data('src');

        wrapper.addClass('videoWrapperActive');

        iframe.attr('src',src);

    }

    jQuery('.js-videoPoster').click(function(){

        jQuery('.box.box_vid').toggleClass('box_vidActv');

    });

}



function show_message(response,form){

     jQuery('div.pop-up-overlay').show();

    switch(response.status){

        case true:

            if(form != null && form != undefined){

                if (jQuery(form).attr('id') == 'requestFreeTrial') {

                    jQuery('div#requestFreeTrialFormLB').removeClass('is-active');

                    jQuery('#requestFreeTrial .form-element').removeClass('has-value');

                }else{

                    jQuery(form)[0].reset();

                }

            }

            

            jQuery('.pop-up-overlay .pop-up-msg').removeClass('failure').addClass('success');

            jQuery('.pop-up-overlay .pop-up-msg').find('.content').find('h6').text('Congratulations!');

            jQuery('.pop-up-overlay .pop-up-msg').find('.content').find('p').text(response.message);

            break;

        case false:

            if(form != null && form != undefined){

                //console.log(form);return false;

                if (jQuery(form).attr('id') == 'requestFreeTrial') {

                    jQuery('#requestFreeTrial .form-element').addClass('has-value');

                }else{

                    jQuery(form)[0].reset();

                }

            }

            jQuery('.pop-up-overlay .pop-up-msg').removeClass('success').addClass('failure');

            jQuery('.pop-up-overlay .pop-up-msg').find('.content').find('h6').text('Sorry!');

            jQuery('.pop-up-overlay .pop-up-msg').find('.content').find('p').text(response.message);           

            break;

        default:

            break;      

    }

    setTimeout(function(){

        msgBoxClose();

        document.body.classList.remove('bound');

    },5000);

}



function msgBoxClose(){

    jQuery('.pop-up-overlay .pop-up-msg').removeClass('success').removeClass('failure');

    jQuery('.pop-up-overlay .pop-up-msg').find('.content').find('h6').text('');

    jQuery('.pop-up-overlay .pop-up-msg').find('.content').find('p').text(''); 

    jQuery('div.pop-up-overlay').hide(); 

}



// //Wow Animation

// var wow = new WOW(

//     {

//       boxClass:     'wow',      // animated element css class (default is wow)

//       animateClass: 'animated', // animation css class (default is animated)

//       offset:       0,          // distance to the element when triggering the animation (default is 0)

//       mobile:       true,       // trigger animations on mobile devices (default is true)

//       live:         true,       // act on asynchronously loaded content (default is true)

//       callback:     function(box) {

//         // the callback is fired every time an animation is started

//         // the argument that is passed in is the DOM node being animated

//       },

//       scrollContainer: null // optional scroll container selector, otherwise use window

//     }

//   );

// wow.init();





// Search

function showSearchBox(){

    jQuery('body').on('click', '.search-toggle', function(){

        jQuery('.comingsoon').hide();

        jQuery('.isk_header').toggleClass('searchBox-open');

        jQuery('.search-input').focus();

        jQuery('body').toggleClass('bound');

    });

}



// Scroll to Top

jQuery("a.back2top").click(function() {

    jQuery("html, body").animate({ scrollTop: 0 }, "slow");

    return false;

});





let player;



// this function gets called when API is ready to use

function onYouTubePlayerAPIReady() {

  // create the global player from the specific iframe (#id)

  player = new YT.Player('frame1', {

    events: {

      // call this function when player is ready to use

      'onReady': onPlayerReady

    }

  });

}



function onPlayerReady(event) {

  

  // bind events

  const playButton = document.querySelector(".msPlay");

  playButton.addEventListener("click", function() {

    console.log('play');

    player.playVideo();

  });

}