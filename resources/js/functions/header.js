/**
 * FUNCTION LIBRARY
 * HEADER SECTION FUNCTIONS
 */

export function render_page_header_sticky() {
  let header = document.querySelector("body > header");
  if (window.scrollY >= header.offsetHeight - 100) {
    header.classList.add("slidein", "scroll-state");
  } else {
    header.classList.remove("slidein", "scroll-state");
  }
}

export function dropdown_menu() {
  const menu = document.querySelector("header .mobile-menu");
  const menu_icon = document.querySelector("header .hamburger-icon");
  if (menu.classList.contains("open")) {
    menu.classList.remove("open");
    if (document.querySelector(".user-panel")) menu.removeAttribute("style");
  } else {
    menu.classList.add("open");
    if (document.querySelector(".user-panel")) {
      const header_height =
        document.querySelector("header.page-header").offsetHeight;
      menu.style.top = `${header_height}px`;
    }
  }
  menu_icon.classList.toggle("open");
}
