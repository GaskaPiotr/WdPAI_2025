document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('traineeSearchInput');
    const tableRows = document.querySelectorAll('.trainee-row');
    const noResultsRow = document.getElementById('noResultsRow');

    // Sprawdzamy czy input istnieje (żeby nie było błędów na innych stronach)
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                // Pobieramy tekst z atrybutu data-search (imię nazwisko email)
                const searchData = row.getAttribute('data-search');

                if (searchData.includes(searchTerm)) {
                    row.style.display = ''; // Pokazujemy wiersz
                    visibleCount++;
                } else {
                    row.style.display = 'none'; // Ukrywamy wiersz
                }
            });

            // Obsługa komunikatu "Brak wyników"
            // Pokazujemy go tylko jeśli mamy jakieś wiersze w ogóle (tableRows.length > 0)
            // ale żaden nie pasuje do wyszukiwania (visibleCount === 0)
            if (noResultsRow) {
                if (visibleCount === 0 && tableRows.length > 0) {
                    noResultsRow.style.display = '';
                } else {
                    noResultsRow.style.display = 'none';
                }
            }
        });
    }
});