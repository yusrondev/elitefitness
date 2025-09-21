"use strict";

function fullCalender(eventsData) {
  var calendarEl = document.getElementById("calendar");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    initialDate: new Date().toISOString().split("T")[0], // Set tanggal awal ke hari ini
    selectable: true,
    selectMirror: true,
    editable: true,
    droppable: true,
    events: eventsData, // Load data events dari backend

	windowResize: function () {
		calendar.updateSize();
	},
  });

  calendar.render();
}

jQuery(window).on("load", function () {
  setTimeout(function () {
    // Gunakan URL dari Blade
    fetch(scheduleDataUrl)
      .then((response) => response.json())
      .then((data) => {
        fullCalender(data); // Load data ke FullCalendar
      })
      .catch((error) => console.error("Error fetching schedule data:", error));
  }, 1000);
});