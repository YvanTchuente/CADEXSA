/**
 * FUNCTIONS LIBRARY
 * PROFILE PAGE FUNCTIONS
 */

// Global variable for setInterval
let interval;

function openTab(event, tab) {
  // Function for opening and closure of tabs in profile page
  let tabcontents, tabcontent, tablinks, tablink;
  tabcontents = document.getElementsByClassName("tabcontent");
  tablinks = document.getElementsByClassName("tablink");

  // Hides all tabs
  for (tabcontent of tabcontents) {
    tabcontent.style.display = "none";
  }
  // Remove the active class from tab links
  for (tablink of tablinks) {
    tablink.classList.remove("active");
  }
  // Display the current tab and add the active class
  document.getElementById(tab).style.display = "block";
  event.currentTarget.parentElement.classList.add("active");
  if (tab == "chats") {
    if (isset(chats_socket) && chats_socket.readyState == 1) {
      let users = document.querySelectorAll(".chatbox ul.list_users li.user");
      if (users.length > 0) {
        users[0].click();
      }
    }
  }
}

function openChatTab(event, requestedUser, requester) {
  const chat_users = document.querySelectorAll(".chatbox .list_users .user");
  for (let chat_user of chat_users) chat_user.classList.remove("open");

  event.currentTarget.classList.add("open");
  const chatReceiver = document.getElementById("chat_receiver");
  chatReceiver.setAttribute("value", requestedUser);
  const memberID = document.getElementById("chat_sender").value;

  if (window.matchMedia("screen and (max-width: 768px)").matches) {
    const menubar = document.querySelectorAll(".menu-wrapper .menu")[1];
    menubar.click();
  }

  let request_user = {
    action: "get_chat_user",
    requestedUser: requestedUser,
    requester: requester,
  };
  let request = JSON.stringify(request_user);
  if (chats_socket.readyState == 1) {
    chats_socket.send(request);
    if (!isset(interval)) {
      interval = setInterval(() => {
        update_last_activity();
      }, 15000);
    }
  }
}

function updateChatWindow(chat_user_info) {
  const avatar = document.querySelector(".chatbox .correspondent_info img");
  const username = document.getElementById("correspondent_name");
  const status = document.getElementById("correspondent_status");
  const chatWindow = document.querySelector("div.chat_window");
  // Proceeds to the update of corresponding fields
  avatar.src = chat_user_info.avatar;
  username.innerHTML = chat_user_info.username;
  status.innerHTML = chat_user_info.status;
  if (chat_user_info.status == "Offline")
    status.classList.add(chat_user_info.status.toLowerCase());
  else status.classList.remove("offline");
  // updates the chat window to new chats
  if (chatWindow.children[0].innerHTML == "<span>No conversation</span>") {
    chatWindow.removeChild(chatWindow.children[0]);
  } else {
    chatWindow.innerHTML = "";
  }
  let conversation = chat_user_info.conversation;
  let type = typeof conversation;
  // console.log(conversation);
  if (type == "string") {
    chatWindow.innerHTML = conversation;
  } else if (type == "object") {
    for (let index = 0; index < conversation.length; index++) {
      const element = conversation[index];
      chatWindow.innerHTML += element;
    }
  }
}

function sendChat() {
  const sender = document.getElementById("chat_sender").value;
  const receiver = document.getElementById("chat_receiver").value;
  const chat_msg = document.getElementById("chat_msg");
  const msg = chat_msg.value;

  if (isset(msg)) {
    if (chats_socket.readyState == 1) {
      let chat = {
        action: "post_chat",
        sender: sender,
        receiver: receiver,
        message: msg,
      };
      chat = JSON.stringify(chat);
      chats_socket.send(chat);
      chat_msg.value = "";
    }
  }
}

function postChat(chat) {
  const chatWindow = document.querySelector("div.chat_window");
  const receiver = document.getElementById("chat_receiver").value;
  const sender = document.getElementById("chat_sender").value;
  let newChat;

  if (sender == chat.sender) {
    newChat =
      '<div class="my_chat"><div><p>' +
      chat.message +
      '</p><span class="time">' +
      chat.timestamp +
      '</span></div><img src="' +
      chat.avatar +
      '"/>';
  } else if (sender == chat.receiver) {
    newChat =
      '<div class="client_chat"><img src="' +
      chat.avatar +
      '"/><div><p>' +
      chat.message +
      '</p><span class="time">' +
      chat.timestamp +
      "</span></div></div>";
  }

  if (isset(newChat)) {
    if (
      (sender == chat.sender && receiver == chat.receiver) ||
      (sender == chat.receiver && receiver == chat.sender)
    ) {
      if (chatWindow.children[0].innerHTML == "<span>No conversation</span>") {
        chatWindow.removeChild(chatWindow.children[0]);
      }
      chatWindow.innerHTML += newChat;
    }
  }
}

