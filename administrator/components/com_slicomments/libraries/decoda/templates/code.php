<?php
// No direct access
defined('_JEXEC') or die;

// Place $content directly within the tags to not leave any whitespace for <pre>
?>
<pre class="slicomments-code<?php if (!empty($default)) { echo ' code-'. htmlentities($default, ENT_QUOTES, 'UTF-8'); } ?>"<?php if (isset($hl)): ?> data-highlight="<?php echo htmlentities($hl, ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?>><?php echo $content; ?></pre>
