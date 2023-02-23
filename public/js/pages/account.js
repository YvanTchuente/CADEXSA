import {
  scrollToTop,
  openProfileTab,
  toggleModalVisibility,
  percentage,
} from "../../../../resources/js/functions/random.js";
import account from "../../../../resources/js/functions/account.js";

const chat_message_input = document.getElementsByName("chat-message")[0];
const exstudent_search_input = document.querySelector("input.exstudent-search");
const updateProfileForm = document.getElementById("profile-editor");
const chat_users_panel_opener = document.querySelector(
  ".chat-window .hamburger-icon"
);
const exstudent = document.getElementById("chat-message-sender").value; // ID of the logged-in ex-student
let intervalId;

let chat_server_socket;

// Establish a connection with the server
connect_to_chat_server(
  `ws://localhost:5000/services/chat.php?exstudent=${exstudent}`
);
// Establish a connection for streaming in of chat-related updates from the server
connect_to_chat_updates_server(
  `ws://localhost:5050/services/chat_updates.php?exstudent=${exstudent}`
);

// Edit profile form
const profile_editor = document.getElementById("profile-editor");
profile_editor.onsubmit = async (e) => {
  e.preventDefault();
  let formElements = [];
  (() => {
    for (
      let index = 0;
      index < profile_editor.querySelectorAll("input,textarea").length;
      index++
    ) {
      const element = profile_editor.querySelectorAll("input,textarea")[index];
      if (
        element.value &&
        !(element.name == "exStudentId" || element.name == "action")
      ) {
        formElements.push(element);
      }
    }
  })();
  if (formElements.length == 0) {
    return;
  }
  const formData = new FormData(profile_editor);
  const request = new Request("/services/account.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin",
  });
  const response = await fetch(request);
  if (response.ok) {
    const reply = await response.text();
    if (reply) {
      if (profile_editor.previousElementSibling.textContent !== reply) {
        const notification = document.createElement("div");
        notification.textContent = reply;
        notification.style.color = "#9a3a43";
        notification.style.marginBottom = "1em";
        notification.style.textAlign = "center";
        notification.style.padding = "0.3em 1em";
        notification.style.borderRadius = "0.5em";
        notification.style.backgroundColor = "#f8d7da";
        profile_editor.before(notification);
        scrollToTop();
        setTimeout(() => {
          notification.remove();
        }, 3000);
      }
    } else {
      window.location.reload();
    }
  }
};

const tablinks = document.getElementsByClassName("tablink");
tablinks[0].firstElementChild.addEventListener("click", (e) =>
  account.openTab(e, "profile", chat_server_socket)
);
tablinks[1].firstElementChild.addEventListener("click", (e) =>
  account.openTab(e, "messages", chat_server_socket)
);
tablinks[2].firstElementChild.addEventListener("click", (e) =>
  account.openTab(e, "settings", chat_server_socket)
);

document.querySelector("#account-nav .user-heading img").onclick = () => {
  toggleModalVisibility("picture-upload-modal");
};

const chat_users = document.querySelectorAll(".chat-window .user");
for (const chat_user of chat_users) {
  let targetExstudentId, requestingExstudentId;
  chat_user.addEventListener("click", (event) => {
    targetExstudentId = chat_user.getAttribute("data-targetExstudentId");
    requestingExstudentId = chat_user.getAttribute(
      "data-requestingExstudentId"
    );
    account.openChatRoom(
      event,
      targetExstudentId,
      requestingExstudentId,
      chat_server_socket
    );
  });
}

document.querySelector(".chat-window .input").onsubmit = (event) => {
  event.preventDefault();
};

const send_message_button = document.querySelector(
  ".chat-window .input button"
);
send_message_button.addEventListener("click", () =>
  account.sendMessage(chat_server_socket)
);

const avatar_upload_button = document.querySelector(".tabcontent#settings button");
avatar_upload_button.addEventListener("click", () => {
  toggleModalVisibility("picture-upload-modal");
});

openProfileTab();

