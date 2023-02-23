/**
 * FUNCTION LIBRARY
 * PROFILE PAGE FUNCTIONS
 */

import validation from "../../../resources/js/functions/validation.js";
import { isset } from "../../../resources/js/functions/random.js";

let intervalId; // Global variable for setInterval

/**
 * Handles the opening and closure of account page tabs
 *
 * @param {Event} event
 * @param {string} tab Tab's ID attribute value
 * @param {WebSocket} socket
 */
function openTab(event, tab, socket) {
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
  if (tab == "messages") {
    if (socket.readyState == 1) {
      let users = document.querySelectorAll(".chat-window .user");
      if (users.length > 0) {
        users[0].click();
      }
    }
  }
}

/**
 * Opens a chat room
 *
 * @param {Event} event
 * @param {number} targetExstudentId
 * @param {number} requestingExstudentId
 * @param {WebSocket} socket
 */
function openChatRoom(event, targetExstudentId, requestingExstudentId, socket) {
  if (socket.readyState == 1) {
    const chat_users = document.querySelectorAll(".chat-window .users .user");
    for (let chat_user of chat_users) {
      chat_user.classList.remove("open");
    }

    event.currentTarget.classList.add("open");
    const chatReceiver = document.getElementsByName("chat-message-receiver")[0];
    chatReceiver.setAttribute("value", targetExstudentId);

    if (window.matchMedia("screen and (max-width: 768px)").matches) {
      const menubar = document.querySelectorAll(".hamburger-icon")[1];
      menubar.click();
    }

    let data = {
      action: "get_chat_user",
      targetExstudentId: targetExstudentId,
      requestingExstudentId: requestingExstudentId,
    };
    socket.send(JSON.stringify(data));
    if (!isset(intervalId)) {
      intervalId = setInterval(() => {
        update_last_activity(socket);
      }, 10000);
    }
  }
}

/**
 * Updates the whole chat window
 */
function updateChatWindow(chat_user_data) {
  const correspondent_avatar = document.querySelector(
    ".chat-window .correspondent img"
  );
  const correspondent_name_element = document.querySelector(
    ".chat-window .correspondent > div:last-child span:first-child"
  );
  const correspondent_state_element = document.querySelector(
    ".chat-window .correspondent > div:last-child span:last-child"
  );
  const chat = document.querySelector(".chat");
  const chat_receiver = document.getElementsByName("chat-message-receiver")[0]
    .value;
  const chat_sender = document.getElementsByName("chat-message-sender")[0]
    .value;
  // Proceeds to the update of corresponding fields
  correspondent_avatar.src = chat_user_data.avatar;
  correspondent_name_element.innerHTML = chat_user_data.username;
  correspondent_state_element.innerHTML = chat_user_data.state;
  if (chat_user_data.state == "Offline") {
    correspondent_state_element.classList.add(
      chat_user_data.state.toLowerCase()
    );
  } else {
    correspondent_state_element.classList.remove("offline");
  }
  // updates the chat window to new messages
  if (chat_user_data.chat.length > 0) {
    if (chat.firstElementChild.innerHTML == "<span>No chat</span>") {
      chat.firstElementChild.remove();
    } else {
      chat.innerHTML = "";
    }
    let messages = formatMessages(...chat_user_data.chat);
    if (messages.length > 0) {
      for (let index = 0; index < messages.length; index++) {
        const message = messages[index];
        if (
          (chat_sender == chat_user_data.chat[index].sender &&
            chat_receiver == chat_user_data.chat[index].receiver) ||
          (chat_sender == chat_user_data.chat[index].receiver &&
            chat_receiver == chat_user_data.chat[index].sender)
        ) {
          chat.innerHTML += message;
        }
      }
    } else {
      chat.innerHTML = "<div id='alert'><span>No chat</span></div>";
    }
  }
}

