<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../../controller/PostController.php");
global $postModel;

// Filtre par tag
if(isset($_GET['tag'])){
    $posts    = $postModel->getPostsByTag($_GET['tag']);
    $activeTag = $postModel->getTagById($_GET['tag']);
} elseif(isset($_GET['search']) && trim($_GET['search']) != ""){
    $posts    = $postModel->searchPosts($_GET['search']);
    $activeTag = null;
} else {
    $posts    = getPosts();
    $activeTag = null;
}
if(!$posts) $posts = [];

$allTags  = $postModel->getAllTags();
$notifCount = $postModel->countUnreadNotifications(1);
?>
<!DOCTYPE html>
<html>
<head>
<title>Mini Forum</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ===== DARK MODE ===== */
body.dark-mode{
  --beige:#1a1a2e; --nude:#16213e; --brown:#e0a882;
  --dark:#e8e8e8; --gray:#aaa;
  background:#1a1a2e; color:#e8e8e8;
}
body.dark-mode .post, body.dark-mode .card{ background:#16213e; color:#e8e8e8; border:1px solid #333; }
body.dark-mode .comment-box{ background:#0f3460; }
body.dark-mode input, body.dark-mode textarea{ background:#16213e; color:#e8e8e8; border-color:#444; }

/* ===== DARK TOGGLE ===== */
.dark-toggle{
  position:fixed; top:15px; right:15px; z-index:999;
  background:rgba(0,0,0,0.15); border:none; border-radius:50px;
  padding:8px 16px; cursor:pointer; font-size:18px; backdrop-filter:blur(4px);
}

/* ===== NOTIFICATIONS ===== */
.notif-wrapper{ position:fixed; top:15px; right:80px; z-index:998; }
.notif-btn{
  background:rgba(0,0,0,0.15); border:none; border-radius:50px;
  padding:8px 14px; cursor:pointer; font-size:18px; backdrop-filter:blur(4px); position:relative;
}
.notif-badge{
  position:absolute; top:-4px; right:-4px;
  background:#e74c3c; color:#fff; border-radius:50%;
  font-size:10px; width:18px; height:18px; display:flex;
  align-items:center; justify-content:center; font-weight:bold;
}
.notif-panel{
  display:none; position:absolute; top:46px; right:0;
  background:#fff; border:1px solid #ddd; border-radius:14px;
  width:300px; box-shadow:0 8px 24px rgba(0,0,0,0.12); overflow:hidden;
}
body.dark-mode .notif-panel{ background:#16213e; border-color:#333; }
.notif-panel.open{ display:block; }
.notif-header{
  padding:12px 16px; border-bottom:1px solid #eee;
  display:flex; justify-content:space-between; align-items:center;
  font-weight:600; font-size:14px;
}
.notif-item{
  padding:10px 16px; border-bottom:1px solid #f0f0f0;
  font-size:13px; color:#555;
}
body.dark-mode .notif-item{ color:#bbb; border-color:#333; }
.notif-item.unread{ background:#fff8f0; font-weight:500; }
body.dark-mode .notif-item.unread{ background:#1a2a3a; }
.notif-empty{ padding:16px; text-align:center; color:#aaa; font-size:13px; }

/* ===== TAGS BAR ===== */
.tags-bar{
  display:flex; gap:8px; flex-wrap:wrap;
  width:90%; max-width:650px; margin:0 auto 10px;
}
.tag-pill{
  padding:5px 14px; border-radius:20px; font-size:13px;
  border:1px solid #ddd; cursor:pointer; text-decoration:none;
  color:#555; background:#fff; transition:.2s;
}
.tag-pill:hover, .tag-pill.active{ background:var(--brown); color:#fff; border-color:var(--brown); }

/* ===== PINNED BADGE ===== */
.pinned-badge{
  display:inline-block; background:#fff3cd; color:#856404;
  border:1px solid #ffc107; border-radius:20px;
  padding:3px 10px; font-size:12px; font-weight:600; margin-bottom:8px;
}

/* ===== SCHEDULED BADGE ===== */
.scheduled-badge{
  display:inline-block; background:#e8f4fd; color:#0066cc;
  border:1px solid #bee3f8; border-radius:20px;
  padding:3px 10px; font-size:12px; font-weight:600; margin-bottom:8px;
}

/* ===== REACTIONS ===== */
.reactions-bar{
  display:flex; gap:6px; flex-wrap:wrap; margin:8px 0;
}
.reaction-btn{
  background:#f0f2f5; border:1px solid #ddd; border-radius:20px;
  padding:5px 12px; cursor:pointer; font-size:14px; transition:.15s;
  display:flex; align-items:center; gap:4px;
}
.reaction-btn:hover{ background:#e4e6ea; transform:scale(1.1); }
.reaction-btn.active{ background:#fff3e0; border-color:#a67b5b; font-weight:600; }
.reaction-btn span{ font-size:12px; color:#555; }

/* ===== REPORT BTN ===== */
.btn-report{
  background:none; border:none; color:#aaa; font-size:11px;
  cursor:pointer; padding:0; text-decoration:underline;
}
.btn-report:hover{ color:#e74c3c; }

/* ===== REPLY SYSTEM ===== */
.btn-reply{
  background:none; border:none; color:#a67b5b;
  font-size:12px; cursor:pointer; font-weight:600; text-decoration:underline;
}
.reply-form{
  display:none; margin-top:8px; gap:6px;
}
.reply-form.open{ display:flex; }
.reply-form input{ flex:1; font-size:13px; padding:6px 10px; }
.replies-section{
  margin-left:28px; border-left:2px solid #e8cfc1;
  padding-left:10px; margin-top:6px;
}

/* ===== REPORT MODAL ===== */
.modal-bg{
  display:none; position:fixed; inset:0;
  background:rgba(0,0,0,0.5); z-index:9999;
  align-items:center; justify-content:center;
}
.modal-bg.open{ display:flex; }
.modal-box{
  background:#fff; border-radius:16px; padding:24px;
  min-width:300px; max-width:400px; width:90%;
}
body.dark-mode .modal-box{ background:#16213e; color:#e8e8e8; }
.modal-box h3{ margin:0 0 16px; font-size:16px; }
.modal-box select, .modal-box button{ width:100%; margin-top:10px; padding:10px; border-radius:8px; }
.modal-box select{ border:1px solid #ddd; }
.modal-close{ background:#eee; border:none; cursor:pointer; }
body.dark-mode .modal-close{ background:#333; color:#eee; }
</style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../dashboard.php">&larr; Dashboard</a>


<!-- DARK MODE TOGGLE -->
<button class="dark-toggle" onclick="toggleDark()" title="Mode sombre">🌙</button>

<!-- NOTIFICATIONS -->
<div class="notif-wrapper">
  <button class="notif-btn" onclick="toggleNotif()" id="notif-btn">
    🔔
    <span class="notif-badge" id="notif-count" style="display:<?= $notifCount>0?'flex':'none' ?>">
      <?= $notifCount ?>
    </span>
  </button>
  <div class="notif-panel" id="notif-panel">
    <div class="notif-header">
      <span>Notifications</span>
      <button onclick="markAllRead()" style="background:none;border:none;color:#a67b5b;cursor:pointer;font-size:12px;">Tout lire</button>
    </div>
    <div id="notif-list">
      <?php
      $notifs = $postModel->getNotifications(1, 8);
      if(empty($notifs)){
          echo '<div class="notif-empty">Aucune notification</div>';
      }
      foreach($notifs as $n){ ?>
        <div class="notif-item <?= $n['is_read']?'':'unread' ?>">
          <?= htmlspecialchars($n['message']) ?>
          <div style="font-size:11px;color:#aaa;margin-top:3px;"><?= $n['created_at'] ?></div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<!-- REPORT MODAL -->
<div class="modal-bg" id="report-modal">
  <div class="modal-box">
    <h3>⚠️ Signaler ce contenu</h3>
    <input type="hidden" id="report-type" value="">
    <input type="hidden" id="report-target" value="">
    <select id="report-motif">
      <option value="spam">Spam</option>
      <option value="haine">Contenu haineux</option>
      <option value="faux">Fausse information</option>
      <option value="autre">Autre</option>
    </select>
    <button class="btn" onclick="submitReport()" style="margin-top:10px;">Envoyer le signalement</button>
    <button class="modal-close" onclick="closeReportModal()">Annuler</button>
  </div>
</div>

<!-- HEADER -->
<header class="hero">
  <div>
    <h1>Mini Forum ✨</h1>
    <div style="margin-top:15px;">
      <a href="addPost.php" class="btn">➕ Ajouter Post</a>
    </div>
  </div>
</header>

<?php if(isset($_GET['success'])){ ?>
<script>alert("✅ Post ajouté avec succès !");</script>
<?php } ?>

<!-- SEARCH -->
<form method="GET" style="text-align:center; margin:20px;">
  <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width:250px;">
  <button class="btn" type="submit">Rechercher</button>
  <?php if(isset($_GET['search']) || isset($_GET['tag'])): ?>
    <a href="index.php" class="btn" style="margin-left:8px;">✕ Réinitialiser</a>
  <?php endif; ?>
</form>

<!-- TAGS BAR -->
<div class="tags-bar">
  <a href="index.php" class="tag-pill <?= !isset($_GET['tag'])?'active':'' ?>">Tous</a>
  <?php foreach($allTags as $tag): ?>
    <a href="?tag=<?= $tag['id'] ?>" class="tag-pill <?= (isset($_GET['tag']) && $_GET['tag']==$tag['id'])?'active':'' ?>">
      #<?= htmlspecialchars($tag['nom']) ?>
    </a>
  <?php endforeach; ?>
</div>

<?php if($activeTag): ?>
  <div style="text-align:center;margin:10px 0;font-size:14px;color:#a67b5b;">
    Filtre actif : <strong>#<?= htmlspecialchars($activeTag['nom']) ?></strong>
  </div>
<?php endif; ?>

<!-- FEED -->
<div class="feed">

<?php foreach($posts as $post):
  $reactions  = $postModel->getReactions($post['id']);
  $userReact  = $postModel->getUserReaction($post['id']);
  $postTags   = $postModel->getPostTags($post['id']);
  $comments   = $postModel->getComments($post['id']);
?>

<div class="post">

  <!-- Badges -->
  <?php if($post['is_pinned']): ?>
    <div class="pinned-badge">📌 Épinglé</div>
  <?php endif; ?>
  <?php if(!empty($post['scheduled_at'])): ?>
    <div class="scheduled-badge">🕐 Publié le <?= date('d/m/Y à H:i', strtotime($post['scheduled_at'])) ?></div>
  <?php endif; ?>

  <!-- Tags du post -->
  <?php if(!empty($postTags)): ?>
    <div style="margin-bottom:8px;">
      <?php foreach($postTags as $t): ?>
        <a href="?tag=<?= $t['id'] ?>" class="tag-pill" style="font-size:11px;padding:2px 10px;">#<?= htmlspecialchars($t['nom']) ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <h3><?= htmlspecialchars($post['titre']) ?></h3>
  <p><?= nl2br(htmlspecialchars($post['contenu'])) ?></p>

  <?php if(!empty($post['image'])): ?>
    <img src="../../assets/images/<?= htmlspecialchars($post['image']) ?>">
  <?php endif; ?>

  <!-- REACTIONS -->
  <div class="reactions-bar" id="reactions-<?= $post['id'] ?>">
    <?php
    $reactionEmojis = ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢'];
    foreach($reactionEmojis as $type => $emoji): ?>
      <button class="reaction-btn <?= $userReact===$type?'active':'' ?>"
              onclick="sendReaction(<?= $post['id'] ?>, '<?= $type ?>')"
              id="rbtn-<?= $post['id'] ?>-<?= $type ?>">
        <?= $emoji ?> <span id="rcount-<?= $post['id'] ?>-<?= $type ?>"><?= $reactions[$type] ?: '' ?></span>
      </button>
    <?php endforeach; ?>
    <small style="align-self:center;color:#aaa;font-size:12px;">
      <span id="rtotal-<?= $post['id'] ?>"><?= $reactions['total'] ?></span> réaction<?= $reactions['total']>1?'s':'' ?>
    </small>
  </div>

  <!-- Follow + Signaler -->
  <div style="display:flex;gap:12px;align-items:center;margin:8px 0;">
    <?php if(isset($post['user_id'])): ?>
      <button onclick="followUser(<?= $post['user_id'] ?>)" class="btn-small">👥 Follow</button>
    <?php endif; ?>
    <button class="btn-report" onclick="openReport('post', <?= $post['id'] ?>)">⚠️ Signaler</button>
  </div>

  <hr style="border:none;border-top:1px solid #eee;margin:12px 0;">

  <!-- ADD COMMENT -->
  <div style="display:flex;gap:8px;margin-bottom:12px;">
    <input id="c-<?= $post['id'] ?>" placeholder="Écrire un commentaire..." style="flex:1;">
    <button class="btn" onclick="addComment(<?= $post['id'] ?>)" style="padding:8px 14px;">Envoyer</button>
  </div>

  <!-- COMMENTS + REPLIES -->
  <?php foreach($comments as $c): ?>
  <div class="comment-box" style="margin-top:10px;padding:12px;background:#f7f7f7;border-radius:12px;">

    <strong><?= htmlspecialchars($c['username'] ?? 'User') ?></strong>
    <small style="color:#aaa;margin-left:8px;"><?= $c['date_comment'] ?? '' ?></small>

    <p style="margin:6px 0;"><?= htmlspecialchars($c['contenu']) ?></p>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <button onclick="likeComment(<?= $c['id'] ?>)">❤️ <span id="clike-<?= $c['id'] ?>"><?= $postModel->countCommentLikes($c['id']) ?></span></button>
      <button class="btn-reply" onclick="toggleReply(<?= $c['id'] ?>)">💬 Répondre</button>
      <button class="btn-report" onclick="openReport('comment', <?= $c['id'] ?>)">⚠️</button>
    </div>

    <!-- Formulaire réponse -->
    <div id="reply-form-<?= $c['id'] ?>" class="reply-form">
      <input id="r-<?= $c['id'] ?>" placeholder="Votre réponse...">
      <button class="btn" onclick="sendReply(<?= $post['id'] ?>, <?= $c['id'] ?>)" style="padding:6px 12px;white-space:nowrap;">Envoyer</button>
    </div>

    <!-- Réponses imbriquées -->
    <?php $replies = $postModel->getReplies($c['id']); ?>
    <?php if(!empty($replies)): ?>
    <div class="replies-section">
      <?php foreach($replies as $r): ?>
      <div class="comment-box" style="margin-top:8px;padding:10px;background:#efefef;border-radius:10px;">
        <strong><?= htmlspecialchars($r['username'] ?? 'User') ?></strong>
        <small style="color:#aaa;margin-left:6px;"><?= $r['date_comment'] ?? '' ?></small>
        <p style="margin:4px 0;"><?= htmlspecialchars($r['contenu']) ?></p>
        <button onclick="likeComment(<?= $r['id'] ?>)">❤️ <span id="clike-<?= $r['id'] ?>"><?= $postModel->countCommentLikes($r['id']) ?></span></button>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
  <?php endforeach; ?>

</div>

<?php endforeach; ?>
</div>

<script>
// ===== DARK MODE =====
(function(){
  if(localStorage.getItem('darkMode')==='1'){
    document.body.classList.add('dark-mode');
  }
})();
function toggleDark(){
  document.body.classList.toggle('dark-mode');
  localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')?'1':'0');
}

// ===== REACTIONS =====
function sendReaction(post_id, type){
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){
    try{
      let data = JSON.parse(this.responseText);
      let emojis = {like:'👍',love:'❤️',haha:'😂',wow:'😮',sad:'😢'};
      for(let t in emojis){
        let el = document.getElementById('rcount-'+post_id+'-'+t);
        if(el) el.textContent = data[t] > 0 ? data[t] : '';
        let btn = document.getElementById('rbtn-'+post_id+'-'+t);
        if(btn) btn.classList.remove('active');
      }
      let total = document.getElementById('rtotal-'+post_id);
      if(total) total.textContent = data.total;
    }catch(e){}
  };
  xhr.send("setReaction=1&post_id="+post_id+"&type="+type);
}

// ===== LIKE COMMENT =====
function likeComment(id){
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){ document.getElementById("clike-"+id).innerHTML=this.responseText; };
  xhr.send("likeComment=1&id="+id);
}

// ===== ADD COMMENT =====
function addComment(id){
  let contenu = document.getElementById("c-"+id).value.trim();
  if(contenu==""){alert("❌ Commentaire vide !");return;}
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){ location.reload(); };
  xhr.send("addComment=1&post_id="+id+"&contenu="+encodeURIComponent(contenu)+"&username=Moi");
}

// ===== REPLY =====
function toggleReply(id){
  let form = document.getElementById("reply-form-"+id);
  form.classList.toggle('open');
  if(form.classList.contains('open')) document.getElementById("r-"+id).focus();
}
function sendReply(post_id, parent_id){
  let contenu = document.getElementById("r-"+parent_id).value.trim();
  if(contenu==""){alert("❌ Réponse vide !");return;}
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){ location.reload(); };
  xhr.send("addComment=1&post_id="+post_id+"&parent_id="+parent_id+"&contenu="+encodeURIComponent(contenu)+"&username=Moi");
}

// ===== FOLLOW =====
function followUser(user_id){
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){ alert("✅ Follow effectué !"); };
  xhr.send("follow=1&user_id="+user_id);
}

// ===== NOTIFICATIONS =====
function toggleNotif(){
  let panel = document.getElementById('notif-panel');
  panel.classList.toggle('open');
  if(panel.classList.contains('open')) markAllRead();
}
function markAllRead(){
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){
    document.getElementById('notif-count').style.display='none';
    document.querySelectorAll('.notif-item.unread').forEach(el=>el.classList.remove('unread'));
  };
  xhr.send("markNotifRead=1");
}
// Poll toutes les 30 secondes
setInterval(function(){
  let xhr = new XMLHttpRequest();
  xhr.open("GET","../../controller/PostController.php?pollNotifications=1",true);
  xhr.onload=function(){
    try{
      let data = JSON.parse(this.responseText);
      let badge = document.getElementById('notif-count');
      if(data.count > 0){
        badge.style.display='flex';
        badge.textContent=data.count;
      } else {
        badge.style.display='none';
      }
    }catch(e){}
  };
  xhr.send();
}, 30000);

// ===== SIGNALEMENT =====
function openReport(type, id){
  document.getElementById('report-type').value = type;
  document.getElementById('report-target').value = id;
  document.getElementById('report-modal').classList.add('open');
}
function closeReportModal(){
  document.getElementById('report-modal').classList.remove('open');
}
function submitReport(){
  let type   = document.getElementById('report-type').value;
  let target = document.getElementById('report-target').value;
  let motif  = document.getElementById('report-motif').value;
  let xhr = new XMLHttpRequest();
  xhr.open("POST","../../controller/PostController.php",true);
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.onload=function(){ alert("✅ Signalement envoyé. Merci."); closeReportModal(); };
  xhr.send("report=1&report_type="+type+"&target_id="+target+"&motif="+motif);
}
// Fermer modal en cliquant dehors
document.getElementById('report-modal').addEventListener('click',function(e){
  if(e.target===this) closeReportModal();
});
</script>
</body>
</html>