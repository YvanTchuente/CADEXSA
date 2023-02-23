import openSelect from "../../../resources/js/functions/nice-select.js";

const nice_select_buttons = document.getElementsByClassName("nice-select");
const nice_select_options = document.querySelectorAll(
  ".nice-select .dropdown li"
);

// Attach event handler to nice-select buttons
for (const nice_select_button of nice_select_buttons) {
  const currentElement = nice_select_button.firstElementChild;
  currentElement.addEventListener("click", (event) => {
    openSelect(event, nice_select_button.id);
  });
}

for (const select_option of nice_select_options) {
  // Attaches an event handler to each nice-select list item
  select_option.addEventListener("click", (event) => {
    const target = event.currentTarget;
    const value = target.innerHTML;
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

const url = window.location.href;
if (/month=(\w+)&year=(\d+)/.test(url)) {
  const match = /month=(\w+)&year=(\d+)/.exec(url);
  const month = match[1];
  const year = match[2];
  for (const select_option of nice_select_options) {
    if (
      select_option.innerHTML.toLocaleLowerCase() ==
        month.toLocaleLowerCase() ||
      select_option.innerHTML == year
    ) {
      select_option.click();
    }
  }
}
