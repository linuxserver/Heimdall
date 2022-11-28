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

  // from https://developer.mozilla.org/en-US/docs/Web/API/Page_Visibility_API
  // Set the name of the hidden property and the change event for visibility
  let hidden;
  let visibilityChange;
  if (typeof document.hidden !== "undefined") {
    // Opera 12.10 and Firefox 18 and later support
    hidden = "hidden";
    visibilityChange = "visibilitychange";
  } else if (typeof document.msHidden !== "undefined") {
    hidden = "msHidden";
    visibilityChange = "msvisibilitychange";
  } else if (typeof document.webkitHidden !== "undefined") {
    hidden = "webkitHidden";
    visibilityChange = "webkitvisibilitychange";
  }

  const livestatsRefreshTimeouts = [];
  const livestatsFuncs = [];
  const livestatsContainers = $(".livestats-container");
  function stopLivestatsRefresh() {
    livestatsRefreshTimeouts.forEach((timeoutId) => {
      window.clearTimeout(timeoutId);
    });
  }
  function startLivestatsRefresh() {
    livestatsFuncs.forEach((fun) => {
      fun();
    });
  }

  if (livestatsContainers.length > 0) {
    if (
      typeof document.addEventListener === "undefined" ||
      hidden === undefined
    ) {
      console.log("This browser does not support visibilityChange");
    } else {
      document.addEventListener(
        visibilityChange,
        () => {
          if (document[hidden]) {
            stopLivestatsRefresh();
          } else {
            startLivestatsRefresh();
          }
        },
        false
      );
    }

    livestatsContainers.each(function (index) {
      const id = $(this).data("id");
      const dataonly = $(this).data("dataonly");
      const increaseby = dataonly === 1 ? 20000 : 1000;
      const container = $(this);
      const maxTimer = 30000;
      let timer = 5000;
      const fun = function worker() {
        $.ajax({
          url: `${base}get_stats/${id}`,
          dataType: "json",
          success(data) {
            container.html(data.html);
            if (data.status === "active") timer = increaseby;
            else if (timer < maxTimer) timer += 2000;
          },
          complete(jqXHR) {
            if (jqXHR.status > 299) {
              // Stop polling when we get errors
              return;
            }

            // Schedule the next request when the current one's complete
            livestatsRefreshTimeouts[index] = window.setTimeout(worker, timer);
          },
        });
      };
      livestatsFuncs[index] = fun;
      fun();
    });
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

  $("#sortable").sortable({
    stop() {
      const idsInOrder = $("#sortable").sortable("toArray", {
        attribute: "data-id",
      });
      $.post(`${base}order`, { order: idsInOrder });
    },
  });
  $("#sortable").sortable("disable");

  $("#main")
    .on("mouseenter", "#sortable.ui-sortable-disabled .item", function () {
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

  $("#search-container")
    .on("input", "input[name=q]", function () {
      const search = this.value;
      const items = $("#sortable").children(".item-container");
      if ($("#search-container select[name=provider]").val() === "tiles") {
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
      }
    })
    .on("change", "select[name=provider]", function () {
      const items = $("#sortable").children(".item-container");
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
      }
    });

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
        $("#sortable").sortable("disable");
      } else {
        $("#sortable .tooltip").css("display", "none");
        $("#sortable").sortable("enable");
        setTimeout(() => {
          $(".add-item").fadeIn();
          $(".item-edit").fadeIn();
        }, 350);
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
      if (overrideUrl.length && overrideUrl !== "") {
        apiurl = overrideUrl;
      }

      const data = {};
      data.url = apiurl;
      $(".config-item").each(function () {
        const config = $(this).data("config");
        data[config] = $(this).val();
      });

      data.id = $("form[data-item-id]").data("item-id");

      if (data.password && data.password === fakePassword) {
        data.password = "";
      }

      $.post(`${base}test_config`, { data }, (responseData) => {
        alert(responseData);
      });
    });
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
