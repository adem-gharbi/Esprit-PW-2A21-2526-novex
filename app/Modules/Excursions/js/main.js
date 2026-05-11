(function ($) {
    "use strict";

    // Spinner handling
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();

    // Field-specific validation rules and messages
    const fieldRules = {
        // Guide Form Fields
        nom: {
            label: 'Last Name',
            validate: function(value) {
                if (value.trim() === '') return "Last name cannot be empty";
                if (value.length < 2) return "Last name must be at least 2 characters";
                if (value.length > 50) return "Last name cannot exceed 50 characters";
                if (!/^[a-zA-Z\s\-']+$/.test(value)) return "Last name can only contain letters, spaces, hyphens, and apostrophes";
                return null;
            }
        },
        prenom: {
            label: 'First Name',
            validate: function(value) {
                if (value.trim() === '') return "First name cannot be empty";
                if (value.length < 2) return "First name must be at least 2 characters";
                if (value.length > 50) return "First name cannot exceed 50 characters";
                if (!/^[a-zA-Z\s\-']+$/.test(value)) return "First name can only contain letters, spaces, hyphens, and apostrophes";
                return null;
            }
        },
        specialite: {
            label: 'Specialty',
            validate: function(value) {
                if (value.trim() === '') return "Please specify a specialty (e.g., Mountain Climbing, City Tours)";
                if (value.length < 3) return "Specialty must be at least 3 characters";
                if (value.length > 100) return "Specialty cannot exceed 100 characters";
                return null;
            }
        },
        langue: {
            label: 'Language',
            validate: function(value) {
                if (value.trim() === '') return "Please select or enter a language (e.g., English, French, Arabic)";
                if (value.length < 2) return "Language must be at least 2 characters";
                if (value.length > 50) return "Language cannot exceed 50 characters";
                if (!/^[a-zA-Z\s,]+$/.test(value)) return "Language can only contain letters and commas";
                return null;
            }
        },
        tel: {
            label: 'Phone Number',
            validate: function(value) {
                if (value.trim() === '') return "Phone number cannot be empty";
                if (value.length < 8) return "Phone number is too short (minimum 8 characters)";
                if (value.length > 13) return "Phone number cannot exceed 13 characters";
                if (!/^[0-9+\-\s()]+$/.test(value)) return "Phone can only contain digits, +, -, spaces, and parentheses";
                // Check for at least some digits
                if (!/\d/.test(value)) return "Phone must contain at least one digit";
                return null;
            }
        },
        photo: {
            label: 'Photo Filename',
            validate: function(value) {
                if (value.trim() === '') return "Photo filename is required";
                if (!/^[\w\-. ]+\.(jpg|jpeg|png|gif|webp)$/i.test(value)) return "Enter a valid image filename (e.g., guide.jpg, photo.png)";
                if (value.length > 255) return "Filename is too long";
                return null;
            }
        },
        // Excursion Form Fields
        titre: {
            label: 'Title',
            validate: function(value) {
                if (value.trim() === '') return "Excursion title cannot be empty";
                if (value.length < 5) return "Title must be at least 5 characters (e.g., 'Mountain Trek Adventure')";
                if (value.length > 100) return "Title cannot exceed 100 characters";
                return null;
            }
        },
        duree: {
            label: 'Duration',
            validate: function(value) {
                if (value.trim() === '') return "Duration is required (e.g., '3 days', '1 week', '2 hours')";
                if (value.length < 2) return "Please enter a valid duration";
                if (value.length > 50) return "Duration cannot exceed 50 characters";
                return null;
            }
        },
        description: {
            label: 'Description',
            validate: function(value) {
                if (value.trim() === '') return "Please provide a description of the excursion";
                if (value.length < 10) return "Description must be at least 10 characters";
                if (value.length > 1000) return "Description cannot exceed 1000 characters";
                return null;
            }
        },
        prix: {
            label: 'Price',
            validate: function(value) {
                if (value.trim() === '') return "Price is required";
                const priceValue = parseFloat(value);
                if (isNaN(priceValue)) return "Price must be a valid number (e.g., 99.99)";
                if (priceValue < 0) return "Price cannot be negative";
                if (priceValue === 0) return "Price must be greater than 0";
                if (!/^\d+(\.\d{1,2})?$/.test(value.trim())) return "Price can have at most 2 decimal places (e.g., 50.99)";
                return null;
            }
        },
        circuit_id: {
            label: 'Circuit ID',
            validate: function(value) {
                if (value.trim() === '') return "Circuit ID is required";
                const idValue = parseInt(value);
                if (isNaN(idValue)) return "Circuit ID must be a number";
                if (idValue <= 0) return "Circuit ID must be a positive number (e.g., 1, 5, 12)";
                if (!Number.isInteger(idValue)) return "Circuit ID must be a whole number, not a decimal";
                return null;
            }
        },
        guide_id: {
            label: 'Guide ID',
            validate: function(value) {
                if (value.trim() === '') return "Guide ID is required";
                const idValue = parseInt(value);
                if (isNaN(idValue)) return "Guide ID must be a number";
                if (idValue <= 0) return "Guide ID must be a positive number (e.g., 1, 5, 12)";
                if (!Number.isInteger(idValue)) return "Guide ID must be a whole number, not a decimal";
                return null;
            }
        }
    };

    // Helper function to clear error state
    function clearError(fieldId) {
        const input = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        if (input) input.classList.remove('is-invalid-border');
        if (errorDiv) {
            errorDiv.style.display = 'none';
            errorDiv.innerText = '';
        }
    }

    // Helper function to show error with dynamic message
    function showError(fieldId, message) {
        const input = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        if (input) input.classList.add('is-invalid-border');
        if (errorDiv) {
            errorDiv.innerText = message;
            errorDiv.style.display = 'block';
        }
    }

    // Validate a single field and return error message or null
    function validateField(fieldId, value) {
        const rule = fieldRules[fieldId];
        if (!rule) return null;
        return rule.validate(value);
    }

    $(document).ready(function () {
        // ===== GUIDE FORM VALIDATION =====
        const addForm = document.getElementById('addForm');
        const editForm = document.getElementById('editForm');

        // Phone input physical truncation (Max 13) and cleaning
        const telInputs = document.querySelectorAll('input[name="tel"]');
        telInputs.forEach(function(telInput) {
            if (telInput) {
                telInput.addEventListener('input', function() {
                    // Only allow numeric characters
                    this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
                    if (this.value.length > 13) {
                        this.value = this.value.substring(0, 13);
                    }
                });
            }
        });

        // Real-time validation for guide form fields
        const guideFields = ['nom', 'prenom', 'specialite', 'langue', 'tel', 'photo'];
        guideFields.forEach(function(fieldId) {
            const input = document.getElementById(fieldId);
            if (!input) return;

            // Validate on blur (when user leaves the field)
            input.addEventListener('blur', function() {
                const error = validateField(fieldId, this.value);
                if (error) {
                    showError(fieldId, error);
                } else {
                    clearError(fieldId);
                }
            });

            // Real-time feedback on input (while typing)
            input.addEventListener('input', function() {
                // Clear error if field now has content
                if (this.value.trim() !== '') {
                    const error = validateField(fieldId, this.value);
                    if (!error) {
                        clearError(fieldId);
                    } else {
                        showError(fieldId, error);
                    }
                }
            });
        });

        // Validate Guide Form on Submit
        function validateGuideForm(form) {
            let isValid = true;
            guideFields.forEach(function(fieldId) {
                const input = document.getElementById(fieldId);
                if (!input) return;
                const error = validateField(fieldId, input.value);
                if (error) {
                    showError(fieldId, error);
                    isValid = false;
                } else {
                    clearError(fieldId);
                }
            });
            return isValid;
        }

        if (addForm) {
            addForm.addEventListener('submit', function (e) {
                if (!validateGuideForm(this)) {
                    e.preventDefault();
                }
            });
        }

        if (editForm) {
            editForm.addEventListener('submit', function (e) {
                if (!validateGuideForm(this)) {
                    e.preventDefault();
                }
            });
        }

        // ===== EXCURSION FORM VALIDATION =====
        const addExcursionForm = document.getElementById('addExcursionForm');
        const editExcursionForm = document.getElementById('editExcursionForm');

        // Real-time validation for excursion form fields
        const excursionFields = ['titre', 'duree', 'description', 'prix', 'circuit_id', 'guide_id'];
        excursionFields.forEach(function(fieldId) {
            const input = document.getElementById(fieldId);
            if (!input) return;

            // Validate on blur
            input.addEventListener('blur', function() {
                const error = validateField(fieldId, this.value);
                if (error) {
                    showError(fieldId, error);
                } else {
                    clearError(fieldId);
                }
            });

            // Real-time feedback on input
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    const error = validateField(fieldId, this.value);
                    if (!error) {
                        clearError(fieldId);
                    } else {
                        showError(fieldId, error);
                    }
                }
            });
        });

        // Validate Excursion Form on Submit
        function validateExcursionForm(form) {
            let isValid = true;
            excursionFields.forEach(function(fieldId) {
                const input = document.getElementById(fieldId);
                if (!input) return;
                const error = validateField(fieldId, input.value);
                if (error) {
                    showError(fieldId, error);
                    isValid = false;
                } else {
                    clearError(fieldId);
                }
            });
            return isValid;
        }

        if (addExcursionForm) {
            addExcursionForm.addEventListener('submit', function (e) {
                if (!validateExcursionForm(this)) {
                    e.preventDefault();
                }
            });
        }

        if (editExcursionForm) {
            editExcursionForm.addEventListener('submit', function (e) {
                if (!validateExcursionForm(this)) {
                    e.preventDefault();
                }
            });
        }

        // Update copyright year
        var currentYear = new Date().getFullYear();
        var copyrightElements = document.querySelectorAll('.copyright-year');
        copyrightElements.forEach(function(element) {
            element.textContent = currentYear;
        });
    });

})(jQuery);