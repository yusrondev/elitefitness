// "use strict";

// function fullCalender(events) {
    /* initialize the external events
        -----------------------------------------------------------------*/

    /* initialize the calendar
    -----------------------------------------------------------------*/

//     var calendarEl = document.getElementById('calendar');
//     var calendar = new FullCalendar.Calendar(calendarEl, {
//         headerToolbar: {
//             left: 'title,prev,next',
//             right: 'today',
//             center: 'dayGridMonth,timeGridWeek,timeGridDay'
//         },
        
//         selectable: true,
//         selectMirror: true,
//         select: function(arg) {
//             var title = prompt('Event Title:');
//             if (title) {
//                 calendar.addEvent({
//                     title: title,
//                     start: arg.start,
//                     end: arg.end,
//                     allDay: arg.allDay
//                 });
//             }
//             calendar.unselect();
//         },
        
//         editable: true,
//         droppable: true, // this allows things to be dropped onto the calendar
//         drop: function(arg) {
//             // is the "remove after drop" checkbox checked?
//             if (document.getElementById('drop-remove').checked) {
//                 // if so, remove the element from the "Draggable Events" list
//                 arg.draggedEl.parentNode.removeChild(arg.draggedEl);
//             }
//         },

//         initialDate: new Date(), // Menampilkan tahun sekarang
//         weekNumbers: true,
//         navLinks: true, // can click day/week names to navigate views
//         editable: true,
//         selectable: true,
//         nowIndicator: true,
//         events: events, // Menambahkan data events yang diterima dari controller
//         eventRender: function(info) {
//             // Untuk menampilkan nama member dan trainer dalam event
//             info.el.querySelector('.fc-title').innerHTML = info.event.title;
//         }
//     });

//     calendar.render();
// }