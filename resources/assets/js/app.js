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
  let sortable;
  if (sortableEl !== null) {
    // eslint-disable-next-line no-undef
    sortable = Sortable.create(sortableEl, {
      disabled: true,
      animation: 150,
      forceFallback: !(
        navigator.userAgent.toLowerCase().indexOf("firefox") > -1
      ),
      draggable: ".item-container",
      onEnd() {
        const idsInOrder = sortable.toArray();
        $.post(`${base}order`, { order: idsInOrder });
      },
    });
    // prevent Firefox drag behavior
    if (navigator.userAgent.toLowerCase().indexOf("firefox") > -1) {
      sortable.option("setData", (dataTransfer) => {
        dataTransfer.setData("Text", "");
      });

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
        if (sortable !== undefined) sortable.option("disabled", true);
      } else {
        $("#sortable .tooltip").css("display", "none");
        if (sortable !== undefined) sortable.option("disabled", false);
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
        data[config] = $(this).val();
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
