/*
 * Time countdown class
 */

export default class Countdown {
  /**
   * @param {HTMLElement} countdown
   * @param {string} targetDate
   */
  constructor(countdown, targetDate) {
    this.countdown = countdown;
    this.targetDate = new Date(targetDate);
    this._setupCountdown();
  }

  // Public

  /**
   * Starts or resumes the coundown.
   */
  start() {
    let previousInterval;
    this.intervalId = setInterval(() => {
      const currentDate = new Date();
      const interval = Math.ceil((this.targetDate - currentDate) / 1000); // In seconds
      this._flipCards(interval);
      previousInterval = interval;
    }, 250);
  }

  /**
   * Stops the countdown.
   */
  stop() {
    clearInterval(this.intervalId);
  }

  // Private

  _setupCountdown() {
    const currentDate = new Date();
    const interval = Math.ceil((this.targetDate - currentDate) / 1000); // In seconds

    if (interval > 0) {
      const seconds = interval % 60;
      const minutes = Math.floor(interval / 60) % 60;
      const hours = Math.floor(interval / 3600) % 24;
      const days = Math.floor(interval / 86400);

      Array.from(
        this.countdown.querySelector("[data-days-tens]").children
      ).forEach((child) => {
        child.textContent = Math.floor(days / 10);
      });
      Array.from(
        this.countdown.querySelector("[data-days-unit]").children
      ).forEach((child) => {
        child.textContent = days % 10;
      });
      Array.from(
        this.countdown.querySelector("[data-hours-tens]").children
      ).forEach((child) => {
        child.textContent = Math.floor(hours / 10);
      });
      Array.from(
        this.countdown.querySelector("[data-hours-unit]").children
      ).forEach((child) => {
        child.textContent = hours % 10;
      });
      Array.from(
        this.countdown.querySelector("[data-minutes-tens]").children
      ).forEach((child) => {
        child.textContent = Math.floor(minutes / 10);
      });
      Array.from(
        this.countdown.querySelector("[data-minutes-unit]").children
      ).forEach((child) => {
        child.textContent = minutes % 10;
      });
      Array.from(
        this.countdown.querySelector("[data-seconds-tens]").children
      ).forEach((child) => {
        child.textContent = Math.floor(seconds / 10);
      });
      Array.from(
        this.countdown.querySelector("[data-seconds-unit]").children
      ).forEach((child) => {
        child.textContent = seconds % 10;
      });
    } else {
      Array.from(
        this.countdown.querySelector("[data-days-tens]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-days-unit]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-hours-tens]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-hours-unit]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-minutes-tens]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-minutes-unit]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-seconds-tens]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
      Array.from(
        this.countdown.querySelector("[data-seconds-unit]").children
      ).forEach((child) => {
        child.textContent = 0;
      });
    }
  }

  /**
   * Flips all the cards of the countdown.
   *
   * @param {number} interval In seconds
   */
  _flipCards(interval) {
    if (interval <= 0) {
      this.stop();
      return;
    }

    const seconds = interval % 60;
    const second_unit = seconds % 10;
    const second_tens = Math.floor(seconds / 10);

    const minutes = Math.floor(interval / 60) % 60;
    const minute_unit = minutes % 10;
    const minute_tens = Math.floor(minutes / 10);

    const hours = Math.floor(interval / 3600) % 24;
    const hour_unit = hours % 10;
    const hour_tens = Math.floor(hours / 10);

    const days = Math.floor(interval / 86400);
    const day_unit = days % 10;
    const day_tens = Math.floor(days / 10);

    this._flip(this.countdown.querySelector("[data-days-tens]"), day_tens);
    this._flip(this.countdown.querySelector("[data-days-unit]"), day_unit);
    this._flip(this.countdown.querySelector("[data-hours-tens]"), hour_tens);
    this._flip(this.countdown.querySelector("[data-hours-unit]"), hour_unit);
    this._flip(
      this.countdown.querySelector("[data-minutes-tens]"),
      minute_tens
    );
    this._flip(
      this.countdown.querySelector("[data-minutes-unit]"),
      minute_unit
    );
    this._flip(
      this.countdown.querySelector("[data-seconds-tens]"),
      second_tens
    );
    this._flip(
      this.countdown.querySelector("[data-seconds-unit]"),
      second_unit
    );
  }

  /**
   * Flips a card.
   *
   * @param {HTMLElement} flipCard
   * @param {number} newNumber
   */
  _flip(flipCard, newNumber) {
    const topHalf = flipCard.querySelector(".topHalf");
    const currentNumber = parseInt(topHalf.textContent);
    if (newNumber === currentNumber) {
      return;
    }

    const bottomHalf = flipCard.querySelector(".bottomHalf");
    const topFlip = document.createElement("div");
    topFlip.classList.add("top-flip");
    const bottomFlip = document.createElement("div");
    bottomFlip.classList.add("bottom-flip");

    topHalf.textContent = currentNumber;
    bottomHalf.textContent = currentNumber;
    topFlip.textContent = currentNumber;
    bottomFlip.textContent = newNumber;

    topFlip.addEventListener("animationstart", (e) => {
      topHalf.textContent = newNumber;
    });
    topFlip.addEventListener("animationend", (e) => {
      topFlip.remove();
    });
    bottomFlip.addEventListener("animationend", (e) => {
      bottomHalf.textContent = newNumber;
      bottomFlip.remove();
    });

    flipCard.append(topFlip, bottomFlip);
  }
}
