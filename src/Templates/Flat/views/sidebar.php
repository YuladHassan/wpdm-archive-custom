<?php
if(!defined("ABSPATH")) die("Shit happens!");
?><div class="col-md-4">
    <ul class="wpdm-cat-tree">
        <?php
        $this->renderCats($parent, wpdm_valueof($params, 'button_style'), $parent, wpdm_valueof($params, 'showcount'));
        ?>
    </ul>
</div>