chat_message_input.addEventListener("keyup", (event) => {
  if (event.key == "Enter") {
    const input_height = parseInt(
      getComputedStyle(chat_message_input).height.replace(/px/, "")
    );
    const new_input_height = input_height + 24;
    if (new_input_height < 160) {
      chat_message_input.style.height = `${input_height + 24}px`;
    }
  }
  if (chat_message_input.scrollHeight > 48 && chat_message_input.value === "") {
    chat_message_input.removeAttribute("style");
  }
});

chat_message_input.addEventListener("focus", () => {
  intervalId = setInterval(() => {
    typing(1);
  }, 100);
});

chat_message_input.addEventListener("blur", () => {
  clearInterval(intervalId);
  typing(0);
});

// Add an event listener to the menu button of the chat section
chat_users_panel_opener.addEventListener("click", (event) => {
  const users_panel = document.querySelector(".chat-window .users-panel");
  if (users_panel.classList.contains("open")) {
    users_panel.classList.remove("open");
    setTimeout(() => {
      users_panel.nextElementSibling.removeAttribute("style");
      users_panel.removeAttribute("style");
    }, 500);
  } else {
    users_panel.style.display = "flex";
    setTimeout(() => {
      users_panel.classList.add("open");
    }, 50);
  }
  event.currentTarget.classList.toggle("open");
});

exstudent_search_input.addEventListener("keyup", (event) => {
  const search_value = event.currentTarget.value;
  const chat_users = document.querySelectorAll(".chat-window .user");
  if (search_value.length > 0) {
    for (const chat_user of chat_users) {
      let name = chat_user.querySelector(".username").textContent;
      const search_expression = new RegExp(search_value, "i");
      if (!search_expression.test(name)) {
        chat_user.style.display = "none";
      }
    }
  } else {
    for (const chat_user of chat_users) {
      if (chat_user.style.display == "none") {
        chat_user.removeAttribute("style");
      }
    }
  }
});

updateProfileForm.addEventListener("submit", (e) =>
  account.validateUpdateForm(e, updateProfileForm)
);

// Function library

function connect_to_chat_updates_server(url) {
  const socket = new WebSocket(url);
  socket.onmessage = (e) => {
    const message = JSON.parse(e.data);
    account.updateChatUsers(message.states);
  };
  socket.onclose = (e) => {
    console.log(
      "The connection closed with the chat update server, restoring the connection in 5 seconds"
    );
    setTimeout(() => {
      connect_to_chat_updates_server(url); // Reconnects
    }, 5000);
  };
}

function connect_to_chat_server(url) {
  chat_server_socket = new WebSocket(url);
  chat_server_socket.onopen = () => {
    let users = document.querySelectorAll(".chat-window .users .user");
    if (users.length > 0) {
      users[0].click();
    }
  };
  chat_server_socket.onmessage = (e) => {
    const message = JSON.parse(e.data);
    const type = message.type;
    delete message.type;
    switch (type) {
      // updates the chat window with the new chat
      case "new_message":
        account.postMessage(message);
        break;
      // Updates the status of the ex-student
      case "member_status":
        account.update_status(message.exstudent, message.status);
        break;
      // Updates the chat window with the selected chat user
      case "chat_user_data":
        account.updateChatWindow(message);
        break;
    }
  };
  chat_server_socket.onclose = (e) => {
    console.log(
      "The connection closed with the chat server, restoring the connection in 5 seconds"
    );
    setTimeout(() => {
      connect_to_chat_server(url); // Reconnect
    }, 5000);
  };
}

function typing(status) {
  if (chat_server_socket.readyState == 1) {
    const exStudentId = document.getElementsByName("chat-message-sender")[0]
      .value; // ID of the ex-student typing
    const correspondentId = document.getElementById(
      "chat-message-receiver"
    ).value; // ID of the ex-student to notify of this event
    const message = {
      action: "update_typing_status",
      exstudent: exStudentId,
      correspondent: correspondentId,
      status: status,
    };
    chat_server_socket.send(JSON.stringify(message)); // sends the request
  }
}
