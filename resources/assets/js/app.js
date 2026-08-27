/* eslint-disable func-names */
$.when($.ready).then(() => {
  const base = (document.querySelector("base") || {}).href;

  const itemID = $("form[data-item-id]").data("item-id");
  const fakePassword = "*****";

  // If in edit mode and password field is present, fill it with stars
  if (itemID) {
    const passwordField = $('input[name="config[password]"]').first();

    if (passwordField.length > 0) {
      passwordField.attr("value", fakePassword);
    }
  }

  if ($(".message-container").length) {
    setTimeout(() => {
      $(".message-container").fadeOut();
    }, 3500);
  }

  function readURL(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();

      reader.onload = function (e) {
        $("#appimage img").attr("src", e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }

  $("#upload").change(function () {
    readURL(this);
  });
  /* $(".droppable").droppable({
        tolerance: "intersect",
        drop: function( event, ui ) {
            var tag = $( this ).data('id');
            var item = $( ui.draggable ).data('id');

            $.get('tag/add/'+tag+'/'+item, function(data) {
                if(data == 1) {
                    $( ui.draggable ).remove();
                } else {
                    alert('not added');
                }
            });

        }
      }); */

  const sortableEl = document.getElementById("sortable");
  const sortables = [];
  if (sortableEl !== null) {
    const isFirefox = navigator.userAgent.toLowerCase().indexOf("firefox") > -1;
    const createSortable = (el, draggable, handle) => {
      // eslint-disable-next-line no-undef
      const instance = Sortable.create(el, {
        disabled: true,
        animation: 150,
        forceFallback: !isFirefox,
        draggable,
        handle,
        onEnd(evt) {
          // eslint-disable-next-line no-undef
          $.post(`${base}order`, { order: Sortable.get(evt.to).toArray() });
        },
      });
      // prevent Firefox drag behavior
      if (isFirefox) {
        instance.option("setData", (dataTransfer) => {
          dataTransfer.setData("Text", "");
        });
      }
      sortables.push(instance);
    };

    // In categories mode the items are nested inside .category blocks, so
    // the categories get their own sortable (dragged by their title bar) and
    // each category sorts its own items. The item sortables deliberately
    // share no `group`: moving an item between categories is a tag change
    // that /order cannot express.
    const categoryEls = Array.from(sortableEl.querySelectorAll(".category"));
    if (categoryEls.length > 0) {
      createSortable(sortableEl, ".category", ".category > .title");
    }
    (categoryEls.length > 0 ? categoryEls : [sortableEl]).forEach((el) => {
      createSortable(el, ".item-container");
    });

    if (isFirefox) {
      sortableEl.addEventListener("dragstart", (event) => {
        const { target } = event;
        if (target.nodeName.toLowerCase() === "a") {
          event.preventDefault();
          event.stopPropagation();
          event.dataTransfer.setData("Text", "");
        }
      });
    }
  }
  const setSortableDisabled = (disabled) => {
    sortables.forEach((instance) => instance.option("disabled", disabled));
  };

  $("#main")
    .on("mouseenter", "#sortable .item", function () {
      $(this).siblings(".tooltip").addClass("active");
      $(".refresh", this).addClass("active");
    })
    .on("mouseleave", ".item", function () {
      $(this).siblings(".tooltip").removeClass("active");
      $(".refresh", this).removeClass("active");
    });
  $("#config-buttons")
    .on("mouseenter", "a", function () {
      $(".tooltip", this).addClass("active");
    })
    .on("mouseleave", "a", function () {
      $(".tooltip", this).removeClass("active");
    });

  $(".searchform > form").on("submit", (event) => {
    if ($("#search-container select[name=provider]").val() === "tiles") {
      event.preventDefault();
    }
  });

  // Autocomplete functionality
  let autocompleteTimeout = null;
  let currentAutocompleteRequest = null;

  function hideAutocomplete() {
    $("#search-autocomplete").remove();
  }

  function showAutocomplete(suggestions, inputElement) {
    hideAutocomplete();

    if (!suggestions || suggestions.length === 0) {
      return;
    }

    const $input = $(inputElement);
    const position = $input.position();
    const width = $input.outerWidth();

    const $autocomplete = $('<div id="search-autocomplete"></div>');

    suggestions.forEach((suggestion) => {
      const $item = $('<div class="autocomplete-item"></div>')
        .text(suggestion)
        .on("click", () => {
          $input.val(suggestion);
          hideAutocomplete();
          $input.closest("form").submit();
        });
      $autocomplete.append($item);
    });

    $autocomplete.css({
      position: "absolute",
      top: `${position.top + $input.outerHeight()}px`,
      left: `${position.left}px`,
      width: `${width}px`,
    });

    $input.closest("#search-container").append($autocomplete);
  }

  function fetchAutocomplete(query, provider) {
    // Cancel previous request if any
    if (currentAutocompleteRequest) {
      currentAutocompleteRequest.abort();
    }

    if (!query || query.trim().length < 2) {
      hideAutocomplete();
      return;
    }

    currentAutocompleteRequest = $.ajax({
      url: `${base}search/autocomplete`,
      method: "GET",
      data: {
        q: query,
        provider,
      },
      success(data) {
        const inputElement = $("#search-container input[name=q]")[0];
        showAutocomplete(data, inputElement);
      },
      error() {
        hideAutocomplete();
      },
      complete() {
        currentAutocompleteRequest = null;
      },
    });
  }

  $("#search-container")
    .on("input", "input[name=q]", function () {
      const search = this.value;
      const items = $("#sortable").find(".item-container");
      // Get provider from either select or hidden input
      const provider =
        $("#search-container select[name=provider]").val() ||
        $("#search-container input[name=provider]").val();

      if (provider === "tiles") {
        hideAutocomplete();
        if (search.length > 0) {
          items.hide();
          items
            .filter(function () {
              const name = $(this).data("name").toLowerCase();
              return name.includes(search.toLowerCase());
            })
            .show();
        } else {
          items.show();
        }
      } else {
        items.show();

        // Debounce autocomplete requests
        clearTimeout(autocompleteTimeout);
        autocompleteTimeout = setTimeout(() => {
          fetchAutocomplete(search, provider);
        }, 300);
      }
    })
    .on("change", "select[name=provider]", function () {
      const items = $("#sortable").find(".item-container");
      if ($(this).val() === "tiles") {
        $("#search-container button").hide();
        const search = $("#search-container input[name=q]").val();
        if (search.length > 0) {
          items.hide();
          items
            .filter(function () {
              const name = $(this).data("name").toLowerCase();
              return name.includes(search.toLowerCase());
            })
            .show();
        } else {
          items.show();
        }
      } else {
        $("#search-container button").show();
        items.show();
        hideAutocomplete();
      }
    });

  // Hide autocomplete when clicking outside
  $(document).on("click", (e) => {
    if (!$(e.target).closest("#search-container").length) {
      hideAutocomplete();
    }
  });

  // Hide autocomplete on Escape key
  $(document).on("keydown", (e) => {
    if (e.key === "Escape") {
      hideAutocomplete();
    }
  });

  $("#search-container select[name=provider]").trigger("change");

  $("#app")
    .on("click", "#config-button", (e) => {
      e.preventDefault();
      const app = $("#app");
      const active = app.hasClass("header");
      app.toggleClass("header");
      if (active) {
        $(".add-item").hide();
        $(".item-edit").hide();
        $("#app").removeClass("sidebar");
        $("#sortable .tooltip").css("display", "");
        setSortableDisabled(true);
      } else {
        $("#sortable .tooltip").css("display", "none");
        setSortableDisabled(false);
        setTimeout(() => {
          $(".add-item").fadeIn();
          $(".item-edit").fadeIn();
        }, 350);
      }
    })
    .on("click", ".tag", (e) => {
      e.preventDefault();
      const tag = $(e.target).data("tag");
      $("#taglist .tag").removeClass("current");
      $(e.target).addClass("current");
      $("#sortable .item-container").show();
      if (tag !== "all") {
        $(`#sortable .item-container:not(.${tag})`).hide();
      }
    })
    .on("click", "#add-item, #pin-item", (e) => {
      e.preventDefault();
      const app = $("#app");
      // const active = app.hasClass("sidebar");
      app.toggleClass("sidebar");
    })
    .on("click", ".close-sidenav", (e) => {
      e.preventDefault();
      const app = $("#app");
      app.removeClass("sidebar");
    })
    .on("click", "#test_config", (e) => {
      e.preventDefault();
      let apiurl = $("#create input[name=url]").val();

      const overrideUrl = $(
        '#sapconfig input[name="config[override_url]"]'
      ).val();

      if (typeof overrideUrl === "string" && overrideUrl !== "") {
        apiurl = overrideUrl;
      }

      const data = {};
      data.url = apiurl;
      $(".config-item").each(function () {
        const config = $(this).data("config");
        // For checkboxes, use checked state instead of value attribute
        if ($(this).is(":checkbox")) {
          data[config] = $(this).is(":checked") ? "1" : "0";
        } else {
          data[config] = $(this).val();
        }
      });

      data.id = $("form[data-item-id]").data("item-id");

      if (data.password && data.password === fakePassword) {
        data.password = "";
      }

      $.post(`${base}test_config`, { data })
        .done((responseData) => {
          // eslint-disable-next-line no-alert
          alert(responseData);
        })
        .fail((responseData) => {
          // eslint-disable-next-line no-alert
          alert(
            `Something went wrong: ${responseData.responseText.substring(
              0,
              100
            )}`
          );
        });
    });
  // Auto-select the configured default tag on load (tags mode only)
  const taglist = document.getElementById("taglist");
  if (taglist !== null) {
    const defaultTag = taglist.getAttribute("data-default-tag");
    if (typeof defaultTag === "string" && defaultTag !== "") {
      $(`#taglist .tag[data-tag="tag-${defaultTag}"]`).trigger("click");
    }
  }

  $("#pinlist").on("click", "a", function (e) {
    e.preventDefault();
    const current = $(this);
    const id = current.data("id");
    const tag = current.data("tag");
    $.get(`${base}items/pintoggle/${id}/true/${tag}`, (data) => {
      const inner = $(data).filter("#sortable").html();
      $("#sortable").html(inner);
      current.toggleClass("active");
    });
  });
  $("#itemform").on("submit", () => {
    const passwordField = $('input[name="config[password]"]').first();
    if (passwordField.length > 0) {
      if (passwordField.attr("value") === fakePassword) {
        passwordField.attr("value", "");
      }
    }
  });
});
