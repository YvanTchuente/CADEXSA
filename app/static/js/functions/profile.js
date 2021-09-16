/**
 * FUNCTIONS LIBRARY
 * PROFILE PAGE FUNCTIONS 
 *
 */

let interval; // Global variable for setInterval 

function openTab(event, tab)
{
    // Function for opening and closure of tabs in profile page
 
    let tabcontents, tabcontent, tablinks, tablink;
 
    tabcontents = document.getElementsByClassName("tabcontent");
    tablinks = document.getElementsByClassName("tablink");
 
    // Hides all tabs
    for (tabcontent of tabcontents)
    {
        tabcontent.style.display = "none";
    }
 
    // Remove the active class from tab links
    for (tablink of tablinks)
    {
        tablink.classList.remove("active");
    }
 
    // Display the current tab and add the active class
    document.getElementById(tab).style.display = "block";
    event.currentTarget.parentElement.classList.add("active");
}

async function openChatContent(event,userid,senderid)
{
    const chat_users = document.querySelectorAll(".chatbox .list_users .user");
    for(let chat_user of chat_users)
        chat_user.classList.remove("open");

    event.currentTarget.classList.add("open");
	const receiver = document.getElementById("chat_receiver");
	receiver.setAttribute("value",""+userid+"");

    // Update the chats 
    updateChatCorrespondent(userid); 
    updateChatsArea(userid,senderid);
            
    if(isset(interval))
		clearInterval(interval);
	else {
        is_typing(receiver.value);
		setTimeout(is_typing(receiver.value), 10000);
    }
}

async function updateChatCorrespondent(userid) 
{
    const avatar = document.querySelector(".chatbox .correspondent_info img");
    const username = document.getElementById("correspondent_name");
    const status = document.getElementById("correspondent_status");
    // Query strings
    const param1 = `?action=updateAvatar&correspondent=${userid}`;
    const param2 = `?action=updateName&correspondent=${userid}`;
    const param3 = `?action=updateStatus&correspondent=${userid}`;

    // Update correspondent's avatar
    fetch(`chatAction.php${param1}`)
        .then(response => {
            if(!response.ok) throw new Error("Network failure")
            response.text()
        })
        .then(src => avatar.src = src)
        .catch(reason => console.log(reason));
        
    // Update correspondent's name
    fetch(`chatAction.php${param2}`)
        .then(response => {
            if(!response.ok) throw new Error("Network failure")
            response.text()
        })
        .then(name => username.innerText = name)
        .catch(reason => console.log(reason));

    // Update correspondent's status
    fetch(`chatAction.php${param3}`)
        .then(response => {
            if(!response.ok) throw new Error("Network failure")
            response.text()
        })
        .then(status => status.innerText = status)
        .catch(reason => console.log(reason));
}

async function updateChatsArea(correspondentID,senderID) 
{
    const chats = document.querySelector("div.chats_area");
    const param = `?action=updateChats&correspondent=${correspondentID}&sender=${senderID}`;

    try {
        const updateChats = await fetch(`chatAction.php${param}`);
        if(updateChats.ok) {
            const new_chats = updateChats.text();
            chats.innerHTML = new_chats // updates the chat area to new chats
        }
    } catch (reason) {
        console.log(reason)
    }
}

async function send_chat()
{
	const chats = document.querySelector("div.chats_area");
	const chat_msg = document.getElementById("chat_msg").value;
	const send_chat = document.getElementById("send_chat");

	if(isset(chat_msg)) {
		const form = new FormData(send_chat);

        try {
            const request = new Request("chatAction.php",{
                method: 'POST',
                mode: 'cors',
                credentials: 'same-origin',
                body: form
            });

            const sendChat = await fetch(request);
            if(sendChat.ok) {
                const newChat = await sendChat.text();
                document.getElementById("chat_msg").value="";
                if(chats.children[0].id == "chat_alert") {
                    chats.removeChild(chats.children[0]);
				    chats.innerHTML += newChat;
                }
                chats.innerHTML += newChat;
            }
        } 
        catch (reason) {
            alert(reason);
        }
	}
}

async function refresh_picture(userid) {
	const profile_pictures = [document.querySelector(".user-heading a img"), document.querySelector(".user-panel .dropdown-menu img"), document.querySelector(".user-panel img")];

    const refresh = await fetch(`ProfilePicture.php?refresh=1&userID=${userid}`);
    const new_src = await refresh.text();
    for(const profile_picture of profile_pictures) {
		profile_picture.src = `/members/profile_pictures/${new_src}.jpg`;
	}
}

async function fetch_users_status() 
{
	const users = [], times = document.querySelectorAll("li.user span.time");
	const statuses = document.querySelectorAll("li.user > span");
    const param = "?action=fetch_users";

	for(let x=0; x < (times.length/statuses.length); x++)
	{
		for(let y=0; y < statuses.length; y++)
		{
			let user_block = [times[x],statuses[y]];
			users.push(user_block);
		}
	}

    const statusesUpdate = await fetch(`chatAction.php${param}`);
    const new_statuses = await statusesUpdate.json();
    // displaying the results
    for(let j=0; j<users.length; j++) 
	{
		for(let i=0; i<new_statuses.length; i++) 
		{
			if(j == results[i].n)
			{
				users[j][0].innerHTML = new_statuses[i].last_seen;
				users[j][1].className = `status ${new_statuses[i].status}`;
			}
		}
	}
}

function update_last_activity() 
{
    fetch(`chatAction.php?action=updateLastActivity`);
}

async function is_typing(correspondent) 
{
	const status = document.getElementById("correspondent_status");
    const param = `?action=is_typing&correspondent=${correspondent}`;

    try {
        const isTyping = await fetch(`chatAction.php${param}`);
        if (isTyping.ok) {
            const typing_status = await isTyping.text();
            status.innerText = typing_status;
        }
    } catch(reason) {
        console.log(reason);
    }
}

function typing(n) 
{
    let param = "?action=";
    switch (n) {
        case 1: 
            param += "typing";
            break;
        case 0: 
            param += "not_typing";
            break;
    }
    // sends the fetch request
    fetch(`chatAction.php${param}`);
}

function updateProfile(event, form) {
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
        if (element.type == "textarea" && isset(element.value)) {
            validity = validity || (element.value >= 50);
        }
    }
    if (!validity) {
        event.preventDefault();
        console.log(validity);
        console.log(selectedInputs);
    }
}