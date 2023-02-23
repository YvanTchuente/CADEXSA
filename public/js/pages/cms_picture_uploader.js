import {
  isset,
  toggleModalVisibility,
} from "../../../../resources/js/functions/random.js";

const picture_maximum_size = 5 * (1024 * 1024); // Maximum image file size

const dropbox = document.getElementById("dropbox");
const cancel_button = document.getElementById("cancel_button");
const accept_button = document.getElementById("accept_button");
const picture_selection_button = dropbox.querySelector("span");
const picture_upload_form = document.forms.namedItem("picture-upload");
const picture_input = picture_upload_form.elements.namedItem("picture");

const initial_dropbox_content =
  "<div><img src='/images/graphics/gallery.png'><h3>Drop your pictures here or <span>browse</span></h3><p>Accepted file types: .jpg and .jpeg only.</p></div>";
const selected_pictures = [],
  standby_pictures = [];

picture_selection_button.addEventListener("click", () => {
  picture_input.click();
});

dropbox.addEventListener("dragover", (e) => {
  e.preventDefault();
  e.dataTransfer.dropEffect = "move";
});

dropbox.addEventListener("drop", (e) => {
  e.preventDefault();
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    const picture = files[0];
    const picture_input = document.forms
      .namedItem("picture-upload")
      .elements.namedItem("picture");
    let error;
    if (picture.size <= picture_maximum_size) {
      const dropbox = document.getElementById("dropbox");
      if (/jpe?g/.test(files[0].type)) {
        readPicture(picture, dropbox);
        picture_input.files = files;
        setTimeout(
          () => toggleModalVisibility("picture-description-modal"),
          1000
        );
      } else {
        error = "Upload only JPEG pictures";
      }
    } else {
      let max_size = picture_maximum_size / (1024 * 1024); 
      error = `The picture's size is above the maximum size limit of ${max_size} MB`;
    }
    if (isset(error)) {
      alert(error);
    }
  }
});

picture_input.onclick = (e) => {
  e.currentTarget.value = null;
};

picture_input.onchange = (e) => {
  if (e.currentTarget.files.length > 0) {
    let error;
    const picture = e.currentTarget.files[0];
    if (picture.size <= picture_maximum_size) {
      if (/jpe?g/.test(picture.type)) {
        const dropbox = document.getElementById("dropbox");
        readPicture(picture, dropbox);
        standby_pictures.push(picture);
      } else {
        error = "Upload only JPEG pictures";
      }
    } else {
      let max_size = picture_maximum_size / (1024 * 1024);
      error = `The picture's size is above the maximum size limit of ${max_size} MB`;
    }
    if (isset(error)) {
      alert(error);
    } else {
      setTimeout(() => toggleModalVisibility("picture-description-modal"), 500);
    }
    e.currentTarget.value = null;
  }
};

cancel_button.onclick = () => {
  reset_uploader();
  toggleModalVisibility("picture-description-modal");
};

accept_button.onclick = () => {
  const picture_upload_form = document.forms.namedItem("picture-upload");
  const picture_upload_form_data = new FormData(picture_upload_form);
  picture_upload_form_data.set("picture", standby_pictures.pop());
  if (
    isset(picture_upload_form_data.get("shotOn")) &&
    isset(picture_upload_form_data.get("description"))
  ) {
    const now = new Date();
    const shotOn = new Date(picture_upload_form_data.get("shotOn"));
    if (shotOn < now) {
      const picture = picture_upload_form_data.get("picture");
      for (const entry of selected_pictures) {
        if (entry.get("picture").name === picture.name) {
          selected_pictures.splice(selected_pictures.indexOf(entry), 1);
        }
      }
      enlistPicture(
        picture.name,
        picture.size,
        picture_upload_form_data.get("shotOn")
      );
      selected_pictures.push(picture_upload_form_data);
      reset_uploader();
      toggleModalVisibility("picture-description-modal");
    } else {
      alert("The shot on date must be in the past.");
    }
  }
};

