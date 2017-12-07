/**
 * main.js
 * @authors chenhaoqiang (chenhaoqiang.irxk@gmail.com)
 * @date    2017-11-16 16:26:55
 * @version $Id$
 */

var g_scrolls = new Object();
// 滚动插件iscroll.js
function loaded() {
    $("div[class*='iscroll-container']").each(function() {
        var id = $(this).attr("data-iscroll-id");
        g_scrolls[id] = new iScroll(this, { 
        						scrollbars: true, //是否显示滚动条。默认为false;
        						fadeScrollbars:true, //滚动条淡入淡出效果,当然前提是你滚动条显示了。默认为false;
        						bounce: true, //滚动到达容器边界时是否执行反弹动画。默认为true;
        						mouseWheel:true, //是否显示启用鼠标滚动;默认为false;
        						invertWheelDirection:true, //激活鼠标滚动后是否启用反向滚动;默认为false;
    							hScrollbar:false,  //hScroll *	是否允许水平滚动
								vScrollbar:true,   //vScroll *	是否允许垂直滚动


    						});
    });
}
window.addEventListener('load', function() { setTimeout(loaded, 200); }, false);


var cart_data = {};
var is_loaded = 0;  //商品列表是否加载完成
var cat_id = 0; //初始化分类
var goods_search = ''; //搜索关键字
var p = 0; //页数

$(function() {

    $(document).ajaxError(function() { 
            layer.open({content:'页面过期请刷新页面',skin: 'msg',time: 2});
    });
    ajax_get_goods_list();//加载商品
    ajax_goodscate();//加载分类
    calcCartTotal(); //计算购物车总价总数量
    // ajax_get_goods_list(); /

    // //上拉加载
    // $(window).scroll(function (e) {
    //     var bot = 60; //bot是底部距离的高度
    //     if ((bot + $(window).scrollTop()) >= ($('.list-pro').height() - $(window).height()) && is_loaded == 1) {   
    //         prompt('block', '正在加载...');
    //         is_loaded = 0; //限制触发一次                                                                         //$(window).scrollTop()这个方法是当前滚动条滚动的距离
    //         p++;                                                                                        //$(window).height()获取当前窗体的高度
    //         ajax_get_goods_list();                                                                                  //$(document).height()获取当前文档的高度 
    //     }
    // });

})

//加载商品列表
function ajax_get_goods_list() {
    $.ajax({
        type: "GET",
        url: tp_url('Index','getGoods'),  
        data: {'p' : p, 'cat_id' : cat_id,},
        success: function(res) {
            //console.log(res);
            //$('.read').css('display','none');    //隐藏顶部加载状态
            //prompt('none', '正在加载...'); //隐藏底部状态
            // var goodslist_data_length = result.data.length;
            // if(goodslist_data_length == 0) { prompt('block', '已到底部 ！'); return false;}
            var goodslist_data_str = '';
            for(i in res) {
                goodslist_data_str += '<li id="menu-product-'+res[i]['id']+'" class="product-item tap-action shopping-item" data-code="'+res[i]['goods_code']+'">';
                goodslist_data_str +=     '<div class="product-imgwrap  product-zero ">';
                goodslist_data_str +=         '<div id="menu-product-img-'+res[i]['id']+'" class="product-img" onclick="showDetail('+res[i]['id']+')">';
                goodslist_data_str +=         '<img class="lazy_product" data-original="'+UPLOADS+''+res[i]['goods_img']+'" src="'+UPLOADS+''+res[i]['img_100']+'"> ';
                goodslist_data_str +=         '</div>';
                goodslist_data_str +=     '</div>';
                goodslist_data_str +=     '<div class="product-desc">';
                goodslist_data_str +=        '<p class="product-name ui-ellipsis">'+res[i]['goods_name']+'</p>';
                goodslist_data_str +=         '<p class="product-model">TL-WQ、M1051001 </p>';
                goodslist_data_str +=         '<p class="product-attribute">散装</p>';
                goodslist_data_str +=         '<p class="product-wholesale-price">批发:￥'+res[i]['shop_price']+'</p>';
                goodslist_data_str +=         '<p class="product-retail-price">零售:￥'+res[i]['market_price']+'</p>';
                goodslist_data_str +=     '</div>';
                

                
                goodslist_data_str +=     '<div class="product-shopope '+(res[i]['cart_num'] ? '' : 'countzero')+' ">';
                goodslist_data_str +=         '<div class="shopping-action product-shopope-del" onclick="editGoodsNum(\''+res[i]['goods_code']+'\', -1)">';
                goodslist_data_str +=             '<div class="add-del-bg"> </div>';
                goodslist_data_str +=         '</div>';
                goodslist_data_str +=         '<div class="product-shopope-num">'+res[i]['cart_num']+'</div>';
                goodslist_data_str +=         '<div class="shopping-action product-shopope-add" onclick="editGoodsNum(\''+res[i]['goods_code']+'\', 1)">';
                goodslist_data_str +=             '<div class="add-del-bg"> </div>';
                goodslist_data_str +=         '</div>';
                goodslist_data_str +=     '</div>';
                goodslist_data_str += '</li>';
            }
            $('#list-product-items').append(goodslist_data_str);
             $('.product-shopope-add').on('click', addFly); //动态加载购物车特效
           // is_loaded = 1; //加载完成
           // option_goods_count(); //加载数量
         
        }
    })
}

