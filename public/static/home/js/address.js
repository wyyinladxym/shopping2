/**
 * address.js
 * @authors chenhaoqiang (chenhaoqiang.irxk@gmail.com)
 * @date    2017-11-16 16:26:55
 * @version $Id$
 */

 var g_scrolls = new Object();
 var g_areas_id = new Object();
 var g_areas_name = new Object();

 function loaded() {
 	$("div[class*='iscroll-container']").each(function() {
 		var id = $(this).attr("data-iscroll-id");

 		g_scrolls[id] = new iScroll(this, { scrollbarClass: 'categoryScrollbar' });
 	});
 }

 document.addEventListener('DOMContentLoaded', function() { setTimeout(loaded, 200); }, false);

 // 修改地址
 function editClick(obj) {
     var id = obj.id;

     $.get("./data/getAddrList.json.php?id=" + 　id, function(data) {
         //alert(data);
         var htmlstr = "";
         var obj = JSON.parse(data);
         if (obj.length > 0) {
             $("#addrid").val(obj[0]['id']);
             $("#addredit-name").val(obj[0]['accept_name']);
             $("#addredit-phone").val(obj[0]['mobile']);
             $("#addredit-address").val(obj[0]['address']);
             $("#addredit-areas").val(obj[0]['areas_text']);

             g_areas_id["province"] = obj[0]['province'];
             g_areas_id["city"] = obj[0]['city'];
             g_areas_id["district"] = obj[0]['area'];

         }
     });

     $("#mod-addredit").show();

 }

 // 获取地区
 function getAreas(id) {
     $.get("./data/getAreas.json.php?id=" + id, function(data) {
         var a = $.parseJSON(data);
         var shtml = "";
         if (a == false) {
             console.log(11111);
         }
         for (key in a) {
             v = a[key];
             shtml += "<div class=\"mine-item tap-action\" data-tap=\"areaClick|" + v.area_id + "|" + v.area_name + "\">" +
                 "<span class=\"item-icon\">" +
                 "</span>" +
                 v.area_name +
                 "</div>" +
                 "<hr>";
         }
         $('#list_select_areas_result').html(shtml);
         $('#list_select_areas_result').focus();

         g_scrolls[$("#list_select_areas").attr("data-iscroll-id")].refresh();
         g_scrolls[$("#list_select_areas").attr("data-iscroll-id")].scrollTo(0, 0);
     });
     $('#mod-select-areas').show();

 }

 //选择Areas
 $('#addradd-areas').focus(function() {
     g_areas_name["province"] = "";
     g_areas_name["city"] = "";
     g_areas_name["district"] = "";
     g_areas_id["province"] = "";
     g_areas_id["city"] = "";
     g_areas_id["district"] = "";
     getAreas("0");
 });

 $('#addredit-areas').focus(function() {
     g_areas_name["province"] = "";
     g_areas_name["city"] = "";
     g_areas_name["district"] = "";
     g_areas_id["province"] = "";
     g_areas_id["city"] = "";
     g_areas_id["district"] = "";
     getAreas("0");
 });

