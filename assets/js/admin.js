/**
* Template Name: ct4gg - Admin
*/
(function() {
    "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Admin Tab
   */
  on('click', 'ul.stats4wp-nav-tabs > li', function(e) {
    e.preventDefault(),
	document.querySelector("ul.stats4wp-nav-tabs li.active").classList.remove("active"),
		document.querySelector(".stats4wp-tab-pane.active").classList.remove("active");
	var t=e.currentTarget,
		n=e.target.getAttribute("href");
	t.classList.add("active"),
		document.querySelector(n).classList.add("active");
  }, true)

  /**
   * Flag Interval
   */
   on('click', '#stats4wp-interval-flag', function(e) {
    var flag = document.querySelector('#stats4wp-interval-flag');
    var interval = document.querySelector('#stats4wp-interval');
    var from = document.querySelector('#stats4wp-from');
    if(flag.checked) {
      from.setAttribute("disabled", "disabled");
      interval.removeAttribute("disabled");
    } else {
      from.removeAttribute("disabled");
      interval.setAttribute("disabled", "disabled");
    }

  }, true)


})();
var acc = document.getElementsByClassName("stats4wp-accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("stats4wp-active");

    /* Toggle between hiding and showing the active panel */
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}

jQuery(document).ready(function () {
  // Send recommendations dismiss request.
  jQuery('#stats4wp-dismiss-geochart .notice-dismiss').on('click', function () {
      jQuery.ajax({
          method: 'GET',
          url: ajaxurl,
          data: { action: 'stats4wp_remove_geochart' },
          dataType: 'json'
      }).always(function (response) {
          if (!response.hasOwnProperty('data') || !response.data.notice_removed) {
              console.log(response);
              console.error('Can not remove notice. Please contact WPES support.');
          }
      });
  });
});