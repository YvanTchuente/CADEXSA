/*
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

class carousel 
{
    constructor(element) {
        this.element = element;
        this.items = element.children; // Carousel items
        this.length = element.children.length;
        this.index = 0; // Internal Pointer
        this.ACTIVE_CLASSNAME = "active";
        this.special_carousels = ['head-carousel']; // special carousels
    }

    // Public methods

    start() {
        this._slide(NEXT);
        setInterval(() => {
            this._slide(NEXT);
        }, 15000);
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
        if(this.index > (this.length - 1)) {
            this.index = 0; 
        }

        let index, activeElement;
        let active = this.ACTIVE_CLASSNAME;
        if(this.special_carousels.includes(this.element.id)) {
            this.ACTIVE_CLASSNAME = 'f_active';
            active = this.ACTIVE_CLASSNAME;
        }

        switch (element)
        {
            case "next":
                this.index = this.index + 1; // Moves the pointer by 1
                index = this.index - 1; // Get the pointer
                activeElement = this.items[index];

                for (const item of this.items) {
                    item.classList.remove(active);
                }

                activeElement.classList.add(active); // Display the item
                this.index = this.index - 1;
                // Moves the pointer to the next element
                this._movePointer(NEXT);
                break;
            case "prev":
                this.index = this.index - 1; // Moves the pointer by 1
                index = this.index + 1; // Get the pointer
                activeElement = this.items[index];

                for (const item of this.items) {
                    item.classList.remove(active);
                }

                activeElement.classList.add(active); // Display the item
                this.index = this.index + 1;
                // Moves the pointer to the next element
                this._movePointer(NEXT);
                break;
        }
    }
}