//{block name="backend/article/view/detail/base"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Article.view.detail.DpnOneoffCostsBase', {
    override: 'Shopware.apps.Article.view.detail.Base',

    createRightElements: function() {
        var me = this;

        var elements = me.callParent(arguments);

        elements.push(me.createOneoffCostsFieldSet());

        return elements;
    },

    createOneoffCostsFieldSet: function () {
        var me = this;

        me.attrFieldPrice = Ext.create('Ext.form.field.Number', {
            xtype: 'numberfield',
            name: 'oneoff_costs_price',
            fieldLabel: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_price_label"}Price value{/s}',
            helpText: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_price_helpText"}Price helptext{/s}',
            minValue: 0,
            step: 0.01
        });

        me.attrFieldTax = Ext.create('Ext.form.field.ComboBox', {
            name: 'oneoff_costs_tax',
            queryMode: 'local',
            fieldLabel: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_tax_label"}Tax value{/s}',
            helpText: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_tax_helpText"}Tax helptext{/s}',
            emptyText: '{s namespace="backend/detail" name="oneoff_costs_empty"}Tax emptytext{/s}',
            allowBlank: true,
            forceSelection: false,
            valueField: 'id',
            displayField: 'name',
            editable: false,
            anchor: '100%'
        });

        me.attrFieldLabel = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'oneoff_costs_label',
            translatable: true,
            fieldLabel: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_label_label"}Label value{/s}',
            helpText: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_label_helpText"}Label helptext{/s}'
        });

        me.attrFieldOrdernum = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: 'oneoff_costs_ordernum',
            translatable: false,
            fieldLabel: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_ordernum_label"}Order number{/s}',
            helpText: '{s namespace="backend/attribute_columns" name="s_articles_attributes_oneoff_costs_ordernum_helpText"}Order number helptext{/s}'
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
                me.attrFieldTax,
                me.attrFieldLabel,
                me.attrFieldOrdernum
            ]
        });
    },

    onStoresLoaded: function(article, stores) {
        var me = this,
            taxValue;

        me.callParent(arguments);

        me.attrFieldTax.bindStore(stores['taxes']);

        if (!me.article.get('mainDetailId')) {
            return;
        }

        Ext.Ajax.request({
            url: '{url controller=AttributeData action=loadData}',
            params: {
                _foreignKey: me.article.get('mainDetailId'),
                _table: 's_articles_attributes'
            },
            success: function(responseData, request) {
                var response = Ext.JSON.decode(responseData.responseText);
                var taxStore = stores['taxes'];
                var taxId = response.data['__attribute_oneoff_costs_tax'];
                if (taxId !== null) {
                    taxValue = taxStore.findRecord('id', taxId);
                }
                me.attrFieldPrice.setValue(response.data['__attribute_oneoff_costs_price']);
                me.attrFieldTax.setValue(taxValue);
                me.attrFieldLabel.setValue(response.data['__attribute_oneoff_costs_label']);
                me.attrFieldOrdernum.setValue(response.data['__attribute_oneoff_costs_ordernum']);
            }
        });
    }
});
//{/block}