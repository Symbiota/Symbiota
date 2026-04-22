radioManagement = document.querySelectorAll('input[name="managementType"]')
radioGUID = document.querySelectorAll('input[name="guidTarget"]')


radioManagement.forEach(radioManagement => {
    radioManagement.addEventListener('change', () => {
        if (radioManagement.checked)
            managementTypeChanged(radioManagement.form)
    });
});

radioGUID.forEach(radioGUID => {
    radioGUID.addEventListener('change', () => {
        if (radioGUID.checked)  {
            checkManagementTypeGuidSource(radioGUID.form)
        }
    });
});

function toggleFossilWarning() {
	const select = document.getElementById('collType');
	const warning = document.getElementById('fossilWarning');
	if (select && warning)
		warning.style.display = select.value === 'Fossil Specimens' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', toggleFossilWarning);