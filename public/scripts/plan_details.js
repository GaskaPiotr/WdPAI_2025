document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Pobieramy elementy DOM
    const exerciseInput = document.getElementById('exercise_input');
    const typeSelectContainer = document.getElementById('type_select_container');
    const existingTypeContainer = document.getElementById('existing_type_container');
    const typeSelect = document.getElementById('type_select');
    
    // Elementy wewnątrz Badge'a (wykryty typ)
    const typeLabel = document.getElementById('type_label');
    const typeIcon = document.getElementById('type_icon');

    // 2. Pobieramy dane o ćwiczeniach z ukrytego diva (dataset)
    const dataContainer = document.getElementById('exercises-data');
    if (!dataContainer) return; // Zabezpieczenie
    
    // Parsujemy JSON z PHP
    const allExercises = JSON.parse(dataContainer.dataset.exercises || '[]');

    // Mapa ikon dla typów (żeby JS wiedział jaką ikonkę wstawić)
    const typeIcons = {
        'weight_reps': 'fitness_center',
        'reps_only': 'accessibility_new',
        'time_only': 'timer',
        'distance_time': 'directions_run',
        'time_distance': 'directions_run'
    };

    // Mapa czytelnych nazw dla typów
    const typeLabels = {
        'weight_reps': 'Weight & Reps',
        'reps_only': 'Reps Only',
        'time_only': 'Time based',
        'distance_time': 'Distance & Time',
        'time_distance': 'Distance & Time'
    };

    // 3. Nasłuchujemy wpisywania w input
    exerciseInput.addEventListener('input', function () {
        const inputValue = this.value.trim().toLowerCase();
        
        // Szukamy czy wpisana nazwa istnieje w bazie (case-insensitive)
        const match = allExercises.find(ex => ex.name.toLowerCase() === inputValue);

        if (match) {
            // --- SCENARIUSZ: ĆWICZENIE ISTNIEJE (POKAŻ BADGE) ---
            
            // 1. Ukryj selecta, Pokaż badge (operując na klasach .hidden)
            typeSelectContainer.classList.add('hidden');
            existingTypeContainer.classList.remove('hidden');

            // 2. Zaktualizuj tekst i ikonę w badge'u
            const type = match.type;
            typeLabel.textContent = typeLabels[type] || type;
            typeIcon.textContent = typeIcons[type] || 'help';

            // 3. WAŻNE: Ustaw wartość w ukrytym selectcie, żeby formularz wysłał poprawny typ!
            typeSelect.value = type;

        } else {
            // --- SCENARIUSZ: NOWE ĆWICZENIE (POKAŻ SELECT) ---
            
            // 1. Pokaż selecta, Ukryj badge
            typeSelectContainer.classList.remove('hidden');
            existingTypeContainer.classList.add('hidden');
            
            // Opcjonalnie: można zresetować select do domyślnej wartości, jeśli chcesz
            // typeSelect.value = 'weight_reps'; 
        }
    });
});