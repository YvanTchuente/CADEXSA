/**
 * FUNCTIONS LIBRARY
 * MISCELLANEOUS FUNCTIONS
 */

import carousel from "../classes/carousel.js";
import countdown from "../classes/countdown.js";

export function gotop() {
  window.scrollTo({
    top: "0px",
    left: "0px",
    behavior: "smooth",
  });
}

/**
 * Fades out gradually an element
 * @param {HTMLElement} elem The HTML element
 * @param {number} ms Time in milliseconds
 */
export function fadeOut(elem, ms) {
  if (!elem) {
    return null;
  }
  if (ms) {
    let opacity = 1;
    let timer = setInterval(() => {
      opacity -= 50 / ms;
      if (opacity <= 0) {
        clearInterval(timer);
        opacity = 0;
        document.body.removeChild(elem);
      }
      elem.style.opacity = opacity;
      elem.style.filter = "alpha(opacity=" + opacity * 100 + ")";
    }, 50);
  } else {
    elem.style.opacity = 0;
    elem.style.filter = "alpha(opacity=0)";
    elem.style.display = "none";
    elem.style.visibility = "hidden";
  }
}

/**
 * Activates all existings carousels
 */
export function start_carousels(slides) {
  let carousel_prev, carousel_next;
  if (slides.length > 0) {
    for (const slide of slides) {
      const id = slide.id;
      let slideItem = new carousel(slide);
      slideItem.start();
      if (
        (carousel_prev = document.querySelectorAll(
          "#" + id + " + .carousel-nav [data-ride='prev']"
        )) &&
        (carousel_next = document.querySelectorAll(
          "#" + id + " + .carousel-nav [data-ride='next']"
        ))
      ) {
        for (const prev of carousel_prev)
          prev.addEventListener("click", () => slideItem.prev());
        for (const next of carousel_next)
          next.addEventListener("click", () => slideItem.next());
      }
    }
  }
}

/**
 * Toggles background wrapper open
 * @param {string} id Background's ID
 */
export function toggleBackgroundWrapperVisibility(id) {
  const elem = document.getElementById(id);
  const child = elem.children[1];
  let display = elem.style.display;
  if (display == "flex") {
    child.classList.remove("open");
    setTimeout(() => elem.removeAttribute("style"), 300);
  } else {
    elem.style.display = "flex";
    setTimeout(() => child.classList.add("open"), 100);
  }
}

/**
 * Toggle the state of chat users panel: open or closed
 */
export function toggleOpen() {
  const user_panel = document.querySelector("div.chatbox div.chat_users");
  const menubtn = document.querySelector(
    "div.chatbox div.menu-wrapper div.menu"
  );
  const chats_section = user_panel.nextElementSibling;

  if (user_panel.classList.contains("open")) {
    user_panel.classList.remove("open");
    setTimeout(() => {
      chats_section.removeAttribute("style");
    }, 500);
  } else {
    user_panel.classList.add("open");
    chats_section.style.width = "60%";
  }
  menubtn.classList.toggle("open");
}

/**
 * Starts all countdowns of events
 */
export function start_countdowns(countdowns) {
  for (const t_countdown of countdowns) {
    let date = t_countdown.getAttribute("data-date");
    // Initialization of an instance of the countdown
    const time_countdown = new countdown(t_countdown, date);
    time_countdown.start();
  }
}

/**
 * Checks the existence of a variable.
 * @param {any} variable
 * @returns Either true or false
 */
export function isset(variable) {
  // Determines if a variable is set to a value
  return typeof variable == "undefined" ||
    variable === null ||
    variable.length == 0
    ? false
    : true;
}

/**
 * Creates a new element
 * @param {string} type Element's tag name
 * @param {string} class_list Element's class list
 * @param {string} innertext Element's text content
 * @returns {HTMLElement} The created element
 */
export function element(type, class_list = NULL, innertext) {
  // Generates a new document element
  let element = document.createElement(type);
  element.classList.value = class_list;
  element.innerText = innertext;
  return element;
}

/**
 * Feeds element's input value with the value of src attribute of target
 * @param {Event} event
 * @param {string} elementID ID of the element
 */
export function selectPicture(event, inputID) {
  const input = document.getElementById(inputID);
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

    const firstChild = elem2.children[0];
    elem2.replaceChild(img, firstChild);
    elem1.value = "";
    toggleBackgroundWrapperVisibility("bc1");
  }
}

// Profile page related
export function routeToTab() {
  const path = window.location.pathname;
  let parts = path.split("/");
  const tab = parts[parts.length - 1];

  switch (tab) {
    case "chats":
      document.getElementById("tabBtn2").click();
      break;
    case "activities":
      document.getElementById("tabBtn3").click();
      break;
    case "settings":
      document.getElementById("tabBtn4").click();
      break;
    default:
      document.getElementById("tabBtn1").click();
      break;
  }
}

export function getPercent(value, total) {
  return (value * 100) / total;
}

export function stripOff(text, value) {
  return text.replace(value, "");
}

export function formatAsPercent(value, total) {
  let number = stripOff(value, "px");
  return getPercent(number, total);
}