picture_upload_form.addEventListener("submit", (e) => {
  e.preventDefault();
  const request = new Request(window.location.pathname, {
    method: "POST",
    body: selected_pictures,
  });
});

/**
 * @param {File} picture
 * @param {HTMLElement} display
 */
function readPicture(picture, display) {
  const reader = new FileReader();
  reader.onload = () => {
    const img = document.createElement("img");
    img.src = reader.result;
    display.replaceChild(img, display.firstElementChild);
  };
  reader.readAsDataURL(picture);
}

function reset_uploader() {
  const picture_upload_form = document.forms.namedItem("picture-upload");
  if (
    /img/i.test(document.getElementById("dropbox").firstElementChild.tagName)
  ) {
    document.getElementById("dropbox").innerHTML = initial_dropbox_content;
    document.querySelector("#dropbox span").addEventListener("click", () => {
      picture_upload_form.elements.namedItem("picture").click();
    });
  }
  for (const element of picture_upload_form.elements) {
    element.value = "";
  }
}

/**
 * @param {FormData} picture_upload_form_data
 */
function fill_uploader(picture_upload_form_data) {
  readPicture(
    picture_upload_form_data.get("picture"),
    document.getElementById("dropbox")
  );
  setTimeout(() => {
    const picture_upload_form = document.forms.namedItem("picture-upload");
    toggleModalVisibility("picture-description-modal");
    for (const element of picture_upload_form.elements) {
      if (element.name == "picture") continue;
      element.value = picture_upload_form_data.get(element.name);
    }
  }, 500);
}

function enlistPicture(name, size, shotOn) {
  if (!name || !size || !shotOn) {
    alert("Invalid picture");
    return;
  }
  const pictures = document.getElementById("pictures");
  if (pictures.childElementCount == 0) {
    pictures.innerHTML = "<h2>Selected pictures</h2>";
  }
  size = (size / (1024 * 1024)).toPrecision(2);
  shotOn = new Date(shotOn).toLocaleDateString();
  let picture = `<article class='picture'><div><img src='/images/graphics/image.png' /><div><span>${name}</span><div><span>${size} MB</span><span>Shot on ${shotOn}</span></div></div></div><div class="actions"><span><i class="fas fa-edit"></i></span><span><i class="fas fa-times"></i></span></div></article>`;
  pictures.innerHTML += picture;
  setTimeout(() => {
    picture = document.getElementById("pictures").lastElementChild;
    const edit_button = picture.querySelector(".actions span:first-child");
    const delete_button = picture.querySelector(".actions span:last-child");
    edit_button.onclick = (e) => edit_picture(e);
    delete_button.onclick = (e) => delete_picture(e);
  }, 500);
  if (!document.querySelector("button#upload")) {
    pictures.parentElement.innerHTML += "<button id='upload'>Upload</button>";
    document.querySelector("button#upload").onclick = () => upload();
  }
}

function edit_picture(e) {
  const picture_name =
    e.currentTarget.parentElement.parentElement.querySelector(
      "div:first-child span:first-of-type"
    ).textContent;
  for (const entry of selected_pictures) {
    if (entry.get("picture").name === picture_name) {
      fill_uploader(entry);
    }
  }
}

function delete_picture(e) {
  const picture = e.currentTarget.parentElement.parentElement;
  const pictures = picture.parentElement;
  const picture_name = picture.querySelector(
    "div:first-child span:first-of-type"
  ).textContent;
  for (const entry of selected_pictures) {
    if (entry.get("picture").name === picture_name) {
      selected_pictures.splice(selected_pictures.indexOf(entry), 1);
      picture.remove();
      if (pictures.querySelectorAll(".picture").length == 0) {
        document.querySelector("button#upload").remove();
        pictures.firstElementChild.remove();
      }
    }
  }
}
