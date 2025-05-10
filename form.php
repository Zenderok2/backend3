<html>
<head>
  <style>
    .error {
      border: 2px solid red;
    }
  </style>
</head>
<body>

<?php
if (!empty($messages)) {
  print('<div id="messages">');
  foreach ($messages as $msg)
    print($msg);
  print('</div>');
}

if (!empty($_SESSION['login'])) {
  echo "<p>Вы вошли как <strong>{$_SESSION['login']}</strong>.</p>";
  echo '<form method="post" action="logout.php">
          <input type="submit" value="Выйти" />
        </form>';
}
?>

<form action="" method="POST">
  <label>ФИО:</label><br />
  <input name="fio"
         <?php if (!empty($errors['fio'])) print 'class="error"'; ?>
         value="<?php print htmlspecialchars($values['fio'] ?? '', ENT_QUOTES); ?>" /><br /><br />

  <label>Email:</label><br />
  <input name="email"
         <?php if (!empty($errors['email'])) print 'class="error"'; ?>
         value="<?php print htmlspecialchars($values['email'] ?? '', ENT_QUOTES); ?>" /><br /><br />

  <label>Дата рождения:</label><br />
  <input name="dob" type="date"
         <?php if (!empty($errors['dob'])) print 'class="error"'; ?>
         value="<?php print htmlspecialchars($values['dob'] ?? '', ENT_QUOTES); ?>" /><br /><br />

  <label>Телефон:</label><br />
  <input name="phone"
         <?php if (!empty($errors['phone'])) print 'class="error"'; ?>
         value="<?php print htmlspecialchars($values['phone'] ?? '', ENT_QUOTES); ?>" /><br /><br />

  <label>Сообщение:</label><br />
  <textarea name="bio"
            <?php if (!empty($errors['bio'])) print 'class="error"'; ?>><?php print htmlspecialchars($values['bio'] ?? '', ENT_QUOTES); ?></textarea><br /><br />

  <input type="submit" value="Сохранить" />
</form>

</body>
</html>
