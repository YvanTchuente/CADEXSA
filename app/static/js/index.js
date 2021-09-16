const pathname = window.location.pathname; // Location of the executing script
const path_to_signup = new RegExp("^\/members/register\.(html|php)+$");
const path_to_profile = new RegExp("^\/members\/profile\.(html|php)+\?\w*$");
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
				if(path_to_signup.test(pathname)) {
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