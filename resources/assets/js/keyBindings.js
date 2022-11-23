const focusSearch = event => {
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        event.preventDefault();
        searchInput.focus();
    }
};

const openFirstNonHiddenItem = event => {
    if (event.target !== document.querySelector('input[name="q"]')) {
        return;
    }

    const item = document.querySelector('#sortable section.item-container:not([style="display: none;"]) a');

    if ('href' in item) {
        event.preventDefault();
        window.open(item.href);
    }
};

const KEY_BINDINGS = {
    '/': focusSearch,
    'Enter': openFirstNonHiddenItem
};

document.addEventListener('keydown', function (event) {
    try {
        if (event.key in KEY_BINDINGS) {
            KEY_BINDINGS[event.key](event);
        }
    } catch (e) {

    }
});