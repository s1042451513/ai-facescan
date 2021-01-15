define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/aitextclass/index',
                    add_url: 'user/aitextclass/add',
                    edit_url: 'user/aitextclass/edit',
                    del_url: 'user/aitextclass/del',
                    multi_url: 'user/aitextclass/multi',
                    table: 'user_aitextclass',
                }
            });

            var table = $("#table");

            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){
                return "搜索：类型，分类名";
            };

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('ID')},
                        {field: 'type', title: __('Type'), operate:'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'name', title: __('Name'), operate:'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'title', title: __('Title'), operate:'LIKE %...%', placeholder: '模糊搜索'},
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