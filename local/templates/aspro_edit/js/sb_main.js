/**
 * Created by DNKolosov on 19.10.2016.
 */
$(function(){
    $(document).ready(function()
    {

        slickInit();

        slickBrand();

        $('.catalog').on('click', '.sb_offers li',function()
        {
            var $element = $(this),
                $skuContainer = $element.parents('.sb_sku_container'),
                $container = $element.parents('.sb_full_container');
            if($element.hasClass('active'))
                return false;


            if($skuContainer.hasClass('prop-type-f'))
            {
                var elementId = [],
                    propValue = $element.attr('data-value'),
                    idSelect = $element.attr('data-id'),
                    propId = $element.attr('data-prop-id');

                $element.parent().children('li').each(function()
                {
                    elementId.push($(this).attr('data-id'));
                });

                SbGlobal.managerAjax.setParams({
                    data: {'elementId': idSelect, 'propId': propId, 'propValue' : propValue, 'sku': elementId, 'params': SbGlobal.arParams},
                    url: '/ajax/sb_ajax.php?method=getElementOffer'
                });
                SbGlobal.managerAjax.ajax(function(data){
                    if(data.error_status == false)
                    {
                        $container.html($(data.html).find('.sb_full_container').html());
                        setBasketAspro();
                        reHeight();
                        slickInit($element.attr('data-slick-index'), $container.find('ul'));
                    }
                    else
                    {
                        console.log(data.errorText);
                    }
                });
            }
            else
            {
                var elementId = $skuContainer.attr('data-id'),
                    propValue = [],
                    idSelect = $element.attr('data-id'),
                    propId = [];

                $container.find('.sb_sku_container').each(function()
                {
                    propId.push($(this).find('.cnt_item.active').parents('li.slick-slide').attr('data-prop-id'));

                    if($element.attr('data-prop-id') == $(this).find('.cnt_item.active').parents('li.slick-slide').attr('data-prop-id'))
                    {
                        propValue.push($element.attr('data-value'));
                        return false;
                    }
                    else
                        propValue.push($(this).find('.cnt_item.active').parents('li.slick-slide').attr('data-value'));
                });

                SbGlobal.managerAjax.setParams({
                    data: {'elementId': idSelect, 'propId': propId, 'propValue' : propValue, 'sku': elementId, 'params': SbGlobal.arParams},
                    url: '/ajax/sb_ajax.php?method=getElementOffer'
                });
                SbGlobal.managerAjax.ajax(function(data){
                    if(data.error_status == false)
                    {
                        $container.html($(data.html).find('.sb_full_container').html());
                        setBasketAspro();
                        reHeight();
                        slickInit($element.attr('data-slick-index'), $container.find('ul'));
                    }
                    else
                    {
                        console.log(data.errorText);
                    }
                });

            }

            return false;
        });

        $(".sb-select").select2({
            minimumResultsForSearch: Infinity,
            templateResult: Select2Customize,
            templateSelection: SelectToCustom
        }).on("select2:open", function () {
            $('.select2-results__options').scrollbar({
                showArrows: true,
                "scrollx": "advanced",
                "scrolly": "advanced"
            });
        });

        /**
         * функции увеличения фотки в детальной карточке при выборе цвета
         */
        $(function()
        {
            var containerStandart = 'li.select2-results__option',
                resizeContainer = containerStandart + '.resize';

            $('body').on('mouseleave', resizeContainer, function()
            {
                $(resizeContainer).remove();
            }).on('click', resizeContainer, function()
            {
                $(resizeContainer).remove();
            }).on('mouseenter', '.select2-results__options li', function(e)
            {
                $(resizeContainer).remove();
                if(e.originalEvent == undefined)
                    return true;
                var $el = $(this).clone().appendTo('body');
                var coord = $(this).offset();
                $el.addClass('resize').css({
                    'top': coord.top - ($el.height()/2 - $(this).height()/2),
                    'left': coord.left - ($el.width()/2 - $(this).width()/2)
                });

            }).on('mousemove', function(e)
            {
                if($(containerStandart).has(e.target).length === 0 && !$(containerStandart).is(e.target))
                    $(resizeContainer).remove();
                return true;
            });
        });
    });

    function slickBrand()
    {
        var $container = $('.pict_block.slider');

        $container.slick({
            arrows: true,
            slidesToShow: 6,
            slidesToScroll: 6,
            infinite: false,
            //variableWidth: true,
            //responsive: responsive,
            nextArrow: '<button type="button" class="sb-next"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
            prevArrow: '<buuton type="button" class="sb-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></buuton>',
        });
    }

    /**
     * инитит слик-слайдер, в передаваемом параметре может быть указан индекс текущего слайдера
     * @param index
     * @param all
     */
    function slickInit(index, all)
    {
        if(SbGlobal.arParams == undefined)
            return;
        
        var show,
            scroll;

        if (index == void 0)
            index = 0;

        //index = Math.floor(index/show);

        if (all == void 0)
            all = true;

        var $slider = $('.item_detail_scu_' + SbGlobal.arParams.DISPLAY_TYPE + ' ul');

        if(all != true)
            $slider = all;

        $slider.each(function()
        {
            var responsive = null;
            if($(this).hasClass('prop-type-f'))
            {
                show = SbGlobal.arParams.DISPLAY_TYPE == 'block' ? 3 : 8;
                scroll = SbGlobal.arParams.DISPLAY_TYPE == 'block' ? 3 : 7;

                if(SbGlobal.arParams.DISPLAY_TYPE == 'list')
                {
                    responsive = [
                        {
                            breakpoint: 1170,
                            settings: {
                                slidesToShow: 7,
                                slidesToScroll: 6
                            }
                        },
                        {
                            breakpoint: 1080,
                            settings: {
                                slidesToShow: 6,
                                slidesToScroll: 5
                            }
                        },
                        {
                            breakpoint: 1020,
                            settings: {
                                slidesToShow: 5,
                                slidesToScroll: 4
                            }
                        },
                        {
                            breakpoint: 940,
                            settings: {
                                slidesToShow: 4,
                                slidesToScroll: 3
                            }
                        },
                        {
                            breakpoint: 870,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 2
                            }
                        }
                    ];
                }
            }
            else
            {
                if($(this).hasClass('razmer1_instrumenty'))
                {
                    show = SbGlobal.arParams.DISPLAY_TYPE == 'block' ? 5 : 12;
                    scroll = SbGlobal.arParams.DISPLAY_TYPE == 'block' ? 5 : 11;
                }
                else
                {
                    show = SbGlobal.arParams.DISPLAY_TYPE == 'block' ? 6 : 15;
                    scroll = SbGlobal.arParams.DISPLAY_TYPE == 'block' ? 6 : 14;
                }
            }

            if(SbGlobal.arParams.DISPLAY_TYPE == 'detail')
            {
                show = $(this).hasClass('razmer1_instrumenty') ? 7 : 9;
                scroll = $(this).hasClass('razmer1_instrumenty') ? 6 : 8;
            }

            $(this).slick({
                arrows: true,
                slidesToShow: show,
                slidesToScroll: scroll,
                infinite: false,
                responsive: responsive,
                nextArrow: '<button type="button" class="sb-next"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
                prevArrow: '<buuton type="button" class="sb-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></buuton>',
            });
            $(this).slick('slickGoTo', index);
            //console.log($(this));
        });
    }

    function Select2Customize(state) {
        if (!state.id) { return state.text; }
        var $state = $(
            '<a href="' + $(state.element).attr('data-href') + '"><img height="78px" title="' + $(state.element).attr('data-name') + '" src="' + $(state.element).attr('data-src') + '" class="sb_border_color" /><span class="sb_background_color">' + $(state.element).attr('data-id') + '</span></a>'
        );
        return $state;
    }

    function SelectToCustom(data, container) {
        //console.log();
        var result = data.text + $(data.element).attr("data-id");
        return result;
    }

    function reHeight()
    {
        $('.catalog_block .catalog_item_wrapp .catalog_item .cost').sliceHeightExt({'parent':'.catalog_item_wrapp'});
        $('.catalog_block .catalog_item_wrapp .catalog_item .item-title').sliceHeightExt({'parent':'.catalog_item_wrapp'});
        $('.catalog_block .catalog_item_wrapp .catalog_item .counter_block').sliceHeightExt({'parent':'.catalog_item_wrapp'});
        $('.catalog_block .catalog_item_wrapp').sliceHeightExt({'classNull':'.hover_block'});
    }
});
