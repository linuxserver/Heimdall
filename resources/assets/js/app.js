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
  if (sortableEl !== null && typeof Sortable !== "undefined") {
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

  const TAG_USAGE_STORAGE_KEY = "heimdall.tagUsageHistory";
  const TAG_USAGE_WINDOW = 100;
  const ALL_TAG = "all";

  function getTagUsageHistory() {
    try {
      const raw = JSON.parse(localStorage.getItem(TAG_USAGE_STORAGE_KEY) || "[]");
      if (Array.isArray(raw)) {
        return raw.filter((entry) => typeof entry === "string");
      }

      // Legacy fallback: old object-based counters.
      if (raw && typeof raw === "object") {
        const expanded = [];
        Object.keys(raw).forEach((tag) => {
          const count = Number(raw[tag] || 0);
          for (let i = 0; i < count; i += 1) {
            expanded.push(tag);
          }
        });
        return expanded.slice(-TAG_USAGE_WINDOW);
      }
    } catch (_error) {
      return [];
    }

    return [];
  }

  function saveTagUsageHistory(history) {
    try {
      localStorage.setItem(
        TAG_USAGE_STORAGE_KEY,
        JSON.stringify(history.slice(-TAG_USAGE_WINDOW))
      );
    } catch (_error) {
      // Ignore localStorage failures and continue with default ordering.
    }
  }

  function getTagUsageCounts() {
    const history = getTagUsageHistory();
    const counts = {};

    history.forEach((tag) => {
      counts[tag] = (counts[tag] || 0) + 1;
    });

    return counts;
  }

  function updateTagButtonCounts() {
    const items = $("#sortable").find(".item-container");
    const provider = $("#search-container select[name=provider]").val();
    const search = (
      $("#search-container input[name=q]").val() || ""
    ).toLowerCase();

    const datasetItems =
      provider === "tiles"
        ? items.filter(function () {
            const name = String($(this).data("name") || "").toLowerCase();
            return name.includes(search);
          })
        : items;

    $("#taglist .tag").each(function () {
      const button = $(this);
      const tag = button.data("tag");

      if (button.data("base-text") === undefined) {
        const baseText = button
          .text()
          .replace(/\s*\(\d+\)\s*$/, "")
          .trim();
        button.data("base-text", baseText);
      }

      const baseText = button.data("base-text");
      const count =
        tag === ALL_TAG
          ? datasetItems.length
          : datasetItems.filter(function () {
              const item = $(this);
              if (String(tag).startsWith("cat-")) {
                return item.closest(".category").hasClass(tag);
              }
              return item.hasClass(tag);
            }).length;

      button.text(`${baseText} (${count})`);
    });
  }

  function reorderTagButtonsByUsage() {
    const taglist = $("#taglist");
    if (taglist.length === 0) {
      return;
    }

    const usage = getTagUsageCounts();
    const buttons = taglist.find(".tag").get();

    buttons.sort((firstButton, secondButton) => {
      const first = $(firstButton);
      const second = $(secondButton);
      const firstTag = String(first.data("tag") || "");
      const secondTag = String(second.data("tag") || "");

      if (firstTag === ALL_TAG) return -1;
      if (secondTag === ALL_TAG) return 1;

      const usageDelta = (usage[secondTag] || 0) - (usage[firstTag] || 0);
      if (usageDelta !== 0) {
        return usageDelta;
      }

      return (first.data("order-index") || 0) - (second.data("order-index") || 0);
    });

    buttons.forEach((button) => {
      taglist.append(button);
    });
  }

  function applyTileFilters() {
    const sortable = $("#sortable");
    const items = sortable.find(".item-container");
    const categoryWrappers = sortable.find(".category");
    const categoryTitles = sortable.find(".category > .title");
    const provider = $("#search-container select[name=provider]").val();
    const search = (
      $("#search-container input[name=q]").val() || ""
    ).toLowerCase();
    const selectedTag =
      String($("#taglist .tag.current").first().data("tag") || ALL_TAG) || ALL_TAG;

    if (provider !== "tiles") {
      items.show();
      categoryWrappers.show();
      categoryTitles.show();
      updateTagButtonCounts();
      return;
    }

    items.hide();
    items
      .filter(function () {
        const item = $(this);
        const name = String(item.data("name") || "").toLowerCase();
        const matchesSearch = search.length === 0 || name.includes(search);
        const matchesTag =
          selectedTag === ALL_TAG ||
          (String(selectedTag).startsWith("cat-")
            ? item.closest(".category").hasClass(selectedTag)
            : item.hasClass(selectedTag));
        return matchesSearch && matchesTag;
      })
      .show();

    if (categoryWrappers.length > 0) {
      // Reset wrapper visibility first so cross-category switching works reliably.
      categoryWrappers.show();

      categoryWrappers.each(function () {
        const wrapper = $(this);
        const matchingChildren = wrapper
          .find(".item-container")
          .filter(function () {
            return $(this).css("display") !== "none";
          }).length;
        wrapper.toggle(matchingChildren > 0);
      });

      const isFiltered = selectedTag !== ALL_TAG || search.length > 0;
      if (isFiltered) {
        categoryTitles.hide();
      } else {
        categoryTitles.show();
      }
    }

    updateTagButtonCounts();
  }

  $("#taglist .tag").each(function (index) {
    $(this).data("order-index", index);
  });

  reorderTagButtonsByUsage();

  $(".searchform > form").on("submit", (event) => {
    if ($("#search-container select[name=provider]").val() === "tiles") {
      event.preventDefault();
    }
  });

  $("#search-container")
    .on("input", "input[name=q]", () => {
      applyTileFilters();
    })
    .on("change", "select[name=provider]", function () {
      if ($(this).val() === "tiles") {
        $("#search-container button").hide();
      } else {
        $("#search-container button").show();
      }
      applyTileFilters();
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
      const tagButton = $(e.currentTarget);
      const tag = String(tagButton.data("tag") || ALL_TAG);
      $("#taglist .tag").removeClass("current");
      tagButton.addClass("current");

      if (tag !== ALL_TAG) {
        const usageHistory = getTagUsageHistory();
        usageHistory.push(tag);
        saveTagUsageHistory(usageHistory);
      }

      reorderTagButtonsByUsage();
      applyTileFilters();
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
      applyTileFilters();
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