//加载分类
function ajax_goodscate() {
    $.ajax({  
        type: "GET",  
        url: tp_url('Index','getGoodsCat'),  
        data: {},
        success: function(res) {
            var cate_data_str = '';
            for(i in res) {
                cate_data_str += '<li  id="menu-leve1-category-' +res[i]['id']+ '" class="category-item ui-graybg category-item-folder" data-id="' +res[i]['id']+ '">';
                cate_data_str +=     '<span class="category-item-name">' +res[i]['name']+ '</span> <span class="category-item-expand">▼</span>';
                cate_data_str +=     '<div class="bottom-line"> </div>';
                cate_data_str +=     '<ul class="subitem-ul">';
                if( res[i]['items'] ) {
                    for(s in res[i]['items']) {
                cate_data_str +=         '<li id="menu-category-' +res[i]['items'][s]['id']+ '" class="fresh category-subitem">';
                cate_data_str +=         '<div class="indicator"></div>' +res[i]['items'][s]['name']+ '</li>';
                    }    
                }
                cate_data_str +=     '</ul>';
                cate_data_str += '</li>';
           }
            $('#list-category-ul').html(cate_data_str);
           //点击左栏大菜单
            $("li[id^='menu-leve1-category-']").click(function() {
                if ($(this).hasClass('expand')) {
                    $(this).find("li").animate({ height: '0px', opacity: '0' });
                } else {
                    $(this).find("li").animate({ height: '44px', opacity: '1' });
                }
                $(this).toggleClass('expand');
            });
            //点击左栏菜单细节项
            $("li[id^='menu-category-']").click(function(e) {
                e.stopPropagation();

                $("li[id^='menu-category-']").attr("class", "category-subitem");
                $(this).attr("class", "category-subitem active");
                //判断是否有mpID
                var id = $(this).attr("data-id");
               
            });
        }
    })
}

//更新购物数量
function editGoodsNum(goods_code, num, type) {
   // var type = arguments[2] ? arguments[2] : 0;
    var type = type ? type : 0;
    $.ajax({
        type: "POST",
        url: tp_url('Index','addCart'),  
        data: {'goods_code' : goods_code, 'num' : num, 'type' : type},
        success: function(res) {
            calcCartTotal(); //计算购物车总价总数量
            var product_item = $('.product-item[data-code="'+goods_code+'"]');
            var shopping_item = $('.shopping-item[data-code="'+goods_code+'"]');
            if(res) {
                product_item.find('.product-shopope').removeClass("countzero");
            }else{
                product_item.find('.product-shopope').addClass("countzero");
                $('.cart-item[data-code="'+goods_code+'"]').remove(); //购物车减到数量为零时删除节点            
            }
            shopping_item.find('.product-shopope-num').text(res); //购物车列表及产品列表数量赋值
            $('#detail_goods_num').text(res) //商品详情数量赋值
        }
    })
}

