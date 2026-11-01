// Quick launcher: Cmd/Ctrl+K (or the button on touch devices) opens an overlay
// that finds any app, pinned or not and on any page, and can fall back to a web
// search. The markup and strings come from resources/views/partials/quicklauncher.
const root = document.getElementById("quicklauncher");

if (root) {
  const base = (document.querySelector("base") || {}).href || "/";
  const input = root.querySelector(".quicklauncher-input");
  const list = root.querySelector(".quicklauncher-list");
  const button = document.getElementById("quicklauncher-button");
  const { user, loading, error, noResults, searchWeb } = root.dataset;
  const searchIcon = document.getElementById("quicklauncher-search-icon");

  // Cmd on Apple platforms, where Ctrl+K is the system "kill line" shortcut
  const isApple = /Mac|iPhone|iPad/.test(navigator.platform);

  // Per Heimdall user, as users can share a browser
  const RECENT_KEY = `heimdall.quicklauncher.recent.${user}`;
  const RECENT_MAX = 8;
  const RESULTS_MAX = 8;

  let data = null; // { items, fallback, target } once /search/items has loaded
  let status = "idle"; // idle | loading | loaded | failed (failed retries on open)
  let opener = null; // what had focus before the launcher opened
  let results = [];
  let selected = 0;

  const isOpen = () => root.classList.contains("open");

  const el = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  };

  // localStorage can be missing or throw (private windows, blocked storage),
  // and the launcher must still work without it.
  const loadRecent = () => {
    try {
      const ids = JSON.parse(localStorage.getItem(RECENT_KEY));
      return Array.isArray(ids) ? ids : [];
    } catch (e) {
      return [];
    }
  };

  const saveRecent = (id) => {
    try {
      const ids = [id, ...loadRecent().filter((x) => x !== id)];
      localStorage.setItem(
        RECENT_KEY,
        JSON.stringify(ids.slice(0, RECENT_MAX))
      );
    } catch (e) {
      // Nothing to do
    }
  };

  const score = (item, q) => {
    const title = item.title.toLowerCase();
    if (title === q) return 100;
    if (title.startsWith(q)) return 80;
    if (title.split(/[\s._-]+/).some((word) => word.startsWith(q))) return 60;
    if (title.includes(q)) return 40;
    if (item.tags.some((tag) => tag.toLowerCase().includes(q))) return 20;
    if ((item.url || "").toLowerCase().includes(q)) return 10;
    return 0;
  };

  const search = (q) => {
    const query = q.trim().toLowerCase();
    const items = data ? data.items : [];
    if (!query) {
      // Recently opened first (apps since deleted are skipped), then the rest
      // in dashboard order.
      const recent = loadRecent()
        .map((id) => items.find((item) => item.id === id))
        .filter(Boolean);
      return recent.concat(items.filter((item) => !recent.includes(item)));
    }
    return items
      .map((item) => ({ item, s: score(item, query) }))
      .filter((r) => r.s > 0)
      .sort((a, b) => b.s - a.s)
      .map((r) => r.item)
      .slice(0, RESULTS_MAX);
  };

  const close = (restoreFocus = true) => {
    root.classList.remove("open");
    input.setAttribute("aria-expanded", "false");
    input.blur();
    if (restoreFocus && opener) opener.focus();
    opener = null;
  };

  // Follow a link the way a tile does, so the link-target setting behaves the
  // same (including reusing a named window).
  const go = (url, target) => {
    close(false);
    const link = el("a");
    link.href = url;
    link.target = target;
    link.rel = "noopener noreferrer";
    // Attached while clicking: some browsers ignore clicks on detached links.
    document.body.appendChild(link);
    link.click();
    link.remove();
  };

  const activate = () => {
    const row = results[selected];
    if (!row) return;
    if (row.type === "app") {
      saveRecent(row.item.id);
      go(row.item.url, data.target);
    } else {
      const { fallback } = data;
      const q = encodeURIComponent(input.value.trim());
      go(
        `${fallback.url}?provider=${encodeURIComponent(
          fallback.provider
        )}&q=${q}`,
        data.target
      );
    }
  };

  const select = (index) => {
    selected = index;
    list.querySelectorAll(".quicklauncher-row").forEach((row, i) => {
      row.classList.toggle("active", i === index);
      row.setAttribute("aria-selected", i === index);
      if (i === index) input.setAttribute("aria-activedescendant", row.id);
    });
  };

  const appRow = (item) => {
    const icon = el("span", "quicklauncher-icon");
    icon.style.backgroundColor = item.colour || "";
    const img = el("img");
    img.src = item.icon;
    img.alt = "";
    icon.appendChild(img);
    return [
      icon,
      el("span", "quicklauncher-title", item.title),
      ...(item.tags.length
        ? [el("span", "quicklauncher-tags", item.tags.join(", "))]
        : []),
    ];
  };

  const webRow = (fallback, q) => {
    const icon = el("span", "quicklauncher-icon");
    icon.appendChild(searchIcon.content.cloneNode(true));
    // Function replacers: the query is user input and may contain "$" patterns.
    const text = searchWeb
      .replace(":provider", () => fallback.name)
      .replace(":query", () => q);
    return [icon, el("span", "quicklauncher-title", text)];
  };

  const render = () => {
    const q = input.value.trim();
    results = search(q).map((item) => ({ type: "app", item }));
    if (q && data && data.fallback) results.push({ type: "web" });
    selected = Math.min(selected, Math.max(results.length - 1, 0));
    list.textContent = "";
    input.removeAttribute("aria-activedescendant");

    if (!results.length) {
      const messages = { loaded: noResults, failed: error };
      list.appendChild(
        el("div", "quicklauncher-empty", messages[status] || loading)
      );
      return;
    }

    results.forEach((row, i) => {
      const li = el("div", "quicklauncher-row");
      li.id = `quicklauncher-option-${i}`;
      li.setAttribute("role", "option");
      (row.type === "app"
        ? appRow(row.item)
        : webRow(data.fallback, q)
      ).forEach((node) => li.appendChild(node));
      li.dataset.index = i;
      list.appendChild(li);
    });
    select(selected);
  };

  const load = () => {
    status = "loading";
    fetch(`${base}search/items`, {
      headers: { Accept: "application/json" },
      credentials: "same-origin",
    })
      .then((response) => {
        // A login redirect ends up as an HTML page, which is not usable.
        const type = response.headers.get("content-type") || "";
        if (!response.ok || !type.includes("json")) throw new Error();
        return response.json();
      })
      .then((json) => {
        data = json;
        status = "loaded";
      })
      .catch(() => {
        status = "failed";
      })
      .then(() => {
        if (isOpen()) render();
      });
  };

  const open = () => {
    // Reordering mode: tiles are being dragged, so stay out of the way
    if (document.getElementById("app").classList.contains("header")) return;
    if (status === "idle" || status === "failed") load();
    opener = document.activeElement;
    input.value = "";
    selected = 0;
    root.classList.add("open");
    input.setAttribute("aria-expanded", "true");
    render();
    input.focus();
  };

  root.addEventListener("mousedown", (e) => {
    if (e.target === root) close();
  });

  input.addEventListener("input", () => {
    selected = 0;
    render();
  });

  // One pair of listeners for all rows
  const rowIndex = (e) => {
    const row = e.target.closest(".quicklauncher-row");
    return row ? Number(row.dataset.index) : -1;
  };

  // Scrolling the list with the arrow keys moves rows under a resting pointer,
  // and some browsers report that as a mouse move. Only follow a real move.
  let pointer = { x: -1, y: -1 };
  list.addEventListener("mousemove", (e) => {
    if (e.clientX === pointer.x && e.clientY === pointer.y) return;
    pointer = { x: e.clientX, y: e.clientY };
    const i = rowIndex(e);
    if (i >= 0 && i !== selected) select(i);
  });

  list.addEventListener("click", (e) => {
    const i = rowIndex(e);
    if (i < 0) return;
    selected = i;
    activate();
  });

  button.appendChild(searchIcon.content.cloneNode(true));
  button.addEventListener("click", () => open());

  // Capture phase, so keys typed into the overlay never reach the page-level
  // handlers (keyBindings.js steals focus on "/", app.js hides things on Esc).
  document.addEventListener(
    "keydown",
    (e) => {
      if (
        (isApple ? e.metaKey : e.ctrlKey) &&
        !e.altKey &&
        (e.key || "").toLowerCase() === "k"
      ) {
        e.preventDefault();
        e.stopPropagation();
        if (isOpen()) close();
        else open();
        return;
      }
      if (!isOpen()) return;
      e.stopPropagation();
      if (e.key === "Tab") {
        // The input is the only control, so keep focus from leaving the dialog
        e.preventDefault();
      } else if (e.key === "Escape") {
        e.preventDefault();
        close();
      } else if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault();
        const n = results.length;
        if (!n) return;
        select((selected + (e.key === "ArrowDown" ? 1 : n - 1)) % n);
        list.querySelector(".active").scrollIntoView({ block: "nearest" });
      } else if (e.key === "Enter" && !e.isComposing) {
        e.preventDefault();
        activate();
      }
    },
    true
  );
}
