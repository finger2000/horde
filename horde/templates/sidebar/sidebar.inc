<div class="sidebar" style="width:<?php echo $prefs->getValue('sidebar_width') ?>px">
 <div id="expandedSidebar"<?php if (!$show_sidebar): ?> style="display:none"<?php endif; ?>>
  <span id="expandButton"><?php echo Horde::img('hide_panel.png', null, array('title' => _("Collapse Sidebar"))) ?></span>
  <div id="sidebarPanel">
   <div id="sidebarLoading" style="display:none"><?php echo _("Loading...") ?></div>
   <?php $tree->renderTree() ?>
  </div>
 </div>

 <div id="hiddenSidebar"<?php if ($show_sidebar): ?> style="display:none"<?php endif; ?>>
  <?php echo Horde::img('show_panel.png') ?>
 </div>
</div>
