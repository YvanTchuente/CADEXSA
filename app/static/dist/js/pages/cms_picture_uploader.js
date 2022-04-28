import { element, gotop, isset } from "../../../src/js/functions/random.js";

// Maximum image file size
const maxSize = 3 * (1024 * 1024);

const root = document.querySelector("body > .ws-container");
const upload_wrapper = root.children[0];

const content_div = upload_wrapper.children[1];
const div1 = content_div.children[0];
const div2 = content_div.children[1];

const form = document.forms[0];
const [grouping1, pictureInput, grouping2, grouping3] = form.children;
const dropbox = document.getElementById("dropbox");
const [db_upload_btn] = dropbox.children;
const reset_btn = grouping3.children[1];

const form_elements = [
  ...Array.from(grouping1.getElementsByTagName("input")),
  ...Array.from(grouping2.getElementsByTagName("textarea")),
];

reset_btn.addEventListener("click", () => {
  const firstElement = dropbox.firstElementChild;
  if (firstElement.tagName == "IMG")
    dropbox.replaceChild(db_upload_btn, firstElement);
});

db_upload_btn.addEventListener("click", () => {
  pictureInput.click();
});

pictureInput.addEventListener("change", (event) => {
  const target = event.currentTarget;
  const firstChild = dropbox.firstElementChild;
  let error_msg;
  if (target.files[0].size <= maxSize) {
    if (/jpe?g/.test(target.files[0].type)) {
      const reader = new FileReader();
      reader.onload = () => {
        const img = document.createElement("img");
        img.src = reader.result;
        dropbox.replaceChild(img, firstChild);
      };
      reader.readAsDataURL(target.files[0]);
    } else {
      error_msg = "Upload only JPEG images";
    }
  } else {
    error_msg = `The image file is too heavy ( > ${
      maxSize / (1024 * 1024)
    } MB )`;
  }
  if (isset(error_msg)) {
    const error_msg_elem = element("span", "error_msg", error_msg);
    root.insertBefore(error_msg_elem, upload_wrapper);
    gotop(); // Scroll the page to the top
    setTimeout(() => root.removeChild(error_msg_elem), 5000);
  }
});

form.addEventListener("submit", (event) => {
  let expr =
    !isset(form_elements[0].value) ||
    !isset(form_elements[1].value) ||
    !isset(form_elements[2].value);
  if (expr || !pictureInput.files[0]) {
    event.preventDefault();
    for (const element of form_elements) {
      if (!isset(element.value)) {
        element.focus();
        break;
      }
    }
  }
});

dropbox.addEventListener("dragover", (e) => {
  e.preventDefault();
  e.dataTransfer.dropEffect = "move";
});
dropbox.addEventListener("drop", (e) => {
  e.preventDefault();
  const files = e.dataTransfer.files;
  const firstChild = dropbox.firstElementChild;
  const reader = new FileReader();
  reader.onload = () => {
    const img = document.createElement("img");
    img.src = reader.result;
    dropbox.replaceChild(img, firstChild);
  };
  reader.readAsDataURL(files[0]);
  pictureInput.files = files;
});

if (window.matchMedia("screen and (min-width: 992px)").matches)
  compute_height();
window.addEventListener("resize", () => {
  window.matchMedia("screen and (min-width: 992px)").onchange = (event) => {
    if (event.matches) compute_height();
    else div1.removeAttribute("style");
  };
});

function compute_height() {
  // Get the length of second element
  let l2 = getComputedStyle(div2).getPropertyValue("height");
  div1.style.height = l2;
}
