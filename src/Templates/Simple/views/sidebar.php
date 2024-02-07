<?php
if(!defined("ABSPATH")) die("Shit happens!");
?><div class="col-md-4">
    <ul class="wpdm-cat-tree">
        <?php $this->renderCats(wpdm_valueof($params, 'category'), wpdm_valueof($params, 'button_style'), wpdm_valueof($params, 'category'), wpdm_valueof($params, 'showcount')); ?>
    </ul>
</div>