// 提交地址操作动作
function submitAddr(action) {

    if (action == 'add') {
        if ($("#addradd-name").val() == "") {
            alert("请填写联系人");
            return;
        }

        if ($("#addradd-phone").val() == "") {
            alert("请填写手机号码");
            return;
        }
        //应急123456
        if (g_areas_id["province"] == '') {
            g_areas_id["province"] = '110100';
        }

        //if(g_areas_id["province"] == false || g_areas_id["city"] == false || g_areas_id["district"] == false){
        if (g_areas_id["province"] == false || g_areas_id["district"] == false) {
            alert("请选择地区");
            return;
        }
        // alert(aresa);
        // alert(g_areas_id["province"]);
        // alert(g_areas_id["district"]);
        // return false;


        if ($("#addradd-address").val() == "") {
            alert("请填写详细地址");
            return;
        }
        $.ajax({
            type: 'POST',
            url: './do/updateMember.php',
            data: {
                "action": "add",
                "name": $("#addradd-name").val(),
                "phone": $("#addradd-phone").val(),
                "province": g_areas_id["province"],
                "city": g_areas_id["city"] ? g_areas_id["city"] : 0,
                "district": g_areas_id["district"],
                "areas_text": $("#addradd-areas").val(),
                "address": $("#addradd-address").val(),
                // "userid": $.cookie("loginuid"),
            },
            success: function(data) {
                if (data != '') {
                    alert(data);
                    return;
                }
                //alert("添加成功");
                //location.reload();
                $("#mod-addradd").hide();
                getAddrList();
                return;
            },
            error: function() {
                alert("添加失败");
                return;
            }
        });

    } else if (action == 'edit') {
        if ($("#addredit-name").val() == "") {
            alert("请填写联系人");
            return;
        }

        if ($("#addredit-phone").val() == "") {
            alert("请填写手机号码");
            return;
        }

        //if(g_areas_id["province"] == false || g_areas_id["city"] == false || g_areas_id["district"] == false){
        if (g_areas_id["province"] == false || g_areas_id["district"] == false) {
            alert("请选择地区");
            return;
        }

        if ($("#addredit-address").val() == "") {
            alert("请填写详细地址");
            return;
        }

        $.ajax({
            type: 'POST',
            url: './do/updateMember.php',
            data: {
                "action": "edit",
                "name": $("#addredit-name").val(),
                "phone": $("#addredit-phone").val(),
                "province": g_areas_id["province"],
                "city": g_areas_id["city"] ? g_areas_id["city"] : 0,
                "district": g_areas_id["district"],
                "areas_text": $("#addredit-areas").val(),
                "address": $("#addredit-address").val(),
                "addrid": $("#addrid").val(),
                "userid": $.cookie("loginuid"),
            },
            success: function(data) {
                if (data != '') {
                    alert(data);
                    return;
                }
                //alert("更改成功");
                $("#mod-addredit").hide();
                getAddrList();
                return;
            },
            error: function() {
                alert("更改失败");
                return;
            }
        });
    } else if (action == 'delete') {
        $.ajax({
            type: 'POST',
            url: './do/updateMember.php',
            data: {
                "action": "delete",
                "name": $("#addredit-name").val(),
                "phone": $("#addredit-phone").val(),
                "province": g_areas_id["province"],
                "city": g_areas_id["city"] ? g_areas_id["city"] : 0,
                "district": g_areas_id["district"],
                "areas_text": $("#addredit-areas").val(),
                "address": $("#addredit-address").val(),
                "addrid": $("#addrid").val(),
                "userid": $.cookie("loginuid"),
            },
            success: function(data) {
                if (data == "re:all") {
                    //已经删除全部了
                    //$("#main-address-tap").hide();
                    $("#mod-addredit").hide();
                    getAddrList();
                    $("span[name='cart-name']").html("");
                    $("span[name='cart-addr']").html("");
                    $("span[name='cart-phone']").html("");
                    $.cookie("sCAName", "");
                    $.cookie("sCAAddr", "");
                    $.cookie("sCAPhone", "");
                    return;
                }

                if (data != '') {
                    alert(data);
                    return;
                }
                //alert("更改成功");
                $("#mod-addredit").hide();
                getAddrList();
                return;
            },
            error: function() {
                alert("更改失败");
                return;
            }
        });

    }

}

