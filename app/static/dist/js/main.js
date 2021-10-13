const pathname = window.location.pathname; // Location of the executing script
const path_to_signup = new RegExp("^\/members/register\.(html|php)+$");
const path_to_profile = new RegExp("^\/members/profile\.(html|php)+\?\w*$");
const path_to_recoveryACC = new RegExp("^\/members/recover_account\.(html|php)+\?\w*$");
const sticky_header_enabled_pages = ["/","/news/","/events/","/gallery/","/about_us/","/contact_us/"];

document.onreadystatechange = function() 
{
	const loader = document.querySelector("#loader");
	if (document.readyState == "complete") {
		try {
			fadeOut(loader,500);
		} catch (error) {
			console.log(error);
		}
	}
}

window.onload = function() 
{	
	/* Event Handler for clicks on empty sections of main page */
	window.addEventListener("click", event => {
		if(document.querySelector(".dropbtn"))
		{
			if (!event.target.matches('.dropbtn')) 
			{
				const dropdown = document.querySelector(".user-panel .dropdown");
				dropdown.style = "";
			}
		}
		if(document.querySelectorAll(".nice-select"))
		{
			if(!event.target.matches('.nice-select .current'))
			{
				const dropdowns = document.querySelectorAll(".dropdown");
				for (dropdown of dropdowns) 
				{
					dropdown.classList.remove("opened");
				}
				const currents = document.querySelectorAll(".nice-select > span");
				for (icon of currents)
				{
					icon.classList.remove("opened");
				}
			}
		}
	})

	/* Always display User panel */
	let userPanel;
	if (userPanel = document.querySelector(".user-panel")) {
		document.getElementById("topnav").style.display = "block";
		if (window.matchMedia("screen and (max-width: 992px)").matches) {
			document.getElementById("contact-us").style.display = "none";
		}
		window.matchMedia("screen and (max-width: 992px)").onchange = (event) => {
			if(event.matches)
				document.getElementById("contact-us").style.display = "none";
			else 
				document.getElementById("contact-us").style.display = "flex";
		}
	}

	/* Main page Counters JS functions */
	const counters = document.querySelectorAll(".counter");
	const regexp = new RegExp("\/about_us$");
	let offset = 1700;
	if(regexp.test(pathname)) offset = 400; 

	/* Event handler for page scrolls */
	window.addEventListener("scroll", () => {
		if (window.scrollY >= offset) {
			const speed = 500;
			counters.forEach(counter => {
				const updateCount = () => {
					const target = +counter.getAttribute('data-target');
					const count = +counter.innerText
	
					const increment = target / speed;
	
					if (count < target) 
					{
						counter.innerText = Math.ceil(count + increment);
						setTimeout(updateCount, 1)
					} else {
						count.innerText = target;
					}
				}
				updateCount();
			})
		}

		if(sticky_header_enabled_pages.includes(pathname)) sticky_header();
	})

	// Append an event listener to the goto top button
	const gotop_btn = document.getElementById("gotop_btn");
	gotop_btn.addEventListener("click", () => gotop())

	// Append an event listener to the menu wrapper
	const main_menu_btn = document.querySelector("header div.menu-wrapper");
	main_menu_btn.addEventListener("click", () => drop_menu())

	// Display user panel when a user is logged in
	const dropbtn = document.querySelector(".dropbtn");
	if(dropbtn) {
		dropbtn.onclick = () => {
			const dropdown = document.querySelector(".user-panel .dropdown");
			dropdown.style.top = "100%";
			dropdown.style.opacity = "1";
			dropdown.style.visibility = "visible";
		}
	}

	// Selection of option for the select elements of the nice-selects
	const nice_select_options = document.querySelectorAll(".nice-select .dropdown li");
	for (const select_option of nice_select_options) {
		// Attaches an event handler to each nice-select list item
		select_option.addEventListener("click", event => {
			const target = event.target;
			const value = target.innerText;
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
			for (const option of select.children)
			{
				option.removeAttribute("selected");
				if(option.innerText === value) {
					option.setAttribute("selected","");
				}
			}	
		})
	}

	/************************************************************************************
	 *********      GENERAL FORM INPUTS EVENT LISTENERS      			       **********
	 ************************************************************************************/

	// Passwords
	let passwords = document.querySelectorAll("input[type='password']");
	for (const password of passwords) {
		if(password.id === "confirm-password") continue;
		password.addEventListener("keyup", event => {
			const target = event.currentTarget;
			const parent = target.parentElement;
			const value = target.value;

			if(value) {
				event.currentTarget.setAttribute("value",value);
				if(path_to_signup.test(pathname) || path_to_recoveryACC.test(pathname)) {
					if(!validatePassword(value)) {
						if(!target.nextElementSibling.nextElementSibling) {
							const msg = element("div","error","Password should contain a minimum of 8 characters");
							target.style.borderColor = "red";
							parent.appendChild(msg);
						}
					} else {
						if(target.nextElementSibling.nextElementSibling) {
							if(target.nextElementSibling.nextElementSibling.matches(".error")) {
								parent.removeChild(target.nextElementSibling.nextElementSibling);
								target.style.borderColor = "green";
							}
						}
					}
				}
			} else {
				target.removeAttribute("value");
				target.removeAttribute("style");
				if(target.nextElementSibling.nextElementSibling) {
					if(target.nextElementSibling.nextElementSibling.matches(".error")) {
						parent.removeChild(target.nextElementSibling.nextElementSibling);
					}
				}
			}
		})
	}

	// Email inputs
	let emails = document.querySelectorAll("input[type='email']");
	for (const email of emails) {
		email.addEventListener("keyup", event => {
			const target = event.currentTarget;
			const parent = target.parentElement;
			const value = target.value;
			
			if(value) {
				// If email input is not a real email address
				if(!validateEmail(value)) {
					if(!target.nextElementSibling) {
						const msg = element("div","error","Invalid email address");
						target.style.borderColor = "red";
						parent.appendChild(msg);
					}
				} else  { 
					if(target.nextElementSibling) {
						if(target.nextElementSibling.matches(".error")) {
							parent.removeChild(target.nextElementSibling);
							target.style.borderColor = "green";
						}
					}
				}	
			} else {
				target.removeAttribute("style");
				if(target.nextElementSibling) {
					if(target.nextElementSibling.matches(".error")) {
						parent.removeChild(target.nextElementSibling);
					}
				}
			}
		})
	} 

	// Phone numbers inputs
	const phoneNumbers = document.querySelectorAll("input[type='number']#phoneNumber");
	for (const phoneNumber of phoneNumbers) {
		phoneNumber.addEventListener("keyup", event => {
			const target = event.currentTarget;
			const parent = target.parentElement;
			const value = target.value;

			if(value) {
				if(!validatePhone(value)) {
					if(!target.nextElementSibling) {
						const msg = element("div","error","Invalid phone number");
						target.style.borderColor = "red";
						parent.appendChild(msg);
					}
				} else  { 
					if(target.nextElementSibling) {
						if(target.nextElementSibling.matches(".error")) {
							parent.removeChild(target.nextElementSibling);
							target.style.borderColor = "green";
						}
					}
				}	
			} else {
				target.removeAttribute("style");
				if(target.nextElementSibling) {
					if(target.nextElementSibling.matches(".error")) {
						parent.removeChild(target.nextElementSibling);
					}
				}
			}
		})
	}

	// Toggle visiblity button
	let toggle_btns = document.getElementsByClassName("password-visibility-btn");
	for (const toggle_btn of toggle_btns) {
		toggle_btn.addEventListener("click", event => {
			const target = event.currentTarget;
			const icon = target.firstElementChild;
			const input = target.previousElementSibling;
			const type = input.getAttribute("type");

			if(type === "text") {
				input.setAttribute("type","password");
				icon.classList.remove("fa-eye-slash");
				icon.classList.add("fa-eye");
			} else {
				input.setAttribute("type","text");
				icon.classList.remove("fa-eye");
				icon.classList.add("fa-eye-slash");
			}
		})
	}

	// IF URL IS SET TO POINT PAGES CONATINING SIGN UP FORMS
	if(path_to_signup.test(pathname)) 
	{
		const countryInput = document.querySelector("input[type='text']#country");
		// Country Input Event
		countryInput.addEventListener("keyup", event => {
			const target = event.currentTarget;
			const parent = target.parentElement;
			const value = target.value;
			if(value) {
				if(!validateCountry(value)) {
					if(!target.nextElementSibling) {
						const msg = element("div","error","Invalid country name");
						parent.appendChild(msg);
					}
				} else  { 
					if(target.nextElementSibling) {
						if(target.nextElementSibling.matches(".error")) {
							parent.removeChild(target.nextElementSibling);
						}
					}
				}	
			} else {
				if(target.nextElementSibling) {
					if(target.nextElementSibling.matches(".error")) {
						parent.removeChild(target.nextElementSibling);
					}
				}
			}
		})

		const textarea = document.querySelector("textarea#aboutme");
		// Sign up textarea: On Key up Event
		textarea.addEventListener("keyup", event => {
			const target = event.currentTarget;
			const parent = target.parentElement;
			const value = target.value;
			if(value) {
				if(!validateTextArea(value)) {
					if(!target.nextElementSibling) {
						const msg = element("div","error","Please provide a short description");
						parent.appendChild(msg);
					}
				} else  { 
					if(target.nextElementSibling) {
						if(target.nextElementSibling.matches(".error")) {
							parent.removeChild(target.nextElementSibling);
						}
					}
				}	
			} else {
				if(target.nextElementSibling) {
					if(target.nextElementSibling.matches(".error")) {
						parent.removeChild(target.nextElementSibling);
					}
				}
			}
		})

		// Sign up textarea: On Change Event
		textarea.addEventListener("change", event => {
			const target = event.currentTarget;
			const parent = target.parentElement;
			const value = target.value;
			if(value) {
				if(value.length < 50) {
					if(!target.nextElementSibling) {
						const msg = element("div","error","Too scanty description");
						parent.appendChild(msg);
					}
				} else  { 
				if(target.nextElementSibling) {
					if(target.nextElementSibling.matches(".error")) {
							parent.removeChild(target.nextElementSibling);
						}
					}
				}	
			} else {
				if(target.nextElementSibling) {
					if(target.nextElementSibling.matches(".error")) {
						parent.removeChild(target.nextElementSibling);
					}
				}
			}
		})
		
		const sign_up_forms = document.querySelectorAll("form.sign_up");
		for (const form of sign_up_forms) 
		{
			// On SIGN UP form submit
			form.addEventListener("submit", event => {
				const target = event.currentTarget; 
				let firstElement;
				const textInputs = Array.from(target.querySelectorAll("input[type='text']"));
				if(target.id === "register-form") {
					const passwords = Array.from(target.querySelectorAll("input[type='password']"));
					firstElement = target.querySelector("label[for='first-name']");
				} else 
					firstElement = target.querySelector("div:first-child");
				const error_msgs = target.querySelectorAll("div.error");
				let error_msg, formdata;
				if(isset(passwords)) {
					formdata = {
						textInputs: textInputs,
						passwords: passwords
					}
				} else {
					formdata = { textInputs: textInputs }
				}
				if(error_msgs.length == 0) {
					if(error_msg = validateSignUp(formdata,target.id)) {
						event.preventDefault();
						let i=1;
						for (const msg of error_msg) {
							const error = element("div","error",msg);
							target.insertBefore(error,firstElement);
							error.id = `error_${i}`;
							i++;
						}
						setTimeout(() => {
							for(let j=1; j <= i; j++) {
								const err_msg = target.querySelector(`div.error#error_${j}`);
								target.removeChild(err_msg);
							}
						}, 5000);
						gotop(); // Scroll page up to the top
					}
				} else event.preventDefault();
			})
			// On SIGN UP form change
			form.addEventListener("change", event => {
				const target = event.currentTarget;
				const error_msgs = target.querySelectorAll("div.error");
				const submitBtn = target.querySelector("button.form-btn");
				if(error_msgs.length > 0) { 
					submitBtn.setAttribute("disabled","true");
				} else {
					if(submitBtn.getAttribute("disabled")) {
						submitBtn.removeAttribute("disabled");	
					}
				}
			})
		}
	}
	// END

	/************************************************************************************
	 *********      PROFILE PAGE INPUTS' EVENT LISTENERS      			       **********
	 ************************************************************************************/
	// IF URL IS SET TO POINT PROFILE PAGE
	if(path_to_profile.test(pathname)) {
		const upload_form = document.getElementById("profile_picture");
		const input_picture = document.getElementById("input_picture");
		const input_text = document.getElementById("input_text");
		const preview = document.getElementById("picture_preview");
		const upload_btn = document.getElementById("upload_btn");
		const cancel_btn = document.getElementById("cancel_btn");
		const userid = document.getElementById("userid");
		const chat_msg = document.getElementById("chat_msg");
		const chats_menu_btn = document.querySelector("div.chatbox div.menu-wrapper");
		const updateProfileForm = document.getElementById("updateProfile");

		/* Add an event listener to the menu button of the chat section */
		chats_menu_btn.addEventListener("click", () => {
			toggleOpen();
		})

		cancel_btn.addEventListener("click", () => toggle_visibility("b1"))
		chat_msg.addEventListener("focus", () => typing(1))
		chat_msg.addEventListener("blur", () => typing(0))

		// Read file immediated after its selection by the user
		input_picture.addEventListener("change", event => {
			const target = event.currentTarget;
			input_text.innerHTML = target.files[0].name;
			const reader = new FileReader();
		
			reader.onloadstart = () => {
				for (child of preview.childNodes)
					child.remove();
		
				const loading = element("span",null,"Loading thumbnail...");
				loading.id = "loading";
				preview.appendChild(loading);
			}
			reader.onload = () => {
				const loading = document.getElementById("loading");
				const img = document.createElement("img");
				img.src = reader.result;
				preview.replaceChild(img,loading);
			}
			reader.readAsDataURL(target.files[0]);
		})

		upload_btn.addEventListener("event",() => {
			if (isset(input_text.innerHTML)) {
				const form = new FormData(upload_form);
				const request = new Request("ProfilePicture.php",{
					method: 'POST',
					headers: { 'Content-Type': 'multipart/form-data' },
					mode: 'cors',
					credentials: 'same-origin',
					body: form
				});

				// Uploads the file
				fetch(request)
					.then(response => {
						if(!response.ok) 
							throw new Error("Network operation failed")
						return response.text();
					})
					.then(text => {
						cancel_btn.click();
						setTimeout(refresh_picture(userid.getAttribute("value")),2000);
					})
					.catch(reason => alert(reason));
			}
		})

		updateProfileForm.addEventListener("submit",event => updateProfile(event,updateProfileForm))
	}
	// END

	/* Accordions */
	const accordions = document.querySelectorAll(".accordion");
	for (const accordion of accordions) {
		const icon = accordion.children[0].children[0];

		accordion.addEventListener("click", event => {
			if(!(event.currentTarget.classList.contains("open")))
			{ 
				icon.style.transform = "rotate(180deg)";
				accordion.classList.add("open");
			}
			else
			{
				icon.removeAttribute("style");
				accordion.classList.remove("open");
			}
		});
	}

	start_carousels();
	start_countdowns();
}
/**
 * Carousel Class
 */

