//{block name="backend/article/controller/detail"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Article.controller.DpnOneoffCostsDetail', {
    override: 'Shopware.apps.Article.controller.Detail',

    onSaveArticle: function(win, article, options) {
        var me = this,
            mainWindow = me.getMainWindow(),
            originalCallback = options.callback;

        var customCallback = function(newArticle, success) {
            Ext.callback(originalCallback, this, arguments);

            Ext.Ajax.request({
                method: 'POST',
                url: '{url controller=AttributeData action=saveData}',
                params: {
                    _foreignKey: newArticle.get('mainDetailId'),
                    _table: 's_articles_attributes',
                    __attribute_oneoff_costs_price: mainWindow.attrFieldPrice.getValue(),
                    __attribute_oneoff_costs_label: mainWindow.attrFieldLabel.getValue()
                }
            });
        };

        if (!options.callback || options.callback.toString() !== customCallback.toString()) {
            options.callback = customCallback;
        }

        me.callParent([win, article, options]);
    }
});
//{/block}