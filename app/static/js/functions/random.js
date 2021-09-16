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