// Constants
const PREV = "prev";
const NEXT = "next";

/*
 ******************************************************
 ***** Class Definition                           *****
 ******************************************************
 */

class carousel 
{
    constructor(element) {
        this.element = element;
        this.items = element.children; // Carousel items
        this.length = element.children.length;
        this.index = 0; // Internal Pointer
        this.duration = 10000; // Carousel duration time in miliseconds
        this.ACTIVE_CLASSNAME = "active";
        this.special_carousels = ['head-carousel']; // special carousels
    }

    // Public methods

    start() {
        this._slide(NEXT);
        // setInterval(() => {
        //     this._slide(NEXT);
        // }, this.duration);
    }

    prev() {
        this._slide(PREV);
    }

    next() {
        this._slide(NEXT);
    }

    // Private methods

    _movePointer() {
        this.index = this.index + 1;
    }

    _slide(element) {
        if(this.index > (this.length - 1)) {
            this.index = 0; 
        }

        let index, activeElement, previousElement;
        let active = this.ACTIVE_CLASSNAME;
        if(this.special_carousels.includes(this.element.id)) {
            this.ACTIVE_CLASSNAME = 'f_active';
            active = this.ACTIVE_CLASSNAME;
        }

        switch (element) {
            case "next":
                for (const item of this.items) {
                    if(item.classList.contains("previous") || item.classList.contains("next")) {
                        break;
                    } else {
                        this.index = this.index + 1; // Moves the pointer by 1
                        index = this.index - 1; // Get the pointer to the active element
                        activeElement = this.items[index];
                        // Get the previous element
                        if(index > 0) 
                            previousElement = this.items[index-1]; 
                        else if(index <= 0)
                            previousElement = this.items[this.length-1];

                        activeElement.classList.add(active,"next");
                        previousElement.classList.add("previous");   

                        setTimeout(() => {
                            for (const item of this.items) {
                                item.classList.remove(active,"previous","next");
                            }
                            activeElement.classList.add(active);
                            this.index = this.index - 1;
                            // Moves the pointer to the next element
                            this._movePointer();
                        },500);
                        break;
                    }
                }
                break;
            case "prev":
                for (const item of this.items) {
                    if(item.classList.contains("previous") || item.classList.contains("next")) {
                        break;
                    } else {
                        this.index = this.index - 1; // Moves the pointer by 1
                        index = this.index + 1; // Get the pointer to active element
                        activeElement = this.items[index];
                        // Get the previous element
                        if(index > 0) 
                            previousElement = this.items[index-1]; 
                        else if(index <= 0)
                            previousElement = this.items[this.length-1];

                        activeElement.classList.add(active,"next-prev");
                        previousElement.classList.add("previous-next"); 

                        setTimeout(() => {
                            // Removes the class "active" from all items
                            for (const item of this.items) {
                                item.classList.remove(active,"previous-next","next-prev");
                            }
                            activeElement.classList.add(active); // Display the item
                            this.index = this.index + 1;
                            // Moves the pointer to the next element
                            this._movePointer();
                        },500);
                        break;
                    }
                }
                break;
        }
    }
}
/*
 * Timer Countdown Class
 */

