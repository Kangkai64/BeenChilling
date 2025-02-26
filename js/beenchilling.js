//Top button script

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  const top = document.getElementById("top");
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    top.style.display = "block";
  } else {
    top.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

// Nav dropdown script

function dropDownHover() {
  const
  dropdown = document.getElementById("dropdown"),
  innerDropdown = document.getElementById("dropdown_content"),
  wrapper = document.getElementById("dropdown_wrapper");

  if (dropdown.matches(':hover')) {
      innerDropdown.style.height = wrapper.offsetHeight + "px"
  }
  else {
      innerDropdown.style.height = "0px"
  }
  setTimeout(dropDownHover, 10) // Calls itself every 10 milisecond
}

// FAQ dropdown script

let addedHeight = 0;

function FAQdropDown(FAQnum) {
  const innerDropdown = document.getElementById("faq" + FAQnum),
  wrapper = document.getElementById("faq" + FAQnum + "_wrapper");

  if (innerDropdown.style.height == "0px" || innerDropdown.style.height == 0) {
    innerDropdown.style.height = wrapper.offsetHeight + "px"
  }
  else {
    innerDropdown.style.height = "0px"
  }
}

// Events display button script
function displayEvent(className) {
  for (i = 0; i < document.getElementsByClassName("new").length; i++) {
    document.getElementsByClassName("new")[i].style.display = "none"
  }

  for (i = 0; i < document.getElementsByClassName("old").length; i++) {
    document.getElementsByClassName("old")[i].style.display = "none"
  }

  for (i = 0; i < document.getElementsByClassName("future").length; i++) {
    document.getElementsByClassName("future")[i].style.display = "none"
  }

  for (i = 0; i < document.getElementsByClassName(className).length; i++) {
    document.getElementsByClassName(className)[i].style.display = "block"
  }

  for (i = document.getElementsByClassName("topics_nav_active").length; i != 0; i--) {
    document.getElementsByClassName("topics_nav_active")[0].classList.remove("topics_nav_active")
  }
  document.getElementById("topics_" + className).classList.add("topics_nav_active")
}

//Change image on hover

function changeImage(x,image){
  if (x==1){
    image.src = "reviews/review_4_like.jpg"
  }
  if (x==2){
    image.src = "reviews/review_4.jpg"
  }
}

// Set active navigation

document.addEventListener('DOMContentLoaded', function() {
  // Get current page URL
  const currentPage = window.location.pathname;
  const currentHash = window.location.hash;
  
  $(function() {
      $('nav ul li a').each(function() {
          const linkHref = $(this).attr('href');
          
          if (currentPage === linkHref) {
              // Remove active_link class from all list items
              $('nav ul li a').removeClass('active_link');
              // Add active_link class to parent list item
              $(this).addClass('active_link');
          }
      });
      
      // Handle click events for navigation
      $('nav ul li a').on('click', function() {
          // Remove active class from all items
          $('nav ul li a').removeClass('active_link');
          // Add active class to parent of clicked link
          $(this).addClass('active_link');
      });
  });
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