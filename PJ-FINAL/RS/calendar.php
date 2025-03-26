<?php
include 'mid_string.php'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/style_calendar.css" />

    <title>ปฏิทินวันเวลานัดหมาย</title>
</head>

<body>
    <div class="container-app">

        <!-- ข้อมูลการรักษา -->
        <div class="head-first">
            <h3>ปฏิทินวันเวลานัดหมาย</h3>
        </div>


        <div class="calendar-container">
            <div class="calendar-header">
                <button id="prevMonth">&#8592;</button>
                <h2 id="currentMonth">January 2025</h2>
                <button id="nextMonth">&#8594;</button>
            </div>

            <div class="calendar-days">
                <div>อา.</div>
                <div>จ.</div>
                <div>อ.</div>
                <div>พ.</div>
                <div>พฤ.</div>
                <div>ศ.</div>
                <div>ส.</div>
            </div>

            <div class="calendar-dates">
                <!-- JavaScript will populate dates here -->
            </div>
        </div>

        <div class="event-list">
            <h3>การนัดหมาย ภายในวัน <span id="selectedDate">กรุณาเลือกวันที่</span></h3>
            <div id="eventsContainer">
                <p>ไม่มีข้อมูลคิวในวันนี้</p>
            </div>
        </div>


    </div>

    <script>
        const calendarDates = document.querySelector('.calendar-dates');
        const currentMonth = document.getElementById('currentMonth');
        const prevMonth = document.getElementById('prevMonth');
        const nextMonth = document.getElementById('nextMonth');
        const eventsContainer = document.getElementById('eventsContainer');
        const selectedDate = document.getElementById('selectedDate');

        const today = new Date();
        let month = today.getMonth();
        let year = today.getFullYear();
        const events = {}; // สำหรับเก็บข้อมูล

        // ฟังก์ชันดึงข้อมูลจาก PHP
        const fetchEvents = async () => {
            try {
                const response = await fetch('get_events.php');
                Object.assign(events, await response.json());
                renderCalendar(); // อัปเดตปฏิทิน
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        };

        const renderCalendar = () => {
            calendarDates.innerHTML = '';
            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();
            currentMonth.textContent = new Date(year, month).toLocaleString('th-TH', {
                month: 'long',
                year: 'numeric',
            });

            // เก็บข้อมูลของปุ่มที่เลือกอยู่
            let selectedButton = null;

            // เพิ่มการป้องกันไม่ให้ย้อนกลับไปเดือนก่อนหน้าถ้าปัจจุบันคือเดือนและปีเดียวกัน
            prevMonth.disabled = (year === today.getFullYear() && month === today.getMonth());

            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                calendarDates.appendChild(emptyCell);
            }

            for (let date = 1; date <= lastDate; date++) {
                const dateContainer = document.createElement('div');
                dateContainer.style.textAlign = 'center';
                dateContainer.style.position = 'relative';

                const dateButton = document.createElement('button');
                dateButton.textContent = date;
                dateButton.style.width = '40px';
                dateButton.style.height = '40px';
                dateButton.style.border = 'none';
                dateButton.style.borderRadius = '50%';
                dateButton.style.backgroundColor = 'transparent';
                dateButton.style.cursor = 'pointer';
                dateButton.style.fontSize = '14px';
                dateButton.style.position = 'relative';

                const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                // เช็ควันที่ก่อนหน้าไม่ให้เลือกได้
                if (new Date(fullDate) < today) {
                    dateButton.disabled = true; // ปิดการใช้งานปุ่มสำหรับวันอดีต
                    dateButton.style.backgroundColor = '#f1f1f1'; // เปลี่ยนสีปุ่ม
                    dateButton.style.cursor = 'not-allowed'; // เปลี่ยนเคอร์เซอร์
                }

                // เพิ่มวงกลมสำหรับวันที่มี event
                if (events[fullDate]) {
                    dateButton.style.backgroundColor = 'rgba(33, 150, 243, 0.2)';
                    dateButton.style.border = '1px solid rgba(33, 150, 243, 0.8)';
                }

                // Highlight today's date
                if (year === today.getFullYear() && month === today.getMonth() && date === today.getDate()) {
                    dateButton.style.backgroundColor = '#6200ea';
                    dateButton.style.color = 'white';
                }

                // Add click event to highlight the selected date
                dateButton.addEventListener('click', () => {
                    if (selectedButton) {
                        selectedButton.classList.remove('selected');
                    }
                    dateButton.classList.add('selected');
                    selectedButton = dateButton;

                    showEvents(date); // แสดงข้อมูลวันที่ที่เลือก
                });

                dateContainer.appendChild(dateButton);

                // Add "โสต นาสิก" under the date if there are events
                if (events[fullDate]) {
                    const treatmentLabel = document.createElement('div');
                    treatmentLabel.textContent = 'โสต นาสิก'; // ข้อความสำหรับทุก event
                    treatmentLabel.classList.add('treatment-label');
                    treatmentLabel.style.fontSize = '12px';
                    treatmentLabel.style.color = '#007bff';
                    treatmentLabel.style.marginTop = '5px';
                    dateContainer.appendChild(treatmentLabel);
                }

                calendarDates.appendChild(dateContainer);
            }
        };

        const showEvents = (date) => {
            const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            selectedDate.textContent = new Date(fullDate).toLocaleDateString('th-TH', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            }); // แสดงวันที่ในรูปแบบภาษาไทย
            eventsContainer.innerHTML = ''; // ล้างรายการ events เก่าออก

            if (events[fullDate]) {
                events[fullDate].forEach(event => {
                    const eventDiv = document.createElement('div');
                    eventDiv.classList.add('event');

                    // เวลานัดหมาย
                    const eventTime = document.createElement('div');
                    eventTime.classList.add('event-time');
                    const startTime = event.time;
                    const endTime = new Date(new Date(`1970-01-01T${startTime}`).getTime() + 20 * 60000) // เพิ่ม 20 นาที
                        .toLocaleTimeString('th-TH', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    eventTime.textContent = `${startTime} - ${endTime} น.`; // แสดงเวลาเริ่มและสิ้นสุด

                    // คิวตรวจที่
                    const queueNumber = document.createElement('div');
                    queueNumber.classList.add('event-queue');
                    queueNumber.textContent = `คิวตรวจที่ ${event.appointment_id}`;

                    // สถานะ
                    const eventStatus = document.createElement('div');
                    eventStatus.classList.add('event-status', event.status === 'ว่าง' ? 'available' : 'busy');
                    eventStatus.textContent = event.status === 'ว่าง' ? 'คิวว่าง' : 'มีคิวตรวจ';

                    // รวมส่วนประกอบใน event
                    eventDiv.appendChild(eventTime);
                    eventDiv.appendChild(queueNumber);
                    eventDiv.appendChild(eventStatus);

                    eventsContainer.appendChild(eventDiv);
                });
            } else {
                eventsContainer.innerHTML = '<p>ไม่มีคิวในวันนี้</p>';
            }
        };

        // เปลี่ยนเดือน
        prevMonth.addEventListener('click', () => {
            if (year === today.getFullYear() && month === today.getMonth()) return; // ป้องกันการย้อนกลับไปเดือนเก่า
            month--;
            if (month < 0) {
                month = 11;
                year--;
            }
            renderCalendar();
        });

        nextMonth.addEventListener('click', () => {
            month++;
            if (month > 11) {
                month = 0;
                year++;
            }
            renderCalendar();
        });

        // เรียกใช้ fetchEvents
        fetchEvents();
    </script>

</body>

</html>