/*
 ******************************************************
 ***** Class Definition                           *****
 ******************************************************
 */

class countdown {
    constructor(element, event_date) {
        this.date = new Date(event_date);
        this.element = element;
    }

    // Public

    start() {
        // Starting the timer
        setInterval(() => {
            this._update();
        }, 1000);
    }

    // Private 

    _diff() {
        // Calculate the difference of time with the current date
        let datediff = new date_diff(this.date);
        return datediff.diff();
    }

    _update() {
        // Update the timer labels
        let day_label = this.element.children[0].children[1];
        let hour_label = this.element.children[1].children[1];
        let minute_label = this.element.children[2].children[1];
        let second_label = this.element.children[3].children[1];

        // Get the time difference
        let time_diff = this._diff();

        if (time_diff) {
            day_label.innerText = time_diff.day;
            hour_label.innerText = time_diff.hour;
            minute_label.innerText = time_diff.minute;
            second_label.innerText = time_diff.second; 
        } else {
            day_label.innerText = 0;
            hour_label.innerText = 0;
            minute_label.innerText = 0;
            second_label.innerText = 0;
        }
    }
}
/*
 * Date Difference Class
 */

// Constant
const toSec = 1000;
const toMin = toSec * 60;
const toHour = toMin * 60;
const toDay = toHour * 24;

