define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/aifacescan/index',
                    // add_url: 'user/aifacescan/add',
                    edit_url: 'user/aifacescan/edit',
                    // del_url: 'user/aifacescan/del',
                    multi_url: 'user/aifacescan/multi',
                    table: 'user_aifacescan',
                }
            });

            var table = $("#table");

            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){
                return "搜索：ID";
            };

            table.on('post-common-search.bs.table', function(event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='u_id']", form).addClass("selectpage").data("source", "user/aifacescan/selectpageuser").data('primaryKey','id').data("field", "nickname").data("orderBy", "id desc");
                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'u_id', title: __('U_id'), formatter:function(val, row, index){
                                return $.isEmptyObject(row['get_user']) ? '' : row['get_user']['nickname'] + "(" + row['get_user']['mobile'] + ")";
                            }},
                        {field: 'img', title: __('Img'), formatter:function(val, row, index){
                                var imagestr = '';
                                if (!$.isEmptyObject(row['img'])) {
                                    var pictures = (row['img']).split(',');
                                    for (var i in pictures) {
                                        if (i > 1) break;
                                        imagestr += "<img src='" + pictures[i] + "' style='height:50px;'/>";
                                    }
                                }
                                return imagestr;
                            }, operate:false},
                        {field: 'age', title: __('Age'), operate:'BETWEEN'},
                        {field: 'appearance', title: __('Appearance'), operate:'BETWEEN'},
                        {field: 'pockmark', title: __('Pockmark'), visible:false, operate:'BETWEEN'},
                        {field: 'spot', title: __('Spot'), visible:false, operate:'BETWEEN'},
                        {field: 'wrinkle', title: __('Wrinkle'), visible:false, operate:'BETWEEN'},
                        {field: 'blackhead', title: __('Blackhead'), visible:false, operate:'BETWEEN'},
                        {field: 'pore', title: __('Pore'), visible:false, operate:'BETWEEN'},
                        {field: 'sensitive', title: __('Sensitive'), visible:false, operate:'BETWEEN'},
                        {field: 'dark_circle', title: __('Dark_circle'), visible:false, operate:'BETWEEN'},
                        {field: 'score', title: __('Score'), operate:false, formatter:function(val, row, index) {
                                return row['score_text'];
                            }},
                        {field: 'question', title: __('Question'), operate:false},
                        {field: 'advise', title: __('Advise'), operate:false},
                        // {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});