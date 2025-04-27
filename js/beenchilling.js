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

  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    console.log('getUserMedia supported');
  } else {
    console.error('getUserMedia is not supported in this browser');
    $('#qrResult').text('Camera access is not supported in this browser.');
  }

  // Initially hide the video and make sure the message is visible
  $('#video').hide();

  // Start webcam function
  $('#startButton').on('click', function () {
    startCam();
  });

  function startCam() {
    const video = $('#video')[0];

    // Show the video element when starting camera
    $('#video').show();
    // Hide the message when camera starts
    $('.booth h2').hide();

    if (navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: 'environment', // Use back camera if available
          width: { ideal: 1280 },
          height: { ideal: 720 }
        }
      })
        .then(function (stream) {
          video.srcObject = stream;
          console.log('Camera started successfully');
        })
        .catch(function (error) {
          console.error("Something went wrong!", error);
          $('#qrResult').text("Error accessing camera: " + error.message);
          // Show message again and hide video if there's an error
          $('.booth h2').show();
          $('#video').hide();
        });
    } else {
      console.log("getUserMedia not supported on your browser!");
      $('#qrResult').text("Camera access is not supported in this browser.");
      // Show message again and hide video if not supported
      $('.booth h2').show();
      $('#video').hide();
    }
  }

  // Stop webcam function
  $('#stopButton').on('click', function () {
    const video = $('#video')[0];
    if (video.srcObject) {
      const stream = video.srcObject;
      const tracks = stream.getTracks();

      $.each(tracks, function (index, track) {
        track.stop();
      });

      video.srcObject = null;
      console.log('Camera stopped');

      // Stop scanning if it's running
      if (scanningInterval) {
        clearInterval(scanningInterval);
        scanningInterval = null;
      }

      // Show the message and hide the video when stopping camera
      $('.booth h2').show();
      $('#video').hide();
    }
  });

  // Generate QR code function
  function generateQRCode() {
    const data = $('#qrData').val();
    if (!data) {
      alert('Please enter data for the QR code');
      return;
    }

    // Clear previous QR code by removing all child nodes
    const qrContainer = document.getElementById("qrCanvas");
    while (qrContainer.firstChild) {
      qrContainer.removeChild(qrContainer.firstChild);
    }

    // Generate QR code
    try {
      new QRCode(qrContainer, {
        text: data,
        width: 256,
        height: 256,
        colorDark: "#d34f73",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
      console.log("QR code generation completed");
    } catch (e) {
      console.error("Error generating QR code:", e);
    }
  }

  // Generate QR code button
  $('#generateQRBtn').on('click', generateQRCode);

  // Scan QR code function with continuous scanning
  let scanningInterval = null;

  $('#scanQRBtn').on('click', function () {
    const video = $('#video')[0];

    // Check if camera is active
    if (!video.srcObject) {
      $('#qrResult').text("Please start the camera first");
      return;
    }

    // If already scanning, stop it
    if (scanningInterval) {
      clearInterval(scanningInterval);
      scanningInterval = null;
      $('#qrResult').text("QR scanning stopped");
      return;
    }

    // Start scanning
    $('#qrResult').text("Scanning for QR codes...");

    // Create canvas for scanning
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    // Set up interval to scan video frames
    scanningInterval = setInterval(function () {
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw current video frame to canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Get image data from canvas
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

        // Scan for QR code
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
          // QR code detected
          console.log("QR Code detected:", code.data);
          $('#qrResult').text(code.data);

          // Optionally stop scanning after successful detection
          clearInterval(scanningInterval);
          scanningInterval = null;
        }
      }
    }, 100); // Scan every 100ms
  });

  // Capture image function
  $('#captureBtn').on('click', function () {
    const video = $('#video')[0];
    const canvas = $('#canvas')[0];

    // Check if camera is active
    if (!video.srcObject) {
      alert("Please start the camera first");
      return;
    }

    const context = canvas.getContext('2d');

    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Draw the current video frame to canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert canvas to image
    const imageDataURL = canvas.toDataURL('image/png');

    // Send the image to server
    saveImageToServer(imageDataURL);
  });

  // Function to send image to server
  function saveImageToServer(imageDataURL) {
    // Remove the data URL prefix
    const base64Data = imageDataURL.replace(/^data:image\/png;base64,/, "");

    // Send AJAX request to server
    $.ajax({
      url: '/page/save_photo.php',  // Your server endpoint
      type: 'POST',
      data: {
        image: base64Data,
        filename: 'photo_' + new Date().getTime() + '.png' // Unique filename
      }
    });
  }

  // Initialize image slider if it exists on the page
  if ($('.image-slider').length) {
    const slider = $('.slider-container');
    const slides = $('.slide');
    const dots = $('.dot');
    const prevBtn = $('.prev-btn');
    const nextBtn = $('.next-btn');
    let currentSlide = 0;

    function showSlide(index) {
      slides.removeClass('active');
      dots.removeClass('active');

      slides.eq(index).addClass('active');
      dots.eq(index).addClass('active');
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % slides.length;
      showSlide(currentSlide);
    }

    function prevSlide() {
      currentSlide = (currentSlide - 1 + slides.length) % slides.length;
      showSlide(currentSlide);
    }

    prevBtn.on('click', prevSlide);
    nextBtn.on('click', nextSlide);

    dots.each(function (index) {
      $(this).on('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
      });
    });

    // Auto slide if needed
    // setInterval(nextSlide, 5000);
  }

  // Image upload handling
  $(function () {
    const maxFileSize = 1 * 1024 * 1024; // 1MB
    const maxFiles = 5; // Maximum 5 images for product insert
    const maxAdditionalFiles = 4; // Maximum 4 additional images for product update
    const allowedTypes = ['image/jpeg', 'image/png'];

    const $uploadZone = $('#imageUploadZone');
    const $fileInput = $('#productImages');
    const $previewContainer = $('#imagePreviewContainer');
    
    // Additional images upload zone
    const $additionalUploadZone = $('#additionalImagesUploadZone');
    const $additionalFileInput = $('#additionalImages');
    const $additionalPreviewContainer = $('#additionalImagesPreviewContainer');
    const $deletedImagesInput = $('#deletedImages');
    let deletedImages = [];

    // Only initialize image upload if the elements exist
    if ($uploadZone.length) {
      // Handle drag and drop events
      $uploadZone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
      });

      $uploadZone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
      });

      $uploadZone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
          handleFiles(files);
        }
      });

      // Handle click to upload
      $uploadZone.on('click', function (e) {
        // Only trigger if clicking on the upload zone or instructions
        if ($(e.target).is('.image-upload-zone, .upload-instructions, .upload-hint, i')) {
          $fileInput.click();
        }
      });

      $fileInput.on('change', function () {
        if (this.files.length > 0) {
          handleFiles(this.files);
        }
      });

      // Handle file processing
      function handleFiles(files) {
        const validFiles = Array.from(files).filter(file => {
          if (!allowedTypes.includes(file.type)) {
            alert(`File ${file.name} is not a valid image type. Only JPG and PNG are allowed.`);
            return false;
          }
          if (file.size > maxFileSize) {
            alert(`File ${file.name} is too large. Maximum size is 1MB.`);
            return false;
          }
          return true;
        });

        if ($previewContainer.children().length + validFiles.length > maxFiles) {
          alert(`Maximum ${maxFiles} images allowed.`);
          return;
        }

        validFiles.forEach(file => {
          const reader = new FileReader();
          reader.onload = function (e) {
            const $preview = $('<div class="image-preview">')
              .append($('<img>').attr('src', e.target.result))
              .append($('<button class="remove-image" type="button">×</button>'));

            $previewContainer.append($preview);

            // Handle remove button
            $preview.find('.remove-image').on('click', function (e) {
              e.stopPropagation();
              $preview.remove();
            });
          };
          reader.readAsDataURL(file);
        });
      }

      // Handle form reset
      $('form').on('reset', function () {
        $previewContainer.empty();
      });

      // Form submission handler - only for product insert form
      $('form[data-title="Insert Product"]').on('submit', function (e) {
        if ($previewContainer.children().length === 0) {
          e.preventDefault();
          alert('Please upload at least one product image');
          return false;
        }
      });
    }
    
    // Initialize additional images upload if the elements exist
    if ($additionalUploadZone.length) {
      // Handle drag and drop events for additional images
      $additionalUploadZone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
      });

      $additionalUploadZone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
      });

      $additionalUploadZone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
          handleAdditionalFiles(files);
        }
      });

      // Handle click to upload additional images
      $additionalUploadZone.on('click', function (e) {
        // Only trigger if clicking on the upload zone or instructions
        if ($(e.target).is('.image-upload-zone, .upload-instructions, .upload-hint, i')) {
          $additionalFileInput.click();
        }
      });

      $additionalFileInput.on('change', function () {
        if (this.files.length > 0) {
          handleAdditionalFiles(this.files);
        }
      });

      // Handle additional file processing
      function handleAdditionalFiles(files) {
        const validFiles = Array.from(files).filter(file => {
          if (!allowedTypes.includes(file.type)) {
            alert(`File ${file.name} is not a valid image type. Only JPG and PNG are allowed.`);
            return false;
          }
          if (file.size > maxFileSize) {
            alert(`File ${file.name} is too large. Maximum size is 1MB.`);
            return false;
          }
          return true;
        });

        // Get current count of additional images (excluding those marked for deletion)
        const currentCount = $additionalPreviewContainer.find('.image-preview:not(.deleted)').length;
        
        if (currentCount + validFiles.length > maxAdditionalFiles) {
          alert(`Maximum ${maxAdditionalFiles} additional images allowed.`);
          return;
        }

        validFiles.forEach(file => {
          const reader = new FileReader();
          reader.onload = function (e) {
            const $preview = $('<div class="image-preview">')
              .append($('<img>').attr('src', e.target.result))
              .append($('<button class="remove-image" type="button">×</button>'));

            $additionalPreviewContainer.append($preview);

            // Handle remove button for new images
            $preview.find('.remove-image').on('click', function (e) {
              e.stopPropagation();
              $preview.remove();
            });
          };
          reader.readAsDataURL(file);
        });
      }

      // Handle remove button for existing images
      $additionalPreviewContainer.on('click', '.remove-image', function (e) {
        e.stopPropagation();
        const $preview = $(this).closest('.image-preview');
        const imageId = $preview.data('image-id');
        
        if (imageId) {
          // This is an existing image, mark it for deletion
          $preview.addClass('deleted');
          $preview.css('opacity', '0.5');
          $(this).prop('disabled', true);
          
          // Add to deleted images array
          if (!deletedImages.includes(imageId)) {
            deletedImages.push(imageId);
            $deletedImagesInput.val(JSON.stringify(deletedImages));
          }
        } else {
          // This is a new image, just remove it
          $preview.remove();
        }
      });

      // Handle form reset for additional images
      $('form').on('reset', function () {
        // Reset deleted images tracking
        deletedImages = [];
        $deletedImagesInput.val('[]');
        
        // Reset UI for existing images
        $additionalPreviewContainer.find('.image-preview').removeClass('deleted').css('opacity', '1');
        $additionalPreviewContainer.find('.remove-image').prop('disabled', false);
        
        // Remove any new images that were added
        $additionalPreviewContainer.find('.image-preview:not([data-image-id])').remove();
      });
    }
  });

  // Function to show order details
  window.showOrderDetails = function (order) {
    const $popup = $('#order-details-popup');
    const $grid = $('#order-products-grid');

    // Clear previous content
    $grid.empty();

    // Parse products string
    const products = order.products.split('||').map(p => {
      const [name, price, image, quantity] = p.split('|');
      return { name, price, image, quantity };
    });

    // Create grid items using jQuery
    products.forEach(product => {
      const $item = $('<div>', {
        class: 'order-product-item'
      }).append(
        $('<img>', {
          src: `/images/product/${product.image}`,
          alt: product.name
        }),
        $('<h3>').text(product.name),
        $('<p>').text(`Quantity: ${product.quantity}`),
        $('<p>').text(`RM ${parseFloat(product.price).toFixed(2)}`)
      );

      $grid.append($item);
    });

    $popup.show();
  };

  // Close popup when clicking the close button
  $('.close-popup').on('click', function () {
    $('#order-details-popup').hide();
  });

  // Close popup when clicking outside
  $(window).on('click', function (event) {
    if ($(event.target).is('#order-details-popup')) {
      $('#order-details-popup').hide();
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
    const productID = form.find('input[name="product_id"]').val();
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
    const button = $(this);
    const id = button.data('id');
    const action = button.data('action');
    const imageSrc = button.data('image'); // Get image source from data attribute
    const url = '/page/member/product.php';

    if (imageSrc) {
      // Create image element with the source
      const imgClone = $('<img>')
        .attr('src', imageSrc)
        .css({
          'position': 'absolute',
          'z-index': '100',
          'width': '100px',
          'height': '100px',
          'top': button.offset().top,
          'left': button.offset().left,
          'transition': 'all 1s ease-in-out',
          'border-radius': '50%',
          'object-fit': 'cover'
        })
        .appendTo('body');

      // Animate the clone to the cart
      setTimeout(() => {
        const cartButton = $('#cart-total-item-menu');
        imgClone.css({
          'width': '30px',
          'height': '30px',
          'top': cartButton.offset().top,
          'left': cartButton.offset().left,
          'opacity': '0'
        });

        // Remove the clone after animation
        setTimeout(() => {
          imgClone.remove();
        }, 1000);
      }, 100);
    }

    $.ajax({
      url: url,
      type: 'POST',
      data: {
        id: id,
        ajax: 'true',
        action: action,
        unit: 1  // Default quantity of 1 for bestseller products
      },
      success: function (data) {
        if (data.success) {
          if (action === 'cart') {
            // Update cart count in menu
            const cartCount = parseInt(data.cart_count) || 0;
            $('#cart-total-item-menu').text('(' + cartCount + ')');

            // Update cart total price if available
            if (data.total) {
              $('#cart-total-price').text(data.total);
            }
          } else if (action === 'wishlist') {
            // Update wishlist count in menu
            const wishlistCount = parseInt(data.wishlist_count) || 0;
            $('#wishlist-total-item-menu').text('(' + wishlistCount + ')');
          }
        }
      },
      error: function (xhr, status, error) {
        console.log('Error:', error);
        console.log('Status:', status);
        console.log('XHR:', xhr);
        alert('An error occurred. Please try again.');
      }
    });
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
  $('#table-view-button').on('click', function () {
    localStorage.setItem('userView', 'table');
    setView('table');
  });

  // Event listener for photo view button
  $('#photo-view-button').on('click', function () {
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
  $('.user-info-container').on('click', function () {
    openNav();
  });

  // Close the sidebar when the close button is clicked
  $('.closebutton').on('click', function () {
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
      function () {
        const contentCount = $('#dropdown_content a').length;
        $('#dropdown_content').css('height', (contentCount * 100) + '%');
        $('#dropdown_content').css('transition', 'height 0.3s');
      },
      function () {
        $('#dropdown_content').css('height', '0px');
        $('#dropdown_content').css('transition', 'height 0.3s');
      }
    );
  }

  // FAQ dropdown
  function initFAQDropdown() {
    $('.faq_q').on('click', function () {
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