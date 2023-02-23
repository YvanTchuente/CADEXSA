import {
  isset,
  element,
  scrollToTop,
} from "../../../../resources/js/functions/random.js";
import validation from "../../../../resources/js/functions/validation.js";

const country_control = document.querySelector("input[type='text']#country");
// Country Input Event
country_control.addEventListener("keyup", (event) => {
  const target = event.currentTarget;
  const parent = target.parentElement;
  const value = target.value;
  if (value) {
    if (!validation.validateCountry(value)) {
      if (!target.nextElementSibling) {
        const error = createElement("div", "error", "Invalid country name");
        parent.appendChild(error);
      }
    } else {
      if (target.nextElementSibling) {
        if (target.nextElementSibling.matches(".error")) {
          parent.removeChild(target.nextElementSibling);
        }
      }
    }
  } else {
    if (target.nextElementSibling) {
      if (target.nextElementSibling.matches(".error")) {
        parent.removeChild(target.nextElementSibling);
      }
    }
  }
});

const aboutMe_textarea = document.querySelector("textarea#aboutme");
// Sign up textarea: On Key up Event
aboutMe_textarea.addEventListener("keyup", (event) => {
  const target = event.currentTarget;
  const parent = target.parentElement;
  const value = target.value;
  if (value) {
    if (!validation.validateTextArea(value)) {
      if (!target.nextElementSibling) {
        const error = createElement(
          "div",
          "error",
          "Please provide a short description"
        );
        parent.appendChild(error);
      }
    } else {
      if (target.nextElementSibling) {
        if (target.nextElementSibling.matches(".error")) {
          parent.removeChild(target.nextElementSibling);
        }
      }
    }
  } else {
    if (target.nextElementSibling) {
      if (target.nextElementSibling.matches(".error")) {
        parent.removeChild(target.nextElementSibling);
      }
    }
  }
});

// Sign up textarea: On Change Event
aboutMe_textarea.addEventListener("change", (event) => {
  const target = event.currentTarget;
  const parent = target.parentElement;
  const value = target.value;
  if (value) {
    if (value.length < 50) {
      if (!target.nextElementSibling) {
        const error = createElement("div", "error", "Too scanty description");
        parent.appendChild(error);
      }
    } else {
      if (target.nextElementSibling) {
        if (target.nextElementSibling.matches(".error")) {
          parent.removeChild(target.nextElementSibling);
        }
      }
    }
  } else {
    if (target.nextElementSibling) {
      if (target.nextElementSibling.matches(".error")) {
        parent.removeChild(target.nextElementSibling);
      }
    }
  }
});

const sign_up_forms = document.querySelectorAll("form.signup");
for (const sign_up_form of sign_up_forms) {
  // On SIGN UP form submit
  sign_up_form.addEventListener("submit", (event) => {
    const target = event.currentTarget;
    let firstElement;
    const textInputs = Array.from(
      target.querySelectorAll("input[type='text']")
    );
    if (target.id === "signup_form") {
      const passwords = Array.from(
        target.querySelectorAll("input[type='password']")
      );
      firstElement = target.querySelector("label[for='first-name']");
    } else firstElement = target.querySelector("div:first-child");
    const error_msgs = target.querySelectorAll("div.error");
    let error, formdata;
    if (isset(passwords)) {
      formdata = {
        textInputs: textInputs,
        passwords: passwords,
      };
    } else {
      formdata = { textInputs: textInputs };
    }
    if (error_msgs.length == 0) {
      if ((errors = validation.validateSignUp(formdata, target.id))) {
        event.preventDefault();
        let i = 1;
        for (const error of error) {
          const error = createElement("div", "error", error);
          target.insertBefore(error, firstElement);
          error.id = `error_${i}`;
          i++;
        }
        setTimeout(() => {
          for (let j = 1; j <= i; j++) {
            const err_msg = target.querySelector(`div.error#error_${j}`);
            target.removeChild(err_msg);
          }
        }, 5000);
        scrollToTop(); // Scroll page up to the top
      }
    } else event.preventDefault();
  });
  // On SIGN UP form change
  sign_up_form.addEventListener("change", (event) => {
    const target = event.currentTarget;
    const error_msgs = target.querySelectorAll("div.error");
    const submitBtn = target.querySelector("button.full-width");
    if (error_msgs.length > 0) {
      submitBtn.setAttribute("disabled", "true");
    } else {
      if (submitBtn.getAttribute("disabled")) {
        submitBtn.removeAttribute("disabled");
      }
    }
  });
}
