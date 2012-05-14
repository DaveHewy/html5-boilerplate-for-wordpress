jQuery(document).ready(function(){

  // scroll spy logic
  // ================

  var activeTarget,
      $window = jQuery(window),
      position = {},
      nav = jQuery('body > .topbar li a'),
      targets = nav.map(function () {
        return jQuery(this).attr('href');
      }),
      offsets = jQuery.map(targets, function (id) {
        return jQuery(id).offset().top;
      });


  function setButton(id) {
    nav.parent("li").removeClass('active');
    $(nav[jQuery.inArray(id, targets)]).parent("li").addClass('active');
  }

  function processScroll(e) {
    var scrollTop = $window.scrollTop() + 10, i;
    for (i = offsets.length; i--;) {
      if (activeTarget != targets[i] && scrollTop >= offsets[i] && (!offsets[i + 1] || scrollTop <= offsets[i + 1])) {
        activeTarget = targets[i];
        setButton(activeTarget);
      }
    }
  }

  nav.click(function () {
    processScroll();
  });

  processScroll();

  $window.scroll(processScroll);


  // Dropdown example for topbar nav
  // ===============================

  jQuery("body").bind("click", function (e) {
    jQuery('a.menu').parent("li").removeClass("open");
  });

  jQuery("a.menu").click(function (e) {
    var $li = jQuery(this).parent("li").toggleClass('open');
    return false;
  });


  // table sort example
  // ==================

  jQuery("#sortTableExample").tablesorter( {sortList: [[1,0]]} );


  // add on logic
  // ============

  jQuery('.add-on :checkbox').click(function() {
    if (jQuery(this).attr('checked')) {
      jQuery(this).parents('.add-on').addClass('active');
    } else {
      jQuery(this).parents('.add-on').removeClass('active');
    }
  });


  // Disable certain links in docs
  // =============================

  jQuery('ul.tabs a, ul.pills a, .pagination a, .well .btn, .actions .btn, .alert-message .btn, a.close').click(function(e) {
    e.preventDefault();
  });

  // Copy code blocks in docs
  jQuery(".copy-code").focus(function() {
    var el = this;
    // push select to event loop for chrome :{o
    setTimeout(function () { jQuery(el).select(); }, 1);
  });


  // POSITION TWIPSIES
  // =================

	jQuery('.twipsies.well a').each(function () {
    var type = this.title
      , $anchor = jQuery(this)
      , $twipsy = jQuery('.twipsy.' + type)

      , twipsy = {
          width: $twipsy.width() + 10
        , height: $twipsy.height() + 10
        }

      , anchor = {
          position: $anchor.position()
        , width: $anchor.width()
        , height: $anchor.height()
        }

      , offset = {
          above: {
            top: anchor.position.top - twipsy.height
          , left: anchor.position.left + (anchor.width/2) - (twipsy.width/2)
          }
        , below: {
            top: anchor.position.top + anchor.height
          , left: anchor.position.left + (anchor.width/2) - (twipsy.width/2)
          }
        , left: {
            top: anchor.position.top + (anchor.height/2) - (twipsy.height/2)
          , left: anchor.position.left - twipsy.width - 5
          }
        , right: {
            top: anchor.position.top + (anchor.height/2) - (twipsy.height/2)
          , left: anchor.position.left + anchor.width + 5
          }
      }

    $twipsy.css(offset[type])

  });

});