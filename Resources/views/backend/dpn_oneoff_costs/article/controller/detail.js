//{block name="backend/article/controller/detail"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Article.controller.DpnDetail', {
    override: 'Shopware.apps.Article.controller.Detail',

    init:function () {
        var me = this;

        me.callParent(arguments);
    },

    onSaveArticle: function(win, article, options) {
        var me = this,
            originalCallback = options.callback;

        var customCallback = function(newArticle, success) {
            Ext.callback(originalCallback, this, arguments);

            params = Ext.merge(
                {
                    _foreignKey: newArticle.get('mainDetailId'),
                    _table: 's_articles_attributes',
                },
                me.getMainWindow().detailForm.getValues()
            );
            Ext.Ajax.request({
                method: 'POST',
                url: '{url controller=AttributeData action=saveData}',
                params: params
            });
        };

        if (!options.callback || options.callback.toString() !== customCallback.toString()) {
            options.callback = customCallback;
        }

        me.callParent([win, article, options]);
    }
});
//{/block}