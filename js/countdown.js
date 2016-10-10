
    function counterDown() {
      var today = new Date(); //variable contains current date and time

      var days = calcDays(today); //calculate the time left until set date below
      document.countDown.daysLeft.value = Math.floor(days); // displays days rounded to the next lowest integer

      var hours = (days - Math.floor(days)) * 24; //calculate the hours left in the current day
      document.countDown.hrLeft.value = Math.floor(hours); // display hours rounded to the next lowest integer

      var minutes = (hours - Math.floor(hours)) * 60; // calculate the minutes left in the current hour
      document.countDown.minLeft.value = Math.floor(minutes); // display minutes rounded to the next lowest integer

      var seconds = (minutes - Math.floor(minutes)) * 60; //calculate the seconds left in the current minute
      document.countDown.secLeft.value = Math.floor(seconds); // display seconds rounded to the next lowest integer
    }

    function calcDays(currentDate) {
      //create a date object for date of graduation
      //calculate the difference between currentDate and set date
      setDate = new Date("Dec 27, 2016");
      currentTime = currentDate.getFullYear() + 0;
      setDate.setFullYear(currentTime);

      days = (setDate - currentDate) / (1000 * 60 * 60 * 24);
      return days;
    }
    
    setInterval('counterDown()', 1000)   