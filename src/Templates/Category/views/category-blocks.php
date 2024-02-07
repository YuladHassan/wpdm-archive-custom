<?php
/**
 * User: shahnuralam
 * Date: 24/12/18
 * Time: 3:20 AM
 */

use WPDM\Category\CategoryController;

if (!defined('ABSPATH')) die();

?>
<div class="w3eden" id="category-blocks-<?php echo $sec_id; ?>">
    <div class="category-blocks <?php echo isset($params['container'])?$params['container']:'' ?>">
        <div class="row">
        <?php
        foreach ($categories as $i => $category) {
            if (is_object($category)) {
                $icon = CategoryController::icon($category->term_id);
                $icon = $icon ? $icon : WPDMAP_ASSET_URL."images/default-cat-icon.svg";
                ?>

                <div class="<?php echo $grid_class; ?>">
                    <a href="<?php echo get_term_link($category->term_id); ?>"
                       class="card card-default panel panel-default panel-category">
                        <div class="card-body panel-body text-center">


                            <img class="cat-icon" src="<?php echo $icon; ?>" alt="<?php echo $category->name; ?>"/>
                            <h3 class="cat-name"><?php echo $category->name; ?></h3>
                            <div class="cat-info text-muted"><?= sprintf(__('%s items', WPDMAP_TEXT_DOMAIN), $category->count); ?></div>
                            <span class="btn btn-primary"><?= __('Explore', WPDMAP_TEXT_DOMAIN) ?></span>

                        </div>
                    </a>
                </div>


            <?php }
        }
            ?>
        </div>
    </div>
</div>
<style>
    #category-blocks-<?php echo $sec_id; ?>.w3eden .cat-icon{ width: 48px; margin: 5px 15px 15px; }
    #category-blocks-<?php echo $sec_id; ?>.w3eden h3.cat-name{
        margin: 0 0 5px; font-weight: 700; font-size: 12pt; color: #555555;
        -webkit-transition: all 400ms ease-in-out;
        -moz-transition: all 400ms ease-in-out;
        -ms-transition: all 400ms ease-in-out;
        -o-transition: all 400ms ease-in-out;
        transition: all 400ms ease-in-out;
    }
    #category-blocks-<?php echo $sec_id; ?>.w3eden .cat-info{ margin-bottom: 10px; font-size: 12px; }
    #category-blocks-<?php echo $sec_id; ?>.w3eden .panel-category{
        padding: 20px;
        -webkit-transition: all 400ms ease-in-out;
        -moz-transition: all 400ms ease-in-out;
        -ms-transition: all 400ms ease-in-out;
        -o-transition: all 400ms ease-in-out;
        transition: all 400ms ease-in-out;
        display: block;
    }
    #category-blocks-<?php echo $sec_id; ?>.w3eden .panel-category:hover{
        border: 1px solid <?php echo $hover_color; ?> !important;
        text-decoration: none !important;
    }
    #category-blocks-<?php echo $sec_id; ?>.w3eden a:hover{
        text-decoration: none !important;
    }
    #category-blocks-<?php echo $sec_id; ?>.w3eden a:hover .cat-name{
        color: <?php echo $hover_color; ?> !important;
    }
    #category-blocks-<?php echo $sec_id; ?>.w3eden .btn{
        background-color: <?php echo $button_color; ?> !important;
        border-color: <?php echo $button_color; ?> !important;
        text-decoration: none !important;
        font-size: 11px;
        letter-spacing: 1.5px;
    }
    #category-blocks-<?php echo $sec_id; ?>.w3eden a:hover .btn{
        background-color: <?php echo $hover_color; ?> !important;
    }
</style>
<script>
    var wpdmap_params = '<?=\WPDM\__\Crypt::encrypt($params); ?>';
</script>
