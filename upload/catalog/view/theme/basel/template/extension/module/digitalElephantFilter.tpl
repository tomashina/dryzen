<?php
/**
 * INTERFACE BY MANUFACTURER, CATEGORY, OPTIONS, ATTRIBUTES:
 * $input_name
 * $input_value
 * $input_label
 * $element_id(options, attributes, manufacturer, category)
 * $image
 */

/* @var $is_filter_show         bool */
/* @var $heading_title          string */
/* @var $JS_config              array */
/* @var $text_price_range       string */
/* @var $symbol_left            string */
/* @var $symbol_right           string */
/* @var $is_show_group_attributes  bool */
/* @var $is_button_apply           bool */
/* @var $is_button_clear        bool */
/* @var $text_ok                string */
/* @var $text_clear             string */
/* @var $is_ajax_render         bool */
?>
<?php if ($is_filter_show) { ?>
<div class="filter-container">
    <div id="digitalElephantFilter_box">

        <div class="box-content digitalElephantFilter">
            <form id="digitalElephantFilter_form" action="<?= $JS_config['action']['getProduct']; ?>" method="get">
                <?php if (!empty($filter_data['price']['show'])) { ?>
                    <div class="filter_element_wrap">
                        <div
                            class="filter_element_title"
                            data-section-name="price"
                            data-section-id="price"
                            >
                            <?= $text_price_range ?>
                        </div>
                        <?php $display = 'none'; ?>
                        <?php if ($filter_data['price']['open']) $display = 'block';?>
                        <div class="filter_element_content price_slider collapsible" style="display: <?= $display; ?>">
                            <input type="hidden" id="digitalElephantFilter_changing_price_min"
                                               value="<?= $filter_data['price']['min']; ?>" name="price[min]"
                                               class="price">
                            <input type="hidden" id="digitalElephantFilter_changing_price_max"
                                               value="<?= $filter_data['price']['max']; ?>" name="price[max]"
                                               class="price">
                            <div id="slider-range"></div>
                            <div class="price-range-holder">
							<?= $text_price_range ?>
							<b>
							<?= $symbol_left ?><span id="price-min-holder"><?= $filter_data['price']['min']; ?></span><?= $symbol_right ?>
							-
							<?= $symbol_left ?><span id="price-max-holder"><?= $filter_data['price']['max']; ?></span><?= $symbol_right ?>
							</b>
							</div>
                        </div>
                    </div>
                <?php } ?>


                <?php foreach ($filter_data['packages'] as $package) { ?>
                    <?php
                    /**
                     * $filter_data = array(category[], manufacturer[], attributes[], options[])
                     */
                    ?>
                    <?php foreach ($package as $section_group) { ?>

                        <?php if ($section_group['sections']) { ?>

                            <?php if ($is_show_group_attributes && $section_group['group_name']) { ?>
                                <div class="filter_element_title"><?= $section_group['group_name']; ?></div>
                            <?php } ?>

                            <?php foreach ($section_group['sections'] as $section) { ?>

                                <?php if ($section['type'] && $section['values']) { ?>
                                    <div class="filter_element_wrap">

                                        <div
                                            class="filter_element_title <?php if ($is_show_group_attributes && $section_group['group_name']) { ?>is-attribute-child<?php } ?>"
                                            data-section-name="<?= $section['name']; ?>"
                                            data-section-id="<?= $section['id']; ?>"
                                            ><?= $section['name']; ?></div>

                                        <?php $display = 'none'; ?>
                                        <?php if ($section['open']) $display = 'block';?>
                                        <div class="filter_element_content" style="display: <?= $display; ?>">

                                            <?php $inputs_data = $section['values']; ?>
                                            <?php include __DIR__ . '/digitalElephantFilter/' . $section['type'] . '.tpl'; ?>
                                        </div>
                                    </div>
                                <?php } ?>

                            <?php } ?>
                        <?php } ?>

                    <?php } ?>

                <?php } ?>
                <div>
                    <table width="100%" class="digitalElephantFilter_btn">
                        <tr>
                            <?php if ($is_button_apply && $is_button_clear) { ?>
                                <td width="50%" style="padding-right:2px;"><input type="reset" id="digitalElephantFilter_button_clear"
                                                       class="btn btn-outline"
                                                       value="<?= $text_clear ?>"/></td>
                                <td width="50%" style="padding-left:2px;"><input type="button" id="digitalElephantFilter_button_apply"
                                                       class="btn btn-contrast"
                                                       value="<?= $text_ok; ?>"/></td>
                            <?php } elseif ($is_button_apply) { ?>
                                <td width="100%"><input type="button" id="digitalElephantFilter_button_apply"
                                                        class="btn btn-contrast"
                                                        value="<?= $text_ok; ?>"/></td>
                            <?php } elseif ($is_button_clear) { ?>
                                <td width="100%"><input type="reset" id="digitalElephantFilter_button_clear"
                                                        class="btn btn-outline"
                                                        value="<?= $text_clear ?>"/></td>
                            <?php } ?>
                        </tr>
                    </table>
                </div>

                <!-- HIDDEN FIELDS BEGIN -->
                <input type="hidden" name="ajax_digitalElephantFilter" value="1"/>
            </form>

            <script>
                var DEFConfig = {
                    peakPrice: {
                        min: '<?= $JS_config['peakPrice']['min'] ?>',
                        max: '<?= $JS_config['peakPrice']['max'] ?>'
                    },
                    currentPrice: {
                        min: '<?= $JS_config['currentPrice']['min'] ?>',
                        max: '<?= $JS_config['currentPrice']['max'] ?>'
                    },
                    selector: {
                        containerProducts: '<?= $JS_config['selector']['containerProducts'] ?>',
                        pagination: '<?= $JS_config['selector']['pagination'] ?>',
                        quantityProducts: '<?= $JS_config['selector']['quantityProducts'] ?>',
                        limit: '<?= $JS_config['selector']['limit'] ?>',
                        sort: '<?= $JS_config['selector']['sort'] ?>'
                    },
                    action: {
                        category: '<?= $JS_config['action']['category'] ?>',
                        categoryProduct: '<?= $JS_config['action']['categoryProduct'] ?>',
                        ajaxRenderPagination: '<?= $JS_config['action']['ajaxRenderPagination'] ?>',
                        ajaxRenderQuantityProducts: '<?= $JS_config['action']['ajaxRenderQuantityProducts'] ?>',
                        getProduct: '<?= $JS_config['action']['getProduct'] ?>',
                        ajaxCheckToRenderShowMore: '<?= $JS_config['action']['ajaxCheckToRenderShowMore'] ?>',
                        ajaxRenderPanel: '<?= $JS_config['action']['ajaxRenderPanel'] ?>',
                        ajaxSetStateSection: '<?= $JS_config['action']['ajaxSetStateSection'] ?>'
                    },
                    text: {
                        productNotFound: '<?= $JS_config['text']['productNotFound'] ?>',
                        buttonShowMore: '<?= $JS_config['text']['buttonShowMore'] ?>'
                    },
                    categoryPath: '<?= $JS_config['categoryPath'] ?>',
                    state: {
                        isButtonShowMore: '<?= $JS_config['state']['isButtonShowMore']?>',
                        isPagination: '<?= $JS_config['state']['isPagination']?>',
                        isQuantityProducts: '<?= $JS_config['state']['isQuantityProducts']?>'
                    },
                    isButtonApply: '<?= $JS_config['isButtonApply']?>',
                    preloaderClass: '<?= $JS_config['preloaderClass']?>'
                };
            </script>

            <?php if ($is_ajax_render) { ?>
                <script>
                    $(function(){
                        //instantiation
                        var DEFController = new DigitalElephantFilterController();
                        initSliderPrice(DEFController);
                        //instantiation END
                    });
                </script>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
// Basel
$(document).ready(function() {
	$('.filter-trigger-btn').addClass('active').html('<i class="icon-line-menu icon"></i> <b><?= $heading_title ?></b>');
	// Basel
	$(".filter-trigger-btn").click(function(){
	$('html').addClass('no-scroll side-filter-open');
	$('.body-cover').addClass('active');
	});
});
</script>
<?php } ?>