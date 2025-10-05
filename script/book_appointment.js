function validateForm() {
        const patient = document.forms["apptForm"]["patient_id"].value;
        const schedule = document.forms["apptForm"]["schedule_id"].value;
        const date = document.forms["apptForm"]["appointment_date"].value;

        if (!patient) {
            alert("Please select a patient.");
            return false;
        }
        if (!schedule) {
            alert("Please select a schedule.");
            return false;
        }
        if (!date) {
            alert("Please select an appointment date.");
            return false;
        }
        return true;
    }