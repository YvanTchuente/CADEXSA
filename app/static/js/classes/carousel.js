/**
 * Carousel Class
 */

// Constants
const PREV = "prev";
const NEXT = "next";

/*
 ******************************************************
 ***** Class Definition                           *****
 ******************************************************
 */

class carousel {
  constructor(element) {
    this.element = element;
    this.items = element.children; // Carousel items
    this.length = element.children.length;
    this.index = 0; // Internal Pointer
    this.duration = 10000; // Carousel duration time in miliseconds
    this.ACTIVE_CLASSNAME = "active";
    this.special_carousels = ["head-carousel"]; // special carousels
  }

  // Public methods

  start() {
    this._slide(NEXT);
    // setInterval(() => {
    //     this._slide(NEXT);
    // }, this.duration);
  }

  prev() {
    this._slide(PREV);
  }

  next() {
    this._slide(NEXT);
  }

  // Private methods

  _movePointer() {
    this.index = this.index + 1;
  }

  _slide(element) {
    if (this.index > this.length - 1) {
      this.index = 0;
    }

    let index, activeElement, previousElement;
    let active = this.ACTIVE_CLASSNAME;
    if (this.special_carousels.includes(this.element.id)) {
      this.ACTIVE_CLASSNAME = "f_active";
      active = this.ACTIVE_CLASSNAME;
    }

    switch (element) {
      case "next":
        for (const item of this.items) {
          if (
            item.classList.contains("previous") ||
            item.classList.contains("next")
          ) {
            break;
          } else {
            this.index = this.index + 1; // Moves the pointer by 1
            index = this.index - 1; // Get the pointer to the active element
            activeElement = this.items[index];
            // Get the previous element
            if (index > 0) previousElement = this.items[index - 1];
            else if (index <= 0) previousElement = this.items[this.length - 1];

            activeElement.classList.add(active, "next");
            previousElement.classList.add("previous");

            setTimeout(() => {
              for (const item of this.items) {
                item.classList.remove(active, "previous", "next");
              }
              activeElement.classList.add(active);
              this.index = this.index - 1;
              // Moves the pointer to the next element
              this._movePointer();
            }, 500);
            break;
          }
        }
        break;
      case "prev":
        for (const item of this.items) {
          if (
            item.classList.contains("previous") ||
            item.classList.contains("next")
          ) {
            break;
          } else {
            this.index = this.index - 1; // Moves the pointer by 1
            index = this.index + 1; // Get the pointer to active element
            activeElement = this.items[index];
            // Get the previous element
            if (index > 0) previousElement = this.items[index - 1];
            else if (index <= 0) previousElement = this.items[this.length - 1];

            activeElement.classList.add(active, "next-prev");
            previousElement.classList.add("previous-next");

            setTimeout(() => {
              // Removes the class "active" from all items
              for (const item of this.items) {
                item.classList.remove(active, "previous-next", "next-prev");
              }
              activeElement.classList.add(active); // Display the item
              this.index = this.index + 1;
              // Moves the pointer to the next element
              this._movePointer();
            }, 500);
            break;
          }
        }
        break;
    }
  }
}
