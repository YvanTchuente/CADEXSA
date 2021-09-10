function gotop() {
	window.scrollTo({
		top: "0px",
		left: "0px",
		behavior: "smooth"
	})
}

function fadeOut(elem, ms)
{
  	if(!elem)
	{ return; }

  	if(ms) {
    	let opacity = 1;
    	let timer = setInterval( function() {
     		opacity -= 50 / ms;
      		if( opacity <= 0 )
      		{
        		clearInterval(timer);
        		opacity = 0;
        		elem.style.display = "none";
        		elem.style.visibility = "hidden";
      		}
      		elem.style.opacity = opacity;
      		elem.style.filter = "alpha(opacity=" + opacity * 100 + ")";
    	}, 50 );
  	}
  	else
  	{
    	elem.style.opacity = 0;
    	elem.style.filter = "alpha(opacity=0)";
    	elem.style.display = "none";
   		elem.style.visibility = "hidden";
  	}
}

function sticky_header () {
	let header = document.querySelector("body > header");
	let height = header.clientHeight;
	if (window.pageYOffset >= height) {
		header.style.position = "fixed"
		header.style.top = "0";
		header.style.left = "0";	
		header.style.boxShadow = "0px 5px 10px rgba(0,0,0,25%)";
		header.classList.add("slidein");
	}
	else 
	{	
		header.style = "";
		header.classList.remove("slidein"); 
	}
}

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

/* JS functions to handle nice-select elements */
function openSelect(event,niceSelect) {
	// Closes all open nice select dropdowns
	var dropdowns = document.querySelectorAll(".nice-select .dropdown");
	for (dropdown of dropdowns) 
	{
		dropdown.style = "";
	}
	// Removes the class .opened from all nice-selects
	var currents = document.querySelectorAll(".nice-select > [class='current']");
	for (current of currents)
	{
		current.classList.remove("opened");
	}

	// Open the specific dropdown
	var selector = "#"+niceSelect+" .dropdown";
	var dropdown = document.querySelector(selector);
	dropdown.style.top = "105%";
	dropdown.style.opacity = "1";
	dropdown.style.visibility = "visible";

	// Rotates the arrow
	event.target.classList.add("opened");
}

// Special function for blur backgrounds
function toggle_visibility(id) {
	var elem = document.getElementById(id);
	var child = elem.querySelector("#"+id+" .update_profile");
	display = elem.style.display;
	if (display == "flex") 
	{
		elem.style.display = "none";
	}
	else
	{
		elem.style.display = "flex";
	}
}

/* Mobile nav dropdown function */
function nav_drop() {
	var mlinks = document.getElementsByClassName("m-links")[0];
	var menubtn = document.getElementsByClassName("m-menu-btn")[0];

	/* Get the bars of the nav icon */
	let bar // For use in loops to refer to any bar below
	let bar1 = document.querySelector(".m-menu-btn #menu-bar1");
	let bar2 = document.querySelector(".m-menu-btn #menu-bar2");
	let bar3 = document.querySelector(".m-menu-btn #menu-bar3");
	let bars = [bar1, bar2, bar3];

	if(mlinks.classList.contains("open")) 
	{
		mlinks.classList.remove("open");
		for(bar of bars) {
			bar.style = "";
		}
	} 
	else 
	{
		mlinks.classList.add("open");
		/*** Animating the menu button ***/
		for(bar of bars) {
			bar.style.position = "relative";
		}
		bar1.style.transform = "rotate(45deg)"
		bar1.style.bottom = "-11px";

		bar2.style.display = "none";

		bar3.style.transform = "rotate(-45deg)"; 
		bar3.style.top = "1px";
	}
}

/* Timers */
function start_timers() {
	let timers = document.querySelectorAll(".timer");
	for (const timer of timers) {
        let date = timer.getAttribute("data-date");
        // Initialization of an instance of the countdown
		let time_countdown = new countdown(timer, date);
        time_countdown.start();
	}
}