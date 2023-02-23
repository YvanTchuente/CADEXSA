import {
  dropdown_menu,
  render_page_header_sticky,
} from "../../resources/js/functions/header.js";
import {
  fadeOut,
  scrollToTop,
  createElement,
  start_carousels,
  start_countdowns,
  toggleModalVisibility,
} from "../../resources/js/functions/random.js";
import validation from "../../resources/js/functions/validation.js";

const sticky_header_enabled_pages = /^(\/|\/gallery\/|\/contactus)$/;

document.onreadystatechange = () => {
  const loader = document.getElementById("loader");
  if (document.readyState == "complete") {
    fadeOut(loader, 500);
  }
};

/**
 * Handles clicks on empty sections of the website.
 */
window.onclick = (event) => {
  if (
    document.getElementById("drop-button") &&
    !event.target.matches("#drop-button")
  ) {
    const dropdown = document.querySelector(".user-panel .dropdown");
    dropdown.style = "";
  }
  if (
    document.querySelectorAll(".nice-select .current") &&
    !event.target.matches(".nice-select .current")
  ) {
    for (const dropdown of document.querySelectorAll(".dropdown")) {
      dropdown.classList.remove("opened");
    }
    for (const current_option of document.querySelectorAll(
      ".nice-select > span"
    )) {
      current_option.classList.remove("opened");
    }
  }
  if (
    document.querySelectorAll(".modal-window") &&
    event.target.matches(".modal-window")
  ) {
    toggleModalVisibility(event.target.id);
  }
};

window.onscroll = () => {
  if (window.matchMedia("screen and (max-width: 768px)").matches) {
    render_page_header_sticky();
  } else if (sticky_header_enabled_pages.test(window.location.pathname)) {
    render_page_header_sticky();
  }
};

if (document.querySelector(".user-panel")) {
  // Always display the user panel
  document.querySelector("header.page-header > div:first-child").style.display =
    "block";
}

if (!document.getElementById("admin-space")) {
  document.querySelector(
    "header.page-header > div:first-child > div:first-child"
  ).style.justifyContent = "flex-end";
}

if (document.getElementById("scrollToTopButton")) {
  const scrollToTopButton = document.getElementById("scrollToTopButton");
  if (
    document.body.clientHeight - document.documentElement.clientHeight >
    100
  ) {
    scrollToTopButton.onclick = () => scrollToTop();
  } else {
    scrollToTopButton.style.display = "none";
  }
}

const mobile_menu_icon = document.querySelector("header .hamburger-icon");
mobile_menu_icon.onclick = () => dropdown_menu();

// Display the user panel when an ex-student is logged-in
if (document.getElementById("drop-button")) {
  const drop_button = document.getElementById("drop-button");
  drop_button.addEventListener("click", () => {
    const dropdown = document.querySelector(".user-panel .dropdown");
    dropdown.style.top = "100%";
    dropdown.style.opacity = "1";
    dropdown.style.visibility = "visible";
  });
}

/**
 * Set up the behaviour of general form inputs
 */
const password_inputs = document.querySelectorAll("input[type='password']");
for (const password_input of password_inputs) {
  if (password_input.id === "confirmation_password") {
    continue;
  }
  password_input.addEventListener("keyup", (event) => {
    const target = event.currentTarget;
    if (target.value) {
      target.setAttribute("value", target.value);
      if (!validation.validatePassword(target.value)) {
        if (!target.nextElementSibling.nextElementSibling) {
          const error = createElement(
            "div",
            "error",
            "Passwords must at least be of 8 characters long"
          );
          target.style.borderColor = "red";
          target.parentElement.appendChild(error);
        }
      } else {
        if (
          target.nextElementSibling.nextElementSibling &&
          target.nextElementSibling.nextElementSibling.matches(".error")
        ) {
          target.nextElementSibling.nextElementSibling.remove();
          target.style.borderColor = "green";
        }
      }
    } else {
      target.removeAttribute("value");
      target.removeAttribute("style");
      if (
        target.nextElementSibling.nextElementSibling &&
        target.nextElementSibling.nextElementSibling.matches(".error")
      ) {
        target.nextElementSibling.nextElementSibling.remove();
      }
    }
  });
}

const email_inputs = document.querySelectorAll("input[type='email']");
for (const email_input of email_inputs) {
  email_input.addEventListener("keyup", (event) => {
    const target = event.currentTarget;
    if (target.value) {
      if (!validation.validateEmail(target.value)) {
        if (!target.nextElementSibling) {
          const error = createElement("div", "error", "Invalid email address");
          target.style.borderColor = "red";
          target.parentElement.appendChild(error);
        }
      } else {
        if (target.nextElementSibling) {
          if (target.nextElementSibling.matches(".error")) {
            target.nextElementSibling.remove();
            target.style.borderColor = "green";
          }
        }
      }
    } else {
      target.removeAttribute("style");
      if (
        target.nextElementSibling &&
        target.nextElementSibling.matches(".error")
      ) {
        target.nextElementSibling.remove();
      }
    }
  });
}

const phone_number_inputs = document.querySelectorAll("input.phone_number");
for (const phone_number_input of phone_number_inputs) {
  phone_number_input.addEventListener("keyup", (event) => {
    const target = event.currentTarget;
    if (target.value) {
      if (!validation.validatePhone(target.value)) {
        if (!target.nextElementSibling) {
          const error = createElement("div", "error", "Invalid phone number");
          target.style.borderColor = "red";
          target.parentElement.appendChild(error);
        }
      } else {
        if (
          target.nextElementSibling &&
          target.nextElementSibling.matches(".error")
        ) {
          target.nextElementSibling.remove();
          target.style.borderColor = "green";
        }
      }
    } else {
      target.removeAttribute("style");
      if (
        target.nextElementSibling &&
        target.nextElementSibling.matches(".error")
      ) {
        target.nextElementSibling.remove();
      }
    }
  });
}

/**
 * Set up the behaviour of password visibility buttons
 */
const password_visibility_buttons = document.getElementsByClassName(
  "password-visibility-button"
);
for (const password_visibility_button of password_visibility_buttons) {
  password_visibility_button.addEventListener("click", (event) => {
    const target = event.currentTarget;
    const visibility_icon = target.firstElementChild;
    const input = target.previousElementSibling;

    if (input.getAttribute("type") === "text") {
      input.setAttribute("type", "password");
      visibility_icon.classList.remove("fa-eye-slash");
      visibility_icon.classList.add("fa-eye");
    } else {
      input.setAttribute("type", "text");
      visibility_icon.classList.remove("fa-eye");
      visibility_icon.classList.add("fa-eye-slash");
    }
  });
}

start_carousels(document.querySelectorAll(".carousel"));
start_countdowns(document.querySelectorAll(".countdown"));

/**
 * Hide modal windows on clicks upon exit buttons
 */
const modalWindows = document.querySelectorAll(".modal-window");
for (const modalWindow of modalWindows) {
  if (modalWindow.querySelector(".exit")) {
    modalWindow.querySelector(".exit").addEventListener("click", () => {
      toggleModalVisibility(modalWindow.id);
    });
  }
}
