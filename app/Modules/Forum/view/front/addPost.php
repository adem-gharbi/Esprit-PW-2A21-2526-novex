<!DOCTYPE html>
<html>
<head>
<title>Ajouter Post</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">
<!-- Quill.js éditeur riche -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
.error{ color:red; font-weight:bold; margin-bottom:10px; }
.schedule-box{
  background:#fff8f0; border:1px solid #e8cfc1;
  border-radius:10px; padding:14px; margin-top:15px;
}
.schedule-box label{ display:block; font-weight:600; margin-bottom:6px; color:#a67b5b; }
.tags-grid{
  display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;
}
.tag-check{
  display:flex; align-items:center; gap:5px;
  background:#f5f5f5; border:1px solid #ddd;
  border-radius:20px; padding:5px 12px; cursor:pointer;
  font-size:13px; user-select:none;
}
.tag-check input{ cursor:pointer; }
.tag-check:hover{ border-color:#a67b5b; }
/* Quill toolbar style */
.ql-container{ border-radius:0 0 8px 8px; min-height:100px; }
.ql-toolbar{ border-radius:8px 8px 0 0; }
</style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../dashboard.php">&larr; Dashboard</a>


<header class="hero">
  <h1>Créer un Post ✍️</h1>
</header>

<div style="text-align:center; margin:20px 0;">
  <a class="btn" href="index.php">⬅ Retour au forum</a>
</div>

<div class="card" style="width:90%; max-width:560px; margin:20px auto; padding:28px;">

  <div id="errorBox" class="error"></div>

  <form name="f"
        id="postForm"
        action="../../controller/PostController.php"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return prepareForm()">

    <!-- Titre -->
    <label style="display:block; margin:12px 0 5px; font-weight:500;">Titre</label>
    <input type="text" name="titre" id="titreInput" required>

    <!-- Contenu via Quill -->
    <label style="display:block; margin:16px 0 5px; font-weight:500;">Contenu</label>
    <div id="quillEditor"></div>
    <!-- Champ caché qui reçoit le HTML de Quill -->
    <input type="hidden" name="contenu" id="contenuHidden">

    <!-- Tags -->
    <label style="display:block; margin:16px 0 5px; font-weight:500;">Tags (optionnel)</label>
    <div class="tags-grid">
      <?php
      require_once "../../controller/PostController.php";
      global $postModel;
      $tags = $postModel->getAllTags();
      foreach($tags as $tag){ ?>
        <label class="tag-check">
          <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>">
          #<?= htmlspecialchars($tag['nom']) ?>
        </label>
      <?php } ?>
    </div>

    <!-- Image -->
    <label style="display:block; margin:16px 0 5px; font-weight:500;">Image (optionnel)</label>
    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
    <img id="imgPreview" src="" alt="" style="display:none;width:100%;border-radius:10px;margin-top:8px;">

    <!-- Publication planifiée -->
    <div class="schedule-box">
      <label>🕐 Planifier la publication</label>
      <input type="datetime-local" id="scheduled_at" name="scheduled_at" onchange="updateSchedulePreview()">
      <small id="schedule-preview" style="display:block;margin-top:5px;color:#888;">Laisser vide = publication immédiate</small>
    </div>

    <!-- Submit -->
    <button class="btn" type="submit" name="addPost" style="width:100%; margin-top:20px; padding:12px;">
      📤 Publier
    </button>

  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
// Init Quill
const quill = new Quill('#quillEditor', {
  theme:'snow',
  placeholder:'Rédigez votre post...',
  modules:{
    toolbar:[
      ['bold','italic','underline'],
      [{'list':'ordered'},{'list':'bullet'}],
      ['link'],
      ['clean']
    ]
  }
});

// Prévisualisation image
function previewImage(input){
  let preview = document.getElementById('imgPreview');
  if(input.files && input.files[0]){
    let reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display='block'; };
    reader.readAsDataURL(input.files[0]);
  }
}

// Preview date planifiée
function updateSchedulePreview(){
  let val = document.getElementById('scheduled_at').value;
  let preview = document.getElementById('schedule-preview');
  if(val){
    let d = new Date(val);
    preview.textContent = '📅 Publication prévue le ' +
      d.toLocaleDateString('fr-FR',{weekday:'long',year:'numeric',month:'long',day:'numeric'}) +
      ' à ' + d.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
    preview.style.color = '#a67b5b';
  } else {
    preview.textContent = 'Laisser vide = publication immédiate';
    preview.style.color = '#888';
  }
}

// Validation + copie contenu Quill
function prepareForm(){
  let titre   = document.getElementById('titreInput').value.trim();
  let contenu = quill.getText().trim();
  let error   = '';

  if(titre == '') error += '❌ Titre obligatoire<br>';
  if(contenu == '') error += '❌ Contenu obligatoire<br>';
  if(titre.split(' ').length > 3) error += '❌ Titre max 3 mots<br>';

  let scheduled = document.getElementById('scheduled_at').value;
  if(scheduled != ''){
    if(new Date(scheduled) <= new Date()) error += '❌ La date doit être dans le futur<br>';
  }

  if(error != ''){
    document.getElementById('errorBox').innerHTML = error;
    return false;
  }

  // Injecter HTML Quill dans le champ caché
  document.getElementById('contenuHidden').value = quill.root.innerHTML;
  return true;
}
</script>
</body>
</html><!DOCTYPE html>
<html>
<head>
<title>Ajouter Post</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">
<!-- Quill.js éditeur riche -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
.error{ color:red; font-weight:bold; margin-bottom:10px; }
.schedule-box{
  background:#fff8f0; border:1px solid #e8cfc1;
  border-radius:10px; padding:14px; margin-top:15px;
}
.schedule-box label{ display:block; font-weight:600; margin-bottom:6px; color:#a67b5b; }
.tags-grid{
  display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;
}
.tag-check{
  display:flex; align-items:center; gap:5px;
  background:#f5f5f5; border:1px solid #ddd;
  border-radius:20px; padding:5px 12px; cursor:pointer;
  font-size:13px; user-select:none;
}
.tag-check input{ cursor:pointer; }
.tag-check:hover{ border-color:#a67b5b; }
/* Quill toolbar style */
.ql-container{ border-radius:0 0 8px 8px; min-height:100px; }
.ql-toolbar{ border-radius:8px 8px 0 0; }
</style>
</head>
<body>

<header class="hero">
  <h1>Créer un Post ✍️</h1>
</header>

<div style="text-align:center; margin:20px 0;">
  <a class="btn" href="index.php">⬅ Retour au forum</a>
</div>

<div class="card" style="width:90%; max-width:560px; margin:20px auto; padding:28px;">

  <div id="errorBox" class="error"></div>

  <form name="f"
        id="postForm"
        action="../../controller/PostController.php"
        method="POST"
        enctype="multipart/form-data"
        onsubmit="return prepareForm()">

    <!-- Titre -->
    <label style="display:block; margin:12px 0 5px; font-weight:500;">Titre</label>
    <input type="text" name="titre" id="titreInput" required>

    <!-- Contenu via Quill -->
    <label style="display:block; margin:16px 0 5px; font-weight:500;">Contenu</label>
    <div id="quillEditor"></div>
    <!-- Champ caché qui reçoit le HTML de Quill -->
    <input type="hidden" name="contenu" id="contenuHidden">

    <!-- Tags -->
    <label style="display:block; margin:16px 0 5px; font-weight:500;">Tags (optionnel)</label>
    <div class="tags-grid">
      <?php
      require_once "../../controller/PostController.php";
      global $postModel;
      $tags = $postModel->getAllTags();
      foreach($tags as $tag){ ?>
        <label class="tag-check">
          <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>">
          #<?= htmlspecialchars($tag['nom']) ?>
        </label>
      <?php } ?>
    </div>

    <!-- Image -->
    <label style="display:block; margin:16px 0 5px; font-weight:500;">Image (optionnel)</label>
    <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
    <img id="imgPreview" src="" alt="" style="display:none;width:100%;border-radius:10px;margin-top:8px;">

    <!-- Publication planifiée -->
    <div class="schedule-box">
      <label>🕐 Planifier la publication</label>
      <input type="datetime-local" id="scheduled_at" name="scheduled_at" onchange="updateSchedulePreview()">
      <small id="schedule-preview" style="display:block;margin-top:5px;color:#888;">Laisser vide = publication immédiate</small>
    </div>

    <!-- Submit -->
    <button class="btn" type="submit" name="addPost" style="width:100%; margin-top:20px; padding:12px;">
      📤 Publier
    </button>

  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
// Init Quill
const quill = new Quill('#quillEditor', {
  theme:'snow',
  placeholder:'Rédigez votre post...',
  modules:{
    toolbar:[
      ['bold','italic','underline'],
      [{'list':'ordered'},{'list':'bullet'}],
      ['link'],
      ['clean']
    ]
  }
});

// Prévisualisation image
function previewImage(input){
  let preview = document.getElementById('imgPreview');
  if(input.files && input.files[0]){
    let reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display='block'; };
    reader.readAsDataURL(input.files[0]);
  }
}

// Preview date planifiée
function updateSchedulePreview(){
  let val = document.getElementById('scheduled_at').value;
  let preview = document.getElementById('schedule-preview');
  if(val){
    let d = new Date(val);
    preview.textContent = '📅 Publication prévue le ' +
      d.toLocaleDateString('fr-FR',{weekday:'long',year:'numeric',month:'long',day:'numeric'}) +
      ' à ' + d.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
    preview.style.color = '#a67b5b';
  } else {
    preview.textContent = 'Laisser vide = publication immédiate';
    preview.style.color = '#888';
  }
}

// Validation + copie contenu Quill
function prepareForm(){
  let titre   = document.getElementById('titreInput').value.trim();
  let contenu = quill.getText().trim();
  let error   = '';

  if(titre == '') error += '❌ Titre obligatoire<br>';
  if(contenu == '') error += '❌ Contenu obligatoire<br>';
  if(titre.split(' ').length > 3) error += '❌ Titre max 3 mots<br>';

  let scheduled = document.getElementById('scheduled_at').value;
  if(scheduled != ''){
    if(new Date(scheduled) <= new Date()) error += '❌ La date doit être dans le futur<br>';
  }

  if(error != ''){
    document.getElementById('errorBox').innerHTML = error;
    return false;
  }

  // Injecter HTML Quill dans le champ caché
  document.getElementById('contenuHidden').value = quill.root.innerHTML;
  return true;
}
</script>
</body>
</html>