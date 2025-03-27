$(() => {

  // Initiate GET request
  $('[data-get]').on('click', e => {
    e.preventDefault();
    const url = e.target.dataset.get;
    location = url || location;
  });

  // Initiate POST request
  $('[data-post]').on('click', e => {
    e.preventDefault();
    const url = e.target.dataset.post;
    const f = $('<form>').appendTo(document.body)[0];
    f.method = 'POST';
    f.action = url || location;
    f.submit();
  });

  // Autofocus
  $('form :input:not(button):first').focus();
  $('.err:first').prev().focus();
  $('.err:first').prev().find(':input:first').focus();

  // Reset form
  $('[type=reset]').on('click', e => {
      e.preventDefault();
      location = location;
  });

  // Auto uppercase
  $('[data-upper]').on('input', e => {
      const a = e.target.selectionStart;
      const b = e.target.selectionEnd;
      e.target.value = e.target.value.toUpperCase();
      e.target.setSelectionRange(a, b);
  });

  // Splash screen
  const $splashScreen = $('#splash-screen');
  const $mainContent = $('#main-content');
  const $scoop = $('#scoop');

  // Check if the splash screen has been shown before
  if (!localStorage.getItem('splashShown')) {
    localStorage.setItem('splashShown', 'true');

    // Trigger the animation
    $scoop.on('animationend', function (event) {
      if (event.originalEvent.animationName === 'drop') {
        $splashScreen.fadeOut(1000, function () {
          $mainContent.show();
          $('body, html').css('overflow', 'auto'); // Enable scrolling
        });
      }
    });
  } else {
    $splashScreen.hide();
    $mainContent.show();
    $('body, html').css('overflow', 'auto'); // Enable scrolling
  }

  // Scroll to top button functionality
  $(window).on('scroll', function() {
    const $topButton = $('#top');
    if ($(window).scrollTop() > 20) {
      $topButton.fadeIn();
    } else {
      $topButton.fadeOut();
    }
  });

  // Scroll to top when button clicked
  $('#top').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
  });

  // Nav dropdown hover
  function initDropdownHover() {
    $('#dropdown').hover(
      function() {
        const contentCount = $('#dropdown_content a').length;
        $('#dropdown_content').css('height', (contentCount * 100) + '%');
        $('#dropdown_content').css('transition', 'height 0.3s');
      },
      function() {
        $('#dropdown_content').css('height', '0px');
        $('#dropdown_content').css('transition', 'height 0.3s');
      }
    );
  }

  // FAQ dropdown
  function initFAQDropdown() {
    $('.faq_q').on('click', function() {
      const $currentDropdown = $(this).next('.faq_a_container');
      const $otherDropdowns = $('.faq_a_container').not($currentDropdown);
      
      // Close all other dropdowns
      $otherDropdowns.css({
        'height': '0px',
        'transition': 'height 0.6s'
      });
      
      if ($currentDropdown.height() === 0) {
        $currentDropdown.css({
          'height': $currentDropdown.prop('scrollHeight') + 'px',
          'transition': 'height 0.6s'
        });
      } else {
        $currentDropdown.css({
          'height': '0px',
          'transition': 'height 0.6s'
        });
      }
    });
  }

  // Events display
  function displayEvent(className) {
    $('.new, .old, .future').hide();
    $(`.${className}`).show();
    
    $('.topics_nav_active').removeClass('topics_nav_active');
    $(`#topics_${className}`).addClass('topics_nav_active');
  }

  // Navigation active state
  function initNavigation() {
    const currentPage = window.location.pathname;
    const $navLinks = $('nav ul li a');
    
    $navLinks.each(function() {
      if ($(this).attr('href') === currentPage) {
        $navLinks.removeClass('active_link');
        $(this).addClass('active_link');
      }
    });
    
    $navLinks.on('click', function(e) {
      $navLinks.removeClass('active_link');
      $(this).addClass('active_link');
    });
  }

  // Initialize all components
  initDropdownHover();
  initFAQDropdown();
  initNavigation();

  // Export functions that need to be called from HTML
  window.displayEvent = displayEvent;
});