//计算购物车总价总数量
function calcCartTotal() {
    $.ajax({
        type: 'post',
        url : tp_url('Index','cartTotalPrice'),
        data: {},
        success: function(res) {
            $('#cart_goods_total').text(res.total_price);
            $('#cart_goods_num').text(res.total_num);
            
        }
    })
}

// 加入购物车动画
function addFly(event) {
    var offset = $('#gocart').offset(),
        //抛物线图片，可自行更改
        addhere = $(this);
        flyimg = addhere.parent().siblings('.product-imgwrap').find('img.lazy_product').attr('src');
        // console.log(flyimg);
        flyer = $('<img class="u-flyer" src="'+flyimg+'"/>');
        // flyer = $('<div class="flyball"></div>');
    flyer.fly({
        start: {
            left: event.pageX-25,
            top: event.pageY-50
        },
        end: {
            left: offset.left+20,
            top: offset.top+100,
            //抛物线完成后留在页面上的图片大小
            width: 0,
            height: 0,
        },
        speed: 1.2, //越大越快，默认1.2
        vertex_Rtop: 50, //运动轨迹最高点top值，默认20
        onEnd: function(){  //结束回调
            $('.u-flyer').remove();
            // $('.flyball').remove();
            $("#gocart").removeClass('icon-cart-shake');
        }
    });
    setTimeout(function(){$("#gocart").addClass('icon-cart-shake');},1000);
}

// 遮罩层
$("#mod-overlay-mask").click(function() {
        cartHide();
        detailHide();
});

//点击购物车
$('#gocart').click(function(){
    if($('#mod-overlay-cart').is(':hidden')) {
        cartShow();
    }else{
        cartHide();
    }
})
// 隐藏购物车
function cartHide() {
    $("#mod-overlay-mask").hide();
    $(".cart-detail").animate({ bottom: '-250px' }, 500, function() { $("#mod-overlay-cart").hide(); });
}

// 显示购物车
function cartShow() {
    getCartData();
    $("#mod-overlay-mask").show();
    $("#mod-overlay-cart").show();
    $(".cart-detail").css({ bottom: '-250px' });

    $(".cart-detail").animate({ bottom: '50px' }, 500, function() {
        g_scrolls[$("#mod-overlay-cart").find("div[class*='iscroll-container']").attr("data-iscroll-id")].refresh();
    });
}

//加载购物车数据
function getCartData() {
    $.ajax({
        type: 'post',
        url : tp_url('Index','getCartList'),
        data: {},
        success: function(res) {
            if(res == false) {
                $('#list_cart_detail').html('');
                return false;
            }
            var cart_str = ''
            for (i in res) {
                cart_str += '<li class="shopping-item cart-item" data-code="'+res[i]['goods_code']+'">';
                cart_str +=     '<div class="product-name product-absolute ui-ellipsis">'+res[i]['goods_name']+'</div>';
                cart_str +=     '<div class="product-price product-absolute"> ￥'+res[i]['shop_price']+' </div>';
                cart_str +=     '<div class="product-number product-absolute">';
                cart_str +=         '<div class="product-del shopping-action" onclick="editGoodsNum(\''+res[i]['goods_code']+'\', -1)" ></div>';
                cart_str +=         '<div class="product-count product-shopope-num" onclick="edit_cart_num(this, \''+res[i]['goods_code']+'\')">'+res[i]['cart_num']+'</div>';
                cart_str +=         '<div class="product-add shopping-action" onclick="editGoodsNum(\''+res[i]['goods_code']+'\', 1)" ></div>';
                cart_str +=     '</div>';
                cart_str += '</li>';
            }
            $('#list_cart_detail').html(cart_str);
             check_cart_li_num(); 
        }
    })
}

