/**
 * FUNCTIONS LIBRARY
 * HEADER SECTION FUNCTIONS
 *
 */

function sticky_header() {
  let header = document.querySelector("body > header");
  let height = header.clientHeight;
  if (window.pageYOffset >= height) {
    header.classList.add("slidein", "scrollState");
  } else {
    header.classList.remove("slidein", "scrollState");
  }
}

/* Mobile nav dropdown function */
function drop_menu() {
  const menu_links = document.querySelector("header div.menu-links");
  const menubtn = document.querySelector("header div.menu-wrapper div.menu");

  if (menu_links.classList.contains("open")) {
    menu_links.classList.remove("open");
    if (document.querySelector(".user-panel"))
      menu_links.removeAttribute("style");
  } else {
    menu_links.classList.add("open");
    if (document.querySelector(".user-panel")) menu_links.style.top = "100px";
  }
  menubtn.classList.toggle("open");
}