/*
 ******************************************************
 ***** Class Definition                           *****
 ******************************************************
 */

class date_diff {
    constructor(date) {
        this.date = date;
    }

    done() {
        if (!this.diff()) {
            return true;
        }
    }

    diff() {
        let currentDate = new Date();
        let diff = this.date.getTime() - currentDate.getTime();

        let time_diff = false;

        if (diff > 0) {
            // Computation of the time difference
            let day = Math.floor(diff / toDay);
            diff = diff - (day * toDay) // Remove the number of days from the difference.

            let hour = Math.floor(diff / toHour);
            diff = diff - (hour * toHour); // Remove the number of hours from the difference

            let minute = Math.floor(diff / toMin);
            diff = diff - (minute * toMin); // Remove the number of minutes from the difference

            let second = Math.floor(diff / toSec);

            // Stores the result in an object and is returned by the method
            time_diff = {
                second: second,
                minute: minute,
                hour: hour,
                day: day
            }
        }

        return time_diff;
    }
}
/**
 * FUNCTIONS LIBRARY
 * HEADER SECTION FUNCTIONS 
 *
 */

function sticky_header () {
	let header = document.querySelector("body > header");
	let height = header.clientHeight;
	if (window.pageYOffset >= height) {
		header.classList.add("slidein","scrollState");
	} else {	
		header.classList.remove("slidein","scrollState"); 
	}
}

