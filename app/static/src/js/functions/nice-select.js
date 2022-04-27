/**
 * FUNCTIONS LIBRARY
 * NICE SELECT CLASS FUNCTIONS
 */

/**
 * Opens a nice-select element
 * @param {Event} event Event Object
 * @param {HTMLElement} niceSelect The HTML nice-select element
 */
export default function openSelect(event, niceSelect) {
  // Closes all open nice select dropdowns
  const dropdowns = document.querySelectorAll(".nice-select .dropdown");
  for (const dropdown of dropdowns) {
    dropdown.classList.remove("opened");
  }
  // Removes the class .opened from all nice-selects
  const currents = document.querySelectorAll(".nice-select > span");
  for (const current of currents) {
    current.classList.remove("opened");
  }

  // Open the specific dropdown
  const selector = "#" + niceSelect + " .dropdown";
  const dropdown = document.querySelector(selector);
  dropdown.classList.add("opened");

  // Rotates the arrow
  event.target.classList.add("opened");
}
