import { start_counters } from "../../resources/js/functions/random.js";

const informative_numbers = document.getElementById("informative_numbers");
window.addEventListener("scroll", () => {
  if (window.scrollY >= informative_numbers.offsetTop - 500) {
    start_counters(500);
  }
});
