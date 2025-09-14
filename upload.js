function handleFormSubmission(event) {
    event.preventDefault();
    
    const itemName = document.getElementById('itemName').value.trim();
    const description = document.getElementById('description').value.trim();
    const hiddenDetails = document.getElementById('hiddenDetails').value.trim();
    const contactNumber = document.getElementById('contactNumber').value.trim();
    const imageFiles = document.getElementById('imageUpload').files;
    
    const alerts = [];

    if (!itemName || !description || !hiddenDetails || !contactNumber) {
        alerts.push('Please fill all fields.');
    }
    
    if (!isValidPhoneNumber(contactNumber)) {
        alerts.push('Please enter a valid contact number.');
    }

    if (imageFiles.length === 0) {
        alerts.push('Please select at least one image of the item.');
    }
    
    if (alerts.length > 0) {
        alert(alerts.join('\n'));
        return;
    }
    
    for (const imageFile of imageFiles) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const imageUrl = event.target.result;
            appendItem(itemName, description, hiddenDetails, contactNumber, imageUrl);
        };
        reader.onerror = function(error) {
            console.error('Error reading the file:', error);
        };
        reader.readAsDataURL(imageFile);
    }
    
    clearFormFields();
}
// Auto-fill description and hidden details fields
document.addEventListener('DOMContentLoaded', function () {
    var descriptionTextarea = document.getElementById('description');
    var hiddenDetailsTextarea = document.getElementById('hiddenDetails');

    var descriptionPlaceholder = "A brief description of the item";
    var hiddenDetailsPlaceholder = "Enter at least two or more hidden descriptions you noticed";

    // Set initial placeholder text
    descriptionTextarea.placeholder = descriptionPlaceholder;
    hiddenDetailsTextarea.placeholder = hiddenDetailsPlaceholder;

    // Event listener for description textarea
    descriptionTextarea.addEventListener('input', function () {
        if (descriptionTextarea.value.length === 0) {
            descriptionTextarea.placeholder = descriptionPlaceholder;
        }
    }

    // Event listener for hidden details textarea
    hiddenDetailsTextarea.addEventListener('input', function () {
        if (hiddenDetailsTextarea.value.length === 0) {
            hiddenDetailsTextarea.placeholder = hiddenDetailsPlaceholder;
        }
    });
});

