import { gotop, element, isset } from "../../../src/js/functions/random.js";
import validation from "../../../src/js/functions/validation.js";

const country_control = document.querySelector("input[type='text']#country");
// Country Input Event
country_control.addEventListener("keyup", (event) => {
  const target = event.currentTarget;
  const parent = target.parentElement;
  const value = target.value;
  if (value) {
    if (!validation.validateCountry(value)) {
      if (!target.nextElementSibling) {
        const msg = element("div", "error", "Invalid country name");
        parent.appendChild(msg);
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
        const msg = element(
          "div",
          "error",
          "Please provide a short description"
        );
        parent.appendChild(msg);
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
        const msg = element("div", "error", "Too scanty description");
        parent.appendChild(msg);
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

const sign_up_forms = document.querySelectorAll("form.sign_up");
for (const sign_up_form of sign_up_forms) {
  // On SIGN UP form submit
  sign_up_form.addEventListener("submit", (event) => {
    const target = event.currentTarget;
    let firstElement;
    const textInputs = Array.from(
      target.querySelectorAll("input[type='text']")
    );
    if (target.id === "register-form") {
      const passwords = Array.from(
        target.querySelectorAll("input[type='password']")
      );
      firstElement = target.querySelector("label[for='first-name']");
    } else firstElement = target.querySelector("div:first-child");
    const error_msgs = target.querySelectorAll("div.error");
    let error_msg, formdata;
    if (isset(passwords)) {
      formdata = {
        textInputs: textInputs,
        passwords: passwords,
      };
    } else {
      formdata = { textInputs: textInputs };
    }
    if (error_msgs.length == 0) {
      if ((error_msg = validation.validateSignUp(formdata, target.id))) {
        event.preventDefault();
        let i = 1;
        for (const msg of error_msg) {
          const error = element("div", "error", msg);
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
        gotop(); // Scroll page up to the top
      }
    } else event.preventDefault();
  });
  // On SIGN UP form change
  sign_up_form.addEventListener("change", (event) => {
    const target = event.currentTarget;
    const error_msgs = target.querySelectorAll("div.error");
    const submitBtn = target.querySelector("button.form-btn");
    if (error_msgs.length > 0) {
      submitBtn.setAttribute("disabled", "true");
    } else {
      if (submitBtn.getAttribute("disabled")) {
        submitBtn.removeAttribute("disabled");
      }
    }
  });
}