async function update_current_correspondent(member_name, status) {
  const correspondent_state_element = document.getElementById(
    "correspondent_status"
  );
  const correspondent_name = document.querySelector(
    ".chat-window .correspondent > div:last-child span:first-child"
  ).innerText;
  if (correspondent_name == member_name) {
    if (status !== "online") {
      correspondent_state_element.className = `status ${status.toLowerCase()}`;
    } else {
      correspondent_state_element.classList.remove("offline");
    }
    correspondent_state_element.innerText = status;
  }
}

/**
 * @param {WebSocket} socket
 */
function sendMessage(socket) {
  const chat_sender = document.getElementsByName("chat-message-sender")[0]
    .value;
  const chat_receiver = document.getElementsByName("chat-message-receiver")[0]
    .value;
  const chat_message_input = document.getElementsByName("chat-message")[0];
  const chat_message = chat_message_input.value;
  if (isset(chat_message)) {
    if (socket.readyState == 1) {
      let data = {
        action: "post_message",
        sender: chat_sender,
        receiver: chat_receiver,
        message: chat_message,
      };
      socket.send(JSON.stringify(data));
      chat_message_input.value = "";
    }
  }
}

function postMessage(message) {
  const chat = document.querySelector(".chat");
  const chat_receiver = document.getElementsByName("chat-message-receiver")[0]
    .value;
  const chat_sender = document.getElementsByName("chat-message-sender")[0]
    .value;
  if (
    (chat_sender == message.sender && chat_receiver == message.receiver) ||
    (chat_sender == message.receiver && chat_receiver == message.sender)
  ) {
    message = formatMessages(message)[0];
    if (chat.firstElementChild.innerHTML == "<span>No chat</span>") {
      chat.firstElementChild.remove();
    }
    chat.innerHTML += message;
  }
}

function formatMessages(...messages) {
  const chat_sender = document.getElementsByName("chat-message-sender")[0]
    .value;
  let messageDisplays = [];
  for (const message of messages) {
    let messageDisplay;
    if (chat_sender == message.sender) {
      messageDisplay = `<div class="sent-message"><div><p>${message.body}</p><img src="${message.avatar}"/></div><span>${message.createdAt}</span></div>`;
    } else if (chat_sender == message.receiver) {
      messageDisplay = `<div class="received-message"><div><img src="${message.avatar}"/><p>${message.body}</p></div><span>${message.createdAt}</span></div>`;
    }
    messageDisplays.push(messageDisplay);
  }
  return messageDisplays;
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
        users[j][1].className = `status ${new_states[i].state.toLowerCase()}`;
        // in case current chat participant being processed is the correspondent of the ex-student
        update_current_correspondent(
          new_states[i].exstudentName,
          new_states[i].state
        );
      }
    }
  }
}

function update_last_activity(socket) {
  const exStudentId = document.getElementsByName("chat-message-sender")[0]
    .value;
  let request = {
    action: "update_last_activity",
    exstudent: exStudentId,
  };
  let request_json = JSON.stringify(request);
  if (socket.readyState == 1) {
    socket.send(request_json);
  }
}

function update_status(exStudentId, status) {
  const member_status = document.getElementById("correspondent_status");
  const chatReceiver = document.getElementsByName("chat-message-receiver")[0]
    .value;
  if (exStudentId == chatReceiver) {
    member_status.innerHTML = status;
    if (status == "Offline") member_status.classList.add(status.toLowerCase());
    else member_status.classList.remove("offline");
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
      validity = validity || validation.validateText(element.value, 4);
    }
    if (element.type == "number" && isset(element.value)) {
      validity = validity || validation.validatePhone(element.value);
    }
    if (element.type == "email" && isset(element.value)) {
      validity = validity || validation.validateEmail(element.value);
    }
    if (element.type == "country" && isset(element.value)) {
      validity = validity || validation.validateCountry(element.value);
    }
    if (element.type == "textarea" && isset(element.value)) {
      validity = validity || element.value >= 50;
    }
  }
  if (!validity) {
    event.preventDefault();
  }
}

export default {
  openTab,
  sendMessage,
  postMessage,
  openChatRoom,
  update_status,
  updateChatUsers,
  updateChatWindow,
  validateUpdateForm,
  update_last_activity,
};
