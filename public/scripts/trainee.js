function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown.style.display === 'none') {
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

// Zamknij jak klikniesz poza
window.onclick = function (event) {
    if (!event.target.closest('.notifications-wrapper')) {
        document.getElementById('notificationDropdown').style.display = 'none';
    }
}