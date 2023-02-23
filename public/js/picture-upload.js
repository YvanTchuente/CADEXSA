/**
 * Picture upload logic
 */

const picture_uploader = document.querySelector(".picture-upload-panel");
const picture_preview = document.getElementById("picture-preview");
const preview_content = picture_preview.firstElementChild;
const picture_selection_button = picture_preview.querySelector("span");
const avatar_upload_form = picture_uploader.querySelector("form");
const avatar_upload_button = picture_uploader.querySelector("button");
const cancel_upload_button = picture_uploader.querySelector(
  "button:last-of-type"
);
const picture_input = avatar_upload_form.querySelector("input[type='file']");

picture_preview.addEventListener("dragover", (e) => {
  e.preventDefault();
  e.dataTransfer.dropEffect = "move";
});

picture_preview.addEventListener("drop", (e) => {
  e.preventDefault();
  const reader = new FileReader();
  reader.onloadstart = () => {
    if (picture_preview.children) {
      for (const child of picture_preview.children) {
        child.remove();
      }
    }
    const loading = document.createElement("span");
    loading.innerHTML = "Loading thumbnail...";
    loading.id = "loading";
    picture_preview.appendChild(loading);
  };
  reader.onload = () => {
    const firstChild = picture_preview.firstElementChild;
    const img = document.createElement("img");
    img.src = reader.result;
    picture_preview.replaceChild(img, firstChild);
  };
  const files = e.dataTransfer.files;
  reader.readAsDataURL(files[0]);
  picture_input.files = files;
});

cancel_upload_button.addEventListener("click", () => {
  if (picture_preview.firstElementChild.tagName == "IMG") {
    picture_preview.replaceChild(
      preview_content,
      picture_preview.firstElementChild
    );
    picture_input.value = "";
  }
});

picture_selection_button.addEventListener("click", () => {
  picture_input.click();
});

picture_input.addEventListener("change", (e) => {
  const target = e.currentTarget;
  const reader = new FileReader();
  reader.onloadstart = () => {
    if (picture_preview.children) {
      for (const child of picture_preview.children) {
        child.remove();
      }
    }
    const loading = document.createElement("span");
    loading.innerHTML = "Loading thumbnail...";
    loading.id = "loading";
    picture_preview.appendChild(loading);
  };
  reader.onload = () => {
    const firstChild = picture_preview.firstElementChild;
    const img = document.createElement("img");
    img.src = reader.result;
    picture_preview.replaceChild(img, firstChild);
  };
  reader.readAsDataURL(target.files[0]);
});

avatar_upload_button.addEventListener("click", async () => {
  if (picture_input.files[0]) {
    const formData = new FormData(avatar_upload_form);
    const request = new Request("/services/account.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    });
    const response = await fetch(request); // Upload the file
    if (response.ok) {
      const reply = await response.text();
      if (!reply) {
        picture_uploader.previousElementSibling.click();
        window.location.reload();
      } else {
        alert(reply);
      }
    }
  }
});
