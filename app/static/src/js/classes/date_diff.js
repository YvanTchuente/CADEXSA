/*
 * Date Difference Class
 */

export default class date_diff {
  constructor(date) {
    this.date = date;
    this.toSec = 1000;
    this.toMin = this.toSec * 60;
    this.toHour = this.toMin * 60;
    this.toDay = this.toHour * 24;
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
      let day = Math.floor(diff / this.toDay);
      diff = diff - day * this.toDay; // Remove the number of days from the difference.

      let hour = Math.floor(diff / this.toHour);
      diff = diff - hour * this.toHour; // Remove the number of hours from the difference

      let minute = Math.floor(diff / this.toMin);
      diff = diff - minute * this.toMin; // Remove the number of minutes from the difference

      let second = Math.floor(diff / this.toSec);

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
