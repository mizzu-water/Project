function validateForm() {
        const name = document.forms["regForm"]["name"].value.trim();
        const age = document.forms["regForm"]["age"].value;
        const contact = document.forms["regForm"]["contact"].value.trim();

        if (!name) {
            alert("Name is required");
            return false;
        }
        if (!age || age <= 0) {
            alert("Valid age is required");
            return false;
        }
        if (!contact) {
            alert("Contact info is required");
            return false;
        }
        return true;
    }