/* Mobile nav dropdown function */
function drop_menu() {
	const menu_links = document.querySelector("header div.menu-links");
	const menubtn = document.querySelector("header div.menu-wrapper div.menu");

	if(menu_links.classList.contains("open")) {
		menu_links.classList.remove("open");
		if (document.querySelector(".user-panel")) menu_links.removeAttribute("style");
	} else {
		menu_links.classList.add("open");
		if (document.querySelector(".user-panel")) menu_links.style.top = "100px";
	}
	menubtn.classList.toggle("open");
}
/**
 * FUNCTIONS LIBRARY
 * NICE SELECT CLASS FUNCTIONS 
 */

/**
 * Opens a nice-select element
 * @param {Event} event Event Object
 * @param {HTMLElement} niceSelect The HTML nice-select element
 */
function openSelect(event,niceSelect) {
	// Closes all open nice select dropdowns
	const dropdowns = document.querySelectorAll(".nice-select .dropdown");
	for (const dropdown of dropdowns) 
	{
		dropdown.classList.remove("opened");
	}
	// Removes the class .opened from all nice-selects
	const currents = document.querySelectorAll(".nice-select > span");
	for (const current of currents)
	{
		current.classList.remove("opened");
	}

	// Open the specific dropdown
	const selector = "#"+niceSelect+" .dropdown";
	const dropdown = document.querySelector(selector);
	dropdown.classList.add("opened");

	// Rotates the arrow
	event.target.classList.add("opened");
}
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
/**
 * FUNCTIONS LIBRARY
 * MISCELLANEOUS FUNCTIONS
 */

