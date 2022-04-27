import profile from "../../../src/js/functions/profile.js";
import {
  toggleBackgroundWrapperVisibility,
  routeToTab,
  toggleOpen,
  element,
} from "../../../src/js/functions/random.js";

// Profile Picture Uploader
const picture_uploader = document.querySelector(".profile-picture-uploader");
const [, uploader_preview, uploader_footer] = picture_uploader.children;
const [uploader_footer_div, uploader_form] = uploader_footer.children;
const [select_button, upload_button] = uploader_footer_div.children;
const [picture_input, picture_name] = uploader_form.children;
const memberID = uploader_form.children[3];

// Miscellaneous
const chat_msg = document.querySelector("#chat_msg");
const user_search = document.querySelector("#user_search");
const chats_menu_btn = document.querySelector("div.chatbox div.menu-wrapper");
const updateProfileForm = document.querySelector("#updateProfile");
let intervalID; // For the typing function

const member = document.querySelector("#chat_sender").value; // ID of the logged-in member

// Stream chat-related updates from the chat update server
const chats_update_socket = new WebSocket(
  "ws://localhost:5050/members/profile/actions/chats-updates.php?member=" +
    member
);

// Communicate with chat server
const chats_socket = new WebSocket(
  "ws://localhost:5000/members/profile/actions/chats.php?member=" + member
);

const tablinks = document.getElementsByClassName("tablink");
tablinks[0].firstElementChild.addEventListener("click", (e) =>
  profile.openTab(e, "profile-info", chats_socket)
);
tablinks[1].firstElementChild.addEventListener("click", (e) =>
  profile.openTab(e, "chats", chats_socket)
);
tablinks[2].firstElementChild.addEventListener("click", (e) =>
  profile.openTab(e, "settings", chats_socket)
);

const profilePictureElem = document.querySelector(
  ".profile-nav .user-heading img"
);
profilePictureElem.addEventListener("click", () => { 
  toggleBackgroundWrapperVisibility("bc1");
  resize(uploader_preview)
})

const chat_users = document.querySelectorAll(".list_users li.user");
for (const chat_user of chat_users) {
  let requestedUser, requester;
  chat_user.addEventListener("click", (e) => {
    requestedUser = chat_user.getAttribute("data-requestedUserID");
    requester = chat_user.getAttribute("data-requesterUserID");
    profile.openChatTab(e, requestedUser, requester, chats_socket);
  });
}

const send_btn = document.querySelector(".send_btn");
send_btn.addEventListener("click", () => profile.sendChat(chats_socket));

const pp_uploader_btn = document.querySelector(".tabcontent#settings button");
pp_uploader_btn.addEventListener("click", () => {
  toggleBackgroundWrapperVisibility("bc1");
  resize(uploader_preview);
});

routeToTab();

chats_update_socket.onmessage = (e) => {
  const msg = JSON.parse(e.data);
  profile.updateChatUsers(msg.states);
};

chats_update_socket.onclose = (e) => {
  console.log("Connection closed with the chat update server");
};

chats_socket.onopen = () => {
  let users = document.querySelectorAll(".chatbox ul.list_users li.user");
  if (users.length > 0) {
    users[0].click();
  }
};

chats_socket.onmessage = (e) => {
  const msg = JSON.parse(e.data);
  switch (msg.type) {
    // updates the chat window with the new chat
    case "new_chat":
      profile.postChat(msg);
      break;
    // Updates the status of chat member
    case "member_status":
      profile.update_status(msg.member, msg.status);
      break;
    // Updates the chat window with the selected chat user
    case "chat_user_info":
      profile.updateChatWindow(msg);
      break;
  }
};

chats_socket.onclose = (e) => {
  console.log("Connection closed with the chat server");
};

// Add an event listener to the menu button of the chat section
chats_menu_btn.addEventListener("click", () => toggleOpen());

chat_msg.addEventListener("focus", () => {
  intervalID = setInterval(() => {
    profile.typing(1, chats_socket);
  }, 100);
});
chat_msg.addEventListener("blur", () => {
  clearInterval(intervalID);
  profile.typing(0, chats_socket);
});

select_button.addEventListener("click", () => {
  picture_input.click();
});

upload_button.addEventListener("click", async () => {
  if (picture_input.files[0]) {
    const form = new FormData(uploader_form);
    const request = new Request("/members/profile/actions/profile.php", {
      method: "POST",
      mode: "cors",
      credentials: "same-origin",
      body: form,
    });
    // Uploads the file
    const response = await fetch(request);
    if (response.ok) {
      const reply = await response.text();
      if (reply == "Successful") {
        picture_uploader.previousElementSibling.click();
        setTimeout(() => profile.refreshPicture(memberID.value), 1000);
      }
    }
  }
});

// Read file immediated after its selection by the user
picture_input.addEventListener("change", (e) => {
  const target = e.currentTarget;
  picture_name.innerHTML = target.files[0].name;
  const reader = new FileReader();

  reader.onloadstart = () => {
    if (uploader_preview.children)
      for (child of uploader_preview.children) child.remove();
    const loading = element("span", null, "Loading thumbnail...");
    loading.id = "loading";
    uploader_preview.appendChild(loading);
  };
  reader.onload = () => {
    const firstChild = uploader_preview.firstElementChild;
    const img = document.createElement("img");
    img.src = reader.result;
    uploader_preview.replaceChild(img, firstChild);
    resize(uploader_preview);
  };
  reader.readAsDataURL(target.files[0]);
});

user_search.addEventListener("keyup", (e) => profile.searchChatUsers(e));
updateProfileForm.addEventListener("submit", (e) =>
  profile.validateUpdateForm(e, updateProfileForm)
);

window.onresize = () => {
  resize(uploader_preview);
};

function resize(element) {
  const parent_element = element.parentElement;
  const parent_header = element.previousElementSibling;
  const parent_footer = element.nextElementSibling;
  const element_height =
    parent_element.clientHeight -
    (parent_header.clientHeight + parent_footer.clientHeight);
  element.style.setProperty("--height", element_height + "px");
}
