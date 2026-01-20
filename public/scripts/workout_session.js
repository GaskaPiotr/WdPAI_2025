document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Obsługa czyszczenia URL po wyświetleniu toasta
    // Jeśli w adresie jest ?saved=1, usuwamy to, żeby po odświeżeniu komunikat zniknął
    if (window.location.search.includes('saved=1')) {
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('saved');
            window.history.replaceState(null, '', url);
        }
    }

});

// 2. Funkcja usuwania sesji (musi być globalna, bo wywołujemy ją przez onclick w HTML)
function prepareDelete(buttonElement) {
    const sessionId = buttonElement.getAttribute('data-id');
    
    if (confirm('Delete this workout session permanently?')) {
        // Ustaw ID w ukrytym formularzu
        document.getElementById('delete-session-id-input').value = sessionId;
        
        // Wyślij formularz
        document.getElementById('delete-session-form').submit();
    }
}