function gotop() {
	window.scrollTo({
		top: "0px",
		left: "0px",
		behavior: "smooth"
	})
}

/**
 * Fades out gradually an element
 * @param {HTMLElement} elem The HTML element
 * @param {number} ms Time in milliseconds
 */
function fadeOut(elem, ms) {
	if(!elem)
	{ return null }
  	if(ms) {
    	let opacity = 1;
    	let timer = setInterval(() => {
     		opacity -= 50 / ms;
      		if(opacity <= 0)
      		{
        		clearInterval(timer);
        		opacity = 0;
				document.body.removeChild(elem);
      		}
      		elem.style.opacity = opacity;
      		elem.style.filter = "alpha(opacity=" + opacity * 100 + ")";
    	},50);
  	}
  	else
  	{
    	elem.style.opacity = 0;
    	elem.style.filter = "alpha(opacity=0)";
    	elem.style.display = "none";
   		elem.style.visibility = "hidden";
  	}
}

/**
 * Activates all existings carousels
 */
 function start_carousels () {
	let carousel_prev, carousel_next, slides; 
	if(slides = document.querySelectorAll(".carousel")) {
		for (const slide of slides) {
			const id = slide.id;
			let slideItem = new carousel(slide);
			slideItem.start();

			if((carousel_prev = document.querySelectorAll("#"+id+" + .carousel-nav [data-ride='prev']")) && (carousel_next = document.querySelectorAll("#"+id+" + .carousel-nav [data-ride='next']")))
			{
				for (const prev of carousel_prev) {
					prev.addEventListener("click", () => {
						slideItem.prev();
					});
				}

				for (const next of carousel_next) {
					next.addEventListener("click", () => {
						slideItem.next();
					});
				}
			}
		}
	}
}

/**
 * Toggles blur backgrounds open
 * @param {string} id Blurred background's ID
 */
function toggle_visibility(id) {
	const elem = document.getElementById(id);
	const child = elem.firstElementChild;
	let display = elem.style.display;
	if (display == "flex") {
		child.classList.remove("open");
		setTimeout(() => elem.removeAttribute("style"), 300);
	}
	else {
		elem.style.display = "flex";
		setTimeout(() => child.classList.add("open"),100); 
	}
}

/**
 * Toggle the state of chat users panel: open or closed
 */
function toggleOpen() {
	const user_panel = document.querySelector("div.chatbox div.chat_users");
	const menubtn = document.querySelector("div.chatbox div.menu-wrapper div.menu");
	const chats_section = user_panel.nextElementSibling;

	if(user_panel.classList.contains("open")) {
		user_panel.classList.remove("open");
		setTimeout(() => {
			chats_section.removeAttribute("style");
		}, 500);
	}
	else {
		user_panel.classList.add("open");
		chats_section.style.width = "60%";
	}
	menubtn.classList.toggle("open");
}

