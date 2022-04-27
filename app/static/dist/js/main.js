import validation from "../../src/js/functions/validation.js";
import { sticky_header, drop_menu } from "../../src/js/functions/header.js";
import openSelect from "../../src/js/functions/nice-select.js";
import {
  gotop,
  fadeOut,
  start_carousels,
  start_countdowns,
  element,
  toggleBackgroundWrapperVisibility,
} from "../../src/js/functions/random.js";

const pathname = window.location.pathname;
const path_to_signup = /^\/members\/register$/;
const path_to_recoveryACC = /^\/members\/recover_account.(html|php)+?w*$/;
const sticky_header_enabled_pages = ["/", "/gallery/", "/contact_us/"];

document.onreadystatechange = function () {
  const loader = document.querySelector("#loader");
  if (document.readyState == "complete") {
    fadeOut(loader, 500);
  }
};

/* Event Handler for clicks on empty sections of main page */
window.addEventListener("click", (event) => {
  if (document.querySelector(".dropbtn")) {
    if (!event.target.matches(".dropbtn")) {
      const dropdown = document.querySelector(".user-panel .dropdown");
      dropdown.style = "";
    }
  }
  if (document.querySelectorAll(".nice-select")) {
    if (!event.target.matches(".nice-select .current")) {
      const dropdowns = document.querySelectorAll(".dropdown");
      for (const dropdown of dropdowns) {
        dropdown.classList.remove("opened");
      }
      const currents = document.querySelectorAll(".nice-select > span");
      for (const icon of currents) {
        icon.classList.remove("opened");
      }
    }
  }
  if (document.querySelectorAll(".background-wrapper")) {
    if (event.target.matches(".background-wrapper")) {
      toggleBackgroundWrapperVisibility(event.target.id);
    }
  }
});

/* Always display User panel */
if (document.querySelector(".user-panel")) {
  document.getElementById("topnav").style.display = "block";
  if (window.matchMedia("screen and (max-width: 992px)").matches) {
    document.getElementById("contact-us").style.display = "none";
  }
  window.matchMedia("screen and (max-width: 992px)").onchange = (event) => {
    if (event.matches)
      document.getElementById("contact-us").style.display = "none";
    else document.getElementById("contact-us").style.display = "flex";
  };
}

/* Main page Counters JS functions */
const counters = document.querySelectorAll(".counter");
/* Event handler for page scrolls */
window.addEventListener("scroll", () => {
  if (window.scrollY >= 1800) {
    const speed = 500;
    counters.forEach((counter) => {
      const updateCount = () => {
        const target = +counter.getAttribute("data-target");
        const count = +counter.innerText;

        const increment = target / speed;

        if (count < target) {
          counter.innerText = Math.ceil(count + increment);
          setTimeout(updateCount, 1);
        } else {
          counter.innerText = target;
        }
      };
      updateCount();
    });
  }
  if (sticky_header_enabled_pages.includes(pathname)) sticky_header();
});

// Append an event listener to the goto top button
const gotop_btn = document.getElementById("gotop_btn");
if ((document.body.clientHeight - document.documentElement.clientHeight) > 100)
  gotop_btn.addEventListener("click", () => gotop());
else 
  gotop_btn.style.display = "none";

// Append an event listener to the menu wrapper
const main_menu_btn = document.querySelector("header div.menu-wrapper");
main_menu_btn.addEventListener("click", () => drop_menu());

// Display user panel when a user is logged in
if (document.querySelector(".dropbtn")) {
  const dropbtn = document.querySelector(".dropbtn");
  dropbtn.onclick = () => {
    const dropdown = document.querySelector(".user-panel .dropdown");
    dropdown.style.top = "100%";
    dropdown.style.opacity = "1";
    dropdown.style.visibility = "visible";
  };
}

// Attach event handler to nice-select buttons
const nice_select_buttons = document.getElementsByClassName("nice-select");
for (const nice_select_button of nice_select_buttons) {
  const current = nice_select_button.firstElementChild;
  current.addEventListener("click", (event) => {
    openSelect(event, nice_select_button.id);
  });
}

// Selection of option for the select elements of the nice-selects
const nice_select_options = document.querySelectorAll(
  ".nice-select .dropdown li"
);
for (const select_option of nice_select_options) {
  // Attaches an event handler to each nice-select list item
  select_option.addEventListener("click", (event) => {
    const target = event.target;
    const value = target.innerText;
    const parent = target.parentElement;

    // Removes class 'selected' from all list items and the class atrribute itself
    for (const item of parent.children) {
      item.classList.remove("selected");
      item.removeAttribute("class");
    }
    // Adds the class 'selected' to the target
    target.classList.add("selected");
    // Selects the 'current' element and update its content
    const current = target.parentElement.previousElementSibling;
    current.innerText = value;
    // Select the 'select' element
    const select = target.parentElement.nextElementSibling;
    for (const option of select.children) {
      option.removeAttribute("selected");
      if (option.innerText === value) {
        option.setAttribute("selected", "");
      }
    }
  });
}

