import {
  isset,
  stripOff,
  selectPicture,
  previewPicture,
  formatAsPercent,
  toggleBackgroundWrapperVisibility,
} from "../../../src/js/functions/random.js";

const uploader_picture_elems = document.querySelectorAll(
  ".select-picture #pictures img"
);
const picture_uploader = document.querySelector(".picture-uploader");
const [header, preview, footer] = picture_uploader.children;
const upload_button = header.children[1];
const [preview_button] = preview.children;
const [footer_span, footer_form] = footer.children;
const [pictureInput, reset_button] = footer_form.children;
const [select_picture_button, upload_option_button] = document.querySelectorAll(
  ".select-picture #footer button"
);
let preview_picture_src;

for (const picture of uploader_picture_elems) {
  picture.addEventListener("click", (event) => {
    selectPicture(event, "picture-url");
  });
}

select_picture_button.addEventListener("click", () =>
  previewPicture("picture-url", "thumbnail-upload", "thumbnail")
);
upload_option_button.addEventListener("click", () => {
  toggleBackgroundWrapperVisibility("bc1");
  toggleBackgroundWrapperVisibility("bc2");
  // Get height properties by stripping off "px" from strings
  let uploader_height = stripOff(
    getComputedStyle(picture_uploader).getPropertyValue("height"),
    "px"
  );
  let header_height = formatAsPercent(
    getComputedStyle(header).getPropertyValue("height"),
    uploader_height
  );
  let footer_height = formatAsPercent(
    getComputedStyle(footer).getPropertyValue("height"),
    uploader_height
  );
  // Dynamically assigning height property to the "preview" element
  let preview_height = 100 - (header_height + footer_height);
  preview_height += "%";
  preview.style.setProperty("--height", preview_height);
});

preview_button.addEventListener("click", () => {
  pictureInput.click();
});

reset_button.addEventListener("click", () => {
  const firstElement = preview.firstElementChild;
  if (firstElement.tagName == "IMG")
    preview.replaceChild(preview_button, firstElement);
  if (footer_span.innerHTML) footer_span.innerHTML = "";
});

pictureInput.addEventListener("change", (event) => {
  const target = event.currentTarget;
  const firstChild = preview.firstElementChild;
  const reader = new FileReader();
  reader.onload = () => {
    const img = document.createElement("img");
    preview_picture_src = reader.result;
    img.src = reader.result;
    preview.replaceChild(img, firstChild);
  };
  reader.readAsDataURL(target.files[0]);
  footer_span.innerHTML = target.files[0].name;
});

upload_button.addEventListener("click", async () => {
  if (isset(pictureInput.files)) {
    const formData = new FormData(footer_form);
    const responseObj = await fetch("/cms/article_picture_uploader.php", {
      method: "POST",
      body: formData,
    });
    const response = await responseObj.json();
    if (response.status === "ok") {
      toggleBackgroundWrapperVisibility("bc2");
      const cms_picture_previewer = document.querySelector("#thumbnail-upload");
      const picture = document.createElement("img");
      picture.src = preview_picture_src;
      cms_picture_previewer.replaceChild(
        picture,
        cms_picture_previewer.firstElementChild
      );
      document.querySelector("#thumbnail").value = response.filename;
    }
  }
});

preview.addEventListener("dragover", (e) => {
  e.preventDefault();
  e.dataTransfer.dropEffect = "move";
});
preview.addEventListener("drop", (e) => {
  e.preventDefault();
  const files = e.dataTransfer.files;
  const firstChild = preview.firstElementChild;
  const reader = new FileReader();
  reader.onload = () => {
    const img = document.createElement("img");
    img.src = reader.result;
    preview.replaceChild(img, firstChild);
  };
  reader.readAsDataURL(files[0]);
  pictureInput.files = files;
  footer_span.innerHTML = pictureInput.files[0].name;
});