/**
 * Starts all countdowns of events
 */
function start_countdowns() {
	let countdowns = document.querySelectorAll(".timer");
	for (const t_countdown of countdowns) {
        let date = t_countdown.getAttribute("data-date");
        // Initialization of an instance of the countdown
		let time_countdown = new countdown(t_countdown, date);
        time_countdown.start();
	}
}

/**
 * Checks the existence of a variable.
 * @param {any} variable 
 * @returns Either true or false
 */
function isset(variable) {
	// Determines if a variable is set to a value
	return (typeof variable == 'undefined') || variable === null || variable.length == 0 ? false : true; 
}

/**
 * Creates a new element
 * @param {string} type Element's tag name
 * @param {string} class_list Element's class list
 * @param {string} innertext Element's text content
 * @returns The created element
 */
function element(type, class_list = NULL, innertext) {
	// Generates a new document element
	let element = document.createElement(type);
	element.classList.value = class_list;
	element.innerText = innertext;
	return element;
}

/**
 * FUNCTIONS LIBRARY
 * VALIDATION FUNCTIONS 
 */

/**
 * Verifies email addresses
 * @param {string} email Email to be checked 
 * @returns {boolean} validity
 */
function validateEmail(email) {
	let validity;
	const mail_format = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	// If email input is a real email address
	if (email.match(mail_format)) validity = true;
	else validity = false;
	return validity;
}

/**
 * Verifies passwords
 * @param {string} password Password
 * @returns {boolean} Either True or False
 */
function validatePassword(password) {
	let validity;
	if (/\w{8}/.test(password)) validity = true;
	else validity = false;
	return validity;
}

/**
 * Verifies that a string "name" has "n" minimum number of characters
 * @param {string} name Name or Username
 * @param {number} n Number of characters
 * @returns {boolean}
 */
function validateText(name, n) {
	let validity;
	let regexp = new RegExp("\\w{" + n + "}");
	if (regexp.test(name)) validity = true;
	else validity = false;
	return validity;
}

/**
 * Verifies that the textarea content has not more than 50 words
 * @param {string} text Textarea content
 * @returns {boolean} Either True or False
 */
function validateTextArea(text) {
	const words = text.split(" ");
	const word_length = words.length;
	if (word_length > 50) validity = false;
	else validity = true;
	return validity;
}

/**
 * Verifies that the phone number has exactly 9 digits
 * @param {number} phone_number Phone number
 * @returns {boolean} Either True or False
 */
function validatePhone(phone_number) {
	if (/^\d{9}$/.test(phone_number)) validity = true;
	else validity = false;
	return validity;
}

// Fetching valid countries from the json file
let valid_countries;
if (/^\/members\/(register|profile){1}\.(html|php){1}$/.test(window.location.pathname)) {
	fetch("/static/database/valid_countries.json")
		.then(response => response.json())
		.then(json => {
			valid_countries = json.valid_countries;
		})
		.catch(reason => console.log(reason));
}

/**
 * Verifies that the country inputted is an existing country 
 * @param {string} country Country name
 * @returns {boolean} Either True or False
 */
function validateCountry(country) {
	let validity = false;
	let escaped = country.replace(/[\\[.+*?(){|^$]/g, "\\$&");
	const regexp = new RegExp("\\b" + escaped + "\\b", "i");
	for (let i = 0; i < valid_countries.length; i++) {
		if (valid_countries[i].match(regexp)) {
			validity = true;
			break;
		}
	}
	return validity;
}

function validateSignUp(formData, formId) {
	let msg = [];
	const formIds = ["register-form", "updateProfile"];
	// In case of passwords mismatch
	if (formId == formIds[0] && formData.passwords[0].value !== formData.passwords[1].value) msg.push("Passwords mismatched");
	// In case of invalid username or names
	if (!(validateText(formData.textInputs[0].value, 4) && validateText(formData.textInputs[1].value, 4) && validateText(formData.textInputs[2].value, 4))) msg.push("Invalid names or username");
	// In case of invalid city names
	if (!(validateText(formData.textInputs[3].value, 5))) msg.push("Invalid city name");
	return msg.length > 0 ? msg : null;
}
//# sourceMappingURL=main.js.map