import {
  isset,
  toggleBackgroundWrapperVisibility,
} from "/static/src/js/functions/random.js";

const newsletter_box = document.querySelector(".newsletter-box");
const parent_wrapper = newsletter_box.parentElement;
const [newsletter_name_input, newsletter_email_input] =
  newsletter_box.querySelectorAll("input");
const newsletter_button = newsletter_box.querySelector("button");

setTimeout(() => toggleBackgroundWrapperVisibility(parent_wrapper.id), 2000);

newsletter_button.addEventListener("click", () => {
  if (
    !isset(newsletter_name_input.value) &&
    !isset(newsletter_email_input.value)
  )
    return;

  const newsletter_user_info = {
    name: newsletter_name_input.value,
    email: newsletter_email_input.value,
  };
  let url = `/news/newsletter.php?name=${newsletter_user_info.name}&email=${newsletter_user_info.email}`;
  fetch(url)
    .then(response => response.text())
    .then(responseText => {
      if ("ok" === responseText)
        toggleBackgroundWrapperVisibility(parent_wrapper.id);
    });
});
