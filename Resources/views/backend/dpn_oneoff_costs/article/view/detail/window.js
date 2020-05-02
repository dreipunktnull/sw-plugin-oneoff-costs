//{namespace name=backend/article/view/main}
//{block name="backend/article/view/detail/window"}
//  {$smarty.block.parent}
Ext.define('Shopware.apps.Article.view.detail.DpnWindow', {
    override: 'Shopware.apps.Article.view.detail.Window',

    createBaseTab: function() {
        var me = this,
            baseTab = me.callParent(arguments);

        me.oneOffCostsFieldSet = me.createOneoffCostsFieldSet()
        baseTab.items.last().add(me.oneOffCostsFieldSet);

        return baseTab;
    },

    createOneoffCostsFieldSet: function() {
        return Ext.create('Shopware.apps.Article.view.detail.OneoffCosts', {
            subApp: this.subApp
        });
    }
});
//{/block}