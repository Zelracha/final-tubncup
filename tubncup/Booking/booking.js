if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

function showPopup() {
    document.getElementById("popup-container").classList.add("active");
}

function closePopup() {
    document.getElementById("popup-container").classList.remove("active");
}

window.onload = function () {
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("reserveDate").setAttribute("min", today);

    // Check if the form was submitted successfully
    const isSuccess = document.getElementById("isSuccess")?.value === 'true'; // Check if the success element exists
    if (isSuccess) {
        showPopup(); // Show the success pop-up
    }
};

function adjustTimeOptions() {
    const dateInput = document.getElementById("reserveDate").value;
    const timeSelect = document.getElementById("reserveTime");

    // Reset the time dropdown
    timeSelect.innerHTML = '<option value="">Select a time</option>';

    if (dateInput) {
        // Fetch unavailable time slots from the server
        fetch(`fetch_time_slots.php?reserveDate=${dateInput}`)
            .then(response => response.json())
            .then(data => {
                // Define time slots for weekdays and weekends
                const weekdayTimes = [
                    "9:00 AM", "9:30 AM", "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM",
                    "1:00 PM", "1:30 PM", "2:30 PM", "3:00 PM", "4:00 PM", "4:30 PM",
                    "5:30 PM", "6:00 PM"
                ];

                const weekendTimes = [
                    "9:00 AM", "9:30 AM", "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM",
                    "1:00 PM", "1:30 PM", "2:30 PM", "3:00 PM", "4:00 PM", "4:30 PM",
                    "5:30 PM", "6:00 PM", "6:30 PM", "7:00 PM"
                ];

                const selectedDate = new Date(dateInput);
                const dayOfWeek = selectedDate.getUTCDay();

                // Determine available time slots based on the day of the week
                const defaultTimes = (dayOfWeek === 0 || dayOfWeek === 6) ? weekendTimes : weekdayTimes;

                // Filter out time slots that are fully booked (2 bookings already exist)
                const availableTimes = defaultTimes.filter(time => !data.bookedSlots.includes(time));

                // Populate the dropdown with available time slots
                availableTimes.forEach(time => {
                    const option = document.createElement("option");
                    option.value = time;
                    option.textContent = time;
                    timeSelect.appendChild(option);
                });

                // If no time slots are available, show a message
                if (availableTimes.length === 0) {
                    const noOptions = document.createElement("option");
                    noOptions.value = "";
                    noOptions.textContent = "No available times for this date.";
                    timeSelect.appendChild(noOptions);
                }
            })
            .catch(error => console.error("Error fetching time slots:", error));
    }
}