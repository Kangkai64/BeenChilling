$(() => {

  if ($(".dropzone-enabled").length > 0) {
    // var t = getUploaderTranslations();
    var dropzone = $(`
      <div class="file-dropzone" style="display: none;">
        <img src="/images/corner.svg" style="left:34px; top:34px; position:absolute;">
        <img src="/images/corner.svg" style="right:34px; top:34px; position:absolute; transform: rotate(90deg);">
        <img src="/images/corner.svg" style="left:34px; bottom:34px; position:absolute; transform: rotate(270deg);">
        <img src="/images/corner.svg" style="right:34px; bottom:34px; position:absolute; transform: rotate(180deg);">
        <h1>
        </h1>
      </div>
    `).appendTo($("body"));

    function showDropzone() {
      if (!dropzone) return;
      dropzone.show();
    }

    function hideDropzone() {
      if (!dropzone) return;
      dropzone.hide();
    }

    dropzone.click(function (e) {
      hideDropzone();
    });
    
    dropzone.on("dragleave", function (e) {
      hideDropzone();
    });

    $("body").on("dragenter", function (e) {
      e.stopPropagation();
      e.preventDefault();
      showDropzone();
    });

    $("body").on("dragover", function (e) {
      e.stopPropagation();
      e.preventDefault();
      showDropzone();
    });

    $("body").on("drop", function (e) {
      hideDropzone();
      fileDropped(e);
    });

    $("body").bind("paste", function (e) {
      var target = $(e.originalEvent.target);
      if (target.is("input, textarea")) return;
      if (!e.originalEvent.clipboardData) return;
      var items = e.originalEvent.clipboardData.items;
      if (!items) return;

      if (items.length > 1) {
        EventBus.$emit("multiple-images-added");
      }

      for (var i = 0; i < items.length; i++) {
        if (items[i].type.indexOf("image") == -1) continue;
        var blob = items[i].getAsFile();

        window.track("Images", "upload_paste_image", "Paste image");
        window.uploadFile(blob);
        return;
      }

      for (var i = 0; i < items.length; i++) {
        if (items[i].type.indexOf("text/plain") == -1) continue;
        items[i].getAsString((url) => {
          window.track("Images", "upload_paste_url", "Paste URL");
          window.uploadUrl(url);
        });
        return;
      }
    });
  };
    
  function setView(view) {
    if (view === 'table') {
      $('#table-view').show();
      $('#photo-view').hide();
      $('#table-view-button').addClass('active_link');
      $('#photo-view-button').removeClass('active_link');
    } else {
      $('#table-view').hide();
      $('#photo-view').show();
      $('#photo-view-button').addClass('active_link');
      $('#table-view-button').removeClass('active_link');
    }
  }

  // Check local storage for user preference
  const userView = localStorage.getItem('userView') || 'table';
  setView(userView);

  // Event listener for table view button
  $('#table-view-button').on('click', function() {
    localStorage.setItem('userView', 'table');
    setView('table');
  });

  // Event listener for photo view button
  $('#photo-view-button').on('click', function() {
    localStorage.setItem('userView', 'photo');
    setView('photo');
  });

  // Open the sidebar
  function openNav() {
    $('#sidebar').css('width', '250px');
  }

  // Close the sidebar
  function closeNav() {
      $('#sidebar').css('width', '0');
  }

  // Show the sidebar when the user-info-container is clicked
  $('.user-info-container').on('click', function() {
      openNav();
  });

  // Close the sidebar when the close button is clicked
  $('.closebutton').on('click', function() {
      closeNav();
  });

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

  // Photo preview
  $('label.upload input[type=file]').on('change', e => {
    const f = e.target.files[0];
    const img = $(e.target).siblings('img')[0];

    if (!img) return;

    img.dataset.src ??= img.src;

    if (f?.type.startsWith('image/')) {
        img.src = URL.createObjectURL(f);
    }
    else {
        img.src = img.dataset.src;
        e.target.value = '';
    }
  });

  // Splash screen
  const $splashScreen = $('#splash-screen');
  const $mainContent = $('#main-content');
  const $scoop = $('#scoop');
  const $topButton = $('#top'); // Define the back-to-top button

  // Check if the splash screen has been shown before
   // Show the splash screen if not shown before
   if (!localStorage.getItem('splashShown')) {
    localStorage.setItem('splashShown', 'true');
    $('#splash-screen').show();

    // Trigger the animation
    $('#scoop').on('animationend', function(event) {
        if (event.originalEvent.animationName === 'drop') {
            $('#slogan').css('visibility', 'visible');
            setTimeout(() => {
                $('#splash-screen').fadeOut(1000, function() {
                    $('#main-content').fadeIn(1000);
                    $('body, html').css('overflow', 'auto'); // Enable scrolling
                });
            }, 1500); // Wait for the typewriter effect to finish
            }
        });
    } else {
        $('#splash-screen').hide();
        $('#main-content').show();
        $('body, html').css('overflow', 'auto'); // Enable scrolling
    }


  // Show top button when the page is scrolled down 20px
  $('body').on('scroll', function () {
    if ($('body').scrollTop() > 20) {
      $topButton.fadeIn();
    } else {
      $topButton.fadeOut();
    }
  });

  // Scroll to top when button clicked
  $topButton.on('click', function () {
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
    const $sidebarLinks = $('#sidebar a');
    const $viewButtons = $('.view-button button');

    setActiveLink($navLinks);
    setActiveLink($sidebarLinks);
    setActiveLink($viewButtons);

    // Function to set the active link
    function setActiveLink($links) {
      $links.each(function () {
        if ($(this).attr('href') === currentPage) {
          $navLinks.removeClass('active_link');
          $sidebarLinks.removeClass('active_link');
          $(this).addClass('active_link');
        }
      });
    }

    // Event listener for view buttons
    $viewButtons.on('click', function () {
      $viewButtons.removeClass('active_link');
      $(this).addClass('active_link');
    });
  }

  // Initialize all components
  initDropdownHover();
  initFAQDropdown();
  initNavigation();

  // Export functions that need to be called from HTML
  window.displayEvent = displayEvent;

  // Confirmation message
  $('[data-confirm]').on('click', e => {
    const text = e.target.dataset.confirm || 'Are you sure?';
    if (!confirm(text)) {
        e.preventDefault();
        e.stopImmediatePropagation();
    }
  });
});