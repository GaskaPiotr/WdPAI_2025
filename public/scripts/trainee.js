document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');
    const workoutItems = document.querySelectorAll('.workout-wrapper');

    function filterWorkouts() {
        // 1. Pobieramy wartości (małymi literami dla porównania)
        const searchTerm = searchInput.value.toLowerCase();

        const filterType = filterSelect ? filterSelect.value : 'all';

        let visibleCount = 0;

        // 2. Pętla po wszystkich treningach
        workoutItems.forEach(item => {
            const itemName = item.getAttribute('data-name');
            const itemSource = item.getAttribute('data-source');

            // Sprawdzamy czy pasuje do WYSZUKIWANIA
            const matchesSearch = itemName.includes(searchTerm);

            // Sprawdzamy czy pasuje do KATEGORII
            const matchesFilter = (filterType === 'all') || (filterType === itemSource);

            // 3. Pokazujemy lub ukrywamy
            if (matchesSearch && matchesFilter) {
                item.style.display = 'flex'; // Przywracamy widoczność (flex bo tak mamy w CSS)
                visibleCount++;
            } else {
                item.style.display = 'none'; // Ukrywamy
            }
        });

        // Opcjonalnie: Obsługa sytuacji "Brak wyników"
        handleEmptyState(visibleCount);
    }

    function handleEmptyState(count) {
        let emptyMsg = document.getElementById('no-results-message');
        
        // Jeśli nie ma wyników i nie ma komunikatu -> stwórz go
        if (count === 0 && !emptyMsg) {
            emptyMsg = document.createElement('div');
            emptyMsg.id = 'no-results-message';
            emptyMsg.style.textAlign = 'center';
            emptyMsg.style.padding = '2rem';
            emptyMsg.style.color = '#888';
            emptyMsg.innerHTML = '<span class="material-symbols-outlined" style="font-size: 3rem; margin-bottom: 0.5rem">search_off</span><p>No workouts found matching filters.</p>';
            document.querySelector('.workout-list').appendChild(emptyMsg);
        } 
        // Jeśli są wyniki, a komunikat wisi -> usuń go
        else if (count > 0 && emptyMsg) {
            emptyMsg.remove();
        }
    }

    // Nasłuchujemy zmian
    if(searchInput) {
        searchInput.addEventListener('input', filterWorkouts);
    }

    // Select istnieje tylko u trainee, więc sprawdzamy przed dodaniem listenera
    if(filterSelect) {
        filterSelect.addEventListener('change', filterWorkouts);
    }
});

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