$(function() {
	$(document).on('click', "div", function() {
	    g_events_click($(this));
	});

	$("span").click(function() {
	    g_events_click($(this));
	});

	$("a").click(function() {
	    g_events_click($(this));
	});

	$(document).on('click', "li", function() {
	    g_events_click($(this));
	});

	//tap切换方法
	function g_events_click(obj) {
		//alert(obj.attr("class"));
		if (typeof(obj.attr("data-tap")) == "undefined") {
		    return;
		} else {
		    //打印 data-tap **调试用**
		    console.log("data-tap=\"" + obj.attr("data-tap") + "\"");
		}

		if (obj.attr("data-tap") == "!back") {
		    obj.parent().parent().hide();
		    return;
		}

        // 支付方式选择


        switch (obj.attr("data-tap")) {

            case 'selectPay|wapweixin': //微信支付选项
                $('#mod-container').css('overflow', 'visible'); //ZJJ 选择微信交易
                $('#warp').hide(100); //隐藏电汇单 上传图片
                $('#auto-tab-negotiate').html('提交议价单').css('background', 'blue'); //修改议价 按钮
                $('.negotiate_unit_price').prev().html('商议价:￥'); //修改议价 单价 提示文字
                $('#confirm_remark_note').show();
                $('#confirm_remark_note').prev().show(); //显示商议备注
                $('#negotiate_price').prev().html('商议总价：')
                break;

            case 'selectPay|telegraphic': //电汇支付选项
                $('#mod-container').css('overflow', 'visible'); //ZJJ 选择电汇付款
                $('#warp').show(100);
                $('#auto-tab-negotiate').html('提交订单').css('background', '#e7601f'); //修改议价 按钮
                $('.negotiate_unit_price').prev().html('提交单价:￥'); //修改议价 单价 提示文字
                $('#confirm_remark_note').hide();
                $('#confirm_remark_note').prev().hide(); //隐藏商议备注
                $('#negotiate_price').prev().html('提交总价：')
                break;

            case 'selectPay|agreement': //协议支付选项
                $('#mod-container').css('overflow', 'visible'); //ZJJ 选择协议付款
                $('#warp').hide(100); //隐藏电汇单 上传图片
                $('#auto-tab-negotiate').html('提交订单').css('background', '#e7601f'); //修改议价 按钮
                $('.negotiate_unit_price').prev().html('提交单价:￥'); //修改议价 单价 提示文字
                $('#confirm_remark_note').hide();
                $('#confirm_remark_note').prev().hide(); //隐藏商议备注
                $('#negotiate_price').prev().html('提交总价：')
                break;
        }

		// 增加新地址
		if (obj.attr("data-tap") == "itemClick|addradd") {
		    $("#addradd-name").val("");
		    $("#addradd-address").val("");
		    // $("#addradd-phone").val("");
		    // $("#addradd-phone").val($.cookie("loginname"));
		    $('#mod-addradd').show();
		    return;
		}

		// 地址修改增存删
		if (obj.attr("data-tap") == "addNewAddrClick") {
		    submitAddr('add');
		}
		if (obj.attr("data-tap") == "updateAddrClick") {
		    submitAddr('edit');
		}
		if (obj.attr("data-tap") == "deleteAddrClick") {
		    submitAddr('delete');
		}

        // 获取地址列表
        function getAddrList() {
            $.get("./data/getAddrList.json.php?uid=" + $.cookie("loginuid"), function(data) {
                //alert(data);
                var htmlstr = "";
                var obj = JSON.parse(data);
                for (i = 0; i < obj.length; i++) {
                    htmlstr += "<li class=\"mod-addresslist g_event\" data-tap=\"selectAddr|" + obj[i]["id"] + "\"><div class=\"list-info\"><div class=\"list-name\">" +
                        obj[i]["accept_name"] + "<span>" + obj[i]["mobile"] + "</span></div><br><div class=\"list-addr\" >" + obj[i]["address"] +
                        "</div></div><div class=\"mod-revisions\" data-tap=\"addredit\"><button id=" + obj[i]["id"] + " class=\"button\" type=\"button\" onclick=\"editClick(this)\">修改</button></div></li>";

                }
                $("#list-addr").html(htmlstr);
            });
        }

		//购物车选择默认地址
		if (obj.attr("data-tap") == "selectAddr") {
		    $('#mod-addrlist').show();
		    getAddrList();
		    return;
		}

		//选择地址
		if (obj.attr("data-tap").substr(0, 11) == "selectAddr|") {
		    if ($("#mod-mine").is(":hidden")) {
		        //选取当前地址到cookie里
		        var id = obj.attr("data-tap").split("|")[1];
		        $.get("./data/getAddrList.json.php?id=" + id + "&setdefault=true", function(data) {
		            var htmlstr = "";
		            var obj = JSON.parse(data);
		            if (obj.length > 0) {
		                $.cookie("sCAName", obj[0]['accept_name'], { expires: 999 });
		                $.cookie("sCAAddr", obj[0]['address'], { expires: 999 });
		                $.cookie("sCAPhone", obj[0]['mobile'], { expires: 999 });
		                $.cookie("sCAAddrId", obj[0]['id'], { expires: 999 });

		                $.cookie("sCAProvince", obj[0]['province'], { expires: 999 });
		                $.cookie("sCACity", obj[0]['city'], { expires: 999 });
		                $.cookie("sCAArea", obj[0]['area'], { expires: 999 });

		                $("span[name='cart-name']").html($.cookie("sCAName"));
		                $("span[name='cart-addr']").html($.cookie("sCAAddr"));
		                $("span[name='cart-phone']").html($.cookie("sCAPhone"));
		            }
		        });


		        $('#mod-addrlist').hide();
		    }
		}


		//选择区域id
		if (obj.attr("data-tap").substr(0, 10) == "areaClick|") {
		    var id = obj.attr("data-tap").split("|")[1];
		    var name = obj.attr("data-tap").split("|")[2];
		    var k = "";
		    if (id.length == 6) {
		        if (id.substr(5, 1) != "0") k = "district";
		        if (id.substr(4, 2) == "00") k = "city";
		        if (id.substr(2, 4) == "0000") k = "province";
		    }

		    g_areas_name[k] = name;
		    g_areas_id[k] = id;

		    if (k == "district") {
		        $("#addradd-areas").val(g_areas_name["province"] + ">" + g_areas_name["city"] + ">" + g_areas_name["district"]);
		        $("#addredit-areas").val(g_areas_name["province"] + ">" + g_areas_name["city"] + ">" + g_areas_name["district"]);
		        $("#mod-select-areas").hide();
		    } else {
		        getAreas(id);
		    }
		}

	}

});


