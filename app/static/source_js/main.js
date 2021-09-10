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
				var dropdowns = document.querySelectorAll(".dropdown");
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
	/* ---User panel dropdown menu--- */

	/* Selection of option for the select elements of the news and events filter area */
	$("#nice-select-1 .dropdown li").click(function()
	{
		value = $(this).html()
		$("#nice-select-1 .dropdown li").removeClass("selected")
		$(this).addClass("selected")
		$("#nice-select-1 .current").html(value)
		$("#nice-select-1 option").removeAttr("selected")
		$("#nice-select-1 option:contains('"+value+"')").attr({'selected':''})
	})
	$("#nice-select-2 .dropdown li").click(function()
	{
		value = $(this).html()
		$("#nice-select-2 .dropdown li").removeClass("selected")
		$(this).addClass("selected")
		$("#nice-select-2 .current").html(value)
		$("#nice-select-2 option").removeAttr("selected")
		$("#nice-select-2 option:contains('"+value+"')").attr({'selected':''})
	})
	$("#nice-select-3 .dropdown li").click(function()
	{
		value = $(this).html() // Equavalent to JS innerHtml
		$("#nice-select-3 .dropdown li").removeClass("selected")
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
	/* --- end --- */
	})

	/* Accordions */
	accordions = document.querySelectorAll(".accordion");
	for (const accordion of accordions) {
		var icon = accordion.children[0].children[0];

		accordion.addEventListener("click", () => {
			if(!(accordion.classList.contains("open")))
			{ 
				icon.style.transform = "rotate(180deg)";
				accordion.classList.add("open");
			}
			else
			{
				icon.style = "";
				accordion.classList.add("closed");
				setTimeout(() => {
					accordion.classList.remove("open");
					accordion.classList.remove("closed");
				}, 500);
			}
		});
	}

	/* Starts carousels */
	start_carousels();

	/* Starts Timers */
	start_timers();
}