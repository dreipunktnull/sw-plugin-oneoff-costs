//{block name="backend/article/controller/detail"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Article.controller.DpnOneoffCostsDetail', {
    override: 'Shopware.apps.Article.controller.Detail',

    init: function () {
        var me = this;

        me.callParent(arguments);
        me.refs.push({ ref: 'oneoffCostsFieldSet', selector: 'article-detail-window article-oneoff-costs-field-set' });
    },

    onSaveArticle: function(win, article, options) {
        var me = this,
            originalCallback = options.callback;

        var customCallback = function(newArticle, success) {
            Ext.callback(originalCallback, this, arguments);

            Ext.Ajax.request({
                method: 'POST',
                url: '{url controller=AttributeData action=saveData}',
                params: {
                    _foreignKey: newArticle.get('mainDetailId'),
                    _table: 's_articles_attributes',
                    __attribute_oneoff_costs_price: me.getOneoffCostsFieldSet().attrFieldPrice.getValue(),
                    __attribute_oneoff_costs_tax: me.getOneoffCostsFieldSet().attrFieldTax.getValue(),
                    __attribute_oneoff_costs_label: me.getOneoffCostsFieldSet().attrFieldLabel.getValue(),
                    __attribute_oneoff_costs_ordernum: me.getOneoffCostsFieldSet().attrFieldOrdernum.getValue()
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