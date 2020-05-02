//{namespace name=backend/plugins/dpn_oneoff_costs}
//{block name="backend/article/view/detail/oneoffcosts"}
Ext.define('Shopware.apps.Article.view.detail.OneoffCosts', {
    extend:'Ext.form.FieldSet',
    layout: 'column',
    alias:'widget.article-oneoffcosts-field-set',
    cls: Ext.baseCSSPrefix + 'article-oneoffcosts-field-set',
    snippets: {
        title: '{s name=title}One-off Costs{/s}',
        label: '{s name=label}Label{/s}',
        ordernum: '{s name=ordernum}Order number{/s}',
        price: '{s name=price}Price{/s}',
        net: '{s name=net}Price net{/s}',
        tax: '{s name=tax}Tax{/s}',
        likearticle: '{s name=likearticle}Like article{/s}',
    },
    initComponent:function () {
        var me = this,
            mainWindow = me.subApp.articleWindow;

        me.title = me.snippets.title;
        me.items = me.createElements();

        mainWindow.on('storesLoaded', me.onStoresLoaded, me);

        me.callParent(arguments);
    },

    createElements: function () {
        var leftContainer, rightContainer, me = this;

        leftContainer = Ext.create('Ext.container.Container', {
            columnWidth:0.5,
            defaults: {
                labelWidth: 155,
                anchor: '100%'
            },
            padding: '0 20 0 0',
            layout: 'anchor',
            border: false,
            items: me.createLeftElements()
        });

        rightContainer = Ext.create('Ext.container.Container', {
            columnWidth:0.5,
            layout: 'anchor',
            defaults: {
                labelWidth: 155,
                anchor: '100%'
            },
            border: false,
            items: me.createRightElements()
        });

        return [
            leftContainer,
            rightContainer
        ];
    },

    createLeftElements: function() {
        var me = this;

        me.oneoffCostsLabelField = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: '__attribute_oneoff_costs_label',
            fieldLabel: me.snippets.label,
            translatable: true,
            translationName: 'oneoff_costs_label',
            labelWidth: 155,
            anchor: '100%'
        });

        me.oneoffCostsOrdernumField = Ext.create('Ext.form.field.Text', {
            xtype: 'textfield',
            name: '__attribute_oneoff_costs_ordernum',
            fieldLabel: me.snippets.ordernum,
            labelWidth: 155,
            anchor: '100%'
        });

        return [
            me.oneoffCostsLabelField,
            me.oneoffCostsOrdernumField
        ];
    },

    createRightElements: function() {
        var me = this;

        me.oneoffCostsPriceField = Ext.create('Ext.form.field.Number', {
            xtype: 'numberfield',
            name: '__attribute_oneoff_costs_price',
            fieldLabel: me.snippets.price,
            labelWidth: 155,
            anchor: '100%'
        });

        me.oneoffCostsNetField = Ext.create('Ext.form.field.Checkbox', {
            xtype: 'checkboxfield',
            name: '__attribute_oneoff_costs_price_net',
            inputValue: 1,
            uncheckedValue: 0,
            fieldLabel: me.snippets.net,
            labelWidth: 155,
            anchor: '100%'
        });

        me.oneoffCostsTaxField = Ext.create('Ext.form.field.ComboBox', {
            name: '__attribute_oneoff_costs_tax',
            queryMode: 'local',
            emptyText: me.snippets.likearticle,
            fieldLabel: me.snippets.tax,
            allowBlank: true,
            valueField: 'id',
            displayField: 'name',
            editable: false,
            labelWidth: 155,
            anchor: '100%'
        });

        return [
            me.oneoffCostsPriceField,
            me.oneoffCostsTaxField,
            me.oneoffCostsNetField
        ];
    },

    onStoresLoaded: function(article, stores) {
        var me = this,
            mainWindow = me.subApp.articleWindow;

        me.oneoffCostsTaxField.bindStore(stores['taxes']);

        Ext.Ajax.request({
            url: '{url controller=AttributeData action=loadData}',
            params: {
                _foreignKey: mainWindow.article.get('mainDetailId'),
                _table: 's_articles_attributes'
            },
            success: function(responseData, request) {
                var response = Ext.JSON.decode(responseData.responseText);

                me.oneoffCostsLabelField.setValue(response.data['__attribute_oneoff_costs_label']);
                me.oneoffCostsOrdernumField.setValue(response.data['__attribute_oneoff_costs_ordernum']);
                me.oneoffCostsPriceField .setValue(response.data['__attribute_oneoff_costs_price']);
                me.oneoffCostsTaxField.setValue(parseInt(response.data['__attribute_oneoff_costs_tax']));
                me.oneoffCostsNetField.setValue(response.data['__attribute_oneoff_costs_price_net']);
            }
        });
    }
});
//{/block}