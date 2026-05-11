<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Guide - Voyagio Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<link rel="stylesheet" href="../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../back.php">&larr; Back Dashboard</a>

  <div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon">VO</div>
        <div>
          <div class="brand-title">Voyagio</div>
          <div class="brand-subtitle">Back Office</div>
        </div>
      </div>
      <nav class="menu">
        <a href="admin.php" class="menu-item active">Guides</a>
        <a href="admin.php?page=excursions" class="menu-item">Excursions</a>
        <a href="index.php" class="menu-item">View Website</a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
      <!-- TOPBAR -->
      <header class="topbar">
        <div>
          <div class="page-title">Add Guide</div>
          <div class="page-subtitle">Create a new guide profile</div>
        </div>
        <div class="user-card">
          <span>Admin</span>
          <div class="avatar">AD</div>
        </div>
      </header>

      <!-- PANEL CARD -->
      <section class="panel-card">
        <div class="panel-header">
          <div>
            <h2>New Guide</h2>
            <p>Fill in all required information</p>
          </div>
        </div>

        <!-- FORM -->
        <form class="panel-form" id="addForm" method="POST" action="admin.php?action=add">
          <label>
            First Name
            <input type="text" name="prenom" id="prenom" value="">
            <small class="error-message" id="prenom-error" style="display: none;"></small>
          </label>

          <label>
            Last Name
            <input type="text" name="nom" id="nom" value="">
            <small class="error-message" id="nom-error" style="display: none;"></small>
          </label>

          <label>
            Specialty
            <input type="text" name="specialite" id="specialite" value="">
            <small class="error-message" id="specialite-error" style="display: none;"></small>
          </label>

          <label>
            Language
            <input type="text" name="langue" id="langue" value="">
            <small class="error-message" id="langue-error" style="display: none;"></small>
          </label>

          <label>
            Phone
            <input type="text" name="tel" id="tel" value="">
            <small class="error-message" id="tel-error" style="display: none;"></small>
          </label>

          <label>
            Photo Filename
            <input type="text" name="photo" id="photo" value="">
            <small class="error-message" id="photo-error" style="display: none;"></small>
          </label>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Guide</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('addForm');
      const fields = {
        prenom: { required: true, minLength: 2 },
        nom: { required: true, minLength: 2 },
        specialite: { required: true },
        langue: { required: true },
        tel: { required: true, pattern: /^(\+216|216)?[0-9]{8}$/, message: 'Must be a valid Tunisian phone number (e.g., +21612345678 or 21612345678)' },
        photo: { required: true, pattern: /\.(jpg|jpeg|png|gif)$/i, message: 'Must be a valid image file (jpg, jpeg, png, gif)' }
      };

      function validateField(fieldName, value) {
        const config = fields[fieldName];
        const errorElement = document.getElementById(fieldName + '-error');
        
        if (config.required && !value.trim()) {
          errorElement.textContent = 'This field is required';
          errorElement.style.display = 'block';
          return false;
        }
        
        if (config.minLength && value.trim().length < config.minLength) {
          errorElement.textContent = `Minimum ${config.minLength} characters required`;
          errorElement.style.display = 'block';
          return false;
        }
        
        if (config.pattern && !config.pattern.test(value)) {
          errorElement.textContent = config.message || 'Invalid format';
          errorElement.style.display = 'block';
          return false;
        }
        
        errorElement.style.display = 'none';
        return true;
      }

      function validateForm() {
        let isValid = true;
        Object.keys(fields).forEach(fieldName => {
          const input = document.getElementById(fieldName);
          if (!validateField(fieldName, input.value)) {
            isValid = false;
          }
        });
        return isValid;
      }

      // Add event listeners for real-time validation
      Object.keys(fields).forEach(fieldName => {
        const input = document.getElementById(fieldName);
        input.addEventListener('blur', function() {
          validateField(fieldName, this.value);
        });
        input.addEventListener('input', function() {
          if (this.value.trim()) {
            validateField(fieldName, this.value);
          }
        });
      });

      // Prevent form submission if invalid
      form.addEventListener('submit', function(e) {
        if (!validateForm()) {
          e.preventDefault();
          alert('Please correct the errors before submitting.');
        }
      });
    });
  </script>
</body>
</html>