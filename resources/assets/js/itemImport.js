const IMPORT_API_URL = "api/item";
const APP_LOAD_URL = "appload";

/**
 *
 * @param {object} item
 * @param {array} errors
 */
const updateStatus = ({ item, errors }) => {
  // eslint-disable-next-line no-console
  console.log(item, errors);
  let statusLine;
  if (errors.length === 0) {
    statusLine = `<li class="success"><i class="fas fa-circle-check"></i> Imported: ${item.title} </li>`;
  } else {
    statusLine = `<li class="fail"><i class="fas fa-circle-xmark"></i> Failed: ${item.title} - ${errors[0]} </li>`;
  }
  document.querySelector(".import-status").innerHTML += statusLine;
};

/**
 *
 */
function clearStatus() {
  const statusContainer = document.querySelector(".import-status");
  statusContainer.innerHTML = "";
}

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
 * @returns {string}
 */
const getCSRFToken = () => {
  const tokenSelector = 'input[name="_token"]';
  return document.querySelector(tokenSelector).value;
};

/**
 *
 * @param {object} item
 * @param {object} appDetails
 * @returns {object}
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
 * @param {string|null} appId
 * @returns {Promise<{}>|Promise<any>}
 */
const fetchAppDetails = (appId) => {
  if (appId === null || appId === "null") {
    return Promise.resolve({});
  }

  return fetch(APP_LOAD_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ app: appId }),
  }).then((response) => response.json());
};

/**
 *
 * @param {array} items
 */
const importItems = (items) => {
  items.forEach((item) => {
    const errors = [];

    fetchAppDetails(item.appid)
      .catch(() =>
        errors.push(new Error(`Failed to find app id: ${item.appid}`))
      )
      .then((appDetails) => {
        const itemWithAppDetails = mergeItemWithAppDetails(item, appDetails);
        const csrfToken = getCSRFToken();

        return postToApi(itemWithAppDetails, csrfToken);
      })
      .catch(() =>
        errors.push(new Error(`Failed to create item: ${item.title}`))
      )
      .finally(() => {
        updateStatus({
          item,
          errors,
        });
      });
  });
};

/**
 *
 * @param {Blob} file
 * @returns {Promise<unknown>}
 */
const readJSON = (file) =>
  new Promise((resolve, reject) => {
    try {
      const reader = new FileReader();

      reader.onload = (event) => {
        const contents = event.target.result;
        resolve(JSON.parse(contents));
      };

      reader.readAsText(file);
    } catch (e) {
      reject(new Error("Unable to read file"));
    }
  });

/**
 *
 * @param {Blob} file
 */
const openFileForImport = (file) => {
  clearStatus();

  return readJSON(file)
    .catch((error) => {
      // eslint-disable-next-line no-console
      console.error(error);
    })
    .then(importItems);
};

const fileInput = document.querySelector("input[name='import']");
const importButtons = document.querySelectorAll(".import-button");

if (fileInput && importButtons) {
  importButtons.forEach((importButton) => {
    importButton.addEventListener("click", () => {
      const file = fileInput.files[0];
      if (!file) {
        return;
      }
      openFileForImport(file);
    });
  });
  fileInput.addEventListener("change", openFileForImport, false);
}
