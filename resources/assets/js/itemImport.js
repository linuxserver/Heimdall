const IMPORT_API_URL = "api/item";
const APP_LOAD_URL = "appload";

/**
 *
 * @param {string|null} appId
 * @returns {Promise<{}>|Promise<any>}
 */
const fetchAppDetails = (appId) => {
  if (appId === null) {
    return Promise.resolve({});
  }

  return fetch(APP_LOAD_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ app: appId }),
  })
    .then((response) => response.json())
    .catch(() => ({}));
};

/**
 *
 * @returns {string}
 */
const getCSRFToken = () => {
  const tokenSelector = 'input[name="_token"]';
  return document.querySelector(tokenSelector).value;
};

/**
 *
 * @param {object} data
 * @param {string} csrfToken
 */
const postToApi = (data, csrfToken) =>
  fetch(IMPORT_API_URL, {
    method: "POST",
    cache: "no-cache",
    redirect: "follow",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": csrfToken,
    },
    body: JSON.stringify(data),
  });

/**
 *
 * @param {object} item
 * @param {object} appDetails
 * @returns {undefined}
 */
const mergeItemWithAppDetails = (item, appDetails) => ({
  pinned: 1,
  tags: [0],

  appid: item.appid,
  title: item.title,
  colour: item.colour,
  url: item.url,
  appdescription: item.appdescription
    ? item.appdescription
    : appDetails.description,

  website: appDetails.website,

  icon: appDetails.iconview,
  config: item.description ? JSON.parse(item.description) : null,
});

/**
 *
 * @param {array} items
 */
const importItems = (items) => {
  items.forEach((item) => {
    fetchAppDetails(item.appid)
      .then((appDetails) => {
        const itemWithAppDetails = mergeItemWithAppDetails(item, appDetails);
        const csrfToken = getCSRFToken();

        return postToApi(itemWithAppDetails, csrfToken);
      })
      .then((response) => {
        console.log(response);
      });
  });
};

/**
 *
 * @param {Blob} file
 * @returns {Promise<unknown>}
 */
const readJSON = (file) =>
  new Promise((resolve) => {
    const reader = new FileReader();

    reader.onload = (e) => {
      const contents = e.target.result;
      resolve(JSON.parse(contents));
    };

    reader.readAsText(file);
  });

/**
 *
 * @param {Event} event
 */
const openFileForImport = (event) => {
  const file = event.target.files[0];
  if (!file) {
    return;
  }

  readJSON(file).then(importItems);
};

const fileInput = document.querySelector("input[name='import']");

if (fileInput) {
  fileInput.addEventListener("change", openFileForImport, false);
}