//根据购物车列表商品数量高度自动伸缩
function check_cart_li_num(){
    var cart_li_num = $("#list_cart_detail li").length;
    if (cart_li_num > 6) {
        $(".mod-overlay-cart .cart-detail").css("height", "350px");
    }else {
        $(".mod-overlay-cart .cart-detail").css("height", "auto");
    }
}

//编辑购物车商品数量
function edit_cart_num(obj, goods_code) {
    var num = parseInt($(obj).text())
    $('.edit_num').attr("value",num);
    var index = layer.open({
        anim: 'up',
        content: $('#edit_cart_num_box').html(),
        style: 'width: 80%;max-width:300px',
        btn: ['确认', '取消'],
        yes: function(index){
            num = parseInt($('.layui-m-layercont .edit_num').val());
            editGoodsNum(goods_code, num, 1);
            layer.close(index);
        }
    });

    //加减操作
    $('.layui-m-layercont .edit_cart_jia, .layui-m-layercont .edit_cart_jian').on('click',function(e){
        var chang_num = parseInt($('.layui-m-layercont .edit_num').val());
        if(e.target.className == 'edit_cart_jia'){
            $('.layui-m-layercont .edit_num').val(chang_num + 1);
        }else{
            if(chang_num <= 0) return false;
            $('.layui-m-layercont .edit_num').val(chang_num - 1);
        }
    })
}

//清除购物车
function del_cart() {
    layer.open({
        content: '您确定要清除购物车中全部商品吗？',
        style: 'width: 80%;max-width:300px',
        btn: ['确定', '取消'],
        yes: function(index){
            $.ajax({
                type: "get",
                url: tp_url('Index','delCart'),  
                data: {},
                success: function(res) {
                    cartHide(); //隐藏购物车
                    calcCartTotal(); //重新计算总价
                    $('.product-shopope-num').text(0); 
                    $('.product-shopope').addClass("countzero");
                    layer.open({content: '购物车清除成功',skin: 'msg',time: 2});
                }
            })
        }
    });
}



// 隐藏商品详情
function detailHide() {
    $("#mod-overlay-mask").hide();
    $("#mod-overlay-detail").animate({ top: '600px' }, 500, function() { $("#mod-overlay-detail").hide(); });
}

//显示商品详情
function showDetail( id ) {
    $.ajax({
        type: "get",
        url: tp_url('Index','goodsDetail'),  
        data: {'id' : id},
        success: function(res) {
            if(res == false) {
                layer.open({content: '获取详情失败',skin: 'msg',time: 2});
                return false;
            }
            $('#bannerContainer').html('<img src="'+UPLOADS+''+ res.goods_img +'" style="width:100%">');
            $('#list_detail .product-name').html(res.goods_name);
            $('#list_detail .product-price').html('￥'+res.shop_price);
            $('#list_detail .content-word').html(res.goods_code);
            $('#list_detail .detail_desc').html(res.goods_content);
            $('#list_detail .product-count').text(res.cart_num);
            $('#list_detail .product-add').attr('onclick','editGoodsNum(\''+res.goods_code+'\', 1)')
            $('#list_detail .product-del').attr('onclick','editGoodsNum(\''+res.goods_code+'\', -1)')
            $("#mod-overlay-mask").show();
            $("#mod-overlay-detail").show();
            $("#mod-overlay-detail").css({ top: '600px' });
            $("#mod-overlay-detail").animate({ top: '120px' }, 500, function() {
                g_scrolls[$("#mod-overlay-detail").find("div[class*='iscroll-container']").attr("data-iscroll-id")].refresh();
                g_scrolls[$("#mod-overlay-detail").find("div[class*='iscroll-container']").attr("data-iscroll-id")].scrollTo(0, 0);
            });

        }
    })
}



