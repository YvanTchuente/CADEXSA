/*
 * Timer Countdown Class
 */

/*
 ******************************************************
 ***** Class Definition                           *****
 ******************************************************
 */

class countdown {
    constructor(element, event_date) {
        this.date = new Date(event_date);
        this.element = element;
    }

    // Public

    start() {
        // Starting the timer
        setInterval(() => {
            this._update();
        }, 1000);
    }

    // Private 

    _diff() {
        // Calculate the difference of time with the current date
        let datediff = new date_diff(this.date);
        return datediff.diff();
    }

    _update() {
        // Update the timer labels
        let day_label = this.element.children[0].children[1];
        let hour_label = this.element.children[1].children[1];
        let minute_label = this.element.children[2].children[1];
        let second_label = this.element.children[3].children[1];

        // Get the time difference
        let time_diff = this._diff();

        if (time_diff) {
            day_label.innerText = time_diff.day;
            hour_label.innerText = time_diff.hour;
            minute_label.innerText = time_diff.minute;
            second_label.innerText = time_diff.second; 
        } else {
            day_label.innerText = 0;
            hour_label.innerText = 0;
            minute_label.innerText = 0;
            second_label.innerText = 0;
        }
    }
}