function updateChatUsers(new_states) {
  const users = [];
  const times = document.querySelectorAll("li.user span.time");
  const statuses = document.querySelectorAll("li.user > span");

  for (let x = 0; x < times.length; x++) {
    for (let y = 0; y < statuses.length; y++) {
      if (x === y) {
        let user_block = [times[x], statuses[y]];
        users.push(user_block);
      }
    }
  }
  // displaying the results
  for (let j = 0; j < users.length; j++) {
    for (let i = 0; i < new_states.length; i++) {
      if (j === new_states[i].n) {
        users[j][0].innerHTML = new_states[i].lastSeen;
        users[j][1].className = `status ${new_states[i].status}`;
      }
    }
  }
}

function update_last_activity() {
  const memberID = document.getElementById("chat_sender").value;
  let request = {
    action: "update_last_activity",
    member: memberID,
  };
  let request_json = JSON.stringify(request);
  if (chats_socket.readyState == 1) {
    chats_socket.send(request_json);
  }
}

function update_status(memberID, status) {
  const member_status = document.getElementById("correspondent_status");
  const chatReceiver = document.getElementById("chat_receiver").value;
  if (memberID == chatReceiver) {
    member_status.innerHTML = status;
    if (status == "Offline") member_status.classList.add(status.toLowerCase());
    else member_status.classList.remove("offline");
  }
}

function typing(n) {
  const memberID = document.getElementById("chat_sender").value; // ID of the member typing
  const correspondentID = document.getElementById("chat_receiver").value; // ID of the member to notify of this event
  let typing_status = {
    action: "update_typing_status",
    member: memberID,
    correspondent: correspondentID,
    value: n,
  };
  let request = JSON.stringify(typing_status);
  // sends the request
  chats_socket.send(request);
}

async function refreshPicture(memberID) {
  const profile_pictures = [
    document.querySelector(".user-heading a img"),
    document.querySelector(".user-panel .dropdown img"),
    document.querySelector(".user-panel img"),
  ];
  const params = `?action=fetchPicture&memberID=${memberID}`;
  const response = await fetch(`/members/profile/actions/profile.php${params}`);
  const src = await response.text();
  for (const profile_picture of profile_pictures) {
    profile_picture.src = src;
  }
}

function searchChatUsers(event) {
  const value = event.currentTarget.value;
  const chatUsers = document.querySelectorAll("li.user");
  if (value.length > 0) {
    for (const chatUser of chatUsers) {
      let name = chatUser.querySelector(".user_name").innerText;
      const regexp = new RegExp(value, "i");
      if (!regexp.test(name)) {
        chatUser.style.display = "none";
      }
    }
  } else {
    for (const chatUser of chatUsers) {
      if (chatUser.style.display == "none") {
        chatUser.removeAttribute("style");
      }
    }
  }
}

function validateUpdateForm(event, form) {
  const inputs = form.querySelectorAll("div > input");
  const selectedInputs = [];
  for (const element of inputs) {
    if (element.disabled) continue;
    selectedInputs.push(element);
  }
  selectedInputs.push(form.querySelector("div > textarea"));

  let validity = false;
  for (const element of selectedInputs) {
    if (element.type == "text" && isset(element.value)) {
      validity = validity || validateText(element.value, 4);
    }
    if (element.type == "number" && isset(element.value)) {
      validity = validity || validatePhone(element.value);
    }
    if (element.type == "email" && isset(element.value)) {
      validity = validity || validateEmail(element.value);
    }
    if (element.type == "country" && isset(element.value)) {
      validity = validity || validateCountry(element.value);
    }
    if (element.type == "textarea" && isset(element.value)) {
      validity = validity || element.value >= 50;
    }
  }
  if (!validity) {
    event.preventDefault();
  }
}