const slides = document.querySelectorAll(".carousel");
const countdowns = document.querySelectorAll(".countdown");
start_carousels(slides);
start_countdowns(countdowns);

/************************************************************************************
 *********              GENERAL FORM INPUTS EVENT LISTENERS                **********
 ************************************************************************************/

// Passwords
const password_controls = document.querySelectorAll("input[type='password']");
for (const password_control of password_controls) {
  if (password_control.id === "confirm-password") continue;
  password_control.addEventListener("keyup", (event) => {
    const target = event.currentTarget;
    const parent = target.parentElement;
    const value = target.value;

    if (value) {
      event.currentTarget.setAttribute("value", value);
      if (path_to_signup.test(pathname) || path_to_recoveryACC.test(pathname)) {
        if (!validation.validatePassword(value)) {
          if (!target.nextElementSibling.nextElementSibling) {
            const msg = element(
              "div",
              "error",
              "Passwords must at least be of 8 characters long"
            );
            target.style.borderColor = "red";
            parent.appendChild(msg);
          }
        } else {
          if (target.nextElementSibling.nextElementSibling) {
            if (
              target.nextElementSibling.nextElementSibling.matches(".error")
            ) {
              parent.removeChild(target.nextElementSibling.nextElementSibling);
              target.style.borderColor = "green";
            }
          }
        }
      }
    } else {
      target.removeAttribute("value");
      target.removeAttribute("style");
      if (target.nextElementSibling.nextElementSibling) {
        if (target.nextElementSibling.nextElementSibling.matches(".error")) {
          parent.removeChild(target.nextElementSibling.nextElementSibling);
        }
      }
    }
  });
}

// Email inputs
const email_controls = document.querySelectorAll("input[type='email']");
for (const email_control of email_controls) {
  email_control.addEventListener("keyup", (event) => {
    const target = event.currentTarget;
    const parent = target.parentElement;
    const value = target.value;

    if (value) {
      // If email input is not a real email address
      if (!validation.validateEmail(value)) {
        if (!target.nextElementSibling) {
          const msg = element("div", "error", "Invalid email address");
          target.style.borderColor = "red";
          parent.appendChild(msg);
        }
      } else {
        if (target.nextElementSibling) {
          if (target.nextElementSibling.matches(".error")) {
            parent.removeChild(target.nextElementSibling);
            target.style.borderColor = "green";
          }
        }
      }
    } else {
      target.removeAttribute("style");
      if (target.nextElementSibling) {
        if (target.nextElementSibling.matches(".error")) {
          parent.removeChild(target.nextElementSibling);
        }
      }
    }
  });
}

// Phone numbers inputs
const phone_number_controls = document.querySelectorAll(
  "input[type='number'].phone_number"
);
for (const phone_number_control of phone_number_controls) {
  phone_number_control.addEventListener("keyup", (event) => {
    const target = event.currentTarget;
    const parent = target.parentElement;
    const value = target.value;

    if (value) {
      if (!validation.validatePhone(value)) {
        if (!target.nextElementSibling) {
          const msg = element("div", "error", "Invalid phone number");
          target.style.borderColor = "red";
          parent.appendChild(msg);
        }
      } else {
        if (target.nextElementSibling) {
          if (target.nextElementSibling.matches(".error")) {
            parent.removeChild(target.nextElementSibling);
            target.style.borderColor = "green";
          }
        }
      }
    } else {
      target.removeAttribute("style");
      if (target.nextElementSibling) {
        if (target.nextElementSibling.matches(".error")) {
          parent.removeChild(target.nextElementSibling);
        }
      }
    }
  });
}

// Toggle visiblity button
let toggle_btns = document.getElementsByClassName("password-visibility-btn");
for (const toggle_btn of toggle_btns) {
  toggle_btn.addEventListener("click", (event) => {
    const target = event.currentTarget;
    const icon = target.firstElementChild;
    const input = target.previousElementSibling;
    const type = input.getAttribute("type");

    if (type === "text") {
      input.setAttribute("type", "password");
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    } else {
      input.setAttribute("type", "text");
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    }
  });
}

// Attach by default event listeners to exit button in background covers
const background_covers = document.querySelectorAll(".background-wrapper");
for (const background_cover of background_covers) {
  const exit_button = background_cover.children[0];
  exit_button.addEventListener("click", () => {
    const parent_id = background_cover.id;
    toggleBackgroundWrapperVisibility(parent_id);
  });
}

window.addEventListener("pageshow", async () => {
  const response = await fetch("/members/user_online_status.php");
  const is_connected = await response.text();
  if (is_connected === "Not connected")
    fetch("/members/user_online_status.php?logout");
});
