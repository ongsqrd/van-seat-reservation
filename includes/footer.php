<?php
  /* ------------------------------------------------------------
     Shared closing tags. A page may set $page_script (a filename
     in /public/js) before including footer.php to load a script
     — e.g. booking-confirm sets 'booking-confirm.js'. Most pages
     set nothing and just close out.
     ------------------------------------------------------------ */
?>
  <?php if (!empty($page_script)): ?>
    <script src="js/<?= htmlspecialchars($page_script) ?>"></script>
  <?php endif; ?>
</body>

</html>