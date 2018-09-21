//{block name="backend/article/view/detail/settings"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Article.view.detail.DpnOneoffCostsSettings', {
    override: 'Shopware.apps.Article.view.detail.Settings',

    createElements: function() {
        var me = this;

        var elements = me.callParent(arguments);

        var oneoffCostsContainer = Ext.create('Ext.container.Container', {
            columnWidth:1,
            defaults: {
                labelWidth: 155,
                anchor: '100%'
            },
            layout: 'anchor',
            border:false,
            items: me.createOneoffCostsFieldSet()
        });

        elements.push(oneoffCostsContainer);

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

        me.oneoffCostsFieldSet = Ext.create('Ext.form.FieldSet', {
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

        return me.oneoffCostsFieldSet;
    },

    onStoresLoaded: function(article, stores) {
        var me = this,
            taxValue;

        me.callParent(arguments);

        me.taxStore = stores['taxes'];
        me.attrFieldTax.bindStore(me.taxStore);

        if (!article.get('mainDetailId')) {
            return;
        }

        Ext.Ajax.request({
            url: '{url controller=AttributeData action=loadData}',
            params: {
                _foreignKey: article.get('mainDetailId'),
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