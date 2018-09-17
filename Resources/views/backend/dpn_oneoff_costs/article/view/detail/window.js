//{block name="backend/article/view/detail/base"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Article.view.detail.DpnOneoffCostsWindow', {
    override: 'Shopware.apps.Article.view.detail.Window',

    createBaseTab: function() {
        var me = this;

        me.callParent(arguments);

        // Insert fieldset at index 2 which is below 'prices' panel by default
        me.detailForm.items.items.splice(2, 0, me.createOneoffCostsFieldSet());

        return me.detailContainer;
    },

    createOneoffCostsFieldSet: function () {
        var me = this;

        me.attrFieldPrice = Ext.create('Ext.form.field.Number', {
            xtype: 'numberfield',
            name: 'oneoff_costs_price',
            fieldLabel: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_price_label"}Price{/s}',
            minValue: 0,
            step: 0.01,
            labelWidth: 155
        });

        me.attrFieldLabel = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'oneoff_costs_label',
            translatable: true,
            fieldLabel: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_label_label"}Label{/s}',
            labelWidth: 155
        });

        return Ext.create('Ext.form.FieldSet', {
            title: '{s namespace="backend/detail" name="oneoff_costs_label"}One-off costs{/s}',
            layout: 'anchor',
            defaults: {
                labelWidth: 155,
                anchor: '100%'
            },
            items: [
                me.attrFieldPrice,
                me.attrFieldLabel
            ]
        });
    },

    onStoresLoaded: function() {
        var me = this;

        me.callParent(arguments);

        Ext.Ajax.request({
            url: '{url controller=AttributeData action=loadData}',
            params: {
                _foreignKey: me.article.get('mainDetailId'),
                _table: 's_articles_attributes'
            },
            success: function(responseData, request) {
                var response = Ext.JSON.decode(responseData.responseText);

                me.attrFieldPrice.setValue(response.data['__attribute_oneoff_costs_price']);
                me.attrFieldLabel.setValue(response.data['__attribute_oneoff_costs_label']);
            }
        });
    }
});
//{/block}