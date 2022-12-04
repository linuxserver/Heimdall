const REFRESH_INTERVAL_SMALL = 5000;
const REFRESH_INTERVAL_BIG = 30000;
const QUEUE_PROCESSING_INTERVAL = 1000;
const CONTAINER_SELECTOR = ".livestats-container";

/**
 * @returns {*[]}
 */
function createQueue() {
  const queue = [];
  let suspended = false;

  function processQueue() {
    if (queue.length === 0 || suspended === true) {
      return;
    }

    const next = queue.shift();
    next();
  }

  document.addEventListener("visibilitychange", () => {
    suspended = document.hidden;
  });

  setInterval(processQueue, QUEUE_PROCESSING_INTERVAL);

  return queue;
}

/**
 * @returns {NodeListOf<Element>}
 */
function getContainers() {
  return document.querySelectorAll(CONTAINER_SELECTOR);
}

/**
 * @param {HTMLElement} container
 * @param {array} queue
 * @returns {function(): Promise<Response>}
 */
function createUpdateJob(container, queue) {
  const id = container.getAttribute("data-id");
  // Data only attribute seems to indicate that the item should not be updated that often
  const isDataOnly = container.getAttribute("data-dataonly") === "1";
  const interval = isDataOnly ? REFRESH_INTERVAL_BIG : REFRESH_INTERVAL_SMALL;

  return () =>
    fetch(`get_stats/${id}`)
      .then((response) => {
        if (response.ok) {
          return response.json();
        }

        throw new Error(`Network response was not ok: ${response.status}`);
      })
      .then((response) => {
        // eslint-disable-next-line no-param-reassign
        container.innerHTML = response.html;

        if (queue) {
          setTimeout(() => {
            queue.push(createUpdateJob(container, queue));
          }, interval);
        }
      })
      .catch((error) => {
        console.error(error);
      });
}

const livestatContainers = getContainers();

if (livestatContainers.length > 0) {
  const myQueue = createQueue();

  livestatContainers.forEach((container) => {
    createUpdateJob(container, myQueue)();
  });
}
