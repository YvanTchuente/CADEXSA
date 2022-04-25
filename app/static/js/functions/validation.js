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
if (
  /^\/members\/(register|profile){1}\.(html|php){1}$/.test(
    window.location.pathname
  )
) {
  fetch("/static/database/valid_countries.json")
    .then((response) => response.json())
    .then((json) => {
      valid_countries = json.Countries;
    })
    .catch((reason) => console.log(reason));
}

/**
 * Verifies that the country inputted is an existing country
 * @param {string} country Country name
 * @returns {boolean} Either True or False
 */
function validateCountry(country) {
  let validity = false;
  let escaped = country.replace(/[\\[.+*?(){|^$]/g, "\\$&");
  const regexp = new RegExp("^" + escaped + "$", "i");
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
