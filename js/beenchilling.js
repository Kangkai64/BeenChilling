$(() => {
  // This must be PLACED BEFORE other event handlers
  // Don't move this, put other event handlers under this
  // Confirmation message
  $('[data-confirm]').on('click', e => {
    const text = e.target.dataset.confirm || 'Are you sure?';
    if (!confirm(text)) {
        e.preventDefault();
        e.stopImmediatePropagation();
    }
  });

  // Add eye icons after each password field's label
  $('label[for="password"], label[for="new_password"], label[for="confirm"]').each(function () {
    // Find the corresponding input field
    var inputId = $(this).attr('for');
    var inputField = $('#' + inputId);

    // Add the toggle icon after the input field
    inputField.after('<span class="password-toggle" data-target="' + inputId + '"><i class="fa fa-eye"></i></span>');
  });

  // Handle click on password toggle icons
  $('.password-toggle').on('click', function () {
    // Get target input field
    var targetId = $(this).data('target');
    var passwordField = $('#' + targetId);

    // Toggle password visibility
    if (passwordField.attr('type') === 'password') {
      passwordField.attr('type', 'text');
      $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      passwordField.attr('type', 'password');
      $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });

  // Handle unit selection change
  $('.unit-form select[name="unit"]').on('change', function () {
    const form = $(this).closest('form');
    const productID = form.find('input[name="ProductID"]').val();
    const unit = $(this).val();

    // Send AJAX request to update cart
    $.ajax({
      url: window.location.href,
      type: 'POST',
      data: {
        'ajax': 'true',
        'id': productID,
        'unit': unit,
        'action': 'cart'
      },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          // Update subtotal for the specific product
          $(`td.subtotal[data-product-id="${productID}"]`).text(response.subtotal);

          // Update cart totals
          $('#cart-total-item').text(response.cart_count);
          $('#cart-total-price').text(response.total);

          // Update wishlist totals
          $('#wishlist-total-item').text(response.wishlist_count);
          $('#wishlist-total-price').text(response.total);
        }
      }
    });
  });

  // Handle unit selection change
  $('.unit-form select[name="unit"]').on('change', function () {
    const form = $(this).closest('form');
    const productID = form.find('input[name="ProductID"]').val();
    const unit = $(this).val();

    // Send AJAX request to update cart
    $.ajax({
      url: window.location.href,
      type: 'POST',
      data: {
        'ajax': 'true',
        'id': productID,
        'unit': unit,
        'action': 'cart'
      },
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          // Update subtotal for the specific product
          $(`td.subtotal[data-product-id="${productID}"]`).text(response.subtotal);

          // Update cart totals
          $('#cart-total-item').text(response.cart_count);
          $('#cart-total-price').text(response.total);

          // Update wishlist totals
          $('#wishlist-total-item').text(response.wishlist_count);
          $('#wishlist-total-price').text(response.total);
        }
      }
    });
  });

  // Add to cart animation
  $('.add-to-cart').on('click', function (e) {
    e.preventDefault();

    // Get product data
    var productId = $(this).data('id');

    // Get the product image
    var productImg = $(this).closest('.product').find('.product-images').eq(0);

    // Get the cart button position in the sidebar
    var cartButton = $('.cart-button');

    // Create a clone of the image
    var imgClone = productImg.clone()
      .offset({
        top: productImg.offset().top,
        left: productImg.offset().left
      })
      .css({
        'opacity': '0.5',
        'position': 'absolute',
        'height': productImg.height(),
        'width': productImg.width(),
        'z-index': '99'
      })
      .appendTo($('body'))
      .animate({
        'top': cartButton.offset().top + 10,
        'left': cartButton.offset().left + 10,
        'width': 75,
        'height': 75
      }, 1000, 'easeInOutExpo');

    // Remove the clone after animation completes
    setTimeout(function () {
      imgClone.remove();

      // Add visual feedback
      cartButton.addClass('added');
      setTimeout(function () {
        cartButton.removeClass('added');
      }, 1000);

      // Send AJAX request to update cart
      $.ajax({
        url: window.location.href,
        type: 'POST',
        data: {
          id: productId,
          unit: 1, // Default to adding one unit
          ajax: 'true',
          action: 'cart'
        },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            // Update cart count in UI
            $('#cart-total-item-menu').text('(' + response.cart_count + ')');
          }
        }
      });

    }, 1000);
  });

  // Add to wishlist animation
  $('.add-to-wishlist').on('click', function (e) {
    e.preventDefault();

    // Get product data
    var productId = $(this).data('id');

    // Get the product image
    var productImg = $(this).closest('.product').find('.product-images').eq(0);

    // Get the wishlist button position in the sidebar
    var wishlistButton = $('.wishlist-button');

    // Create a clone of the image
    var imgClone = productImg.clone()
      .offset({
        top: productImg.offset().top,
        left: productImg.offset().left
      })
      .css({
        'opacity': '0.5',
        'position': 'absolute',
        'height': productImg.height(),
        'width': productImg.width(),
        'z-index': '99'
      })
      .appendTo($('body'))
      .animate({
        'top': wishlistButton.offset().top + 10,
        'left': wishlistButton.offset().left + 10,
        'width': 75,
        'height': 75
      }, 1000, 'easeInOutExpo');

    // Remove the clone after animation completes
    setTimeout(function () {
      imgClone.remove();

      // Add visual feedback
      wishlistButton.addClass('added');
      setTimeout(function () {
        wishlistButton.removeClass('added');
      }, 1000);

      // Send AJAX request to update wishlist
      $.ajax({
        url: window.location.href,
        type: 'POST',
        data: {
          id: productId,
          unit: 1, // Add default unit value
          ajax: 'true',
          action: 'wishlist'
        },
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            // Update wishlist count in UI
            $('#wishlist-total-item-menu').text('(' + response.wishlist_count + ')');
          }
        }
      });
    }, 1000);
  });

  // Handle clear and checkout buttons
  $('.button-group .button').on('click', function () {
    const postUrl = $(this).data('post');
    const getUrl = $(this).data('get');

    if (postUrl) {
      // Create a form to submit
      const form = $('<form>').attr({
        method: 'POST',
        action: postUrl
      });

      // Extract any data attributes and add as hidden fields
      const btnName = $(this).text().toLowerCase();
      form.append($('<input>').attr({
        type: 'hidden',
        name: 'btn',
        value: btnName
      }));

      // Append form to body, submit it, and remove it
      $('body').append(form);
      form.submit();
      form.remove();
    } else if (getUrl) {
      window.location.href = getUrl;
    }
  });

  const stars = $('.star');
  const ratingInput = $('#selected-rating');
  const ratingText = $('.rating-text');
  const ratingTexts = [
    'Select your rating',
    'Poor - 1 star',
    'Fair - 2 stars',
    'Good - 3 stars',
    'Very Good - 4 stars',
    'Excellent - 5 stars'
  ];

  // Handle initial state if rating was already selected
  const initialRating = ratingInput.val();
  if (initialRating) {
    updateStars(initialRating);
  }

  // Add event listeners to stars
  stars.each(function () {
    // Hover effect
    $(this).on('mouseenter', function () {
      const rating = $(this).data('rating');
      highlightStars(rating);
      ratingText.text(ratingTexts[rating]);
    });

    // Click to select rating
    $(this).on('click', function () {
      const rating = $(this).data('rating');
      ratingInput.val(rating);
      updateStars(rating);
      ratingText.text(ratingTexts[rating]);
    });
  });

  // Reset stars on mouse leave if no rating selected
  $('.stars').on('mouseleave', function () {
    const selectedRating = ratingInput.val();
    if (selectedRating) {
      updateStars(selectedRating);
      ratingText.text(ratingTexts[selectedRating]);
    } else {
      resetStars();
      ratingText.text(ratingTexts[0]);
    }
  });

  // Helper functions
  function highlightStars(rating) {
    stars.each(function () {
      const starRating = $(this).data('rating');
      if (starRating <= rating) {
        $(this).html('★');
        $(this).addClass('active');
      } else {
        $(this).html('☆');
        $(this).removeClass('active');
      }
    });
  }

  function updateStars(rating) {
    highlightStars(rating);
  }

  function resetStars() {
    stars.each(function () {
      $(this).html('☆');
      $(this).removeClass('active');
    });
  }

  // Popup notification handling
  $('.close-popup').on('click', function () {
    $(this).parent().parent().hide();
  });

  // Auto-close popup after 5 seconds
  setTimeout(function () {
    $('.popup-notification').hide();
  }, 5000);

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
    $('#sidebar').css('width', '300px');
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

    const text = e.target.dataset.confirm || 'Are you sure?';
    if (!confirm(text)) {
      e.stopImmediatePropagation();
      return false;
    }

    // Only reload if confirmed or if no confirmation needed
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
    $splashScreen.show();

    // Trigger the animation
    $scoop.on('animationend', function (event) {
      if (event.originalEvent.animationName === 'drop') {
        $('#slogan').css('visibility', 'visible');
        setTimeout(() => {
          $splashScreen.fadeOut(1000, function () {
            $mainContent.fadeIn(1000);
            $('body, html').css('overflow', 'auto'); // Enable scrolling
          });
        }, 1500); // Wait for the typewriter effect to finish
      }
    });
  } else {
    $splashScreen.hide();
    $mainContent.show();
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

    $navLinks.removeClass('active_link');
    $sidebarLinks.removeClass('active_link');

    // Check for specific pages that need custom active link handling
    if (currentPage.includes('/page/admin/user_update.php') ||
      currentPage.includes('/page/admin/user_details.php') ||
      currentPage.includes('/page/admin/user_insert.php') ||
      currentPage.includes('/page/admin/user_list.php')) {
      // Set "User List" as active for all user-related pages
      $navLinks.removeClass('active_link');
      $('nav ul li a[href="/page/admin/user_list.php"]').addClass('active_link');
    } else {
      // For other pages, use the standard matching logic
      setActiveLink($navLinks);
      setActiveLink($sidebarLinks);
    }

    // Always set the view buttons active state
    setActiveLink($viewButtons);

    // Function to set the active link
    function setActiveLink($links) {
      $links.each(function () {
        const linkHref = $(this).attr('href');
        if (linkHref && currentPage.startsWith(linkHref)) {
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

  // Initialize all components
  initDropdownHover();
  initFAQDropdown();
  initNavigation();

  // Export functions that need to be called from HTML
  window.displayEvent = displayEvent;
});