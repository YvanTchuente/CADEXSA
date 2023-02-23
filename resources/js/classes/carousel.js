/**
 * Carousel class
 */

export default class Carousel {
  /**
   * @param {HTMLElement} carousel
   */
  constructor(carousel) {
    this.carousel = carousel;
    this.items = carousel.children; // Carousel items
    this.length = carousel.children.length;
    this.position = 0; // Internal pointer position
    this.duration = 10000; // Slide animation duration time in miliseconds
    this.DIR_PREV = "previous";
    this.DIR_NEXT = "next";
  }

  // Public methods

  start() {
    this.items[0].classList.add("active-item");
  }

  prev() {
    this._slide(this.DIR_PREV);
  }

  next() {
    this._slide(this.DIR_NEXT);
  }

  // Private methods

  _slide(direction) {
    let currentItem, newPosition;
    currentItem = this.items[this.position]; // The previous carousel item
    switch (direction) {
      case this.DIR_NEXT:
        newPosition = this.position + 1;
        if (newPosition > this.length - 1) {
          this.position = 0;
        } else {
          this.position = newPosition;
        }

        const nextItem = this.items[this.position];
        nextItem.classList.add("active-item", "next-button-next-item");
        currentItem.classList.add("next-button-previous-item");

        setTimeout(() => {
          for (const item of this.items) {
            item.classList.remove(
              "active-item",
              "next-button-previous-item",
              "next-button-next-item"
            );
          }
          nextItem.classList.add("active-item");
        }, 500);
        break;

      case this.DIR_PREV:
        newPosition = this.position - 1;
        if (newPosition < 0) {
          this.position = this.length - 1;
        } else {
          this.position = newPosition;
        }

        const previousItem = this.items[this.position];
        previousItem.classList.add("active-item", "previous-button-next-item");
        currentItem.classList.add("previous-button-previous-item");

        setTimeout(() => {
          for (const item of this.items) {
            item.classList.remove(
              "active-item",
              "previous-button-previous-item",
              "previous-button-next-item"
            );
          }
          previousItem.classList.add("active-item");
        }, 500);
        break;
    }
  }
}
