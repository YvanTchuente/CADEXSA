/**
 * FUNCTION LIBRARY
 * VALIDATION FUNCTIONS
 */

function validateEmail(email) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
    return true;
  }
  return false;
}

function validatePassword(password) {
  if (/\w{10}/.test(password)) {
    return true;
  }
  return false;
}

function validateText(name, n) {
  let regexp = new RegExp("\\w{" + n + "}");
  if (regexp.test(name)) {
    return true;
  }
  return false;
}

function validateTextArea(text) {
  const words = text.split(" ");
  if (words.length < 50) {
    return true;
  }
  return false;
}

function validatePhone(phone_number) {
  if (/(\(+\d{3}\s\))?\d{9}/.test(phone_number)) {
    return true;
  }
  return false;
}

// Fetching valid countries from the json file
let valid_countries;
fetch("/data/valid_countries.json")
  .then((response) => response.json())
  .then((json) => {
    valid_countries = json.Countries;
  })
  .catch((reason) => console.log(reason));

function validateCountry(country) {
  let escaped = country.replace(/[\\[.+*?(){|^$]/g, "\\$&");
  const regexp = new RegExp("^" + escaped + "$", "i");
  for (let i = 0; i < valid_countries.length; i++) {
    if (valid_countries[i].match(regexp)) {
      return true;
    }
  }
  return false;
}

function validateSignUp(formData, formId) {
  let msg = [];
  const formIds = ["signup_form", "profile-editor"];
  // In case of passwords mismatch
  if (
    formId == formIds[0] &&
    formData.passwords[0].value !== formData.passwords[1].value
  )
    msg.push("Passwords mismatched");
  // In case of invalid username or names
  if (
    !(
      validateText(formData.textInputs[0].value, 4) &&
      validateText(formData.textInputs[1].value, 4) &&
      validateText(formData.textInputs[2].value, 4)
    )
  )
    msg.push("Invalid names or username");
  // In case of invalid city names
  if (!validateText(formData.textInputs[3].value, 5))
    msg.push("Invalid city name");
  return msg.length > 0 ? msg : null;
}

export default {
  validateText,
  validateEmail,
  validatePhone,
  validateSignUp,
  validateCountry,
  validatePassword,
  validateTextArea,
};
