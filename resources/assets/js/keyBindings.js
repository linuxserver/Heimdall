const focusSearch = (event) => {
  const searchInput = document.querySelector('input[name="q"]');
  if (searchInput) {
    event.preventDefault();
    searchInput.focus();
  }
};

const openFirstNonHiddenItem = (event) => {
  if (event.target !== document.querySelector('input[name="q"]')) {
    return;
  }

  const providerSelect = document.querySelector(
    "#search-container select[name=provider]"
  );

  if (providerSelect.value !== "tiles") {
    return;
  }

  const item = document.querySelector(
    '#sortable section.item-container:not([style="display: none;"]) a'
  );

  if ("href" in item) {
    event.preventDefault();
    window.open(item.href);
  }
};

const KEY_BINDINGS = {
  "/": focusSearch,
  Enter: openFirstNonHiddenItem,
};

document.addEventListener("keydown", (event) => {
  try {
    if (event.key in KEY_BINDINGS) {
      KEY_BINDINGS[event.key](event);
    }
  } catch (e) {
    // Nothing to do
  }
});
