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
	let height = 400;
	if (window.pageYOffset >= height) {
		header.style.position = "fixed"
		header.style.top = "0";
		header.style.left = "0";	
		header.style.boxShadow = "0px 5px 5px rgba(0,0,0,15%)";
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
	var dropdowns = document.querySelectorAll(".dropdownMenu");
	for (dropdown of dropdowns) 
	{
		dropdown.style = "";
	}
	// Removes the class opened from all nice-selects
	var currents = document.querySelectorAll(".nice-select > span");
	for (icon of currents)
	{
		icon.classList.remove("opened");
	}

	// Open the specific dropdown
	var selector = "#"+niceSelect+" .dropdownMenu";
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

/*** Mobile nav dropdown function***/
function nav_drop() {
	var mlinks = document.getElementsByClassName("m-links")[0];
	var menubtn = document.getElementsByClassName("m-menu-btn")[0];
	var nav_visibility = mlinks.style.visibility;

	/*** Get the bars of the nav icon ***/
	var bar1 = document.querySelector(".m-menu-btn #menu-bar1");
	var bar2 = document.querySelector(".m-menu-btn #menu-bar2");
	var bar3 = document.querySelector(".m-menu-btn #menu-bar3");

	if(nav_visibility == "visible") 
	{
		mlinks.style = ""; 
		bar1.style = "";
		bar2.style = "";
		bar3.style = "";
	} 
	else 
	{
		if(window.matchMedia("(max-width:375px)").matches)
			mlinks.style.top = "100px";
		else
			mlinks.style.top = "60px";
		mlinks.style.opacity = "1";
		mlinks.style.visibility = "visible";

		/*** Animating the menu button ***/
		bar1.style.transform = "rotate(45deg)";
		bar1.style.position = "relative";
		bar1.style.bottom = "-11px";

		bar2.style.display = "none";

		bar3.style.transform = "rotate(-45deg)";
		bar3.style.position = "relative";
		if(window.matchMedia("(max-width: 375px)").matches) 
			bar3.style.top = "3px";
		else
			bar3.style.top = "1px";
	}
}

window.onload = function() 
{
	/* User panel dropdown menu and nice-selects */
	
	// Display user panel when a user is logged in
	if(document.querySelector(".dropbtn")) {
		let dropbtn = document.querySelector(".dropbtn");
		dropbtn.onclick = function()
		{
			var dropdown = document.querySelector(".user-panel .dropdown");
			dropdown.style.top = "100%";
			dropdown.style.opacity = "1";
			dropdown.style.visibility = "visible";
		}
	}

	window.addEventListener("click", (event) => {
		if(document.querySelector(".dropbtn"))
		{
			if (!event.target.matches('.dropbtn')) 
			{
				var dropdown = document.querySelector(".user-panel .dropdown");
				dropdown.style = "";
			}
		}
		if(document.querySelectorAll(".nice-select"))
		{
			if(!event.target.matches('.nice-select .current'))
			{
				var dropdowns = document.querySelectorAll(".dropdownMenu");
				for (dropdown of dropdowns) 
				{
					dropdown.style = "";
				}
				var currents = document.querySelectorAll(".nice-select > span");
				for (icon of currents)
				{
					icon.classList.remove("opened");
				}
			}
		}
	})
	/*** ---User panel dropdown menu--- ***/

	/*** Selection of option for the select elements of the news and events filter area ***/
	$("#nice-select-1 .dropdownMenu li").click(function()
	{
		value = $(this).html()
		$("#nice-select-1 .dropdownMenu li").removeClass("selected")
		$(this).addClass("selected")
		$("#nice-select-1 .current").html(value)
		$("#nice-select-1 option").removeAttr("selected")
		$("#nice-select-1 option:contains('"+value+"')").attr({'selected':''})
	})
	$("#nice-select-2 .dropdownMenu li").click(function()
	{
		value = $(this).html()
		$("#nice-select-2 .dropdownMenu li").removeClass("selected")
		$(this).addClass("selected")
		$("#nice-select-2 .current").html(value)
		$("#nice-select-2 option").removeAttr("selected")
		$("#nice-select-2 option:contains('"+value+"')").attr({'selected':''})
	})
	$("#nice-select-3 .dropdownMenu li").click(function()
	{
		value = $(this).html() // Equavalent to JS innerHtml
		$("#nice-select-3 .dropdownMenu li").removeClass("selected")
		$(this).addClass("selected")
		$("#nice-select-3 .current").html(value)
		$("#nice-select-3 option").removeAttr("selected")
		$("#nice-select-3 option:contains('"+value+"')").attr({'selected':''})
	})
	/*** ---Selection of option for the select elements of the news and events filter area--- ***/

	let passwrd = $("#password"); 
	let confirm_passwrd = $("#confirm-password"); 
	
	passwrd.keypress(function()
	{
		var val = $(this).val();
		passwrd.attr({'value':val});
	})

	confirm_passwrd.keypress(function()
	{
		var val = $(this).val();
		confirm_passwrd.attr({'value':val});
	})

	/*** Functions to handle password toggle visibility button ***/
	let password_btn = $("#hide_pass");
	password_btn.click(function()
	{
		var type = passwrd.attr('type');
		if (type == 'password')
		{
			passwrd.attr({'type':'text'});
			$("#hide_pass i").removeClass("fas fa-eye");
			$("#hide_pass i").addClass("fas fa-eye-slash");
		}
		else
		{
			passwrd.attr({'type':'password'});
			$("#hide_pass i").removeClass("fas fa-eye-slash");
			$("#hide_pass  i").addClass("fas fa-eye");
		}
	})

	let password_btn1 = $("#hide_confirm_pass");
	password_btn1.click(function()
	{
		var type = confirm_passwrd.attr('type');
		if (type == 'password')
		{
			confirm_passwrd.attr({'type':'text'});
			$("#hide_confirm_pass i").removeClass("fas fa-eye");
			$("#hide_confirm_pass i").addClass("fas fa-eye-slash");
		}
		else
		{
			confirm_passwrd.attr({'type':'password'});
			$("#hide_confirm_pass i").removeClass("fas fa-eye-slash");
			$("#hide_confirm_pass  i").addClass("fas fa-eye");
		}
	})
	/*** ---Functions to handle password toggle visibility button--- ***/

	counters = document.querySelectorAll(".counter"); 
	const speed = 100;

	window.addEventListener("scroll", () => {
	/*** Main page Counters JS functions ***/
	if (window.pageYOffset >= 1700)
	{
		counters.forEach(counter => {
			const updateCount = () => {
				const target = +counter.getAttribute('data-target');
				const count = +counter.innerText
	
				const increment = target / speed;
	
				if (count < target) {
					counter.innerText = Math.ceil(count + increment);
					setTimeout(updateCount, 1)
				} else {
					count.innerText = target;
				}
			}
			updateCount();
		});
	}
	/*** --- Main page Counters JS functions --- ***/
	})

	/* Starts carousels */
	start_carousels();
}