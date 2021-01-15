define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/aitext/index',
                    add_url: 'user/aitext/add',
                    edit_url: 'user/aitext/edit',
                    del_url: 'user/aitext/del',
                    multi_url: 'user/aitext/multi',
                    table: 'user_aitext',
                }
            });

            var table = $("#table");

            table.on('post-common-search.bs.table', function(event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='c_id']", form).addClass("selectpage").data("source", "user/aitextclass/index").data('primaryKey','id').data("field", "name").data("orderBy", "id desc");
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
                        {field: 'id', title: __('ID')},
                        {field: 'c_id', title: __('C_id'),align: 'left', formatter:function(val, row){
                                return row['get_class']['title'];
                            }},
                        {field: 'start_score', title: __('Score'), operate: 'BETWEEN', formatter: function(val, row)
                            {
                                return val+' - '+row['end_score']
                            }},
                        {field: 'end_score', title: __('End_score'), visible:false, operate: 'BETWEEN'},
                        {field: 'content', title: __('Content'), operate:false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
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