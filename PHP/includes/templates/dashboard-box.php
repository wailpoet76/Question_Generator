<?php
// Auther: Walid Bakr
// Date: 2024-07-15
// Last Update: 2024-09-01
// Description: DASH BOARD

/**
 * Template for dashboard box
 * @param array $box Box configuration from getDashboardBoxConfig()
 */
?>
<div class="<?php echo $box['id']; ?>">
    <div class="colorbar <?php echo $box['active'] ? 'active' : ''; ?>"></div>
    <div class="create">
        <?php echo fn_lang("CREATED") ?>: <?php echo $box['date'] ?: "EMPTY"; ?>
    </div>
    <div class="tabcontent">
        <p class="logo">
            <i class="<?php echo $box['icon']; ?> fa-3x" style="color: <?php echo $box['icon_color']; ?>;"></i>
        </p>
        <div class="label">
            <h5><?php echo fn_lang($box['title']); ?></h5>
        </div>
    </div>
    <div class="tabfooter">
        <p><?php echo fn_Lang("RESULT") . " (" . $box['count'] . ")"; ?></p>
    </div>
</div>
