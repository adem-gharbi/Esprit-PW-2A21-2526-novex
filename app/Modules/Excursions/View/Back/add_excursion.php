<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Excursion - Voyagio Admin</title>
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
        <a href="admin.php" class="menu-item">Guides</a>
        <a href="admin.php?page=excursions" class="menu-item active">Excursions</a>
        <a href="index.php" class="menu-item">View Website</a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
      <!-- TOPBAR -->
      <header class="topbar">
        <div>
          <div class="page-title">Add Excursion</div>
          <div class="page-subtitle">Create a new excursion</div>
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
            <h2>New Excursion</h2>
            <p>Fill in all required information</p>
          </div>
        </div>

        <!-- FORM -->
        <form class="panel-form" id="addExcursionForm" method="POST" action="admin.php?page=excursions&action=add">
          <label>
            Title
            <input type="text" name="titre" id="titre">
            <small class="error-message" id="titre-error" style="display: none;"></small>
          </label>

          <label>
            Duration
            <input type="text" name="duree" id="duree" placeholder="e.g., 3 days">
            <small class="error-message" id="duree-error" style="display: none;"></small>
          </label>

          <label>
            Description
            <textarea name="description" id="description"></textarea>
            <small class="error-message" id="description-error" style="display: none;"></small>
          </label>

          <label>
            Price (TND)
            <input type="text" name="prix" id="prix">
            <small class="error-message" id="prix-error" style="display: none;"></small>
          </label>

          <label>
            Circuit ID
            <input type="text" name="circuit_id" id="circuit_id">
            <small class="error-message" id="circuit_id-error" style="display: none;"></small>
          </label>

          <label>
            Guide ID
            <input type="text" name="guide_id" id="guide_id">
            <small class="error-message" id="guide_id-error" style="display: none;"></small>
          </label>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Excursion</button>
            <a href="admin.php?page=excursions" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('addExcursionForm');
      const fields = {
        titre: { required: true, minLength: 3 },
        duree: { required: true },
        description: { required: true, minLength: 10 },
        prix: { required: true, pattern: /^\d+(\.\d{1,2})?$/, message: 'Must be a valid price (e.g., 100 or 100.50)' },
        circuit_id: { required: true, pattern: /^\d+$/, message: 'Must be a number' },
        guide_id: { required: true, pattern: /^\d+$/, message: 'Must be a number' }
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
