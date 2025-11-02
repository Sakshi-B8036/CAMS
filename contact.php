<?php
require_once 'config.php';
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name===''||$email===''||$message==='') $error='All fields required.';
    elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Invalid email';
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name,email,subject,message) VALUES (:n,:e,:s,:m)");
            $stmt->execute([':n'=>$name,':e'=>$email,':s'=>$subject,':m'=>$message]);
            $success = "Message sent. Thank you!";
        } catch (PDOException $e) { $error="DB error: ".$e->getMessage(); }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Contact</title></head>
<body style="font-family:Segoe UI;background:linear-gradient(90deg,#74617c,#3498db);color:#fff;padding:30px">
<div style="max-width:700px;margin:0 auto;background:rgba(0,0,0,0.28);padding:20px;border-radius:10px">
<h2>Contact Us</h2>
<?php if ($error) echo "<div style='background:#c0392b;padding:8px;border-radius:6px'>".htmlspecialchars($error)."</div>"; ?>
<?php if ($success) echo "<div style='background:#2ecc71;padding:8px;border-radius:6px;color:#052d12'>".htmlspecialchars($success)."</div>"; ?>
<form method="post">
<input name="name" placeholder="Your name" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px"><br>
<input name="email" placeholder="Your email" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px"><br>
<input name="subject" placeholder="Subject" style="width:100%;padding:8px;margin:6px 0;border-radius:6px"><br>
<textarea name="message" placeholder="Message" required style="width:100%;padding:8px;margin:6px 0;border-radius:6px" rows="6"></textarea><br>
<button style="padding:10px 14px;background:#1abc9c;border:none;border-radius:6px;color:#fff">Send</button>
</form>
<p><a href="index.php" style="color:#fff">← Back to Home</a></p>
</div></body></html>
