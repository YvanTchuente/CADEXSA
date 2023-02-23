/**
 * FUNCTION LIBRARY
 * NICE SELECT CLASS FUNCTIONS
 */

/**
 * Opens a nice-select element.
 *
 * @param {Event} event Event object
 * @param {HTMLElement} niceSelect The HTML nice-select element
 */
export default function openSelect(event, niceSelect) {
  // Closes all open nice select dropdowns
  const dropdowns = document.querySelectorAll(".nice-select .dropdown");
  for (const dropdown of dropdowns) {
    dropdown.classList.remove("opened");
  }
  // Removes the opened class from all nice-selects
  const currents = document.querySelectorAll(".nice-select > span");
  for (const current of currents) {
    current.classList.remove("opened");
  }
  // Open the specific dropdown
  const dropdown = document.querySelector(`#${niceSelect} .dropdown`);
  dropdown.classList.add("opened");
  // Rotates the arrow
  event.currentTarget.classList.add("opened");
}
