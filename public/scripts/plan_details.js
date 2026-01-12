document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. KONFIGURACJA SŁOWNIKÓW ---
    // Zaktualizowałem to, żeby pasowało do Twojej bazy (masz tam 'time_distance')
    const typeLabels = {
        'weight_reps': 'Weight & Reps',
        'reps_only': 'Reps Only',
        'time_only': 'Time / Duration',
        'time_distance': 'Cardio (Time & Dist)', // <--- TO PASUJE DO TWOJEJ BAZY
        'distance_time': 'Cardio (Dist & Time)'  // Zostawiam na wszelki wypadek
    };

    const typeIcons = {
        'weight_reps': 'fitness_center',
        'reps_only': 'accessibility_new',
        'time_only': 'timer',
        'time_distance': 'directions_run',
        'distance_time': 'directions_run'
    };

    // --- 2. POBIERANIE I PARSOWANIE DANYCH ---
    let dbExercises = {};
    const dataElement = document.getElementById('exercises-data');

    if (dataElement) {
        try {
            // Używamy .dataset.exercises - przeglądarka automatycznie zamieni &quot; na "
            let rawData = dataElement.dataset.exercises;
            
            console.log("1. Pobrane dane (surowe):", rawData); // SPRAWDŹ KONSOLĘ (F12)

            if (rawData) {
                const exercisesArray = JSON.parse(rawData);
                console.log("2. Sparsowana tablica:", exercisesArray); // SPRAWDŹ KONSOLĘ

                exercisesArray.forEach(exercise => {
                    if (exercise.name) {
                        // Klucz to nazwa małymi literami, Wartość to typ
                        dbExercises[exercise.name.toLowerCase()] = exercise.type;
                    }
                });
                
                console.log("3. Gotowa mapa do szukania:", dbExercises); // SPRAWDŹ KONSOLĘ
            }
        } catch (e) {
            console.error("Błąd krytyczny JS podczas czytania danych:", e);
        }
    } else {
        console.error("Nie znaleziono elementu div#exercises-data!");
    }

    // --- 3. OBSŁUGA INTERFEJSU ---
    const input = document.getElementById('exercise_input');
    const selectContainer = document.getElementById('type_select_container');
    const displayContainer = document.getElementById('existing_type_container');
    const displayLabel = document.getElementById('type_label');
    const displayIcon = document.getElementById('type_icon');

    if (!input) {
        console.warn("Nie znaleziono inputa exercise_input");
        return;
    }

    input.addEventListener('input', function() {
        // Usuwamy białe znaki i zamieniamy na małe litery
        const val = this.value.trim().toLowerCase();
        console.log("Wpisano:", val); // Debugowanie wpisywania

        if (dbExercises.hasOwnProperty(val)) {
            // === ZNALEZIONO ===
            console.log("-> Znaleziono w bazie! Typ:", dbExercises[val]);
            
            const typeKey = dbExercises[val];

            // Ukryj select
            if(selectContainer) selectContainer.style.display = 'none';
            
            // Pokaż badge
            if(displayContainer) {
                displayContainer.style.display = 'flex'; // Użyj flex dla ładnego wyglądu
                displayLabel.textContent = typeLabels[typeKey] || typeKey; // Użyj ładnej nazwy lub klucza
                displayIcon.textContent = typeIcons[typeKey] || 'check_circle';
            }

        } else {
            // === NIE ZNALEZIONO (NOWE) ===
            // console.log("-> Nowe ćwiczenie");

            // Pokaż select
            if(selectContainer) selectContainer.style.display = 'block';
            
            // Ukryj badge
            if(displayContainer) displayContainer.style.display = 'none';
        }
    });
});