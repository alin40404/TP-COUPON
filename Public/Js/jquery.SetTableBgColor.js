(function($){
        $.fn.extend({
            "SetTableBgColor":function(options){
                //设置默认样式值
                option=$.extend({
                    odd:"odd",//奇数行
                    even:"even",//偶数航
                    selected:"selected",//选中行
                    over:"over"//鼠标移动上去时
                },options);//此处options与function里的参数为同一个对象 
                
                //隔行换色
                $("tbody>tr:even",this).addClass(option.even);
                $("tbody>tr:odd",this).addClass(option.odd);
                
                //单击行变色
                $("tbody>tr",this).click(function(){
                    $("tbody>tr").removeClass(option.selected);
                    //var hasSelected=$(this).hasClass(option.selected);//返回true或false 查询是否已经包含点击状态下的样式
                    $(this).addClass(option.selected);//给选中行添加样式 [hasSelected?"removeClass":"addClass"]根据是否包含移除和添加样式
                });
                //鼠标移动上去变色
                $("tbody>tr",this).mouseover(function(){
                    $(this).addClass(option.over);
                });
                //鼠标移出时变回原来的样式
                $("tbody>tr",this).mouseout(function(){
                    $(this).removeClass(option.over);
                });
                return this;//返回this，使方法可链 注意 这里必须返回 否则无法直接的调用方法
            }
        });
    })(jQuery);//这个地方（jquery）必须加上，不然会报错
    
    
    //调用方法
//    $(".TableList").SetTableBgColor({
//            odd:"",
//            even:"alt",
//            selected:"selected",
//            over:"over"
//        });