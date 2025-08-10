document.addEventListener("DOMContentLoaded", function () {
    const calendar = document.getElementById("calendar");
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    const headDay = document.querySelector(".head-day");
    const headMonth = document.querySelector(".head-month");

    const prevButton = document.querySelector(".pre-button");
    const nextButton = document.querySelector(".next-button");
    const resetButton = document.getElementById("reset");

    let today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    let selectedDate = formatDate(today);

    // Render awal kalender
    renderCalendar(currentMonth, currentYear);

    // Event navigasi bulan
    prevButton.addEventListener("click", function () {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentMonth, currentYear);
    });

    nextButton.addEventListener("click", function () {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    });

    resetButton.addEventListener("click", function () {
        today = new Date();
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        selectedDate = formatDate(today);
        renderCalendar(currentMonth, currentYear);
    });

    // Event klik tanggal (delegation)
    calendar.addEventListener("click", function (e) {if (e.target.classList.contains("calendar-day") && e.target.dataset.date) {
        selectedDate = e.target.dataset.date;
        
        // Ubah header agar sesuai dengan tanggal yang diklik
        const parts = selectedDate.split("-");
        headDay.textContent = parseInt(parts[2], 10); // tanggal
        headMonth.textContent = `${monthNames[parseInt(parts[1], 10) - 1].toUpperCase()} - ${parts[0]}`;

        highlightSelectedDate();
        renderPelatihan(selectedDate);
    }
    });

    function renderCalendar(month, year) { // Ambil tanggal dari selectedDate kalau ada, kalau belum ada pakai tanggal hari ini
    let displayDate = today;
    if (selectedDate) {
        const parts = selectedDate.split("-");
        displayDate = new Date(parts[0], parts[1] - 1, parts[2]);
    }

    // Update header sesuai tanggal yang sedang aktif
    headDay.textContent = displayDate.getDate();
    headMonth.textContent = `${monthNames[month].toUpperCase()} - ${year}`;

    // Reset semua cell kalender
    const cells = calendar.querySelectorAll("tbody td");
    cells.forEach(cell => {
        cell.textContent = "";
        cell.dataset.date = "";
        cell.classList.remove("selected");
        });

        // Perhitungan hari
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let date = 1;
        for (let i = firstDay; i < daysInMonth + firstDay; i++) {
            const cell = cells[i];
            if (!cell) continue;
            const thisDate = new Date(year, month, date);
            const formatted = formatDate(thisDate);

            cell.textContent = date;
            cell.dataset.date = formatted;

            // Tandai hari ini
            if (formatted === formatDate(new Date())) {
                cell.classList.add("today");
            }

            // Tandai jika yang dipilih
            if (formatted === selectedDate) {
                cell.classList.add("selected");
            }

            date++;
        }
    }

    function highlightSelectedDate() {
        const cells = calendar.querySelectorAll(".calendar-day");
        cells.forEach(cell => {
            cell.classList.remove("selected");
            if (cell.dataset.date === selectedDate) {
                cell.classList.add("selected");
            }
        });
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = ("0" + (date.getMonth() + 1)).slice(-2);
        const day = ("0" + date.getDate()).slice(-2);
        return `${year}-${month}-${day}`;
    }
});
