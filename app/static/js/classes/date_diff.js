/*
 * Date Difference Class
 */

// Constant
const toSec = 1000;
const toMin = toSec * 60;
const toHour = toMin * 60;
const toDay = toHour * 24;

/*
 ******************************************************
 ***** Class Definition                           *****
 ******************************************************
 */

class date_diff {
  constructor(date) {
    this.date = date;
  }

  done() {
    if (!this.diff()) {
      return true;
    }
  }

  diff() {
    let currentDate = new Date();
    let diff = this.date.getTime() - currentDate.getTime();

    let time_diff = false;

    if (diff > 0) {
      // Computation of the time difference
      let day = Math.floor(diff / toDay);
      diff = diff - day * toDay; // Remove the number of days from the difference.

      let hour = Math.floor(diff / toHour);
      diff = diff - hour * toHour; // Remove the number of hours from the difference

      let minute = Math.floor(diff / toMin);
      diff = diff - minute * toMin; // Remove the number of minutes from the difference

      let second = Math.floor(diff / toSec);

      // Stores the result in an object and is returned by the method
      time_diff = {
        second: second,
        minute: minute,
        hour: hour,
        day: day,
      };
    }

    return time_diff;
  }
}
