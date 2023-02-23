/**
 * FUNCTION LIBRARY
 * MISCELLANEOUS FUNCTIONS
 */

import Carousel from "../classes/carousel.js";
import Countdown from "../classes/countdown.js";

export function scrollToTop() {
  window.scrollTo({
    top: "0px",
    left: "0px",
    behavior: "smooth",
  });
}

/**
 * Gradually fades out an element.
 *
 * @param {HTMLElement} element The HTML element
 * @param {number} time Time in milliseconds
 */
export function fadeOut(element, time) {
  if (!element) {
    return null;
  }
  if (time) {
    let opacity = 1;
    let intervalId = setInterval(() => {
      opacity -= 50 / time;
      if (opacity <= 0) {
        clearInterval(intervalId);
        opacity = 0;
        element.remove();
      }
      element.style.opacity = opacity;
      element.style.filter = `alpha(opacity=${opacity * 100})`;
    }, 50);
  } else {
    element.style.opacity = 0;
    element.style.filter = "alpha(opacity=0)";
    element.style.display = "none";
    element.style.visibility = "hidden";
  }
}

/**
 * Activates all carousels
 *
 * @param {HTMLCollection} carousel_elements
 */
export function start_carousels(carousel_elements) {
  if (carousel_elements.length > 0) {
    for (const carousel_element of carousel_elements) {
      const carousel = new Carousel(carousel_element);
      const carousel_previous_item_button =
        carousel_element.parentElement.querySelector(
          ".carousel-navigation [data-ride='prev']"
        );
      const carousel_next_item_button =
        carousel_element.parentElement.querySelector(
          ".carousel-navigation [data-ride='next']"
        );
      if (carousel_previous_item_button && carousel_next_item_button) {
        carousel_previous_item_button.addEventListener("click", () =>
          carousel.prev()
        );
        carousel_next_item_button.addEventListener("click", () =>
          carousel.next()
        );
      }
      carousel.start();
    }
  }
}

/**
 * Starts all countdowns
 *
 * @param {HTMLCollection} countdowns
 */
export function start_countdowns(countdown_elements) {
  for (const countdown_element of countdown_elements) {
    const targetDate = countdown_element.dataset.targetDate;
    const countdown = new Countdown(countdown_element, targetDate);
    countdown.start();
  }
}

/**
 * @param {number} speed
 */
export function start_counters(speed) {
  const counters = document.querySelectorAll(".counter");
  counters.forEach((counter) => {
    const updateCount = () => {
      const target = parseInt(counter.getAttribute("data-target"));
      const count = parseInt(counter.innerText);
      const increment = target / speed;
      if (count < target) {
        counter.innerText = Math.ceil(count + increment);
        setTimeout(updateCount, 50);
      } else {
        counter.innerText = target;
      }
    };
    updateCount();
  });
}

/**
 * Toggles the visibility of a modal window
 *
 * @param {string} id The modal window's ID
 */
export function toggleModalVisibility(id) {
  const modalWindow = document.getElementById(id);
  let modalBox;
  if (modalWindow.firstElementChild.tagName == "SPAN") {
    modalBox = modalWindow.children[1];
  } else {
    modalBox = modalWindow.firstElementChild;
  }
  if (modalWindow.style.display === "flex") {
    modalBox.classList.remove("open");
    setTimeout(() => modalWindow.removeAttribute("style"), 300);
  } else {
    modalWindow.style.display = "flex";
    setTimeout(() => modalBox.classList.add("open"), 100);
  }
}

/**
 * Determine if a variable is declared and is different than NULL
 *
 * @param {any} variable The variable
 */
export function isset(variable) {
  return typeof variable == "undefined" ||
    variable === null ||
    variable.length == 0
    ? false
    : true;
}

/**
 * Creates a new element.
 *
 * @param {string} type The element's tag name
 * @param {string} classList The element's class list
 * @param {string} innertext The element's text content
 * @returns {HTMLElement} The created element
 */
export function createElement(type, classList, innerText) {
  const element = document.createElement(type);
  element.classList.value = classList;
  element.innerText = innerText;
  return element;
}

/**
 * Feeds element's input value with the value of src attribute of target
 *
 * @param {Event} event
 * @param {string} elementId ID of the element
 */
export function selectPicture(event, inputId) {
  const input = document.getElementById(inputId);
  const target = event.target;
  input.value = target.src;
}

export function previewPicture(elem1_id, elem2_id, elem3_id) {
  const elem1 = document.getElementById(elem1_id);
  const elem2 = document.getElementById(elem2_id);
  const elem3 = document.getElementById(elem3_id);

  if (isset(elem1.value)) {
    const link = elem1.value.slice(
      document.location.protocol.length +
        "//".length +
        document.location.host.length
    );
    elem3.value = link;
    const img = document.createElement("img");
    img.src = elem3.value;

    const firstChild = elem2.firstElementChild;
    elem2.replaceChild(img, firstChild);
    elem1.value = "";
    toggleModalVisibility("picture-upload-modal");
  }
}

export function openProfileTab() {
  const pathname = window.location.pathname;
  let path_parts = pathname.split("/");
  const tab = path_parts[path_parts.length - 1];

  switch (tab) {
    case "messages":
      document.getElementById("tabBtn2").click();
      break;
    
    case "settings":
      document.getElementById("tabBtn3").click();
      break;
    
    default:
      document.getElementById("tabBtn1").click();
      break;
  }
}

export function percentage(value, total) {
  const percentage = (value / total) * 100;
  return percentage;
}
