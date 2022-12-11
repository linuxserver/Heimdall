const EXPORT_FILE_NAME = "HeimdallExport.json";
const EXPORT_API_URL = "api/item";

/**
 *
 * @param {string} fileName
 * @param {string} data
 */
function triggerFileDownload(fileName, data) {
  const a = document.createElement("a");

  const file = new Blob([data], {
    type: "text/plain",
  });

  a.href = URL.createObjectURL(file);
  a.download = EXPORT_FILE_NAME;

  a.click();
}

/**
 *
 * @param {Event} event
 */
const exportItems = (event) => {
  event.preventDefault();

  fetch(EXPORT_API_URL)
    .then((response) => {
      if (response.status !== 200) {
        // eslint-disable-next-line no-alert
        window.alert("An error occurred while exporting...");
      }

      return response.json();
    })
    .then((data) => {
      const exportedJson = JSON.stringify(data, null, 2);

      triggerFileDownload(EXPORT_FILE_NAME, exportedJson);
    });
};

const exportButton = document.querySelector("#item-export");

if (exportButton) {
  exportButton.addEventListener("click", exportItems);
}
