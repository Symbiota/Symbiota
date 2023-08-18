radio = document.querySelectorAll('input[name="imagetype"]')

radio.forEach(radio => {
    radio.addEventListener('change', () => {
        if (radio.checked)
            imageTypeChanged(radio);
    });
});
